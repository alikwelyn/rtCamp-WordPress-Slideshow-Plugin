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

        // Check if the "action" parameter is set to "edit"
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
        if ( 'edit' === $action ) {
            $this->edit_slider_page();
            return;
        }

        // Check if the "action" parameter is set to "delete"
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
        if ( 'delete' === $action ) {
            $this->delete_slider();
            return;
        }

        // Display a success message if the slider was deleted successfully
        if ( 'slider_deleted' === $message ) {
            printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'Slider deleted successfully.', 'rtcamp-wp-slideshow' ) );
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

    /**
	 * Render the HTML markup for the edit slider page.
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
	public function edit_slider_page() {

        global $wpdb;
	    $table_name = $wpdb->prefix . 'rtcamp_wp_slideshow';

        // Get the ID of the slider to edit from the URL query parameter
        $slider_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

		// Load the slider data
	    $slider = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $slider_id ) );

        echo '<div class="wrap">';
        echo '<h1>' . esc_html( get_admin_page_title() ).  '- Edit Slider: ' . $slider_id .'</h1>';

		if ( ! $slider ) {
			echo '<p>' . esc_html__( 'Invalid slider ID.', 'rtcamp-wp-slideshow' ) . '</p>';
			return;
		}

		// Check if the form has been submitted
        if ( isset( $_POST['submit'] ) && check_admin_referer( 'edit_slider_' . $slider_id ) ) {
            // Save the updated slider data
            $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
            $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
            $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
    
            $update_args = array(
                'id' => $slider_id,
                'slider_name' => $name,
                'slider_type' => $type,
                'status' => $status,
                'date_updated' => current_time( 'mysql' ),
            );
    
            $update_result = $wpdb->update( $table_name, $update_args, array( 'id' => $slider_id ) );

            if ( false === $update_result ) {
                printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html__( 'Slider updated successfully.', 'rtcamp-wp-slideshow' ) );
            } else {
                printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'Slider updated successfully.', 'rtcamp-wp-slideshow' ) );
            }

            // Reload the slider data to display the updated values
		    $slider = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $slider_id ) );
        }

        // Display the slider edit form
		echo '<form method="post">';
		wp_nonce_field( 'edit_slider_' . $slider_id );

		echo '<table class="form-table">';
		echo '<tr>';
		echo '<th>' . esc_html__( 'Slider Name', 'rtcamp-wp-slideshow' ) . '</th>';
		echo '<td><input type="text" name="name" value="' . esc_attr( $slider->slider_name ) . '" /></td>';
		echo '</tr>';

        echo '<tr>';
        echo '<th>' . esc_html__( 'Type', 'rtcamp-wp-slideshow' ) . '</th>';
        echo '<td><input type="text" name="type" value="' . esc_attr( $slider->slider_type ) . '" /></td>';
        echo '</tr>';

		echo '<tr>';
		echo '<th>' . esc_html__( 'Status', 'rtcamp-wp-slideshow' ) . '</th>';
		echo '<td>';
		echo '<select name="status">';
		echo '<option value="draft"' . selected( 'draft', $slider->status, false ) . '>' . esc_html__( 'Draft', 'rtcamp-wp-slideshow' ) . '</option>';
		echo '<option value="publish"' . selected( 'publish', $slider->status, false ) . '>' . esc_html__( 'Publish', 'rtcamp-wp-slideshow' ) . '</option>';
        echo '</select>';
		echo '</td>';
		echo '</tr>';

		echo '</table>';

		submit_button();

		echo '</form>';
		echo '</div>';

    }

    /**
	 * Delete slider
     *
     * @link       https://github.com/alikwelyn/rtCamp-WordPress-Slideshow-Plugin
     * @since      1.0.0
     *
     * @package    Rtcamp_Wp_Slideshow
     */
    public function delete_slider() {
        // Get the ID of the slider to delete from the URL query parameter
        $slider_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'rtcamp_wp_slideshow';
    
        // Check if the slider exists
        $slider = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $slider_id ) );
        if ( ! $slider ) {
            printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html__( 'Invalid slider ID.', 'rtcamp-wp-slideshow' ) );
            return;
        }
    
        // Check if the delete button has been clicked
        if ( isset( $_POST['submit'] ) && check_admin_referer( 'delete_slider_' . $slider_id ) ) {
            // Delete the slider
            $delete_result = $wpdb->delete( $table_name, array( 'id' => $slider_id ) );
    
            if ( false === $delete_result ) {
                printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html__( 'Slider delete failed.', 'rtcamp-wp-slideshow' ) );
            } else {
                // Use JavaScript to redirect instead of wp_redirect()
                echo '<script>window.location.href="' . admin_url( 'admin.php?page=rtcamp-wp-slideshow&message=slider_deleted' ) . '";</script>';
                exit;
            }
        }
    
        echo '<div class="wrap">';
        echo '<h1>' . esc_html( get_admin_page_title() ).  '- Delete Slider: ' . $slider_id .'</h1>';
    
        echo '<p>' . esc_html__( 'Are you sure you want to delete this slider?', 'rtcamp-wp-slideshow' ) . '</p>';
    
        // Display the delete slider form
        echo '<form method="post">';
        wp_nonce_field( 'delete_slider_' . $slider_id );
        submit_button( esc_html__( 'Delete', 'rtcamp-wp-slideshow' ), 'delete' );
        echo '</form>';
        echo '</div>';
    }

}