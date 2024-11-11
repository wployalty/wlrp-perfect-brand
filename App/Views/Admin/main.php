<?php
/**
 * @author      Wployalty (Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */
defined( "ABSPATH" ) or die();
$saved_choice = get_option( 'wlrp_compatability_choice', '' );
?>
<div id="wlrp-main-page">
    <div class="wlrp-main-header">
        <h1><?php echo esc_html( WLRP_PLUGIN_NAME ); ?></h1>
        <div><b><?php echo esc_html( "v" . WLRP_PLUGIN_VERSION ); ?></b></div>
    </div>
    <div class="wlrp-admin-main">
        <div class="wlrp-admin-nav">
            <a class="active-nav"
               href="<?php echo esc_url( admin_url( "admin.php?" . http_build_query( array(
					   "page" => WLRP_PLUGIN_SLUG,
					   "view" => 'settings'
				   ) ) ) ) ?>"
            ><?php esc_html_e( "Settings", "wlrp-perfect-brand" ); ?></a>
        </div>
    </div>
    <div class="wlrp-parent">
        <div class="wlrp-body-content">
            <div class="wlrp-body-active-content active-content">
                <form id="wlrp-save-settings">
                    <div class="button-container">
                        <a class="wlrp-button" target="_self"
                           href="<?php echo isset( $app_url ) ? esc_url( $app_url ) : '#'; ?>"><i
                                    class="wlrpf-back">&nbsp;</i>
							<?php esc_html_e( 'Back to WPLoyalty', 'wlrp-perfect-brand' ); ?></a>
                        <button type="submit" class="wlrp-button"><i
                                    class="wlrpf-save-success">&nbsp;</i><?php esc_html_e( 'Save',
								'wlrp-perfect-brand' ) ?></button>
                    </div>
                    <div class="wlrp-notice"
                         style="background-color: #f4cd76;border-radius: 10px;border-color: #f5c6cb;margin: 12px 0;padding: 14px;font-weight: bold;">
                        <h3><i class="wlrpf-error">&nbsp;</i><?php esc_html_e( 'IMPORTANT:', 'wlrp-perfect-brand' ); ?>
                        </h3>
                        <ul style="font-style: italic;">
                            <li style="text-indent: 50px;font-size: 14px;"><?php esc_html_e( 'Brands compatability won\'t work for both plugins at once. It works only for one plugin & the condition should be configured in the campaign.',
									'wlrp-perfect-brand' ); ?></li>
                        </ul>
                    </div>
                    <div class="wlrp-form-content">
                        <label for="wlrp-compatability-choice"><?php esc_html_e( 'Choose the plugin:',
								'wlrp-perfect-brand' ); ?></label>
                        <select id="wlrp-compatability-choice" name="wlrp-compatability-choice">
                            <option value=""><?php esc_html_e( 'Select plugin',
									'wlrp-perfect-brand' ); ?></option>
                            <option value="pwb-brand"<?php selected( $saved_choice,
								'pwb-brand' ); ?>><?php esc_html_e( 'Perfect Brands for Woocommerce',
									'wlrp-perfect-brand' ); ?></option>
                            <option value="product_brand"<?php selected( $saved_choice,
								'product_brand' ); ?>><?php esc_html_e( 'Woocommerce Brands',
									'wlrp-perfect-brand' ); ?></option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="wlr-toast-box"></div>
</div>
