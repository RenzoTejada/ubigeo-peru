<?php

function rt_ubigeo_get_departamentos_for_select()
{
    $dptos = [
        '' => 'Seleccionar Departamento'
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
    $idDepa = isset($_POST['idDepa']) ? $_POST['idDepa'] : null;
    $response = [];
    if (is_numeric($idDepa)) {
        if (!rt_plugin_ubigeo_costo_enabled()) {
            $provincias = rt_ubigeo_get_provincia_by_idDepa($idDepa);
        } else {
            $provincias = rt_ubigeo_get_provincia_by_idDepa_display($idDepa);
        }

        foreach ($provincias as $provincia) {
            $response[$provincia['idProv']] = $provincia['provincia'];
        }
    }
    echo json_encode($response);
    wp_die();
}

function rt_ubigeo_get_provincia_by_idDepa($idDepa = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT * FROM $table_name where idDepa = $idDepa";
    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_get_provincia_by_idDepa_display($idDepa = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $table_ubigeo_costo = $wpdb->prefix . "ubigeo_costo";
    $tipo = get_tipo_costo_ubigeo_by_idDepa($idDepa);

    if ($tipo['tipo'] == 1) {
        $result = rt_ubigeo_get_provincia_by_idDepa($idDepa);
    } else {
        $request = "SELECT prov.idProv, prov.provincia FROM $table_name  as prov  
        inner join $table_ubigeo_costo as cos on cos.idProv = prov.idProv
        where prov.idDepa=$idDepa group by prov.idProv";
        $result = $wpdb->get_results($request, ARRAY_A);
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
    $idProv = isset($_POST['idProv']) ? $_POST['idProv'] : null;
    $response = [];
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
    echo json_encode($response);
    wp_die();
}

function rt_ubigeo_get_distrito_by_idProv($idProv = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT * FROM $table_name where idProv = $idProv";
    return $wpdb->get_results($request, ARRAY_A);
}

function rt_ubigeo_get_distrito_by_idProv_display($idProv = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $table_ubigeo_costo = $wpdb->prefix . "ubigeo_costo";

    $tipo = get_tipo_costo_ubigeo_by_idProv($idProv);

    if ($tipo['tipo'] == 1) {
        $result = rt_ubigeo_get_distrito_by_idProv($idProv);
    } else {
        $request = "SELECT dist.idDist, dist.distrito FROM $table_name  as dist 
        inner join $table_ubigeo_costo as cos on cos.idDist = dist.idDist
        where cos.idProv=$idProv group by dist.idDist;";
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
    $request = "SELECT departamento FROM ". $table_name ." where idDepa=" . $idDep;
    return $wpdb->get_row($request, ARRAY_A);
}

function rt_ubigeo_get_provincia_por_id($idDep)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT provincia FROM ". $table_name ." where idProv=" . $idDep;
    return $wpdb->get_row($request, ARRAY_A);
}

function rt_ubigeo_get_distrito_por_id($idDep)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT distrito FROM ". $table_name ." where idDist=" . $idDep;
    return $wpdb->get_row($request, ARRAY_A);
}