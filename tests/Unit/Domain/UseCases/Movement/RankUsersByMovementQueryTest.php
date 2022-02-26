<?php

namespace Test\Unit\Domain\UseCases\Movement;

use App\Domain\UseCases\Movement\RankUsersByMovementQuery;
use PHPUnit\Framework\TestCase;

class RankUsersByMovementQueryTest extends TestCase
{
    public function test_should_create_valid_instance_successfully()
    {
        // When
        $query = new RankUsersByMovementQuery(1, 2, 10);

        // Then
        $this->assertEquals(1, $query->getMovementId());
        $this->assertEquals(2, $query->getPage());
        $this->assertEquals(10, $query->getPageSize());
    }

    /**
     * @dataProvider provideZeroAndNegative
     */
    public function test_should_throw_exception_when_movementid_zero_or_negative(int $movementId)
    {
        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The movementId it's required");

        // When
        new RankUsersByMovementQuery($movementId, 2, 10);
    }

    /**
     * @dataProvider provideZeroAndNegative
     */
    public function test_should_throw_exception_when_page_zero_or_negative(int $page)
    {
        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The page number must be greater than 0");

        // When
        new RankUsersByMovementQuery(1, $page, 10);
    }

    /**
     * @dataProvider provideZeroAndNegative
     */
    public function test_should_throw_exception_when_pagesize_zero_or_negative(int $pageSize)
    {
        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The page size number must be greater than 0");

        // When
        new RankUsersByMovementQuery(1, 1, $pageSize);
    }

    public function provideZeroAndNegative(): array
    {
        return [[0], [-1]];
    }
}