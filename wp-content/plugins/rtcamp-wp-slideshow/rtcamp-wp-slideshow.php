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
register_activation_hook ( __FILE__, 'create_table' );

function create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rtcamp_wp_slideshow';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      slider_name varchar(255) NOT NULL,
      slider_type varchar(255) NOT NULL,
      date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      date_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      status varchar(20) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Initialize the plugin.
function my_slideshow_plugin() {
    $plugin = new Rtcamp_Wp_Slideshow();
    $plugin->init();
}
my_slideshow_plugin();