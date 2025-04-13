<?php
/**
 * The Template for displaying product archives, including the main shop page.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
get_template_part('template-parts/hero-banner');
?>

<div class="jf-shop-container">
    <!-- Left Sidebar for Filters -->
    <aside class="jf-shop-sidebar">
        <h3 class="jf-sidebar-title">Filteri</h3>

        <?php echo do_shortcode('[br_filters_group group_id=2915]'); ?>
    </aside>

    <!-- Right Section for Products -->
    <main class="jf-shop-products">
        <?php
        if ( woocommerce_product_loop() ) {
            do_action( 'woocommerce_before_shop_loop' );
            woocommerce_product_loop_start();

            $counter = 0; // Counter to track products

            if ( wc_get_loop_prop( 'total' ) ) {
                while ( have_posts() ) {
                    the_post();
                    $counter++;
                    do_action( 'woocommerce_shop_loop' );
                    wc_get_template_part( 'content', 'product' );

                    // After the fourth product, inject the image as a separate grid element
                    if ( $counter == 4 ) {
                        echo '<div class="jf-image-block">';
                        echo '<img src="/wp-content/uploads/Juicefast-tim-1x1-1.jpg" alt="Shop Background" class="shop-fixed-img">';
                        echo '<div class="jf-image-overlay">';
                        echo '<h2>Why 60.000 people using Juicefast</h2>';
                        echo '<a href="/shop" class="jf-button">Saznaj više i kupi</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }

            woocommerce_product_loop_end();
            do_action( 'woocommerce_after_shop_loop' );
        } else {
            do_action( 'woocommerce_no_products_found' );
        }
        ?>
    </main>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
?>