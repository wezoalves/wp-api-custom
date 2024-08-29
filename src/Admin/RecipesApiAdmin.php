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
            'Controle API', 
            'API', 
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
        register_setting('recipes-api-settings', 'recipes_api_cpt');
    }

    public function adminPageHtml()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['submit'])) {
            $sites = isset($_POST['recipes_api_sites']) ? array_filter(array_map('sanitize_text_field', $_POST['recipes_api_sites'])) : [];
            $cpts = isset($_POST['recipes_api_cpt']) ? array_map('sanitize_text_field', $_POST['recipes_api_cpt']) : [];

            update_option('recipes_api_sites', empty($sites) ? null : $sites);
            update_option('recipes_api_cpt', $cpts);

            $auth = sanitize_text_field($_POST['recipes_api_auth']);
            update_option('recipes_api_auth', $auth);
        }

        $sites = get_option('recipes_api_sites', []);
        $cpts = get_option('recipes_api_cpt', []);
        $auth = get_option('recipes_api_auth', "1");
        $auth_active = $auth == 1 ? 'selected' : '';
        $auth_desactive = $auth != 1 ? 'selected' : '';

        $registered_cpts = get_post_types(['public' => true], 'objects');
        ?>
        <div class="wrap">
            <h1>Controle API Receitas</h1>
            <form method="post" action="">
                <?php settings_fields('recipes-api-settings'); ?>
                <?php do_settings_sections('recipes-api-settings'); ?>

                <div id="save-notice" class="notice">
                    Lembre-se de clicar em "Salvar" para aplicar as alterações.
                </div>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            Segurança da API
                            <br>
                            <br>
                            <small class="help-text">
                                Ao habilitar <b>Exigir Autenticação</b>a API precisará ser autenticada usando o sistema <b><a href="https://pt.stackoverflow.com/questions/254503/o-que-%C3%A9-basic-auth" target="_blank" rel="nofollow">Basic Autentication</a></b> com 
                                <code>Authorization:Basic username_wordpress:password_app</code> 
                            </small>
                        </th>
                        <td>
                            <select name="recipes_api_auth" id="recipes_api_auth">
                                <option value="1" <?php echo $auth_active;?>>Exigir Autenticação</option>
                                <option value="0" <?php echo $auth_desactive;?>>Acesso Público</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            Filtro (Site)
                            <br>
                            <br>
                            <small class="help-text">Defina os nomes dos sites que estaram disponíveis para filtro na API</small>
                        </th>
                        <td>
                            <div id="site-fields">
                                <?php if (!empty($sites)) : ?>
                                    <?php foreach ($sites as $index => $site) : 
                                        
                                        $value = esc_attr($site);

                                        if (!$value) {
                                            continue;
                                        }

                                        $input = <<<HTML
                                            <div class="site-field-wrapper">
                                                <input class="site-field" type="text" name="recipes_api_sites[]" value="{$value}" />
                                                <button type="button" class="button button-secondary remove-site" data-index="{$index}">Remover</button>
                                            </div>
                                        HTML;

                                        echo $input;
                                    endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="button button-secondary" id="add-site">Adicionar Novo</button>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            Tipos de Conteúdo
                            <br>
                            <br>
                            <small class="help-text">Selecione os tipos de conteúdo que serão exibidos nas APIs.</small>
                        </th>
                        <td>
                            <?php foreach ($registered_cpts as $slug => $cpt) : ?>
                                <label>
                                    <input type="checkbox" name="recipes_api_cpt[]" value="<?php echo esc_attr($slug); ?>" <?php echo in_array($slug, $cpts) ? 'checked' : ''; ?>>
                                    <?php echo esc_html($cpt->labels->name); ?> ( <?php echo esc_attr($slug); ?> )
                                </label><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                const saveNotice = document.getElementById('save-notice');
                const form = document.querySelector('form');
                const inputs = form.querySelectorAll('input[type="text"], input[type="checkbox"], select');

                function showSaveNotice() {
                    saveNotice.style.display = 'block';
                }

                inputs.forEach(input => {
                    input.addEventListener('change', showSaveNotice);
                });

                document.getElementById('add-site').addEventListener('click', function () {
                    showSaveNotice();

                    const inputWrapper = document.createElement('div');
                    inputWrapper.classList.add('site-field-wrapper');
                    inputWrapper.innerHTML = `
                        <input class="site-field" type="text" name="recipes_api_sites[]" value="" />
                        <button type="button" class="button button-secondary remove-site">Remover</button>
                    `;
                    document.getElementById('site-fields').appendChild(inputWrapper);

                    inputWrapper.querySelector('.remove-site').addEventListener('click', function () {
                        this.closest('.site-field-wrapper').remove();
                        showSaveNotice();
                    });

                    inputWrapper.querySelector('.site-field').addEventListener('change', showSaveNotice);
                });

                document.querySelectorAll('.remove-site').forEach(button => {
                    button.addEventListener('click', function () {
                        this.closest('.site-field-wrapper').remove();
                        showSaveNotice();
                    });
                });
            });
        </script>
        <style>
        #site-fields{
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
        }
        .site-field-wrapper {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .site-field {
            padding: 5px 20px;
            font-size: 18px;
            margin-right: 10px;
        }
        button#add-site{
            margin: 15px 0;
        }
        button.remove-site{
            margin: 0 30px 0 0px !important;
        }
        .form-table tr{
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            background-color: white;
        }
        .form-table td{
            padding: 10px;
        }
        .form-table th{
            padding: 10px;
        }
        .notice{
            display: none;
            background-color: #ff3400;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            color: white;
            border: 1px solid #f98162;
            font-size: 14px;
        }
        .help-text{
            font-size: 12px;
            font-weight: 400;
        }
        </style>
        <?php
    }
}
