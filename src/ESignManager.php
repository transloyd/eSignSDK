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

    public function getSignedDocumentRaw($fileData, $keyData): ?string
    {
        $eSign = $this->eSign
            ->createSession()
            ->loadSessionData($fileData)
            ->setSessionData($this->sessionData)
            ->setKeyData($keyData)
            ->createESign()
            ->getESignedDoc();

        return $eSign->getResponse()->base64Data ?? null;
    }
}