/**
 * JuiceFast Mega Menu Functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Mega menu script loaded');
    
    // Basic hover functionality is already handled by CSS :hover
    // This is just for additional functionality
    
    // Make submenus appear when parent is hovered
    const menuItems = document.querySelectorAll('.menu-item-has-children');
    menuItems.forEach(item => {
        // Nothing needed here, CSS handles hover states
    });
    
    // Add visual indicator for menu items with products
    const productMenuItems = document.querySelectorAll('.menu-item .jf-category-products-wrapper');
    productMenuItems.forEach(wrapper => {
        const parentItem = wrapper.closest('li');
        if (parentItem) {
            parentItem.classList.add('has-products');
        }
    });
    
    // Fix any z-index issues with submenus
    const subMenus = document.querySelectorAll('.sub-menu');
    for (let i = 0; i < subMenus.length; i++) {
        subMenus[i].style.zIndex = 100 + i;
    }
});
