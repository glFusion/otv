<?php
/**
 * Upgrade routines for the KeyShare plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

if (!defined('GVERSION')) {
    die('This file can not be used on its own.');
}
use KeyShare\Config;
use glFusion\Database\Database;
use glFusion\Log\Log;


/**
 * Upgrade the plugin.
 *
 * @param   boolean $dvlp   True to ignore errors (development upgrade)
 * @return  boolean     True on success, False on failure
 */
function KEYSHARE_do_upgrade($dvlp = false)
{
    global $_TABLES, $_CONF, $_PLUGINS, $_PLUGIN_INFO, $KEYSHARE_UPGRADE;

    include_once __DIR__ . '/sql/mysql_install.php';
    $db = Database::getInstance();

    $installed_ver = $_PLUGIN_INFO[Config::PI_NAME]['pi_version'];
    $code_ver = plugin_chkVersion_keyshare();
    $current_ver = $installed_ver;

    // Final version update to catch any code-only updates
    if ($current_ver != $code_ver) {
        if (!KEYSHARE_do_set_version($code_ver)) return false;
    }

    // Update any configuration item changes
    USES_lib_install();
    global $keyshareConfigData;
    require_once __DIR__ . '/install_defaults.php';
    _update_config('keyshare', $keyshareConfigData);

    // Clear all caches
    CTL_clearCache();

    // Remove deprecated files
    KEYSHARE_remove_old_files();

    // Made it this far, return OK
    return true;
}


/**
 * Actually perform any sql updates.
 * Gets the sql statements from the $UPGRADE array defined (maybe)
 * in the SQL installation file.
 *
 * @param   string  $version    Version being upgraded TO
 * @param   boolean $ignore_error   True to ignore SQL errors
 * @return  boolean     True on success, False on failure
 */
function KEYSHARE_do_upgrade_sql($version, $ignore_error=false)
{
    global $_TABLES, $KEYSHARE_UPGRADE;

    // If no sql statements passed in, return success
    if (!isset($KEYSHARE_UPGRADE[$version]) || !is_array($KEYSHARE_UPGRADE[$version])) {
        return true;
    }

    $db = Database::getInstance();

    // Execute SQL now to perform the upgrade
    Log::write('system', Log::INFO, "--- Updating KeyShare to version $version");
    foreach($KEYSHARE_UPGRADE[$version] as $sql) {
        Log::write('system', Log::INFO, "KeyShare Plugin $version update: Executing SQL => $sql");
        try {
            $db->conn->executeStatement($sql);
        } catch (\Exception $e) {
            Log::write('system', Log::ERROR, __FUNCTION__ . ': ' . $e->getMessage());
            if (!$ignore_error){
                return false;
            }
        }
    }
    Log::write('system', Log::INFO, "--- KeyShare plugin SQL update to version $version done");
    return true;
}


/**
 * Update the plugin version number in the database.
 * Called at each version upgrade to keep up to date with
 * successful upgrades.
 *
 * @param   string  $ver    New version to set
 * @return  boolean         True on success, False on failure
 */
function KEYSHARE_do_set_version($ver)
{
    global $_TABLES;

    $db = Database::getInstance();

    // now update the current version number.
    try {
        $db->conn->update(
            $_TABLES['plugins'],
            array(
                'pi_version' => $ver,
                'pi_gl_version' => Config::get('gl_version'),
                'pi_homepage' => Config::get('pi_url'),
            ),
            array('pi_name' => Config::PI_NAME),
            array(Database::STRING, Database::STRING, Database::STRING, Database::STRING)
        );
    } catch (\Exception $e) {
        Log::write('system', Log::ERROR, __FUNCTION__ . ': ' . $e->getMessage());
        return false;
    }
    return true;
}


/**
 * Remove deprecated files.
 */
function KEYSHARE_remove_old_files()
{
    global $_CONF;

    $paths = array(
        // private/plugins/keyshare
        __DIR__ => array(
        ),
        // public_html/keyshare
        $_CONF['path_html'] . 'keyshare' => array(
        ),
    );

    foreach ($paths as $path=>$files) {
        foreach ($files as $file) {
            if (file_exists("$path/$file")) {
                @unlink("$path/$file");
            }
        }
    }
}
