<?php

namespace App\Infrastructure\Database\Repositories;

use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\DTOs\UserRank;
use App\Domain\Entities\Movement;
use App\Domain\Exceptions\MovementNotFoundException;
use App\Domain\Repositories\MovementRepositoryInterface;
use App\Infrastructure\Database\Database;
use PDO;

class MysqlMovementRepository implements MovementRepositoryInterface
{

    public function getById(int $movementId): Movement
    {
        $stmt = Database::getConnection()->query('SELECT * FROM movement WHERE id = :id;');
        $stmt->bindValue(':id', $movementId, PDO::PARAM_INT);
        $stmt->execute();

        $movement = $stmt->fetchObject( Movement::class);

        if($movement === false) {
            throw new MovementNotFoundException();
        }

        return $movement;
    }

    /**
     * @throws MovementNotFoundException
     */
    public function getRankUsersByMovementId(int $movementId, int $page = 0, int $pageSize = 10): RankUsersByMovementPaginate
    {
        // get movement data
        $movement = $this->getById($movementId);

        $offsetPagination = ($page === 0) ? 0 : ($page - 1) * $pageSize + 1;

        /**
         * Define a new page size, to check if exist next page in pagination
         */
        $pageSizeCheckNext = $pageSize + 1;

        $stmt = Database::getConnection()
            ->prepare("SELECT pr.user_id, u.name as user_name, pr.date, MAX(pr.value) as record,
                                    DENSE_RANK() OVER (ORDER BY MAX(value) DESC) `rank` 
                             FROM personal_record pr
                             JOIN movement m ON(pr.movement_id = m.id)
                             JOIN user u ON(pr.user_id = u.id)
                             WHERE pr.movement_id = :movementId
                             GROUP BY pr.user_id
                             LIMIT :offset, :limit");
        $stmt->bindValue(':movementId', $movementId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSizeCheckNext, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offsetPagination, PDO::PARAM_INT);
        $stmt->execute();

        /**
         * @var UserRank[]
         */
        $usersRank = $stmt->fetchAll(PDO::FETCH_CLASS, UserRank::class);

        $hasNextPage = false;

        /**
         * Check if the total records found its equal page size + 1,
         * and remove the last element, retrieved just to check if exist next page
         */
        if(count($usersRank) === $pageSizeCheckNext) {
            array_pop($usersRank);
            $hasNextPage = true;
        }

        return new RankUsersByMovementPaginate(
            $movement,
            $usersRank,
            $page,
            $pageSize,
            ($hasNextPage) ? $page + 1 : null);
    }
}