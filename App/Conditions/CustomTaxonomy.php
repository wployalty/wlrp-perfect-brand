<?php

namespace Wlrp\App\Conditions;

defined( 'ABSPATH' ) or die;

use Wlrp\App\Conditions\Base;

class CustomTaxonomy extends Base {
	function __construct() {
		parent::__construct();
		$this->name  = 'brands';
		$this->label = __( 'Custom Taxonomy', 'wlrp-perfect-brand' );
		$this->group = __( 'Product', 'wlrp-perfect-brand' );
	}

	public function check( $options, $data ) {
		$status = false;
		if ( isset( $options->operator ) && isset( $options->value ) ) {
			$options->value    = $this->changeOptionValue( $options->value );
			$is_calculate_base = isset( $data['is_calculate_based'] ) && ! empty( $data['is_calculate_based'] ) ? $data['is_calculate_based'] : '';
			if ( $is_calculate_base === 'cart' && isset( $data[ $is_calculate_base ] ) && ! empty( $data[ $is_calculate_base ] ) ) {
				$object = self::$woocommerce_helper->getCart( $data[ $is_calculate_base ] );
				$items  = self::$woocommerce_helper->getCartItems( $object );
				$status = $this->doItemsCheck( $object, $items, $options, $data, 'brands' );
			} elseif ( $is_calculate_base === 'order' && isset( $data[ $is_calculate_base ] ) && ! empty( $data[ $is_calculate_base ] ) ) {
				$object = self::$woocommerce_helper->getOrder( $data[ $is_calculate_base ] );
				$items  = self::$woocommerce_helper->getOrderItems( $object );
				$status = $this->doItemsCheck( $object, $items, $options, $data, 'brands' );
			} elseif ( $is_calculate_base === 'product' && isset( $data[ $is_calculate_base ] ) && ! empty( $data[ $is_calculate_base ] ) ) {
				$status = $this->isProductValid( $options, $data );
			}
		}

		return $status;
	}

	public function isProductValid( $options, $data ) {
		$is_calculate_base = $this->getCalculateBased( $data );
		$product           = array();
		if ( $is_calculate_base == 'cart' && isset( $data[ $is_calculate_base ] ) && ! empty( $data[ $is_calculate_base ] ) ) {
			$item    = isset( $data['current'] ) ? $data['current'] : array();
			$product = isset( $item['data'] ) ? $item['data'] : array();
		} elseif ( $is_calculate_base == 'order' && isset( $data[ $is_calculate_base ] ) && ! empty( $data[ $is_calculate_base ] ) ) {
			$order   = self::$woocommerce_helper->getOrder( $data[ $is_calculate_base ] );
			$item    = isset( $data['current'] ) ? $data['current'] : array();
			$product = version_compare( WC_VERSION, '4.4.0', '<' )
				? $order->get_product_from_item( $item )
				: $item->get_product();
		} elseif ( $is_calculate_base == 'product' && isset( $data[ $is_calculate_base ] ) && ! empty( $data[ $is_calculate_base ] ) ) {
			$product = self::$woocommerce_helper->getProduct( $data[ $is_calculate_base ] );
		}
		if ( empty( $product ) ) {
			return false;
		}
		$status            = false;
		$comparison_value  = (array) isset( $options->value ) ? ( array_column( $options->value,
			'value' ) ? $this->changeOptionValue( $options->value ) : $options->value ) : array();
		$comparison_method = isset( $options->operator ) ? $options->operator : 'in_list';
		$is_in_list        = $this->compareWithBrands( $product, $comparison_value );
		if ( $is_in_list && $comparison_method == 'in_list' ) {
			$status = true;
		}
		if ( ! $is_in_list && $comparison_method == 'not_in_list' ) {
			$status = true;
		}

		return $status;
	}

}