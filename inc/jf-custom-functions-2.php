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
        <form method="post" class="store-locator-form">
            <?php wp_nonce_field('store_locator_save'); ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th colspan="9">Store Details</th>
                        <th colspan="1">Action</th>
                    </tr>
                </thead>
                <tbody id="store-rows">
                    <?php foreach ($data as $i => $store): ?>
                        <tr>
                            <td colspan="9">
                                <div class="store-details-wrapper">
                                    <div class="form-fields-main-row">
                                        <div class="field-group field-title">
                                            <label for="title_<?php echo $i; ?>">Title</label>
                                            <textarea id="title_<?php echo $i; ?>" name="title[]" rows="2" required><?php echo esc_textarea($store['title']); ?></textarea>
                                        </div>
                                        <div class="field-group field-street">
                                            <label for="street_<?php echo $i; ?>">Street</label>
                                            <textarea id="street_<?php echo $i; ?>" name="street[]" rows="2" required><?php echo esc_textarea($store['street']); ?></textarea>
                                        </div>
                                        <div class="field-group field-city">
                                            <label for="city_<?php echo $i; ?>">City</label>
                                            <input id="city_<?php echo $i; ?>" type="text" name="city[]" value="<?php echo esc_attr($store['city']); ?>" required>
                                        </div>
                                        <div class="field-group field-postcode">
                                            <label for="postal_code_<?php echo $i; ?>">Post Code</label>
                                            <input id="postal_code_<?php echo $i; ?>" type="number" name="postal_code[]" value="<?php echo esc_attr($store['postal_code']); ?>">
                                        </div>
                                        <div class="field-group field-lat">
                                            <label for="lat_<?php echo $i; ?>">Lat</label>
                                            <input id="lat_<?php echo $i; ?>" type="number" name="lat[]" value="<?php echo esc_attr($store['lat']); ?>" step="any" required>
                                        </div>
                                        <div class="field-group field-lon">
                                            <label for="lng_<?php echo $i; ?>">Lon</label>
                                            <input id="lng_<?php echo $i; ?>" type="number" name="lng[]" value="<?php echo esc_attr($store['lng']); ?>" step="any" required>
                                        </div>
                                        <div class="field-group field-phone">
                                            <label for="phone_<?php echo $i; ?>">Tel</label>
                                            <input id="phone_<?php echo $i; ?>" type="tel" name="phone[]" value="<?php echo esc_attr($store['phone']); ?>">
                                        </div>
                                        <div class="field-group field-email">
                                            <label for="email_<?php echo $i; ?>">Email</label>
                                            <input id="email_<?php echo $i; ?>" type="email" name="email[]" value="<?php echo esc_attr($store['email']); ?>">
                                        </div>
                                    </div>
                                    <div class="form-fields-secondary-row">
                                        <details>
                                            <summary>Opening Hours</summary>
                                            <div>
                                                <?php
                                                $current_hours_json = $store['open_hours'] ?? '{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"0","sun":"0"}';
                                                $current_hours = json_decode($current_hours_json, true);
                                                if (!is_array($current_hours)) { $current_hours = ['mon'=>'0', 'tue'=>'0', 'wed'=>'0', 'thu'=>'0', 'fri'=>'0', 'sat'=>'0', 'sun'=>'0']; }
                                                $days_map = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
                                                foreach ($days_map as $day_key => $day_label):
                                                    $day_value_from_json = $current_hours[$day_key] ?? '0';
                                                    $input_field_value = ($day_value_from_json === '0') ? '' : $day_value_from_json;
                                                ?>
                                                <div class="day-editor-row" data-day-key="<?php echo esc_attr($day_key); ?>" style="display: flex; align-items: center; margin-bottom: 5px;">
                                                    <label for="day-time-input-<?php echo esc_attr($day_key); ?>_<?php echo $i; ?>" style="margin-right: 5px; min-width: 80px;"><?php echo esc_html($day_label); ?>:</label>
                                                    <input type="text" id="day-time-input-<?php echo esc_attr($day_key); ?>_<?php echo $i; ?>" class="day-time-input" value="<?php echo esc_attr($input_field_value); ?>" pattern="[0-9:\-\s,]*" placeholder="e.g., 09:00-12:00, 14:00-17:00 / empty for closed" style="flex-grow: 1;">
                                                </div>
                                                <?php endforeach; ?>
                                                <input type="hidden" name="open_hours[]" class="hours-data-json" value="<?php echo esc_attr($current_hours_json); ?>">
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            </td>
                            <td><button type="button" class="remove-row button">X</button></td>
                        </tr>
                        <tr class="store-item-separator-row">
                            <td colspan="10" class="store-item-separator"><hr></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="add-row">+ Add Store</button></p>
            <p><input type="submit" name="store_locator_submit" class="button button-primary" value="Save Stores"></p>
        </form>
    </div>

<style type="text/css">
.store-locator-form .widefat th {
    font-weight: bold;
    padding: 10px;
}
.store-locator-form .widefat th[colspan="9"] {
    text-align: left;
}
.store-locator-form .widefat td {
    padding: 8px;
}
.store-locator-form .widefat td[colspan="9"] {
    padding: 15px;
    border-bottom: none;
}
.store-locator-form .widefat td[colspan="1"] { /* For the action button cell */
    vertical-align: middle;
    text-align: center;
    border-bottom: none;
}

.store-details-wrapper {
    display: flex;
    flex-direction: column;
    gap: 20px; /* Increased gap between main fields row and secondary (hours) row */
}

.form-fields-main-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px 20px; /* Row gap, Column gap */
}

.form-fields-main-row .field-group {
    display: flex;
    flex-direction: column;
    gap: 5px; /* Gap between label and input */
    flex-grow: 1;
    min-width: 180px; /* Minimum width before wrapping */
}

.form-fields-main-row .field-group.field-title,
.form-fields-main-row .field-group.field-street {
    flex-basis: calc(50% - 10px); /* Two full-width fields per row, accounting for gap */
    min-width: calc(50% - 10px);
}

.form-fields-main-row .field-group.field-city,
.form-fields-main-row .field-group.field-postcode,
.form-fields-main-row .field-group.field-lat,
.form-fields-main-row .field-group.field-lon,
.form-fields-main-row .field-group.field-phone,
.form-fields-main-row .field-group.field-email {
    flex-basis: calc(25% - 15px); /* Aim for four smaller fields per row */
    min-width: 150px;
}

.form-fields-main-row .field-group label {
    font-weight: bold;
    font-size: 13px;
    color: #2c3338;
}

.form-fields-main-row .field-group input[type="text"],
.form-fields-main-row .field-group input[type="number"],
.form-fields-main-row .field-group input[type="tel"],
.form-fields-main-row .field-group input[type="email"],
.form-fields-main-row .field-group textarea {
    width: 100% !important; /* Inputs fill their .field-group container */
    padding: 6px 8px;
    box-sizing: border-box;
    border: 1px solid #8c8f94;
    border-radius: 3px;
}
.form-fields-main-row .field-group textarea {
    min-height: 60px;
}

.form-fields-secondary-row details {
    border: 1px solid #ccd0d4;
    border-radius: 3px;
    background-color: #fff;
}
.form-fields-secondary-row details summary {
    padding: 10px 12px;
    background-color: #f7f7f7;
    border-bottom: 1px solid #ccd0d4;
    cursor: pointer;
    font-weight: bold;
    list-style: revert; /* Or 'disclosure-closed' / 'disclosure-open' for custom markers */
}
.form-fields-secondary-row details[open] summary {
    border-bottom: 1px solid #ccd0d4;
}
.form-fields-secondary-row details summary:hover {
    background-color: #f0f0f0;
}
.form-fields-secondary-row details > div {
    padding: 12px;
    background-color: #fff;
}
.store-locator-form .day-editor-row {
    margin-bottom: 8px !important;
}
.store-locator-form .day-editor-row:last-child {
    margin-bottom: 0 !important;
}
.store-locator-form .day-editor-row label {
    min-width: 90px !important;
    font-size: 13px;
    color: #2c3338;
    padding-top: 5px;
}

.store-item-separator-row td.store-item-separator {
    padding: 0 !important;
    border: none !important;
    height: 1px;
}
.store-item-separator-row hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0; /* Space around the HR */
}

.store-locator-form .remove-row.button {
    color: #b32d2e;
    border-color: #b32d2e;
    background: #fef7f7;
    padding: 0 8px !important;
    line-height: 26px !important;
    min-height: 28px !important;
    font-size: 18px;
    font-weight: bold;
}
.store-locator-form .remove-row.button:hover,
.store-locator-form .remove-row.button:focus {
    background: #f8e3e3;
    border-color: #a02727;
    color: #a02727;
    box-shadow: none;
}
.store-locator-form #add-row {
    margin-right: 10px;
}
.store-locator-form .button-primary {
    font-weight: bold;
}
</style>

<script>
    const days_map_js_global = { mon: 'Monday', tue: 'Tuesday', wed: 'Wednesday', thu: 'Thursday', fri: 'Friday', sat: 'Saturday', sun: 'Sunday' };
    
    document.getElementById('add-row').addEventListener('click', function () {
        const tableBody = document.getElementById('store-rows');
        const new_row_index = tableBody.getElementsByTagName('tr').length; // Unique index for new row names

        let day_editors_html = '';
        for (const day_key in days_map_js_global) {
            day_editors_html += `
            <div class="day-editor-row" data-day-key="${day_key}" style="display: flex; align-items: center; margin-bottom: 5px;">
                <label for="day-time-input-${day_key}_${new_row_index}" style="margin-right: 5px; min-width: 80px;">${days_map_js_global[day_key]}:</label>
                <input type="text" id="day-time-input-${day_key}_${new_row_index}" class="day-time-input" value="" pattern="[0-9:\-\s,]*" placeholder="e.g., 09:00-12:00, 14:00-17:00 / empty for closed" style="flex-grow: 1;">
            </div>
            `;
        }

        const row_html_content = `
<tr>
    <td colspan="9">
        <div class="store-details-wrapper">
            <div class="form-fields-main-row">
                <div class="field-group field-title">
                    <label for="title_${new_row_index}">Title</label>
                    <textarea id="title_${new_row_index}" name="title[]" rows="2" required></textarea>
                </div>
                <div class="field-group field-street">
                    <label for="street_${new_row_index}">Street</label>
                    <textarea id="street_${new_row_index}" name="street[]" rows="2" required></textarea>
                </div>
                <div class="field-group field-city">
                    <label for="city_${new_row_index}">City</label>
                    <input id="city_${new_row_index}" type="text" name="city[]" value="" required>
                </div>
                <div class="field-group field-postcode">
                    <label for="postal_code_${new_row_index}">Post Code</label>
                    <input id="postal_code_${new_row_index}" type="number" name="postal_code[]" value="">
                </div>
                <div class="field-group field-lat">
                    <label for="lat_${new_row_index}">Lat</label>
                    <input id="lat_${new_row_index}" type="number" name="lat[]" value="" step="any" required>
                </div>
                <div class="field-group field-lon">
                    <label for="lng_${new_row_index}">Lon</label>
                    <input id="lng_${new_row_index}" type="number" name="lng[]" value="" step="any" required>
                </div>
                <div class="field-group field-phone">
                    <label for="phone_${new_row_index}">Tel</label>
                    <input id="phone_${new_row_index}" type="tel" name="phone[]" value="">
                </div>
                <div class="field-group field-email">
                    <label for="email_${new_row_index}">Email</label>
                    <input id="email_${new_row_index}" type="email" name="email[]" value="">
                </div>
            </div>
            <div class="form-fields-secondary-row">
                <details>
                    <summary>Opening Hours</summary>
                    <div>
                        ${day_editors_html}
                        <input type="hidden" name="open_hours[]" class="hours-data-json" value='{\"mon\":\"0\",\"tue\":\"0\",\"wed\":\"0\",\"thu\":\"0\",\"fri\":\"0\",\"sat\":\"0\",\"sun\":\"0\"}'>
                    </div>
                </details>
            </div>
        </div>
    </td>
    <td><button type="button" class="remove-row button">X</button></td>
</tr>
<tr class="store-item-separator-row">
    <td colspan="10" class="store-item-separator"><hr></td>
</tr>
`;
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