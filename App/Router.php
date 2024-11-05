<?php

namespace Wlrp\App;

use Wlrp\App\Controllers\Admin\Main;
use Wlrp\App\Helpers\AjaxCompatiblityCondition;

class Router {
	public static function init() {
		//Add label
		add_filter( 'wlr_plugin_labels', [ Main::class, 'registerLabels' ], 10, 1 );
		//Add Brands to condition list
		add_filter( 'wlr_action_conditions', [ Main::class, 'appendFreeCampaignConditions' ], 10, 1 );
		add_filter( 'wlr_pro_conditions', [ Main::class, 'appendProCampaignConditions' ], 10, 1 );

		add_filter( 'wlr_condition_class_loading', [ Main::class, 'registerConditionData' ], 10, 1 );
		add_filter( 'wlr_available_conditions', [ Main::class, 'addCondition' ] );
	}
}