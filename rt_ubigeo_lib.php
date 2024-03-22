<?php

function rt_ubigeo_get_departamentos_for_select()
{
    $dptos = [
        '' => __('Select Department ', 'ubigeo-peru')
    ];

    if (!rt_plugin_ubigeo_costo_enabled()) {
        $departamentoList = rt_ubigeo_get_departamento();
    } else {
        $departamentoList = rt_ubigeo_get_departamento_display();
    }

    foreach ($departamentoList as $dpto) {
        $dptos[$dpto['idDepa']] = $dpto['departamento'];
    }
    
    return $dptos;
}

function rt_ubigeo_get_departamentos_for_adress()
{
    $dptos = [
        '' => __('Select Department ', 'ubigeo-peru')
    ];

    $departamentoList = rt_ubigeo_get_departamento();

    foreach ($departamentoList as $dpto) {
        $dptos[$dpto['idDepa']] = $dpto['departamento'];
    }
    return $dptos;
}

function rt_ubigeo_get_departamento_display()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $table_display = $wpdb->prefix . "ubigeo_display";
    $request = $wpdb->prepare("SELECT * FROM $table_name as dep inner join $table_display as dis on dis.idDepa=dep.idDepa order by dep.departamento asc");
    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_get_departamento()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = $wpdb->prepare("SELECT * FROM $table_name");

    return $wpdb->get_results($request, ARRAY_A);
}

add_action('wp_ajax_rt_ubigeo_load_provincias_front', 'rt_ubigeo_load_provincias_front');
add_action('wp_ajax_nopriv_rt_ubigeo_load_provincias_front', 'rt_ubigeo_load_provincias_front');

function rt_ubigeo_load_provincias_front()
{
    session_start();
    $idDepa = sanitize_text_field($_POST['idDepa']) !== null ? sanitize_text_field($_POST['idDepa']) : null;
    $_SESSION["idDepa"] = $idDepa;
    $response = $provincias = [];

    if (is_numeric($idDepa)) {
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $provincias = rt_ubigeo_get_provincia_by_idDepa($idDepa);
        } else {
            $provincias = rt_ubigeo_get_provincia_by_idDepa_display($idDepa);
        }
    }
    echo json_encode($provincias);
    wp_die();
}

function rt_ubigeo_get_provincia_by_idDepa($idDepa = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = $wpdb->prepare("SELECT idProv, provincia FROM $table_name where idDepa =%d  order by provincia asc",sanitize_text_field($idDepa));

    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_get_provincia_by_idDepa_display($idDepa = 0)
{
    global $wpdb;
    $table_costo_ubigeo = $wpdb->prefix . "ubigeo_costo_ubigeo";
    $table_ubigeo_provincia = $wpdb->prefix . "ubigeo_provincia";
    $result = array();
    if($idDepa > 0){
        $tipo = get_tipo_costo_ubigeo_by_idDepa($idDepa);

        if (isset($tipo['tipo']) == 1) {
            $result = rt_ubigeo_get_provincia_by_idDepa($idDepa);
        } else {
            $request = $wpdb->prepare("SELECT up.idProv, up.provincia FROM $table_costo_ubigeo  as ucu  
                    inner join $table_ubigeo_provincia as up on up.idProv=ucu.idProv
                    where ucu.idDepa=%d group by up.idProv order by up.provincia",$idDepa);
            $result = $wpdb->get_results($request, ARRAY_A);
        }
    }

    return $result;
}

function rt_plugin_ubigeo_costo_enabled()
{
    if (in_array('costo-ubigeo-peru/costo-ubigeo-peru.php', (array) get_option('active_plugins', array()))) {
        return true;
    }
    return false;
}

add_action('wp_ajax_rt_ubigeo_load_distritos_front', 'rt_ubigeo_load_distritos_front');
add_action('wp_ajax_nopriv_rt_ubigeo_load_distritos_front', 'rt_ubigeo_load_distritos_front');

function rt_ubigeo_load_distritos_front()
{
//    session_start();
    $idProv = sanitize_text_field($_POST['idProv']) !== null ? sanitize_text_field($_POST['idProv']) : null;
//    $_SESSION["idProv"] = $idProv;
    $distritos = [];
    if (is_numeric($idProv)) {
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $distritos = rt_ubigeo_get_distrito_by_idProv($idProv);
        } else {
            $distritos = rt_ubigeo_get_distrito_by_idProv_display($idProv);
        }
    }
    echo json_encode($distritos);
    wp_die();
}

function rt_ubigeo_get_distrito_by_idProv($idProv = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = $wpdb->prepare("SELECT * FROM $table_name where idProv = %d order by distrito asc",sanitize_text_field($idProv));

    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_validate_prov_of_depa($idDepa, $idProv)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = $wpdb->prepare("SELECT * FROM $table_name where idProv =%d and idDepa =%d", sanitize_text_field($idProv), sanitize_text_field($idDepa));

    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_get_distrito_by_idProv_display($idProv = 0)
{
    global $wpdb;
    $table_costo_ubigeo = $wpdb->prefix . "ubigeo_costo_ubigeo";
    $table_ubigeo_distrito = $wpdb->prefix . "ubigeo_distrito";
    $tipo = get_tipo_costo_ubigeo_by_idProv($idProv);

    if ($tipo['tipo'] == 1) {
        $result = rt_ubigeo_get_distrito_by_idProv($idProv);
    } else {
        $request = $wpdb->prepare( "SELECT dist.idDist, dist.distrito  FROM $table_costo_ubigeo  as ucu  
        inner join $table_ubigeo_distrito as dist on dist.idDist=ucu.idDist
        where ucu.idProv=%d group by dist.idDist order by dist.distrito ASC",$idProv);

        $result = $wpdb->get_results($request, ARRAY_A);
    }
    return $result;
}

function rt_costo_ubigeo_plugin_enabled()
{
    if (in_array('costo-ubigeo-peru/costo-ubigeo-peru.php', (array) get_option('active_plugins', array()))) {
        return true;
    }
    return false;
}


function rt_ubigeo_get_departamento_por_id($idDep)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = $wpdb->prepare("SELECT departamento FROM ". $table_name ." where idDepa =%d",sanitize_text_field($idDep));
    return $wpdb->get_row($request, ARRAY_A);
}

function rt_ubigeo_get_provincia_por_id($idProv)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = $wpdb->prepare("SELECT provincia FROM ". $table_name ." where idProv=%d",sanitize_text_field($idProv));
    return $wpdb->get_row($request, ARRAY_A);
}

function rt_ubigeo_get_distrito_por_id($idDist)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = $wpdb->prepare("SELECT distrito FROM ". $table_name ." where idDist=%d",sanitize_text_field($idDist));
    return $wpdb->get_row($request, ARRAY_A);
}

function rt_ubigeo_load_provincias_front_session($idDepa)
{
//    $response = [];
    $response = array('' => __('Select Province ', 'ubigeo-peru'));
    if (is_numeric($idDepa)) {
       
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $provincias = rt_ubigeo_get_provincia_by_idDepa($idDepa);
        } else {
            $provincias = rt_ubigeo_get_provincia_by_idDepa_display($idDepa);
        }
        if ($provincias) {
            foreach ($provincias as $provincia) {
                $response[$provincia['idProv']] = $provincia['provincia'];
            }
        }
    }
    return $response;
}

function rt_ubigeo_load_distritos_front_session($idProv)
{
//    $response = [];
    $response = array('' => __('Select District ', 'ubigeo-peru'));
    if (is_numeric($idProv)) {
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $distritos = rt_ubigeo_get_distrito_by_idProv($idProv);
        } else {
            $distritos = rt_ubigeo_get_distrito_by_idProv_display($idProv);
        }
        foreach ($distritos as $distrito) {
            $response[$distrito['idDist']] = $distrito['distrito'];
        }
    }
    return $response;
}

function rt_ubigeo_get_provincia_address_by_idDepa($idDepa)
{
    $reponse = $provincias = array();
    if($idDepa){
        $provincias = rt_ubigeo_get_provincia_by_idDepa($idDepa);
    }
    $reponse = array( '' => __('Select Province ', 'ubigeo-peru'));

    if($provincias){
        foreach ($provincias as $prov) {
            $reponse[$prov['idProv']] = $prov['provincia'];
        }
    }
    return $reponse;
}

function rt_ubigeo_get_distrito_address_by_idProv($idProv)
{
    $reponse = $distritos = array();
    if($idProv){
        $distritos = rt_ubigeo_get_distrito_by_idProv($idProv);
    }
    $reponse = array( '' => __('Select District ', 'ubigeo-peru'));
    if($distritos){
        foreach ($distritos as $dist) {
            $reponse[$dist['idDist']] = $dist['distrito'];
        }
    }
    return $reponse;
}

add_action('wp_ajax_rt_ubigeo_load_provincias_address', 'rt_ubigeo_load_provincias_address');
add_action('wp_ajax_nopriv_rt_ubigeo_load_provincias_address', 'rt_ubigeo_load_provincias_address');

function rt_ubigeo_load_provincias_address()
{
    $idDepa = sanitize_text_field($_POST['idDepa']) !== null ? sanitize_text_field($_POST['idDepa']) : null;
    $provincias = rt_ubigeo_get_provincia_by_idDepa($idDepa);
    echo json_encode($provincias);
    wp_die();
}

add_action('wp_ajax_rt_ubigeo_load_distritos_address', 'rt_ubigeo_load_distritos_address');
add_action('wp_ajax_nopriv_rt_ubigeo_load_distritos_address', 'rt_ubigeo_load_distritos_address');

function rt_ubigeo_load_distritos_address()
{
    $idProv = sanitize_text_field($_POST['idProv']) !== null ? sanitize_text_field($_POST['idProv']) : null;
    $distritos = rt_ubigeo_get_distrito_by_idProv($idProv);
    echo json_encode($distritos);
    wp_die();
}

function rt_libro_lrq_get_departamento_front()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = $wpdb->prepare("SELECT * FROM $table_name");

    return $wpdb->get_results($request, ARRAY_A);
}

function rt_libro_get_provincia_by_idDepa($idDepa = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = $wpdb->prepare("SELECT * FROM $table_name where idDepa =%d",$idDepa);
    return $wpdb->get_results($request, ARRAY_A);
}

function rt_libro_get_distrito_by_idProv($idProv = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = $wpdb->prepare("SELECT * FROM $table_name where idProv = %d",$idProv);

    return $wpdb->get_results($request, ARRAY_A);
}

function rt_libro_load_distrito_front()
{
    $idProv = sanitize_text_field($_POST['idProv']) !== null ? sanitize_text_field($_POST['idProv']) : null;

    $response = [];
    if (is_numeric($idProv)) {
        $distritos = rt_libro_get_distrito_by_idProv($idProv);

        foreach ($distritos as $distrito) {
            $response[$distrito['idDist']] = $distrito['distrito'];
        }
    }
    echo json_encode($response);
    wp_die();
}

function rt_libro_lrq_get_departamento_por_id_one($idDep)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = $wpdb->prepare("SELECT departamento FROM " . $table_name . " where idDepa= %d",$idDep);

    $rpt = $wpdb->get_row($request, ARRAY_A);
    return $rpt['departamento'];
}

function rt_libro_lrq_get_provincia_por_id_one($prov)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = $wpdb->prepare("SELECT provincia FROM " . $table_name . " where idProv= %d",$prov);

    $rpt = $wpdb->get_row($request, ARRAY_A);
    return $rpt['provincia'];
}

function rt_libro_lrq_get_distrito_por_id_one($dist)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = $wpdb->prepare("SELECT distrito FROM " . $table_name . " where idDist= %d",$dist);

    $rpt = $wpdb->get_row($request, ARRAY_A);
    return $rpt['distrito'];
}

function rt_libro_load_provincias_front()
{
    $idDepa = sanitize_text_field($_POST['idDep']) !== null ? sanitize_text_field($_POST['idDep']) : null;

    $response = [];
    if (is_numeric($idDepa)) {
        $provincias = rt_libro_get_provincia_by_idDepa($idDepa);
        foreach ($provincias as $provincia) {
            $response[$provincia['idProv']] = $provincia['provincia'];
        }
    }
    echo json_encode($response);
    wp_die();
}

add_action('yith_ywpi_invoice_template_customer_data', 'rt_ubigeo_show_invoice_template_customer_data', 50);

function rt_ubigeo_show_invoice_template_customer_data() {
    global $ywpi_document;
    /** @var WC_Order $order */
    $order = $ywpi_document->order;
    
    if ($order->get_meta('_billing_departamento')) { 
        $depa = rt_ubigeo_get_departamento_por_id($order->get_meta('_billing_departamento'))
        ?>
        <br>
       <?php _e('Department ', 'ubigeo-peru') ?> : <?php echo esc_html($depa['departamento']); ?><br>
    <?php } if ($order->get_meta('_billing_provincia')) { 
        $prov = rt_ubigeo_get_provincia_por_id($order->get_meta('_billing_provincia'));
        ?>
        <?php _e('Province ', 'ubigeo-peru') ?> : <?php echo esc_html($prov['provincia']) ?><br>
    <?php } if ($order->get_meta('_billing_distrito')) { 
        $dist = rt_ubigeo_get_distrito_por_id($order->get_meta('_billing_distrito'));
        ?>
        <?php _e('District ', 'ubigeo-peru') ?> : <?php echo esc_html($dist['distrito']) ?><br>
    <?php } 
}

function rt_yith_woo_request_quote_premium_plugin_enabled()
{
    if (in_array('yith-woocommerce-request-a-quote-premium/init.php', (array) get_option('active_plugins', array()))) {
        return true;
    }
    return false;
}


function rt_ubigeo_get_product_order($response, $object, $request)
{
    if (empty($response->data))
        return $response;

    $data_billing_departamento = rt_ubigeo_get_departamento_por_id(get_post_meta($response->data['id'], '_billing_departamento', true));
    $billing_departamento = ($data_billing_departamento) ? $data_billing_departamento['departamento'] : '';
    $data_billing_provincia = rt_ubigeo_get_provincia_por_id(get_post_meta($response->data['id'], '_billing_provincia', true));
    $billing_provincia = ($data_billing_provincia) ? $data_billing_provincia['provincia'] : '';
    $data_billing_distrito = rt_ubigeo_get_distrito_por_id(get_post_meta($response->data['id'], '_billing_distrito', true));
    $billing_distrito = ($data_billing_distrito) ? $data_billing_distrito['distrito'] : '';
    $data_shipping_departamento = rt_ubigeo_get_departamento_por_id(get_post_meta($response->data['id'], '_shipping_departamento', true));
    $shipping_departamento = ($data_shipping_departamento) ? $data_shipping_departamento['departamento'] : '';
    $data_shipping_provincia = rt_ubigeo_get_provincia_por_id(get_post_meta($response->data['id'], '_shipping_provincia', true));
    $shipping_provincia = ($data_shipping_provincia) ? $data_shipping_provincia['provincia'] : '';
    $data_shipping_distrito = rt_ubigeo_get_distrito_por_id(get_post_meta($response->data['id'], '_shipping_distrito', true));
    $shipping_distrito = ($data_shipping_distrito) ? $data_shipping_distrito['distrito'] : '';


    $response->data['billing']['departamento'] = ($billing_departamento) ? $billing_departamento : '';
    $response->data['billing']['provincia'] = ($billing_provincia) ? $billing_provincia : '';
    $response->data['billing']['distrito'] = ($billing_distrito) ? $billing_distrito : '';
    $response->data['shipping']['departamento'] = ($shipping_departamento) ? $shipping_departamento : '';
    $response->data['shipping']['provincia'] = ($shipping_provincia) ? $shipping_provincia : '';
    $response->data['shipping']['distrito'] = ($shipping_distrito) ? $shipping_distrito : '';
    return $response;
}

add_filter("woocommerce_rest_prepare_shop_order_object", "rt_ubigeo_get_product_order", 1, 3);



