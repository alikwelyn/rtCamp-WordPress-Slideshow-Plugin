<?php

class Rtcamp_Wp_Slideshow {

    /**
     * Initialize the plugin.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Add the plugin admin menu.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function add_admin_menu() {
        add_menu_page(
            'WP Slideshow',
            'WP Slideshow',
            'manage_options',
            'rtcamp-wp-slideshow',
            array( $this, 'admin_page' )
        );
    }

    /**
     * Enqueue scripts and styles for the admin page.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function enqueue_scripts( $hook ) {
        if ( 'toplevel_page_rtcamp-wp-slideshow' === $hook ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_style( 'rtcamp-wp-slideshow-admin', plugins_url( 'admin/css/rtcamp-wp-slideshow-admin.css', __FILE__ ), array(), '1.0.0' );
            wp_enqueue_script( 'rtcamp-wp-slideshow-admin', plugins_url( 'admin/js/rtcamp-wp-slideshow-admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
        }
    }

    /**
     * Render the plugin admin page.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function admin_page() {
        require_once plugin_dir_path( __FILE__ ) . 'class-rtcamp-wp-slideshow-table.php';
        $table = new My_Slider_Plugin_Table();
        $table->prepare_items();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php $table->display(); ?>
            <a href="#" class="button button-primary"><?php esc_html_e( 'Add New Slider', 'rtcamp-wp-slideshow' ); ?></a>
        </div>
        <?php
    }

}