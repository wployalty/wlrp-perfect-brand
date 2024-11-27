<?php

namespace Wlrp\App\Helpers;

defined( 'ABSPATH' ) or die;

/**
 * Class Woocommerce
 */
class Woocommerce {
	/**
	 * Checks if the current user has admin privilege.
	 * This method determines if the current user has the privilege to manage WooCommerce.
	 * @return bool Returns true if the current user has admin privilege, false otherwise.
	 */
	public static function hasAdminPrivilege() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Creates a nonce for a given action.
	 * This method generates a cryptographic nonce which can be used to verify the origin of a request for a specific action.
	 *
	 * @param   int|string  $action  Optional. The action name. Default is -1.
	 *
	 * @return string The generated nonce string.
	 */
	public static function createNonce( $action = - 1 ) {
		return wp_create_nonce( $action );
	}

	/**
	 * Checks if a method exists in the given object.
	 * This method determines whether the specified object has the method with the given name.
	 *
	 * @param   object  $object       The object to check for the method.
	 * @param   string  $method_name  The name of the method to check.
	 *
	 * @return bool Returns true if the method exists in the object, false otherwise.
	 */
	public static function isMethodExists( $object, $method_name ) {
		if ( is_object( $object ) && method_exists( $object, $method_name ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Verify the validity of a nonce.
	 *
	 * @param   string      $nonce   The nonce value to verify.
	 * @param   string|int  $action  Optional. The nonce action name. Default is -1.
	 *
	 * @return bool Whether the nonce is valid or not.
	 */
	public static function verifyNonce( $nonce, $action = - 1 ) {
		if ( wp_verify_nonce( $nonce, $action ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to fetch the brands of the products.
	 *
	 * @param $product
	 *
	 * @return array
	 */
	function getProductBrands( $product ) {
		if ( ! is_object( $product ) ) {
			return [];
		}
		$brand_ids = wp_get_post_terms( $product->get_id(),
			self::isParentPluginEnabled( get_option( 'wlrp_compatibility_choice' ) ) ? get_option( 'wlrp_compatibility_choice' ) : '',
			array( 'fields' => 'ids' ) );

		return is_array( $brand_ids ) ? $brand_ids : [];
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

	/**
	 * Method to get the clean html.
	 *
	 * @param   string  $html  The html to clean.
	 *
	 * @return string The cleaned html.
	 * @throws \Exception
	 */
	public static function getCleanHtml( $html ) {
		try {
			$html         = html_entity_decode( $html );
			$html         = preg_replace( '/(<(script|style|iframe)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $html );
			$allowed_html = [
				'br'     => [],
				'strong' => [],
				'span'   => [ 'class' => [] ],
				'div'    => [ 'class' => [] ],
				'p'      => [ 'class' => [] ],
				'b'      => [ 'class' => [] ],
				'i'      => [ 'class' => [] ],
			];

			return wp_kses( $html, $allowed_html );
		} catch ( \Exception $e ) {
			return '';
		}
	}
}