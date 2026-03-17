<?php
if (!defined('ABSPATH')) exit;

$cta_title = function_exists('get_field') ? (string) (get_field('footer_cta_title', 'option') ?: 'Frase per andare ai contatti') : 'Frase per andare ai contatti';
$cta_link  = function_exists('get_field') ? get_field('footer_cta_link', 'option') : null;

$cta_url    = is_array($cta_link) && !empty($cta_link['url']) ? (string) $cta_link['url'] : (string) site_url('/contatti/');
$cta_target = is_array($cta_link) && !empty($cta_link['target']) ? (string) $cta_link['target'] : '_self';

$hs_portal = function_exists('get_field') ? (string) (get_field('footer_hubspot_portal_id', 'option') ?: '') : '';
$hs_form   = function_exists('get_field') ? (string) (get_field('footer_hubspot_form_id', 'option') ?: '') : '';
$hs_target = function_exists('get_field') ? (string) (get_field('footer_hubspot_target', 'option') ?: '#hs-newsletter-form') : '#hs-newsletter-form';

$links = function_exists('get_field') ? (array) (get_field('footer_links', 'option') ?: []) : [];

$fb = function_exists('get_field') ? (string) (get_field('footer_social_facebook', 'option') ?: '') : '';
$tt = function_exists('get_field') ? (string) (get_field('footer_social_tiktok', 'option') ?: '') : '';
$ig = function_exists('get_field') ? (string) (get_field('footer_social_instagram', 'option') ?: '') : '';

$exclude = is_singular('case_study') ? (int) get_queried_object_id() : 0;
$cs = function_exists('moma_get_footer_case_study') ? moma_get_footer_case_study($exclude) : null;

$year = date_i18n('Y');
$site = get_bloginfo('name');
?>

<footer id="colophon" class="moma-footer" role="contentinfo">
  <div class="container moma-footer__container">

    <div class="moma-footer__top">
      <h2 class="moma-footer__title"><?php echo esc_html($cta_title); ?></h2>
      <a class="moma-footer__cta" href="<?php echo esc_url($cta_url); ?>" target="<?php echo esc_attr($cta_target); ?>" rel="<?php echo $cta_target === '_blank' ? 'noopener noreferrer' : ''; ?>" aria-label="Vai ai contatti">
        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path d="M7 17L17 7" />
          <path d="M10 7h7v7" />
        </svg>
      </a>
    </div>

    <div class="moma-footer__layout">
      <!-- BOX BIANCO (solo newsletter + menu + social) -->
      <div class="flex-wrap moma-footer__box" aria-label="Newsletter e link">
        <div class="moma-footer__boxgrid">

          <div class="moma-footer__newsletter">
            <div class="moma-footer__h">Iscriviti alla newsletter</div>

            <?php if ($hs_portal && $hs_form): ?>
              <div id="hs-newsletter-form" class="moma-footer__hs"></div>
              <script>
                (function(){
                  function init(){
                    if(!window.hbspt || !window.hbspt.forms) return;
                    try{
                      window.hbspt.forms.create({
                        portalId: "<?php echo esc_js($hs_portal); ?>",
                        formId: "<?php echo esc_js($hs_form); ?>",
                        target: "<?php echo esc_js($hs_target); ?>"
                      });
                    }catch(e){}
                  }
                  if(window.hbspt && window.hbspt.forms) init();
                  else document.addEventListener('DOMContentLoaded', init);
                })();
              </script>
            <?php else: ?>
              <!-- Fallback (solo per mantenere il layout se HubSpot non è ancora configurato) -->
              <form class="moma-footer__stub" action="#" onsubmit="return false;">
                <input type="email" placeholder="email@address.com" aria-label="Email" />
                <button class="m-btn m-btn--form" type="submit"><span class="m-btn__label">iscriviti.</span></button>
              </form>
            <?php endif; ?>
          </div>

          <?php if (!empty($links)): ?>
            <nav class="moma-footer__menu" aria-label="Link footer">
              <ul>
                <?php foreach ($links as $row):
                  $page_id = isset($row['page']) ? (int) $row['page'] : 0;
                  if (!$page_id) continue;
                  $label = isset($row['label']) && $row['label'] ? (string) $row['label'] : get_the_title($page_id);
                  $url   = get_permalink($page_id);
                ?>
                  <li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </nav>
          <?php else: ?>
            <nav class="moma-footer__menu" aria-label="Link footer">
              <?php
                // fallback leggero: mostra il menu primary se esiste
                if (has_nav_menu('primary')) {
                  wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'depth'          => 1,
                    'items_wrap'     => '<ul>%3$s</ul>',
                  ]);
                } else {
                  echo '<ul><li><a href="' . esc_url(site_url('/servizi/')) . '">Servizi</a></li><li><a href="' . esc_url(site_url('/portfolio/')) . '">Portfolio</a></li><li><a href="' . esc_url(site_url('/team/')) . '">Team</a></li><li><a href="' . esc_url(site_url('/newsletter/')) . '">Newsletter</a></li></ul>';
                }
              ?>
            </nav>
          <?php endif; ?>

          <div class="moma-footer__social" aria-label="Social">
            <?php if ($fb): ?>
              <a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path d="M13.5 8.5V7.2c0-.7.5-1.2 1.2-1.2h1.3V3h-2.1C11.7 3 10 4.7 10 6.9v1.6H8v3h2V21h3.1v-9.5h2.6l.4-3h-3z" />
                </svg>
              </a>
            <?php endif; ?>
            <?php if ($tt): ?>
              <a href="<?php echo esc_url($tt); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path d="M16.7 3c.3 2.5 2.1 4.4 4.3 4.7v3.3c-1.8-.1-3.3-.7-4.4-1.6v6.6c0 4-3.2 7.3-7.2 7.3S2.2 20.2 2.2 16.2c0-4 3.2-7.3 7.2-7.3.4 0 .7 0 1.1.1v3.6c-.3-.1-.7-.2-1.1-.2-2 0-3.6 1.6-3.6 3.7 0 2 1.6 3.7 3.6 3.7s3.6-1.6 3.6-3.7V3h3.7z" />
                </svg>
              </a>
            <?php endif; ?>
            <?php if ($ig): ?>
              <a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm-5 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm4.5-2.3a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4z" />
                </svg>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <!-- CLAIM (FUORI dal box bianco, come in Figma) -->
        <div class="flex flex-col !mt-10 moma-footer__claim" aria-label="Claim">
          <div class="moma-footer__claim-top font-fraunces">Esperienza, strategia, visione.</div>
          <div class="moma-footer__claim-bottom font-fraunces"><span class="moma-footer__claim-accent">Siamo un punto fermo</span><span class="moma-footer__claim-dot">.</span></div>
        </div>
      </div>

      <!-- CASE STUDY BOX (alto come il box bianco) -->
      <div class="moma-footer__case" aria-label="Case study">
        <?php if ($cs instanceof WP_Post):
          $thumb_id = (int) get_post_thumbnail_id($cs);
          $title    = get_the_title($cs);
          $subtitle = function_exists('get_field') ? (string) (get_field('cs_subtitle', $cs->ID) ?: '') : '';
          $subtitle = $subtitle ?: wp_trim_words(wp_strip_all_tags(get_the_excerpt($cs)), 10);
        ?>
          <a class="moma-footer__case-link" href="<?php echo esc_url(get_permalink($cs)); ?>">
            <div class="moma-footer__case-media">
              <?php
                if ($thumb_id) {
                  echo wp_get_attachment_image($thumb_id, 'large', false, [
                    'class'   => 'moma-footer__case-img',
                    'loading' => 'lazy',
                    'alt'     => '',
                  ]);
                } else {
                  echo '<div class="moma-footer__case-img moma-footer__case-img--placeholder" aria-hidden="true"></div>';
                }
              ?>

              <div class="moma-footer__case-overlay" aria-hidden="true"></div>
              <div class="moma-footer__case-text">
                <div class="moma-footer__case-title"><?php echo esc_html($title); ?></div>
                <div class="moma-footer__case-subtitle"><?php echo esc_html($subtitle); ?></div>
              </div>
              <span class="moma-footer__case-dot" aria-hidden="true"></span>
            </div>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div class="moma-footer__copyright">
      &copy; <?php echo esc_html($year); ?> - <?php echo esc_html($site); ?>
    </div>

  </div>

  <?php do_action('tailpress_footer'); ?>
</footer>
