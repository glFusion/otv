<?php
/**
 * Class to provide admin and user-facing menus.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
namespace KeyShare;


/**
 * Class to provide admin and user-facing menus.
 * @package keyshare
 */
class Menu
{
    /**
     * Create the administrator menu.
     *
     * @param   string  $view   View being shown, so set the help text
     * @return  string      Administrator menu
     */
    public static function Admin($view='')
    {
        global $_CONF, $LANG01, $LANG_KEYSHARE;

        USES_lib_admin();
        $menu_arr = array (
            array(
                'url' => Config::get('admin_url') . '/index.php?purge',
                'text' => $LANG_KEYSHARE['purge_all'],
            ),
            array(
                'url' => $_CONF['site_admin_url'],
                'text' => $LANG01[53]       // Admin Home,
            ),
        );
        $T = new \Template(Config::get('template_path'));
        $T->set_file('title', 'admin.thtml');
        $T->set_var(array(
            'version'   => Config::get('pi_version'),
            'logo_url' => plugin_geticon_keyshare(),
            'lang_pi_title' => $LANG_KEYSHARE['display_name'],
        ) );
        $retval = $T->parse('', 'title');
        $retval .= ADMIN_createMenu(
            $menu_arr,
            $LANG_KEYSHARE['hlp_purge_all'],
            plugin_geticon_keyshare()
        );
        return $retval;
    }


    /**
     * Display the site header, with or without blocks according to configuration.
     *
     * @param   string  $title  Title to put in header
     * @param   string  $meta   Optional header code
     * @return  string          HTML for site header, from COM_siteHeader()
     */
    public static function siteHeader($title='', $meta='')
    {
        return COM_siteHeader($title);
    }


    /**
     * Display the site footer, with or without blocks as configured.
     *
     * @return  string      HTML for site footer, from COM_siteFooter()
     */
    public static function siteFooter()
    {
        return COM_siteFooter();
    }


    /**
     * Create the user-facing menu.
     *
     * @param   string  $view   View being shown, so set the help text
     * @return  string      Administrator menu
     */
    public static function User(string $view='') : string
    {
        global $LANG_KEYSHARE, $LANG_ADMIN;

        USES_lib_admin();
        $menu_arr = array (
            array(
                'url' => Config::get('url') . '/index.php',
                'text' => $LANG_ADMIN['submit'],
            ),
        );
        $T = new \Template(Config::get('template_path'));
        $T->set_file('title', 'guest.thtml');
        $T->set_var(array(
            'version'   => Config::get('pi_version'),
            'logo_url' => plugin_geticon_keyshare(),
            'lang_pi_title' => $LANG_KEYSHARE['display_name'],
        ) );
        $retval = $T->parse('', 'title');
        $retval .= ADMIN_createMenu(
            $menu_arr,
            '',
            plugin_geticon_keyshare()
        );
        return $retval;
    }

}
