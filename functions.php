<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

/**
 * @snippet		Load assets
 */
function jf_add_assets() {
    wp_enqueue_style('css', get_stylesheet_directory_uri() . '/style.css', array(), '1.0.0');
    wp_enqueue_script('mega-menu-js', get_stylesheet_directory_uri() . '/inc/assets/mega-menu.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'jf_add_assets');

/**
 * @snippet		Load slick sliders
 */
function jf_theme_enqueue_swiper() {
    wp_enqueue_style('swiper-css', get_stylesheet_directory_uri() . '/assets/swiper/swiper-bundle.min.css', array(), '10.0.0');
    wp_enqueue_script('swiper-js', get_stylesheet_directory_uri() . '/assets/swiper/swiper-bundle.min.js', array(), '10.0.0', true);
    wp_enqueue_script('custom-js', esc_url(get_stylesheet_directory_uri() . '/assets/swiper/custom.js'), ['swiper-js'], null, true);
}
add_action('wp_enqueue_scripts', 'jf_theme_enqueue_swiper');

/**
 * @snippet		Load child theme files with shortcodes and custom functions
 */
require_once get_stylesheet_directory() . '/inc/jf-shortcodes.php';
require_once get_stylesheet_directory() . '/inc/jf-custom-functions.php';

/**
 * @snippet		Register Wordpress Menu items for Mobile menu header
 * @location	Header
 * @description Call Wordpress menus for custom mobile header
 */
function jf_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'hello-elementor'),
    ));
}
add_action('after_setup_theme', 'jf_register_menus');

/**
 * @snippet		Allow SVG uploads
 */
function allow_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_uploads');

/**
 * @snippet		Disable Woocommerce Brands
 * @location	Wordpress dashboard & Woocommerce
 * @Description Disable brands from loading because we do not have additional brands 
 */
add_action( 'init', function() {
    update_option( 'wc_feature_woocommerce_brands_enabled', 'no' );
} );

/**
 * @snippet		Disable Block Library stylesheets
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
}, 110 );

/**
 * @snippet		Add banner at single page template
 * @location	Single pages
 * @Description   
 */
function add_jf_hero_banner() {
    if ( ! is_admin() && ! function_exists( 'elementor_theme_do_location' ) ) {
        echo do_shortcode('[jf_hero_banner]');
    }
}
add_action('wp_body_open', 'add_jf_hero_banner');


/**
*	@Woocommerce My Account
*/
/**
 * @snippet		Add custom content to dashboard
 * @location	Woocommerce My Account
 * @Description   
 */
function jf_custom_account_dashboard_content() {
    ?>
    <div class="jf-account-dashboard">
        <!-- Full-Width Deliveries Box -->
        <div class="jf-dashboard-box full-width">
            <div class="jf-dashboard-icon">🚚</div>
            <h3>Deliveries</h3>
            <p>No upcoming deliveries</p>
        </div>

        <div class="jf-dashboard-row">
            <!-- Half-Width Subscriptions Box -->
            <div class="jf-dashboard-box orange half-width">
                <div class="jf-dashboard-icon">📦</div>
                <h3>Subscriptions</h3>
                <p>Within your subscriptions you can easily edit your flavours, change the delivery date, pause or even cancel your subscription.</p>
				<button class="jf-btn-black">Go to Shop</button>
            </div>

            <!-- Half-Width Referrals Box -->
            <div class="jf-dashboard-box green half-width">
                <div class="jf-dashboard-icon">🎁</div>
                <h3>Refer friends. Earn rewards</h3>
                <p>Pošalji poveznicu od Juicefasta svojim prijateljima osvoji 25 BODOVA koje možeš iskoristiti za iduću kupnju Juicefast proizvoda.</p>
				<button class="jf-btn-black">Preporuči prijateljima</button>
            </div>
        </div>
    </div>
    <?php
}
add_action('woocommerce_account_dashboard', 'jf_custom_account_dashboard_content');

/**
 * @snippet		Add content after product title
 * @location	Woo Product Shop & Category pages
 * @Description Product short icon list with benefits  
 */
function jf_add_custom_product_info() {
    global $product;

    // Get the ACF WYSIWYG field value
    $benefits = get_field('single_product_benefits', $product->get_id());

    // Output only if there's content
    if ($benefits) {
        echo '<div class="jf-pr-category-benefits">';
        echo $benefits; // Outputs HTML from WYSIWYG
        echo '</div>';
    }
}
add_action('woocommerce_shop_loop_item_title', 'jf_add_custom_product_info', 15);

/**
 * @snippet		Display default Woo variable price as single price "from:"
 * @location	Woo Product Shop & Category pages
 * @Description Default Woo variable prices is structured like 10€-150€. We want single price based on default/choosed variation in form "From: 50.99€".
 */
function jf_modify_variable_price_display( $price, $product ) {
    if ( $product->is_type( 'variable' ) ) {
        $min_price     = $product->get_variation_price( 'min', true );
        $min_reg_price = $product->get_variation_regular_price( 'min', true );

        if ( $min_price < $min_reg_price ) {
            // Sale is active, show regular price with strikethrough + sale price
            $price = sprintf(
                __( 'From <span style="color: #000;"><ins>%s</ins> <del>%s</del></span>', 'woocommerce' ),
                wc_price( $min_reg_price ),
                wc_price( $min_price )
            );
        } else {
            // No sale, show regular price only
            $price = sprintf(
                __( 'From %s', 'woocommerce' ),
                wc_price( $min_price )
            );
        }
    }

    return $price;
}
add_filter( 'woocommerce_get_price_html', 'jf_modify_variable_price_display', 10, 2 );

/**
 * @snippet		Display Percentage Discount on all Woocommerce "SALE" Badges
 * @location	Woo Product Shop & Category pages & Loop products
 * @Description Replace default Woo sale badge with title "Uštedi" to badge with maximum discount in percentages for that product
 */
add_filter('woocommerce_sale_flash', 'juicefast_custom_sale_percentage_flash', 10, 3);
function juicefast_custom_sale_percentage_flash($html, $post, $product) {
    if ($product->is_type('variable')) {
        $max_percentage = 0; // Initialize maximum discount percentage

        // Loop through each variation of the variable product
        foreach ($product->get_children() as $child_id) {
            $variation = wc_get_product($child_id);
            $regular_price = $variation->get_regular_price();
            $sale_price = $variation->get_sale_price();

            if ($sale_price && $regular_price) {
                $percentage = round(100 - ($sale_price / $regular_price * 100));
                $max_percentage = max($max_percentage, $percentage); // Update maximum discount percentage
            }
        }

        // Display maximum discount percentage if greater than 0
        if ($max_percentage > 0) {
            return '<span class="onsale"> Save -' . $max_percentage . '%</span>';
        } else {
            return $html;
        }
    } else {
        // For simple products, display the discount percentage if applicable
        if ($product->is_on_sale() && $product->get_regular_price()) {
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();

            $percentage = round(100 - ($sale_price / $regular_price * 100));
            if ($percentage > 0) {
                return '<span class="onsale">Save  -' . $percentage . '%</span>';
            }
        }
    }

    return $html;
}

// JS
function jf_enqueue_variation_discount_script() {
    if (is_product()) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.variations_form').on('found_variation', function(event, variation) {
                    var regularPrice = parseFloat(variation.display_regular_price);
                    var salePrice = parseFloat(variation.display_price);

                    if (regularPrice > salePrice) {
                        var percentage = Math.round(100 - (salePrice / regularPrice * 100));
                        if (percentage > 0) {
                            $('.onsale').html('Save -' + percentage + '%');
                        } else {
                            $('.onsale').html('Sale'); // Default text when no percentage discount
                        }
                    } else {
                        $('.onsale').html('Sale'); // Fallback if no sale is detected
                    }
                });

                // Reset badge when changing variation or no variation is found
                $('.variations_form').on('reset_data', function() {
                    $('.onsale').html('Sale'); // Reset to default text
                });
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'jf_enqueue_variation_discount_script');

/**
 * @snippet		Add class .skip-lazy
 * @location	Woo Product Shop & Category pages
 * @description Add class .skip-lazy on first 4 category images to ensure images are visible on the first load
 */
add_filter( 'wp_lazy_loading_enabled', '__return_false' );

/**
 * @snippet		Register Wordpress Menu items for Footer
 * @location	Footer
 * @description Call Wordpress menus for custom footer
 */
function my_custom_footer_menus() {
    register_nav_menus( array(
        'naši_proizvodi_menu'  => 'Naši proizvodi - Footer',
        'saznaj_vise_menu'      => 'Saznaj više - Footer',
        'o_juicefastu_menu'    => 'O Juicefastu - Footer',
        'informacije_menu'     => 'Informacije - Footer',
    ));
}
add_action( 'after_setup_theme', 'my_custom_footer_menus' );

/**
 * @snippet		Display Best Seller badge
 * @location	Woo Product Shop & Category pages & Product pages
 */
add_action('woocommerce_before_shop_loop_item_title', 'custom_best_seller_badge_by_id', 9);

function custom_best_seller_badge_by_id() {
    global $product;

    // Define the product IDs for which the badge should show
    $best_seller_ids = array(354);
    if (in_array($product->get_id(), $best_seller_ids)) {
		echo '<span class="custom-badge best-seller-badge">🔥 Best Seller</span>';
	}
}

add_action('woocommerce_after_shop_loop_item', 'jf_insert_image_after_4th_product', 20);

/**
 * @snippet		Add custom image after 4th product in woo archives
 * @location	Woo Product Shop & Category pages
 */
function jf_insert_image_after_4th_product() {
    static $counter = 0;
    $counter++;

    if ($counter == 4) {
        echo '</li>'; // Close current product loop item if needed

        echo '<li class="product jf-image-block">';
        echo '<img src="/wp-content/uploads/Juicefast-tim-1x1-1.jpg" alt="Shop Background" class="shop-fixed-img">';
        echo '<div class="jf-image-overlay">';
        echo '<h2>Fasting programs</h2>';
        echo '<a href="/shop" class="jf-button">View all fasting products</a>';
        echo '</div>';
        echo '</li>';
    }
}

/**
 * @snippet		Add reviews stars above product title
 * @location	Woo Product Shop & Category pages & Product pages
 */
// Remove the default position (below the title)
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
// Add star rating above the title
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );

/**
 * @snippet WooCommerce Display Variations as Radio Buttons
 * @author Misha Rudrastyh
 * @url https://rudrastyh.com/woocommerce/variations-as-radio-buttons.html
 */
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'custom_variation_attribute_display', 20, 2 );
function custom_variation_attribute_display( $html, $args ) {
	$attribute = $args['attribute'];
	$options   = $args['options'];
	$product   = $args['product'];
	$name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class     = $args['class'];

	if ( empty( $options ) || ! $product ) {
		return $html;
	}

	// --- RADIO BUTTONI ZA 'pa_trajanje' ---
	if ( 'pa_trajanje' === $attribute ) {
		$output = '<div class="rudr-variation-radios rudr-inline">';

		if ( taxonomy_exists( $attribute ) ) {
			$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options, true ) ) {
					$checked = checked( $args['selected'], $term->slug, false );
					$output .= "<input type=\"radio\" id=\"{$name}-{$term->slug}\" name=\"{$name}\" value=\"{$term->slug}\" {$checked}>
						<label for=\"{$name}-{$term->slug}\">
							{$term->name}
							<div class=\"jf-price-info\" data-trajanje=\"{$term->slug}\">
								<span class=\"jf-sale-price\"></span>
								<span class=\"jf-regular-price\"></span>
								<span class=\"jf-discount\"></span>
							</div>
						</label>";
				}
			}
		}

		$output .= '</div>';
		return $html . $output;
	}

	// --- BUTTONI ZA 'pa_velicina' ---
	if ( 'pa_velicina' === $attribute ) {
		$output = '<div class="rudr-button-options rudr-inline">';

		if ( taxonomy_exists( $attribute ) ) {
			$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options, true ) ) {
					$active = $args['selected'] === $term->slug ? 'active' : '';
					$output .= "<button type=\"button\" class=\"rudr-button-option {$active}\" data-name=\"{$name}\" data-value=\"{$term->slug}\">{$term->name}</button>";
				}
			}
		}

		$output .= '</div>';
		return $html . $output;
	}

	return $html;
}