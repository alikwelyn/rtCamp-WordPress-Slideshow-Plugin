<?php

/**
 * @wordpress-plugin
 * Plugin Name:       WP Slideshow Plugin
 * Plugin URI:        https://rtcamp.com
 * Description:       A WordPress slideshow plugin tool that allows you to create and display image slideshows on your WordPress website.
 * Version:           1.0.0
 * Author:            Alik Welyn
 * Author URI:        https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rtcamp-wp-slideshow
 *
 * @link              https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
 * @since             1.0.0
 * @package           Rtcamp_Wp_Slideshow
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-rtcamp-wp-slideshow.php';

// Initialize the plugin.
function my_slideshow_plugin() {
    $plugin = new Rtcamp_Wp_Slideshow();
    $plugin->init();
}
my_slideshow_plugin();