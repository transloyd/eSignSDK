<?php

namespace Transloyd\Services\ESign;

use Psr\Http\Message\RequestInterface;
use stdClass;

class Facade
{
    protected $provider;
    public $response;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function getResponse(): stdClass
    {
        return $this->response;
    }

    public function getResponseBody(RequestInterface $request): stdClass
    {
        $response = $this->provider->getResponse($request);

        return json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);
    }
}