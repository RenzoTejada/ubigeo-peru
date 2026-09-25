<?php
/**
 * Compatibility layer for YITH Proteo Toolkit AJAX handling.
 *
 * YITH Proteo Toolkit 1.3.1 registers YITH_Proteo_Wizard::admin_page()
 * on admin_init. WordPress also runs admin_init during admin-ajax.php, so
 * anonymous AJAX requests can be stopped by the wizard permission check
 * before WordPress dispatches wp_ajax_nopriv callbacks.
 *
 * This compatibility layer only removes that wizard page callback during
 * WordPress AJAX requests. It does not alter YITH, WooCommerce, or Ubigeo
 * AJAX actions/endpoints.
 *
 * @package Ubigeo_Peru
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('RT_Ubigeo_YITH_Proteo_Ajax_Compatibility')) {
    /**
     * Prevent the YITH Proteo setup wizard page callback from running in AJAX.
     */
    final class RT_Ubigeo_YITH_Proteo_Ajax_Compatibility
    {
        const YITH_WIZARD_CLASS = 'YITH_Proteo_Wizard';
        const YITH_WIZARD_METHOD = 'admin_page';
        const YITH_WIZARD_HOOK = 'admin_init';
        const YITH_WIZARD_PRIORITY = 30;

        /**
         * Register compatibility hooks.
         *
         * @return void
         */
        public static function init()
        {
            add_action(
                self::YITH_WIZARD_HOOK,
                array(__CLASS__, 'remove_yith_wizard_admin_page_during_ajax'),
                0
            );
        }

        /**
         * Remove only YITH_Proteo_Wizard::admin_page() during admin-ajax.php.
         *
         * @return void
         */
        public static function remove_yith_wizard_admin_page_during_ajax()
        {
            if (!self::is_wordpress_ajax_request()) {
                return;
            }

            if (!class_exists(self::YITH_WIZARD_CLASS, false)) {
                return;
            }

            global $proteo_setup_wizard;

            if (!isset($proteo_setup_wizard) || !is_object($proteo_setup_wizard)) {
                return;
            }

            if (!is_a($proteo_setup_wizard, self::YITH_WIZARD_CLASS)) {
                return;
            }

            if (!method_exists($proteo_setup_wizard, self::YITH_WIZARD_METHOD)) {
                return;
            }

            $callback = array($proteo_setup_wizard, self::YITH_WIZARD_METHOD);

            if (self::YITH_WIZARD_PRIORITY !== has_action(self::YITH_WIZARD_HOOK, $callback)) {
                return;
            }

            remove_action(
                self::YITH_WIZARD_HOOK,
                $callback,
                self::YITH_WIZARD_PRIORITY
            );
        }

        /**
         * Check whether the current request is a WordPress AJAX request.
         *
         * @return bool
         */
        private static function is_wordpress_ajax_request()
        {
            if (function_exists('wp_doing_ajax')) {
                return wp_doing_ajax();
            }

            return defined('DOING_AJAX') && DOING_AJAX;
        }
    }

    RT_Ubigeo_YITH_Proteo_Ajax_Compatibility::init();
}
