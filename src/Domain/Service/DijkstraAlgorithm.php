<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Interface\PathFindingAlgorithm;
use App\Domain\ValueObject\PathResult;

final class DijkstraAlgorithm implements PathFindingAlgorithm
{
    public function findPath(array $graph, string $start, string $end): PathResult
    {
        [$distances, $previousNodes] = $this->initializeGraph($graph, $start);
        $priorityQueue = $this->initializePriorityQueue($start);
        $visited = [];
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

        $totalDistance = $distances[$end];
        if (PHP_INT_MAX === $totalDistance) {
            throw new \Exception("No hay camino válido entre $start y $end.");
        }
        $totalDistance = round($totalDistance, 2);

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
        \SplPriorityQueue $priorityQueue,
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

        while (null !== $currentNode) {
            array_unshift($path, $currentNode);
            $currentNode = $previousNodes[$currentNode];
        }

        if ($path[0] !== $start) {
            throw new \Exception("No hay camino válido entre $start y $end.");
        }

        return $path;
    }
}
