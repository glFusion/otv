<?php
/**
 * Admin entry point for the KeyShare plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
require_once('../../../lib-common.php');

if (!plugin_ismoderator_keyshare()) {
    COM_404();
}
USES_lib_admin();
use KeyShare\Config;
use KeyShare\Models\Secret;
use KeyShare\Menu;

$expected = array(
    'purge',
);
$action = '';

foreach($expected as $provided) {
    if (isset($_POST[$provided])) {
        $action = $provided;
        $actionval = $_POST[$provided];
        break;
    } elseif (isset($_GET[$provided])) {
        $action = $provided;
        $actionval = $_GET[$provided];
        break;
    }
}

switch ($action) {
case 'purge':
    if (Secret::purge()) {
        COM_setMsg($LANG_KEYSHARE['op_success']);
    } else {
        COM_setMsg($LANG_KEYSHARE['op_failure']);
    }
    echo COM_refresh(Config::get('admin_url') . '/index.php');
    break;

default:
    break;
}

echo COM_siteHeader();
echo Menu::Admin($action);
//echo $content;
echo COM_siteFooter();
