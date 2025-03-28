<?php
/**
 * Environmental Module
 *
 * Displays environmental data in the WordPress admin dashboard widget.
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Environmental Module Class
 */
class EnvironmentalModule extends AbstractModule {


	/**
	 * Generate the environmental data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$env_data = self::get_environmental_data(); // Get the environmental data.

		$content = '<ul class="admin-tools-dashboard-widget_list">'; // Initialize content variable.
		foreach ( $env_data as $key => $value ) {
			// Build the content string with environmental data.
			$content .= '<li><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( $value ) . '</li>';
		}
		$content .= '</ul>'; // Close the unordered list.

		$section = new Section(
			title: 'Environmental Data',
			content: $content, // Content will be generated in the render_section method.
			css_id: 'environmental-data-section' // CSS ID for the section.
		);

		$sections[] = $section;
		return $sections;
	}

	/**
	 * Add CSS
	 *
	 * @param string $widget_css The existing custom CSS from other modules or sections.
	 * @return string
	 */
	public static function add_css( $widget_css ) {
		$css = <<<HTML
			#environmental-data-section {
				ul {
				columns: 2;
				margin: 0;
				}
				li {
					font-size: .8rem;
					line-height: 1.2;
					margin: 0 0 .5em;
				}
			}
		HTML;

		return $widget_css . $css;
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
