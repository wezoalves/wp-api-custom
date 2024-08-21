<?php
namespace RecipesAPI\API;

class RecipesApiEndpoint
{
    private \WP_REST_Request|null $params = null;
    public function init()
    {
        add_action('rest_api_init', [$this, 'registerApiEndpoints']);
    }

    public function registerApiEndpoints()
    {
        $args = [
            'methods' => 'GET',
            'callback' => [$this, 'getRecipes'],
            'permission_callback' => [$this, 'authenticateRequest'], 
        ];

        register_rest_route(
            'ra/v1', '/recipes/', $args
        );
    }
  
    public function authenticateRequest()
    {

        if (get_option('recipes_api_auth', 0) == 0) {
            return true;
        }
        
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

    public function getRecipes($data)
    {

        $this->params = $data;

        $limitFilter = isset($data['limit']) ? min($data['limit'], 100) : 6;
        $limitFilter = intval($limitFilter);

        $page = isset($data['page']) ? absint($data['page']) : 1;
        
        $siteFilter = isset($data['site']) ? sanitize_text_field($data['site']) : '';
        $sites = get_option('recipes_api_sites', []);

        // Verifica se o site passado no filtro existe na lista de sites disponíveis
        if (!empty($siteFilter) && !in_array($siteFilter, $sites)) {
            return new \WP_Error('invalid_site', 'Site inválido.', array('status' => 400));
        }

        // Converte o site para a meta key que foi usada ao salvar os dados
        $metaKey = '_site_available_' . sanitize_key($siteFilter);
    
        // Monta a meta_query para verificar se a key existe e o valor é "1"
        $metaQuery = [];
        if (!empty($siteFilter)) {
            $metaQuery[] = [
                'key' => $metaKey,
                'value' => '1',
                'compare' => '='
            ];
        }

        // Filter categories
        $categories = isset($data['categories']) ? explode(',', sanitize_text_field($data['categories'])) : [];
        $relation = isset($data['relation']) && in_array(strtoupper($data['relation']), ['AND', 'OR']) ? strtoupper($data['relation']) : 'AND';

        // Filter field category -> by term_id or slug
        $field = isset($data['field']) && in_array(strtolower($data['field']), ['term_id', 'slug']) ? strtolower($data['field']) : 'term_id';

        $taxQuery = [];
        if (!empty($categories)) {
            $taxQuery = [
            'relation' => $relation,
            [
                'taxonomy' => 'category',
                'field' => $field,
                'terms' => $categories,
            ]
            ];
        }

        $args = array(
            'post_type' => API_CUSTOM_CPTSLUG,
            'posts_per_page' => $limitFilter,
            'paged' => $page,
            'meta_query' => $metaQuery,
            'tax_query' => $taxQuery,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $responseData['query'] = $args;

        $query = new \WP_Query($args);
        $recipes = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $recipe = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'resume' => wp_strip_all_tags(get_the_excerpt()),
                    'recipe_url' => $this->concat_params(get_permalink()),
                    'image_url' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'category' => $this->get_categories(get_the_ID())
                ];

                foreach(API_CUSTOM_FIELDS as $fieldId):
                    $recipe['meta_'.$fieldId] = get_post_meta(get_the_ID(), $fieldId, true);
                endforeach;

                $recipes[] = $recipe;
            }
            wp_reset_postdata();
        }
        $responseData['data'] = $recipes;
        $responseData['pagination'] = [
            'total' => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => $page,
            'limit' => $limitFilter
        ];
        $responseData['status'] = 200;

        return rest_ensure_response($responseData);
    }

    private function get_categories($postId)
    {
        $categories = wp_get_post_terms($postId, 'category', array('fields' => 'all'));

        $category_list = [];
        foreach ($categories as $category) {
            $category_list[] = [
                'name' => $category->name,
                'url' => $this->concat_params(
                    get_term_link($category)
                ),
            ];
        }

        return $category_list;

    }
    private function concat_params($url)
    {
        $data = $this->params;

        $utmParams = [
            'utm_source',
            'utm_campaign',
            'utm_medium',
            'utm_term',
            'utm_content'
        ];
    
        $params = [];
        foreach ($utmParams as $param) {
            if (!empty($data[$param])) {
                $params[] = $param . '=' . sanitize_text_field($data[$param]);
            }
        }
    
        if (!empty($params)) {
            $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . implode('&', $params);
        }
    
        return $url;
    }


}
