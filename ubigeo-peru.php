<?php

/**
 *
 * @link              https://renzotejada.com/
 * @since             2.0.0
 * @package           Ubigeo de Per&uacute;
 *
 * @wordpress-plugin
 * Plugin Name:       Ubigeo de Per&uacute; para Woocommerce
 * Plugin URI:        https://renzotejada.com/blog/ubigeo-de-peru-para-woocommerce/
 * Description:       Ubigeo de Per&uacute; para woocommerce - Plugin contiene los departamentos - provincias y distritos del Per&uacute;
 * Version:           2.0.0
 * Author:            Renzo Tejada
 * Author URI:        https://renzotejada.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ubigeo-peru
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Currently plugin version.
 * Start at version 2.0.0
 */
define('UBIGEO_PERU_VERSION', '2.0.0');
define('UBIGEO_TITLE', 'Ubigeo Perú');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ubigeo-peru-activator.php
 */
function activate_ubigeo_peru() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru-activator.php';
    Ubigeo_Peru_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ubigeo-peru-deactivator.php
 */
function deactivate_ubigeo_peru() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru-deactivator.php';
    Ubigeo_Peru_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ubigeo_peru');
register_deactivation_hook(__FILE__, 'deactivate_ubigeo_peru');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_ubigeo_peru() {

    $plugin = new Ubigeo_Peru();
    $plugin->run();
}

run_ubigeo_peru();
