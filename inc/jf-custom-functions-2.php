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
                    'title' => sanitize_text_field(stripslashes($title)),
                    'street' => sanitize_text_field(stripslashes($_POST['street'][$index])),
                    'city' => sanitize_text_field(stripslashes($_POST['city'][$index])),
                    'postal_code' => sanitize_text_field(stripslashes($_POST['postal_code'][$index])),
                    'lat' => floatval(sanitize_text_field(stripslashes($_POST['lat'][$index]))),
                    'lng' => floatval(sanitize_text_field(stripslashes($_POST['lng'][$index]))),
                    'phone' => sanitize_text_field(stripslashes($_POST['phone'][$index])),
                    'email' => sanitize_email(stripslashes($_POST['email'][$index])),
                    'open_hours' => sanitize_text_field(stripslashes($_POST['open_hours'][$index])),
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
                            <td>
                                <details>
                                    <summary>Opening Hours</summary>
                                    <div>
                                        <?php
                                        $current_hours_json = $store['open_hours'] ?? '{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"0","sun":"0"}';
                                        $current_hours = json_decode($current_hours_json, true);
                                        if (!is_array($current_hours)) { // Fallback if JSON is invalid or not an array
                                            $current_hours = ['mon'=>'0', 'tue'=>'0', 'wed'=>'0', 'thu'=>'0', 'fri'=>'0', 'sat'=>'0', 'sun'=>'0'];
                                        }
                                        $days_map = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
                                        
                                        foreach ($days_map as $day_key => $day_label):
                                            $day_value_from_json = $current_hours[$day_key] ?? '0';
                                            $input_field_value = ($day_value_from_json === '0') ? '' : $day_value_from_json;
                                        ?>
                                        <div class="day-editor-row" data-day-key="<?php echo esc_attr($day_key); ?>">
                                            <input type="text" class="day-time-input" value="<?php echo esc_attr($input_field_value); ?>">
                                        </div>
                                        <?php endforeach; ?>
                                        <input type="hidden" name="open_hours[]" class="hours-data-json" value="<?php echo esc_attr($current_hours_json); ?>">
                                    </div>
                                </details>
                            </td>
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
    const days_map_js_global = { mon: 'Monday', tue: 'Tuesday', wed: 'Wednesday', thu: 'Thursday', fri: 'Friday', sat: 'Saturday', sun: 'Sunday' };
    
    document.getElementById('add-row').addEventListener('click', function () {
        const tableBody = document.getElementById('store-rows');
        const new_row_index = tableBody.getElementsByTagName('tr').length; // Unique index for new row names

        let day_editors_html = '';
        for (const day_key in days_map_js_global) {
            day_editors_html += `
            <div class="day-editor-row" data-day-key="${day_key}">
                <input type="text" class="day-time-input">
            </div>
            `;
        }

        const row_html_content = `
        <tr>
            <td><textarea name="title[]" rows="2" required></textarea></td>
            <td><textarea name="street[]" rows="2" required></textarea></td>
            <td><input type="text" name="city[]" value="" required></td>
            <td><input type="number" name="postal_code[]" value=""></td>
            <td><input type="number" name="lat[]" value="" step="any" required></td>
            <td><input type="number" name="lng[]" value="" step="any" required></td>
            <td><input type="tel" name="phone[]" value=""></td>
            <td><input type="email" name="email[]" value=""></td>
            <td>
                <details>
                    <summary>Opening Hours</summary>
                    <div>
                        ${day_editors_html}
                        <input type="hidden" name="open_hours[]" class="hours-data-json" value='{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"0","sun":"0"}'>
                    </div>
                </details>
            </td>
            <td><button type="button" class="remove-row button">X</button></td>
        </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row_html_content);
    });

    // Event delegation for remove button and opening hours controls
    document.getElementById('store-rows').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('tr').remove();
        }
    });
    
    document.getElementById('store-rows').addEventListener('change', function(event) {
        const target = event.target;
        const isTimeInput = target.matches('.day-time-input');

        if (isTimeInput) {
            const detailsElement = target.closest('details'); // Find the parent <details> element
            if (detailsElement) {
                updateJsonForStore(detailsElement);
            }
        }
    });

    function updateJsonForStore(detailsElement) {
        const hiddenInput = detailsElement.querySelector('.hours-data-json');
        const dayEditorRows = detailsElement.querySelectorAll('.day-editor-row');
        let hoursData = {};

        dayEditorRows.forEach((row) => {
            const day_key = row.dataset.dayKey;
            if (!day_key) return;

            const timeInput = row.querySelector('.day-time-input');
            if (timeInput && timeInput.value.trim() !== '') {
                hoursData[day_key] = timeInput.value.trim();
            } else {
                hoursData[day_key] = "0"; // Empty input means closed
            }
        });
        hiddenInput.value = JSON.stringify(hoursData);
    }
</script>
    <?php
}