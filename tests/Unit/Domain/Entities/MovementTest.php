<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Movement;
use PHPUnit\Framework\TestCase;

class MovementTest extends TestCase
{
    public function test_should_create_valid_instance_successfully()
    {
        // When
        $movement = new Movement(1, 'Deadlift');

        // Then
        $this->assertEquals(['id' => 1, 'name' => 'Deadlift'], $movement->jsonSerialize());
        $this->assertEquals(1, $movement->getId());
        $this->assertEquals('Deadlift', $movement->getName());
    }

    public function test_should_create_valid_instance_from_array_successfully()
    {
        // When
        $movement = Movement::fromArray(['id' => 1, 'name' => 'Deadlift']);

        // Then
        $this->assertEquals(['id' => 1, 'name' => 'Deadlift'], $movement->jsonSerialize());
        $this->assertEquals(1, $movement->getId());
        $this->assertEquals('Deadlift', $movement->getName());
    }

    public function test_should_throw_exception_when_empty_name()
    {
        // Expected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The movement name can not be empty");

        // When
        new Movement(1, ' ');
    }
}