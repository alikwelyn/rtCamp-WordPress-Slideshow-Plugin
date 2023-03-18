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
            wp_enqueue_style( 'rtcamp-wp-slideshow-admin', plugins_url( '../admin/css/rtcamp-wp-slideshow-admin.css', __FILE__ ), array(), '1.0.0' );
            wp_enqueue_script( 'rtcamp-wp-slideshow-admin', plugins_url( '../admin/js/rtcamp-wp-slideshow-admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
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
        $table = new Rtcamp_Wp_Slideshow_Table();
        $table->prepare_items();
            
        // Check if the "action" parameter is set to "add"
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
        if ( 'add' === $action ) {
            $this->add_slider_page();
            return;
        }
    
        // Check if a message has been set
        $message = isset( $_GET['message'] ) ? sanitize_text_field( $_GET['message'] ) : '';
        
        // Display a success message if the slider was added successfully
        if ( 'slider_added' === $message ) {
            printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'Slider added successfully.', 'rtcamp-wp-slideshow' ) );
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php $table->display(); ?>
            <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'rtcamp-wp-slideshow', 'action' => 'add' ), admin_url( 'admin.php' ) ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Slider', 'rtcamp-wp-slideshow' ); ?></a>
        </div>
        <?php
    }

    /**
     * Display the form to add a new slider.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function add_slider_page() {
        if ( isset( $_POST['submit'] ) ) {
            $this->save_slider();
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?> - Add New Slider</h1>
            <form method="post">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Slider Name', 'rtcamp-wp-slideshow' ); ?></th>
                            <td><input type="text" name="slider_name" value=""></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Slider Type', 'rtcamp-wp-slideshow' ); ?></th>
                            <td><input type="text" name="slider_type" value=""></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Status', 'rtcamp-wp-slideshow' ); ?></th>
                            <td><select name="status">
                                <option value="draft"><?php esc_html_e( 'Draft', 'rtcamp-wp-slideshow' ); ?></option>
                                <option value="published"><?php esc_html_e( 'Published', 'rtcamp-wp-slideshow' ); ?></option>
                            </select></td>
                        </tr>
                    </tbody>
                </table>
                <?php wp_nonce_field( 'add_slider', 'add_slider_nonce' ); ?>
                <input type="submit" name="submit" value="<?php esc_attr_e( 'Save Slider', 'rtcamp-wp-slideshow' ); ?>" class="button button-primary">
            </form>
        </div>
        <?php
    }

    /**
     * Save the slider form data to the database.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function save_slider() {
        global $wpdb;
    
        // Check if the user has permission to save the slider
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
    
        // Verify the nonce
        if ( ! isset( $_POST['add_slider_nonce'] ) || ! wp_verify_nonce( $_POST['add_slider_nonce'], 'add_slider' ) ) {
            return;
        }
    
        // Get the slider data from the form
        $slider_name = isset( $_POST['slider_name'] ) ? sanitize_text_field( $_POST['slider_name'] ) : '';
        $slider_type = isset( $_POST['slider_type'] ) ? sanitize_text_field( $_POST['slider_type'] ) : '';
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
    
        // Save the slider data to the database
        $table_name = $wpdb->prefix . 'rtcamp_wp_slideshow';
        $wpdb->insert(
            $table_name,
            array(
                'slider_name' => $slider_name,
                'slider_type' => $slider_type,
                'date_created' => current_time( 'mysql' ),
                'date_updated' => current_time( 'mysql' ),
                'status' => $status,
            ),
            array( '%s', '%s', '%s', '%s', '%s' )
        );
    
        // Use JavaScript to redirect instead of wp_redirect()
        echo '<script>window.location.href="' . admin_url( 'admin.php?page=rtcamp-wp-slideshow&message=slider_added' ) . '";</script>';
        exit;
    }

}