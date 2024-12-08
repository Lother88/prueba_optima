<?php

namespace App\Application\Service;

use App\Domain\Interface\PathFindingAlgorithm;
use App\Domain\Repository\PointRepositoryInterface;
use App\Domain\Repository\PointUnionRepositoryInterface;
use App\Domain\ValueObject\PathResult;

class PathFindingService
{
    private PointRepositoryInterface $pointRepository;
    private PointUnionRepositoryInterface $unionRepository;
    private PathFindingAlgorithm $algorithm;

    public function __construct(
        PointRepositoryInterface $pointRepository,
        PointUnionRepositoryInterface $unionRepository,
        PathFindingAlgorithm $algorithm,
    ) {
        $this->pointRepository = $pointRepository;
        $this->unionRepository = $unionRepository;
        $this->algorithm = $algorithm;
    }

    /**
     * Calcular el camino más corto entre dos puntos.
     */
    public function calculateShortestPath(string $startId, string $endId): PathResult
    {
        $startPoint = $this->pointRepository->findById($startId);
        $endPoint = $this->pointRepository->findById($endId);

        if (!$startPoint || !$endPoint) {
            throw new \InvalidArgumentException('Uno o ambos puntos no existen.');
        }

        $graph = $this->buildGraph();

        return $this->algorithm->findPath($graph, $startId, $endId);
    }

    /**
     * Construir el grafo en base a las uniones definidas por el usuario.
     */
    private function buildGraph(): array
    {
        $graph = [];
        $points = $this->pointRepository->findAll();
        $unions = $this->unionRepository->findAll();

        foreach ($points as $point) {
            $graph[$point->id] = [];
        }

        foreach ($unions as $union) {
            $start = $union->point1->id;
            $end = $union->point2->id;
            $distance = $union->distance;

            $graph[$start][$end] = $distance;
            $graph[$end][$start] = $distance; // Bidireccional
        }

        return $graph;
    }
}
