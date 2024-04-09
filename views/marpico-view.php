<?php
class Marpico_View {
    public function render_settings_page() {
        add_menu_page(
            'Configuración de Marpico Sync',
            'Marpico Sync',
            'manage_options',
            'marpico_wc_sync_settings',
            array($this, 'render_settings_page_content'),
            'dashicons-admin-tools',
            100
        );
    }

    public function render_settings_page_content() {
        ?>
        <div class="wrap">
            <h1>Configuración de Marpico Sync</h1>
            <form method="post" action="">
                <?php
                wp_nonce_field('marpico_wc_sync_settings');
                $api_url = get_option('marpico_wc_sync_api_url');
                $api_key = get_option('marpico_wc_sync_api_key');
                ?>
                <table class="form-table">
                    <tr>
                        <th><label for="marpico_wc_sync_api_url">URL de la API de Marpico</label></th>
                        <td><input type="text" name="marpico_wc_sync_api_url" value="<?php echo esc_attr($api_url); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="marpico_wc_sync_api_key">API Key de Marpico</label></th>
                        <td><input type="text" name="marpico_wc_sync_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>Programación de sincronización</th>
                        <td>
                            <label><input type="radio" name="marpico_sync_schedule" value="hourly" <?php checked(get_option('marpico_sync_schedule'), 'hourly'); ?>> Cada hora</label><br>
                            <label><input type="radio" name="marpico_sync_schedule" value="twicedaily" <?php checked(get_option('marpico_sync_schedule'), 'twicedaily'); ?>> Dos veces al día</label><br>
                            <label><input type="radio" name="marpico_sync_schedule" value="daily" <?php checked(get_option('marpico_sync_schedule'), 'daily'); ?>> Diariamente</label>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Guardar cambios', 'primary', 'marpico_wc_sync_submit'); ?>
            </form>

            <?php $this->display_sync_status(); ?> <!-- Mostrar estado de sincronización -->
        </div>
        <?php
    }

    public function display_sync_status() {
        ?>
        <div class="wrap">
            <h1>Estado de Sincronización</h1>
            <p>Última sincronización: <?php echo get_option('marpico_last_sync_time'); ?></p>
            <p>Número de productos importados: <?php echo get_option('marpico_imported_product_count'); ?></p>
            <!-- Agrega más detalles según sea necesario -->
        </div>
        <?php
    }
}
