<?php

namespace App\Domain\Entity;

class Point
{
    private string $id;
    private float $x;
    private float $y;

    public function __construct(string $id, float $x, float $y)
    {
        if (!is_numeric($x) || !is_numeric($y)) {
            throw new \InvalidArgumentException('Las coordenadas deben ser numéricas.');
        }

        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }
}
