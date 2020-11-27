<?php

declare(strict_types=1);

namespace Transloyd\Services\ESign;

use stdClass;
use Transloyd\Services\ESign\Exception\{InvalidResponse, InvalidResponseException};
use Symfony\Component\Dotenv\Dotenv;

class ESign extends Facade
{
    public const JSON_HEADERS = [
        'Content-Type' => 'application/json'
    ];
    private $uuid;
    /**
     * @var Dotenv
     */
    private $dotenv;
    private $rootUrl;
    private $keyPass;
    private $signedFile;

    public function __construct(
        Provider $provider
    ) {
        parent::__construct($provider);

        $this->dotenv = new Dotenv();
        $this->dotenv->load(__DIR__ . '/../.env');
        $this->rootUrl = $_ENV['ROOT_URL'];
        $this->keyPass = $_ENV['KEY_PASS'];
    }

    /**
     * @return stdClass
     */
    public function createSession(): stdClass
    {
        try {
            $response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . '/ticket',
                    self::JSON_HEADERS
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        $this->uuid = $response->ticketUuid;

        return $response;
    }

    /**
     * @param string $filePath
     * @return stdClass
     */
    public function loadSessionData(string $filePath): stdClass
    {
        try {
            $response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . '/ticket/' . $this->uuid . '/data',
                    ESign::JSON_HEADERS,
                    $filePath
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $response;
    }

    /**
     * @param string $data
     * @return stdClass
     */
    public function setSessionData(string $data): stdClass
    {
        try {
            $response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::PUT_METHOD,
                    $this->rootUrl . '/ticket/' . $this->uuid . '/option',
                    ESign::JSON_HEADERS,
                    $data
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $response;
    }

    /**
     * @param string $keyData
     * @return stdClass
     */
    public function setKeyData(string $keyData): stdClass
    {
        try {
            $response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::PUT_METHOD,
                    $this->rootUrl . '/ticket/' . $this->uuid . '/keyStore',
                    ESign::JSON_HEADERS,
                    $keyData
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $response;
    }

    /**
     * @return stdClass
     */
    public function createESign(): stdClass
    {
        try {
            $response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . '/ticket/' . $this->uuid . '/ds/creator',
                    ESign::JSON_HEADERS,
                    '{"keyStorePassword": ' . $this->keyPass . '}'
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $response;
    }

    /**
     * @return stdClass
     */
    public function getESignedDoc(): stdClass
    {
        try {
            $response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::GET_METHOD,
                    $this->rootUrl . '/ticket/' . $this->uuid . '/ds/base64Data'
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        $this->signedFile = $response->base64Data;

        return $response;
    }
}
