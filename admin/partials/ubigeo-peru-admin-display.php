<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://renzotejada.com/
 * @since      2.0.0
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 */
?>
<div class="wrap">
    <h2><?php _e('Ubigeo Perú', 'ubigeo-peru-title'); ?></h2>
    <p> Documentación de Ubigeo de Perú para Woocommerce</p>

</div>
<div id="dashboard-widgets-wrap">
    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
        <div id="dashboard_right_now" class="postbox">
            <h3 class="main">Funciones de Ubigeo de Perú para Woocommerce</h3>
            <div class="inside">
                <div class="main">
                    <ul>
                        <li> getDepartamento : Devuelve todos los departamentos en un array.</li>
                        <li> Ej: getDepartamento(); </li>
                        <li></li>
                        <li></li>
                        <li> getDepartamentoByidDepa : Devuelve el departamento en string, parametro de entrada idDepa.
                        </li>
                        <li> Ej: getDepartamentoByidDepa(1);</li>
                        <li></li>
                        <li></li>
                        <li> getProvinciaByidDepa : Devuelve las provincias segun el departamento en array, parametro de
                            entrada idDepa.</li>
                        <li> Ej: getProvinciaByidDepa(1);</li>
                        <li></li>
                        <li></li>
                        <li> getProvinciaByidProv : Devuelve la provincia en string, parametro de entrada idProv.</li>
                        <li> Ej: getProvinciaByidProv(1);</li>
                        <li></li>
                        <li></li>
                        <li> getDistritoByidProv : Devuelve los distritos segun la provincia en array, parametro de
                            entrada idProv.</li>
                        <li> Ej: getDistritoByidProv(1);</li>
                        <li></li>
                        <li></li>
                        <li> getDistritoByidDist : Devuelve el distrito en string, parametro de entrada idDist. </li>
                        <li> Ej: getDistritoByidDist(1); </li>
                        <li></li>
                        <li></li>
                    </ul>
                    <p id="wp-version-message"><span id="wp-version">Plugin Ubigeo de Perú para Woocommerce, desarrollador por <a
                                href="https://renzotejada.com/">Renzo Tejada</a>.</span></p>
                    <p></p>
                </div>
            </div>
        </div>
    </div>
</div>