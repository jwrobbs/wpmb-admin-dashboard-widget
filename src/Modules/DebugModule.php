<?php
/**
 * Debug Module
 *
 * Displays debug settings and a toggle button.
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Debug Module Class
 */
class DebugModule extends AbstractModule {


	/**
	 * Generate the debug data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {
		$content = '';

		$debug_data = self::get_debug_data();

		// Add statuses.
		$content .= '<div class="debug-data-section__statuses">';

		$content .= '<div class="debug-data-section__label">WP_DEBUG</div>';
		$content .= '<div class="debug-data-section__value" id="WP_DEBUG-value">'
			. esc_html( self::compute_status( $debug_data['active'] ) )
			. '</div>';
		$content .= '<div class="debug-data-section__label">WP_DEBUG_LOG</div>';
		$content .= '<div class="debug-data-section__value" id="WP_DEBUG_LOG-value">'
			. esc_html( self::compute_status( $debug_data['debug_log'] ) )
			. '</div>';
		$content .= '<div class="debug-data-section__label">WP_DEBUG_DISPLAY</div>';
		$content .= '<div class="debug-data-section__value" id="WP_DEBUG_DISPLAY-value">'
			. esc_html( self::compute_status( $debug_data['debug_display'] ) )
			. '</div>';

		$content .= '</div>';

		// Add button.
		if ( self::is_wp_config_writable() ) {
			$content .= self::get_button( $debug_data );
		}

		// Create Section.
		$section = new Section(
			title: 'Debug Data',
			content: $content, // Content will be generated in the render_section method.
			css_id: 'debug-data-section' // CSS ID for the section.
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
			#debug-data-section .admin-tools-dashboard-widget__content {
				display: grid;
				grid-template-columns: 1fr 1fr;

				.debug-data-section__statuses {
					display: grid;
					grid-template-columns: auto auto;
					column-gap: 10px;
					margin: 0;
					padding: 0;
				}
				ul {
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
	 * Get debug data
	 *
	 * This function collects debug information for display.
	 *
	 * @return array
	 */
	public static function get_debug_data() {
		// Collect debug information. This is just an example and can be expanded as needed.
		$debug_data                  = array();
		$debug_data['active']        = WP_DEBUG ?? null;
		$debug_data['debug_log']     = WP_DEBUG_LOG ?? null;
		$debug_data['debug_display'] = WP_DEBUG_DISPLAY ?? null;

		return $debug_data;
	}

	/**
	 * Compute status.
	 *
	 * @param bool|null $active The active status.
	 * @return string
	 */
	public static function compute_status( $active = null ) {
		/**
		 * Compute the status based on the active parameter.
		 *
		 * @param bool|null $active The active status.
		 * @return string
		 */
		if ( is_null( $active ) ) {
			return 'Not set';
		}

		if ( $active ) {
			return 'Enabled';
		} else {
			return 'Disabled';
		}
	}

	/**
	 * Is config writeable?
	 *
	 * @return bool
	 */
	protected static function is_wp_config_writable() {
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			WP_Filesystem();
		}
		$config_path = ABSPATH . 'wp-config.php';
		return $wp_filesystem
		&& $wp_filesystem->exists( $config_path )
		&& $wp_filesystem->is_writable( $config_path );
	}

	/**
	 * Get button HTML
	 *
	 * @param array $debug_data The debug data array.
	 * @return string
	 */
	protected static function get_button( $debug_data ) {
		$nonce = wp_create_nonce( 'toggle_debug' );

		if ( $debug_data['active'] ) {
			$button_label = 'Disable Debug Mode';
		} else {
			$button_label = 'Enable Debug Mode';
		}

		$button = <<<HTML
		<button id="toggle-debug-button" class="button button-secondary" data-nonce={$nonce}>
			$button_label
		</button>
		HTML;

		return $button;
	}

	/**
	 * Extend Init
	 *
	 * This method can be overridden in the child class to perform additional initialization
	 * tasks specific to that module.
	 */
	protected static function extend_init() {

		add_action( 'admin_enqueue_scripts', array( static::class, 'enqueue_js' ) );
	}

	/**
	 * Enqueue JavaScript
	 *
	 * This method is used to enqueue JavaScript files for the module.
	 */
	public static function enqueue_js() {
		wp_enqueue_script(
			'toggle-debug',
			\WPMB_ADMIN_DASHBOARD_WIDGET_URL . 'assets/toggle-debug.js',
			array( 'jquery' ),
			filemtime( \WPMB_ADMIN_DASHBOARD_WIDGET_DIR . '/assets/toggle-debug.js' ),
			true
		);
		wp_localize_script(
			'toggle-debug',
			'DebugToggleAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'wpmb_toggle_debug',
			)
		);
	}
}
