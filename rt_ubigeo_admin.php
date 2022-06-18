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

function rt_ubigeo_peru_success_notice()
{
    ?>
    <div class="updated notice">
        <p><?php _e('Was saved successfully', 'ubigeo-peru') ?></p>
    </div>
    <?php
}

function rt_ubigeo_submenu_settings_callback()
{
    if (isset($_REQUEST["settings-updated"]) && sanitize_text_field($_REQUEST["settings-updated"] == true)) {
        rt_ubigeo_peru_success_notice();
    } ?>

    <div class="wrap woocommerce" id="facto-conf">
        <div style="background-color:#87b43e;">
        </div>
        <h1><?php _e("Ubigeo from Peru for Woocommerce | Integration of Ubigeo from Peru to your Woocommerce", 'ubigeo-peru') ?></h1>
        <hr>
        <h2 class="nav-tab-wrapper">
            <a href="?page=rt_ubigeo_settings&tab=docs" class="nav-tab <?php
            if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
                print " nav-tab-active";
            } ?>"><?php echo _e('Docs', 'ubigeo-peru') ?></a>
            <?php
            if (rt_costo_ubigeo_plugin_enabled()) {
                ?>
                <a href="?page=rt_ubigeo_settings&tab=cost" class="nav-tab <?php
                if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == "cost") {
                    print " nav-tab-active";
                } ?>"><?php _e('Ubigeo', 'ubigeo-peru') ?></a>
                <a href="?page=rt_ubigeo_settings&tab=import" class="nav-tab <?php
                if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == "import") {
                    print " nav-tab-active";
                } ?>"><?php _e('Import', 'ubigeo-peru') ?></a>
                <a href="?page=rt_ubigeo_settings&tab=license" class="nav-tab <?php
                if ( isset($_REQUEST['tab']) && $_REQUEST['tab'] == "license") {
                    print " nav-tab-active";
                } ?>"><?php _e('License', 'ubigeo-peru') ?></a>
                <?php
            } ?>
            <a href="?page=rt_ubigeo_settings&tab=settings" class="nav-tab <?php
            if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == "settings") {
                print " nav-tab-active";
            } ?>"><?php _e('Settings', 'ubigeo-peru') ?></a>
            <a href="?page=rt_ubigeo_settings&tab=help" class="nav-tab <?php
            if ( isset($_REQUEST['tab']) && $_REQUEST['tab'] == "help") {
                print " nav-tab-active";
            } ?>"><?php _e('Help', 'ubigeo-peru') ?></a>
            <a href="?page=rt_ubigeo_settings&tab=addons" class="nav-tab <?php
            if ( isset($_REQUEST['tab']) && $_REQUEST['tab'] == "addons") {
                print " nav-tab-active";
            } ?>"><?php _e('Addons', 'ubigeo-peru') ?></a>

        </h2>
        <?php
        if ((!isset($_REQUEST['tab'])) || ($_REQUEST['tab'] == "docs")) {
            rt_ubigeo_submenu_settings_docs();
        } elseif ($_REQUEST['tab'] == "cost") {
            if (rt_costo_ubigeo_plugin_enabled()) {
                if (isset($_REQUEST['section']) == "ubigeo") {
                    if (($_REQUEST['list_cost']) == "clear") {
                        rt_ubigeo_submenu_settings_cost_clear();
                    } elseif (($_REQUEST['list_cost']) == "new") {
                        rt_ubigeo_submenu_settings_cost_new();
                    } elseif (isset($_REQUEST['edit'])) {
                        rt_ubigeo_submenu_settings_cost_edit(sanitize_text_field($_REQUEST['edit']));
                    } else {
                        rt_ubigeo_submenu_settings_cost_ubigeo();
                    }
                } else {
                    rt_ubigeo_submenu_settings_cost();
                }
            }
        } elseif ($_REQUEST['tab'] == "import") {
            rt_ubigeo_submenu_settings_import();
        } elseif ($_REQUEST['tab'] == "license") {
            rt_ubigeo_submenu_settings_license();
        } elseif ($_REQUEST['tab'] == "settings") {
            rt_ubigeo_submenu_settings_settings();
        } elseif ($_REQUEST['tab'] == "help") {
            if (rt_costo_ubigeo_plugin_enabled()) {
                rt_ubigeo_submenu_settings_help_cost();
            } else {
                rt_ubigeo_submenu_settings_help();
            }
        } elseif ($_REQUEST['tab'] == "addons") {
            rt_ubigeo_submenu_settings_addons();
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
        if (!mercado_pago_plugin_enabled()) {
            $mp_arr = WC()->payment_gateways->get_available_payment_gateways();
        } else {
            $mp_arr = array();
        }
    } else {
        $mp_arr = array();
    }
    register_setting('ubigeo_peru_settings_group', 'ubigeo_checkout_checkbox');
    register_setting('ubigeo_peru_settings_group', 'ubigeo_emails_checkbox');
    register_setting('ubigeo_peru_settings_group', 'ubigeo_thanks_checkbox');
    register_setting('ubigeo_peru_settings_group', 'ubigeo_title_checkbox');
    register_setting('ubigeo_peru_settings_group', 'ubigeo_format_checkbox');
    register_setting('ubigeo_peru_settings_group', 'ubigeo_priority_method_free_checkbox');
}

function mercado_pago_plugin_enabled()
{
    if (in_array('woocommerce-mercadopago/woocommerce-mercadopago.php', (array)get_option('active_plugins', array()))) {
        return true;
    }
    return false;
}

function rt_ubigeo_submenu_settings_docs()
{
    ?>
    <h1><?php _e("Peru Ubigeo Documentation for Woocommerce", 'ubigeo-peru'); ?></h1>
    <div>
        <div>
            <h3><?php _e("Description", 'ubigeo-peru'); ?></h3>
        </div>
        <div>
            <p><?php _e('Allows you to select departments, provinces and districts of Peru', 'ubigeo-peru'); ?></p>
        </div>
    </div>
    <div>
        <div>
            <h3><?php _e('Attributes', 'ubigeo-peru'); ?></h3>
        </div>
        <div>
            <ul>
                <li><?php _e('Add the Departments of Peru', 'ubigeo-peru'); ?></li>
                <li><?php _e('Add the Provinces of Peru', 'ubigeo-peru'); ?></li>
                <li><?php _e('Add the Districts of Peru', 'ubigeo-peru'); ?></li>
            </ul>
        </div>
    </div>
    <?php if (rt_costo_ubigeo_plugin_enabled()) { ?>
    <div>
        <div>
            <h3><?php _e("Activate shipping ubigeo", 'ubigeo-peru'); ?></h3>
        </div>
        <div>
            <p><?php _e('You can go to the following link', 'ubigeo-peru'); ?> <a
                        href="<?php echo admin_url('admin.php?page=wc-settings&tab=shipping&section=costo_ubigeo_peru_shipping_method') ?>"><?php _e('Shipping Ubigeo ', 'ubigeo-peru'); ?></a>.
            </p>
        </div>
    </div>
    <div>
        <div>
            <h3><?php _e("Link Docs Ubigeo", 'ubigeo-peru'); ?></h3>
        </div>
        <div>
            <p><?php _e('You can read the documentation in the ', 'ubigeo-peru'); ?> <a
                        href="https://renzotejada.com/documentacion/docs-costo-de-envio-de-ubigeo-de-peru-para-woocommerce/?url=dashboard-wodpress"
                        target="_blank"><?php _e('Docs ', 'ubigeo-peru'); ?></a>.</p>
        </div>
    </div>
    <?php
}
}

function rt_ubigeo_submenu_settings_settings()
{
    ?>
    <h2><?php _e('Settings', 'ubigeo-peru'); ?></h2>
    <form method="post" action="options.php" id="ubigeo_peru_formulario">
        <?php settings_fields('ubigeo_peru_settings_group'); ?>
        <?php do_settings_sections('ubigeo_peru_settings_group'); ?>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label><?php _e('Enable in Checkout', 'ubigeo-peru') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="ubigeo_checkout_checkbox" id="ubigeo_checkout_checkbox" value="on"
                        <?php if (esc_attr(get_option('ubigeo_checkout_checkbox')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label><?php _e('Display in Emails', 'ubigeo-peru') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="ubigeo_emails_checkbox" id="ubigeo_emails_checkbox" value="on"
                        <?php if (esc_attr(get_option('ubigeo_emails_checkbox')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label><?php _e('Display in Checkout', 'ubigeo-peru') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="ubigeo_thanks_checkbox" id="ubigeo_thanks_checkbox" value="on"
                        <?php if (esc_attr(get_option('ubigeo_thanks_checkbox')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label><?php _e('Show title', 'ubigeo-peru') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="ubigeo_title_checkbox" id="ubigeo_title_checkbox" value="on"
                        <?php if (esc_attr(get_option('ubigeo_title_checkbox')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label><?php _e('Format ', 'ubigeo-peru') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <select name="ubigeo_format_checkbox" id="ubigeo_format_checkbox">
                        <option value="vertical" <?php if (esc_attr(get_option('ubigeo_format_checkbox')) == "vertical") echo "selected"; ?> ><?php _e('Vertical', 'ubigeo-peru') ?></option>
                        <option value="horizontal" <?php if (esc_attr(get_option('ubigeo_format_checkbox')) == "horizontal") echo "selected"; ?> ><?php _e('Horizontal', 'ubigeo-peru') ?></option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label><?php _e('Priority Method Free Shipping', 'ubigeo-peru') ?></label>
                </th>
                <td class="forminp forminp-checkbox">
                    <input type="checkbox" name="ubigeo_priority_method_free_checkbox" id="ubigeo_priority_method_free_checkbox" value="on"
                        <?php if (esc_attr(get_option('ubigeo_priority_method_free_checkbox')) == "on") echo "checked"; ?> />
                </td>
            </tr>
            </tbody>
        </table>

        <?php submit_button(__('Save Changes', 'ubigeo-peru')); ?>
    </form>
    <?php
}

function rt_ubigeo_submenu_settings_help()
{
    ?>
    <h2><?php _e('Help', 'ubigeo-peru'); ?></h2>

    <h3><?php _e('What does this module do?', 'ubigeo-peru'); ?></h3>

    <p><?php _e('It allows you to integrate your Woocommerce to Ubigeo from Peru to ask customers for their information at checkout.', 'ubigeo-peru'); ?></p>

    <h3><?php _e('What is the cost of the module?', 'ubigeo-peru'); ?></h3>

    <p><?php _e('This plugin is totally free.', 'ubigeo-peru'); ?></p>

    <h3><?php _e('How do I add the shipping cost or configure the costs?', 'ubigeo-peru'); ?></h3>

    <p><?php _e('This plugin only adds the ubigeo in the woocommerce checkout, it does not have the shipping cost functionality', 'ubigeo-peru'); ?></p>

    <h3><?php _e('How to configure the shipping cost to the module?', 'ubigeo-peru'); ?></h3>

    <p><?php _e('Said functional functionality is in a PREMIUM version.', 'ubigeo-peru'); ?></p>

    <h3><?php _e('What is the PREMIUM version?', 'ubigeo-peru'); ?></h3>

    <p><?php _e('The PREMIUM version is in the following ', 'ubigeo-peru'); ?><a
                href="https://renzotejada.com/plugin/costo-de-envio-de-ubigeo-de-peru-para-woocommerce/"
                target="_blank"><?php _e('LINK', 'ubigeo-peru'); ?></a>.</p>

    <h3><?php _e('I have other questions', 'ubigeo-peru'); ?></h3>

    <p><?php _e('Go to', 'ubigeo-peru'); ?> <a href="https://renzotejada.com/contacto?url=dashboard-wodpress"
                                               target="_blank"><?php _e('RT - Contact', 'ubigeo-peru'); ?></a></p>
    <?php
}

function rt_ubigeo_submenu_settings_addons()
{
    wp_register_style('css_ubigeo_admin', plugins_url('css/css_ubigeo_admin.css', __FILE__), array(), '0.0.2');
    wp_enqueue_style('css_ubigeo_admin');
    ?>
    <div class="wrap">
        <div class="ubigeo_admin_title">
            <h2><?php _e('Addons', 'ubigeo-peru'); ?></h2>
        </div>
        <div class="ubigeo_admin_all">
            <div class="ubigeo_admin_container">
                <div class="ubigeo_admin_item">
                    <div class="ubigeo_admin_header">
                        <img src="<?php echo plugins_url('image/costo-de-envio-de-ubigeo-peru.jpg', __FILE__); ?>"/>
                    </div>
                    <div class="ubigeo_admin_body">
                        <h2><?php _e('Costo de Envío de Ubigeo Perú', 'ubigeo-peru'); ?></h2>
                        <p><?php _e('This plugin adds the shipping cost functionality of ubigeo Peru to WooCommerce.', 'ubigeo-peru'); ?></p>
                    </div>
                    <div class="ubigeo_admin_footer">
                        <a href="https://renzotejada.com/plugin/costo-de-envio-de-ubigeo-de-peru-para-woocommerce/?utm_source=addons&utm_medium=link&utm_campaign=ubigeo&utm_content=click"
                           target="_blank"
                           class="button"><?php _e('Add', 'ubigeo-peru'); ?></a>
                    </div>
                </div>
            </div>
            <div class="ubigeo_admin_container">
                <div class="ubigeo_admin_item">
                    <div class="ubigeo_admin_header">
                        <img src="<?php echo plugins_url('image/libro-de-reclamaciones-y-quejas-pro.jpg', __FILE__); ?>"/>
                    </div>
                    <div class="ubigeo_admin_body">
                        <h2><?php _e('Libro de Reclamos y quejas PRO', 'ubigeo-peru'); ?></h2>
                        <p><?php _e('Useful tool for the protection of consumer rights, and by Indecopi\'s considerations.', 'ubigeo-peru'); ?></p>
                    </div>
                    <div class="ubigeo_admin_footer">
                        <a href="https://renzotejada.com/plugin/libro-de-reclamaciones-y-quejas-pro/?utm_source=addons&utm_medium=link&utm_campaign=ubigeo&utm_content=click"
                           target="_blank"
                           class="button"><?php _e('Add', 'ubigeo-peru'); ?></a>
                    </div>
                </div>
            </div>
            <div class="ubigeo_admin_container">
                <div class="ubigeo_admin_item">
                    <div class="ubigeo_admin_header">
                        <img src="<?php echo plugins_url('image/plugin-wooyape.jpg', __FILE__); ?>"/>
                    </div>
                    <div class="ubigeo_admin_body">
                        <h2><?php _e('WooYape para WooCommerce', 'ubigeo-peru'); ?></h2>
                        <p><?php _e('Add Yape digital wallet for WooCommerce to your online shop.', 'ubigeo-peru'); ?></p>
                    </div>
                    <div class="ubigeo_admin_footer">
                        <a href="https://renzotejada.com/plugin/wooyape-para-woocommerce/?utm_source=addons&utm_medium=link&utm_campaign=ubigeo&utm_content=click"
                           target="_blank"
                           class="button"><?php _e('Add', 'ubigeo-peru'); ?></a>
                    </div>
                </div>
            </div>
            <div class="ubigeo_admin_container">
                <div class="ubigeo_admin_item">
                    <div class="ubigeo_admin_header">
                        <img src="<?php echo plugins_url('image/plugin-wooplin.jpg', __FILE__); ?>"/>
                    </div>
                    <div class="ubigeo_admin_body">
                        <h2><?php _e('WooPlin para WooCommerce', 'ubigeo-peru'); ?></h2>
                        <p><?php _e('Add Plin digital wallet for WooCommerce to your online shop.', 'ubigeo-peru'); ?></p>
                    </div>
                    <div class="ubigeo_admin_footer">
                        <a href="https://renzotejada.com/plugin/wooplin-para-woocommerce/?utm_source=addons&utm_medium=link&utm_campaign=ubigeo&utm_content=click"
                           target="_blank"
                           class="button"><?php _e('Add', 'ubigeo-peru'); ?></a>
                    </div>
                </div>
            </div>
            <div class="ubigeo_admin_container">
                <div class="ubigeo_admin_item">
                    <div class="ubigeo_admin_header">
                        <img src="<?php echo plugins_url('image/comprobante-de-pago-woo.jpg', __FILE__); ?>"/>
                    </div>
                    <div class="ubigeo_admin_body">
                        <h2><?php _e('Comprobante de Pago Perú', 'ubigeo-peru'); ?></h2>
                        <p><?php _e('Choose bill or Invoice or others in your online shop.', 'ubigeo-peru'); ?></p>
                    </div>
                    <div class="ubigeo_admin_footer">
                        <a href="https://renzotejada.com/plugin/comprobante-de-pago-peru-para-woocooomerce/?utm_source=addons&utm_medium=link&utm_campaign=ubigeo&utm_content=click"
                           target="_blank"
                           class="button"><?php _e('Download', 'ubigeo-peru'); ?></a>
                    </div>
                </div>
            </div>
            <div class="ubigeo_admin_container">
                <div class="ubigeo_admin_item">
                    <div class="ubigeo_admin_header">
                        <img src="<?php echo plugins_url('image/tipo-de-documento-woo.jpg', __FILE__); ?>"/>
                    </div>
                    <div class="ubigeo_admin_body">
                        <h2><?php _e('Tipo Documento Perú', 'ubigeo-peru'); ?></h2>
                        <p><?php _e('Choose DNI or RUC or others is added in your online shop.', 'ubigeo-peru'); ?></p>
                    </div>
                    <div class="ubigeo_admin_footer">
                        <a href="https://renzotejada.com/plugin/tipo-de-documento-peru-para-woocooomerce/?utm_source=addons&utm_medium=link&utm_campaign=ubigeo&utm_content=click"
                           target="_blank"
                           class="button"><?php _e('Download', 'ubigeo-peru'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}



