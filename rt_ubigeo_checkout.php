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
            'departamento'  => '#billing_departamento_field, #shipping_departamento_field',
            'provincia'  => '#billing_provincia_field, #shipping_provincia_field',
            'distrito'  => '#billing_distrito_field, #shipping_distrito_field',
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

    $fields['shipping']['shipping_address_1']['priority'] = 74;
    $fields['shipping']['shipping_address_2']['priority'] = 76;
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
            if(empty($data_prov)){
                $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if ($idProv) {
            $data_dist = rt_ubigeo_load_distritos_front_session($idProv);
            if(empty($data_dist)){
                $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist = array('' => __('Select District ', 'ubigeo-peru'));
        }
        
        if ($idDepa_shipping) {
            $data_prov_shipping = rt_ubigeo_load_provincias_front_session($idDepa_shipping);
            if(empty($data_prov_shipping)){
                $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if ($idProv_shipping) {
            $data_dist_shipping = rt_ubigeo_load_distritos_front_session($idProv_shipping);
            if(empty($data_dist_shipping)){
                $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
        }
    } else {
        $idDepa = $idProv = $idDist = $idDepa_shipping = $idProv_shipping = $idDist_shipping = '';

        if (isset($_SESSION['idDepa']) && !empty($_SESSION['idDepa'])) {
            $data_prov = rt_ubigeo_load_provincias_front_session($_SESSION['idDepa']);
            if(empty($data_prov)){
                $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if (isset($_SESSION['idProv']) && !empty($_SESSION['idProv'])) {
            $is_prov = rt_ubigeo_validate_prov_of_depa($_SESSION['idDepa'],$_SESSION['idProv']);
            if($is_prov) {
                $data_dist = rt_ubigeo_load_distritos_front_session($_SESSION['idProv']);
                if(empty($data_dist)){
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
            if(empty($data_prov_shipping)){
                $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
            }
        } else {
            $data_prov_shipping = array('' => __('Select Province ', 'ubigeo-peru'));
        }

        if ($idProv_shipping) {
            $data_dist_shipping = rt_ubigeo_load_distritos_front_session($idProv_shipping);
            if(empty($data_dist_shipping)){
                $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
            }
        } else {
            $data_dist_shipping = array('' => __('Select District ', 'ubigeo-peru'));
        }
    }

    if (count($data_prov) < 2) {
        ?>
            <script>
                    jQuery(document).ready(function () {
                            jQuery("#billing_departamento").select2().select2('val',"0");
                            jQuery("#billing_provincia").select2().select2('val',"0");
                            jQuery("#billing_distrito").select2().select2('val',"0");
                    });
            </script>
                <?php
            } else {
                ?>
            <script>
                    jQuery(document).ready(function () {
                            jQuery("#billing_distrito").select2().select2('val',"0");
                    });
            </script>
        <?php
    }
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



function is_theme_avada()
{
    $rpt = false;
    $theme = wp_get_theme();
    if ('Avada' == $theme->name || 'Avada' == $theme->parent_theme) {
        $rpt = true;
    }
    return $rpt;
}


function is_theme_pawsitive()
{
    $rpt = false;
    $theme = wp_get_theme();
    if ('Pawsitive' == $theme->name || 'Pawsitive' == $theme->parent_theme) {
        $rpt = true;
    }
    return $rpt;
}

add_action('woocommerce_after_checkout_form', 'rt_ubigeo_custom_jscript_checkout');

function rt_ubigeo_custom_jscript_checkout()
{
    wp_register_script('select2-js', plugins_url('js/select2.min.js', __FILE__), array(), '4.0.1', true);
    wp_enqueue_script('select2-js');
    $idDepa = $idProv = $idDist = '';
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $idDepa = $current_user->billing_departamento;
        $idProv = $current_user->billing_provincia;
        $idDist = $current_user->billing_distrito;
        $idDepa_shipping = $current_user->shipping_departamento;
        $idProv_shipping = $current_user->shipping_provincia;
        $idDist_shipping = $current_user->shipping_distrito;
    } ?>
    <?php if (is_theme_pawsitive()) { ?>
    <style>
    .select2-container {
        display : none;
    }
    </style>
    <?php } ?>
    
    <style>
        .woocommerce form .form-row.woocommerce-invalid label {
            color: #a00;
        }
		
		.loader{
                    position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100vh;
                        display: none;
                        justify-content: center;
                        align-items: center;
                        background: #ffffff99;
                        z-index: -1;
		}
		
		 .loader.active {
	        display: flex;
			 z-index: 1000;
		 }
 
		
		.lds-roller {
		  display: inline-block;
		  position: relative;
		  width: 80px;
		  height: 80px;
		}
		.lds-roller div {
		  animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
		  transform-origin: 40px 40px;
		}
		.lds-roller div:after {
		  content: " ";
		  display: block;
		  position: absolute;
		  width: 7px;
		  height: 7px;
		  border-radius: 50%;
		  background: black;
		  margin: -4px 0 0 -4px;
		}
		.lds-roller div:nth-child(1) {
		  animation-delay: -0.036s;
		}
		.lds-roller div:nth-child(1):after {
		  top: 63px;
		  left: 63px;
		}
		.lds-roller div:nth-child(2) {
		  animation-delay: -0.072s;
		}
		.lds-roller div:nth-child(2):after {
		  top: 68px;
		  left: 56px;
		}
		.lds-roller div:nth-child(3) {
		  animation-delay: -0.108s;
		}
		.lds-roller div:nth-child(3):after {
		  top: 71px;
		  left: 48px;
		}
		.lds-roller div:nth-child(4) {
		  animation-delay: -0.144s;
		}
		.lds-roller div:nth-child(4):after {
		  top: 72px;
		  left: 40px;
		}
		.lds-roller div:nth-child(5) {
		  animation-delay: -0.18s;
		}
		.lds-roller div:nth-child(5):after {
		  top: 71px;
		  left: 32px;
		}
		.lds-roller div:nth-child(6) {
		  animation-delay: -0.216s;
		}
		.lds-roller div:nth-child(6):after {
		  top: 68px;
		  left: 24px;
		}
		.lds-roller div:nth-child(7) {
		  animation-delay: -0.252s;
		}
		.lds-roller div:nth-child(7):after {
		  top: 63px;
		  left: 17px;
		}
		.lds-roller div:nth-child(8) {
		  animation-delay: -0.288s;
		}
		.lds-roller div:nth-child(8):after {
		  top: 56px;
		  left: 12px;
		}
		@keyframes lds-roller {
		  0% {
			transform: rotate(0deg);
		  }
		  100% {
			transform: rotate(360deg);
		  }
		}

		
    </style>
	<div class="loader">
		<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>	
	</div>

    <script>
        jQuery(document).ready(function () {
            
            jQuery("#billing_departamento").select2();            
            jQuery("#billing_provincia").select2();
            jQuery("#billing_distrito").select2();
            jQuery("#shipping_departamento").select2();
            jQuery("#shipping_provincia").select2();
            jQuery("#shipping_distrito").select2();
			
            function loader(){
                    jQuery('.loader').toggleClass('active');
            }
			
            <?php if ($idDepa) { ?>
                jQuery("#billing_departamento").val('<?php echo $idDepa ?>').trigger('change');
                jQuery("#billing_provincia").val('<?php echo $idProv ?>').trigger('change');
                jQuery("#billing_distrito").val('<?php echo $idDist ?>').trigger('change');
            <?php } ?>
            <?php if ($idDepa_shipping) { ?>
                jQuery("#shipping_departamento").val('<?php echo $idDepa_shipping ?>').trigger('change');
                jQuery("#shipping_provincia").val('<?php echo $idProv_shipping ?>').trigger('change');
                jQuery("#shipping_distrito").val('<?php echo $idDist_shipping ?>').trigger('change');
            <?php } ?>
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>"
                        
            function rt_ubigeo_event_departamento(select, selectType) {

                var data = {
                    'action': 'rt_ubigeo_load_provincias_front',
                    'idDepa': jQuery(select).val()
                }
				
                loader();
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        <?php if (!is_theme_avada()) { ?>
                        jQuery('#' + selectType + '_provincia').html('<option value="0"><?php _e('Select Province', 'ubigeo-peru') ?></option>')
                        jQuery('#' + selectType + '_distrito').html('<option value="0"><?php _e('Select District', 'ubigeo-peru') ?></option>')
                        <?php } ?>
                        if (response) {
                            for (var r in response) {
                                jQuery('#' + selectType + '_provincia').append('<option value=' + response[r].idProv + '>' + response[r].provincia + '</option>');
                            }
                        }
                        loader();
                    }
                })
            }

            function rt_ubigeo_event_provincia(select, selectType) {
                var data = {
                    'action': 'rt_ubigeo_load_distritos_front',
                    'idProv': jQuery(select).val()
                }
				loader();
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        <?php if (!is_theme_avada()) { ?>
                        jQuery('#' + selectType + '_distrito').html('<option value="0"><?php _e('Select District', 'ubigeo-peru') ?></option>')
                        <?php } ?>
                        if (response) {
                            for (var r in response) {
                                jQuery('#' + selectType + '_distrito').append('<option value=' + response[r].idDist + '>' + response[r].distrito + '</option>')
                            }
                        }
                        loader();
                    }
                });
            }

            jQuery('#billing_departamento').on('change', function () {
                rt_ubigeo_event_departamento(this, 'billing')
            });
            jQuery('#shipping_departamento').on('change', function () {
                rt_ubigeo_event_departamento(this, 'shipping')
            });
            jQuery('#billing_provincia').on('change', function () {
                rt_ubigeo_event_provincia(this, 'billing')
            });
            jQuery('#shipping_provincia').on('change', function () {
                rt_ubigeo_event_provincia(this, 'shipping')
            });
            
            jQuery('#billing_distrito, #shipping_distrito').on('change', function () {
                jQuery(document.body).trigger("update_checkout", {update_shipping_method: true})
            });
            
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
    parse_str($post_data, $data);

    if (array_key_exists('ship_to_different_address',$data)) {
        if (array_key_exists('shipping_distrito',$data)) {
            $_SESSION["idDist"] = $data['shipping_distrito'];
        }
    } else {
         if (array_key_exists('billing_distrito',$data)) {
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
    $ubigeo = [];
    if ($type == 'object') {
        $idDep = get_post_meta($order->id, '_billing_departamento');
        $prov = get_post_meta($order->id, '_billing_provincia');
        $dist = get_post_meta($order->id, '_billing_distrito');
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
    
    return $ubigeo;
}


function rt_show_custom_fields_order_billing($order)
{
    $ubigeo_billing = get_name_ubigeo_billing($order->get_id(), 'value');
    if ($ubigeo_billing) {
        echo '<div class="ubigeo_data_column">';
        echo '<h3>'.__('Billing Ubigeo Peru', 'ubigeo-peru').'</h3>';
        echo '<p><strong>' . __('Department','ubigeo-peru') . ':</strong> ' . $ubigeo_billing['departamento'] . '</p>';
        echo '<p><strong>' . __('Province','ubigeo-peru') . ':</strong> ' . $ubigeo_billing['provincia'] . '</p>';
        echo '<p><strong>' . __('District','ubigeo-peru') . ':</strong> ' . $ubigeo_billing['distrito'] . '</p>';
        echo '</div>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'rt_show_custom_fields_order_billing', 1);

function rt_show_custom_fields_order_shipping($order)
{
    $ubigeo_shipping = get_name_ubigeo_shipping($order->get_id(), 'value');
    
    if ($ubigeo_shipping) {
        echo '<div class="ubigeo_data_column">';
        echo '<h3>'.__('Shipping Ubigeo Peru', 'ubigeo-peru').'</h3>';
        echo '<p><strong>' . __('Department','ubigeo-peru') . ':</strong> ' . $ubigeo_shipping['departamento'] . '</p>';
        echo '<p><strong>' . __('Province','ubigeo-peru') . ':</strong> ' . $ubigeo_shipping['provincia'] . '</p>';
        echo '<p><strong>' . __('District','ubigeo-peru') . ':</strong> ' . $ubigeo_shipping['distrito'] . '</p>';
        echo '</div>';
    }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'rt_show_custom_fields_order_shipping', 1);

function rt_show_custom_fields_thankyou($order)
{
    echo '<section class="woocommerce-customer-details">';
    echo '<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">';
    $ubigeo = get_name_ubigeo_billing($order, 'value');
    if ($ubigeo) {
        echo '<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">';
        echo '<h2 class="woocommerce-column__title">'.__('Billing Ubigeo Peru', 'ubigeo-peru').'</h2>';
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . $ubigeo['departamento'] . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . $ubigeo['provincia'] . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . $ubigeo['distrito'] . '</p>';
        echo '</div>';
        echo '</address>';
    }
    
    $ubigeo_shipping = get_name_ubigeo_shipping($order, 'value');
    
    if ($ubigeo_shipping) {
        echo '<div class="woocommerce-column woocommerce-column--2 woocommerce-column--billing-address col-2">';
        echo '<h2 class="woocommerce-column__title">'.__('Shipping Ubigeo Peru', 'ubigeo-peru').'</h2>';
        echo '<address>';
        echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . $ubigeo_shipping['departamento'] . '</p>';
        echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . $ubigeo_shipping['provincia'] . '</p>';
        echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' . $ubigeo_shipping['distrito'] . '</p>';
        echo '</div>';
        echo '</address>';
    }
    echo '</section>';
    echo '</section>';
}
add_action('woocommerce_thankyou', 'rt_show_custom_fields_thankyou', 20);
add_action('woocommerce_view_order', 'rt_show_custom_fields_thankyou', 20);

function rt_show_custom_fields_emails($orden, $sent_to_admin, $order)
{
    $ubigeo = get_name_ubigeo_billing($order, 'object');
    echo '<h2 class="woocommerce-order-details__title">'.__('Ubigeo Peru', 'ubigeo-peru').'</h2>';
    echo '<address>';
    echo '<p><strong>' . __('Department', 'ubigeo-peru') . ':</strong> ' . $ubigeo['departamento'] . '</p>';
    echo '<p><strong>' . __('Province', 'ubigeo-peru') . ':</strong> ' . $ubigeo['provincia'] . '</p>';
    echo '<p><strong>' . __('District', 'ubigeo-peru') . ':</strong> ' .$ubigeo['distrito'] . '</p>';
    echo '</address>';
}
add_action('woocommerce_email_order_meta_fields', 'rt_show_custom_fields_emails', 10, 3);


function clear_checkout_fields($value, $input)
{
    if ($input == 'billing_departamento') {
        if (isset($_SESSION['idDepa']) && !empty($_SESSION['idDepa'])) {
            $value = $_SESSION['idDepa'];
        } else {
            $value = '';
        }
    }
    
    if ($input == 'billing_provincia') {
        if (isset($_SESSION['idProv']) && !empty($_SESSION['idProv'])) {
            $value = $_SESSION['idProv'];
        } else {
            $value = '';
        }
    }
    
    if ($input == 'billing_distrito') {
        if (isset($_SESSION['idDist']) && !empty($_SESSION['idDist'])) {
            $value = $_SESSION['idDist'];
        } else {
            $value = '';
        }
    }

    return $value;
}
add_filter('woocommerce_checkout_get_value', 'clear_checkout_fields', 1, 2);
