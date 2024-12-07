<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Point;
use App\Domain\Entity\PointUnion;

interface PointUnionRepositoryInterface
{
    public function findAll(): array;

    public function findById(string $id): ?PointUnion;

    public function findUnion(Point $point1, Point $point2): ?PointUnion;

    public function save(PointUnion $union): void;

    public function delete(PointUnion $union): void;
}
