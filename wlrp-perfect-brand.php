<?php
/**
 * Plugin Name: WPLoyalty - Perfect Brand Compatability
 * Plugin URI: https://www.wployalty.net
 * Description: Compatability to earn via configured brands for Perfect Brands for Woocommerce.
 * Version: 1.0.0
 * Author: WPLoyalty
 * Slug: wp-loyalty-optin
 * Text Domain: wlrp-perfect-brand
 * Domain Path: /i18n/languages/
 * Requires at least: 4.9.0
 * WC requires at least: 6.5
 * WC tested up to: 8.0
 * Contributors: Sabhari
 * Author URI: https://wployalty.net/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WPLoyalty: 1.2.9
 * WPLoyalty Page Link: wp-loyalty-optin
 */
defined( 'ABSPATH' ) or die;

// Autoload the vendor
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}

require __DIR__ . '/vendor/autoload.php';

if ( ! class_exists( \Wlrp\App\Router::class ) || ! class_exists( \Wlrp\App\Helpers\Compatibility::class ) ) {
	return;
}
//Define the plugin version
defined( 'WLRP_PLUGIN_NAME' ) or define( 'WLRP_PLUGIN_NAME', 'WPLoyalty - Perfect Brand Compatability' );
defined( 'WLRP_PLUGIN_VERSION' ) or define( 'WLRP_PLUGIN_VERSION', '1.0.0' );
defined( 'WLRP_MINIMUM_PHP_VERSION' ) or define( 'WLRP_MINIMUM_PHP_VERSION', '7.4' );
defined( 'WLRP_MINIMUM_WP_VERSION' ) or define( 'WLRP_MINIMUM_WP_VERSION', '4.9' );
defined( 'WLRP_MINIMUM_WC_VERSION' ) or define( 'WLRP_MINIMUM_WC_VERSION', '6.0' );
defined( 'WLRP_MINIMUM_WLR_VERSION' ) or define( 'WLRP_MINIMUM_WLR_VERSION', '1.2.9' );
defined( 'WLRP_PLUGIN_SLUG' ) or define( 'WLRP_PLUGIN_SLUG', 'wlrp-perfect-brand' );
defined( 'WLRP_TEXT_DOMAIN' ) or define( 'WLRP_TEXT_DOMAIN', 'wlrp-perfect-brand' );
defined( 'WLRP_PLUGIN_FILE' ) or define( 'WLRP_PLUGIN_FILE', __FILE__ );
defined( 'WLRP_PLUGIN_PATH' ) or define( 'WLRP_PLUGIN_PATH', __DIR__ . '/' );
defined( 'WLRP_PLUGIN_URL' ) or define( 'WLRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! \Wlrp\App\Helpers\Compatibility::check() ) {
	return;
}


\Wlrp\App\Router::init();
