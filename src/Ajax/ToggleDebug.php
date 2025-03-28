<?php
/**
 * AJAX handler for toggling WP_DEBUG and WP_DEBUG_LOG in wp-config.php
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget\Ajax;

use WP_Filesystem_Base;

defined( 'ABSPATH' ) || exit;

/**
 * ToggleDebug Class
 */
class ToggleDebug {

	/**
	 * Init
	 */
	public static function init() {
		add_action( 'wp_ajax_wpmb_toggle_debug', array( self::class, 'handle' ) );
		add_action( 'wp_ajax_wpmb_delete_debug_log', array( static::class, 'delete_log' ) );
	}
	/**
	 * Handle the AJAX request to toggle WP_DEBUG and WP_DEBUG_LOG.
	 *
	 * This function checks the current status of WP_DEBUG and toggles it.
	 * It also updates the wp-config.php file accordingly.
	 *
	 * @return void
	 */
	public static function handle(): void {
		check_ajax_referer( 'toggle_debug' );

		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		global $wp_filesystem;
		WP_Filesystem();

		if ( ! $wp_filesystem instanceof WP_Filesystem_Base ) {
			wp_send_json_error( 'Filesystem initialization failed.' );
		}

		$config_path = ABSPATH . 'wp-config.php';

		if ( ! $wp_filesystem->is_writable( $config_path ) ) {
			wp_send_json_error( 'Config file is not writable.' );
		}

		$content = $wp_filesystem->get_contents( $config_path );

		if ( ! $content ) {
			wp_send_json_error( 'Could not read config file.' );
		}

		$has_debug         = preg_match( "/define\s*\(\s*'WP_DEBUG'\s*,\s*(true|false)\s*\);/", $content );
		$has_debug_log     = preg_match( "/define\s*\(\s*'WP_DEBUG_LOG'\s*,\s*(true|false)\s*\);/", $content );
		$has_debug_display = preg_match( "/define\s*\(\s*'WP_DEBUG_DISPLAY'\s*,\s*(true|false)\s*\);/", $content );

		$enabled = ( defined( 'WP_DEBUG' ) && WP_DEBUG );

		$new_debug         = $enabled ? 'false' : 'true';
		$new_debug_log     = $new_debug;
		$new_debug_display = 'false'; // Always false.

		// Toggle WP_DEBUG.
		if ( $has_debug ) {
			$content = preg_replace(
				"/define\s*\(\s*'WP_DEBUG'\s*,\s*(true|false)\s*\);/",
				"define('WP_DEBUG', $new_debug);",
				$content
			);
		} else {
			$content = self::insert_before_stop( $content, "define('WP_DEBUG', $new_debug);" );
		}

		// Toggle WP_DEBUG_LOG.
		if ( $has_debug_log ) {
			$content = preg_replace(
				"/define\s*\(\s*'WP_DEBUG_LOG'\s*,\s*(true|false)\s*\);/",
				"define('WP_DEBUG_LOG', $new_debug_log);",
				$content
			);
		} else {
			$content = self::insert_before_stop( $content, "define('WP_DEBUG_LOG', $new_debug_log);" );
		}

		// Always disable WP_DEBUG_DISPLAY.
		if ( $has_debug_display ) {
			$content = preg_replace(
				"/define\s*\(\s*'WP_DEBUG_DISPLAY'\s*,\s*(true|false)\s*\);/",
				"define('WP_DEBUG_DISPLAY', false);",
				$content
			);
		} else {
			$content = self::insert_before_stop( $content, "define('WP_DEBUG_DISPLAY', false);" );
		}

		$success = $wp_filesystem->put_contents( $config_path, $content );

		if ( ! $success ) {
			wp_send_json_error( 'Failed to write config file.' );
		}

		wp_send_json_success(
			array(
				'status'  => 'true' === $new_debug,
				'log'     => 'true' === $new_debug_log,
				'display' => false,
			)
		);
	}

	/**
	 * Insert before the "That's all, stop editing!" line in wp-config.php.
	 *
	 * This function ensures that the new define line is inserted before the
	 * "That's all, stop editing! Happy publishing." comment in the wp-config.php file.
	 *
	 * @param string $content The content of the wp-config.php file.
	 * @param string $define_line The define line to insert.
	 * @return string The modified content of the wp-config.php file.
	 */
	protected static function insert_before_stop( string $content, string $define_line ): string {
		$pattern     = '/\/\* That\'s all, stop editing! Happy publishing\. \*\//';
		$replacement = $define_line . "\n\n/* That's all, stop editing! Happy publishing. */";

		if ( preg_match( $pattern, $content ) ) {
			return preg_replace( $pattern, $replacement, $content );
		}

		// Fallback: append to end.
		return $content . "\n\n" . $define_line . "\n";
	}

	/**
	 * Delete the debug log file.
	 *
	 * This function handles the AJAX request to delete the debug log file (debug.log).
	 *
	 * @return void
	 */
	public static function delete_log() {
		check_ajax_referer( 'delete_debug_log' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		global $wp_filesystem;
		WP_Filesystem();

		$log_path = WP_CONTENT_DIR . '/debug.log';

		if ( ! $wp_filesystem->exists( $log_path ) ) {
			wp_send_json_error( 'File does not exist' );
		}

		if ( $wp_filesystem->delete( $log_path ) ) {
			wp_send_json_success();
		}

		wp_send_json_error( 'Failed to delete' );
	}
}
