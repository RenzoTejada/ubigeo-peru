<?php
use Automattic\WooCommerce\Utilities\OrderUtil;

add_filter('woocommerce_states', 'rt_ubigeo_remove_peru_state');

function rt_ubigeo_remove_peru_state($states)
{
    $states['PE'] = array();
    return $states;
}

add_filter('woocommerce_country_locale_field_selectors', 'rt_ubigeo_country_locale_field_selectors');

function rt_ubigeo_country_locale_field_selectors($locale_fields)
{
    $custom_locale_fields = array(
        'departamento' => '#billing_departamento_field, #shipping_departamento_field',
        'provincia' => '#billing_provincia_field, #shipping_provincia_field',
        'distrito' => '#billing_distrito_field, #shipping_distrito_field',
    );

    $locale_fields = array_merge($locale_fields, $custom_locale_fields);

    return $locale_fields;
}

add_filter('woocommerce_default_address_fields', 'rt_ubigeo_default_address_fields');

function rt_ubigeo_default_address_fields($fields)
{
    $custom_fields = array(
        'departamento' => array(
            'hidden' => true,
            'required' => false,
        ),
        'provincia' => array(
            'hidden' => true,
            'required' => false,
        ),
        'distrito' => array(
            'hidden' => true,
            'required' => false,
        ),
    );

    $fields = array_merge($fields, $custom_fields);

    return $fields;
}

add_filter('woocommerce_get_country_locale', 'rt_ubigeo_get_country_locale');

function rt_ubigeo_get_country_locale($locale)
{
    $locale['PE']['departamento'] = array(
        'required' => true,
        'hidden' => false,
    );

    $locale['PE']['provincia'] = array(
        'required' => true,
        'hidden' => false,
    );

    $locale['PE']['distrito'] = array(
        'required' => true,
        'hidden' => false,
    );

    $locale['PE']['state'] = array(
        'required' => false,
        'hidden' => true,
    );

    $locale['PE']['city'] = array(
        'required' => false,
        'hidden' => true,
    );

    $locale['PE']['postcode'] = array(
        'required' => false,
        'hidden' => true,
    );

    return $locale;
}

add_filter('woocommerce_checkout_fields', 'rt_ubigeo_wc_checkout_fields', 99);

function rt_ubigeo_wc_checkout_fields($fields)
{

    $fields['billing']['billing_phone']['priority'] = 34;
    $fields['billing']['billing_email']['priority'] = 36;
    $fields['billing']['billing_address_1']['priority'] = 74;
    $fields['shipping']['shipping_address_1']['priority'] = 74;
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $idDepa = $current_user->billing_departamento;
        $idProv = $current_user->billing_provincia;
        $idDist = $current_user->billing_distrito;
        $idDepa_shipping = $current_user->shipping_departamento;
        $idProv_shipping = $current_user->shipping_provincia;
        $idDist_shipping = $current_user->shipping_distrito;

        if ($idDepa) {
            $data_prov = rt_ubigeo_load_provincias_front_session($idDepa);
            if (empty($data_prov)) {
                $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if ($idProv) {
            $data_dist = rt_ubigeo_load_distritos_front_session($idProv);
            if (empty($data_dist)) {
                $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
        }

        if ($idDepa_shipping) {
            $data_prov_shipping = rt_ubigeo_load_provincias_front_session($idDepa_shipping);
            if (empty($data_prov_shipping)) {
                $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if ($idProv_shipping) {
            $data_dist_shipping = rt_ubigeo_load_distritos_front_session($idProv_shipping);
            if (empty($data_dist_shipping)) {
                $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
        }
    } else {
        $idDepa = $idProv = $idDist = $idDepa_shipping = $idProv_shipping = $idDist_shipping = '';

        if (isset($_SESSION['idDepa']) && !empty($_SESSION['idDepa'])) {
            $data_prov = rt_ubigeo_load_provincias_front_session($_SESSION['idDepa']);
            if (empty($data_prov)) {
                $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if (isset($_SESSION['idProv']) && !empty($_SESSION['idProv'])) {
            $is_prov = rt_ubigeo_validate_prov_of_depa($_SESSION['idDepa'], $_SESSION['idProv']);
            if ($is_prov) {
                $data_dist = rt_ubigeo_load_distritos_front_session($_SESSION['idProv']);
                if (empty($data_dist)) {
                    $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
                }
            } else {
                $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
        }

        if ($idDepa_shipping) {
            $data_prov_shipping = rt_ubigeo_load_provincias_front_session($idDepa_shipping);
            if (empty($data_prov_shipping)) {
                $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if ($idProv_shipping) {
            $data_dist_shipping = rt_ubigeo_load_distritos_front_session($idProv_shipping);
            if (empty($data_dist_shipping)) {
                $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
        }
    }

    if($fields['billing']['billing_state']['country'] == 'PE') {

        $fields['billing']['billing_departamento'] = [
            'type' => 'select',
            'label' => __('Department', 'ubigeo-peru'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'options' => rt_ubigeo_get_departamentos_for_select(),
            'priority' => 65
        ];

        $fields['billing']['billing_provincia'] = [
            'type' => 'select',
            'label' => __('Province', 'ubigeo-peru'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'options' => $data_prov,
            'priority' => 66
        ];

        $fields['billing']['billing_distrito'] = [
            'type' => 'select',
            'label' => __('District', 'ubigeo-peru'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'options' => $data_dist,
            'priority' => 67
        ];

        $fields['shipping']['shipping_departamento'] = [
            'type' => 'select',
            'label' => __('Department', 'ubigeo-peru'),
            'required' => false,
            'class' => array('form-row-wide'),
            'clear' => true,
            'options' => rt_ubigeo_get_departamentos_for_select(),
            'priority' => 65
        ];


        $fields['shipping']['shipping_provincia'] = [
            'type' => 'select',
            'label' => __('Province', 'ubigeo-peru'),
            'required' => false,
            'class' => array('form-row-wide'),
            'clear' => true,
            'options' => $data_prov_shipping,
            'priority' => 66
        ];

        $fields['shipping']['shipping_distrito'] = [
            'type' => 'select',
            'label' => __('District', 'ubigeo-peru'),
            'required' => false,
            'class' => array('form-row-wide'),
            'clear' => true,
            'options' => $data_dist_shipping,
            'priority' => 67
        ];
    }

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'jsm_override_checkout_fields');

function jsm_override_checkout_fields($fields)
{
    $fields['billing']['billing_departamento']['default'] = 15;
    $fields['billing']['billing_provincia']['default'] = 130;
    return $fields;
}

add_filter('default_checkout_billing_departamento', 'rt_ubigeo_change_default_checkout_departamento');

function rt_ubigeo_change_default_checkout_departamento()
{
    return '0';
}

add_filter('default_checkout_shipping_departamento', 'rt_ubigeo_change_default_checkout_shipping_departamento');

function rt_ubigeo_change_default_checkout_shipping_departamento()
{
    return '';
}


function rt_ubigeo_custom_wc_default_address_fields($address_fields)
{
    $address_fields['address_1']['priority'] = 70;
    $address_fields['address_2']['priority'] = 80;

    return $address_fields;
}

add_filter('woocommerce_default_address_fields', 'rt_ubigeo_custom_wc_default_address_fields');

function rt_ubigeo_peru_is_theme_avada()
{
    $rpt = false;
    $theme = wp_get_theme();
    if ('Avada' == $theme->name || 'Avada' == $theme->parent_theme) {
        $rpt = true;
    }
    return $rpt;
}

function rt_ubigeo_peru_is_theme_astra()
{
    $rpt = false;
    $theme = wp_get_theme();
    if ('Astra' == $theme->name || 'Astra' == $theme->parent_theme) {
        $rpt = true;
    }
    return $rpt;
}

function rt_ubigeo_peru_is_theme_pawsitive()
{
    $rpt = false;
    $theme = wp_get_theme();
    if ('Pawsitive' == $theme->name || 'Pawsitive' == $theme->parent_theme) {
        $rpt = true;
    }
    return $rpt;
}

function rt_ubigeo_peru_is_theme_meabhy()
{
    $rpt = false;
    $theme = wp_get_theme();

    if ('Meabhy' == $theme->name || 'Meabhy' == $theme->parent_theme) {
        $rpt = true;
    }
    return $rpt;
}


add_action('wp_enqueue_scripts', 'rt_ubigeo_able_woocommerce_loading_css_js', 99);

function rt_ubigeo_able_woocommerce_loading_css_js()
{
    // Check if WooCommerce plugin is active
    if (function_exists('is_woocommerce')) {
        // Check if it's any of WooCommerce page
        if (is_checkout()) {
            wp_register_script('select2-ubigeo', plugins_url('js/select2.min.js', __FILE__), array(), '4.0.1', true);
            wp_enqueue_script('select2-ubigeo');
            wp_register_script('js_ubigeo_checkout-js', plugins_url('js/js_ubigeo_checkout.js', __FILE__), array(), '0.0.2', true);
            wp_enqueue_script('js_ubigeo_checkout-js');
            wp_register_style('css_ubigeo_checkout', plugins_url('css/css_ubigeo_checkout.css', __FILE__), array(), '0.0.1');
            wp_enqueue_style('css_ubigeo_checkout');
            if (rt_ubigeo_peru_is_theme_meabhy()) {
                wp_dequeue_script('selectWoo');
            }
            $idDepa = $idProv = $idDist = $idDepa_shipping = $idProv_shipping = $idDist_shipping = '';
            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                $idDepa = $current_user->billing_departamento;
                $idProv = $current_user->billing_provincia;
                $idDist = $current_user->billing_distrito;
                $idDepa_shipping = $current_user->shipping_departamento;
                $idProv_shipping = $current_user->shipping_provincia;
                $idDist_shipping = $current_user->shipping_distrito;
            }
            ?>
            <script>
                var idDepa = "<?php echo esc_attr($idDepa) ?>";
                var idProv = "<?php echo esc_attr($idProv) ?>";
                var idDist = "<?php echo esc_attr($idDist) ?>";
                var idDepa_shipping = "<?php echo esc_attr($idDepa_shipping) ?>";
                var idProv_shipping = "<?php echo esc_attr($idProv_shipping) ?>";
                var idDist_shipping = "<?php echo esc_attr($idDist_shipping) ?>";
                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                var is_theme_avada = "<?php echo (int)rt_ubigeo_peru_is_theme_avada(); ?>";
                var is_theme_astra = "<?php echo (int)rt_ubigeo_peru_is_theme_astra(); ?>";
            </script>
            <?php

        }
    }
}

add_action('woocommerce_after_checkout_form', 'rt_ubigeo_custom_jscript_checkout');

function rt_ubigeo_custom_jscript_checkout()
{
    wp_register_script('select2-js', plugins_url('js/select2.min.js', __FILE__), array(), '4.0.1', true);
    wp_enqueue_script('select2-js');

    ?>
    <?php if (rt_ubigeo_peru_is_theme_pawsitive()) { ?>
    <style>
        .select2-container {
            display: none;
        }
    </style>
<?php } ?>
    <div class="loader">
        <div class="lds-roller">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <?php
}

add_action('woocommerce_checkout_update_order_review', 'rt_ubigeo_checkout_update_refresh_shipping_methods', 10, 1);

function rt_ubigeo_checkout_update_refresh_shipping_methods($post_data)
{
    parse_str($post_data, $data);

    if (array_key_exists('ship_to_different_address', $data)) {
        if (array_key_exists('shipping_distrito', $data)) {
            $_SESSION["idDist"] = $data['shipping_distrito'];
        }
    } else {
        if (array_key_exists('billing_distrito', $data)) {
            $_SESSION["idDist"] = $data['billing_distrito'];
        }
    }
    $packages = WC()->cart->get_shipping_packages();

    foreach ($packages as $package_key => $package) {
        WC()->session->set('shipping_for_package_' . $package_key, false); // Or true
    }
}

add_action('woocommerce_after_checkout_validation', 'rt_ubigeo_custom_wc_checkout_fields_validation', 999, 2);

function rt_ubigeo_custom_wc_checkout_fields_validation($fields, $errors)
{
    if ('PE' === $fields['billing_country']) {
        if ('' === $fields['billing_departamento']) {
            $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(_e('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Billing Departamento') . '</strong>'), 'Billing Departamento'));
        }
        if ('' === $fields['billing_provincia']) {
            $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(_e('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Billing Provincia') . '</strong>'), 'Billing Provincia'));
        }
        if ('' === $fields['billing_distrito']) {
            $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(_e('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Billing Distrito') . '</strong>'), 'Billing Distrito'));
        }
    }

    if (1 == $fields['ship_to_different_address']) {
        if ('PE' === $fields['shipping_country']) {
            if ('' === $fields['shipping_departamento']) {
                $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(_e('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Shipping Departamento') . '</strong>'), 'Shipping Departamento'));
            }
            if ('' === $fields['shipping_provincia']) {
                $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(_e('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Shipping Provincia') . '</strong>'), 'Shipping Provincia'));
            }
            if ('' === $fields['shipping_distrito']) {
                $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(_e('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Shipping Distrito') . '</strong>'), 'Shipping Distrito'));
            }
        }
    }
}

add_action('woocommerce_checkout_process', 'rt_remove_wc_validation', 1);

function rt_remove_wc_validation()
{
    remove_action('woocommerce_checkout_process', 'some_custom_checkout_field_process');
}

function get_name_ubigeo_billing($order, $type = 'object')
{
    $ubigeo = array();
    if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
        $order_data = wc_get_order( $order );
        $idDep = $order_data->get_meta( '_billing_departamento');
        $prov = $order_data->get_meta( '_billing_provincia');
        $dist = $order_data->get_meta( '_billing_distrito');
        if ($idDep) {
            $ubigeo['departamento'] = rt_ubigeo_get_departamento_por_id($idDep)['departamento'];
            $ubigeo['provincia'] = rt_ubigeo_get_provincia_por_id($prov)['provincia'];
            $ubigeo['distrito'] = rt_ubigeo_get_distrito_por_id($dist)['distrito'];
        }
    } else {
        if ($type == 'object') {
            $idDep = get_post_meta($order->id, '_billing_departamento');
            $prov = get_post_meta($order->id, '_billing_provincia');
            $dist = get_post_meta($order->id, '_billing_distrito');
        } else {
            $idDep = get_post_meta($order, '_billing_departamento');
            $prov = get_post_meta($order, '_billing_provincia');
            $dist = get_post_meta($order, '_billing_distrito');
        }
        if ($idDep) {
            $ubigeo['departamento'] = rt_ubigeo_get_departamento_por_id($idDep[0])['departamento'];
            $ubigeo['provincia'] = rt_ubigeo_get_provincia_por_id($prov[0])['provincia'];
            $ubigeo['distrito'] = rt_ubigeo_get_distrito_por_id($dist[0])['distrito'];
        }
    }

    return $ubigeo;
}

function get_name_ubigeo_shipping($order, $type = 'object')
{
    $ubigeo = [];
    if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
        $order_data = wc_get_order( $order );
        $idDep = $order_data->get_meta( '_shipping_departamento');
        $prov = $order_data->get_meta( '_shipping_provincia');
        $dist = $order_data->get_meta( '_shipping_distrito');
        if ($idDep) {
            $ubigeo['departamento'] = rt_ubigeo_get_departamento_por_id($idDep)['departamento'];
            $ubigeo['provincia'] = rt_ubigeo_get_provincia_por_id($prov)['provincia'];
            $ubigeo['distrito'] = rt_ubigeo_get_distrito_por_id($dist)['distrito'];
        }
    } else {
        if ($type == 'object') {
            $idDep = get_post_meta($order->id, '_shipping_departamento');
            $prov = get_post_meta($order->id, '_shipping_provincia');
            $dist = get_post_meta($order->id, '_shipping_distrito');
        } else {
            $idDep = get_post_meta($order, '_shipping_departamento');
            $prov = get_post_meta($order, '_shipping_provincia');
            $dist = get_post_meta($order, '_shipping_distrito');
        }
        if ($idDep) {
            $ubigeo['departamento'] = rt_ubigeo_get_departamento_por_id($idDep[0])['departamento'];
            $ubigeo['provincia'] = rt_ubigeo_get_provincia_por_id($prov[0])['provincia'];
            $ubigeo['distrito'] = rt_ubigeo_get_distrito_por_id($dist[0])['distrito'];
        }
    }


    return $ubigeo;
}


function rt_show_custom_fields_order_billing($order)
{

    $ubigeo_billing = get_name_ubigeo_billing($order->get_id(), 'value');
    if ($ubigeo_billing) {
        echo '<div class="ubigeo_data_column">';
        echo '<h3>' . __('Billing Ubigeo Peru', 'ubigeo-peru') . '</h3>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_billing['departamento']) . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_billing['provincia']) . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_billing['distrito']) . '</p>';
        echo '</div>';
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'rt_show_custom_fields_order_billing', 1);

function rt_show_custom_fields_order_shipping($order)
{
    $ubigeo_shipping = get_name_ubigeo_shipping($order->get_id(), 'value');

    if ($ubigeo_shipping) {
        echo '<div class="ubigeo_data_column">';
        echo '<h3>' . __('Shipping Ubigeo Peru', 'ubigeo-peru') . '</h3>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['departamento']) . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['provincia']) . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['distrito']) . '</p>';
        echo '</div>';
    }
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'rt_show_custom_fields_order_shipping', 1);

function rt_show_custom_view_order($order)
{
    echo '<section class="woocommerce-customer-details">';
    echo '<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">';
    $ubigeo = get_name_ubigeo_billing($order, 'value');
    if ($ubigeo) {
        echo '<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">';
        echo '<h2 class="woocommerce-column__title">' . __('Billing Ubigeo Peru', 'ubigeo-peru') . '</h2>';
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['departamento']) . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['provincia']) . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['distrito']) . '</p>';
        echo '</div>';
        echo '</address>';
    }

    $ubigeo_shipping = get_name_ubigeo_shipping($order, 'value');

    if ($ubigeo_shipping) {
        echo '<div class="woocommerce-column woocommerce-column--2 woocommerce-column--billing-address col-2">';
        echo '<h2 class="woocommerce-column__title">' . __('Shipping Ubigeo Peru', 'ubigeo-peru') . '</h2>';
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['departamento']) . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['provincia']) . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['distrito']) . '</p>';
        echo '</div>';
        echo '</address>';
    }
    echo '</section>';
    echo '</section>';

}

add_action('woocommerce_view_order', 'rt_show_custom_view_order', 20);

function rt_show_custom_fields_thankyou($order)
{
    echo '<section class="woocommerce-customer-details">';
    echo '<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">';
    $ubigeo = get_name_ubigeo_billing($order, 'value');
    if ($ubigeo) {
        echo '<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">';
        echo '<h2 class="woocommerce-column__title">';
        if (get_option('ubigeo_title_checkbox') == "on") :
            echo __('Billing Ubigeo Peru', 'ubigeo-peru');
        endif;
        echo '</h2>';

        if (get_option('ubigeo_format_checkbox') == "vertical") {
            echo '<address>';
            echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['departamento']) . '</p>';
            echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['provincia']) . '</p>';
            echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['distrito']) . '</p>';
            echo '</address>';
        }

        if (get_option('ubigeo_format_checkbox') == "horizontal") {
            echo '<address>';
            echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['departamento']) . '&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '<strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['provincia']) . '&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '<strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['distrito']) . '</p>';
            echo '</address>';
        }
        echo '</div>';
    }

    $ubigeo_shipping = get_name_ubigeo_shipping($order, 'value');

    if ($ubigeo_shipping) {
        echo '<div class="woocommerce-column woocommerce-column--2 woocommerce-column--billing-address col-2">';
//        echo '<h2 class="woocommerce-column__title">' . __('Shipping Ubigeo Peru', 'ubigeo-peru') . '</h2>';
        echo '<h2 class="woocommerce-column__title">';
        if (get_option('ubigeo_title_checkbox') == "on") :
            echo  __('Shipping Ubigeo Peru', 'ubigeo-peru');
        endif;
        echo '</h2>';
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['departamento']) . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['provincia']) . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['distrito']) . '</p>';
        echo '</div>';
        echo '</address>';
    }
    echo '</section>';
    echo '</section>';
}

function rt_show_custom_fields_emails($order)
{
    $ubigeo = get_name_ubigeo_billing($order, 'object');
    echo '<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0;">';
    echo '<tr>';
    echo '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;">';
    if (get_option('ubigeo_title_checkbox') == "on") {
        echo '<h2 class="woocommerce-order-details__title">' . __('Billing Ubigeo Peru', 'ubigeo-peru') . '</h2>';
    }
    if (get_option('ubigeo_format_checkbox') == "vertical") {
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['departamento']) . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['provincia']) . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['distrito']) . '</p>';
        echo '</address>';
    }
    if (get_option('ubigeo_format_checkbox') == "horizontal") {
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['departamento']) .'&nbsp;&nbsp;&nbsp;&nbsp;' ;
        echo '<strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['provincia']) .'&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo['distrito']) . '</p>';
        echo '</address>';
    }

    echo '</td>';

    $ubigeo_shipping = get_name_ubigeo_shipping($order->get_id(), 'value');

    if ($ubigeo_shipping) {
        echo '<td valign="top" width="50%" style="text-align: left; font-family: Helvetica, Roboto, Arial, sans-serif; padding: 0;">';
        if (get_option('ubigeo_title_checkbox') == "on") {
            echo '<h2 class="woocommerce-order-details__title">' . __('Shipping Ubigeo Peru', 'ubigeo-peru') . '</h2>';
        }
        if (get_option('ubigeo_format_checkbox') == "vertical") {
            echo '<address>';
            echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['departamento']) . '</p>';
            echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['provincia']) . '</p>';
            echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['distrito']) . '</p>';
            echo '</address>';
        }
        if (get_option('ubigeo_format_checkbox') == "horizontal") {
            echo '<address>';
            echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['departamento']) .'&nbsp;&nbsp;&nbsp;&nbsp;' ;
            echo '<strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['provincia']) .'&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '<strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . esc_html($ubigeo_shipping['distrito']) . '</p>';
            echo '</address>';
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '</table>';

}

if (get_option('ubigeo_emails_checkbox') == "on") {
    add_action('woocommerce_email_after_order_table', 'rt_show_custom_fields_emails', 20, 1);
}

if (get_option('ubigeo_thanks_checkbox') == "on") {
    add_action('woocommerce_thankyou', 'rt_show_custom_fields_thankyou', 20);
}

function clear_checkout_fields($value, $input)
{
    if ($input == 'billing_departamento') {
        if (sanitize_text_field(isset($_SESSION['idDepa'])) !== null && !empty(sanitize_text_field(isset($_SESSION['idDepa'])))) {
            $value = sanitize_text_field(isset($_SESSION['idDepa']));
        } else {
            $value = '';
        }
    }

    if ($input == 'billing_provincia') {
        if (sanitize_text_field(isset($_SESSION['idProv'])) !== null && !empty(sanitize_text_field(isset($_SESSION['idProv'])))) {
            $value = sanitize_text_field(isset($_SESSION['idProv']));
        } else {
            $value = '';
        }
    }

    if ($input == 'billing_distrito') {
        if (sanitize_text_field(isset($_SESSION['idDist'])) !== null && !empty(sanitize_text_field(isset($_SESSION['idDist'])))) {
            $value = sanitize_text_field(isset($_SESSION['idDist']));
        } else {
            $value = '';
        }
    }

    return $value;
}

add_filter('woocommerce_checkout_get_value', 'clear_checkout_fields', 1, 2);
