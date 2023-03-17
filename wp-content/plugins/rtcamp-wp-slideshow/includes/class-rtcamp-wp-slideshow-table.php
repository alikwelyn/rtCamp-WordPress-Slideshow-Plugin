<?php

class My_Slider_Plugin_Table extends WP_List_Table {

    /**
     * Prepare the table data.
     */
    public function prepare_items() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'my_slider_plugin_sliders';
        $per_page = 10;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $this->items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY %s %s LIMIT %d OFFSET %d",
                'id',
                'DESC',
                $per_page,
                ( $current_page - 1 ) * $per_page
            ),
            ARRAY_A
        );

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
            )
        );
    }

    /**
     * Get the columns for the table.
     */
    public function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'id'            => esc_html__( 'ID', 'rtcamp-wp-slideshow' ),
            'slider_name'   => esc_html__( 'Slider Name', 'rtcamp-wp-slideshow' ),
            'slider_type'   => esc_html__( 'Slider Type', 'rtcamp-wp-slideshow' ),
            'date_created'  => esc_html__( 'Date Created', 'rtcamp-wp-slideshow' ),
            'date_updated'  => esc_html__( 'Date Updated', 'rtcamp-wp-slideshow' ),
            'status'        => esc_html__( 'Status', 'rtcamp-wp-slideshow' ),
        );
        return $columns;
    }

    /**
     * Get the sortable columns for the table.
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array( 'id', true ),
            'slider_name' => array( 'slider_name', false ),
            'slider_type' => array( 'slider_type', false ),
            'date_created' => array( 'date_created', false ),
            'date_updated' => array( 'date_updated', false ),
            'status' => array( 'status', false ),
        );
        return $sortable_columns;
    }

    /**
     * Get the checkbox column for the table.
     */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="slider[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * Get the column output for the ID column.
     */
    public function column_id( $item ) {
        return $item['id'];
    }

    /**
     * Get the column output for the slider name column.
     */
    public function column_slider_name( $item ) {
        $edit_link = add_query_arg(
            array(
                'page' => 'rtcamp-wp-slideshow',
                'action' => 'edit',
                'id' => $item['id'],
            ),
            admin_url( 'admin.php' )
        );
        $delete_link = add_query_arg(
            array(
                'page' => 'rtcamp-wp-slideshow',
                'action' => 'delete',
                'id' => $item['id'],
            ),
            admin_url( 'admin.php' )
        );
        $actions = array(
            'edit' => sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html__( 'Edit', 'rtcamp-wp-slideshow' ) ),
            'delete' => sprintf( '<a href="%s">%s</a>', esc_url( $delete_link ), esc_html__( 'Delete', 'rtcamp-wp-slideshow' ) ),
        );
        return sprintf(
            '%1$s %2$s',
            esc_html( $item['slider_name'] ),
            $this->row_actions( $actions )
        );
    }

    
    /**
     * Get the column output for the slider type column.
     */
    public function column_slider_type( $item ) {
        return esc_html( $item['slider_type'] );
    }

    /**
     * Get the column output for the date created column.
     */
    public function column_date_created( $item ) {
        return esc_html( $item['date_created'] );
    }

    /**
     * Get the column output for the date updated column.
     */
    public function column_date_updated( $item ) {
        return esc_html( $item['date_updated'] );
    }

    /**
     * Get the column output for the status column.
     */
    public function column_status( $item ) {
        return esc_html( $item['status'] );
    }
}