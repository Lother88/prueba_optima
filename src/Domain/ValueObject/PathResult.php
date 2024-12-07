<?php

namespace App\Domain\ValueObject;

class PathResult
{
    public function __construct(
        public readonly array $path,
        public readonly float $totalDistance
    ){
        $this->validatePath();
        $this->validateTotalDistance();
    }

    private function validatePath(): void
    {
        if (empty($this->path)) {
            throw new \InvalidArgumentException('El camino no puede estar vacío.');
        }

        foreach ($this->path as $node) {
            if (!is_string($node)) {
                throw new \InvalidArgumentException('Cada nodo en el camino debe ser un string.');
            }
        }
    }

    private function validateTotalDistance(): void
    {
        if ($this->totalDistance < 0) {
            throw new \InvalidArgumentException('La distancia total no puede ser negativa.');
        }

        if (!is_float($this->totalDistance)) {
            throw new \InvalidArgumentException('La distancia total debe ser un número de tipo float.');
        }
    }
}
