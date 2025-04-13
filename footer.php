<?php
/**
 * Custom 4-Column Footer for Hello Elementor Child Theme
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div id="footer-signup-container" style="display: flex; align-items: center;">
	<div class="footer-signup-content">
		<div>
			<h2>Pridruži se Juicefast obitelji</h2>
			<p>Prijavi se i iscijedi popuste, 15% na prvu kupnju.</p>
		</div>
		<div>
			<form action="#" method="POST">
				<input type="email" name="email" placeholder="Unesi svoju email adresu" required style="padding: 10px; width: 100%; max-width: 300px;">
				<button type="submit" style="padding: 10px 20px; margin-left: 10px; background-color: black; color: white; border: none; cursor: pointer;">
					Prijavi se
				</button>
			</form>
		</div>
	</div>
</div>
<footer id="jf-custom-footer">
    <div class="jf-footer-container">
    <div class="jf-footer-top-bar">
        <img src="/wp-content/uploads/Juicefast-logo-white.png" alt="Juicefast logo" class="jf-logo" width="120" height="37">
        <span class="jf-slogan">Postani najbolja verzija sebe</span>
        <div class="jf-social-icons">
            <a href="#" target="_blank">
                <img src="/wp-content/uploads/Facebook-icon-white.svg" alt="Facebook" width="24" height="25">
            </a>
            <a href="#" target="_blank">
                <img src="/wp-content/uploads/Tik-Tok-icon-white.svg" alt="Tik Tok" width="24" height="25">
            </a>
            <a href="#" target="_blank">
                <img src="/wp-content/uploads/Instagram-icon-white.svg" alt="Instagram" width="24" height="25">
            </a>
        </div>
    </div>
        <div class="jf-footer-columns">
			<!-- Column 1: Naši proizvodi -->
			<div class="jf-footer-column">
				<button class="footer-menu-toggle" aria-expanded="false">Naši proizvodi</button>
				<?php 
				wp_nav_menu( array(
					'theme_location' => 'naši_proizvodi_menu',
					'container'      => 'ul',
					'menu_class'     => 'footer-submenu',
				) );
				?>
			</div>

			<!-- Saznaj više -->
			<div class="jf-footer-column">
				<button class="footer-menu-toggle" aria-expanded="false">Saznaj više</button>
				<?php 
				wp_nav_menu( array(
					'theme_location' => 'saznaj_vise_menu',
					'container'      => 'ul',
					'menu_class'     => 'footer-submenu',
				) );
				?>
			</div>
			<!-- O Juicefastu -->
			<div class="jf-footer-column">
				<button class="footer-menu-toggle" aria-expanded="false">O Juicefastu</button>
				<?php 
				wp_nav_menu( array(
					'theme_location' => 'o_juicefastu_menu',
					'container'      => 'ul',
					'menu_class'     => 'footer-submenu',
				) );
				?>
			</div>
			<!-- Informacije -->
			<div class="jf-footer-column">
				<button class="footer-menu-toggle" aria-expanded="false">Informacije</button>
				<?php 
				wp_nav_menu( array(
					'theme_location' => 'informacije_menu',
					'container'      => 'ul',
					'menu_class'     => 'footer-submenu',
				) );
				?>
			</div>
        </div>
    </div>

<div class="jf-footer-bottom">
    <span>© 2025 Juicefast d.o.o. Sva prava pridržana</span>
    <div class="jf-footer-icons">
        <img src="/wp-content/uploads/Visa-white.svg" alt="Visa" width="43" height="33">
        <img src="/wp-content/uploads/Master-white.svg" alt="Mastercard" width="43" height="33">
        <img src="/wp-content/uploads/Pay-Pal-white.svg" alt="Pay Pal" width="43" height="33">
        <img src="/wp-content/uploads/Apple-Pay-white.svg" alt="Apple Pay" width="43" height="33">
        <img src="/wp-content/uploads/Google-Pay-white.svg" alt="Google Pay" width="43" height="33">
    </div>
</div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".footer-menu-toggle");

    toggles.forEach(toggle => {
        toggle.addEventListener("click", function () {
            const submenu = this.nextElementSibling;

            if (submenu.style.display === "block") {
                submenu.style.display = "none";
                this.setAttribute("aria-expanded", "false");
            } else {
                // Close other open menus
                document.querySelectorAll(".footer-submenu").forEach(menu => menu.style.display = "none");
                document.querySelectorAll(".footer-menu-toggle").forEach(btn => btn.setAttribute("aria-expanded", "false"));

                // Open the selected menu
                submenu.style.display = "block";
                this.setAttribute("aria-expanded", "true");
            }
        });
    });
});
</script>
<?php wp_footer(); ?>

</body>
</html>