<?php
class Marpico_Model {
    private $last_error_message;

    public function get_last_error_message() {
        return $this->last_error_message;
    }

    public function get_api_data() {
        $api_url = get_option('marpico_wc_sync_api_url');
        $api_key = get_option('marpico_wc_sync_api_key');

        if (empty($api_url) || empty($api_key)) {
            $this->last_error_message = 'URL de API o API Key no configurada.';
            return false;
        }

        $response = wp_remote_get($api_url, array(
            'headers' => array(
                'Authorization' => 'Api-Key ' . $api_key,
            ),
        ));

        if (is_wp_error($response)) {
            $this->last_error_message = $response->get_error_message();
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $this->last_error_message = 'Error al conectar con la API de Marpico. Código de respuesta: ' . $response_code;
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $api_data = json_decode($body, true);

        if ($api_data === null) {
            $this->last_error_message = 'Error al procesar los datos de la API de Marpico.';
            return false;
        }

        return $api_data;
    }

    public function process_api_data($api_data) {
        foreach ($api_data as $product_item) {
            $product_id = $this->get_product_id_by_sku($product_item['sku']);
            if ($product_id) {
                $this->update_product_attributes($product_id, $product_item);
            } else {
                $this->create_new_product($product_item);
            }
        }
    }

    private function get_product_id_by_sku($sku) {
        $product_id = false;

        $args = array(
            'post_type'   => 'product',
            'post_status' => 'any',
            'meta_query'  => array(
                array(
                    'key'     => '_sku',
                    'value'   => $sku,
                    'compare' => '=',
                ),
            ),
        );

        $products = new WP_Query($args);

        if ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();
        }

        wp_reset_postdata();

        return $product_id;
    }

    private function update_product_attributes($product_id, $product_data) {
        if ($product_id) {
            update_post_meta($product_id, '_regular_price', $product_data['regular_price']);
            update_post_meta($product_id, '_price', $product_data['sale_price']);
        }
    }

    private function create_new_product($product_data) {
        $new_product = array(
            'post_title'   => $product_data['name'],
            'post_content' => $product_data['description'],
            'post_status'  => 'publish',
            'post_type'    => 'product',
        );

        $product_id = wp_insert_post($new_product);

        if ($product_id && !is_wp_error($product_id)) {
            if (!empty($product_data['sku'])) {
                update_post_meta($product_id, '_sku', $product_data['sku']);
            }

            if (!empty($product_data['regular_price'])) {
                update_post_meta($product_id, '_regular_price', $product_data['regular_price']);
            }

            if (!empty($product_data['sale_price'])) {
                update_post_meta($product_id, '_sale_price', $product_data['sale_price']);
            }
        }

        return $product_id;
    }
}
