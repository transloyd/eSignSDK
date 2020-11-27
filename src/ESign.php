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

    public const ENDPOINTS = [
        'create_session' => '/ticket',
        'load_session_data' => '/ticket/%s/data',
        'set_session_data' => '/ticket/%s/option',
        'set_key_data' => '/ticket/%s/keyStore',
        'create_e_sign' => '/ticket/%s/ds/creator',
        'get_e_signed_doc' => '/ticket/%s/ds/base64Data',
    ];

    private $uuid;
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
                    $this->rootUrl . self::ENDPOINTS['create_session'],
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
                    $this->rootUrl . sprintf(self::ENDPOINTS['load_session_data'], $this->uuid),
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
                    $this->rootUrl . sprintf(self::ENDPOINTS['set_session_data'], $this->uuid),
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
                    $this->rootUrl . sprintf(self::ENDPOINTS['set_key_data'], $this->uuid),
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
                    $this->rootUrl . sprintf(self::ENDPOINTS['create_e_sign'], $this->uuid),
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
                    $this->rootUrl . sprintf(self::ENDPOINTS['get_e_signed_doc'], $this->uuid)
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $response;
    }
}
