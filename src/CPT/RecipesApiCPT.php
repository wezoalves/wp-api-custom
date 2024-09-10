<?php
namespace RecipesAPI\CPT;

class RecipesApiCPT
{
    public function init()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post', [$this, 'saveMetaBoxData']);
        add_action('init', [$this, 'addCustomRewriteRules']);
    }

    public function addMetaBox()
    {
        $selectedCpts = get_option('recipes_api_cpt', []);

        foreach ($selectedCpts as $cpt) {
            add_meta_box(
                'ra_sites_meta_box', 
                'Api', 
                [$this, 'metaBoxCallback'], 
                $cpt, 
                'side', 
                'low'
            );
        }
    }

    public function metaBoxCallback($post)
    {
        $sites = get_option('recipes_api_sites', []);
        $selectedSites = [];

        foreach ($sites as $site) {
            if (get_post_meta($post->ID, '_site_available_' . sanitize_key($site), true)) {
                $selectedSites[] = $site;
            }
        }

        if (empty($sites)) {
            echo '<p>Nenhum site disponível. Por favor, adicione sites no menu principal "Api".</p>';
            return;
        }

        echo '<p>Selecione os sites onde este conteúdo estará disponível:</p>';

        foreach ($sites as $site) {
            $checked = in_array($site, $selectedSites) ? 'checked' : '';
            echo '<label><input type="checkbox" name="ra_selected_sites[]" value="' . esc_attr($site) . '" ' . $checked . '> ' . esc_html($site) . '</label><br>';
        }
    }

    public function saveMetaBoxData($postId)
    {

        // Certifique-se de que o post ID é válido
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verifica se o post está sendo salvo no contexto correto
        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        // Obtém todas as metas do post
        $all_meta = get_post_meta($postId);

        // Itera sobre todas as metas e remove aquelas que têm o prefixo '_site_available_'
        foreach ($all_meta as $meta_key => $meta_value) {
            if (strpos($meta_key, '_site_available_') === 0) {
                delete_post_meta($postId, $meta_key);
            }
        }

        if (isset($_POST['ra_selected_sites']) && is_array($_POST['ra_selected_sites'])) {
            foreach ($_POST['ra_selected_sites'] as $selectedSite) {
                update_post_meta($postId, '_site_available_' . sanitize_key($selectedSite), '1');
            }
        } 
    }

    public function addCustomRewriteRules()
    {
        add_rewrite_rule('^api/v1/recipes/?', 'index.php?rest_route=/ra/v1/recipes', 'top');
    }
}
