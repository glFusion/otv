<?php
/**
 * Global configuration items for the KeyShare plugin.
 * These are either static items, such as the plugin name and table
 * definitions, or are items that don't lend themselves well to the
 * glFusion configuration system, such as allowed file types.
 *
 * @copyright   Copyright (c) 2022 The following authors:
 * @author      Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

use KeyShare\Config;
Config::set('pi_version', '0.0.1');
Config::set('gl_version', '2.0.0');

// Add to $_TABLES array the tables your plugin uses
global $_DB_table_prefix;
$_TABLES['keyshare_secrets']    = $_DB_table_prefix . 'keyshare_secrets';
