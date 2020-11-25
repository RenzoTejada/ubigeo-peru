<?php
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
    $fields['billing']['billing_address_2']['priority'] = 76;

    $fields['shipping']['shipping_phone']['priority'] = 34;
    $fields['shipping']['shipping_email']['priority'] = 36;
    $fields['shipping']['shipping_address_1']['priority'] = 74;
    $fields['shipping']['shipping_address_2']['priority'] = 76;

    $fields['billing']['billing_departamento'] = [
        'type' => 'select',
        'label' => 'Departamento',
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => rt_ubigeo_get_departamentos_for_select(),
        'priority' => 65
    ];

    $fields['billing']['billing_provincia'] = [
        'type' => 'select',
        'label' => 'Provincia',
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => [
            '' => 'Seleccionar Provincia',
        ],
        'priority' => 66
    ];

    $fields['billing']['billing_distrito'] = [
        'type' => 'select',
        'label' => 'Distrito',
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => [
            '' => 'Seleccionar Distrito',
        ],
        'priority' => 67
    ];

    $fields['shipping']['shipping_departamento'] = [
        'type' => 'select',
        'label' => 'Departamento',
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => rt_ubigeo_get_departamentos_for_select(),
        'priority' => 65
    ];

    $fields['shipping']['shipping_provincia'] = [
        'type' => 'select',
        'label' => 'Provincia',
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => [
            '' => 'Seleccionar Provincia',
        ],
        'priority' => 66
    ];

    $fields['shipping']['shipping_distrito'] = [
        'type' => 'select',
        'label' => 'Distrito',
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'options' => [
            '' => 'Seleccionar Distrito',
        ],
        'priority' => 67
    ];

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
    return '0';
}

add_filter('woocommerce_default_address_fields', 'rt_ubigeo_custom_wc_default_address_fields');

function rt_ubigeo_custom_wc_default_address_fields($address_fields)
{
    $address_fields['phone']['priority'] = 34;
    $address_fields['email']['priority'] = 36;
    $address_fields['address_1']['priority'] = 74;
    $address_fields['address_2']['priority'] = 76;
    return $address_fields;
}


function is_theme_avada()
{
    $rpt = false;
    $theme = wp_get_theme();
    if ('Avada' == $theme->name || 'Avada' == $theme->parent_theme) {
       $rpt = true;
    }
    return $rpt;
}

add_action('woocommerce_after_checkout_form', 'rt_ubigeo_custom_jscript_checkout');

function rt_ubigeo_custom_jscript_checkout()
{
    wp_register_script('select2-js', plugins_url('js/select2.min.js', __FILE__), array(), '4.0.1', true);
    wp_enqueue_script('select2-js');
    

    ?>
    <script>
        jQuery(document).ready(function () {

            jQuery("#billing_departamento").select2();
            jQuery("#billing_provincia").select2();
            jQuery("#billing_distrito").select2();
            jQuery("#shipping_departamento").select2();
            jQuery("#shipping_provincia").select2();
            jQuery("#shipping_distrito").select2();

            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>"

            function rt_ubigeo_event_departamento(select, selectType) {

                var data = {
                    'action': 'rt_ubigeo_load_provincias_front',
                    'idDepa': jQuery(select).val()
                }

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    beforeSend: function (xhr, settings) {
                        jQuery('form.woocommerce-checkout').addClass('processing').block({
                            message: null,
                            overlayCSS: {
                                background: '#fff',
                                opacity: 0.6
                            }
                        });
                    },
                    success: function (response) {
                        <?php if(!is_theme_avada()) { ?>
                        jQuery('#' + selectType + '_provincia').html('<option value="">Seleccionar Provincia</option>')
                        jQuery('#' + selectType + '_distrito').html('<option value="">Seleccionar Distrito</option>')
                        <?php } ?>
                        if (response) {
                            for (var r in response) {
                                jQuery('#' + selectType + '_provincia').append('<option value=' + r + '>' + response[r] + '</option>');
                            }
                        }
                    },
                    complete: function (xhr, ts) {
                        jQuery('form.woocommerce-checkout').removeClass('processing').unblock()
                    }
                })
            }

            function rt_ubigeo_event_provincia(select, selectType) {
                var data = {
                    'action': 'rt_ubigeo_load_distritos_front',
                    'idProv': jQuery(select).val()
                }

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    beforeSend: function (xhr, settings) {
                        jQuery('form.woocommerce-checkout').addClass('processing').block({
                            message: null,
                            overlayCSS: {
                                background: '#fff',
                                opacity: 0.6
                            }
                        });
                    },
                    success: function (response) {
                        <?php if(!is_theme_avada()) { ?>
                        jQuery('#' + selectType + '_distrito').html('<option value="">Seleccionar Distrito</option>')
                        <?php } ?>
                        if (response) {
                            for (var r in response) {
                                jQuery('#' + selectType + '_distrito').append('<option value=' + r + '>' + response[r] + '</option>')
                            }
                        }
                    },
                    complete: function (xhr, ts) {
                        jQuery('form.woocommerce-checkout').removeClass('processing').unblock()
                    }
                })
            }

            jQuery('#billing_departamento').on('change', function () {
                rt_ubigeo_event_departamento(this, 'billing')
            })
            jQuery('#shipping_departamento').on('change', function () {
                rt_ubigeo_event_departamento(this, 'shipping')
            })

            jQuery('#billing_provincia').on('change', function () {
                rt_ubigeo_event_provincia(this, 'billing')
            })
            jQuery('#shipping_provincia').on('change', function () {
                rt_ubigeo_event_provincia(this, 'shipping')
            })

            jQuery('#billing_distrito, #shipping_distrito').on('change', function () {
                jQuery(document.body).trigger("update_checkout", {update_shipping_method: true})
            })

            jQuery('#billing_country').on('change', function () {
                jQuery('#billing_departamento').val('').trigger('change');
                jQuery('#billing_provincia').val('').trigger('change');
                jQuery('#billing_distrito').val('').trigger('change');
            });
        });
       
    </script>
    <?php
}

add_action('woocommerce_checkout_update_order_review', 'rt_ubigeo_checkout_update_refresh_shipping_methods', 10, 1);

function rt_ubigeo_checkout_update_refresh_shipping_methods($post_data)
{
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
            $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Billing Departamento') . '</strong>'), 'Billing Departamento'));
        }
        if ('' === $fields['billing_provincia']) {
            $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Billing Provincia') . '</strong>'), 'Billing Provincia'));
        }
        if ('' === $fields['billing_distrito']) {
            $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Billing Distrito') . '</strong>'), 'Billing Distrito'));
        }
    }

    if (1 == $fields['ship_to_different_address']) {
        if ('PE' === $fields['shipping_country']) {
            if ('' === $fields['shipping_departamento']) {
                $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Shipping Departamento') . '</strong>'), 'Shipping Departamento'));
            }
            if ('' === $fields['shipping_provincia']) {
                $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Shipping Provincia') . '</strong>'), 'Shipping Provincia'));
            }
            if ('' === $fields['shipping_distrito']) {
                $errors->add('required-field', apply_filters('woocommerce_checkout_required_field_notice', sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html('Shipping Distrito') . '</strong>'), 'Shipping Distrito'));
            }
        }
    }
}

add_action( 'woocommerce_checkout_process', 'rt_remove_wc_validation', 1 );
function rt_remove_wc_validation () {
	remove_action( 'woocommerce_checkout_process', 'some_custom_checkout_field_process' );
}

function get_name_ubigeo_billing($order, $type = 'object')
{
    if ($type == 'object') {
        $idDep = get_post_meta($order->id, '_billing_departamento');
        $prov = get_post_meta($order->id, '_billing_provincia');
        $dist = get_post_meta($order->id, '_billing_distrito');
    } else {
        $idDep = get_post_meta($order, '_billing_departamento');
        $prov = get_post_meta($order, '_billing_provincia');
        $dist = get_post_meta($order, '_billing_distrito');
    }
    $ubigeo['departamento'] = rt_ubigeo_get_departamento_por_id($idDep[0])['departamento'];
    $ubigeo['provincia'] = rt_ubigeo_get_provincia_por_id($prov[0])['provincia'];
    $ubigeo['distrito'] = rt_ubigeo_get_distrito_por_id($dist[0])['distrito'];
    
    return $ubigeo;
}

function get_name_ubigeo_shipping($order, $type = 'object')
{
    if ($type == 'object') {
        $idDep = get_post_meta($order->id, '_billing_departamento');
        $prov = get_post_meta($order->id, '_billing_provincia');
        $dist = get_post_meta($order->id, '_billing_distrito');
    } else {
        $idDep = get_post_meta($order, '_shipping_departamento');
        $prov = get_post_meta($order, '_shipping_provincia');
        $dist = get_post_meta($order, '_shipping_distrito');
    }
    $ubigeo['departamento'] = rt_ubigeo_get_departamento_por_id($idDep[0])['departamento'];
    $ubigeo['provincia'] = rt_ubigeo_get_provincia_por_id($prov[0])['provincia'];
    $ubigeo['distrito'] = rt_ubigeo_get_distrito_por_id($dist[0])['distrito'];
    
    return $ubigeo;
}


function rt_show_custom_fields_order_billing($order)
{
    $ubigeo_billing = get_name_ubigeo_billing($order->get_id(), 'value');
    if ($ubigeo_billing) {
        echo '<div class="ubigeo_data_column">';
        echo '<h3>Billing Ubigeo Perú</h3>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . $ubigeo_billing['departamento'] . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . $ubigeo_billing['provincia'] . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . $ubigeo_billing['distrito'] . '</p>';
        echo '</div>';
    }
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'rt_show_custom_fields_order_billing', 1 );

function rt_show_custom_fields_order_shipping($order)
{
    $ubigeo_shipping = get_name_ubigeo_shipping($order->get_id(), 'value');
    if ($ubigeo_shipping) {
        echo '<div class="ubigeo_data_column">';
        echo '<h3>Shipping Ubigeo Perú</h3>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . $ubigeo_shipping['departamento'] . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . $ubigeo_shipping['provincia'] . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . $ubigeo_shipping['distrito'] . '</p>';
        echo '</div>';
    }
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'rt_show_custom_fields_order_shipping', 1 );

function rt_show_custom_fields_thankyou($order) 
{
    echo '<section class="woocommerce-customer-details">';
    echo '<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">';
    $ubigeo = get_name_ubigeo_billing($order,'value');
    if ($ubigeo) {
        echo '<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">';
        echo '<h2 class="woocommerce-column__title">Billing Ubigeo Perú</h2>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . $ubigeo['departamento'] . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . $ubigeo['provincia'] . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . $ubigeo['distrito'] . '</p>';
        echo '</div>';
    }

    $ubigeo_shipping = get_name_ubigeo_shipping($order, 'value');
    if ($ubigeo_shipping) {
        echo '<div class="woocommerce-column woocommerce-column--2 woocommerce-column--billing-address col-1">';
        echo '<h2 class="woocommerce-column__title">Shipping Ubigeo Perú</h2>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . $ubigeo_shipping['departamento'] . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . $ubigeo_shipping['provincia'] . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . $ubigeo_shipping['distrito'] . '</p>';
        echo '</div>';
    }
    echo '</section>';
    echo '</section>';
}
add_action( 'woocommerce_thankyou', 'rt_show_custom_fields_thankyou', 20 );
add_action( 'woocommerce_view_order', 'rt_show_custom_fields_thankyou', 20 );

function rt_show_custom_fields_emails($orden, $sent_to_admin, $order) 
{
    $ubigeo = get_name_ubigeo_billing($order,'object');
    echo '<h2 class="woocommerce-order-details__title">Ubigeo Perú</h2>';
    echo '<p><strong>' . __('Departamento') . ':</strong> ' . $ubigeo['departamento'] . '</p>';
    echo '<p><strong>' . __('Provincia') . ':</strong> ' . $ubigeo['provincia'] . '</p>';
    echo '<p><strong>' . __('Distrito') . ':</strong> ' .$ubigeo['distrito'] . '</p>';
//    if (get_post_meta($order->id, 'shipping_departamento', true)) {
//        echo '<h2 class="woocommerce-order-details__title">Shipping Address</h2>';
//        echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order->id, 'shipping_departamento', true) . '</p>';
//        echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order->id, 'shipping_provincia', true) . '</p>';
//        echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order->id, 'shipping_distrito', true) . '</p>';
//    }
}
add_action( 'woocommerce_email_order_meta_fields', 'rt_show_custom_fields_emails', 10 , 3 );

