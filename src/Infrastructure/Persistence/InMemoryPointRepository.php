<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Point;
use App\Domain\Repository\PointRepositoryInterface;

class InMemoryPointRepository implements PointRepositoryInterface
{
    private array $points = [];

    public function findAll(): array
    {
        return $this->points;
    }

    public function save(Point $point): void
    {
        $this->points[$point->getId()] = $point;
    }

    public function findById(string $id): ?Point
    {
        return $this->points[$id] ?? null;
    }
}
