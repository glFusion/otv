<?php
/**
 * Configuration defaults for the KeyShare secure value sharing plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.1.2
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

if (!defined ('GVERSION')) {
    die('This file can not be used on its own!');
}
use KeyShare\Config;
$pi_name = Config::PI_NAME;

/*
 * Default settings.
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 */
/** @var global config data */
global $keyshareConfigData;
$keyshareConfigData = array(
    array(
        'name' => 'sg_main',
        'default_value' => NULL,
        'type' => 'subgroup',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => true,
        'group' => $pi_name,
    ),
    array(
        'name' => 'fs_main',
        'default_value' => NULL,
        'type' => 'fieldset',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => true,
        'group' => $pi_name,
    ),
    array(
        'name' => 'hidemenu',
        'default_value' => '0',
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 0,
        'sort' => 10,
        'set' => true,
        'group' => $pi_name,
    ),
    array(
        'name' => 'expire_after',
        'default_value' => '7',
        'type' => 'text',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 0,
        'sort' => 20,
        'set' => true,
        'group' => $pi_name,
    ),
    array(
        'name' => 'del_after_view',
        'default_value' => '1',
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 0,
        'sort' => 30,
        'set' => true,
        'group' => $pi_name,
    ),
);


/**
 * Initialize the plugin configuration.
 * Creates the database entries for the configuation if they don't already exist.
 *
 * @return  boolean     true: success; false: an error occurred
 */
function plugin_initconfig_keyshare()
{
    global $_CONF, $keyshareConfigData;

    $pi_name = Config::PI_NAME;
    $c = \config::get_instance();
    if (!$c->group_exists($pi_name)) {
        USES_lib_install();
        foreach ($keyshareConfigData AS $cfgItem) {
            _addConfigItem($cfgItem);
        }
    } else {
        COM_errorLog(
            __FUNCTION__ . ': ' . Config::PI_NAME . ' config group already exists'
        );
    }
    return true;
}
