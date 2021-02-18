<?php

namespace Transloyd\Services\Traits;

use Symfony\Component\Dotenv\Dotenv;

trait DotEnvTrait
{
    private $rootUrl;
    private $keyData;
    private $dotEnv;

    public function initDotEnv()
    {
        $this->dotEnv = new Dotenv();
        $this->dotEnv->load(__DIR__ . '/../../.env');
        $this->rootUrl = $_ENV['ROOT_URL'];
    }
}