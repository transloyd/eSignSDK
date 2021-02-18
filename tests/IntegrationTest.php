<?php

declare(strict_types=1);

namespace Transloyd\Tests\Services\ESign;

use GuzzleHttp\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use PHPUnit\Framework\TestCase;
use Transloyd\Services\ESign\{ESign, Facade, Provider};
use Transloyd\Services\Traits\DotEnvTrait;

class IntegrationTest extends TestCase
{
    use DotEnvTrait;

    private const RESPONSE = [
        'session_created' => 'Створена сесія.',
        'session_data_uploaded' => 'Дані для сесії успішно завантажені.',
        'session_data_set' => 'Запит на встановлення налаштувань сесії виконано.',
        'key_data_uploaded' => 'Дані ключового контейнера успішно завантажені.',
        'e_sign_init' => 'Ініційована асинхронна операція створення електронного підпису.',
    ];

    protected $facade;
    private $client;
    private $provider;

    /**
     * Set up data before test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->initDotEnv();

        $this->client = new Client();
        $this->provider = new Provider(
            $this->client,
            new GuzzleMessageFactory()
        );

        $this->facade = new Facade($this->provider);
    }

    public function testGetESignedDoc(): void
    {
        $eSign = new ESign($this->provider);
        $eSign = $eSign->createSession();
        $this->assertEquals($eSign->getResponse()->message, self::RESPONSE['session_created']);
        $this->assertNotEmpty($eSign->getResponse()->ticketUuid);


        $base64 = '{"base64Data": "e1xydGYxXGFuc2lcYW5zaWNwZzEyNTFcY29jb2FydGYyNTEzClxjb2NvYXRleHRzY2FsaW5nMFxjb2NvYXBsYXRmb3JtMHtcZm9udHRibFxmMFxmc3dpc3NcZmNoYXJzZXQwIEhlbHZldGljYTtcZjFcZnN3aXNzXGZjaGFyc2V0MCBIZWx2ZXRpY2EtQm9sZDt9CntcY29sb3J0Ymw7XHJlZDI1NVxncmVlbjI1NVxibHVlMjU1O30Ke1wqXGV4cGFuZGVkY29sb3J0Ymw7O30KXHBhcGVydzExOTAwXHBhcGVyaDE2ODQwXG1hcmdsMTQ0MFxtYXJncjE0NDBcdmlld3cyODYwMFx2aWV3aDE4MDAwXHZpZXdraW5kMApccGFyZFx0eDU2Nlx0eDExMzNcdHgxNzAwXHR4MjI2N1x0eDI4MzRcdHgzNDAxXHR4Mzk2OFx0eDQ1MzVcdHg1MTAyXHR4NTY2OVx0eDYyMzZcdHg2ODAzXHBhcmRpcm5hdHVyYWxccGFydGlnaHRlbmZhY3RvcjAKClxmMFxmczI0IFxjZjAgXApcdWMwXHUxMDcyIFx1MTA4NCBcdTEwODYgXHUxMDg4IFx1MTA5MCBcdTEwODAgXHUxMDc5IFx1MTA3MiBcdTEwOTAgXHUxMDg2IFx1MTA4OCAgXHUxMDg3IFx1MTA4OCBcdTEwNzIgXHUxMDc0IFx1MTA4MCBcdTEwODEgXAoKXGYxXGIgXHVjMFx1MTA5MCBcdTEwODYgXHUxMDg4IFx1MTA4NCBcdTEwODYgXHUxMDc5IFx1MTA4NSBcdTExMTAgIFx1MTA3NiBcdTEwODAgXHUxMDg5IFx1MTA4MiBcdTEwODAgIFx1MTA4NyBcdTEwNzcgXHUxMDg4IFx1MTA3NyBcdTEwNzYgXHUxMDg1IFx1MTExMCAKXGYwXGIwIFwKClxmMVxiIFx1YzBcdTEwODIgXHUxMDg4IFx1MTExMCBcdTEwODcgXHUxMDgzIFx1MTA3NyBcdTEwODUgXHUxMDg1IFx1MTEwMyAgXHUxMDc2IFx1MTA3NCBcdTEwODAgXHUxMDc1IFx1MTA5MSBcdTEwODUgXHUxMDcyIApcZjBcYjAgXAoKXGYxXGIgXHVjMFx1MTA4NyBcdTEwODYgXHUxMDgzIFx1MTA5MSBcdTEwODYgXHUxMDg5IFx1MTExMCAgXHUxMDg3IFx1MTA3NyBcdTEwODggXHUxMDc3IFx1MTA3NiBcdTEwODUgXHUxMTEwIApcZjBcYjAgXApcdWMwXHUxMDc5IFx1MTA3MiBcdTEwNzYgXHUxMDg1IFx1MTEwMyAgXHUxMDg3IFx1MTExMCBcdTEwNzYgXHUxMDc0IFx1MTExMCBcdTEwODkgXHUxMDgyIFx1MTA3MiAgXHUxMDg5IFx1MTA3MiBcdTEwODMgXHUxMDc3IFx1MTA4NSBcdTEwOTAgXHUxMDczIFx1MTA4MyBcdTEwODYgXHUxMDgyIFx1MTExMCAgXHUxMDg3IFx1MTA4OCBcdTEwODYgXHUxMDc2IFx1MTA4NiBcdTEwODMgXHUxMTAwIFx1MTA4NSBcdTEwODYgXHUxMDc1IFx1MTA4NiAgXHUxMDg4IFx1MTA4MCBcdTEwOTUgXHUxMDcyIFx1MTA3NSBcdTEwNzIgIFx1MTA4NiBcdTEwNzMgXHUxMDgwIFx1MTA3NiBcdTEwNzQgXHUxMDcyIFwKClxmMVxiIFx1YzBcdTEwODggXHUxMDc3IFx1MTA4NCBcdTExMTAgXHUxMDg1IFx1MTEwMCAgXHUxMDg1IFx1MTA3MiBcdTEwOTAgXHUxMTAzIFx1MTA3OCBcdTEwODUgXHUxMDgwIFx1MTA4MSAgKyBcdTEwODUgXHUxMDcyIFx1MTA5MCBcdTExMDMgXHUxMDc4IFx1MTA4MCBcdTEwOTAgXHUxMDc3IFx1MTA4MyBcdTExMDAgIApcZjBcYjAgXApcdWMwXHUxMDg3IFx1MTExMCBcdTEwOTYgXHUxMDgwIFx1MTA4NyBcdTEwODUgXHUxMDgwIFx1MTA4MiAgXHUxMDg3IFx1MTA4OCBcdTEwNzIgXHUxMDc0IFx1MTA4NiBcdTExMTEgIFx1MTA4NyBcdTExMTAgXHUxMDc0IFx1MTExMCBcdTEwODkgXHUxMTEwIFwKClxmMVxiIFx1YzBcdTEwODcgXHUxMDg4IFx1MTA4NiBcdTEwODIgXHUxMDgzIFx1MTA3MiBcdTEwNzYgXHUxMDgyIFx1MTA3MiAgXHUxMDgyIFx1MTA4MyBcdTEwNzIgXHUxMDg3IFx1MTA3MiBcdTEwODUgXHUxMDcyICBcdTEwODIgXHUxMDg4IFx1MTA4MCBcdTEwOTYgXHUxMDgyIFx1MTA4MCAgXHUxMDc2IFx1MTA3NCBcdTEwODAgXHUxMDc1IFx1MTA5MSBcdTEwODUgXHUxMDcyICArIFx1MTA4MiBcdTEwODMgXHUxMDcyIFx1MTA4NyBcdTEwNzIgXHUxMDg1IFx1MTA4MCAgXHUxMDc0IFx1MTA3MiBcdTEwODUgXHUxMDg2IFx1MTA4OSBcdTEwNzIgClxmMFxiMCBcCgpcZjFcYiBcdWMwXHUxMDg0IFx1MTA3MiBcdTEwNzUgXHUxMTEwIFx1MTA4OSBcdTEwOTAgXHUxMDg4IFx1MTA3MiBcdTEwODMgXHUxMTAwICBcdTEwODcgXHUxMDcyIFx1MTA4MyBcdTEwODAgXHUxMDc0IFx1MTA4NSBcdTEwNzIgClxmMFxiMCBcCgpcZjFcYiBcdWMwXHUxMDg5IFx1MTA3NCBcdTExMTAgXHUxMDk1IFx1MTA4MiBcdTEwODAgIFx1MTA3OSBcdTEwNzIgXHUxMDg0IFx1MTExMCBcdTEwODUgXHUxMDgwIFx1MTA5MCBcdTEwODAgClxmMFxiMCBcClwKXAp9"}';
        $eSign = $eSign->loadSessionData($base64);
        $this->assertEquals($eSign->getResponse()->message, self::RESPONSE['session_data_uploaded']);

        $data = '{"caId": "tovUkraine", "cadesType": "CAdESXLong", "signatureType" : "attached"}';
        $eSign = $eSign->setSessionData($data);
        $this->assertEquals($eSign->getResponse()->message, self::RESPONSE['session_data_set']);

        $keyData = '{"base64Data" : "MIIDxAIBAzCCA2EGCSqGSIb3DQEHAaCCA1IEggNOMIIDSjCCAZUGCSqGSIb3DQEHAaCCAYYEggGCMIIBfjCCAXoGCyqGSIb3DQEMCgECoIIBNjCCATIwgbMGCSqGSIb3DQEFDTCBpTBGBgkqhkiG9w0BBQwwOQQgBAvWQE+I8wQ7KJUyNOrkDafZ1jLH2vITSeTfWMOYZ18CAgPoAgEgMA4GCiqGJAIBAQEBAQIFADBbBgsqhiQCAQEBAQEBAzBMBAjmVJOmkAY7fwRAqdbrRfE8cIKAxJZ7Ix9erfZY66TANykdONlr8CXKThf46XINxhW0OiiXXwvB3qNkOLVk6iwXn9ASPm24+sV5BAR6etEAXdY2eISDJ8X2VE6v+TfbZsoiM5z5dhWcAUERFor+vobbLR0URSeNYP8lUxv335EM4c3+iqpdsUSMpAiRlS5zH/MwXVddu3RHztgUIvQGytztvZOf0CrkaFO31JRiccsC2NunlM0t/IguRI11QR8Z7WdO+RN5wkgxMTAvBgkqhkiG9w0BCRUxIgQgeaG9A1P+7iwewKAagCyh8xgrIGET773Adp3PB2eg7gwwggGtBgkqhkiG9w0BBwGgggGeBIIBmjCCAZYwggGSBgsqhkiG9w0BDAoBAqCCAU4wggFKMIGzBgkqhkiG9w0BBQ0wgaUwRgYJKoZIhvcNAQUMMDkEINlo6rfrD3e+tA6R4caLqzK0l8lld7HLnx8dT0SeEt1yAgID6AIBIDAOBgoqhiQCAQEBAQECBQAwWwYLKoYkAgEBAQEBAQMwTAQIPykpDc3Os9sEQKnW60XxPHCCgMSWeyMfXq32WOukwDcpHTjZa/Alyk4X+OlyDcYVtDool18Lwd6jZDi1ZOosF5/QEj5tuPrFeQQEgZHgZJZCDLBkPyh3YL52Pe/bCXPf0nNXuFO6suIX1N1U21vEwiWGpO1E3XhxIbQmRlWpFU72NKxuPfQAHPWNWM/CfcL9o/Abs65Zr6gR5wRc84akbhd8DkcP2e1GqOp3GVlnhpr4x16ZbN/CU6kdgUZ96nB9GLH5yXtUSPPcAD6Jh0YTkID91c3lEcktl5tKW/8DMTEwLwYJKoZIhvcNAQkVMSIEIOUxVKrTgM0tuNSQ1hXgPnanR7kXvH4hPS+O/1CyXCvYMFowMjAOBgoqhiQCAQEBAQIBBQAEIHC/Nurof37F/HtP9BaYqT3AGa7kAaBs6Qzi2E9xYYsdBCCSoobkON4AVZ6UevjYKAlmWPYLKMVzHQKn03tztY43nAICA+g="}';
        $eSign = $eSign->setKeyData($keyData);
        $this->assertEquals($eSign->getResponse()->message, self::RESPONSE['key_data_uploaded']);

        $eSign = $eSign->createESign(123);
        $this->assertEquals($eSign->getResponse()->message, self::RESPONSE['e_sign_init']);

        $eSign = $eSign->getESignedDoc();
        $this->assertNotEmpty($eSign->getResponse()->base64Data);
    }
}
