<?php
declare(strict_types=1);

use App\Domain\Exceptions\MovementNotFoundException;
use App\Domain\UseCases\Movement\RankUsersByMovementHandler;
use App\Domain\UseCases\Movement\RankUsersByMovementQuery;
use App\WebAPI\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return function(\App\WebAPI\App $app) {

    $app->getRouter()->get('/movements/{id}/rank', function (ServerRequestInterface $request, array $args) use ($app): ResponseInterface {
        try {
            $queryParams = $request->getQueryParams();

            $page = $queryParams['page'] ?? 1;
            $pageSize = $queryParams['pageSize'] ?? 10;

            $handler = $app->getContainer()->get(RankUsersByMovementHandler::class);
            $query = new RankUsersByMovementQuery((int)$args['id'], (int)$page, (int)$pageSize);
            $rank = $handler->handler($query);
            return ApiResponse::success($rank);
        } catch (MovementNotFoundException $ex) {
            return ApiResponse::notFound($ex->getMessage());
        } catch (InvalidArgumentException $ex) {
            return ApiResponse::badRequest($ex->getMessage());
        }
    });

};