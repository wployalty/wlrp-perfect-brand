<?php

namespace Wlrp\App\Conditions;

use Wlrp\App\Helpers\Woocommerce;

abstract class Base extends \Wlr\App\Conditions\Base {

	/**
	 * Method to add the missing condition of parent class for processing.
	 *
	 * @param $product
	 * @param $type
	 * @param $method
	 * @param $values
	 * @param $cart_item
	 *
	 * @return bool
	 */
	function match( $product, $type, $method, $values, $cart_item = array() ) {
		if ( is_a( $product, 'WC_Product' ) ) {
			$method = ! empty( $method ) ? $method : 'in_list';
			if ( 'brands' === $type ) {
				return $this->compareWithBrands( $product, $values );
			}
			parent::match( $product, $type, $method, $values, $cart_item );
		}

		return false;
	}

	/**
	 * Method to compare the incoming product with branded products.
	 *
	 * @param $product
	 * @param $operation_values
	 *
	 * @return bool
	 */
	function compareWithBrands( $product, $operation_values ) {
		if ( ! is_object( $product ) || ! is_array( $operation_values ) ) {
			return false;
		}
		$woocommerce_helper = new Woocommerce();
		$brand_ids          = $woocommerce_helper->getProductBrands( $product );
		if ( count( array_intersect( $brand_ids, $operation_values ) ) > 0 ) {
			return true;
		}
		if ( self::$woocommerce_helper->isMethodExists( $product,
				'get_type' ) && $product->get_type() === 'variation' ) {
			$parent_product = self::$woocommerce_helper->getParentProduct( $product );
			if ( is_object( $parent_product ) ) {
				$brand_ids = $woocommerce_helper->getProductBrands( $parent_product );
			}
		}

		return count( array_intersect( $brand_ids, $operation_values ) ) > 0;
	}
}