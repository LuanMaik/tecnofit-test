<?php

namespace Test\Unit\Domain\UseCases\Movement;

use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\Exceptions\MovementNotFoundException;
use App\Domain\Repositories\MovementRepositoryInterface;
use App\Domain\UseCases\Movement\RankUsersByMovementHandler;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../../../utils/factories.php';

class RankUsersByMovementHandlerTest extends TestCase
{
    public function test_should_get_rank_successfully()
    {
        // Given
        $repositoryResponse = createValidRankUsersByMovementPaginate();
        $mockRepository = $this->createMock(MovementRepositoryInterface::class);
        $mockRepository->method('getRankUsersByMovementId')->willReturn(clone $repositoryResponse);
        $rankUsersByMovementHandler = new RankUsersByMovementHandler($mockRepository);

        // When
        $response = $rankUsersByMovementHandler->handler(createValidRankUsersByMovementQuery());

        // Then
        $this->assertInstanceOf(RankUsersByMovementPaginate::class, $response);
        $this->assertEquals($repositoryResponse->getMovement(), $response->getMovement());
        $this->assertEquals($repositoryResponse->getRank(), $response->getRank());
        $this->assertEquals($repositoryResponse->getCurrentPage(), $response->getCurrentPage());
        $this->assertEquals($repositoryResponse->getNextPage(), $response->getNextPage());
        $this->assertEquals($repositoryResponse->getPageSize(), $response->getPageSize());
        $this->assertEquals($repositoryResponse->jsonSerialize(), $response->jsonSerialize());
    }

    public function test_should_throw_exception_movement_not_found()
    {
        // Given
        $mockRepository = $this->createMock(MovementRepositoryInterface::class);
        $mockRepository->method('getRankUsersByMovementId')->willThrowException(new MovementNotFoundException);
        $rankUsersByMovementHandler = new RankUsersByMovementHandler($mockRepository);

        // Expected
        $this->expectException(MovementNotFoundException::class);

        // When
        $rankUsersByMovementHandler->handler(createValidRankUsersByMovementQuery());
    }
}