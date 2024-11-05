<?php

namespace Wlrp\App\Controllers\Admin;

use Wlrp\App\Helpers\AjaxCompatiblityCondition;
use Wlrp\App\Helpers\Input;
use Wlrp\App\Helpers\Woocommerce;

class Main {
	/**
	 * Method to add brands to the labels.
	 *
	 * @param   array  $json  Labels.
	 *
	 * @return array
	 */
	public static function registerLabels( $json ) {
		$json['common']['select']['select_custom_taxonomy'] = __( 'Select custom taxonomy', WLRP_TEXT_DOMAIN );
		$json['conditions']['brands']                       = [
			'name'                   => __( 'Brands', WLRP_TEXT_DOMAIN ),
			'condition'              => __( 'Taxonomy should be ', WLRP_TEXT_DOMAIN ),
			'select_custom_taxonomy' => __( 'Select Custom taxonomy', WLRP_TEXT_DOMAIN ),
			'value_condition'        => __( 'Taxonomy product in cart', WLRP_TEXT_DOMAIN ),
			'value'                  => __( 'Taxonomy quantity', WLRP_TEXT_DOMAIN ),
		];

		return $json;
	}

	/**
	 * Method to add brand condition to free campaigns.
	 *
	 * @param   array  $conditions  Conditions.
	 *
	 * @return array
	 */
	public static function appendFreeCampaignConditions( $conditions ) {
		if ( taxonomy_exists( 'pwb-brand' ) ) {
			$conditions['point_for_purchase']['Product']['options']['brands'] = __( 'Brands', WLRP_TEXT_DOMAIN );
		}

		return $conditions;
	}

	/**
	 * Method to add brand condition to pro campaigns.
	 *
	 * @param   array  $conditions  Conditions.
	 *
	 * @return array
	 */
	public static function appendProCampaignConditions( $conditions ) {
		if ( taxonomy_exists( 'pwb-brand' ) ) {
			$conditions['subtotal']['Product']['options']['brands'] = __( 'Brands', WLRP_TEXT_DOMAIN );
		}

		return $conditions;
	}

	/**
	 * Method to initiate function to fetch the condition of search result.
	 *
	 * @param   array  $data  Existing data.
	 *
	 * @return array
	 */
	public static function registerConditionData( $data ) {
		$method      = (string) Input::get( 'method', '' );
		$query       = (string) Input::get( 'q', '' );
		$method_name = 'ajax' . ucfirst( $method );
		if ( empty( $method ) || empty( $query ) ) {
			$data['success'] = false;
			$data['data']    = array(
				'message' => __( "Invalid method", 'wp-loyalty-rules' )
			);

			return $data;
		}
		$woocommerce_helper = new \Wlr\App\Helpers\Woocommerce();
		$ajax_condition     = AjaxCompatiblityCondition::getInstance();
		if ( $woocommerce_helper->isMethodExists( $ajax_condition, $method_name ) ) {

			$data['success'] = true;
			$data['data']    = $ajax_condition->$method_name();
		} else {
			$data['success'] = false;
			$data['data']    = array(
				'message' => __( "Method not found", 'wp-loyalty-rules' )
			);
		}

		return $data;
	}

	/**
	 * Method to include condiiton of Custom Taxonomy to existing conditions.
	 *
	 * @param $available_conditions
	 *
	 * @return array
	 */
	public static function addCondition( $available_conditions ) {
		if ( file_exists( WLRP_PLUGIN_PATH . 'App/Conditions/' ) ) {
			$conditions_list = array_slice( scandir( WLRP_PLUGIN_PATH . 'App/Conditions/' ), 2 );
			if ( ! empty( $conditions_list ) ) {
				foreach ( $conditions_list as $condition ) {
					$class_name = basename( $condition, '.php' );
					if ( ! in_array( $class_name, [ 'Base' ] ) ) {
						$condition_class_name = 'Wlrp\App\Conditions\\' . $class_name;
						if ( class_exists( $condition_class_name ) ) {
							$condition_object = new $condition_class_name();
							if ( $condition_object instanceof \Wlrp\App\Conditions\Base ) {
								$rule_name = $condition_object->name();
								if ( ! empty( $rule_name ) ) {
									$available_conditions[ $rule_name ] = [
										'object'       => $condition_object,
										'label'        => $condition_object->label,
										'group'        => $condition_object->group,
										'extra_params' => $condition_object->extra_params
									];
								}
							}
						}
					}
				}
			}
		}

		return $available_conditions;
	}
}