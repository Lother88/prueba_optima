<?php

namespace App\Tests\Domain\Service;

use App\Domain\Service\DijkstraAlgorithm;
use PHPUnit\Framework\TestCase;

class DijkstraAlgorithmTest extends TestCase
{
    public function testFindPath(): void
    {
        $graph = [
            'A' => ['B' => 5, 'C' => 10],
            'B' => ['A' => 5, 'C' => 3, 'D' => 20],
            'C' => ['A' => 10, 'B' => 3, 'D' => 2],
            'D' => ['B' => 20, 'C' => 2],
        ];

        $algorithm = new DijkstraAlgorithm();
        $result = $algorithm->findPath($graph, 'A', 'D');

        $this->assertEquals(['A', 'C', 'D'], $result->getPath());
        $this->assertEquals(12, $result->getTotalDistance());
    }
}
