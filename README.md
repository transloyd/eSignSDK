# Products Service Facade

![version](https://img.shields.io/badge/version-0.1.1-blue.svg)

Service facade for API

## Setup

```bash
$ composer install

````
## Tests


````bash
$ vendor/bin/phpunit tests/
````


## How to use

````
public function __constructor( ..., GuzzleHttp\Client $client){

...

    $this->provider = new Provider($client, new GuzzleMessageFactory());

...

}

...

$documentInBase64 = BASE_64_HASH;
$keyDataInBase64 = BASE_64_HASH;

$eSign = new ESign($this->provider);
$eSignManager = new ESignManager($eSign);

$signedDocumentRaw = $eSignManager->getSignedDocumentRaw($documentInBase64, $keyDataInBase64);

````

But better is to use Service Container (Dependency Injection)!
