<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Point;

interface PointRepositoryInterface
{
    public function findAll(): array; // Retorna un array de puntos
    public function save(Point $point): void;
    public function findById(string $id): ?Point;
}
