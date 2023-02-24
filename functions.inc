<?php
/**
 * glFusion API functions for the KeyShare secure value sharing plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

if (!defined ('GVERSION')) {
    die('This file can not be used on its own.');
}

spl_autoload_register(function ($class)
{
    // project-specific namespace prefix
    $prefix = 'KeyShare\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/classes/';

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.class.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
use KeyShare\Config;
use glFusion\Log\Log;
Log::config('keyshare',
    array(
      'type'  => 'file',
      'path'  => $_CONF['path_log'],
      'file'  => 'keyshare.log',
      'level' => Log::INFO,
    )
);

// Load the site's language file, falling back to English.
$langpath = __DIR__ . '/language';
$langfile = "{$_CONF['language']}.php";
if (!is_file("$langpath/$langfile")) {
    $langfile = 'english_utf-8.php';
}
/** Include the proper language file */
require_once "$langpath/$langfile";

if (is_file("$langpath/custom/$langfile")) {
    /** Include the custom language file, if any */
    include_once "$langpath/custom/$langfile";
}

require_once __DIR__ . '/keyshare.php';
if (file_exists(__DIR__ . '/config.php')) {
    include_once __DIR__ . '/config.php';
}

// +---------------------------------------------------------------------------+
// | glFusion Plugin API Implementations                                       |
// +---------------------------------------------------------------------------+

/**
* Returns the items for this plugin that should appear on the main menu.
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*/
function plugin_getmenuitems_keyshare()
{
    global $_CONF;

    if (
        Config::get('hidemenu') == 1 ||
        (
            COM_isAnonUser() && $_CONF['loginrequired']
        )
    ) {
        return false;
    }
    return array(
        Config::get('pi_display_name') . ' <i class="uk-icon-lock"></i>' => Config::get('url') . '/index.php',
    );
}


/**
 * Check if this is a plugin moderator.
 * The Moderator can edit election.
 *
 * @return  boolean     True for admins, False for regular users
 */
function plugin_ismoderator_keyshare()
{
    static $isMod = NULL;
    if ($isMod === NULL) {
        $isMod = SEC_hasRights('admin,edit', 'OR');
    }
    return $isMod;
}


/**
 * This will put an option for election in the command and control block.
 */
function plugin_cclabel_keyshare()
{
    global $LANG_KEYSHARE;

    if (plugin_ismoderator_keyshare()) {
        return array(
            $LANG_KEYSHARE['display_name'],
            Config::get('admin_url') . '/index.php',
            plugin_geticon_keyshare()
        );
    }
    return false;
}


/**
 * Returns the administrative option for this plugin.
 */
function plugin_getadminoption_keyshare()
{
    global $_CONF, $LANG_KEYSHARE;

    if (plugin_ismoderator_keyshare()) {
        return array(
            $LANG_KEYSHARE['display_name'],
            Config::get('admin_url') . '/index.php',
        );
    }
}


/**
 * Return the current version of code.
 * Used in the Plugin Editor to show the registered version and code version
 */
function plugin_chkVersion_keyshare()
{
    return Config::get('pi_version');
}


/**
 * Returns the URL of the plugin's icon.
 *
 * @return   string      URL of the icon
 */
function plugin_geticon_keyshare()
{
    return Config::get('url') . '/images/keyshare.png';
}


/**
* Update the Election plugin
*
* @return   integer     Number of message to display (true = generic success msg)
*/
function plugin_upgrade_keyshare()
{
    require_once __DIR__ . '/upgrade.php';
    return KEYSHARE_upgrade();
}


function plugin_getconfigelementhelp_keyshare($type, $option, $doclang = 'english' )
{
    global $_CONF;

    $retval = '';
    if ($doclang !== 'english') {
        $langs = array($doclang, 'english');
    } else {
        $langs = array($doclang);
    }
    foreach ($langs as $doclang) {
        $file = Config::PI_NAME . '/docs/' . $doclang . '/config.html';
        if (file_exists($_CONF['path_html'] . '/' . $file)) {
            $retval = $_CONF['site_url'] . '/' . $file;
            break;
        }
    }
    return array($retval,0);
}


function plugin_runscheduledtask_keyshare()
{
    KeyShare\Models\Secret::expireStale();
}


/**
 * Get additional Javascript files to import in the header.
 *
 * @return  array   Array of filenames to import
 */
function plugin_getheaderjs_keyshare()
{
    $files = array(
        __DIR__ . '/js/util.js',  // uikit v2/v3 compatible notifications
    );
    return $files;
}
