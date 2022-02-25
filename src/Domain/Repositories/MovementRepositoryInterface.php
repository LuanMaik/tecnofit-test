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
     * @param int $idMovement
     * @return Movement|bool
     * @throws MovementNotFoundException
     */
    public function getById(int $idMovement): Movement|bool;

    /**
     * @param int $idMovement
     * @param int $page starts from 0 (zero)
     * @param int $pageSize
     * @return RankUsersByMovementPaginate
     */
    public function getRankUsersByMovementId(int $idMovement, int $page = 0, int $pageSize = 10): RankUsersByMovementPaginate;
}