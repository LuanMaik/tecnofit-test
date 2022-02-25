<?php


use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\DTOs\UserRank;
use App\Domain\Entities\Movement;
use App\Domain\UseCases\Movement\RankUsersByMovementQuery;

function createValidRankUsersByMovementPaginate()
{
    return new RankUsersByMovementPaginate(
        new Movement(1, 'Deadlift'),
        [new UserRank(1, 'Luan Maik', '2021-01-01 00:00:00', 180, 1)],
        1,
        10,
        null
    );
}

function createValidRankUsersByMovementQuery()
{
    return new RankUsersByMovementQuery(1, 1, 10);
}