<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://renzotejada.com/
 * @since      2.0.0
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 * @author     Renzo Tejada <info@renzotejada.com>
 */
class Ubigeo_Peru_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $ubigeo_peru    The ID of this plugin.
     */
    private $ubigeo_peru;

    /**
     * The version of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param      string    $ubigeo_peru       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($ubigeo_peru, $version) {

        $this->ubigeo_peru = $ubigeo_peru;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    2.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ubigeo_Peru_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ubigeo_Peru_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->ubigeo_peru, plugin_dir_url(__FILE__) . 'css/ubigeo-peru-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ubigeo_Peru_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ubigeo_Peru_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->ubigeo_peru, plugin_dir_url(__FILE__) . 'js/ubigeo-peru-admin.js', array('jquery'), $this->version, false);
    }

    public function ubigeo_menu() {
        // Extraemos el directorio en el que estamos para ir us�ndolo luego
        $pluginDir = pathinfo(__FILE__);
        $pluginDir = $pluginDir['dirname'];
        // titulo de la nueva sección:
        $page_title = UBIGEO_TITLE;
        // titulo en el menú
        $menu_title = UBIGEO_TITLE;
        // nivel necesario para poder ver el menú (admin:10, editores:8)
        // + info en: http://codex.wordpress.org/User_Levels
//        $access_level = "edit_posts";
        $access_level = "manage_options";

        // la página que se cargaré al clickar en el menú
//        $content_file2 = $pluginDir . '/partials/ubigeo-peru-admin-display.php';
        $content_file = 'ubigeo-peru';
//            require plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru.php';
        // Función para cargar dentro de la página incluida para generar el menú
        // Si no se indica, se asume que al incluir el fichero ya se ha generado todo el
        // contenido necesario.
//        $content_function = null;
        // url del icono para el menú
        $menu_icon_url = null;

        add_menu_page($page_title, $menu_title, $access_level, $content_file, 'ubigeo_panel_dash');
//        add_submenu_page($content_file, $page_title, $menu_title, $access_level, 'ubigeo-peru-panel', 'ubigeo_panel');
    }

}

function ubigeo_panel_dash() 
{
    include('partials/ubigeo-peru-admin-display.php');
}
