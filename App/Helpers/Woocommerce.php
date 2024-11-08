<?php

namespace Wlrp\App\Helpers;

defined( 'ABSPATH' ) or die;

class Woocommerce {

	/**
	 * Method to fetch the brands of the products.
	 *
	 * @param $product
	 *
	 * @return array
	 */
	function getProductBrands( $product ) {
		if ( ! is_object( $product ) ) {
			return array();
		}
		$brand_ids = wp_get_post_terms( $product->get_id(),
			self::isParentPluginEnabled( get_option( 'wlrp_compatability_choice' ) ) ? get_option( 'wlrp_compatability_choice' ) : '',
			array( 'fields' => 'ids' ) );

		return is_array( $brand_ids ) ? $brand_ids : array();
	}

	/**
	 * Method to check the active status of selected plugin.
	 *
	 * @param   string  $parent_plugin  Selected plugin name.
	 *
	 * @return bool
	 */
	public static function isParentPluginEnabled( $parent_plugin ) {
		if ( empty( $parent_plugin ) ) {
			return false;
		}
		$supported_plugins = [
			'pwb-brand'     => 'perfect-woocommerce-brands/perfect-woocommerce-brands.php',
			'product_brand' => 'woocommerce-brands/woocommerce-brands.php',
		];

		if ( ! isset( $supported_plugins[ $parent_plugin ] ) ) {
			return false;
		}

		$selected_plugin_needle = $supported_plugins[ $parent_plugin ];


		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( $selected_plugin_needle,
				$active_plugins ) || array_key_exists( $selected_plugin_needle, $active_plugins );
	}
}