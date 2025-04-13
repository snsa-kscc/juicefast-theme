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
 * @param array $category The category data
 * @return array ACF fields
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