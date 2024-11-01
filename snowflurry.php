<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Plugin Name: SnowFlurry Live - Sync with Current Snowfall
 * Description: Add an animated snow effect to your website based on live weather conditions anywhere in the world. More configuration options coming soon!
 * Version: 0.2
 * Author: The 215 Guys
 * Author URI: https://www.the215guys.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Schedule event on plugin activation
register_activation_hook(__FILE__, 'snow_snowflurry_schedule_cron');
function snow_snowflurry_schedule_cron() {
    if (!wp_next_scheduled('snowflurry_snow_check_weather_event')) {
        wp_schedule_event(time(), 'hourly', 'snowflurry_snow_check_weather_event');
    }
}

// Clear scheduled event on plugin deactivation
register_deactivation_hook(__FILE__, 'snow_snowflurry_clear_cron');
function snow_snowflurry_clear_cron() {
    wp_clear_scheduled_hook('snowflurry_snow_check_weather_event');
}

// Hook your function to the custom event
add_action('snowflurry_snow_check_weather_event', 'snowflurry_check_weather_conditions');



add_action( 'admin_menu', 'snowflurry_menu_function' );
 
function snowflurry_menu_function(){
      add_menu_page(
            'Custom Snow Flurry',
            'SnowFlurry',
            'manage_options',
            'menu','snowflurry_menu'
      );
	  	   
}

// Callback Function
function snowflurry_menu(){
      include( plugin_dir_path( __FILE__ ) . '/settings.php');
}


include( plugin_dir_path( __FILE__ ) . 'functions.php');