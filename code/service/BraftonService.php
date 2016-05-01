<?php

class BraftonService extends RestfulService {
    private $api;

    public function __construct() {
        $siteConfig = SiteConfig::current_site_config();
        $apiUrl = $siteConfig->BraftonApiUrl;
        $apiKey = $siteConfig->BraftonApiKey;
        if (!$apiUrl || !$apiKey) {
            return null;
        }

        $this->api = new \brafton\APIHandler($apiKey, $apiUrl);
    }

    public function getApi() {
        return $this->api;
    }
}
