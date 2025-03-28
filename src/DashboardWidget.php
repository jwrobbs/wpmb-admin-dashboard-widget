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
		add_action( 'admin_head', array( self::class, 'add_custom_css' ) );
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

		echo '<div class="admin-tools-dashboard-widget">';
		foreach ( $sections as $section ) {

			if ( ! empty( $section->css_id ) ) {
				// If a CSS ID is provided, add it to the section for styling or identification.
				$css_id = "id='{$section->css_id}'";
			} else {
				$css_id = '';
			}
			$title    = $section->title ? '<h3>' . $section->title . '</h3>' : '';
			$subtitle = $section->subtitle ? '<h4>' . $section->subtitle . '</h4>' : '';
			$body     = $section->content ?? '';

			$content = <<<HTML
				<div class="admin-tools-dashboard-widget__section" {$css_id}>
					{$title}
					{$subtitle}
					<div class="admin-tools-dashboard-widget__content">
						{$body}
					</div>
				</div>
			HTML;
			echo wp_kses_post( $content );
		}
		echo '</div>'; // Close the main widget div.
	}

	/**
	 * Add custom CSS for the dashboard widget
	 *
	 * This function outputs custom CSS to style the dashboard widget.
	 */
	public static function add_custom_css() {
		$screen = get_current_screen();
		if ( ! $screen || 'dashboard' !== $screen->id ) {
			return;
		}

		$custom_css = self::get_main_css();

		// Add filter to allow modules to add their own CSS.
		$custom_css .= apply_filters( 'wp_admin_tools_custom_css', '' );

		// Output the custom CSS to the admin head.
		if ( ! empty( $custom_css ) ) {
			// Ensure the CSS is properly escaped.
			$custom_css = "<style type='text/css'>\n" . esc_html( $custom_css ) . "\n</style>";
			echo $custom_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get the main CSS for the dashboard widget
	 *
	 * @return string
	 */
	public static function get_main_css() {
		return <<<CSS
			#dashboard-widgets .admin-tools-dashboard-widget h3 {
				font-size: 1.5em; /* Adjust the font size for section titles */
				margin: .5em 0; /* Add some space below the title */
				font-weight: bold; /* Make the title bold */

			}
		CSS;
	}
}
