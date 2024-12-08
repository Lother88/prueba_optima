<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Point;

interface PointRepositoryInterface
{
    public function findAll(): array;

    public function save(Point $point): void;

    public function findById(string $id): ?Point;

    public function delete(Point $point): void;
}
