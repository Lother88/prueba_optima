<?php

namespace App\Domain\ValueObject;

class PathResult
{
    private array $path; // IDs de los puntos recorridos
    private float $totalDistance;

    public function __construct(array $path, float $totalDistance)
    {
        $this->path = $path;
        $this->totalDistance = $totalDistance;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function getTotalDistance(): float
    {
        return $this->totalDistance;
    }
}
