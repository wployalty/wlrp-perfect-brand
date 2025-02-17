<?php
/**
 * @author      Wployalty (Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlrp\App\Helpers;

defined( 'ABSPATH' ) or die();

class Compatibility {
	public static function check( $allow_exit = false ) {
		if ( ! self::isPHPCompatible() ) {
			// translators: First %s will replace plugin name, Second %s replace minimum PHP version
			$message = sprintf( __( '%1$s requires minimum PHP version %2$s', 'wlrp-perfect-brand' ), WLRP_PLUGIN_NAME,
				WLRP_MINIMUM_PHP_VERSION );
			$allow_exit ? die( esc_html( $message ) ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}
		if ( ! self::isWordPressCompatible() ) {
			// translators: First %s will replace plugin name, Second %s replace a minimum WordPress version
			$message = sprintf( __( '%1$s requires minimum WordPress version %2$s', 'wlrp-perfect-brand' ), WLRP_PLUGIN_NAME,
				WLRP_MINIMUM_WP_VERSION );
			$allow_exit ? die( esc_html( $message ) ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}
		if ( ! self::isWooCompatible() ) {
			// translators: First %s will replace plugin name, Second %s replace minimum WooCommerce version
			$message = sprintf( __( '%1$s requires minimum Woocommerce version %2$s', 'wlrp-perfect-brand' ),
				WLRP_PLUGIN_NAME, WLRP_MINIMUM_WC_VERSION );
			$allow_exit ? exit( $message ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}
		if ( ! self::isWPLoyaltyCompatible() ) {
			// translators: First %s will replace plugin name, Second %s replace minimum WPLoyalty version
			$message = sprintf( __( '%1$s requires minimum WPLoyalty version %2$s', 'wlrp-perfect-brand' ), WLRP_PLUGIN_NAME,
				WLRP_MINIMUM_WLR_VERSION );
			$allow_exit ? exit( $message ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}
		if ( ! self::isWoocommerceActive() ) {
			// translators: %s will replace plugin name
			$message = sprintf( __( 'Woocommerce should be active in order to use %s', 'wlrp-perfect-brand' ),
				WLRP_PLUGIN_NAME );
			$allow_exit ? exit( $message ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}
		if ( ! self::isWPLoyaltyActive() ) {
			// translators: %s will replace plugin name
			$message = sprintf( __( 'WPLoyalty should be active in order to use %s', 'wlrp-perfect-brand' ),
				WLRP_PLUGIN_NAME );
			$allow_exit ? exit( $message ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}

		return true;
	}

	/**
	 * Check PHP version for compatibility.
	 *
	 * @return bool
	 */
	protected static function isPHPCompatible() {
		return (int) version_compare( PHP_VERSION, WLRP_MINIMUM_PHP_VERSION, '>=' ) > 0;
	}

	/**
	 * Check WordPress version for compatibility.
	 *
	 * @return bool
	 */
	protected static function isWordPressCompatible() {
		return (int) version_compare( get_bloginfo( 'version' ), WLRP_MINIMUM_WP_VERSION, '>=' ) > 0;
	}

	/**
	 * Check Woocommerce version for compatibility.
	 *
	 * @return bool
	 */
	protected static function isWooCompatible() {
		$woo_version = self::getWooVersion();

		return (int) version_compare( $woo_version, WLRP_MINIMUM_WC_VERSION, '>=' ) > 0;
	}

	/**
	 * Check if Woocommerce is active.
	 *
	 * @return bool Returns true if active else false.
	 */
	protected static function isWoocommerceActive() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'woocommerce/woocommerce.php',
				$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}

	/**
	 * Check WPLoyalty version for compatibility.
	 *
	 * @return bool
	 */
	protected static function isWPLoyaltyCompatible() {
		$wlr_version = self::getWLRVersion();

		return (int) version_compare( $wlr_version, WLRP_MINIMUM_WLR_VERSION, '>=' ) > 0;
	}

	/**
	 *
	 */
	protected static function isWPLoyaltyActive() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'wp-loyalty-rules/wp-loyalty-rules.php',
				$active_plugins ) || array_key_exists( 'wp-loyalty-rules/wp-loyalty-rules.php', $active_plugins );
	}

	/**
	 * Add admin notice.
	 *
	 * @param string $message Message.
	 * @param string $status Status.
	 *
	 * @return void
	 */
	public static function adminNotice( $message, $status = 'success' ) {
		add_action( 'admin_notices', function () use ( $message, $status ) {
			?>
            <div class='notice notice-<?php echo esc_attr( $status ); ?>'>
                <p><?php echo wp_kses_post( $message ); ?></p>
            </div>
			<?php
		}, 1 );
	}

	/**
	 * Get Woocommerce version.
	 *
	 * @return string
	 */
	public static function getWooVersion() {
		if ( defined( 'WC_VERSION' ) ) {
			return WC_VERSION;
		}
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_folder = get_plugins( '/woocommerce' );

		return isset( $plugin_folder['woocommerce.php']['Version'] ) ? $plugin_folder['woocommerce.php']['Version'] : '1.0.0';
	}

	/**
	 * Get WPLoyalty version.
	 *
	 * @return string
	 */
	protected static function getWLRVersion() {
		if ( defined( 'WLR_PLUGIN_VERSION' ) ) {
			return WLR_PLUGIN_VERSION;
		}
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_file = 'wp-loyalty-rules/wp-loyalty-rules.php';
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			return '1.0.0';
		}
		$plugin_folder = get_plugins( '/wp-loyalty-rules' );

		return isset( $plugin_folder['wp-loyalty-rules.php']['Version'] ) ? $plugin_folder['wp-loyalty-rules.php']['Version'] : '1.0.0';
	}
}