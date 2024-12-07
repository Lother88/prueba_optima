<?php

namespace App\Infrastructure\Persistence\InMemory;

use App\Domain\Entity\Point;
use App\Domain\Entity\PointUnion;
use App\Domain\Repository\PointUnionRepositoryInterface;

class InMemoryPointUnionRepository implements PointUnionRepositoryInterface
{
    private array $unions = [];

    public function findAll(): array
    {
        return array_values($this->unions);
    }

    public function findById(string $id): ?PointUnion
    {
        return $this->unions[$id] ?? null;
    }

    public function findUnion(Point $point1, Point $point2): ?PointUnion
    {
        foreach ($this->unions as $union) {
            if (
                ($union->point1 === $point1 && $union->point2 === $point2) ||
                ($union->point1 === $point2 && $union->point2 === $point1)
            ) {
                return $union;
            }
        }

        return null;
    }

    public function save(PointUnion $union): void
    {
        $this->unions[$union->getId()] = $union;
    }

    public function delete(PointUnion $union): void
    {
        unset($this->unions[$union->getId()]);
    }
}
