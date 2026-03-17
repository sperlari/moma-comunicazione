<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('moma_get_hubspot_form_config')) {
  function moma_get_hubspot_form_config(string $context = 'footer'): array
  {
    if (!function_exists('get_field')) {
      return [];
    }

    $context = $context === 'contact' ? 'contact' : 'footer';

    $config = [
      'portal_id' => trim((string) get_field($context . '_hubspot_portal_id', 'option')),
      'form_id'   => trim((string) get_field($context . '_hubspot_form_id', 'option')),
      'region'    => trim((string) get_field($context . '_hubspot_region', 'option')),
      'target'    => trim((string) get_field($context . '_hubspot_target', 'option')),
    ];

    if ($config['region'] === '') {
      $config['region'] = 'eu1';
    }

    if ($context === 'footer' && $config['target'] === '') {
      $config['target'] = '#hs-newsletter-form';
    }

    if ($context === 'contact' && $config['target'] === '') {
      $config['target'] = '#hs-contact-form';
    }

    return $config;
  }
}

add_action('wp_enqueue_scripts', function () {
  $footer = moma_get_hubspot_form_config('footer');
  $contact = moma_get_hubspot_form_config('contact');

  if (
    empty($footer['portal_id']) && empty($footer['form_id']) &&
    empty($contact['portal_id']) && empty($contact['form_id'])
  ) {
    return;
  }

  wp_enqueue_script(
    'hubspot-forms',
    'https://js-eu1.hsforms.net/forms/embed/v2.js',
    [],
    null,
    true
  );
}, 20);

if (!function_exists('moma_render_hubspot_form')) {
  function moma_render_hubspot_form(array $args = []): string
  {
    $args = wp_parse_args($args, [
      'context'    => 'footer',
      'target_id'  => '',
      'target'     => '',
      'class'      => '',
      'submit_label' => '',
      'empty_html' => '',
    ]);

    $config = moma_get_hubspot_form_config((string) $args['context']);
    if (empty($config['portal_id']) || empty($config['form_id'])) {
      return (string) $args['empty_html'];
    }

    $target = trim((string) $args['target']);
    if ($target === '') {
      $target = trim((string) $config['target']);
    }

    $target_id = trim((string) $args['target_id']);
    if ($target_id === '') {
      $target_id = ltrim($target, '#');
    }

    if ($target_id === '') {
      $target_id = 'hs-form-' . sanitize_html_class((string) $args['context']);
      $target = '#' . $target_id;
    }

    $wrapper_class = trim('moma-hubspot-form ' . (string) $args['class']);

    ob_start();
    ?>
    <div id="<?php echo esc_attr($target_id); ?>" class="<?php echo esc_attr($wrapper_class); ?>" data-moma-hubspot-context="<?php echo esc_attr((string) $args['context']); ?>"></div>
    <script>
      (function() {
        let attempts = 0;
        let retryTimer = null;

        function enhanceForm(formEl) {
          if (!formEl || formEl.dataset.momaEnhanced === '1') return;

          const submit = formEl.querySelector('input[type="submit"]');
          const submitLabel = <?php echo wp_json_encode(trim((string) $args['submit_label'])); ?>;
          const emailField = formEl.querySelector('input[type="email"]');

          let feedback = formEl.querySelector('.moma-hs-feedback');
          if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'moma-hs-feedback';
            feedback.setAttribute('aria-live', 'polite');
            const submitWrap = formEl.querySelector('.hs-submit');
            if (submitWrap && submitWrap.parentNode) {
              submitWrap.parentNode.insertBefore(feedback, submitWrap.nextSibling);
            } else {
              formEl.appendChild(feedback);
            }
          }

          function setFeedback(message, type) {
            if (!feedback) return;
            feedback.textContent = message || '';
            feedback.classList.remove('is-error', 'is-success', 'is-visible');

            if (message) {
              feedback.classList.add('is-visible');
              if (type) {
                feedback.classList.add(type === 'success' ? 'is-success' : 'is-error');
              }
            }
          }

          if (submit && submitLabel) {
            submit.value = submitLabel;
            submit.setAttribute('aria-label', submitLabel);
          }

          if (emailField) {
            emailField.setAttribute('required', 'required');
            if (!emailField.getAttribute('autocomplete')) {
              emailField.setAttribute('autocomplete', 'email');
            }

            emailField.addEventListener('input', function() {
              emailField.classList.remove('invalid', 'error');
              setFeedback('', '');
            });
          }

          const actions = submit ? submit.closest('.actions') : null;
          if (actions && actions.dataset.momaSubmitProxy !== '1') {
            actions.dataset.momaSubmitProxy = '1';
            actions.addEventListener('click', function(event) {
              if (event.target === submit) return;
              submit.click();
            });
          }

          if (submit && submit.dataset.momaNativeValidation !== '1') {
            submit.dataset.momaNativeValidation = '1';
            submit.addEventListener('click', function(event) {
              if (typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
                event.preventDefault();
                if (emailField && typeof emailField.checkValidity === 'function' && !emailField.checkValidity()) {
                  emailField.classList.add('invalid');
                  setFeedback('Inserisci un indirizzo email valido.', 'error');
                }
                if (typeof formEl.reportValidity === 'function') {
                  formEl.reportValidity();
                }
              }
            });
          }

          if (formEl.dataset.momaSubmitValidation !== '1') {
            formEl.dataset.momaSubmitValidation = '1';
            formEl.addEventListener('submit', function(event) {
              if (typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
                event.preventDefault();
                if (emailField && typeof emailField.checkValidity === 'function' && !emailField.checkValidity()) {
                  emailField.classList.add('invalid');
                  setFeedback('Inserisci un indirizzo email valido.', 'error');
                }
                if (typeof formEl.reportValidity === 'function') {
                  formEl.reportValidity();
                }
              }
            });
          }

          formEl.dataset.momaEnhanced = '1';
        }

        function init() {
          const target = <?php echo wp_json_encode($target); ?>;
          if (!target || !document.querySelector(target)) return;

          const existingForm = document.querySelector(target + ' .hs-form');
          if (existingForm) {
            enhanceForm(existingForm);
            return;
          }

          if (!window.hbspt || !window.hbspt.forms) {
            attempts += 1;
            if (attempts > 30) return;
            retryTimer = window.setTimeout(init, 250);
            return;
          }

          try {
            window.hbspt.forms.create({
              portalId: <?php echo wp_json_encode($config['portal_id']); ?>,
              formId: <?php echo wp_json_encode($config['form_id']); ?>,
              region: <?php echo wp_json_encode($config['region']); ?>,
              target: target,
              onFormReady: function(form) {
                const formEl =
                  form && form.nodeType === 1 ? form :
                  form && typeof form.get === 'function' ? form.get(0) :
                  form && form[0] ? form[0] :
                  null;

                enhanceForm(formEl);
              },
              onFormSubmitted: function(form) {
                const formEl =
                  form && form.nodeType === 1 ? form :
                  form && typeof form.get === 'function' ? form.get(0) :
                  form && form[0] ? form[0] :
                  null;

                if (!formEl) return;
                const feedback = formEl.querySelector('.moma-hs-feedback');
                if (feedback) {
                  feedback.textContent = 'Iscrizione inviata correttamente.';
                  feedback.classList.remove('is-error');
                  feedback.classList.add('is-visible', 'is-success');
                }
              }
            });
          } catch (error) {}
        }

        if (window.hbspt && window.hbspt.forms) {
          init();
        } else {
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init, { once: true });
          } else {
            init();
          }
        }
      })();
    </script>
    <?php

    return trim((string) ob_get_clean());
  }
}

add_shortcode('moma_hubspot_form', function ($atts) {
  $atts = shortcode_atts([
    'context' => 'contact',
    'class'   => '',
    'target'  => '',
  ], $atts, 'moma_hubspot_form');

  return moma_render_hubspot_form([
    'context'   => (string) $atts['context'],
    'class'     => (string) $atts['class'],
    'target_id' => ltrim((string) $atts['target'], '#'),
    'target'    => (string) $atts['target'],
  ]);
});
