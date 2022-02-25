<?php
declare(strict_types=1);

namespace App\WebAPI;

use Exception;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Strategy\StrategyAwareInterface;
use League\Route\Router;
use League\Container\Container;
use League\Route\Strategy\ApplicationStrategy;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;


class App
{
    private StrategyAwareInterface $router;
    private Container $container;

    public function __construct()
    {
        $this->container = new Container;
        $strategy = (new ApplicationStrategy)->setContainer($this->container);
        $this->router = (new Router)->setStrategy($strategy);

        $this->registerContainerDefinitions();
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        $routes = require __DIR__ . '/config/routes.php';
        $routes($this->router, $this->container);
    }

    private function registerContainerDefinitions()
    {
        $definitions = require __DIR__ . '/config/container-definitions.php';
        $definitions($this->container);
    }

    public function run()
    {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        try {
            $response = $this->router->dispatch($request);
        } catch (NotFoundException $ex) {
            $response = ApiResponse::notFound();
        } catch (Exception $ex) {
            if(getenv('MODE') !== 'PROD') {
                throw $ex;
            }
            $response = ApiResponse::serverError();
        }

        // send the response to the browser
        (new SapiEmitter)->emit($response);
    }
}