<?php

namespace Transloyd\Services\ESign;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use stdClass;

class Facade
{
    /** @var Client */
    protected $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param RequestInterface $request
     * @return stdClass
     */
    public function getResponseBody(RequestInterface $request): stdClass
    {
        $response = $this->provider->getResponse($request);

        return json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);
    }
}