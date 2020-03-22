<?php


/**
 * Fill the countries select
 * @return Array
 */
function departamento_select($selectedCountry = null) {
    global $wpdb;
    $db = $wpdb->get_results("SELECT idDepa, CONCAT(UCASE(LEFT(departamento, 1)), LOWER(SUBSTRING(departamento, 2))) as departamento FROM " . $wpdb->prefix . "ubigeo_departamento order by departamento ASC");

    $items = array();

    if (null == $selectedCountry)
        $items[] = 'Seleccione';

    foreach ($db as $data) {
        $items[$data->idDepa] = ucwords(strtolower($data->departamento));
    }
    return $items;
}

//obtener todo los departamento
function getDepartamento() {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT idDepa, CONCAT(UCASE(LEFT(departamento, 1)), LOWER(SUBSTRING(departamento, 2))) as departamento FROM $table_name";
    return $wpdb->get_results($request, ARRAY_A);
}

//obtener el departamento por su idDepa
function getDepartamentoByidDepa($idDepa = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT idDepa, CONCAT(UCASE(LEFT(departamento, 1)), LOWER(SUBSTRING(departamento, 2))) as departamento FROM $table_name  where idDepa = $idDepa";
    $dto = $wpdb->get_results($request, ARRAY_A);
    return $dto[0]['departamento'];
}

//obtener las provincias por idDepa
function getProvinciaByidDepa($idDepa = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT idProv, CONCAT(UCASE(LEFT(provincia, 1)), LOWER(SUBSTRING(provincia, 2))) as provincia, idDepa FROM $table_name where idDepa = $idDepa";
    return $wpdb->get_results($request, ARRAY_A);
}

//obtener provincia por idProv
function getProvinciaByidProv($idProv = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT idProv, CONCAT(UCASE(LEFT(provincia, 1)), LOWER(SUBSTRING(provincia, 2))) as provincia, idDepa FROM $table_name where idProv = $idProv";
    $idProv = $wpdb->get_results($request, ARRAY_A);
    return $idProv[0]['provincia'];
}

//obtener distrito por idProv
function getDistritoByidProv($idProv = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT idDist, CONCAT(UCASE(LEFT(distrito, 1)), LOWER(SUBSTRING(distrito, 2))) as distrito, idProv FROM $table_name where idProv = $idProv";
    return $wpdb->get_results($request, ARRAY_A);
}

//obtener distrito por idDist
function getDistritoByidDist($idDist = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT idDist, CONCAT(UCASE(LEFT(distrito, 1)), LOWER(SUBSTRING(distrito, 2))) as distrito, idProv FROM $table_name where idDist = $idDist";
    $dist = $wpdb->get_results($request, ARRAY_A);
    return $dist[0]['distrito'];
}
