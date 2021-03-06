<?php
declare(strict_types=1);

namespace App\Domain\DTOs;


use App\Domain\Entities\Movement;

class RankUsersByMovementPaginate implements \JsonSerializable
{
    private Movement $movement;
    private array $rank;
    private int $currentPage;
    private ?int $nextPage;
    private int $pageSize;

    /**
     * @param Movement $movement
     * @param UserRank[] $rank
     * @param int $currentPage
     * @param int $pageSize
     * @param int|null $nextPage
     */
    public function __construct(Movement $movement, array $rank, int $currentPage, int $pageSize, ?int $nextPage = null)
    {
        if($currentPage === $nextPage) {
            throw new \InvalidArgumentException("The nextPage can not be equals currentPage");
        }

        if($nextPage !== null and $nextPage < $currentPage) {
            throw new \InvalidArgumentException("The nextPage when informed, must be greater than currentPage");
        }

        if(count($rank) > $pageSize) {
            throw new \InvalidArgumentException("The rank array count can not be greater than pageSize");
        }

        $this->movement = $movement;
        $this->rank = $rank;
        $this->currentPage = $currentPage;
        $this->nextPage = $nextPage;
        $this->pageSize = $pageSize;
    }

    /**
     * @return Movement
     */
    public function getMovement(): Movement
    {
        return $this->movement;
    }

    /**
     * @return array
     */
    public function getRank(): array
    {
        return $this->rank;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'movement' => $this->movement,
            'rank' => $this->rank,
            'currentPage' => $this->currentPage,
            'pageSize' => $this->pageSize,
            'nextPage' => $this->nextPage
        ];
    }
}