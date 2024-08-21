<?php
namespace RecipesAPI;

use RecipesAPI\CPT\RecipesApiCPT;
use RecipesAPI\API\RecipesApiEndpoint;
use RecipesAPI\Admin\RecipesApiAdmin;

class RecipesApiPlugin
{
    public function init()
    {
        $this->initClasses();       
        register_activation_hook(__FILE__, [$this, 'flushRewriteRules']);
        register_deactivation_hook(__FILE__, [$this, 'flushRewriteRulesOnDeactivation']);
    }

    private function initClasses() 
    {
        $admin = new RecipesApiAdmin();
        $admin->init();

        $cpt = new RecipesApiCPT();
        $cpt->init();

        $api = new RecipesApiEndpoint();
        $api->init();
    }

    public function flushRewriteRules() 
    {
        $this->initClasses();
        flush_rewrite_rules();
    }

    public function flushRewriteRulesOnDeactivation()
    {
        flush_rewrite_rules();
    }
}
