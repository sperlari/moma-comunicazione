export function initMomaNav() {
  const mobile = document.getElementById("moma-mobile");
  const burger = document.querySelector(".moma-burger");
  const close  = document.querySelector(".moma-mobile__close");
  if (!mobile || !burger || !close) return;

  const open = () => {
    mobile.hidden = false;
    burger.setAttribute("aria-expanded", "true");
    document.documentElement.classList.add("is-menu-open");
  };
  const shut = () => {
    mobile.hidden = true;
    burger.setAttribute("aria-expanded", "false");
    document.documentElement.classList.remove("is-menu-open");
    // chiudi eventuali sub-open
    mobile.querySelectorAll("li.is-open").forEach(li => li.classList.remove("is-open"));
  };

  burger.addEventListener("click", open);
  close.addEventListener("click", shut);
  mobile.addEventListener("click", (e) => {
    if (e.target === mobile) shut();
  });

  // Mobile: tap su item con children -> toggle card submenu (non navigare)
  mobile.querySelectorAll("li.menu-item-has-children > a").forEach((a) => {
    a.addEventListener("click", (e) => {
      e.preventDefault();
      const li = a.closest("li");
      if (!li) return;
      const isOpen = li.classList.contains("is-open");
      mobile.querySelectorAll("li.is-open").forEach(x => x.classList.remove("is-open"));
      if (!isOpen) li.classList.add("is-open");
    });
  });
}