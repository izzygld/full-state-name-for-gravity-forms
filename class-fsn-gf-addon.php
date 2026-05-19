<?php
/**
 * Main Full State Name for Gravity Forms Addon Class
 *
 * this is where all the addon magic happens
 * extends GFAddOn to hook into merge tag filtering
 *
 * @package FSN_GF
 */

// dont let anyone access this directly
defined( 'ABSPATH' ) || exit;

/**
 * FSN_GF_Addon class
 *
 * followin the GFAddOn pattern from the gf docs
 * this handles registering the merge tag expander
 */
class FSN_GF_Addon extends GFAddOn {

    /**
     * holds an instance of this class, if we got one
     *
     * @var FSN_GF_Addon|null
     */
    private static $_instance = null;

    /**
     * addon version number
     *
     * @var string
     */
    protected $_version = FSN_GF_VERSION;

    /**
     * minimum gf version we need to work
     *
     * @var string
     */
    protected $_min_gravityforms_version = FSN_GF_MIN_GF_VERSION;

    /**
     * url-safe addon slug, gotta be max 33 chars
     *
     * @var string
     */
    protected $_slug = 'full-state-name-for-gravity-forms';

    /**
     * path to plugin from the plugins folder
     *
     * @var string
     */
    protected $_path = 'full-state-name-for-gravity-forms/full-state-name-for-gravity-forms.php';

    /**
     * full path to the main plugin file
     *
     * @var string
     */
    protected $_full_path = __FILE__;

    /**
     * the full title of our addon
     *
     * @var string
     */
    protected $_title = 'Full State Name for Gravity Forms';

    /**
     * shorter title for menus n stuff
     *
     * @var string
     */
    protected $_short_title = 'Full State Name';

    /**
     * our expander instance that handles the merge tag filter
     *
     * @var FSN_GF_Expander
     */
    public $expander;

    /**
     * gets the singleton instance of this class
     * creates it if it dont exist yet
     *
     * @return FSN_GF_Addon
     */
    public static function get_instance() {
        if ( null === self::$_instance ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * minimum requirements to run this addon
     * checkin php version and gf version
     *
     * @return array
     */
    public function minimum_requirements() {
        return array(
            'gravityforms' => array(
                'version' => $this->_min_gravityforms_version,
            ),
            'php'          => array(
                'version' => '7.4',
            ),
        );
    }

    /**
     * runs before wordpress init kicks off
     * settin up our handler instances early
     *
     * @return void
     */
    public function pre_init() {
        parent::pre_init();

        // spinnin up our expander handler
        $this->expander = new FSN_GF_Expander();
    }

    /**
     * init method that runs on all pages
     * hookin up our merge tag filter here
     *
     * @return void
     */
    public function init() {
        parent::init();

        // hookin up the merge tag expansion for address state sub-field
        $this->expander->hookup();
    }
}
