<?php
/**
 * Plugin Name: Full State Name for Gravity Forms
 * Plugin URI: https://github.com/izzygld/full-state-name-for-gravity-forms
 * Description: Ensures the Address "State / Province" sub-field (input X.4) always renders as the full state name (e.g. "New Jersey") rather than a 2-letter abbreviation (e.g. "NJ") in merge tags such as {all_fields}, {Address (State / Province):X.4}, notifications, confirmations, and URL query strings.
 * Version: 1.0.1
 * Author: izzygld
 * Author URI: https://github.com/izzygld
 * Text Domain: full-state-name-for-gravity-forms
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// dont let ppl access this file directly thats bad
defined( 'ABSPATH' ) || exit;

// settin up all our constant values for the plugin
define( 'FSN_GF_VERSION', '1.0.0' );
define( 'FSN_GF_MIN_GF_VERSION', '2.5' );
define( 'FSN_GF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FSN_GF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * checkin if gravity forms is installed on the site
 * if its not there we gotta show an error and turn off the plugin
 */
function fsn_gf_check_gf_installed() {
    // lookin for gravity forms class to see if its active
    if ( ! class_exists( 'GFForms' ) ) {
        add_action( 'admin_notices', 'fsn_gf_show_missing_gf_error' );
        add_action( 'admin_init', 'fsn_gf_turn_off_plugin' );
        return false;
    }
    return true;
}

/**
 * showin the admin error when gravity forms aint there
 * this pops up at the top of the admin area
 */
function fsn_gf_show_missing_gf_error() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <strong><?php esc_html_e( 'Full State Name for Gravity Forms', 'full-state-name-for-gravity-forms' ); ?>:</strong>
            <?php
            printf(
                /* translators: %s: Gravity Forms plugin name */
                esc_html__( 'This plugin requires %s to be installed and activated. The plugin has been deactivated.', 'full-state-name-for-gravity-forms' ),
                '<strong>Gravity Forms</strong>'
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * turnin off the plugin if gravity forms isnt there
 * we dont want it runnin without its dependency ya know
 */
function fsn_gf_turn_off_plugin() {
    // only do this if were in admin and user can activate plugins
    if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );

        // get rid of the "Plugin activated" message cuz its misleading
        if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }
    }
}

// runnin our dependency check early before gf loads
add_action( 'plugins_loaded', 'fsn_gf_check_gf_installed', 1 );

/**
 * this class handles loadin up the whole plugin
 * its like the main entry point that gets everything started
 * followin the gf addon framework patterns from their docs
 */
class FSN_GF_Bootstrap {

    /**
     * loads up da addon when gravity forms is ready to go
     * this is where all the magic starts happenin
     *
     * @return void
     */
    public static function fire_it_up() {
        // makin sure the gf addon framework stuff is available
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        // bringin in the addon framework
        GFForms::include_addon_framework();

        // loadin up our helper classes
        require_once FSN_GF_PLUGIN_PATH . 'includes/class-state-code-map.php';
        require_once FSN_GF_PLUGIN_PATH . 'includes/class-state-name-expander.php';
        require_once FSN_GF_PLUGIN_PATH . 'class-fsn-gf-addon.php';

        // registerin the addon with gravity forms
        GFAddOn::register( 'FSN_GF_Addon' );
    }

    /**
     * grabbin da addon instance so we can use it elsewhere
     *
     * @return FSN_GF_Addon|null
     */
    public static function grab_da_instance() {
        return class_exists( 'FSN_GF_Addon' ) ? FSN_GF_Addon::get_instance() : null;
    }
}

// hookin into gravity forms load event to fire up our addon
add_action( 'gform_loaded', array( 'FSN_GF_Bootstrap', 'fire_it_up' ), 5 );
