<?php
/**
 * Plugin Name: WP Rocket | No LazyLoad for CookieCheck User Agent
 * Description: Disables WP Rocket’s LazyLoad feature for visitors with user agent containing "CookieCheck".
 * Plugin URI:  https://github.com/cookiefirst/wprocket-disable-lazyload
 * Author:      CookieFirst support team
 * Author URI:  https://cookiefirst.com
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright Digital Data Solutions BV -  2024
 */

namespace WP_Rocket\Helpers\compat\cookie_check;

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) or die();

/**
 * Disables WP Rocket’s LazyLoad feature for visitors with user agent containing "CookieCheck".
 */
function disable_lazyload_for_cookiecheck() {

	if ( is_admin() ) {
		return;
	}

	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'CookieCheck' ) !== false ) {
		add_filter( 'do_rocket_lazyload', '__return_false' );
	}
}
add_action( 'wp', __NAMESPACE__ . '\disable_lazyload_for_cookiecheck', 100 );

/**
 * Render admin notice.
 */
function render_admin_notice() {

	// Only for admins.
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	// Bail if WP Rocket is not active.
	if ( ! function_exists( 'get_rocket_option' ) ) {
		return false;
	}

	// Bail if LazyLoad is disabled.
	if ( ! get_rocket_option( 'lazyload' ) ) {
		return false;
	}

	$current_screen = get_current_screen();

	// Only on WP Rocket settings pages.
	if ( 'admin_notices' === current_filter() && ( isset( $current_screen ) && 'settings_page_wprocket' !== $current_screen->base ) ) {
		return false;
	}

	// Render message.
	printf(
		'<div class="notice notice-info"><p>%s</p></div>',
		__( '<strong>Note:</strong> LazyLoad is programmatically disabled for visitors with user agent containing <em>CookieCheck</em>.' )
	);
}
add_action( 'admin_notices', __NAMESPACE__ . '\render_admin_notice', 100 );
