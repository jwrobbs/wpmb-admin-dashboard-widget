<?php
/**
 * Plugin Name: WPMB Admin Dashboard Widget
 * Plugin URI: https://joshrobbs.com
 * Description: Adds custom dashboard widget for admins.
 * Version: 0.0.1
 * Author: Josh Robbs
 * Author URI: https://joshrobbs.com
 * License: The Unlicense
 * License URI: https://unlicense.org/
 * Text Domain: wpmb-admin-dashboard-widget
 *
 * @package WPMB_Admin_Dashboard_Widget
 */

namespace WPMB_Admin_Metabox;

defined( 'ABSPATH' ) || exit;

if ( ! is_admin() ) {
	return;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/common/constants.php';

\WPMB_Admin_Dashboard_Widget_Common\Hooks::init();
