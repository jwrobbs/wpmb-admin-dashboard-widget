<?php
/**
 * Dashboard Widget
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget;

defined( 'ABSPATH' ) || exit;

/**
 * Dashboard Widget class
 */
class DashboardWidget {
	/**
	 * Constructor
	 */
	public static function init() {
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'add_dashboard_widget' ) );
	}

	/**
	 * Add the dashboard widget
	 */
	public static function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'wp_admin_tools',
			'Admin Tools',
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Render the dashboard widget
	 */
	public static function render(): void {
		$sections = apply_filters( 'wp_admin_tools_sections', array() );

		foreach ( $sections as $section ) {
			echo '<div class="wp-admin-tools-section">';
			echo wp_kses_post( $section );
			echo '</div>';
		}
	}
}
