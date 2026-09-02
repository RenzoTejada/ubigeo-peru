<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recupera la mejora histórica de Ubigeo en Mi Cuenta > Direcciones.
 * Se mantiene separada del checkout y del calculador de carrito.
 */
add_action('wp_enqueue_scripts', 'rt_ubigeo_account_address_assets', 99);

function rt_ubigeo_account_address_assets()
{
    if (!function_exists('is_account_page') || !is_account_page() || !is_wc_endpoint_url('edit-address')) {
        return;
    }

    wp_enqueue_script(
        'select2-ubigeo-address',
        plugins_url('js/select2.min.js', __FILE__),
        array('jquery'),
        '4.0.1',
        true
    );

    wp_enqueue_script(
        'js-ubigeo-account-address',
        plugins_url('js/js_ubigeo_peru.js', __FILE__),
        array('jquery', 'select2-ubigeo-address'),
        Version_RT_Ubigeo_Peru,
        true
    );

    wp_localize_script(
        'js-ubigeo-account-address',
        'rtUbigeoAddress',
        array(
            'ajaxurl'      => admin_url('admin-ajax.php'),
            'provinceText' => __('Select Province', 'ubigeo-peru'),
            'districtText' => __('Select District', 'ubigeo-peru'),
        )
    );
}

add_filter('woocommerce_billing_fields', 'rt_ubigeo_address_billing_fields');

function rt_ubigeo_address_billing_fields($fields)
{
    if (!is_wc_endpoint_url('edit-address')) {
        return $fields;
    }

    $user_id = get_current_user_id();
    $country = (string) get_user_meta($user_id, 'billing_country', true);

    if ($country === '' && function_exists('WC') && WC()->countries) {
        $country = WC()->countries->get_base_country();
    }

    if ('PE' !== $country) {
        return $fields;
    }

    unset($fields['billing_city'], $fields['billing_state'], $fields['billing_postcode']);
    unset($fields['city'], $fields['state'], $fields['postcode']);

    $idDepa = absint(get_user_meta($user_id, 'billing_departamento', true));
    $idProv = absint(get_user_meta($user_id, 'billing_provincia', true));

    $fields['billing_departamento'] = array(
        'type'     => 'select',
        'label'    => __('Department', 'ubigeo-peru'),
        'required' => true,
        'options'  => rt_ubigeo_get_departamentos_for_adress(),
        'class'    => array('form-row-wide'),
        'priority' => 65,
    );
    $fields['billing_provincia'] = array(
        'type'     => 'select',
        'label'    => __('Province', 'ubigeo-peru'),
        'required' => true,
        'class'    => array('form-row-wide'),
        'options'  => rt_ubigeo_get_provincia_address_by_idDepa($idDepa),
        'priority' => 66,
    );
    $fields['billing_distrito'] = array(
        'type'     => 'select',
        'label'    => __('District', 'ubigeo-peru'),
        'required' => true,
        'class'    => array('form-row-wide'),
        'options'  => rt_ubigeo_get_distrito_address_by_idProv($idProv),
        'priority' => 67,
    );

    return $fields;
}

add_filter('woocommerce_shipping_fields', 'rt_ubigeo_address_shipping_fields');

function rt_ubigeo_address_shipping_fields($fields)
{
    if (!is_wc_endpoint_url('edit-address')) {
        return $fields;
    }

    $user_id = get_current_user_id();
    $country = (string) get_user_meta($user_id, 'shipping_country', true);

    if ($country === '' && function_exists('WC') && WC()->countries) {
        $country = WC()->countries->get_base_country();
    }

    if ('PE' !== $country) {
        return $fields;
    }

    unset($fields['shipping_city'], $fields['shipping_state'], $fields['shipping_postcode']);
    unset($fields['city'], $fields['state'], $fields['postcode']);

    $idDepa = absint(get_user_meta($user_id, 'shipping_departamento', true));
    $idProv = absint(get_user_meta($user_id, 'shipping_provincia', true));

    $fields['shipping_departamento'] = array(
        'type'     => 'select',
        'label'    => __('Department', 'ubigeo-peru'),
        'required' => true,
        'options'  => rt_ubigeo_get_departamentos_for_adress(),
        'class'    => array('form-row-wide'),
        'priority' => 65,
    );
    $fields['shipping_provincia'] = array(
        'type'     => 'select',
        'label'    => __('Province', 'ubigeo-peru'),
        'required' => true,
        'class'    => array('form-row-wide'),
        'options'  => rt_ubigeo_get_provincia_address_by_idDepa($idDepa),
        'priority' => 66,
    );
    $fields['shipping_distrito'] = array(
        'type'     => 'select',
        'label'    => __('District', 'ubigeo-peru'),
        'required' => true,
        'class'    => array('form-row-wide'),
        'options'  => rt_ubigeo_get_distrito_address_by_idProv($idProv),
        'priority' => 67,
    );

    return $fields;
}
