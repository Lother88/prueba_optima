<?php

namespace App\Application\Service;

use App\Domain\Entity\Point;
use App\Domain\Repository\PointRepositoryInterface;
use App\Domain\Interface\PathFindingAlgorithm;
use App\Domain\ValueObject\PathResult;

class PathFindingService
{
    private PointRepositoryInterface $pointRepository;
    private PathFindingAlgorithm $algorithm;
    private array $connections = []; // Almacena las conexiones manuales

    public function __construct(PointRepositoryInterface $pointRepository, PathFindingAlgorithm $algorithm)
    {
        $this->pointRepository = $pointRepository;
        $this->algorithm = $algorithm;
    }

    /**
     * Configura las conexiones manuales entre puntos.
     */
    public function setConnections(array $connections): void
    {
        $this->connections = $connections;
    }

    /**
     * Calcula el camino más corto entre dos puntos.
     *
     * @param string $startId ID del punto de inicio.
     * @param string $endId ID del punto de destino.
     * @return PathResult Resultado del cálculo.
     * @throws \Exception Si no se encuentran los puntos o el camino es inválido.
     */
    public function calculateShortestPath(string $startId, string $endId): PathResult
    {
        // Obtener todos los puntos desde el repositorio
        $points = $this->pointRepository->findAll();

        // Convertir los puntos en un grafo para el algoritmo
        $graph = $this->convertPointsToGraph($points);

        // Buscar los puntos de inicio y fin
        $startPoint = $this->pointRepository->findById($startId);
        $endPoint = $this->pointRepository->findById($endId);

        if (!$startPoint || !$endPoint) {
            throw new \Exception("Puntos de inicio o destino no encontrados.");
        }

        // Calcular el camino más corto
        return $this->algorithm->findPath($graph, $startId, $endId);
    }

    /**
     * Convierte los puntos en un grafo basado en las conexiones manuales.
     *
     * @param array $points Lista de puntos.
     * @return array Grafo generado.
     */
    private function convertPointsToGraph(array $points): array
    {
        $graph = [];

        foreach ($points as $point) {
            $graph[$point->id] = [];
        }

        foreach ($this->connections as $connection) {
            [$start, $end, $distance] = $connection;

            // Añade la conexión bidireccional
            $graph[$start][$end] = $distance;
            $graph[$end][$start] = $distance;
        }

        return $graph;
    }

    /**
     * Calcula la distancia euclidiana entre dos puntos.
     *
     * @param Point $pointA Punto A.
     * @param Point $pointB Punto B.
     * @return float Distancia redondeada a 2 decimales.
     */
    private function calculateDistance(Point $pointA, Point $pointB): float
    {
        $dx = $pointA->getX() - $pointB->getX();
        $dy = $pointA->getY() - $pointB->getY();

        return round(sqrt($dx ** 2 + $dy ** 2), 2);
    }
}
