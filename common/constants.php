<?php
/**
 * Plugin constants
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

defined( 'ABSPATH' ) || exit;

// Typical plugins constants for plugin dir and url.
if ( ! defined( 'WPMB_ADMIN_DASHBOARD_WIDGET_DIR' ) ) {
	define( 'WPMB_ADMIN_DASHBOARD_WIDGET_DIR', plugin_dir_path( __DIR__ ) );
}
if ( ! defined( 'WPMB_ADMIN_DASHBOARD_WIDGET_URL' ) ) {
	define( 'WPMB_ADMIN_DASHBOARD_WIDGET_URL', plugin_dir_url( __DIR__ ) );
}
