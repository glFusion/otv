<?php
/**
 * English Language File for the KeyShare secure value sharing plugin.
 *
 * @license GNU General Public License version 2 or later
 *     http://www.opensource.org/licenses/gpl-license.php
 *
 *  Copyright (C) 2008-2018 by the following authors:
 *   Mark R. Evans   mark AT glfusion DOT org
 *
 *  Based on prior work Copyright (C) 2001-2005 by the following authors:
 *   Tony Bibbs - tony AT tonybibbs DOT com
 *   Trinity Bays - trinity93 AT gmail DOT com
 *
 */

if (!defined ('GVERSION')) {
    die ('This file cannot be used on its own.');
}
use KeyShare\Config;
global $LANG32;

$LANG_KEYSHARE = array(
    'display_name'  => 'Key Sharing',
    'hlp_purge_all' => 'Purge All: Removes all secrets from the database',
    'purge_all' => 'Purge All',
    'op_success' => 'The operation succeeded.',
    'op_failure' => 'The operation failed. Check the error log.',
    'hlp_submit1' => 'Enter your secret value in the boxl provided and click the submit button below.',
    'hlp_submit2' => 'A one-time URL will be displayed, be sure to copy the URL as it cannot be displayed again.',
    'hlp_postsubmit1' => 'Be sure to copy this url as it can be displayed only once. This is also a one-time access link.',
    'err_empty_value' => 'The secret value cannot be empty.',
    'copy_clipboard'    => 'Copy to clipboard',
    'copy_success' => 'The value has been copied to the clipboard.',
    'hlp_display1' => 'Be sure to copy this value as it can be displayed only once.',
    'record_not_found' => 'The requested record was not found.',
    'display_secret' => 'Display Secret',
    'hide_secret' => 'Hide Secret',
);

// Localization of the Admin Configuration UI
$LANG_configsections[Config::PI_NAME] = array(
    'label' => 'Key Sharing',
    'title' => 'Key Sharing',
);

$LANG_confignames[Config::PI_NAME] = array(
    'hidemenu' => 'Hide Menu Entry',
    'expire_after' => 'Expire unviewed secrets after X days',
    'del_after_view' => 'Delete secrets after viewing',
);

$LANG_configsubgroups[Config::PI_NAME] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs[Config::PI_NAME] = array(
    'fs_main' => 'General Settings',
    'fs_permissions' => 'Default Permissions'
);

$LANG_configSelect[Config::PI_NAME] = array(
    0 => array(1=>'Yes', 0=>'No'),
    1 => array(true=>'True', false=>'False'),
    12 => array(0=>'No access', 2=>'Read-Only', 3=>'Read-Write'),
    13 => array(0=>'Left Blocks', 1=>'Right Blocks', 2=>'Left & Right Blocks', 3=>'None'),
);
