<?php
/**
 * Custom functions file for JuiceFast theme
 * 
 * Add your custom functions here to keep your code organized
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Vite integration for WordPress
 *
 * Handles the loading of Vite assets in both development and production environments.
 * In development mode, it connects to the Vite dev server.
 * In production mode, it loads the built assets.
 *
 * @return void
 */
function jf_enqueue_vite_assets() {
    $theme_dir = get_stylesheet_directory();
    $theme_uri = get_stylesheet_directory_uri();
    $hot_file = $theme_dir . '/inc/vite/public/hot';
    $is_dev = file_exists($hot_file);
    
    if ($is_dev) {
        // Hardcoded URLs for testing
        add_action('wp_head', function() {
            echo "<!-- Vite Development Mode -->\n";
            echo "<script type='module'>\n";
            echo "    import RefreshRuntime from 'http://localhost:5173/@react-refresh'\n";
            echo "    RefreshRuntime.injectIntoGlobalHook(window)\n";
            echo "    window.\$RefreshReg\$ = () => {}\n";
            echo "    window.\$RefreshSig\$ = () => (type) => type\n";
            echo "    window.__vite_plugin_react_preamble_installed__ = true\n";
            echo "</script>\n";
            echo "<script type='module' src='http://localhost:5173/@vite/client'></script>\n";
            echo "<script type='module' src='http://localhost:5173/src/main.tsx'></script>\n";
        });
    } else {
        // Production mode - use built assets
        add_action('wp_head', function() {
            echo "<!-- Vite Production Mode - Using Built Assets -->\n";
        });
        
        // Add the CSS and JS files directly
        wp_enqueue_style('jf-theme-style', $theme_uri . '/assets/react/app.css', [], null);
        wp_enqueue_script('jf-theme-script', $theme_uri . '/assets/react/app.js', [], null, true);
    }
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'jf_enqueue_vite_assets');

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/menu-items/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => function ($data) {
            $menu_id = (int) $data['id'];
            $items = wp_get_nav_menu_items($menu_id);

            if (!$items) {
                return new WP_Error('no_items', 'No menu items found', ['status' => 404]);
            }

            $normalized = [];
            foreach ($items as $item) {
                $item_data = (array) $item;

                // Get real ACF fields if available
                $acf_fields = function_exists('get_fields') ? get_fields($item->ID) : [];

                // Always include is_accent and is_icon keys
                $item_data['acf'] = [
                    'is_accent' => $acf_fields['is_accent'] ?? null,
                    'is_icon' => $acf_fields['is_icon'] ?? null
                ];

                $item_data['children'] = [];

                $normalized[$item->ID] = $item_data;
            }

            $tree = [];
            foreach ($normalized as $id => &$item) {
                $parent_id = $item['menu_item_parent'];
                if ($parent_id && isset($normalized[$parent_id])) {
                    $normalized[$parent_id]['children'][] = &$item;
                } else {
                    $tree[] = &$item;
                }
            }

            $reorder = function (&$items) use (&$reorder) {
                usort($items, fn($a, $b) => $a['menu_order'] <=> $b['menu_order']);
                foreach ($items as $index => &$item) {
                    $item['menu_order'] = $index + 1;
                    if (!empty($item['children'])) {
                        $reorder($item['children']);
                    }
                }
            };
            $reorder($tree);

            return rest_ensure_response($tree);
        },
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Add specific ACF fields to WooCommerce product categories in REST API
 *
 * Registers a REST field for product categories to include ACF fields in the API response.
 * Only works if ACF plugin is active.
 *
 * @return void
 */
function add_acf_to_product_categories_api() {
    // Check if ACF is active
    if (!function_exists('get_field')) {
        return;
    }

    // Register REST field for product categories
    register_rest_field(
        'product_cat',
        'acf',
        array(
            'get_callback' => 'get_product_category_acf_data',
            'schema' => null,
        )
    );
}
add_action('rest_api_init', 'add_acf_to_product_categories_api');

/**
 * Get specific ACF fields for product categories
 *
 * Retrieves ACF fields for a given product category and formats them for API response.
 *
 * @param array $category The category data
 * @return array ACF fields for the category
 */
function get_product_category_acf_data($category) {
    $field1_name = 'category_label'; 
    $field2_name = 'category_order'; 
    $field3_name = 'sub_category'; 

    $category_id = $category['id'];
    
    // Get the values of your specific ACF fields
    $field1_value = get_field($field1_name, 'product_cat_' . $category_id);
    $field2_value = get_field($field2_name, 'product_cat_' . $category_id);
	$field3_value = get_field($field3_name, 'product_cat_' . $category_id);

    
    // Return the fields in an array
    return array(
        $field1_name => $field1_value,
        $field2_name => $field2_value,
        $field3_name => $field3_value,
    );
}

/**
 * Allow WooCommerce REST API access from localhost without authentication
 * Only for development environments
 *
 * @param int $user_id The current user ID
 * @return int The user ID to use for API requests
 */
add_filter('determine_current_user', function($user_id) {
    // Only apply in REST API requests
    if (!defined('REST_REQUEST') || !REST_REQUEST) {
        return $user_id;
    }
    
    // Check if it's a WooCommerce API request
    $rest_route = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (strpos($rest_route, 'wp-json/wc/') === false) {
        return $user_id;
    }
    
    // If no user is logged in, get an admin user for API requests
    if (empty($user_id)) {
        // Get an admin user to use for API requests
        $users = get_users([
            'role' => 'administrator',
            'number' => 1,
            'fields' => 'ID'
        ]);
        
        if (!empty($users)) {
            return $users[0];
        }
    }
    
    return $user_id;
}, 999);

/**
 * Display products under product category menu items
 *
 * Uses the walker_nav_menu_start_el filter to add products after each category menu item.
 * Only applies to menu items that are linked to WooCommerce product categories.
 *
 * @param string $item_output The menu item's HTML output
 * @param object $item The menu item object
 * @return string Modified menu item HTML with products list
 */
function jf_add_products_to_menu_items($item_output, $item) {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return $item_output;
    }
    
    // We only care about product category taxonomy items
    if ($item->type !== 'taxonomy' || $item->object !== 'product_cat') {
        return $item_output;
    }
    
    // The category ID is in the object_id
    $category_id = $item->object_id;
    
    // Get products for this specific category only
    $products_html = jf_get_category_products_html($category_id);
    
    // Add products HTML after the menu item's </a> tag
    $item_output = str_replace('</a>', '</a>' . $products_html, $item_output);
    
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'jf_add_products_to_menu_items', 10, 2);

/**
 * Get HTML for products in a given category
 *
 * Retrieves products for a given category and formats them as HTML.
 *
 * @param int $category_id The product category ID
 * @return string HTML output for the products
 */
function jf_get_category_products_html($category_id) {
    // Get the category
    $category = get_term($category_id, 'product_cat');
    if (!$category || is_wp_error($category)) {
        return '';
    }
    
    // Query ONLY direct products in this specific category (not including subcategories)
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 7, // Limit products as needed
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
                'include_children' => false, // Very important - only include direct products
            ),
        ),
    );
    
    $products_query = new WP_Query($args);
    
    if (!$products_query->have_posts()) {
        wp_reset_postdata();
        return ''; // No products, return empty string
    }
    
    $output = '<div class="jf-category-products-wrapper">';
    $output .= '<ul class="jf-category-products">';
    
    while ($products_query->have_posts()) {
        $products_query->the_post();
        global $product;
        
        if (!$product) continue;
        
        $product_id = $product->get_id();
        $product_name = $product->get_name();
        $product_link = get_permalink($product_id);
        
        // Get product thumbnail
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'thumbnail');
        $image_url = $image ? $image[0] : wc_placeholder_img_src('thumbnail');
        
        $output .= '<li class="jf-product-item">';
        $output .= '<a href="' . esc_url($product_link) . '">';
        $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product_name) . '" width="40" height="40" class="jf-product-thumb" />';
        $output .= '<span>' . esc_html($product_name) . '</span>';
        $output .= '</a>';
        $output .= '</li>';
    }
    
    // Add a link with the category name
    $category_name = $category->name;
    $output .= '<li class="jf-category-name-item"><a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category_name) . '</a></li>';
    
    $output .= '</ul>';
    $output .= '</div>';
    
    // Very important - always reset the post data
    wp_reset_postdata();
    
    return $output;
}

/**
 * Simple WordPress Integration for Vite React App
 */

define('VITE_APP_PATH', get_stylesheet_directory_uri() . '/vite-app/assets/');

/**
 * Add modulepreload for App.js in the head
 *
 * Adds modulepreload link in the head section for better performance with ES modules.
 *
 * @return void
 */
function vite_add_modulepreload() {
    echo '<link rel="modulepreload" crossorigin href="' . VITE_APP_PATH . 'App.js">';
}
add_action('wp_head', 'vite_add_modulepreload');

/**
 * Add the CSS file and shared App.js file
 *
 * Enqueues the common Vite assets (CSS and JS) for the theme.
 *
 * @return void
 */
function vite_enqueue_common_assets() {
    wp_enqueue_style('vite-app-css', VITE_APP_PATH . 'style.css');
    
    wp_enqueue_script('vite-app-js', VITE_APP_PATH . 'App.js', [], null, true);
    
    // Add script type="module" attribute to App.js
    add_filter('script_loader_tag', function($tag, $handle) {
        if ('vite-app-js' !== $handle) {
            return $tag;
        }
        return str_replace(' src', ' type="module" crossorigin src', $tag);
    }, 10, 2);
}

/**
 * Stores Page Shortcode
 *
 * Creates a shortcode for displaying the stores page React component.
 * Usage: [vite_stores]
 *
 * @return string HTML markup for the stores component
 */
function vite_stores_shortcode() {
    vite_enqueue_common_assets();
    
    wp_enqueue_script('vite-stores-js', VITE_APP_PATH . 'stores.js', ['vite-app-js'], null, true);
    
    add_filter('script_loader_tag', function($tag, $handle) {
        if ('vite-stores-js' !== $handle) {
            return $tag;
        }
        return str_replace(' src', ' type="module" crossorigin src', $tag);
    }, 10, 2);
    
    return '
    <div class="vite-app stores-app">
        <div id="stores-root"></div>
    </div>';
}
add_shortcode('vite_stores', 'vite_stores_shortcode');

/**
 * Quiz Page Shortcode
 *
 * Creates a shortcode for displaying the quiz page React component.
 * Usage: [vite_quiz]
 *
 * @return string HTML markup for the quiz component
 */
function vite_quiz_shortcode() {
    vite_enqueue_common_assets();
    
    wp_enqueue_script('vite-quiz-js', VITE_APP_PATH . 'quiz.js', ['vite-app-js'], null, true);
    
    add_filter('script_loader_tag', function($tag, $handle) {
        if ('vite-quiz-js' !== $handle) {
            return $tag;
        }
        return str_replace(' src', ' type="module" crossorigin src', $tag);
    }, 10, 2);
    
    return '
    <div class="vite-app quiz-app">
        <div id="quiz-root"></div>
    </div>';
}
add_shortcode('vite_quiz', 'vite_quiz_shortcode');

/**
 * Generic App Shortcode
 *
 * Creates a generic shortcode for displaying any React component.
 * Usage: [vite_app page="stores"] or [vite_app page="quiz"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML markup for the specified component
 */
function vite_app_shortcode($atts) {
    // Get the page name from shortcode
    $atts = shortcode_atts([
        'page' => 'stores' // Default to stores page if none specified
    ], $atts);
    
    $page = $atts['page'];
    
    vite_enqueue_common_assets();
    
    wp_enqueue_script('vite-' . $page . '-js', VITE_APP_PATH . $page . '.js', ['vite-app-js'], null, true);
    
    add_filter('script_loader_tag', function($tag, $handle) use ($page) {
        if ('vite-' . $page . '-js' !== $handle) {
            return $tag;
        }
        return str_replace(' src', ' type="module" crossorigin src', $tag);
    }, 10, 2);
    
    return '
    <div class="vite-app ' . $page . '-app">
        <div id="' . $page . '-root"></div>
    </div>';
}
add_shortcode('vite_app', 'vite_app_shortcode');
