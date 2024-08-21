<?php
namespace RecipiesAPI\Admin;

class RecipiesApiAdmin {
    public function init() {
        add_action('admin_menu', [$this, 'registerAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerAdminMenu() {
        add_menu_page(
            'Gerenciar Sites Disponíveis', 
            'Gerenciar Sites', 
            'manage_options', 
            'recipies-api-sites', 
            [$this, 'adminPageHtml'], 
            'dashicons-admin-generic', 
            80
        );
    }

    public function registerSettings() {
        register_setting('recipies-api-settings', 'recipies_api_sites');
    }

    public function adminPageHtml() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['submit'])) {
            $sites = array_map('sanitize_text_field', $_POST['recipies_api_sites']);
            update_option('recipies_api_sites', $sites);
        }

        $sites = get_option('recipies_api_sites', []);

        ?>
        <div class="wrap">
            <h1>Gerenciar Sites Disponíveis</h1>
            <form method="post" action="">
                <?php settings_fields('recipies-api-settings'); ?>
                <?php do_settings_sections('recipies-api-settings'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Sites Disponíveis</th>
                        <td>
                            <div id="site-fields">
                                <?php if (!empty($sites)) : ?>
                                    <?php foreach ($sites as $site) : ?>
                                        <input type="text" name="recipies_api_sites[]" value="<?php echo esc_attr($site); ?>" /><br>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" id="add-site">Adicionar Novo Site</button>
                        </td>
                    </tr>
                </table>
                <script type="text/javascript">
                    document.getElementById('add-site').addEventListener('click', function () {
                        const div = document.createElement('div');
                        div.innerHTML = '<input type="text" name="recipies_api_sites[]" value="" /><br>';
                        document.getElementById('site-fields').appendChild(div);
                    });
                </script>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
