<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class PointUnion
{
    public function __construct(
        public readonly string $id,
        public readonly Point $point1,
        public readonly Point $point2,
        public float $distance,
    ) {
        if ($point1->id === $point2->id) {
            throw new \InvalidArgumentException('Los puntos de una unión no pueden ser los mismos.');
        }

        if (!Uuid::isValid($this->id)) {
            throw new \InvalidArgumentException('Invalid UUID provided');
        }
    }

    public static function create(Point $point1, Point $point2, float $distance, ?string $id = null): self
    {
        $id = $id ?? Uuid::v4()->toRfc4122();
        return new self($id, $point1, $point2, $distance);
    }
}
