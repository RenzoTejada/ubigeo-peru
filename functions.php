<?php

/**
* Get states by related Country Code
* @return JSON Object
*/
function get_states_call()
{
    $country_code = $_POST['country_code'];

    global $wpdb;
    $db = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ubigeo_provincia WHERE idDepa = '".$country_code."' order by provincia ASC");

    $items = array();
    $items[0]['idDepa'] = "";
    $items[0]['idProv'] = "";
    $items[0]['provincia'] = 'Provincia';

    $i = 1;
    foreach( $db as $data ) {
        $items[$i]['idDepa'] = $data->idDepa;
        $items[$i]['idProv'] = $data->idProv;
        $items[$i]['provincia'] = $data->provincia;
        $i++;
    }
    //return $items;
    ob_clean();
    echo json_encode($items);
    die();
}

 
/**
* Get cities by related State Code or Country Code (IF State code == "00" or States == 'N/A')
* @return JSON Object
*/
function get_cities_call()
{
    if( trim($_POST['row_code']) ) {
        $state_code = $_POST['row_code'];
        
        global $wpdb;

        $db = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ubigeo_distrito WHERE idProv = '".$state_code."' order by distrito ASC");
        $items = array();
        
        $items[0]['id'] = "";
        $items[0]['city_name'] = 'Distrito';
        $i = 1;
        foreach( $db as $data )
        {
            $items[$i]['id'] = $data->idDist;
            $items[$i]['city_name'] = $data->distrito;
            $i++;
        }
        //return $items;
        ob_clean();
        echo json_encode($items);
        die();
    }
}

/**
* Fill the countries select
* @return Array
*/
function departamento_select($selectedCountry = null)
{
    global $wpdb;
    $db = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ubigeo_departamento order by departamento ASC");

    $items = array();

    if (null==$selectedCountry)
        $items[]='Departamento';

    foreach( $db as $data ) {
        $items[$data->idDepa] = $data->departamento;
    }
    return $items;
}

//obtener el departamento por su idDepa
function getDepartamentoByidDepa($idDepa = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT * FROM $table_name  where idDepa = $idDepa";
    $dto = $wpdb->get_results($request,ARRAY_A);
    return $dto[0]['departamento'];
}

//obtener provincia por idProv
function getProvinciaByidProv($idProv = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT * FROM $table_name where idProv = $idProv";
    $idProv = $wpdb->get_results($request,ARRAY_A);
    return $idProv[0]['provincia'];
}
//obtener distrito por idDist
function getDistritoByidDist($idDist = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT * FROM $table_name where idDist = $idDist";
    $dist = $wpdb->get_results($request,ARRAY_A);
    return $dist[0]['distrito'];
}