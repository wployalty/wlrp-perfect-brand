<?php

namespace Wlrp\App\Controllers\Admin;

defined( 'ABSPATH' ) or die;

use Wlrp\App\Helpers\AjaxCompatiblityCondition;
use Wlrp\App\Helpers\Input;
use Wlrp\App\Helpers\Util;
use Wlrp\App\Helpers\Woocommerce;

class Main {
	/**
	 * Method to add admin menu.
	 *
	 * @return void
	 */
	public static function addAdminMenu() {
		if ( Woocommerce::hasAdminPrivilege() ) {
			add_menu_page( __( "WPLoyalty: Brands compatibility", 'wlrp-perfect-brand' ),
				__( "WPLoyalty: Brands compatibility", 'wlrp-perfect-brand' ), "manage_woocommerce", WLRP_PLUGIN_SLUG,[self::class,'addMenuPage'], 'dashicons-megaphone', 59 );
		}
	}

	/**
	 * Method to add menu page.
	 *
	 * @return void
	 */
	public static function addMenuPage() {
		$params = [
			'app_url' => admin_url( 'admin.php?' . http_build_query( [ 'page' => WLR_PLUGIN_SLUG ] ) ) . '#/apps',
		];
		$path   = WLRP_PLUGIN_PATH . 'App/Views/Admin/main.php';
		$path   = apply_filters( 'wlrp_admin_main_template', $path );
		Util::renderTemplate( $path, $params, true );
	}

	/**
	 * Method to enqueue assets.
	 *
	 * @return void
	 */
	public static function adminAssets() {
		if ( ! Woocommerce::hasAdminPrivilege() || Input::get( 'page' ) != WLRP_PLUGIN_SLUG ) {
			return;
		}

		$suffix = '';
		if ( defined( 'SCRIPT_DEBUG' ) ) {
			$suffix = SCRIPT_DEBUG ? '' : '.min';
		}
		remove_all_actions( 'admin_notices' );
		wp_enqueue_style( WLRP_PLUGIN_SLUG . '-main-style', WLRP_PLUGIN_URL . 'Assets/Admin/Css/wlrp-main.css', [],
			WLRP_PLUGIN_VERSION . '&t=' . time() );
		wp_enqueue_style( WLRP_PLUGIN_SLUG . '-wlrp-font', WLRP_PLUGIN_URL . 'Assets/Admin/Css/wlrp-fonts.css', [],
			WLRP_PLUGIN_VERSION . '&t=' . time() );
		wp_enqueue_style( WLRP_PLUGIN_SLUG . '-alertify',
			WLRP_PLUGIN_URL . 'Assets/Admin/Css/alertify.css', [], WLRP_PLUGIN_URL );
		wp_enqueue_script( WLRP_PLUGIN_SLUG . '-alertify', WLRP_PLUGIN_URL . 'Assets/Admin/Js/alertify.js',
			[], WLRP_PLUGIN_URL . '&t=' . time() );
		wp_enqueue_script(
			WLRP_PLUGIN_SLUG . '-main',
			WLRP_PLUGIN_URL . 'Assets/Admin/Js/wlrp-main.js',
			[ 'jquery' ],
			WLRP_PLUGIN_VERSION,
			true
		);
		wp_localize_script(
			WLRP_PLUGIN_SLUG . '-main',
			'wlrp_localize_data',
			[
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'save_nonce' => Woocommerce::createNonce( 'wlrp-save-settings-nonce' )
			]
		);
	}

	/**
	 * Method to hide the admin menu.
	 *
	 * @return void
	 */
	public static function hideAdminMenu() {
		?>
        <style>
            #toplevel_page_wlrp-perfect-brand {
                display: none !important;
            }
        </style>
		<?php
	}

	/**
	 * Method to add brands to the labels.
	 *
	 * @param   array  $json  Labels.
	 *
	 * @return array
	 */
	public static function registerLabels( $json ) {
        if(empty($json) || !is_array($json)){
            return $json;
        }
		$json['common']['select']['select_custom_taxonomy'] = __( 'Select custom taxonomy', 'wlrp-perfect-brand' );
		$json['conditions']['brands']                       = [
			'name'                   => __( 'Brands', 'wlrp-perfect-brand' ),
			'condition'              => __( 'Taxonomy should be ', 'wlrp-perfect-brand' ),
			'select_custom_taxonomy' => __( 'Select Custom taxonomy', 'wlrp-perfect-brand' ),
			'value_condition'        => __( 'Taxonomy product in cart', 'wlrp-perfect-brand' ),
			'value'                  => __( 'Taxonomy quantity', 'wlrp-perfect-brand' ),
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
		if ( !empty($conditions) && is_array($conditions) && taxonomy_exists( get_option( 'wlrp_compatibility_choice' ) ) && Woocommerce::isParentPluginEnabled( get_option( 'wlrp_compatibility_choice' ) ) ) {
			$conditions['point_for_purchase']['Product']['options']['brands'] = __( 'Brands', 'wlrp-perfect-brand' );
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
		if ( ! apply_filters( 'wlr_is_pro', false ) ) {
			return $conditions;
		}
		if ( !empty($conditions) && is_array($conditions) && taxonomy_exists( get_option( 'wlrp_compatibility_choice' ) ) && Woocommerce::isParentPluginEnabled( get_option( 'wlrp_compatibility_choice' ) ) ) {
			$conditions['subtotal']['Product']['options']['brands'] = __( 'Brands', 'wlrp-perfect-brand' );
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
			$data['data']    = [
				'message' => __( "Invalid method", 'wlrp-perfect-brand' )
			];

			return $data;
		}

		$ajax_condition     = AjaxCompatiblityCondition::getInstance();
		if ( Woocommerce::isMethodExists( $ajax_condition, $method_name ) ) {
			$data['success'] = true;
			$data['data']    = $ajax_condition->$method_name();
		} else {
			$data['success'] = false;
			$data['data']    = [
				'message' => __( 'Method not found', 'wlrp-perfect-brand' )
			];
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


	/**
	 * Ajax callback method to save settings.
	 *
	 * @return void
	 */
	public static function saveSettings() {
		$wlrp_nonce = Input::get( 'wlrp_nonce', '' );
		if ( ! Woocommerce::verifyNonce( $wlrp_nonce, 'wlrp-save-settings-nonce' ) ) {
            wp_send_json_error(['message' => __( 'Basic validation failed', 'wlrp-perfect-brand' )]);
		}
		try {
			$value = sanitize_text_field( Input::get( 'wlrp-compatibility-choice' ) );
			if ( empty( $value ) ) {
				wp_send_json_error(['message' => __( 'Select plugin to enable compatibility!', 'wlrp-perfect-brand' )]);
			}
			if ( ! Woocommerce::isParentPluginEnabled( $value ) ) {
				update_option( 'wlrp_compatibility_choice', '' );
				wp_send_json_error(['message' => __( 'Please activate the selected plugin!', 'wlrp-perfect-brand' )]);
			}
			if ( update_option( 'wlrp_compatibility_choice', $value ) ) {
				wp_send_json_success(['message' => __( 'Settings saved successfully!', 'wlrp-perfect-brand' )]);
			}
			wp_send_json_error(['message' => __( 'Something went wrong!', 'wlrp-perfect-brand' )]);
		} catch ( \Exception $e ) {
			wp_send_json_error(['message' =>  $e->getMessage()]);
		}
	}
}