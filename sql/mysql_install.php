<?php
/**
 * Database creation and update statements for the KeyShare plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 The Above Authors
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}
global $_SQL, $KEYSHARE_UPGRADE;

$_SQL = array();
$_SQL['keyshare_secrets'] = "CREATE TABLE {$_TABLES['keyshare_secrets']} (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ts` int(10) unsigned DEFAULT NULL,
  `pub_key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM";

$KEYSHARE_UPGRADE = array(
);
