<?php

namespace App\Domain\DTOs;

//pr.user_id, u.name as user_name, pr.date, MAX(pr.value) as record,
//                                    DENSE_RANK() OVER (ORDER BY MAX(value) DESC) `rank`
class UserRank implements \JsonSerializable
{
    private int $id;
    private string $name;
    private string $date;
    private float $record;
    private int $rank;

    /**
     * @param int $id
     * @param string $name
     * @param string $date
     * @param float $record
     * @param int $rank
     */
    public function __construct(int $id, string $name, string $date, float $record, int $rank)
    {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->record = $record;
        $this->rank = $rank;
    }

    /**
     * @return int
     */
    public function getId(): int
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

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getRecord(): float
    {
        return $this->record;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }


    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
            'record' => $this->record,
            'rank' => $this->rank
        ];
    }
}