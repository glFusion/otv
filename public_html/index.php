<?php
/**
 * Guest-facing entry point for the keyshare plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
require_once '../lib-common.php';

use KeyShare\Config;
use KeyShare\Models\Secret;
if (!in_array(Config::get('pi_name'), $_PLUGINS)) {
    COM_404();
    exit;
}

$display = '';
$page = '';
$title = 'Secure Sharing';

if (isset($_POST['submitval'])) {
    // Submitting a new secret
    if (!SEC_hasRights('keyshare.submit')) {
        COM_404();
    }
    // Try to keep this from becoming a spam vector.
    if (PLG_checkforSpam($_POST['secret'], $_CONF['spamx'], $_POST)) {
        COM_displayMessageAndAbort ($result, 'spamx', 403, 'Forbidden');
    }
    $S = new Secret;
    $status = $S->withValue($_POST['secret'])->save();
    if ($status) {
        $url_key = $S->getUrlKey();
        $url = Config::get('url') . '/index.php?k=' . urlencode($url_key);
        $T = new Template(Config::get('template_path'));
        $T->set_file('display', 'postsubmit.thtml');
        $T->set_var('urlkey', $url_key);
        $T->set_var('url', $url);
        $T->parse('output', 'display');
        $page .= $T->finish($T->get_var('output'));
    } else {
        $msg = $S->getErrors(true);
        if (!empty($msg)) {
            COM_setMsg($msg, 'error', true);
        }
        echo COM_refresh(Config::get('url') . '/index.php');
    }
} elseif (isset($_GET['k'])) {
    // Decoding a secret value.
    if (!SEC_hasRights('keyshare.view')) {
        COM_404();
    }
    $S = Secret::getFromUrlKey($_GET['k']);
    if ($S && $S->getId() > 0) {
        $page .= $S->render();
    } else {
        $page .= COM_showMessageText($LANG_KEYSHARE['record_not_found'], '', true, 'error');
    }
} else {
    // Display the submission form.
    if (!SEC_hasRights('keyshare.submit')) {
        COM_404();
    }
    $page .= Secret::submit();
}

$display = COM_siteHeader($title);
$display .= KeyShare\Menu::User();
$display .= $page;
$display .= COM_siteFooter();
echo $display;
