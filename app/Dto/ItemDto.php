<?php

namespace App\Dto;

final class ItemDto
{
    public readonly string $name;

    public readonly int $amount;

    public function __construct(string $name, int $amount)
    {
        $this->name = $name;
        $this->amount = $amount;
    }
}
