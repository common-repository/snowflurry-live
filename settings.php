<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if the user has the necessary permission
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

// Processing form submission
if (isset($_POST['submit'])) {
    // Check nonce for security
    check_admin_referer('snowflurry-settings-save', 'snowflurry-settings-nonce');

    // Sanitize and save the options
    $country_codes = sanitize_text_field($_POST['country_codes']);
    $api_key = sanitize_text_field($_POST['api_key']);
    $snow_color = sanitize_hex_color($_POST['snow_color']);
    $always_show_snow = isset($_POST['always_show_snow']) ? '1' : '0';

    update_option('snowflurry_weather_country_codes', $country_codes);
    update_option('snowflurry_weather_api_key', $api_key);
    update_option('snowflurry_snow_color', $snow_color);
    update_option('snowflurry_always_show_snow', $always_show_snow);

    snowflurry_check_weather_conditions();
}

// Retrieve the saved values
$country_codes_value = esc_attr(get_option('snowflurry_weather_country_codes', ''));
$api_key_value = esc_attr(get_option('snowflurry_weather_api_key', ''));
$snow_color_value = esc_attr(get_option('snowflurry_snow_color', '#ffffff')); // Default to white if not set
$always_show_snow_value = get_option('snowflurry_always_show_snow', '0');

// HTML form
?>
<div class="wrap">
    <h1>Snow Settings</h1>
    <p>This plugin works by checking real weather using an API. Enter the areas (zip codes) for the plugin to check. If the current weather is snowing, then your website will display snow. Or, you can bypass this and have it always display snow.</p>
    <form method="post" action="">
        <?php wp_nonce_field('snowflurry-settings-save', 'snowflurry-settings-nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="always_show_snow">Always Show Snow:</label>
                    <p class="description">If checked, this will force snow to always show regardless of current weather conditions.</p>
                </th>
                <td>
                    <input type="checkbox" id="always_show_snow" name="always_show_snow" value="1" <?php checked($always_show_snow_value, '1'); ?> />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="country_codes">Zip Codes:</label>
                </th>
                <td>
                    <input type="text" id="country_codes" name="country_codes" value="<?php echo esc_attr($country_codes_value); ?>" class="regular-text" />
                    <p class="description">Enter zip code. If more than 1, separate by commas.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="snow_color">Snow Color:</label>
                </th>
                <td>
                    <input type="color" id="snow_color" name="snow_color" value="<?php echo esc_attr($snow_color_value); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="api_key">API Key:</label>
                </th>
                <td>
                    <input type="text" id="api_key" name="api_key" value="<?php echo esc_attr($api_key_value); ?>" class="regular-text" />
                    <p class="description">Get a free API key here: <a href="https://www.weatherapi.com/" target="_blank">https://www.weatherapi.com/</a></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
        </p>
    </form>
</div>
