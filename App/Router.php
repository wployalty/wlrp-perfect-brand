<?php

namespace Wlrp\App;

defined( 'ABSPATH' ) or die;

use Wlrp\App\Controllers\Admin\Main;
use Wlrp\App\Helpers\AjaxCompatiblityCondition;

class Router {
	/**
	 * Init action and filters.
	 *
	 * @return void
	 */
	public static function init() {
		if ( is_admin() ) {
			add_action( 'admin_menu', [ Main::class, 'addAdminMenu' ], 10 );
			add_action( 'admin_footer', [ Main::class, 'hideAdminMenu' ], 10 );
			add_action( 'admin_enqueue_scripts', [ Main::class, 'adminAssets' ], 10 );
			//Add Brands to condition list
			add_filter( 'wlr_action_conditions', [ Main::class, 'appendFreeCampaignConditions' ], 10, 1 );
			add_filter( 'wlr_pro_conditions', [ Main::class, 'appendProCampaignConditions' ], 10, 1 );
			add_action( 'wp_ajax_wlrp_save_settings', [ Main::class, 'saveSettings' ] );
			add_filter( 'wlr_before_save_campaign_validation', [ Main::class, 'validateCampaign' ] );
		}
		add_filter( 'wlr_condition_class_loading', [ Main::class, 'registerConditionData' ], 10, 1 );
		add_filter( 'wlr_available_conditions', [ Main::class, 'addCondition' ] );
	}
}