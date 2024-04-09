<?php
/*
Plugin Name: Marpico WooCommerce Sync
Description: Plugin para sincronizar datos con la API de Marpico y WooCommerce.
Version: 1.0
*/

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'models/marpico-model.php';
require_once plugin_dir_path(__FILE__) . 'views/marpico-view.php';
require_once plugin_dir_path(__FILE__) . 'controllers/marpico-controller.php';

// Initialize the plugin
add_action('init', 'marpico_wc_sync_init');
function marpico_wc_sync_init() {
    // Initialize controller
    $controller = new Marpico_Controller();
    $controller->init();
}
