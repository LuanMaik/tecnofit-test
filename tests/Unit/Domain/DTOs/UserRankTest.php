<?php

namespace Tests\Unit\Domain\DTOs;

use App\Domain\DTOs\UserRank;
use PHPUnit\Framework\TestCase;

class UserRankTest extends TestCase
{
    public function test_should_create_valid_instance_successfully()
    {
        // When
        $userRank = new UserRank(1,'Luan','2022-01-01 00:00:00',130,1);

        // Then
        $this->assertEquals([
            'id' => 1,
            'name' => 'Luan',
            'date' => '2022-01-01 00:00:00',
            'record' => 130,
            'rank' => 1
        ], $userRank->jsonSerialize());
        $this->assertEquals(1, $userRank->getId());
        $this->assertEquals('Luan', $userRank->getName());
        $this->assertEquals('2022-01-01 00:00:00', $userRank->getDate());
        $this->assertEquals(130, $userRank->getRecord());
        $this->assertEquals(1, $userRank->getRank());
    }
}