<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Api extends BaseConfig
{
    public string $apiURL;

    public function __construct()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $this->apiURL = $scheme . '://' . $host . '/' . trim(getenv('api.path') ?? '', '/');
    }
}
