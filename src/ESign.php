<?php

declare(strict_types=1);

namespace Transloyd\Services\ESign;

use Transloyd\Services\ESign\Exception\{InvalidResponse, InvalidResponseException};
use Transloyd\Services\Traits\DotEnvTrait;

class ESign extends Facade
{
    use DotEnvTrait;

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
        'verify_esign' => '/ticket/%s/ds/verifier',
        'load_es_session_data' => '/ticket/%s/ds/data',
    ];

    private $uuid;
    private $signedFile;
    private $base64Data;

    public function __construct(Provider $provider)
    {
        parent::__construct($provider);

        $this->initDotEnv();
    }

    public function createSession(): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . self::ENDPOINTS['create_session'],
                    self::JSON_HEADERS
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        $this->uuid = $this->response->ticketUuid;

        return $this;
    }

    public function loadSessionData(string $fileData): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . sprintf(self::ENDPOINTS['load_session_data'], $this->uuid),
                    ESign::JSON_HEADERS,
                    $fileData
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $this;
    }

    public function setSessionData(string $data): self
    {
        try {
            $this->response = $this->getResponseBody(
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

        return $this;
    }

    public function setKeyData(string $keyData): self
    {
        try {
            $this->response = $this->getResponseBody(
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

        return $this;
    }

    public function createESign($keyPass): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . sprintf(self::ENDPOINTS['create_e_sign'], $this->uuid),
                    ESign::JSON_HEADERS,
                    '{"keyStorePassword": ' . $keyPass . '}'
                )
            );
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $this;
    }

    public function getESignedDoc(): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::GET_METHOD,
                    $this->rootUrl . sprintf(self::ENDPOINTS['get_e_signed_doc'], $this->uuid)
                )
            );

            $this->base64Data = $this->response;
        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $this;
    }

    public function loadEsSessionData(): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . sprintf(self::ENDPOINTS['load_es_session_data'], $this->uuid),
                    ESign::JSON_HEADERS,
                    '{"base64Data": "' . $this->base64Data . '"}'
                )
            );

        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $this;
    }

    public function setVerifierMethod(): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::POST_METHOD,
                    $this->rootUrl . sprintf(self::ENDPOINTS['verify_esign'], $this->uuid)
                )
            );

        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $this;
    }
    public function getVerifierData(): self
    {
        try {
            $this->response = $this->getResponseBody(
                $this->provider->createRequest(
                    Provider::GET_METHOD,
                    $this->rootUrl . sprintf(self::ENDPOINTS['verify_esign'], $this->uuid)
                )
            );

        } catch (InvalidResponse $exception) {
            throw new InvalidResponseException($exception->getMessage(), 503, $exception);
        }

        return $this;
    }
}
