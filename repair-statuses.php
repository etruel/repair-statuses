<?php
/*
Plugin Name: Repair statuses
Plugin URI: https://bitbucket.org/etruel/repair-statuses
Description: Read the info from a hosted csv file and allow the clients to see its repair status from a Wordpress page via ajax 
Version: 1.2
Author: esteban
Author URI: https://etruel.com
License: GPLv2
*/

/* 
Copyright (C) 2016 Esteban Truelsegaard

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

if ( ! defined( 'ABSPATH' ) ) exit;


// Plugin version
if ( ! defined('WPE_REPARA_VERSION' ) ) define('WPE_REPARA_VERSION', '1.2' ); 

if ( ! class_exists( 'REPARA' ) ) :

class REPARA {
	
	private static $instance = null;
	
	public static function getInstance() {
		if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
	}
	function __construct() {
		$this->setupGlobals();
		$this->includes();
		$this->loadTextDomain();
		
		add_action( 'plugins_loaded', array($this,'repara_git_updater') );
	}
	private function includes() {
		require_once REPARA_PLUGIN_DIR . 'includes/functions.php'; 
		do_action('REPARA_include_files');
		
	}
	function repara_git_updater() {
		if ( is_admin() && !class_exists( 'GPU_Controller' ) ) {
			require_once dirname( __FILE__ ) . '/git-plugin-updates/git-plugin-updates.php';
			add_action( 'plugins_loaded', 'GPU_Controller::get_instance', 20 );
		}
	}
	private function setupGlobals() {

		// Plugin Folder Path
		if (!defined('REPARA_PLUGIN_DIR')) {
			define('REPARA_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
		}

		// Plugin Folder URL
		if (!defined('REPARA_PLUGIN_URL')) {
			define('REPARA_PLUGIN_URL', plugin_dir_url(__FILE__));
		}

		// Plugin Root File
		if (!defined('REPARA_PLUGIN_FILE')) {
			define('REPARA_PLUGIN_FILE', __FILE__ );
		}
		
		// Plugin text domain
		if (!defined('REPARA_TEXT_DOMAIN')) {
			define('REPARA_TEXT_DOMAIN', 'REPARA' );
		}

	}
	public function loadTextDomain() {
		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters('repair-statuses_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'repair-statuses' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'repair-statuses', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/repair-statuses/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/repair-statuses/ folder
			load_textdomain( 'repair-statuses', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/repair-statuses/languages/ folder
			load_textdomain( 'repair-statuses', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'repair-statuses', false, $lang_dir );
		}
		
	}
}

endif; // End if class_exists check

$REPARA = null;
function getClassREPARA() {
	global $REPARA;
	if (is_null($REPARA)) {
		$REPARA = REPARA::getInstance();
	}
	return $REPARA;
}
getClassREPARA();
?>
