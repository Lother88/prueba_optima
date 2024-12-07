<?php

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class PointUnion
{
    public function __construct(
        public readonly string $id,
        public readonly Point $point1,
        public readonly Point $point2,
        public float $distance
    ) {
        if ($point1->getId() === $point2->getId()) {
            throw new \InvalidArgumentException('Los puntos de una unión no pueden ser los mismos.');
        }

        if (empty($id)) {
            $this->id = Uuid::v4()->toRfc4122();
        }
    }
}
