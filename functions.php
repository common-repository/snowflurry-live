<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function snowflurry_enqueue_snowstorm_script() {
    $should_enqueue = get_transient('snowflurry_enqueue_snowstorm_script');
    if ($should_enqueue) {
        wp_enqueue_script('snowstorm', plugin_dir_url(__FILE__) . 'snowstorm.js', array(), '1.0', true);
        
        // Add action to wp_footer to output the div
        add_action('wp_footer', 'snowflurry_snow_settings', 99);
    }
}
add_action('wp_enqueue_scripts', 'snowflurry_enqueue_snowstorm_script');

// Function to output the snow div
function snowflurry_snow_settings() {
    // Retrieve the saved snow color value, default to '#fff' if not set
    $snow_color = get_option('snowflurry_snow_color', '#fff');

    // Echo the script tag with the snow color
    echo '<script>snowStorm.snowColor = \'' . esc_attr($snow_color) . '\';</script>';
}



function snowflurry_check_weather_conditions() {
    //check for zip or bypass that
      $always_show = esc_attr(get_option('snowflurry_always_show_snow', ''));

      if ($always_show == 1) {
            $should_enqueue = true;
            set_transient('snowflurry_enqueue_snowstorm_script', $should_enqueue, HOUR_IN_SECONDS);
            return;
      }
    

    $country_codes = explode(',', trim(get_option('snowflurry_weather_country_codes', '')));
    $api_key = esc_attr(get_option('snowflurry_weather_api_key', ''));
    $api_endpoint = 'http://api.weatherapi.com/v1/current.json';
    $should_enqueue = false;

    foreach ($country_codes as $country_code) {
        $request_url = "$api_endpoint?key=$api_key&q=$country_code&aqi=yes";
        $response = wp_remote_get($request_url);

        if (is_wp_error($response)) {
            // Handle error (log or notify admin)
            continue;
        }

        $weather_data = json_decode(wp_remote_retrieve_body($response), true);
        $weather_condition = $weather_data['current']['condition']['text'];

        if (stripos($weather_condition, "snow") !== false || stripos($weather_condition, "blizzard") !== false) {
            $should_enqueue = true;
            break;
        }
    }

    set_transient('snowflurry_enqueue_snowstorm_script', $should_enqueue, HOUR_IN_SECONDS);
}