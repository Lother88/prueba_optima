<?php

namespace App\Domain\Entity;

class Point
{
    public function __construct(
        public readonly string $id,
        public readonly float $x,
        public readonly float $y
    ) {
        if (!is_numeric($x) || !is_numeric($y)) {
            throw new \InvalidArgumentException('Las coordenadas deben ser numéricas.');
        }
    }
}
