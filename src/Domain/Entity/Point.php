<?php

namespace App\Domain\Entity;

class Point
{
    public function __construct(
        public readonly string $id,
        public readonly float $x,
        public readonly float $y,
    ) {
        if (empty($id) || !is_string($id)) {
            throw new \InvalidArgumentException('El ID debe ser un string no vacío.');
        }

        if (strlen($id) > 255) {
            throw new \InvalidArgumentException('El ID no puede superar los 255 caracteres.');
        }

        if (!is_numeric($x) || !is_numeric($y)) {
            throw new \InvalidArgumentException('Las coordenadas deben ser numéricas.');
        }
    }
}
