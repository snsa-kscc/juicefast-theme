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

add_action('rest_api_init', function () {
    register_rest_route('store-locator/v1', '/stores', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',  // public, no auth required
        'callback' => function () {
            $data = get_option('store_locator_data', '[]');
            return rest_ensure_response(json_decode($data, true));
        },
    ]);
});

add_action('admin_menu', function () {
    add_menu_page(
        'Store Locator',
        'Store Locator',
        'manage_options',
        'store-locator-ui',
        'store_locator_render_form',
        'dashicons-location-alt',
        80
    );
});

/**
 * Render the Store Locator admin form
 *
 * Displays and processes the admin form for managing store locations.
 * Handles form submission, validation, and data storage.
 *
 * @return void
 */
function store_locator_render_form() {
    if (isset($_POST['store_locator_submit'])) {
        check_admin_referer('store_locator_save');

        $new_data = [];
        if (!empty($_POST['title'])) {
            foreach ($_POST['title'] as $index => $title) {
                $new_data[] = [
                    'title' => sanitize_text_field($title),
                    'street' => sanitize_text_field($_POST['street'][$index]),
                    'city' => sanitize_text_field($_POST['city'][$index]),
                    'postal_code' => sanitize_text_field($_POST['postal_code'][$index]),
                    'lat' => floatval(sanitize_text_field($_POST['lat'][$index])),
                    'lng' => floatval(sanitize_text_field($_POST['lng'][$index])),
                    'phone' => sanitize_text_field($_POST['phone'][$index]),
                    'email' => sanitize_email($_POST['email'][$index]),
                    'open_hours' => sanitize_text_field($_POST['open_hours'][$index]),
                ];
            }
        }

        update_option('store_locator_data', wp_json_encode($new_data));
        echo '<div class="updated"><p>Saved successfully.</p></div>';
    }

    $data = json_decode(get_option('store_locator_data', '[]'), true);
    ?>
    <div class="wrap">
        <h1>Store Locator Editor</h1>
        <form method="post">
            <?php wp_nonce_field('store_locator_save'); ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Street</th>
                        <th>City</th>
                        <th>Postal Code</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Open Hours</th>
                        <th>Del</th>
                    </tr>
                </thead>
                <tbody id="store-rows">
                    <?php foreach ($data as $i => $store): ?>
                        <tr>
                            <td><textarea name="title[]" rows="2" required><?php echo esc_textarea($store['title']); ?></textarea></td>
                            <td><textarea name="street[]" rows="2" required><?php echo esc_textarea($store['street']); ?></textarea></td>
                            <td><input type="text" name="city[]" value="<?php echo esc_attr($store['city']); ?>" required></td>
                            <td><input type="number" name="postal_code[]" value="<?php echo esc_attr($store['postal_code']); ?>" ></td>
                            <td><input type="number" name="lat[]" value="<?php echo esc_attr($store['lat']); ?>" step="any" required></td>
                            <td><input type="number" name="lng[]" value="<?php echo esc_attr($store['lng']); ?>" step="any" required></td>
                            <td><input type="tel" name="phone[]" value="<?php echo esc_attr($store['phone']); ?>"></td>
                            <td><input type="email" name="email[]" value="<?php echo esc_attr($store['email']); ?>"></td>
                            <td><button type="button" class="remove-row button">X</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="add-row">+ Add Store</button></p>
            <p><input type="submit" name="store_locator_submit" class="button button-primary" value="Save Stores"></p>
        </form>
    </div>

    <script>
    document.getElementById('add-row').addEventListener('click', function () {
        const row = `
        <tr>
            <td><textarea name="title[]" rows="2" required></textarea></td>
            <td><textarea name="street[]" rows="2" required></textarea></td>
            <td><input type="text" name="city[]" value="" required></td>
            <td><input type="number" name="postal_code[]" value=""></td>
            <td><input type="number" name="lat[]" value="" step="any" required></td>
            <td><input type="number" name="lng[]" value="" step="any" required></td>
            <td><input type="tel" name="phone[]" value=""></td>
            <td><input type="email" name="email[]" value=""></td>
            <td><input type="text" name="open_hours[]" value=""></td>
            <td><button type="button" class="remove-row button">X</button></td>
        </tr>`;
        document.getElementById('store-rows').insertAdjacentHTML('beforeend', row);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
    </script>
    <?php
}