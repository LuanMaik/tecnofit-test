<?php
declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\Entities\Movement;
use App\Domain\Exceptions\MovementNotFoundException;

interface MovementRepositoryInterface
{
    /**
     * Return a movement
     * @param int $movementId
     * @return Movement
     * @throws MovementNotFoundException
     */
    public function getById(int $movementId): Movement;

    /**
     * @param int $movementId
     * @param int $page starts from 1
     * @param int $pageSize
     * @return RankUsersByMovementPaginate
     */
    public function getRankUsersByMovementId(int $movementId, int $page = 1, int $pageSize = 10): RankUsersByMovementPaginate;
}