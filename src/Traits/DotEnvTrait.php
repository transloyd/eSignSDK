<?php

namespace Transloyd\Services\Traits;

use Symfony\Component\Dotenv\Dotenv;

trait DotEnvTrait
{
    private $rootUrl;
    private $keyPass;
    private $dotenv;

    public function initDotEnv()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load(__DIR__ . '/../../.env');
        $this->rootUrl = $_ENV['ROOT_URL'];
        $this->keyPass = $_ENV['KEY_PASS'];
    }
}