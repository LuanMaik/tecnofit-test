<?php
declare(strict_types=1);

use App\Domain\Repositories\MovementRepositoryInterface;
use App\Domain\UseCases\Movement\RankUsersByMovementHandler;
use App\Infrastructure\Database\Repositories\MysqlMovementRepository;

return function(\App\WebAPI\App $app) {
    // Repositories
    $app->getContainer()->add(MovementRepositoryInterface::class, MysqlMovementRepository::class);

    // Use Cases
    $app->getContainer()->add(RankUsersByMovementHandler::class, RankUsersByMovementHandler::class)
        ->addArgument(MovementRepositoryInterface::class);
};