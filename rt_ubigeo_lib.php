<?php

/**
 * Acceso unificado a la sesión de WooCommerce para Ubigeo.
 *
 * Evita session_start()/$_SESSION, que puede bloquear peticiones AJAX y
 * generar conflictos con caché, headers y configuraciones de sesión PHP.
 */
function rt_ubigeo_session_get($key, $default = '')
{
    if (function_exists('WC') && WC()->session) {
        $value = WC()->session->get($key, $default);
        return ($value === null) ? $default : $value;
    }

    return $default;
}

function rt_ubigeo_session_set($key, $value)
{
    if (function_exists('WC') && WC()->session) {
        WC()->session->set($key, $value);
        return true;
    }

    return false;
}

function rt_ubigeo_debug_log($context)
{
    if (!defined('WP_DEBUG_LOG') || !WP_DEBUG_LOG) {
        return;
    }

    if (!is_array($context)) {
        $context = array('message' => (string) $context);
    }

    $message = function_exists('wp_json_encode')
        ? wp_json_encode($context)
        : json_encode($context);

    error_log('[ubigeo-peru] ' . $message);
}

function rt_ubigeo_log_query_failure($action, $params, $sql, $started_at)
{
    global $wpdb;

    if (empty($wpdb->last_error)) {
        return false;
    }

    rt_ubigeo_debug_log(array(
        'action'     => $action,
        'params'     => $params,
        'sql'        => $sql,
        'last_error' => $wpdb->last_error,
        'elapsed_ms' => round((microtime(true) - $started_at) * 1000, 2),
    ));

    return true;
}

function rt_ubigeo_front_ajax_db_error($action, $params, $started_at)
{
    rt_ubigeo_debug_log(array(
        'action'     => $action,
        'params'     => $params,
        'last_error' => 'db_error',
        'elapsed_ms' => round((microtime(true) - $started_at) * 1000, 2),
    ));

    wp_send_json_error(
        array('message' => __('No se pudieron cargar los datos de Ubigeo.', 'ubigeo-peru')),
        500
    );
}

function rt_ubigeo_costo_tipo_departamento()
{
    return defined('COSTO_UBIGEO_TIPO_DEPA') ? (int) COSTO_UBIGEO_TIPO_DEPA : 1;
}

function rt_ubigeo_costo_tipo_distrito()
{
    return defined('COSTO_UBIGEO_TIPO_DIST') ? (int) COSTO_UBIGEO_TIPO_DIST : 2;
}

function rt_ubigeo_costo_tipo_provincia()
{
    return function_exists('rt_costo_ubigeo_tipo_provincia') ? (int) rt_costo_ubigeo_tipo_provincia() : 3;
}


/**
 * Devuelve una estructura normalizada de Ubigeo.
 */
function rt_ubigeo_empty_location()
{
    return array(
        'country'      => 'PE',
        'departamento' => 0,
        'provincia'    => 0,
        'distrito'     => 0,
    );
}

/**
 * Comprueba que provincia pertenezca al departamento y distrito a provincia.
 */
function rt_ubigeo_is_valid_location($departamento, $provincia, $distrito)
{
    global $wpdb;

    $departamento = absint($departamento);
    $provincia    = absint($provincia);
    $distrito     = absint($distrito);

    if (!$departamento || !$provincia || !$distrito) {
        return false;
    }

    $table_provincia = $wpdb->prefix . 'ubigeo_provincia';
    $table_distrito  = $wpdb->prefix . 'ubigeo_distrito';

    $provincia_ok = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_provincia} WHERE idDepa = %d AND idProv = %d",
            $departamento,
            $provincia
        )
    );

    if ($provincia_ok < 1) {
        return false;
    }

    $distrito_ok = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_distrito} WHERE idProv = %d AND idDist = %d",
            $provincia,
            $distrito
        )
    );

    return $distrito_ok > 0;
}

/**
 * Lee una ubicación completa desde la sesión de WooCommerce.
 */
function rt_ubigeo_get_session_location($type = 'billing')
{
    $type = ('shipping' === $type) ? 'shipping' : 'billing';

    $location = array(
        'country'      => (string) rt_ubigeo_session_get($type . '_country', 'PE'),
        'departamento' => absint(rt_ubigeo_session_get($type . '_departamento', 0)),
        'provincia'    => absint(rt_ubigeo_session_get($type . '_provincia', 0)),
        'distrito'     => absint(rt_ubigeo_session_get($type . '_distrito', 0)),
    );

    if ('PE' !== $location['country'] || !rt_ubigeo_is_valid_location($location['departamento'], $location['provincia'], $location['distrito'])) {
        return rt_ubigeo_empty_location();
    }

    return $location;
}

/**
 * Lee el Ubigeo previamente guardado en la dirección del cliente.
 */
function rt_ubigeo_get_customer_saved_location($type = 'billing', $user_id = 0)
{
    $type    = ('shipping' === $type) ? 'shipping' : 'billing';
    $user_id = $user_id ? absint($user_id) : get_current_user_id();

    if (!$user_id) {
        return rt_ubigeo_empty_location();
    }

    $country = (string) get_user_meta($user_id, $type . '_country', true);
    if ($country !== '' && 'PE' !== $country) {
        return rt_ubigeo_empty_location();
    }

    $location = array(
        'country'      => 'PE',
        'departamento' => absint(get_user_meta($user_id, $type . '_departamento', true)),
        'provincia'    => absint(get_user_meta($user_id, $type . '_provincia', true)),
        'distrito'     => absint(get_user_meta($user_id, $type . '_distrito', true)),
    );

    if (!rt_ubigeo_is_valid_location($location['departamento'], $location['provincia'], $location['distrito'])) {
        return rt_ubigeo_empty_location();
    }

    return $location;
}

function rt_ubigeo_set_session_location($type, $location)
{
    $type = ('shipping' === $type) ? 'shipping' : 'billing';

    if (!is_array($location)) {
        return false;
    }

    rt_ubigeo_session_set($type . '_country', 'PE');
    rt_ubigeo_session_set($type . '_departamento', absint($location['departamento'] ?? 0));
    rt_ubigeo_session_set($type . '_provincia', absint($location['provincia'] ?? 0));
    rt_ubigeo_session_set($type . '_distrito', absint($location['distrito'] ?? 0));

    return true;
}

/**
 * Prioridad de ubicación:
 * 1) selección válida de la sesión actual (por ejemplo, calculador del carrito),
 * 2) dirección guardada del cliente recurrente,
 * 3) vacío para que un cliente nuevo la complete.
 */
function rt_ubigeo_get_effective_location($type = 'billing', $seed_session = true)
{
    $type = ('shipping' === $type) ? 'shipping' : 'billing';

    $session_location = rt_ubigeo_get_session_location($type);
    if (!empty($session_location['departamento'])) {
        return $session_location;
    }

    if (is_user_logged_in()) {
        $saved_location = rt_ubigeo_get_customer_saved_location($type);

        if (!empty($saved_location['departamento'])) {
            if ($seed_session) {
                rt_ubigeo_set_session_location($type, $saved_location);
            }
            return $saved_location;
        }
    }

    return rt_ubigeo_empty_location();
}

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

    $departamentoList = rt_ubigeo_get_departamento_adress();

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
    $request = "SELECT * FROM $table_name as dep inner join $table_display as dis on dis.idDepa=dep.idDepa order by dep.departamento asc";
    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_get_departamento()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT * FROM $table_name";

    return $wpdb->get_results($request, ARRAY_A);
}

add_action('wp_ajax_rt_ubigeo_load_provincias_front', 'rt_ubigeo_load_provincias_front');
add_action('wp_ajax_nopriv_rt_ubigeo_load_provincias_front', 'rt_ubigeo_load_provincias_front');

function rt_ubigeo_load_provincias_front()
{
    $started_at = microtime(true);
    $idDepa = isset($_POST['idDepa']) ? absint(wp_unslash($_POST['idDepa'])) : 0;
    $provincias = array();

    if ($idDepa > 0) {
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $provincias = rt_ubigeo_get_provincia_by_idDepa($idDepa);
        } else {
            $provincias = rt_ubigeo_get_provincia_by_idDepa_display($idDepa);
        }
    }

    if (is_wp_error($provincias)) {
        rt_ubigeo_front_ajax_db_error(
            'rt_ubigeo_load_provincias_front',
            array('idDepa' => $idDepa),
            $started_at
        );
    }

    wp_send_json($provincias);
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
    $table_tipo_costo = $wpdb->prefix . "ubigeo_tipo_costo";
    $table_ubigeo_provincia = $wpdb->prefix . "ubigeo_provincia";
    $result = array();

    $idDepa = absint($idDepa);
    if ($idDepa <= 0) {
        return $result;
    }

    $tipo_departamento = rt_ubigeo_costo_tipo_departamento();
    $tipo_distrito     = rt_ubigeo_costo_tipo_distrito();
    $tipo_provincia    = rt_ubigeo_costo_tipo_provincia();

    $broad_sql = $wpdb->prepare(
        "SELECT 1
         FROM {$table_costo_ubigeo} AS ucu
         INNER JOIN {$table_tipo_costo} AS tc ON tc.costo_id = ucu.costo_id
         WHERE ucu.idDepa = %d
           AND ucu.idProv = 0
           AND ucu.idDist = 0
           AND tc.tipo = %d
           AND (ucu.estado = 1 OR ucu.estado IS NULL)
           AND (tc.estado = 1 OR tc.estado IS NULL)
         LIMIT 1",
        $idDepa,
        $tipo_departamento
    );

    $started_at = microtime(true);
    $has_department_rule = (bool) $wpdb->get_var($broad_sql);
    if (rt_ubigeo_log_query_failure(
        'rt_ubigeo_get_provincia_by_idDepa_display',
        array('idDepa' => $idDepa, 'scope' => 'department_rule'),
        $broad_sql,
        $started_at
    )) {
        return new WP_Error('rt_ubigeo_db_error', 'Error consultando provincias.');
    }

    if ($has_department_rule) {
        $request = $wpdb->prepare(
            "SELECT idProv, provincia
             FROM {$table_ubigeo_provincia}
             WHERE idDepa = %d
             ORDER BY provincia ASC",
            $idDepa
        );
    } else {
        $request = $wpdb->prepare(
            "SELECT DISTINCT up.idProv, up.provincia
             FROM {$table_costo_ubigeo} AS ucu
             INNER JOIN {$table_tipo_costo} AS tc ON tc.costo_id = ucu.costo_id
             INNER JOIN {$table_ubigeo_provincia} AS up ON up.idProv = ucu.idProv
             WHERE ucu.idDepa = %d
               AND up.idDepa = %d
               AND ucu.idProv > 0
               AND tc.tipo IN (%d, %d)
               AND (ucu.estado = 1 OR ucu.estado IS NULL)
               AND (tc.estado = 1 OR tc.estado IS NULL)
             ORDER BY up.provincia ASC",
            $idDepa,
            $idDepa,
            $tipo_distrito,
            $tipo_provincia
        );
    }

    $started_at = microtime(true);
    $result = $wpdb->get_results($request, ARRAY_A);
    if (rt_ubigeo_log_query_failure(
        'rt_ubigeo_get_provincia_by_idDepa_display',
        array('idDepa' => $idDepa, 'scope' => $has_department_rule ? 'all' : 'configured'),
        $request,
        $started_at
    )) {
        return new WP_Error('rt_ubigeo_db_error', 'Error consultando provincias.');
    }

    return is_array($result) ? $result : array();
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
    $started_at = microtime(true);
    $idProv = isset($_POST['idProv']) ? absint(wp_unslash($_POST['idProv'])) : 0;
    $distritos = array();

    if ($idProv > 0) {
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $distritos = rt_ubigeo_get_distrito_by_idProv($idProv);
        } else {
            $distritos = rt_ubigeo_get_distrito_by_idProv_display($idProv);
        }
    }

    if (is_wp_error($distritos)) {
        rt_ubigeo_front_ajax_db_error(
            'rt_ubigeo_load_distritos_front',
            array('idProv' => $idProv),
            $started_at
        );
    }

    wp_send_json($distritos);
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

function rt_ubigeo_get_distrito_by_idProv_display( $idProv = 0 ) {
    global $wpdb;

    $idProv = absint( $idProv );
    if ( $idProv <= 0 ) {
        return array();
    }

    $table_costo_ubigeo    = $wpdb->prefix . 'ubigeo_costo_ubigeo';
    $table_tipo_costo      = $wpdb->prefix . 'ubigeo_tipo_costo';
    $table_ubigeo_provincia = $wpdb->prefix . 'ubigeo_provincia';
    $table_ubigeo_distrito = $wpdb->prefix . 'ubigeo_distrito';

    $provincia_sql = $wpdb->prepare(
        "SELECT idDepa
         FROM {$table_ubigeo_provincia}
         WHERE idProv = %d
         LIMIT 1",
        $idProv
    );
    $started_at = microtime(true);
    $idDepa = absint($wpdb->get_var($provincia_sql));
    if (rt_ubigeo_log_query_failure(
        'rt_ubigeo_get_distrito_by_idProv_display',
        array('idProv' => $idProv, 'scope' => 'province_lookup'),
        $provincia_sql,
        $started_at
    )) {
        return new WP_Error('rt_ubigeo_db_error', 'Error consultando provincia.');
    }

    if ($idDepa <= 0) {
        return array();
    }

    $tipo_departamento = rt_ubigeo_costo_tipo_departamento();
    $tipo_distrito     = rt_ubigeo_costo_tipo_distrito();
    $tipo_provincia    = rt_ubigeo_costo_tipo_provincia();

    $broad_sql = $wpdb->prepare(
        "SELECT 1
         FROM {$table_costo_ubigeo} AS ucu
         INNER JOIN {$table_tipo_costo} AS tc ON tc.costo_id = ucu.costo_id
         WHERE (
                (ucu.idDepa = %d AND ucu.idProv = 0 AND ucu.idDist = 0 AND tc.tipo = %d)
             OR (ucu.idDepa = %d AND ucu.idProv = %d AND ucu.idDist = 0 AND tc.tipo = %d)
         )
           AND (ucu.estado = 1 OR ucu.estado IS NULL)
           AND (tc.estado = 1 OR tc.estado IS NULL)
         LIMIT 1",
        $idDepa,
        $tipo_departamento,
        $idDepa,
        $idProv,
        $tipo_provincia
    );

    $started_at = microtime(true);
    $has_broad_rule = (bool) $wpdb->get_var($broad_sql);
    if (rt_ubigeo_log_query_failure(
        'rt_ubigeo_get_distrito_by_idProv_display',
        array('idProv' => $idProv, 'idDepa' => $idDepa, 'scope' => 'broad_rule'),
        $broad_sql,
        $started_at
    )) {
        return new WP_Error('rt_ubigeo_db_error', 'Error consultando configuracion de distritos.');
    }

    if ($has_broad_rule) {
        $prepared = $wpdb->prepare(
            "SELECT idDist, distrito
             FROM {$table_ubigeo_distrito}
             WHERE idProv = %d
             ORDER BY distrito ASC",
            $idProv
        );
    } else {
        $prepared = $wpdb->prepare(
            "SELECT DISTINCT dist.idDist, dist.distrito
             FROM {$table_costo_ubigeo} AS ucu
             INNER JOIN {$table_tipo_costo} AS tc ON tc.costo_id = ucu.costo_id
             INNER JOIN {$table_ubigeo_distrito} AS dist
                ON dist.idDist = ucu.idDist
               AND dist.idProv = %d
             WHERE ucu.idDepa = %d
               AND ucu.idProv = %d
               AND ucu.idDist > 0
               AND tc.tipo = %d
               AND (ucu.estado = 1 OR ucu.estado IS NULL)
               AND (tc.estado = 1 OR tc.estado IS NULL)
             ORDER BY dist.distrito ASC",
            $idProv,
            $idDepa,
            $idProv,
            $tipo_distrito
        );
    }

    $started_at = microtime(true);
    $result = $wpdb->get_results( $prepared, ARRAY_A );
    if (rt_ubigeo_log_query_failure(
        'rt_ubigeo_get_distrito_by_idProv_display',
        array('idProv' => $idProv, 'idDepa' => $idDepa, 'scope' => $has_broad_rule ? 'all' : 'configured'),
        $prepared,
        $started_at
    )) {
        return new WP_Error('rt_ubigeo_db_error', 'Error consultando distritos.');
    }

    return is_array( $result ) ? $result : array();
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
    $idDepa = isset($_POST['idDepa']) ? absint(wp_unslash($_POST['idDepa'])) : 0;
    $provincias = $idDepa ? rt_ubigeo_get_provincia_by_idDepa($idDepa) : array();
    wp_send_json($provincias);
}

add_action('wp_ajax_rt_ubigeo_load_distritos_address', 'rt_ubigeo_load_distritos_address');
add_action('wp_ajax_nopriv_rt_ubigeo_load_distritos_address', 'rt_ubigeo_load_distritos_address');

function rt_ubigeo_load_distritos_address()
{
    $idProv = isset($_POST['idProv']) ? absint(wp_unslash($_POST['idProv'])) : 0;
    $distritos = $idProv ? rt_ubigeo_get_distrito_by_idProv($idProv) : array();
    wp_send_json($distritos);
}

function rt_libro_lrq_get_departamento_front()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT * FROM $table_name";

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
    if (empty($response->data)) return $response;

    $get_meta = method_exists($object, 'get_meta') ? [$object, 'get_meta'] : 'get_post_meta';
    $order_id = $object->get_id();

    $billing_departamento_id = is_callable($get_meta) ? call_user_func($get_meta, '_billing_departamento', true) : get_post_meta($order_id, '_billing_departamento', true);
    $billing_provincia_id    = is_callable($get_meta) ? call_user_func($get_meta, '_billing_provincia', true) : get_post_meta($order_id, '_billing_provincia', true);
    $billing_distrito_id     = is_callable($get_meta) ? call_user_func($get_meta, '_billing_distrito', true) : get_post_meta($order_id, '_billing_distrito', true);
    $shipping_departamento_id = is_callable($get_meta) ? call_user_func($get_meta, '_shipping_departamento', true) : get_post_meta($order_id, '_shipping_departamento', true);
    $shipping_provincia_id    = is_callable($get_meta) ? call_user_func($get_meta, '_shipping_provincia', true) : get_post_meta($order_id, '_shipping_provincia', true);
    $shipping_distrito_id     = is_callable($get_meta) ? call_user_func($get_meta, '_shipping_distrito', true) : get_post_meta($order_id, '_shipping_distrito', true);

    // Lógica de transformación
    $billing_departamento = rt_ubigeo_get_departamento_por_id($billing_departamento_id);
    $billing_provincia    = rt_ubigeo_get_provincia_por_id($billing_provincia_id);
    $billing_distrito     = rt_ubigeo_get_distrito_por_id($billing_distrito_id);
    $shipping_departamento = rt_ubigeo_get_departamento_por_id($shipping_departamento_id);
    $shipping_provincia    = rt_ubigeo_get_provincia_por_id($shipping_provincia_id);
    $shipping_distrito     = rt_ubigeo_get_distrito_por_id($shipping_distrito_id);

    // Inserción en la respuesta
    $response->data['billing']['departamento'] = $billing_departamento['departamento'] ?? '';
    $response->data['billing']['provincia'] = $billing_provincia['provincia'] ?? '';
    $response->data['billing']['distrito'] = $billing_distrito['distrito'] ?? '';
    $response->data['shipping']['departamento'] = $shipping_departamento['departamento'] ?? '';
    $response->data['shipping']['provincia'] = $shipping_provincia['provincia'] ?? '';
    $response->data['shipping']['distrito'] = $shipping_distrito['distrito'] ?? '';

    return $response;
}

add_filter('woocommerce_rest_prepare_shop_order_object', 'rt_ubigeo_get_product_order', 10, 3);



function rt_ubigeo_get_departamento_adress()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT * FROM $table_name";

    return $wpdb->get_results($request, ARRAY_A);
}
