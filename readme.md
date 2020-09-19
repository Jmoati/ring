```php
<?php

use Jmoati\Ring\Model\Doorbot;
use Jmoati\Ring\Ring;
use Jmoati\Ring\Serializer\DoorbotNormalizer;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require __DIR__ . '/vendor/autoload.php';

$httpClient = HttpClient::create();
$serializer = new Serializer([
        new DoorbotNormalizer(),
        new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter()),
    ], [
        new JsonEncoder(),
    ]);

$ring = new Ring(
    $httpClient,
    $refreshToken,
    $serializer
);

if ($ring->updateSnapshots()) {
    $doorbots = $ring->getDoorbots();
    
    array_walk(
        $doorbots,
        fn(Doorbot $device) => file_put_contents('/tmp/'.$device->id.'.jpeg', $ring->getSnapshot($device->id))
    );
}

```