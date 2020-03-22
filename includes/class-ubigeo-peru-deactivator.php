<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://renzotejada.com/
 * @since      2.0.0
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 * @author     Renzo Tejada <info@renzotejada.com>
 */
class Ubigeo_Peru_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    2.0.0
     */
    public static function deactivate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $ubigeo = new Ubigeo_Peru_Deactivator();
        $ubigeo->delete_tabla("ubigeo_departamento");
        $ubigeo->delete_tabla("ubigeo_provincia");
        $ubigeo->delete_tabla("ubigeo_distrito");
    }

    function delete_tabla($tabla) {
        global $wpdb;
        $table_name = $wpdb->prefix . $tabla;

        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }

}
