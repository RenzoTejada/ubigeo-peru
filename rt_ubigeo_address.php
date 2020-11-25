<?php
add_filter('woocommerce_billing_fields', 'rt_ubigeo_address_billing_fields');

function rt_ubigeo_address_billing_fields($fields) {

    if (is_wc_endpoint_url( 'edit-address' )) {
        ?>
        <script>
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            var name_province = "<?php _e('Select Province', 'ubigeo-peru') ?>";
            var name_distrito = "<?php _e('Select District', 'ubigeo-peru') ?>";
        </script>
        <?php
        wp_register_script('jquery.3.0.0.min', plugins_url('js/jquery.3.0.0.min.js', __FILE__), array(), '3.0.0');
        wp_enqueue_script('jquery.3.0.0.min');
        wp_register_script('js_ubigeo_peru', plugins_url('js/js_ubigeo_peru.js', __FILE__), array(), '0.0.3');
        wp_enqueue_script('js_ubigeo_peru');

        if ('PE' === get_user_meta(get_current_user_id(), 'billing_country', true)) {
            unset($fields['city']);
            unset($fields['state']);
            unset($fields['postcode']);
            $idDepa = get_user_meta(get_current_user_id(), 'billing_departamento', true);
            $idProv = get_user_meta(get_current_user_id(), 'billing_provincia', true);
            $idDist = get_user_meta(get_current_user_id(), 'billing_distrito', true);

            $fields['billing_departamento'] = array(
                'type' => 'select',
                'label' => __('Department', 'ubigeo-peru'),
                'placeholder' => __('Department', 'placeholder', 'woocommerce'),
                'required' => false,
                'options' => rt_ubigeo_get_departamentos_for_adress(),
                'class' => array('form-row-wide'),
                'priority' => 65
            );
            $fields['billing_provincia'] = array(
                'type' => 'select',
                'label' => __('Province', 'ubigeo-peru'),
                'placeholder' => __('Department', 'placeholder', 'woocommerce'),
                'required' => false,
                'class' => array('form-row-wide'),
                'options' => rt_ubigeo_get_provincia_address_by_idDepa($idDepa),
                'priority' => 66
            );
            $fields['billing_distrito'] = array(
                'type' => 'select',
                'label' => __('District', 'ubigeo-peru'),
                'placeholder' => __('Department', 'placeholder', 'woocommerce'),
                'required' => false,
                'class' => array('form-row-wide'),
                'options' => rt_ubigeo_get_distrito_address_by_idProv($idProv),
                'priority' => 67
            );
        }
    }

    return $fields;
}

add_filter('woocommerce_shipping_fields', 'rt_ubigeo_address_shipping_fields');

function rt_ubigeo_address_shipping_fields($fields) {
    if (is_wc_endpoint_url( 'edit-address' )) {
        ?>
        <script>
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            var name_province = "<?php _e('Select Province', 'ubigeo-peru') ?>";
            var name_distrito = "<?php _e('Select District', 'ubigeo-peru') ?>";
        </script>
        <?php
        wp_register_script('jquery.3.0.0.min', plugins_url('js/jquery.3.0.0.min.js', __FILE__), array(), '3.0.0');
        wp_enqueue_script('jquery.3.0.0.min');
        wp_register_script('js_ubigeo_peru', plugins_url('js/js_ubigeo_peru.js', __FILE__), array(), '0.0.4');
        wp_enqueue_script('js_ubigeo_peru');


        if ('PE' === get_user_meta(get_current_user_id(), 'shipping_country', true)) {
            unset($fields['city']);
            unset($fields['state']);
            unset($fields['postcode']);
            $idDepa = get_user_meta(get_current_user_id(), 'shipping_departamento', true);
            $idProv = get_user_meta(get_current_user_id(), 'shipping_provincia', true);
            $idDist = get_user_meta(get_current_user_id(), 'shipping_distrito', true);

            $fields['shipping_departamento'] = array(
                'type' => 'select',
                'label' => __('Department', 'ubigeo-peru'),
                'placeholder' => __('Department', 'placeholder', 'woocommerce'),
                'required' => false,
                'options' => rt_ubigeo_get_departamentos_for_adress(),
                'class' => array('form-row-wide'),
                'priority' => 65
            );
            $fields['shipping_provincia'] = array(
                'type' => 'select',
                'label' => __('Province', 'ubigeo-peru'),
                'placeholder' => __('Department', 'placeholder', 'woocommerce'),
                'required' => false,
                'class' => array('form-row-wide'),
                'options' => rt_ubigeo_get_provincia_address_by_idDepa($idDepa),
                'priority' => 66
            );
            $fields['shipping_distrito'] = array(
                'type' => 'select',
                'label' => __('District', 'ubigeo-peru'),
                'placeholder' => __('Department', 'placeholder', 'woocommerce'),
                'required' => false,
                'class' => array('form-row-wide'),
                'options' => rt_ubigeo_get_distrito_address_by_idProv($idProv),
                'priority' => 67
            );
        }
    }

    return $fields;
}
