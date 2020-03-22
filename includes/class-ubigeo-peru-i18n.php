<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://renzotejada.com/
 * @since      2.0.0
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 * @author     Renzo Tejada <info@renzotejada.com>
 */
class Ubigeo_Peru_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ubigeo-peru',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
