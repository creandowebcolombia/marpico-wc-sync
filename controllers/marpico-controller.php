<?php
class Marpico_Controller {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new Marpico_Model();
        $this->view = new Marpico_View();
    }

    public function init() {
        add_action('admin_menu', array($this->view, 'render_settings_page'));
        add_action('admin_init', array($this, 'save_api_settings'));
        add_action('marpico_sync_schedule', array($this, 'sync_products')); // Agregar acción para la programación de sincronización
        add_filter('cron_schedules', array($this, 'add_sync_schedule_interval')); // Agregar un intervalo de programación personalizado
    }

    public function save_api_settings() {
        if (isset($_POST['marpico_wc_sync_submit'])) {
            check_admin_referer('marpico_wc_sync_settings');

            $api_url = sanitize_text_field($_POST['marpico_wc_sync_api_url']);
            $api_key = sanitize_text_field($_POST['marpico_wc_sync_api_key']);

            update_option('marpico_wc_sync_api_url', $api_url);
            update_option('marpico_wc_sync_api_key', $api_key);

            $this->schedule_sync(); // Programar la sincronización después de guardar la configuración
        }
    }

    public function schedule_sync() {
        if (!wp_next_scheduled('marpico_sync_schedule')) {
            wp_schedule_event(time(), 'marpico_sync_interval', 'marpico_sync_schedule'); // Programar la sincronización
        }
    }

    public function add_sync_schedule_interval($schedules) {
        $schedules['marpico_sync_interval'] = array(
            'interval' => 3600, // Intervalo en segundos (1 hora)
            'display' => __('Cada hora')
        );
        return $schedules;
    }

    public function sync_products() {
        $api_data = $this->model->get_api_data();
        if ($api_data) {
            $this->model->process_api_data($api_data);
        } else {
            $error_message = $this->model->get_last_error_message();
            $this->view->display_connection_error_notice($error_message);
        }
    }
}
