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
 * Action Scheduler Module Class
 */
class ActionSchedulerModule extends AbstractModule {


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
		$content = "<p $class>Usage: $usage MB</p>";

		$nonce_week = wp_create_nonce( 'clean_history_week' );
		$nonce_all  = wp_create_nonce( 'clean_history_all' );

		$content .= <<<HTML
		<button
			class="button button-secondary wpmb-clean-history"
			data-scope="week"
			data-nonce="{$nonce_week}"
		>
			Delete entries older than 1 week
		</button>

		<button
			class="button button-secondary wpmb-clean-history"
			data-scope="all"
			data-nonce="{$nonce_all}"
		>
			Delete all history
		</button>

		<span id="history-clean-response" style="margin-left: 1em;"></span>
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
			.admin-tools-dashboard-widget__content {
				display: grid;
				grid-template-columns: auto auto auto;
				grid-template-areas:
					"usage week all"
					"ajax ajax ajax";
			}
			#history-clean-response {
				grid-area: ajax;
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
			$wpdb->prefix . 'actionscheduler_actions',
			$wpdb->prefix . 'actionscheduler_claims',
			$wpdb->prefix . 'actionscheduler_groups',
			$wpdb->prefix . 'actionscheduler_logs',
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
			'clean-history',
			\WPMB_ADMIN_DASHBOARD_WIDGET_URL . 'assets/clean-history.js',
			array( 'jquery' ),
			filemtime( \WPMB_ADMIN_DASHBOARD_WIDGET_DIR . '/assets/clean-history.js' ),
			true
		);
		wp_localize_script(
			'clean-history',
			'CleanHistoryAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'wpmb_clean_history',
			)
		);
	}
}
