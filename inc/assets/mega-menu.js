/**
 * JuiceFast Mega Menu Functionality
 */
document.addEventListener("DOMContentLoaded", function () {
  // Add visual indicator for menu items with products
  const productMenuItems = document.querySelectorAll(".menu-item .jf-category-products-wrapper");
  productMenuItems.forEach((wrapper) => {
    const parentItem = wrapper.closest("li");
    if (parentItem) {
      parentItem.classList.add("has-products");
    }
  });

  // Fix any z-index issues with submenus
  const subMenus = document.querySelectorAll(".sub-menu");
  for (let i = 0; i < subMenus.length; i++) {
    subMenus[i].style.zIndex = 100 + i;
  }

  // Add hover animation effects
  const menuLinks = document.querySelectorAll(".jf-menu > li > a");
  menuLinks.forEach((link) => {
    link.addEventListener("mouseenter", function () {
      link.style.transform = "translateY(-2px)";
    });

    link.addEventListener("mouseleave", function () {
      link.style.transform = "translateY(0)";
    });
  });

  // Add arrow icons only to top-level menu items with children
  const hasChildrenItems = document.querySelectorAll(".jf-menu > li.menu-item-has-children > a");
  hasChildrenItems.forEach((item) => {
    if (!item.querySelector(".jf-menu-arrow")) {
      const arrow = document.createElement("span");
      arrow.className = "jf-menu-arrow";
      arrow.innerHTML = `<svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`;
      item.appendChild(arrow);
    }
  });

  // Remove any existing arrows from submenu items
  const submenuChildrenItems = document.querySelectorAll(".sub-menu .menu-item-has-children > a .jf-menu-arrow");
  submenuChildrenItems.forEach((arrow) => {
    arrow.remove();
  });

  // Add current-menu-item class for active submenus
  const submenuItems = document.querySelectorAll(".jf-menu .sub-menu > li");
  submenuItems.forEach((item) => {
    item.addEventListener("click", function () {
      // Remove current-menu-item class from all siblings
      const siblings = Array.from(item.parentNode.children);
      siblings.forEach((sibling) => {
        sibling.classList.remove("current-menu-item");
      });

      // Add current-menu-item class to clicked item
      item.classList.add("current-menu-item");
    });
  });
});
