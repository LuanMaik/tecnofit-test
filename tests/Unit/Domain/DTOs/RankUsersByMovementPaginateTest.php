<?php

namespace Tests\Unit\Domain\DTOs;

use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\DTOs\UserRank;
use App\Domain\Entities\Movement;
use PHPUnit\Framework\TestCase;

class RankUsersByMovementPaginateTest extends TestCase
{
    public function test_should_create_valid_instance_successfully()
    {
        // Given
        $movement = new Movement(1, 'Deadlift');
        $rankUsers = [new UserRank(1, 'Luan Maik', '2021-01-01 00:00:00', 180, 1)];
        $currentPage = 1;
        $pageSize = 10;
        $nextPage = null;

        // When
        $dto = new RankUsersByMovementPaginate(clone $movement, $rankUsers, $currentPage, $pageSize, $nextPage);

        // Then
        $this->assertInstanceOf(Movement::class, $dto->getMovement());
        $this->assertEquals($movement->jsonSerialize(), $dto->getMovement()->jsonSerialize());
        $this->assertEquals($dto->getRank(), $rankUsers);
        $this->assertEquals($dto->getCurrentPage(), $currentPage);
        $this->assertEquals($dto->getPageSize(), $pageSize);
        $this->assertEquals($dto->getNextPage(), $nextPage);
        $this->assertEquals($dto->jsonSerialize(), [
            'movement' => $movement,
            'rank' => $rankUsers,
            'currentPage' => $currentPage,
            'pageSize' => $pageSize,
            'nextPage' => $nextPage
        ]);
    }

    public function test_should_throw_exception_when_nextpage_equals_currentpage()
    {
        // Given
        $movement = new Movement(1, 'Deadlift');
        $rankUsers = [new UserRank(1, 'Luan Maik', '2021-01-01 00:00:00', 180, 1)];
        $currentPage = 1;
        $pageSize = 10;
        $nextPage = $currentPage;

        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The nextPage can not be equals currentPage");

        // When
        new RankUsersByMovementPaginate(clone $movement, $rankUsers, $currentPage, $pageSize, $nextPage);
    }

    public function test_should_throw_exception_when_currentpage_greater_than_nextpage()
    {
        // Given
        $movement = new Movement(1, 'Deadlift');
        $rankUsers = [new UserRank(1, 'Luan Maik', '2021-01-01 00:00:00', 180, 1)];
        $currentPage = 2;
        $pageSize = 10;
        $nextPage = $currentPage - 1;

        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The nextPage when informed, must be greater than currentPage");

        // When
        new RankUsersByMovementPaginate(clone $movement, $rankUsers, $currentPage, $pageSize, $nextPage);
    }

    public function test_should_throw_exception_when_rank_count_greater_than_pagesize()
    {
        // Given
        $movement = new Movement(1, 'Deadlift');
        $rankUsers = [
            new UserRank(1, 'Luan Maik', '2021-01-01 00:00:00', 180, 1),
            new UserRank(1, 'Luan Maik', '2021-01-01 00:00:00', 180, 1)
        ];
        $currentPage = 2;
        $pageSize = 1;
        $nextPage = null;

        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The rank array count can not be greater than pageSize");

        // When
        new RankUsersByMovementPaginate(clone $movement, $rankUsers, $currentPage, $pageSize, $nextPage);
    }
}