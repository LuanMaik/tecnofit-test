<?php
declare(strict_types=1);

use App\Domain\Repositories\MovementRepositoryInterface;
use App\Domain\UseCases\Movement\RankUsersByMovementHandler;
use App\Infrastructure\Database\Repositories\MysqlMovementRepository;

return function(League\Container\Container $container) {
    // Repositories
    $container->add(MovementRepositoryInterface::class, MysqlMovementRepository::class);

    // Use Cases
    $container->add(RankUsersByMovementHandler::class, RankUsersByMovementHandler::class)
        ->addArgument(MovementRepositoryInterface::class);
};