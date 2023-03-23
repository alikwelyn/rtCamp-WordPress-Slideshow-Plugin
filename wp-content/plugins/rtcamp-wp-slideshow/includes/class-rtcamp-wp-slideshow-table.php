<?php
/**
 * Class file for Rtcamp_Wp_Slideshow_Table.
 *
 * @package    Rtcamp_Wp_Slideshow
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class for creating and managing the slideshow table.
 *
 * @package    Rtcamp_Wp_Slideshow
 */
class Rtcamp_Wp_Slideshow_Table extends WP_List_Table {

	/**
	 * Prepare the table data.
	 */
	public function prepare_items() {
		global $wpdb;

		$table_name            = $wpdb->prefix . 'rtcamp_wp_slideshow';
		$per_page              = 5;
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$current_page          = $this->get_pagenum();

		$total_items = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `$table_name`" ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$this->items = $wpdb->get_results( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->prepare(
				"SELECT * FROM {$table_name} ORDER BY %s %s LIMIT %d OFFSET %d",
				array( 'id', 'DESC', $per_page, ( $current_page - 1 ) * $per_page )
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
			'slider_name'  => esc_html__( 'Slider Name', 'rtcamp-wp-slideshow' ),
			'date_created' => esc_html__( 'Date Created', 'rtcamp-wp-slideshow' ),
			'date_updated' => esc_html__( 'Date Updated', 'rtcamp-wp-slideshow' ),
			'status'       => esc_html__( 'Status', 'rtcamp-wp-slideshow' ),
			'shortcode'    => esc_html__( 'Shortcode', 'rtcamp-wp-slideshow' ),
		);
		return $columns;
	}

	/**
	 * Get the sortable columns for the table.
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'slider_name'  => array( 'slider_name', false ),
			'date_created' => array( 'date_created', false ),
			'date_updated' => array( 'date_updated', false ),
			'status'       => array( 'status', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Get the column output for the ID column.
	 *
	 * @param array $item The current item being displayed.
	 * @return string The column output.
	 */
	public function column_id( $item ) {
		return $item['id'];
	}

	/**
	 * Get the column output for the slider name column.
	 *
	 * @param array $item The current item being displayed.
	 */
	public function column_slider_name( $item ) {
		$edit_link   = add_query_arg(
			array(
				'page'   => 'rtcamp-wp-slideshow',
				'action' => 'edit',
				'id'     => $item['id'],
			),
			admin_url( 'admin.php' )
		);
		$delete_link = add_query_arg(
			array(
				'page'   => 'rtcamp-wp-slideshow',
				'action' => 'delete',
				'id'     => $item['id'],
			),
			admin_url( 'admin.php' )
		);
		$actions     = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html__( 'Edit', 'rtcamp-wp-slideshow' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', esc_url( $delete_link ), esc_html__( 'Delete', 'rtcamp-wp-slideshow' ) ),
		);
		return sprintf(
			'%1$s %2$s',
			esc_html( $item['slider_name'] ),
			$this->row_actions( $actions )
		);
	}

	/**
	 * Get the column output for the date created column.
	 *
	 * @param array $item The current item being displayed.
	 */
	public function column_date_created( $item ) {
		return esc_html( $item['date_created'] );
	}

	/**
	 * Get the column output for the date updated column.
	 *
	 * @param array $item The current item being displayed.
	 */
	public function column_date_updated( $item ) {
		return esc_html( $item['date_updated'] );
	}

	/**
	 * Get the column output for the status column.
	 *
	 * @param array $item The current item being displayed.
	 */
	public function column_status( $item ) {
		return esc_html( $item['status'] );
	}

	/**
	 * Get the column output for the shortcode column.
	 *
	 * @param array $item The current item being displayed.
	 */
	public function column_shortcode( $item ) {
		$slider_id = $item['id'];
		$shortcode = '[rtcamp_wp_slideshow id="' . $slider_id . '"]';
		return sprintf(
			'<input type="text" value="%s" readonly="readonly" class="rtcamp-wp-slideshow-shortcode" onclick="this.select();" />',
			esc_attr( $shortcode )
		);
	}

}
