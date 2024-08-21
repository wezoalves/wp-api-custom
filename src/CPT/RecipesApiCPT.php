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
        add_meta_box(
            'ra_sites_meta_box', 
            'Api Receitas', 
            [$this, 'metaBoxCallback'], 
            API_CUSTOM_CPTSLUG, 
            'side', 
            'low'
        );
    }

    public function metaBoxCallback($post)
    {
        $sites = get_option('recipes_api_sites', []);
        $selectedSites = [];

        // Verifica os metadados individuais de cada site
        foreach ($sites as $site) {
            if (get_post_meta($post->ID, '_site_available_' . sanitize_key($site), true)) {
                $selectedSites[] = $site;
            }
        }

        if (empty($sites)) {
            echo '<p>Nenhum site disponível. Por favor, adicione sites em "Api Receitas".</p>';
            return;
        }

        echo '<p>Selecione os sites onde a Receita estará disponível:</p>';

        foreach ($sites as $site) {
            $checked = in_array($site, $selectedSites) ? 'checked' : '';
            echo '<label><input type="checkbox" name="ra_selected_sites[]" value="' . esc_attr($site) . '" ' . $checked . '> ' . esc_html($site) . '</label><br>';
        }
    }

    public function saveMetaBoxData($postId)
    {
        if (!isset($_POST['ra_selected_sites']) || !is_array($_POST['ra_selected_sites'])) {
            return;
        }

        $sites = get_option('recipes_api_sites', []);
        foreach ($sites as $site) {
            delete_post_meta($postId, '_site_available_' . sanitize_key($site));
        }

        foreach ($_POST['ra_selected_sites'] as $selectedSite) {
            update_post_meta($postId, '_site_available_' . sanitize_key($selectedSite), '1');
        }
    }

    public function addCustomRewriteRules()
    {
        add_rewrite_rule('^api/v1/recipes/?', 'index.php?rest_route=/ra/v1/recipes', 'top');
    }
}
