<?php

namespace Wlrp\App\Helpers;

use Exception;
use Valitron\Validator;

class Validation {
	/**
	 * Method to validate the brands tab.
	 *
	 * @param $post
	 *
	 * @return bool|array Returns true if the data is valid, otherwise an array of error messages.
	 */
	static function validateBrandsTab( $post ) {
		$rule_validator      = new Validator( $post );
		$labels_array        = [];
		$labels_array_fields = [
			'conditions.*.options.qty',
			'conditions.*.options.operator',
			'conditions.*.options.condition',
		];
		$this_field          = __( "This field", 'wlrp-perfect-brand' );
		foreach ( $labels_array_fields as $label ) {
			$labels_array[ $label ] = $this_field;
		}
		$rule_validator->labels( $labels_array );
		$rule_validator->stopOnFirstFail( false );
		Validator::addRule( 'cleanHtml', [
			__CLASS__,
			'validateCleanHtml'
		], __( 'Invalid characters', 'wlrp-perfect-brand' ) );
		Validator::addRule( 'basicHtmlTags', [
			__CLASS__,
			'validateBasicHtmlTags'
		], __( 'Invalid characters', 'wlrp-perfect-brand' ) );
		Validator::addRule( 'alphaNumWithUnderscore', [
			__CLASS__,
			'validateAlphaNumWithUnderscore'
		], __( 'validation has failed', 'wlrp-perfect-brand' ) );
		Validator::addRule( 'number', [
			__CLASS__,
			'validateNumber'
		], __( 'accepts only numbers', 'wlrp-perfect-brand' ) );
		Validator::addRule( 'numberGeZero', [
			__CLASS__,
			'validateNumberGeZero'
		], __( 'required field', 'wlrp-perfect-brand' ) );
		Validator::addRule( 'isEmpty', [ __CLASS__, 'validateIsEmpty' ], __( ' is empty', 'wlrp-perfect-brand' ) );
		Validator::addRule( 'isBrandExists', [
			__CLASS__,
			'checkIsBrandExist'
		], __( 'brand is not available', 'wlrp-perfect-brand' ) );
		$required_fields       = [];
		$number_non_zero_field = [];
		$empty_check_fields    = [];

		if ( isset( $post['conditions'] ) && is_array( $post['conditions'] ) && ! empty( $post['conditions'] ) ) {
			$condition_label_text   = $condition_label = $condition_clean = $check_brand_exist = [];
			$condition_label_fields = [
				'conditions.brands.value',
				'conditions.brands.operator',
				'conditions.brands.condition',
				'conditions.brands.qty',
			];
			foreach ( $condition_label_fields as $label ) {
				$condition_label_text[ $label ] = $this_field;
			}
			foreach ( $post['conditions'] as $key => $condition ) {
				switch ( $condition['type'] ) {
					case 'brands':
						$required_fields[]                                              = 'conditions.' . $key . '.options.operator';
						$empty_check_fields[]                                           = 'conditions.' . $key . '.options.operator';
						$condition_label[ 'conditions.' . $key . '.options.operator' ]  = $condition_label_text[ 'conditions.' . $condition['type'] . '.operator' ];
						$condition_clean[]                                              = 'conditions.' . $key . '.options.operator';
						$required_fields[]                                              = 'conditions.' . $key . '.options.value';
						$empty_check_fields[]                                           = 'conditions.' . $key . '.options.value';
						$check_brand_exist[]                                            = 'conditions.' . $key . '.options.value';
						$condition_label[ 'conditions.' . $key . '.options.value' ]     = $condition_label_text[ 'conditions.' . $condition['type'] . '.value' ];
						$required_fields[]                                              = 'conditions.' . $key . '.options.condition';
						$empty_check_fields[]                                           = 'conditions.' . $key . '.options.condition';
						$condition_label[ 'conditions.' . $key . '.options.condition' ] = $condition_label_text[ 'conditions.' . $condition['type'] . '.condition' ];
						$condition_clean[]                                              = 'conditions.' . $key . '.options.condition';
						$required_fields[]                                              = 'conditions.' . $key . '.options.qty';
						$number_non_zero_field[]                                        = 'conditions.' . $key . '.options.qty';
						$condition_label[ 'conditions.' . $key . '.options.qty' ]       = $condition_label_text[ 'conditions.' . $condition['type'] . '.qty' ];
						break;
				}
			}
			$rule_validator->labels( $condition_label );
			$rule_validator->rule( 'cleanHtml', $condition_clean )->message( __( '{field} has invalid characters',
				'wlrp-perfect-brand' ) );
			$rule_validator->rule( 'basicHtmlTags', $condition_clean )->message( __( '{field} has invalid characters',
				'wlrp-perfect-brand' ) );
			$rule_validator->rule( 'isBrandExists',
				$check_brand_exist )->message( __( '{field} has brand not available in configured brand plugin.',
				'wlrp-perfect-brand' ) );
		}
		$rule_validator->rule( 'required', $required_fields )->message( __( '{field} is required',
			'wlrp-perfect-brand' ) );
		$rule_validator->rule( 'isEmpty', $empty_check_fields )->message( __( '{field} is empty',
			'wlrp-perfect-brand' ) );
		if ( ! empty( $number_non_zero_field ) ) {
			$rule_validator->rule( 'numberGeZero',
				$number_non_zero_field )->message( __( '{field} must be greater than 0', 'wlrp-perfect-brand' ) );
		}
		$rule_validator->rule( 'number', [
			'conditions.*.options.qty',
		] )->message( __( '{field} accepts only numbers', 'wlrp-perfect-brand' ) );
		$rule_validator->rule( 'alphaNumWithUnderscore',
			[
				'conditions.*.options.operator',
				'conditions.*.options.condition',
			]
		)->message( __( '{field} accepts only letters,numbers and underscore', 'wlrp-perfect-brand' ) );
		if ( $rule_validator->validate() ) {
			return true;
		} else {
			return $rule_validator->errors();
		}

	}

	/**
	 * validate the conditional values
	 *
	 * @param          $field
	 * @param          $value
	 * @param   array  $params
	 * @param   array  $fields
	 *
	 * @return bool
	 * @throws Exception
	 */
	static function validateCleanHtml( $field, $value, array $params, array $fields ) {
		$html  = Woocommerce::getCleanHtml( $value );
		$value = str_replace( '&amp;', '&', $value );
		$html  = str_replace( '&amp;', '&', $html );
		if ( $html != $value ) {
			return false;
		}

		return true;
	}

	/**
	 * validate Input Text Html Tags
	 *
	 * @param          $field
	 * @param          $value
	 * @param   array  $params
	 * @param   array  $fields
	 *
	 * @return bool
	 */
	static function validateBasicHtmlTags( $field, $value, array $params, array $fields ) {
		$value        = stripslashes( $value );
		$value        = html_entity_decode( $value );
		$invalid_tags = [ "script", "iframe", "style" ];
		foreach ( $invalid_tags as $tag_name ) {
			$pattern = "#<\s*?$tag_name\b[^>]*>(.*?)</$tag_name\b[^>]*>#s";;
			preg_match( $pattern, $value, $matches );
			//script or iframe found
			if ( ! empty( $matches ) ) {
				return false;
			}
		}

		return true;
	}

	static function validateAlphaNumWithUnderscore( $field, $value, array $params, array $fields ) {
		return (bool) preg_match( '/^[a-zA-Z0-9_-]+$/', $value );
	}

	static function validateNumber( $field, $value, $params, $fields ) {
		$value = (int) $value;

		return preg_match( '/^([0-9])+$/i', $value );
	}

	static function validateNumberGeZero( $field, $value, array $params, array $fields ) {
		$value = (int) $value;
		if ( $value == 0 || empty( $value ) ) {
			return false;
		}

		return filter_var( $value, \FILTER_VALIDATE_INT ) !== false;
	}

	static function validateIsEmpty( $field, $value, array $params, array $fields ) {
		$status = false;
		if ( ! empty( $value ) ) {
			$status = true;
		}

		return $status;
	}

	static function checkIsBrandExist( $field, $value, array $params, array $fields ) {
		$status = true;
		if ( ! empty( $value ) && is_array( $value ) ) {
			$saved_brand_preference = get_option( 'wlrp_compatibility_choice' );
			if ( empty( $saved_brand_preference ) ) {
				return false;
			}
			foreach ( $value as $brand ) {
				$terms = get_terms( [
					'taxonomy'   => $saved_brand_preference,
					'name__like' => (string) $brand['label'],
					'hide_empty' => false,
					'fields'     => 'ids'
				] );
				if ( empty( $terms ) ) {
					$status = false;
					break;
				}
			}
		}

		return $status;
	}
}