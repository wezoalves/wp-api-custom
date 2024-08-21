<?php
/**
 * Plugin Name: Recipies API
 * Description: Add meta box on CPT "Recipies" 
 * Version: 1.0.0
 * Author: Weslley Alves
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'src/RecipiesApiPlugin.php';
require_once plugin_dir_path(__FILE__) . 'src/Admin/RecipiesApiAdmin.php';
require_once plugin_dir_path(__FILE__) . 'src/CPT/RecipiesApiCPT.php';
require_once plugin_dir_path(__FILE__) . 'src/API/RecipiesApiEndpoint.php';



use RecipiesAPI\RecipiesApiPlugin;

function recipiesApiInit() {
    $plugin = new RecipiesApiPlugin();
    $plugin->init();
}
add_action('plugins_loaded', 'recipiesApiInit');
