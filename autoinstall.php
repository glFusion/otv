<?php
/**
 * Automatic installation functions for the KeyShare plugin.
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
    die ('This file can not be used on its own.');
}

global $_DB_dbms;

require_once __DIR__ . '/functions.inc';
require_once __DIR__ . '/sql/mysql_install.php';
use KeyShare\Config;

$ucPI_NAME = ucfirst(Config::PI_NAME);

// +--------------------------------------------------------------------------+
// | Plugin installation options                                              |
// +--------------------------------------------------------------------------+

$INSTALL_plugin[Config::PI_NAME] = array(
    'installer' => array(
        'type' => 'installer',
        'version' => '1',
        'mode' => 'install',
    ),
    'plugin' => array(
        'type' => 'plugin',
        'name' => Config::get('pi_name'),
        'ver' => Config::get('pi_version'),
        'gl_ver' => Config::get('gl_version'),
        'url' => Config::get('pi_url'),
        'display' => Config::get('pi_display_name'),
    ),
    array(
        'type' => 'table',
        'table' => $_TABLES['keyshare_secrets'],
        'sql' => $_SQL['keyshare_secrets'],
    ),
    array(
        'type' => 'feature',
        'feature' => Config::PI_NAME . '.admin',
        'desc' => 'Full admin access to ' . $ucPI_NAME,
        'variable' => 'admin_feature_id',
    ),
    array(
        'type' => 'feature',
        'feature' => Config::PI_NAME . '.submit',
        'desc' => 'Access to create values for ' . $ucPI_NAME,
        'variable' => 'create_feature_id',
    ),
    array(
        'type' => 'feature',
        'feature' => Config::PI_NAME . '.view',
        'desc' => 'Access to view values for ' . $ucPI_NAME,
        'variable' => 'view_feature_id',
    ),
    array(
        'type' => 'mapping',
        'findgroup' => 'Root',
        'feature' => 'admin_feature_id',
        'log' => 'Adding feature to the admin group',
    ),
    array(
        'type' => 'mapping',
        'findgroup' => 'Logged-In Users',
        'feature' => 'create_feature_id',
        'log' => 'Adding create feature to logged-in users',
    ),
    array(
        'type' => 'mapping',
        'findgroup' => 'All Users',
        'feature' => 'view_feature_id',
        'log' => 'Adding view feature to all users',
    ),
);


/**
 * Puts the datastructures for this plugin into the glFusion database.
 * Note: Corresponding uninstall routine is in functions.inc.
 * @return   boolean True if successful False otherwise
 */
function plugin_install_keyshare()
{
    global $INSTALL_plugin;

    COM_errorLog("Attempting to install the " . Config::get('pi_display_name') . " plugin", 1);
    $ret = INSTALLER_install($INSTALL_plugin[Config::get('pi_name')]);
    if ($ret > 0) {
        return false;
    }
    return true;
}


/**
 * Loads the configuration records for the Online Config Manager.
 *
 * @return  boolean     true = proceed with install, false = an error occured
 */
function plugin_load_configuration_keyshare()
{
    global $_CONF;

    require_once __DIR__ . '/install_defaults.php';
    return plugin_initconfig_keyshare();
}


/**
 * Automatic uninstall function for plugins.
 *
 * @return   array
 *
 * This code is automatically uninstalling the plugin.
 * It passes an array to the core code function that removes
 * tables, groups, features and php blocks from the tables.
 * Additionally, this code can perform special actions that cannot be
 * foreseen by the core code (interactions with other plugins for example)
 */
function plugin_autouninstall_keyshare()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(
            Config::PI_NAME . '_secrets',
        ),
        /* give the full name of the group, as in the db */
        'groups' => array(
        ),
        /* give the full name of the feature, as in the db */
        'features' => array(
            Config::PI_NAME . '.admin',
            Config::PI_NAME . '.submit',
            Config::PI_NAME . '.view',
        ),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(
        ),
        /* give all vars with their name */
        'vars'=> array()
    );
    return $out;
}
