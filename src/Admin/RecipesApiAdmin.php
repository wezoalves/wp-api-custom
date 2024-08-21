<?php
namespace RecipesAPI\Admin;

class RecipesApiAdmin
{
    public function init()
    {
        add_action('admin_menu', [$this, 'registerAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerAdminMenu()
    {
        add_menu_page(
            'Controle API Receitas', 
            'API Receitas', 
            'manage_options', 
            'recipes-api-sites', 
            [$this, 'adminPageHtml'], 
            'dashicons-rest-api', 
            80
        );
    }

    public function registerSettings()
    {
        register_setting('recipes-api-settings', 'recipes_api_sites');
    }

    public function adminPageHtml()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['submit'])) {
            $sites = array_map('sanitize_text_field', $_POST['recipes_api_sites']);
            update_option('recipes_api_sites', $sites);

            $auth = sanitize_text_field($_POST['recipes_api_auth']);
            update_option('recipes_api_auth', $auth);
        }

        $sites = get_option('recipes_api_sites', []);
        
        $auth = get_option('recipes_api_auth', "1");
                
        $auth_active = $auth == 1 ? 'selected' : '';
        $auth_desactive = $auth != 1 ? 'selected' : '';
        ?>
        <div class="wrap">
            <h1>Controle API Receitas</h1>
            <form method="post" action="">
                <?php settings_fields('recipes-api-settings'); ?>
                <?php do_settings_sections('recipes-api-settings'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Sites Disponíveis</th>
                        <td>
                            <div id="site-fields">
                                <?php if (!empty($sites)) : ?>
                                    <?php foreach ($sites as $site) : 
                                        
                                        $value = esc_attr($site);

                                        if (!$value ) {
                                            continue;
                                        }

                                        $input = <<<HTML
                                            <input class="site-field" type="text" name="recipes_api_sites[]" value="{$value}" />
                                        HTML;

                                        echo $input;
                                    endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="button button-primary" id="add-site">Adicionar Novo Site</button>
                        </td>
                        <tr valign="top">
                        <th scope="row">Segurança da API</th>
                        <td>
                            <select name="recipes_api_auth" id="recipes_api_auth">
                                <option value="1" <?php echo $auth_active;?>>Exigir Autenticação</option>
                                <option value="0" <?php echo $auth_desactive;?>>Acesso Público</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <style>
                    #site-fields{
                        display: flex;
                        flex-wrap: wrap;
                        flex-direction: row;
                    }
                    #site-fields .site-field{
                        margin: 10px auto;
                        padding: 5px 20px;
                        font-size: 18px;
                        
                    }
                    button#add-site{
                    margin: 15px;
                    }
                </style>
                <script type="text/javascript">
                    document.getElementById('add-site').addEventListener('click', function () {
                        const input = document.createElement('input');
                        input.classList.add('site-field');
                        input.type = 'text';
                        input.name = 'recipes_api_sites[]';
                        document.getElementById('site-fields').appendChild(input);
                    });
                </script>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
