<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Interface\PathFindingAlgorithm;
use App\Domain\ValueObject\PathResult;

final class DijkstraAlgorithm implements PathFindingAlgorithm
{
    public function findPath(array $graph, string $start, string $end): PathResult
    {
        // 1. Inicializar estructuras
        [$distances, $previousNodes] = $this->initializeGraph($graph, $start);
        $priorityQueue = $this->initializePriorityQueue($start);

        $visited = [];

        // 2. Procesar la cola de prioridad
        while (!$priorityQueue->isEmpty()) {
            $currentNode = $priorityQueue->extract();
            if (isset($visited[$currentNode])) {
                continue;
            }
            $visited[$currentNode] = true;

            if ($currentNode === $end) {
                break;
            }

            $this->processNeighbors(
                $graph[$currentNode],
                $currentNode,
                $distances,
                $previousNodes,
                $priorityQueue
            );
        }

        // 3. Verificar si el nodo de destino es accesible
        $totalDistance = $distances[$end];
        
        if ($totalDistance === PHP_INT_MAX) {
            throw new \Exception("No hay camino válido entre $start y $end.");
        }

        // Redondear la distancia total
        $totalDistance = round($totalDistance, 2);

        // 4. Reconstruir el camino más corto
        $path = $this->reconstructPath($previousNodes, $start, $end);

        return new PathResult($path, $totalDistance);
    }

    private function initializeGraph(array $graph, string $start): array
    {
        $distances = [];
        $previousNodes = [];

        foreach ($graph as $node => $neighbors) {
            $distances[$node] = PHP_INT_MAX;
            $previousNodes[$node] = null;
        }
        $distances[$start] = 0;

        return [$distances, $previousNodes];
    }

    private function initializePriorityQueue(string $start): \SplPriorityQueue
    {
        $priorityQueue = new \SplPriorityQueue();
        $priorityQueue->insert($start, 0);

        return $priorityQueue;
    }

    private function processNeighbors(
      array $neighbors,
      string $currentNode,
      array &$distances,
      array &$previousNodes,
      \SplPriorityQueue $priorityQueue
    ): void {
      foreach ($neighbors as $neighbor => $distance) {
          $newDistance = $distances[$currentNode] + $distance;

          if ($newDistance < $distances[$neighbor]) {
              $distances[$neighbor] = $newDistance;
              $previousNodes[$neighbor] = $currentNode;
              $priorityQueue->insert($neighbor, -$newDistance);
          }
      }
    }

    private function reconstructPath(array $previousNodes, string $start, string $end): array
    {
      $path = [];
      $currentNode = $end;

      while ($currentNode !== null) {
          array_unshift($path, $currentNode);
          $currentNode = $previousNodes[$currentNode];
      }

      if ($path[0] !== $start) {
          throw new \Exception("No hay camino válido entre $start y $end.");
      }

      return $path;
    }

}
