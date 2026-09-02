<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Compatibility bridge between Ubigeo Peru's custom IDs and WooCommerce's
 * standard address properties.
 *
 * Ubigeo keeps the canonical IDs in:
 * - billing/shipping_departamento
 * - billing/shipping_provincia
 * - billing/shipping_distrito
 *
 * At the same time, WooCommerce and payment gateways can consume readable
 * standard values:
 * - state = Province name
 * - city  = District name
 *
 * This is intentionally gateway-agnostic: it benefits Culqi, Izipay and any
 * integration that reads the normal WooCommerce address fields.
 */

/**
 * Resolve Province/District display names from Ubigeo IDs.
 */
function rt_ubigeo_standard_address_names($departamento_id, $provincia_id, $distrito_id)
{
    $departamento_id = absint($departamento_id);
    $provincia_id    = absint($provincia_id);
    $distrito_id     = absint($distrito_id);

    if (!$provincia_id || !$distrito_id) {
        return array('state' => '', 'city' => '');
    }

    // If the complete chain is available, reject inconsistent combinations.
    if ($departamento_id && function_exists('rt_ubigeo_is_valid_location')) {
        if (!rt_ubigeo_is_valid_location($departamento_id, $provincia_id, $distrito_id)) {
            return array('state' => '', 'city' => '');
        }
    }

    $province = function_exists('rt_ubigeo_get_provincia_por_id')
        ? rt_ubigeo_get_provincia_por_id($provincia_id)
        : array();
    $district = function_exists('rt_ubigeo_get_distrito_por_id')
        ? rt_ubigeo_get_distrito_por_id($distrito_id)
        : array();

    return array(
        'state' => (is_array($province) && !empty($province['provincia']))
            ? sanitize_text_field($province['provincia'])
            : '',
        'city' => (is_array($district) && !empty($district['distrito']))
            ? sanitize_text_field($district['distrito'])
            : '',
    );
}

/**
 * Resolve standard address names from an array containing checkout fields.
 */
function rt_ubigeo_standard_address_from_data($type, $data)
{
    $type = ('shipping' === $type) ? 'shipping' : 'billing';
    $data = is_array($data) ? $data : array();

    $country = isset($data[$type . '_country'])
        ? wc_clean(wp_unslash($data[$type . '_country']))
        : 'PE';

    if ('PE' !== $country) {
        return array('state' => '', 'city' => '');
    }

    return rt_ubigeo_standard_address_names(
        $data[$type . '_departamento'] ?? 0,
        $data[$type . '_provincia'] ?? 0,
        $data[$type . '_distrito'] ?? 0
    );
}

/**
 * Fill WooCommerce standard posted fields without altering Ubigeo ID fields.
 */
function rt_ubigeo_sync_standard_checkout_posted_data($data)
{
    if (!is_array($data)) {
        return $data;
    }

    $billing = rt_ubigeo_standard_address_from_data('billing', $data);
    if ($billing['state'] !== '') {
        $data['billing_state'] = $billing['state'];
    }
    if ($billing['city'] !== '') {
        $data['billing_city'] = $billing['city'];
    }

    $shipping = rt_ubigeo_standard_address_from_data('shipping', $data);
    if ($shipping['state'] !== '') {
        $data['shipping_state'] = $shipping['state'];
    }
    if ($shipping['city'] !== '') {
        $data['shipping_city'] = $shipping['city'];
    }

    return $data;
}
add_filter('woocommerce_checkout_posted_data', 'rt_ubigeo_sync_standard_checkout_posted_data', 5);

/**
 * Keep $_POST in sync for third-party gateway code that reads the raw checkout
 * request instead of the WC_Order / WC_Customer APIs.
 */
function rt_ubigeo_sync_standard_checkout_superglobal($data, $errors)
{
    unset($errors);

    $data = rt_ubigeo_sync_standard_checkout_posted_data($data);

    foreach (array('billing_state', 'billing_city', 'shipping_state', 'shipping_city') as $key) {
        if (isset($data[$key]) && '' !== $data[$key]) {
            $_POST[$key] = wc_clean(wp_unslash($data[$key]));
        }
    }
}
add_action('woocommerce_after_checkout_validation', 'rt_ubigeo_sync_standard_checkout_superglobal', 1, 2);

/**
 * Persist readable state/city values in the order using WooCommerce setters,
 * which keeps this compatible with HPOS and classic order storage.
 */
function rt_ubigeo_sync_standard_order_address($order, $data)
{
    if (!($order instanceof WC_Order) || !is_array($data)) {
        return;
    }

    $billing = rt_ubigeo_standard_address_from_data('billing', $data);
    if ($billing['state'] !== '') {
        $order->set_billing_state($billing['state']);
    }
    if ($billing['city'] !== '') {
        $order->set_billing_city($billing['city']);
    }

    $shipping = rt_ubigeo_standard_address_from_data('shipping', $data);
    if ($shipping['state'] !== '') {
        $order->set_shipping_state($shipping['state']);
    }
    if ($shipping['city'] !== '') {
        $order->set_shipping_city($shipping['city']);
    }
}
add_action('woocommerce_checkout_create_order', 'rt_ubigeo_sync_standard_order_address', 50, 2);

/**
 * Persist standard fields in the customer's saved address after checkout.
 */
function rt_ubigeo_sync_standard_customer_address_checkout($customer, $data)
{
    if (!($customer instanceof WC_Customer) || !is_array($data)) {
        return;
    }

    $billing = rt_ubigeo_standard_address_from_data('billing', $data);
    if ($billing['state'] !== '') {
        $customer->set_billing_state($billing['state']);
    }
    if ($billing['city'] !== '') {
        $customer->set_billing_city($billing['city']);
    }

    $shipping = rt_ubigeo_standard_address_from_data('shipping', $data);
    if ($shipping['state'] !== '') {
        $customer->set_shipping_state($shipping['state']);
    }
    if ($shipping['city'] !== '') {
        $customer->set_shipping_city($shipping['city']);
    }
}
add_action('woocommerce_checkout_update_customer', 'rt_ubigeo_sync_standard_customer_address_checkout', 50, 2);

/**
 * When an address is edited from My Account (or the admin customer profile),
 * backfill state/city from the Ubigeo IDs that were just saved.
 */
function rt_ubigeo_sync_standard_customer_saved_address($user_id, $address_type, $address = array(), $customer = null)
{
    $user_id      = absint($user_id);
    $address_type = ('shipping' === $address_type) ? 'shipping' : 'billing';

    if (!$user_id) {
        return;
    }

    $country = (string) get_user_meta($user_id, $address_type . '_country', true);
    if ($country !== '' && 'PE' !== $country) {
        return;
    }

    $names = rt_ubigeo_standard_address_names(
        get_user_meta($user_id, $address_type . '_departamento', true),
        get_user_meta($user_id, $address_type . '_provincia', true),
        get_user_meta($user_id, $address_type . '_distrito', true)
    );

    if ($names['state'] === '' && $names['city'] === '') {
        return;
    }

    if (!($customer instanceof WC_Customer)) {
        $customer = new WC_Customer($user_id);
    }

    if ('billing' === $address_type) {
        if ($names['state'] !== '') {
            $customer->set_billing_state($names['state']);
        }
        if ($names['city'] !== '') {
            $customer->set_billing_city($names['city']);
        }
    } else {
        if ($names['state'] !== '') {
            $customer->set_shipping_state($names['state']);
        }
        if ($names['city'] !== '') {
            $customer->set_shipping_city($names['city']);
        }
    }

    $customer->save();
}
add_action('woocommerce_customer_save_address', 'rt_ubigeo_sync_standard_customer_saved_address', 50, 4);

/**
 * Lazy backfill for recurrent customers with old Ubigeo data: when they enter
 * checkout, ensure the standard WooCommerce address properties already contain
 * readable Province/District values before any payment plugin runs.
 */
function rt_ubigeo_backfill_current_customer_standard_address()
{
    if (!is_user_logged_in() || !function_exists('is_checkout') || !is_checkout() || is_order_received_page()) {
        return;
    }

    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }

    $customer = new WC_Customer($user_id);
    $changed  = false;

    foreach (array('billing', 'shipping') as $type) {
        $location = function_exists('rt_ubigeo_get_customer_saved_location')
            ? rt_ubigeo_get_customer_saved_location($type, $user_id)
            : array();

        if (empty($location['provincia']) || empty($location['distrito'])) {
            continue;
        }

        $names = rt_ubigeo_standard_address_names(
            $location['departamento'] ?? 0,
            $location['provincia'] ?? 0,
            $location['distrito'] ?? 0
        );

        if ('billing' === $type) {
            if ($names['state'] !== '' && $customer->get_billing_state() !== $names['state']) {
                $customer->set_billing_state($names['state']);
                $changed = true;
            }
            if ($names['city'] !== '' && $customer->get_billing_city() !== $names['city']) {
                $customer->set_billing_city($names['city']);
                $changed = true;
            }
        } else {
            if ($names['state'] !== '' && $customer->get_shipping_state() !== $names['state']) {
                $customer->set_shipping_state($names['state']);
                $changed = true;
            }
            if ($names['city'] !== '' && $customer->get_shipping_city() !== $names['city']) {
                $customer->set_shipping_city($names['city']);
                $changed = true;
            }
        }
    }

    if ($changed) {
        $customer->save();
    }
}
add_action('wp', 'rt_ubigeo_backfill_current_customer_standard_address', 20);
