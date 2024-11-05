<?php

namespace Wlrp\App\Helpers;

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
		$brand_ids = wp_get_post_terms( $product->get_id(), 'pwb-brand', array( 'fields' => 'ids' ) );

		return is_array( $brand_ids ) ? $brand_ids : array();
	}
}