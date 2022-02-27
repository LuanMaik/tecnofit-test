<?php

namespace Test\EndToEnd;

use App\WebAPI\App;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class EndToEndTestCase extends TestCase
{
    private App $app;

    public function setUp(): void
    {
        $this->app = new App();
    }

    /**
     * @param string $uri
     * @param array $queryParams example ['page' => 1, 'pageSize' => 10]
     * @return ResponseInterface
     */
    public function httpGet(string $uri, array $queryParams = []): ResponseInterface
    {
        $request = new ServerRequest(
            [],
            [],
            new Uri(getenv('API_BASE_URL').$uri),
            'GET',
            'php://input',
            [],
            [],
            $queryParams,
            null,
            '1.1');

        return $this->app->run($request);
    }

}