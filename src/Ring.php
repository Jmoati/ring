<?php

declare(strict_types=1);

namespace Jmoati\Ring;

use Jmoati\Ring\Exception\ThumbnailNotFound;
use Jmoati\Ring\Model\Authentication;
use Jmoati\Ring\Model\Doorbot;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Ring
{
    private HttpClientInterface $httpClient;
    private string $refreshToken;
    private SerializerInterface $serializer;
    private ?Authentication $authentication = null;

    public function __construct(HttpClientInterface $httpClient, string $refreshToken, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->refreshToken = $refreshToken;
        $this->serializer = $serializer;
    }

    public function updateSnapshots(): bool
    {
        $response = $this
            ->httpClient
            ->request(Request::METHOD_PUT, 'snapshots/update_all', $this->getDefaultOpions() + [
                                             'json' => [
                                                 'doorbot_ids' => array_map(fn ($a) => $a->id, $this->getDoorbots()),
                                                 'refresh' => true,
                                             ],
                                         ]);

        return Response::HTTP_NO_CONTENT === $response->getStatusCode();
    }

    public function getSnapshot(int $doorbotId): string
    {
        $url = sprintf('snapshots?doorbot_ids[]=%d', $doorbotId);
        $response = $this
            ->httpClient
            ->request(Request::METHOD_GET, $url, $this->getDefaultOpions());

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new ThumbnailNotFound($doorbotId);
        }

        $url = sprintf('snapshots/image/%d', $doorbotId);

        $response = $this
            ->httpClient
            ->request(Request::METHOD_GET, $url, $this->getDefaultOpions());

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new ThumbnailNotFound($doorbotId);
        }

        return $response->getContent();
    }

    public function getDoorbots(): array
    {
        $response = $this
            ->httpClient
            ->request(Request::METHOD_GET, 'ring_devices', $this->getDefaultOpions());

        return $this->serializer->deserialize($response->getContent(), Doorbot::class.'[]', JsonEncoder::FORMAT);
    }

    private function getDefaultOpions(): array
    {
        return [
            'base_uri' => 'https://api.ring.com/clients_api/',
            'headers' => [
                'Authorization' => 'Bearer '.($this->authentication->accessToken ?? $this->authenticate()->accessToken),
            ],
        ];
    }

    private function authenticate(): Authentication
    {
        $response = $this
            ->httpClient
            ->request(Request::METHOD_POST, 'https://oauth.ring.com/oauth/token', [
                'json' => [
                    'client_id' => 'ring_official_android',
                    'scope' => 'client',
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->refreshToken,
                ],
            ]);

        $this->authentication = $this->serializer->deserialize(
            $response->getContent(),
            Authentication::class,
            JsonEncoder::FORMAT
        );

        return $this->authentication;
    }
}
