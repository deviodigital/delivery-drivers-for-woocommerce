<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package DDWC
 * @author  Devio Digital <contact@deviodigital.com>
 * @license GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 * @link    https://www.deviodigital.com
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    wp_die();
}
