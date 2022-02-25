<?php
declare(strict_types=1);

use App\Domain\Exceptions\MovementNotFoundException;
use App\Domain\UseCases\Movement\RankUsersByMovementHandler;
use App\Domain\UseCases\Movement\RankUsersByMovementQuery;
use App\WebAPI\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return function(League\Route\Router $router, League\Container\Container $container) {

    $router->get('/movimentos/{id}/rank', function (ServerRequestInterface $request, array $args) use ($container): ResponseInterface {
        try {
            $queryParams = $request->getQueryParams();

            $page = $queryParams['page'] ?? 0;
            $pageSize = $queryParams['pageSize'] ?? 10;

            $handler = $container->get(RankUsersByMovementHandler::class);
            $query = new RankUsersByMovementQuery((int)$args['id'], (int)$page, (int)$pageSize);
            $rank = $handler->handler($query);
            return ApiResponse::success($rank);
        } catch (MovementNotFoundException | InvalidArgumentException $ex) {
            return ApiResponse::badRequest($ex->getMessage());
        }
    });

};