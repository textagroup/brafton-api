<?php

class BraftonService extends RestfulService {
    private $api;

    public function __construct() {
        $apiUrl = 'http://api.brafton.com/';
        $apiKey = 'dada3480-9d3b-4989-876a-663fdbe48be8';

        $this->api = new \brafton\APIHandler($apiKey, $apiUrl);
    }

    public function getApi() {
        return $this->api;
    }
}
