<?php

namespace Transloyd\Services\ESign;

use Transloyd\Services\Traits\DotEnvTrait;

class ESignManager
{
    use DotEnvTrait;

    private $eSign;
    private $sessionData = '{"caId": "tovUkraine", "cadesType": "CAdESXLong", "signatureType" : "attached"}';

    public function __construct(ESign $eSign)
    {
        $this->eSign = $eSign;

        $this->initDotEnv();
    }

    public function getSignedDocumentRaw($fileData, $keyData, $keyPass): ?string
    {
        $eSign = $this->eSign
            ->createSession()
            ->loadSessionData($fileData)
            ->setSessionData($this->sessionData)
            ->setKeyData($keyData)
            ->createESign($keyPass)
            ->getESignedDoc();

        return $eSign->getResponse()->base64Data ?? null;
    }

    public function checkVerify(string $base64): ?\stdClass
    {
        $eSign = $this->eSign
            ->createSession()
            ->setSessionData($this->sessionData)
            ->loadEsSessionData($base64)
            ->setVerifierMethod()
            ->getVerifierData();

        $response = $eSign->getResponse();
        $eSign->deleteSession();

        return $response;
    }

    public function checkVerifyKeyData(string $keyData, string $keyPass): ?\stdClass
    {
        $eSign = $this->eSign
            ->createSession()
            ->setKeyData($keyData)
            ->putKeyData($keyPass);

        $response = $eSign->getResponse();
        $eSign->deleteSession();

        return $response;
    }
}