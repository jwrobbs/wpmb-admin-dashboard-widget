<?php
/**
 * Hooks
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Dashboard_Widget_Common;

use WPMB_Admin_Dashboard_Widget\DashboardWidget;

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
	}
}
