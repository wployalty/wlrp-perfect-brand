<?php

namespace Wlrp\App\Helpers;


use Wlr\App\Premium\Helpers\AjaxProCondition;

class AjaxCompatiblityCondition extends AjaxProCondition {
	public static $instance = null;

	public static function getInstance( array $config = array() ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $config );
		}

		return self::$instance;
	}

	/**
	 * Method to fetch the brand.
	 *
	 * @return array|array[]
	 */
	public function ajaxBrands() {
		$query = Input::get( 'q', '' );
		$terms = get_terms( array(
			'taxonomy'   => 'pwb-brand',
			'name__like' => $query,
			'hide_empty' => false,
			'number'     => 20
		) );

		return array_map( function ( $term ) {
			return array(
				'value' => (string) $term->term_id,
				'label' => $term->name,
			);
		}, $terms );

	}

}