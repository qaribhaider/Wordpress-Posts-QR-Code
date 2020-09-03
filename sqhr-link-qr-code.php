<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://twitter.com/syedqarib
 * @since             1.0.0
 * @package           Sqhr_Link_Qr_Code
 *
 * @wordpress-plugin
 * Plugin Name:       Link QR Code
 * Plugin URI:        https://github.com/qaribhaider/Wordpress-Posts-QR-Code
 * Description:       Add a QR code to each post, page and custom post type
 * Version:           1.0.1
 * Author:            Qarib Haider
 * Author URI:        https://twitter.com/syedqarib
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sqhr-link-qr-code
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SQHR_LINK_QR_CODE_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sqhr-link-qr-code-activator.php
 */
function activate_sqhr_link_qr_code()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sqhr-link-qr-code-activator.php';
    Sqhr_Link_Qr_Code_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sqhr-link-qr-code-deactivator.php
 */
function deactivate_sqhr_link_qr_code()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sqhr-link-qr-code-deactivator.php';
    Sqhr_Link_Qr_Code_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sqhr_link_qr_code');
register_deactivation_hook(__FILE__, 'deactivate_sqhr_link_qr_code');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sqhr-link-qr-code.php';

/**
 * Add link for the settings page under plugins list
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sqhr_qrcode_add_plugin_page_settings_link');
function sqhr_qrcode_add_plugin_page_settings_link($links)
{
    $links[] = '<a href="' .
    admin_url('options-general.php?page=sqhrqrcsp-settings') .
    '">' . __('Settings') . '</a>';
    return $links;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sqhr_link_qr_code()
{

    $plugin = new Sqhr_Link_Qr_Code();
    $plugin->run();

}
run_sqhr_link_qr_code();
