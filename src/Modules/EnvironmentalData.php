<?php
/**
 * Environmental Data
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Environmental Data class
 */
class EnvironmentalData {

	/**
	 * Init
	 */
	public static function init() {
		add_filter( 'wp_admin_tools_sections', array( __CLASS__, 'render_section' ) );
	}

	/**
	 * Render the environmental data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function render_section( $sections ) {
		$new_section  = '';
		$new_section .= '<h3>' . esc_html__( 'Environmental Data', 'wp-admin-tools' ) . '</h3>';

		$environmental_data = self::get_environmental_data();

		foreach ( $environmental_data as $key => $value ) {
			$new_section .= '<p><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( $value ) . '</p>';
		}

		$sections[] = $new_section;
		return $sections;
	}

	/**
	 * Get environmental data
	 * Creates and returns an array of environmental data such as:
	 * - PHP version
	 * - WordPress version
	 * - Server software
	 * - MySQL version
	 * - PHP memory limit
	 * - PHP max execution time
	 * - PHP max input time
	 * - PHP post max size
	 * - PHP upload max size
	 * - PHP max file uploads
	 * - PHP max input vars
	 * - PHP display errors
	 * - PHP error reporting
	 *
	 * @return array
	 */
	public static function get_environmental_data() {
		$environmental_data = array(
			'PHP Version'            => phpversion(),
			'WordPress Version'      => get_bloginfo( 'version' ),
			'Server Software'        => $_SERVER['SERVER_SOFTWARE'], // phpcs:ignore
			'MySQL Version'          => $GLOBALS['wpdb']->db_version(),
			'PHP Memory Limit'       => ini_get( 'memory_limit' ),
			'PHP Max Execution Time' => ini_get( 'max_execution_time' ),
			'PHP Max Input Time'     => ini_get( 'max_input_time' ),
			'PHP Post Max Size'      => ini_get( 'post_max_size' ),
			'PHP Upload Max Size'    => ini_get( 'upload_max_filesize' ),
			'PHP Max File Uploads'   => ini_get( 'max_file_uploads' ),
			'PHP Max Input Vars'     => ini_get( 'max_input_vars' ),
			'PHP Display Errors'     => ini_get( 'display_errors' ),
		);

		return $environmental_data;
	}
}
