<?php
/**
 * Plugin Name: WPLoyalty - Brand Compatibility
 * Plugin URI: https://www.wployalty.net
 * Description: Compatibility to earn via configured brands for Perfect Brands for Woocommerce & Woocommerce Brands.
 * Version: 1.0.0
 * Author: WPLoyalty
 * Slug: wlrp-perfect-brand
 * Text Domain: wlrp-perfect-brand
 * Domain Path: /i18n/languages/
 * Requires at least: 4.9.0
 * WC requires at least: 9.6
 * WC tested up to: 9.6
 * Contributors: Sabhari
 * Author URI: https://wployalty.net/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WPLoyalty: 1.2.9
 * WPLoyalty Page Link: wlrp-perfect-brand
 */
defined( 'ABSPATH' ) or die;
if ( ! function_exists( 'isWLRActive' ) ) {
	function isWLRActive() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'wp-loyalty-rules/wp-loyalty-rules.php',
				$active_plugins ) || array_key_exists( 'wp-loyalty-rules/wp-loyalty-rules.php', $active_plugins );
	}
}

// Autoload the vendor
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}

require __DIR__ . '/vendor/autoload.php';

if ( ! class_exists( \Wlrp\App\Router::class ) || ! class_exists( \Wlrp\App\Helpers\Compatibility::class ) ) {
	return;
}

//Define the plugin version
defined( 'WLRP_PLUGIN_NAME' ) or define( 'WLRP_PLUGIN_NAME', 'WPLoyalty - Brand compatibility' );
defined( 'WLRP_PLUGIN_VERSION' ) or define( 'WLRP_PLUGIN_VERSION', '1.0.0' );
defined( 'WLRP_MINIMUM_PHP_VERSION' ) or define( 'WLRP_MINIMUM_PHP_VERSION', '7.4' );
defined( 'WLRP_MINIMUM_WP_VERSION' ) or define( 'WLRP_MINIMUM_WP_VERSION', '4.9' );
defined( 'WLRP_MINIMUM_WC_VERSION' ) or define( 'WLRP_MINIMUM_WC_VERSION', '9.6' );
defined( 'WLRP_MINIMUM_WLR_VERSION' ) or define( 'WLRP_MINIMUM_WLR_VERSION', '1.3.0' );
defined( 'WLRP_PLUGIN_SLUG' ) or define( 'WLRP_PLUGIN_SLUG', 'wlrp-perfect-brand' );
defined( 'WLRP_TEXT_DOMAIN' ) or define( 'WLRP_TEXT_DOMAIN', 'wlrp-perfect-brand' );
defined( 'WLRP_PLUGIN_FILE' ) or define( 'WLRP_PLUGIN_FILE', __FILE__ );
defined( 'WLRP_PLUGIN_PATH' ) or define( 'WLRP_PLUGIN_PATH', __DIR__ . '/' );
defined( 'WLRP_PLUGIN_URL' ) or define( 'WLRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! \Wlrp\App\Helpers\Compatibility::check() ) {
	return;
}
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

// Check WPLoyalty PRO installed and file loaded
add_action( 'plugins_loaded', function () {
	if ( isWLRActive() && class_exists( '\Wlr\App\Premium\Premium' ) ) {
		\Wlrp\App\Router::init();
	}
}, 10 );
