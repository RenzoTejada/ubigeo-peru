<?php

/**
 *
 * @link              https://renzotejada.com/
 * @package           Ubigeo de Per&uacute;
 *
 * @wordpress-plugin
 * Plugin Name:       Ubigeo de Per&uacute; para WooCommerce
 * Plugin URI:        https://renzotejada.com/blog/ubigeo-de-peru-para-woocommerce/
 * Description:       Ubigeo de Per&uacute; para WooCommerce - Plugin contiene los departamentos - provincias y distritos del Per&uacute;
 * Version:           3.0.8
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

// Agrega la página de settings en plugins
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'rt_add_plugin_page_settings_link');


function rt_add_plugin_page_settings_link($links)
{
    $links2[] = '<a href="' .
            admin_url('admin.php?page=rt_ubigeo_settings') .
            '">' . __('Ajustes') . '</a>';

    $links = array_merge($links2, $links);

    return $links;
}

/*
 * LIB
 */
require dirname(__FILE__) . "/rt_ubigeo_lib.php";

/*
 * SETUP
 */
require dirname(__FILE__) . "/rt_ubigeo_setup.php";

register_activation_hook(__FILE__, 'rt_ubigeo_setup');

/*
 * ADMIN
 */
require dirname(__FILE__) . "/rt_ubigeo_admin.php";

/*
 * CHECKOUT
 */
require dirname(__FILE__) . "/rt_ubigeo_checkout.php";
