<?php

namespace Test\EndToEnd;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class EndToEndTestCase extends TestCase
{
    private $http;

    public function setUp(): void
    {
        $this->http = new Client([
            'base_uri' => getenv('API_BASE_URL'),
            'http_errors' => false
        ]);
    }

    public function tearDown(): void
    {
        $this->http = null;
    }

    /**
     * @param string $uri
     * @param array $queryParams example ['page' => 1, 'pageSize' => 10]
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function httpGet(string $uri, array $queryParams = []): ResponseInterface
    {
        return $this->http->get($uri, [
            'query' => $queryParams
        ]);
    }

}