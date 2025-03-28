<?php
/**
 * Hooks
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget_Common;

use WPMB_Admin_Dashboard_Widget\Ajax\ToggleDebug;
use WPMB_Admin_Dashboard_Widget\DashboardWidget;
use WPMB_Admin_Dashboard_Widget\Modules\DebugModule;
use WPMB_Admin_Dashboard_Widget\Modules\EnvironmentalModule;

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
	}
}
