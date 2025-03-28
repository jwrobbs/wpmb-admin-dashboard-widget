<?php
/**
 * Hooks
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget_Common;

use WPMB_Admin_Dashboard_Widget\Ajax\CleanSimpleHistory;
use WPMB_Admin_Dashboard_Widget\Ajax\ToggleDebug;
use WPMB_Admin_Dashboard_Widget\DashboardWidget;
use WPMB_Admin_Dashboard_Widget\Modules\DebugModule;
use WPMB_Admin_Dashboard_Widget\Modules\EnvironmentalModule;
use WPMB_Admin_Dashboard_Widget\Modules\SimpleHistoryModule;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks class
 */
class Hooks {
	/**
	 * Initialize the hooks
	 */
	public static function init() {
		DashboardWidget::init();
		ToggleDebug::init();

		EnvironmentalModule::init();
		DebugModule::init();

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( \is_plugin_active( 'simple-history/index.php' ) ) {
			CleanSimpleHistory::init();
			SimpleHistoryModule::init();
		}
	}
}
