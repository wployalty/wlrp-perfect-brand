<?php
/**
 * @author      Wployalty (Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlrp\App\Helpers;

defined( 'ABSPATH' ) or die;

class Util {
	/**
	 * render template.
	 *
	 * @param   string  $file     File path.
	 * @param   array   $data     Template data.
	 * @param   bool    $display  Display or not.
	 *
	 * @return string|void
	 */
	public static function renderTemplate( string $file, array $data = [], bool $display = true ) {
		$content = '';
		if ( file_exists( $file ) ) {
			ob_start();
			extract( $data );
			include $file;
			$content = ob_get_clean();
		}
		if ( $display ) {
			echo $content;
		} else {
			return $content;
		}
	}
}