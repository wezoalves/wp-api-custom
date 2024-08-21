<?php
namespace RecipiesAPI\API;

class RecipiesApiEndpoint {
    public function init() {
        add_action('rest_api_init', [$this, 'registerApiEndpoints']);
    }

    public function registerApiEndpoints() {
        register_rest_route('ra/v1', '/recipies/', array(
            'methods' => 'GET',
            'callback' => [$this, 'getRecipies'],
            'permission_callback' => [$this, 'authenticateRequest'], // Adiciona o método de autenticação
        ));
    }
  
    public function authenticateRequest() {
        
        $authorizationHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

        if (empty($authorizationHeader)) {
            return new \WP_Error('no_auth_header', 'Cabeçalho de autenticação não encontrado.', array('status' => 401));
        }

        $creds = base64_decode(str_replace('Basic ', '', $authorizationHeader));
        list($username, $password) = explode(':', $creds);

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user) || !is_a($user, 'WP_User')) {
            return new \WP_Error('authentication_failed', 'Credenciais inválidas.', array('status' => 403));
        }

        if (!wp_is_application_password($password, $user)) {
            return new \WP_Error('invalid_app_password', 'Senha de aplicativo inválida.', array('status' => 403));
        }

        return true;
    }

    public function getRecipies($data) {
        $siteFilter = isset($data['sites']) ? sanitize_text_field($data['sites']) : '';
        $sites = get_option('recipies_api_sites', []);

        $metaQuery = [];

        if (!empty($siteFilter)) {
            if (!in_array($siteFilter, $sites)) {
                return new \WP_Error('invalid_site', 'Site inválido.', array('status' => 400));
            }

            $metaQuery[] = [
                'key' => '_sites_api_available',
                'value' => $siteFilter,
                'compare' => 'LIKE',
            ];
        }

        $args = array(
            'post_type' => 'recipies',
            'posts_per_page' => -1,
            'meta_query' => $metaQuery, // O filtro só será aplicado se o site for informado
        );

        $query = new \WP_Query($args);
        $recipies = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $recipies[] = array(
                    'ID' => get_the_ID(),
                    'title' => get_the_title(),
                    'link' => get_permalink(),
                    'meta' => get_post_meta(get_the_ID()), // Adiciona os campos meta
                );
            }
            wp_reset_postdata();
        }

        return rest_ensure_response($recipies);
    }
}
