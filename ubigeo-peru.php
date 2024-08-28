<?php
/**
 * Ubigeo de Perú para WooCommerce y WordPress
 *
 *
 * @link              https://renzotejada.com/
 * @package           Ubigeo de Perú para WooCommerce y WordPress
 * @author            Renzo Tejada
 * @wordpress-plugin
 * Plugin Name:       Ubigeo de Perú para WooCommerce y WordPress
 * Plugin URI:        https://renzotejada.com/ubigeo-de-peru-para-woocommerce/
 * Description:       Peru's Ubigeo for WordPress and WooCommerce - Plugin contains the departments - provinces and districts of Peru
 * Version:           3.7.8
 * Author:            Renzo Tejada
 * Author URI:        https://renzotejada.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ubigeo-peru
 * Domain Path:       /language
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * WC tested up to:   9.2.3
 * WC requires at least: 2.6
 */
if (!defined('ABSPATH')) {
    exit;
}

$plugin_ubigeo_peru_version = get_file_data(__FILE__, array('Version' => 'Version'), false);

define('Version_RT_Ubigeo_Peru', $plugin_ubigeo_peru_version['Version']);

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

function ubigeo_load_textdomain()
{
    load_plugin_textdomain('ubigeo-peru', false, basename(dirname(__FILE__)) . '/language/');
}

add_action('init', 'ubigeo_load_textdomain');

// Agrega la página de settings en plugins
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'rt_add_plugin_page_settings_link');

// Agrega link plugins
add_filter( 'plugin_row_meta', 'rt_ubigeo_plugin_row_meta', 10, 2 );

// Actualizaciones para el plugin
add_action( 'plugins_loaded', 'rt_plugin_update_change');

function rt_ubigeo_plugin_row_meta( $links, $file )
{
    if ( 'ubigeo-peru/ubigeo-peru.php' !== $file ) {
        return $links;
    }

    $row_meta = array(
        'docs'    => '<a href="' . admin_url('admin.php?page=rt_ubigeo_settings') . '">' .  __('Docs', 'ubigeo-peru') . '</a>',
        'support' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/ubigeo-peru/' ) . '">' . esc_html__( 'Support', 'ubigeo-peru' ) . '</a>',
    );

    return array_merge( $links, $row_meta );
}

function rt_add_plugin_page_settings_link($links)
{
    $links2[] = '<a href="' . admin_url('admin.php?page=rt_ubigeo_settings&tab=settings') . '">' .  __('Settings', 'ubigeo-peru') . '</a>';
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
if (get_option('ubigeo_checkout_checkbox') == "on") {
    require dirname(__FILE__) . "/rt_ubigeo_checkout.php";
}

/*
 * Address
 */
require dirname(__FILE__) . "/rt_ubigeo_address.php";

