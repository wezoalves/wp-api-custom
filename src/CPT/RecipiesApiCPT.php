<?php
namespace RecipiesAPI\CPT;

class RecipiesApiCPT {
    public function init() {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post', [$this, 'saveMetaBoxData']);
        add_action('init', [$this, 'addCustomRewriteRules']);
    }

    public function addMetaBox() {
        add_meta_box(
            'ra_sites_meta_box', 
            'Sites Disponíveis', 
            [$this, 'metaBoxCallback'], 
            'recipies', 
            'normal', 
            'high'
        );
    }

    public function metaBoxCallback($post) {
        $sites = get_option('recipies_api_sites', []);
        $selectedSites = get_post_meta($post->ID, '_sites_api_available', true);

        if (empty($sites)) {
            echo '<p>Nenhum site disponível. Por favor, adicione sites em "Gerenciar Sites".</p>';
            return;
        }

        echo '<p>Selecione os sites onde este CPT estará disponível:</p>';

        foreach ($sites as $site) {
            $checked = (is_array($selectedSites) && in_array($site, $selectedSites)) ? 'checked' : '';
            echo '<label><input type="checkbox" name="ra_selected_sites[]" value="' . esc_attr($site) . '" ' . $checked . '> ' . esc_html($site) . '</label><br>';
        }
    }

    public function saveMetaBoxData($postId) {
        if (!isset($_POST['ra_selected_sites']) || !is_array($_POST['ra_selected_sites'])) {
            return;
        }

        update_post_meta($postId, '_sites_api_available', array_map('sanitize_text_field', $_POST['ra_selected_sites']));
    }

    public function addCustomRewriteRules() {
        add_rewrite_rule('^apireceita/V1/recipies/?', 'index.php?rest_route=/ra/v1/recipies', 'top');
    }
}
