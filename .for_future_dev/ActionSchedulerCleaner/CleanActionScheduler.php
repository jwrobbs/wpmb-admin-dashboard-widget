<?php
/**
 * Clean History Ajax Handler
 *
 * Handles the AJAX request to clean history data from the database.
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget\Ajax;

defined( 'ABSPATH' ) || exit;

/**
 * CleanActionScheduler Class
 */
class CleanActionScheduler {
	/**
	 * Init
	 */
	public static function init() {
		add_action( 'wp_ajax_wpmb_clean_history', array( self::class, 'handle' ) );
	}
	/**
	 * Handle the AJAX request to clean history data.
	 *
	 * @return void
	 */
	public static function handle(): void {
		$scope        = $_POST['scope'] ?? ''; // phpcs:ignore
		$nonce_action = 'week' === $scope ? 'clean_history_week' : 'clean_history_all';

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized.' );
		}

		if ( ! check_ajax_referer( $nonce_action, '_wpnonce', false ) ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		global $wpdb;

		$results = array();

		// Simple History tables.
		$sh_main    = $wpdb->prefix . 'simple_history';
		$sh_context = $wpdb->prefix . 'simple_history_contexts';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$sh_main}'" ) === $sh_main ) { // phpcs:ignore
			if ( 'week' === $scope ) {
				$cutoff = gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$sh_main} WHERE date < %s", $cutoff ) ); // phpcs:ignore
			} else {
				$wpdb->query( "TRUNCATE TABLE {$sh_main}" ); // phpcs:ignore
			}
			$results[] = 'Simple History cleared';
		}

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$sh_context}'" ) === $sh_context && $scope === 'all' ) { // phpcs:ignore
			$wpdb->query( "TRUNCATE TABLE {$sh_context}" ); // phpcs:ignore
		}

		// Action Scheduler tables.
		$as_tables = array(
			$wpdb->prefix . 'actionscheduler_actions',
			$wpdb->prefix . 'actionscheduler_claims',
			$wpdb->prefix . 'actionscheduler_groups',
			$wpdb->prefix . 'actionscheduler_logs',
		);

		foreach ( $as_tables as $table ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) { // phpcs:ignore
				if ( 'week' === $scope && false !== strpos( $table, 'actionscheduler_actions' ) ) {
					$cutoff = gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE scheduled_date_gmt < %s", $cutoff ) ); // phpcs:ignore
				} else {
					$wpdb->query( "TRUNCATE TABLE {$table}" ); // phpcs:ignore
				}
			}
		}

		$results[] = 'Action Scheduler cleared';

		wp_send_json_success(
			array(
				'message' => implode( '; ', $results ),
			)
		);
	}
}
