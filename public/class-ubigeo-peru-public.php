<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ubigeo_Peru
 * @subpackage Ubigeo_Peru/includes
 * @author     Renzo Tejada <info@renzotejada.com>
 */
class Ubigeo_Peru_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $ubigeo_peru    The ID of this plugin.
     */
    private $ubigeo_peru;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $ubigeo_peru       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($ubigeo_peru, $version) {

        $this->ubigeo_peru = $ubigeo_peru;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
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
        wp_enqueue_style($this->ubigeo_peru, plugin_dir_url(__FILE__) . 'css/ubigeo-peru-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
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
        wp_enqueue_script($this->ubigeo_peru, plugin_dir_url(__FILE__) . 'js/ubigeo-peru-public.js', array('jquery'), $this->version, false);
    }

    public function add_checkout_script() {
        
        wp_enqueue_script('ubigeo-peru-woocommerce-after-checkout-form', plugin_dir_url(__FILE__) . 'js/ubigeo-peru-woocommerce-after-checkout-form.js', array('jquery'), $this->version, false);
        
    }

    public function pluginname_ajaxurl() {
        ?>
        <script type="text/javascript">
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        </script>
        <?php
    }

    /**
     * Get states by related Country Code
     * @return JSON Object
     */
    public function get_states_call() {
        $country_code = $_POST['country_code'];

        global $wpdb;
        $db = $wpdb->get_results("SELECT idProv, CONCAT(UCASE(LEFT(provincia, 1)), LOWER(SUBSTRING(provincia, 2))) as provincia, idDepa FROM " . $wpdb->prefix . "ubigeo_provincia WHERE idDepa = '" . $country_code . "' order by provincia ASC");

        $items = array();
        $items[0]['idDepa'] = "";
        $items[0]['idProv'] = "";
        $items[0]['provincia'] = 'Seleccione';

        $i = 1;
        foreach ($db as $data) {
            $items[$i]['idDepa'] = $data->idDepa;
            $items[$i]['idProv'] = $data->idProv;
            $items[$i]['provincia'] = ucwords(strtolower($data->provincia));
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
    public function get_cities_call() {
        if (trim($_POST['row_code'])) {
            $codes = explode('-', $_POST['row_code']);
            $country_code = $codes[1];
            $city_code = $codes[0];

            global $wpdb;

            $db = $wpdb->get_results("SELECT idDist, CONCAT(UCASE(LEFT(distrito, 1)), LOWER(SUBSTRING(distrito, 2))) as distrito, idProv  FROM " . $wpdb->prefix . "ubigeo_distrito WHERE idProv = '" . $city_code . "' order by distrito ASC");
            $items = array();

            $items[0]['id'] = "";
            $items[0]['city_name'] = 'Seleccione';
            $i = 1;
            foreach ($db as $data) {
                $items[$i]['id'] = $data->idDist;
                $items[$i]['city_name'] = ucwords(strtolower($data->distrito));
                $i++;
            }
            //return $items;
            ob_clean();
            echo json_encode($items);
            die();
        }
    }

    public function rt_remove_fields($woo_checkout_fields_array) {
        unset($woo_checkout_fields_array['billing']['billing_city']);
        unset($woo_checkout_fields_array['billing']['billing_state']); // remove state field
        unset($woo_checkout_fields_array['billing']['billing_postcode']); // remove zip code field
        unset($woo_checkout_fields_array['shipping']['shipping_city']);
        unset($woo_checkout_fields_array['shipping']['shipping_state']);
        unset($woo_checkout_fields_array['shipping']['shipping_postcode']);
        return $woo_checkout_fields_array;
    }

    public function rt_select_field_deptoCode($checkout) {
        woocommerce_form_field('deptoCode', array(
            'type' => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
            'required' => true, // actually this parameter just adds "*" to the field
            'class' => array('rt-field', 'form-row-wide'), // array only, read more about classes and styling in the previous step
            'label' => 'Departamento',
            'label_class' => 'rt-label', // sometimes you need to customize labels, both string and arrays are supported
            'options' => departamento_select()
                ), $checkout->get_value('deptoCode'));
    }

    public function rt_select_field_shippingDeptoCode($checkout) {
        woocommerce_form_field('shippingDeptoCode', array(
            'type' => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
            'required' => true, // actually this parameter just adds "*" to the field
            'class' => array('rt-field-shipping', 'form-row-wide'), // array only, read more about classes and styling in the previous step
            'label' => 'Departamento',
            'label_class' => 'rt-label-shipping', // sometimes you need to customize labels, both string and arrays are supported
            'options' => departamento_select()
                ), $checkout->get_value('shippingDeptoCode'));
    }

    public function rt_select_field_billing_city($checkout) {
        woocommerce_form_field('billing_city', array(
            'type' => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
            'required' => true, // actually this parameter just adds "*" to the field
            'class' => array('billing_city', 'form-row-wide'), // array only, read more about classes and styling in the previous step
            'label' => 'Provincia',
            'label_class' => 'rt-label', // sometimes you need to customize labels, both string and arrays are supported
            'options' => array('' => 'Provincia')
                ), $checkout->get_value('billing_city'));
    }

    public function rt_select_field_shipping_city($checkout) {
        woocommerce_form_field('shipping_city', array(
            'type' => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
            'required' => true, // actually this parameter just adds "*" to the field
            'class' => array('shipping_city', 'form-row-wide'), // array only, read more about classes and styling in the previous step
            'label' => 'Provincia',
            'label_class' => 'rt-label-shipping', // sometimes you need to customize labels, both string and arrays are supported
            'options' => array('' => 'Provincia')
                ), $checkout->get_value('shipping_city'));
    }

    public function rt_select_field_distCode($checkout) {
        woocommerce_form_field('distCode', array(
            'type' => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
            'required' => true, // actually this parameter just adds "*" to the field
            'class' => array('distCode', 'form-row-wide'), // array only, read more about classes and styling in the previous step
            'label' => 'Distrito',
            'label_class' => 'rt-label', // sometimes you need to customize labels, both string and arrays are supported
            'options' => array('' => 'Distrito')
                ), $checkout->get_value('distCode'));
    }

    public function rt_select_field_shippingDistCode($checkout) {
        woocommerce_form_field('shippingDistCode', array(
            'type' => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
            'required' => true, // actually this parameter just adds "*" to the field
            'class' => array('shippingDistCode', 'form-row-wide'), // array only, read more about classes and styling in the previous step
            'label' => 'Distrito',
            'label_class' => 'rt-label-shipping', // sometimes you need to customize labels, both string and arrays are supported
            'options' => array('' => 'Distrito')
                ), $checkout->get_value('shippingDistCode'));
    }

    // save field values
    public function save_ubigeo_peru($order_id) {

        if (!empty($_POST['deptoCode'])) {
            $dto = getDepartamentoByidDepa($_POST['deptoCode']);
            update_post_meta($order_id, 'departamento', sanitize_text_field($dto));
        }

        if (!empty($_POST['billing_city'])) {
            $codes = explode('-', $_POST['billing_city']);
            $city_code = $codes[0];
            $prov = getProvinciaByidProv($city_code);
            update_post_meta($order_id, 'provincia', sanitize_text_field($prov));
        }

        if (!empty($_POST['distCode'])) {
            $dist = getDistritoByidDist($_POST['distCode']);
            update_post_meta($order_id, 'distrito', sanitize_text_field($dist));
        }



        if (!empty($_POST['shippingDeptoCode'])) {
            $dto = getDepartamentoByidDepa($_POST['shippingDeptoCode']);
            update_post_meta($order_id, 'shipping_departamento', sanitize_text_field($dto));
        }

        if (!empty($_POST['shipping_city'])) {
            $codes = explode('-', $_POST['shipping_city']);
            $city_code = $codes[0];
            $prov = getProvinciaByidProv($city_code);
            update_post_meta($order_id, 'shipping_provincia', sanitize_text_field($prov));
        }

        if (!empty($_POST['shippingDistCode'])) {
            $dist = getDistritoByidDist($_POST['shippingDistCode']);
            update_post_meta($order_id, 'shipping_distrito', sanitize_text_field($dist));
        }
    }

    public function check_if_selected() {

        if (empty($_POST['deptoCode']))
            wc_add_notice('Por favor selecciona un Departamento.', 'error');

        if (empty($_POST['billing_city']))
            wc_add_notice('Por favor selecciona un Provincia.', 'error');

        if (empty($_POST['distCode']))
            wc_add_notice('Por favor selecciona un Distrito.', 'error');


        if ($_POST['ship_to_different_address']) {

            if (empty($_POST['shippingDeptoCode']))
                wc_add_notice('Por favor selecciona un Departamento.', 'error');

            if (empty($_POST['shipping_city']))
                wc_add_notice('Por favor selecciona un Provincia.', 'error');

            if (empty($_POST['shippingDistCode']))
                wc_add_notice('Por favor selecciona un Distrito.', 'error');
        }
    }

    public function show_custom_fields_order_billing($order) {
        echo '<h2 class="woocommerce-order-details__title">Billing Address</h2>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order->id, 'departamento', true) . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order->id, 'provincia', true) . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order->id, 'distrito', true) . '</p>';
    }

    public function show_custom_fields_order_shipping($order) {
        if (get_post_meta($order->id, 'shipping_departamento', true)) {
            echo '<h2 class="woocommerce-order-details__title">Shipping Address</h2>';
            echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order->id, 'shipping_departamento', true) . '</p>';
            echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order->id, 'shipping_provincia', true) . '</p>';
            echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order->id, 'shipping_distrito', true) . '</p>';
        }
    }

    public function show_custom_fields_thankyou($order) {
        echo '<h2 class="woocommerce-order-details__title">Billing Address</h2>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order, 'departamento', true) . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order, 'provincia', true) . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order, 'distrito', true) . '</p>';
        if (get_post_meta($order, 'shipping_departamento', true)) {
            echo '<h2 class="woocommerce-order-details__title">Shipping Address</h2>';
            echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order, 'shipping_departamento', true) . '</p>';
            echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order, 'shipping_provincia', true) . '</p>';
            echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order, 'shipping_distrito', true) . '</p>';
        }
    }

    public function show_custom_fields_emails($orden, $sent_to_admin, $order) {
        echo '<h2 class="woocommerce-order-details__title">Billing Address</h2>';
        echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order->id, 'departamento', true) . '</p>';
        echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order->id, 'provincia', true) . '</p>';
        echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order->id, 'distrito', true) . '</p>';
        if (get_post_meta($order->id, 'shipping_departamento', true)) {
            echo '<h2 class="woocommerce-order-details__title">Shipping Address</h2>';
            echo '<p><strong>' . __('Departamento') . ':</strong> ' . get_post_meta($order->id, 'shipping_departamento', true) . '</p>';
            echo '<p><strong>' . __('Provincia') . ':</strong> ' . get_post_meta($order->id, 'shipping_provincia', true) . '</p>';
            echo '<p><strong>' . __('Distrito') . ':</strong> ' . get_post_meta($order->id, 'shipping_distrito', true) . '</p>';
        }
    }

    public function rearrange_checkout_fields($fields) {
        //para mover el orden de los elementos del array, debemos asignar una propiedad de prioridad a cada campo, en nuestro ejemplo le dimos una prioridad menor al email, entonces colocará este campo al principio de nuestra forma
        $fields['billing']['billing_email']['priority'] = 10;
        $fields['billing']['billing_first_name']['priority'] = 20;
        $fields['billing']['billing_last_name']['priority'] = 30;
        $fields['billing']['billing_company']['priority'] = 40;
        $fields['billing']['billing_address_1']['priority'] = 50;
        $fields['billing']['billing_address_2']['priority'] = 60;
        $fields['billing']['billing_postcode']['priority'] = 70;
        $fields['billing']['billing_country']['priority'] = 100;
        $fields['billing']['billing_state']['priority'] = 90;
        $fields['billing']['billing_phone']['priority'] = 80;
        //Podemos hacer lo mismo para los campos de envío. En este ejemplo cambiamos el orden de los campos de Nombre y apellido con el apellido primero
        $fields['shipping']['shipping_first_name']['priority'] = 20;
        $fields['shipping']['shipping_last_name']['priority'] = 10;
        $fields['shipping']['shipping_company']['priority'] = 30;
        $fields['shipping']['shipping_address_1']['priority'] = 40;
        $fields['shipping']['shipping_address_2']['priority'] = 50;
        $fields['shipping']['shipping_postcode']['priority'] = 60;
        $fields['shipping']['shipping_country']['priority'] = 70;
        $fields['shipping']['shipping_city']['priority'] = 80;
        $fields['shipping']['shipping_state']['priority'] = 90;
        return $fields;
    }

    public function rename_city($fields) {
        $fields['city']['label'] = 'Provincia';
        return $fields;
    }

}

include('functions.php');
