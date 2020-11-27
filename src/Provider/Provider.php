<?php

namespace Transloyd\Services\ESign;

use Psr\Http\Client\ClientInterface;
use Http\Message\RequestFactory;
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface, UriInterface};
use Transloyd\Services\ESign\Exception\{Exception as ProviderException, InvalidResponse, ServiceUnavailable};

class Provider
{
    public const GET_METHOD = 'GET';
    public const POST_METHOD = 'POST';
    public const PUT_METHOD = 'PUT';
    public const HTTP_BAD_REQUEST = 503;

    private $client;
    private $requestFactory;

    public function __construct(ClientInterface $client, RequestFactory $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Build a PSR-7 request using the supplied request factory
     *
     * @param string $method
     * @param string|UriInterface $uri
     * @param array $headers
     * @param resource|string|StreamInterface|null $body
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, string $uri, array $headers = [], $body = null): RequestInterface
    {
        return $this->requestFactory->createRequest(
            $method,
            $uri,
            $headers,
            $body
        );
    }

    /**
     * Send a request and return the response
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function getResponse(RequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->client->send($request, []);
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }

        return $response;
    }

    /**
     * Handle http exception
     *
     * @param \Exception $exception
     */
    private function handleException(\Exception $exception): void
    {
        if ($exception instanceof ProviderException) {
            $data = $exception->getResponse();
        } else {
            throw new ServiceUnavailable(
                sprintf(
                    InvalidResponse::INVALID_JSON_STRUCTURE_MESSAGE,
                    $exception->getMessage()
                ),
                self::HTTP_BAD_REQUEST,
                $exception
            );
        }

        throw new ServiceUnavailable(
            $data->error,
            $exception->getCode(),
            $exception
        );
    }
}