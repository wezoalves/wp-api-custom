<?php
/**
 * Plugin Name: Recipes API
 * Description: Add meta box on CPT "recipes" 
 * Version: 1.0.0
 * Author: Weslley Alves
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'src/RecipesApiPlugin.php';
require_once plugin_dir_path(__FILE__) . 'src/Admin/RecipesApiAdmin.php';
require_once plugin_dir_path(__FILE__) . 'src/CPT/RecipesApiCPT.php';
require_once plugin_dir_path(__FILE__) . 'src/API/RecipesApiEndpoint.php';

define('API_CUSTOM_CPTSLUG', 'recipes');
define('API_CUSTOM_FIELDS', ['yield', 'prep_time']);

use RecipesAPI\RecipesApiPlugin;

function recipesApiInit()
{
    $plugin = new RecipesApiPlugin();
    $plugin->init();
}
add_action('plugins_loaded', 'recipesApiInit');
