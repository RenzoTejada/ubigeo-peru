<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


add_action('admin_menu', 'rt_ubigeo_register_admin_page');

function rt_ubigeo_register_admin_page()
{
    add_submenu_page('woocommerce', 'Configuraciones', 'Ubigeo Perú', 'manage_woocommerce', 'rt_ubigeo_settings', 'rt_ubigeo_submenu_settings_callback');
    add_action('admin_init', 'rt_ubigeo_register_settings');
}

function rt_ubigeo_submenu_settings_callback()
{
    if (isset($_REQUEST["settings-updated"]) && sanitize_text_field($_REQUEST["settings-updated"] == true)) {
        echo "<script>alert('Se han guardado la nuevas opciones.');</script>";
    }
    //script
    //wp_register_script('rt_ubigeo_script_admin', plugins_url('js/rt_ubigeo_admin.js', __FILE__));
    wp_enqueue_script('rt_ubigeo_script_admin'); ?>
    <style>
        input[type=text], select {
            width: 400px;
            margin: 0;
            padding: 6px !important;
            box-sizing: border-box;
            vertical-align: top;
            height: auto;
            line-height: 2;
            min-height: 30px;
        }
    </style>
    <div class="wrap woocommerce" id="facto-conf">
        <div style="background-color:#87b43e;">
        </div>
        <h1>Ubigeo de Per&uacute; para Woocommerce | Integración del Ubigeo del Perú a tu Woocommerce</h1>
        <hr>
        <h2 class="nav-tab-wrapper">
            <a href="?page=rt_ubigeo_settings&tab=docs" class="nav-tab <?php
            if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
                print " nav-tab-active";
            } ?>">Docs</a>
               <?php
               if (rt_costo_ubigeo_plugin_enabled()) {
                   ?>
                <a href="?page=rt_ubigeo_settings&tab=cost" class="nav-tab <?php
                if ($_REQUEST['tab'] == "cost") {
                    print " nav-tab-active";
                } ?>">Ubigeo</a>
                <a href="?page=rt_ubigeo_settings&tab=license" class="nav-tab <?php
                if ($_REQUEST['tab'] == "license") {
                    print " nav-tab-active";
                } ?>">Licencia</a>
               <?php
               } ?>
            <a href="?page=rt_ubigeo_settings&tab=help" class="nav-tab <?php
               if ($_REQUEST['tab'] == "help") {
                   print " nav-tab-active";
               } ?>">Ayuda</a>

        </h2>
        <?php
        if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
            rt_ubigeo_submenu_settings_docs();
        } elseif ($_REQUEST['tab'] == "cost") {
            if (rt_costo_ubigeo_plugin_enabled()) {
                ?>
                <?php
                if (isset($_REQUEST['section']) == "ubigeo") {
                    if (isset($_REQUEST['list_cost']) == "new") {
                        rt_ubigeo_submenu_settings_cost_new();
                    } elseif (isset($_REQUEST['edit'])) {
                        rt_ubigeo_submenu_settings_cost_edit($_REQUEST['edit']);
                    } else {
                        rt_ubigeo_submenu_settings_cost_ubigeo();
                    }
                } else {
                    rt_ubigeo_submenu_settings_cost();
                }
            }
        } elseif ($_REQUEST['tab'] == "license") {
            rt_ubigeo_submenu_settings_license();
        } elseif ($_REQUEST['tab'] == "help") {
            if (rt_costo_ubigeo_plugin_enabled()) {
                rt_ubigeo_submenu_settings_help_cost();
            } else {
                rt_ubigeo_submenu_settings_help();
            }
        } ?>
    </div>
    <?php
}

function rt_ubigeo_register_settings()
{
    // Comprobar que existan las tablas necesarias
    global $wpdb;

    $sql = 'show tables like "' . $wpdb->prefix . 'ubigeo_departamento"';
    $resultado = $wpdb->query($sql);

    if ($resultado == 0) {
        add_action('admin_notices', 'rt_ubigeo_errortabledep');
    }

    $sql = 'show tables like "' . $wpdb->prefix . 'ubigeo_provincia"';
    $resultado = $wpdb->query($sql);

    if ($resultado == 0) {
        add_action('admin_notices', 'rt_ubigeo_errortableprov');
    }

    $sql = 'show tables like "' . $wpdb->prefix . 'ubigeo_distrito"';
    $resultado = $wpdb->query($sql);

    if ($resultado == 0) {
        add_action('admin_notices', 'rt_ubigeo_errortabledist');
    }


    //para cada mp obtenemos su configuraci&oacute;n
    if (class_exists('woocommerce')) {
//        $mp_arr = WC()->payment_gateways->get_available_payment_gateways();
        if(!mercado_pago_plugin_enabled()){
            $mp_arr = WC()->payment_gateways->get_available_payment_gateways();
        } else {
            $mp_arr = array();
        }
    } else {
        add_action('admin_notices', 'rt_ubigeo_errornowoocommerce');

        $mp_arr = array();
    }
}

function mercado_pago_plugin_enabled()
{
    if (in_array('woocommerce-mercadopago/woocommerce-mercadopago.php', (array) get_option('active_plugins', array()))) {
        return true;
    }
    return false;
}

function rt_ubigeo_submenu_settings_docs()
{
    ?>
    <h1>Documentación del Ubigeo de Perú para Woocommerce </h1>
    <div>
        <div>
            <h3>Descripción</h3>
        </div>
        <div>
            <p>Permite seleccionar departamentos, provincias y distritos del Perú</p>
        </div>
    </div>
    <div>
        <div>
            <h3>Atributos</h3>
        </div>
        <div>
            <ul>
                <li>Agrega los Departamentos del Perú</li>
                <li>Agrega las Provincias del Perú</li>
                <li>Agrega los Distritos del Perú</li>
            </ul>
        </div>
    </div>
    <?php
}

function rt_ubigeo_submenu_settings_help()
{
    ?>
    <h2>Ayuda</h2>

    <h3>¿Qué hace este módulo?</h3>

    <p>Te permite integrar tu Woocommerce a Ubigeo de Perú para pedir a los clientes sus información en el checkout.</p>

    <h3>¿Cuál es el costo del modulo?</h3>

    <p>Es totalmente gratis este plugin.</p>

    <h3>¿Como agrego el costo de envio o configuro los costos?</h3>

    <p>Este plugin solo agrega el ubigeo en el checkout de woocommerce, no tiene la funcionalidad de costo de envio</p>

    <h3>¿Cómo configuro el costo de envio al módulo?</h3>

    <p>Dicha funcionada funcionada se encuentra en una versión PREMIUM.</p>

    <h3>¿Cual es la versión PREMIUM?</h3>

    <p>La versión PREMIUM se encuentra en el siguiente <a href="https://renzotejada.com/costo-de-envio-de-ubigeo-de-peru-para-woocommerce/" target="_blank">LINK</a>.</p>

    <h3>Tengo otras preguntas</h3>

    <p>Ingresa a <a href="https://renzotejada.com/contacto?url=dashboard-wodpress" target="_blank">RT - Contacto</a> </p>
    <?php
}
