<?php
/**
 * Simple History Module
 *
 * Displays Simple History data in the WordPress admin dashboard widget.
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Simple History Module Class
 */
class SimpleHistoryModule extends AbstractModule {


	/**
	 * Generate the Simple History data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$usage = self::get_usage();
		if ( 1023 < $usage ) {
			$class = 'class="data-warning"';
		} else {
			$class = '';
		}
		$content = "<h4 $class>Usage: $usage MB</h4>";

		$nonce_week = wp_create_nonce( 'clean_simple_history_week' );
		$nonce_all  = wp_create_nonce( 'clean_simple_history_all' );

		$content .= <<<HTML
		<div class="button-container">
			<button
				class="button button-secondary wpmb-clean-simple-history"
				data-scope="week"
				data-nonce="{$nonce_week}"
			>
				Delete entries older than 1 week
			</button>

			<button
				class="button button-secondary wpmb-clean-simple-history"
				data-scope="all"
				data-nonce="{$nonce_all}"
			>
				Delete all history
			</button>
		</div>
		<span id="simple-history-clean-response" style="margin-left: 1em;"></span>
		HTML;

		$section = new Section(
			title: 'Simple History Data',
			content: $content, // Content will be generated in the render_section method.
			css_id: 'simple-history-data-section' // CSS ID for the section.
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
		#simple-history-data-section {
			.data-warning {
				color: red;
				font-weight: bold;
			}
			h4 {
				font-size: 16px;
				font-weight: normal;
			}
			.admin-tools-dashboard-widget__content .button-container {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 10px;
				align-items: center;
			}
			#simple-history-clean-response {
				display: block;
				text-align: center;
				padding: 1em 0;
			}
		}
		HTML;

		return $widget_css . $css;
	}

	/**
	 * Get usage data
	 *
	 * This function collects Simple History usage information for display.
	 *
	 * @return string
	 */
	public static function get_usage() {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . 'simple_history',
			$wpdb->prefix . 'simple_history_contexts',
		);

		$table_placeholders = implode( ',', array_fill( 0, count( $tables ), '%s' ) );

		$sql = "
			SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size
			FROM information_schema.tables
			WHERE table_schema = DATABASE()
			AND table_name IN ($table_placeholders)
		";

		$size = $wpdb->get_var( $wpdb->prepare( $sql, ...$tables ) ); // phpcs:ignore

		return $size ?? 0;
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
			'clean-simple-history',
			\WPMB_ADMIN_DASHBOARD_WIDGET_URL . 'assets/clean-simple-history.js',
			array( 'jquery' ),
			filemtime( \WPMB_ADMIN_DASHBOARD_WIDGET_DIR . '/assets/clean-simple-history.js' ),
			true
		);
		wp_localize_script(
			'clean-simple-history',
			'CleanHistoryAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'wpmb_clean_simple_history',
			)
		);
	}
}
