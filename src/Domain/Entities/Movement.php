<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use JetBrains\PhpStorm\Pure;

class Movement implements \JsonSerializable
{
    private ?int $id;
    private string $name;

    /**
     * @param int|null $id
     * @param string $name
     */
    public function __construct(?int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    #[Pure]
    public static function fromArray(array $data) {
        return new Movement($data['id'], $data['name']);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}