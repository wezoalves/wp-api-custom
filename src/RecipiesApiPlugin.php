<?php
namespace RecipiesAPI;

use RecipiesAPI\CPT\RecipiesApiCPT;
use RecipiesAPI\API\RecipiesApiEndpoint;
use RecipiesAPI\Admin\RecipiesApiAdmin;

class RecipiesApiPlugin {
    public function init() {
        $this->initClasses();       
        register_activation_hook(__FILE__, [$this, 'flushRewriteRules']);
        register_deactivation_hook(__FILE__, [$this, 'flushRewriteRulesOnDeactivation']);
    }

    private function initClasses() {
        $admin = new RecipiesApiAdmin();
        $admin->init();

        $cpt = new RecipiesApiCPT();
        $cpt->init();

        $api = new RecipiesApiEndpoint();
        $api->init();
    }

    public function flushRewriteRules() {
        $this->initClasses();
        flush_rewrite_rules();
    }

    public function flushRewriteRulesOnDeactivation() {
        flush_rewrite_rules();
    }
}
