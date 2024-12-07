<?php

namespace App\Infrastructure\Persistence\InMemory\Repository;

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
        $this->points[$point->id] = $point;
    }

    public function findById(string $id): ?Point
    {
        return $this->points[$id] ?? null;
    }

    public function delete(Point $point): void
    {
        unset($this->points[$point->id]);
    }
}