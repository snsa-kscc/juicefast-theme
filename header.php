<?php
/**
 * Custom Header Template for Hello Elementor Child Theme
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
        <link rel="profile" href="https://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'hello-elementor' ); ?></a>
<!-- HEADER START -->
<div class="jf-text-slider">
    <div class="jf-top-header swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="<?php echo esc_url('/wp-content/uploads/2025/02/discount.svg'); ?>" alt="Up To 30% Off" width="25" height="25">
                <span><b>Up To 30% Off</b> | With code NEWYOU30.</span>
            </div>
            <div class="swiper-slide">
                <img src="<?php echo esc_url('/wp-content/uploads/2025/02/subscribe.svg'); ?>" alt="Subscribe" width="25" height="25">
                <span><b>Subscribe</b> | For a best price</span>
            </div>
            <div class="swiper-slide">
                <img src="<?php echo esc_url('/wp-content/uploads/2025/02/money-2.svg'); ?>" alt="Recommend & Earn" width="25" height="25">
                <span><b>Recommend & Earn</b> | Bring your buddy</span>
            </div>
			<!-- Duplicate -->
			<div class="swiper-slide">
                <img src="<?php echo esc_url('/wp-content/uploads/2025/02/discount.svg'); ?>" alt="Up To 30% Off" width="25" height="25">
                <span><b>Up To 30% Off</b> | With code NEWYOU30.</span>
            </div>
            <div class="swiper-slide">
                <img src="<?php echo esc_url('/wp-content/uploads/2025/02/subscribe.svg'); ?>" alt="Subscribe" width="25" height="25">
                <span><b>Subscribe</b> | For a best price</span>
            </div>
            <div class="swiper-slide">
                <img src="<?php echo esc_url('/wp-content/uploads/2025/02/money-2.svg'); ?>" alt="Recommend & Earn" width="25" height="25">
                <span><b>Recommend & Earn</b> | Bring your buddy</span>
            </div>
        </div>
    </div>
</div>

<header class="jf-custom-header">
    <div class="jf-header-container">
        <div class="jf-logo">
            <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="Juicefast logo">
                <?php the_custom_logo(); ?>
            </a>
        </div>

        <nav class="jf-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'jf-menu',
                'container'      => false
            ));
            ?>
        </nav>

        <div class="jf-header-icons">
            <a href="#" class="jf-btn-find-juicefast jf-green_button">Take the quiz</a>

            <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="jf-icon-link jf-faq-icon">
                <img src="<?php echo esc_url($upload_dir . '/wp-content/uploads/2025/02/faq-icon.svg'); ?>" alt="FAQ">
            </a>

            <a href="<?php echo esc_url(home_url('/moj-racun/')); ?>" class="jf-icon-link jf-user-icon">
                <img src="<?php echo esc_url($upload_dir . '/wp-content/uploads/2025/02/user-icon.svg'); ?>" alt="My Account">
            </a>

            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="jf-icon-link jf-cart-icon">
                <img src="<?php echo esc_url($upload_dir . '/wp-content/uploads/2025/02/cart-icon.svg'); ?>" alt="Cart">
                <span class="jf-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            </a>
        </div>
    </div>
</header>

<!-- <div id="react-island-products"></div> -->
<!-- HEADER END -->

<!-- Smooth Infinite Scroll Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    new Swiper('.jf-top-header', {
        slidesPerView: 'auto',
        loop: true,
        loopedSlides: 6,
        speed: 4800,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
        },
        allowTouchMove: false,
        freeMode: true,
        freeModeMomentum: false,
    });
});
</script>

<main id="content">