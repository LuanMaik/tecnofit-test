<?php
declare(strict_types=1);

namespace App\Domain\UseCases\Movement;

use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\Repositories\MovementRepositoryInterface;
use InvalidArgumentException;

class RankUsersByMovementHandler
{
    private MovementRepositoryInterface $movementRepository;

    /**
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * @param RankUsersByMovementQuery $query
     * @return RankUsersByMovementPaginate
     */
    public function handler(RankUsersByMovementQuery $query): RankUsersByMovementPaginate
    {
        return $this->movementRepository->getRankUsersByMovementId(
            $query->getMovementId(),
            $query->getPage(),
            $query->getPageSize());
    }
}



class RankUsersByMovementQuery
{
    private int $movementId;
    private int $page;
    private int $pageSize;

    /**
     * @param int $movementId
     * @param int $page
     * @param int $pageSize
     */
    public function __construct(int $movementId, int $page = 1, int $pageSize = 10)
    {
        if(empty($movementId) or $movementId < 0) {
            throw new InvalidArgumentException("The movementId it's required");
        }

        if($page < 1) {
            throw new InvalidArgumentException("The page number must be greater than 0");
        }

        if($pageSize < 1) {
            throw new InvalidArgumentException("The page size number must be greater than 0");
        }

        $this->movementId = $movementId;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    /**
     * @return int
     */
    public function getMovementId(): int
    {
        return $this->movementId;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}