<?php

namespace App\Tests\Domain\Service;

use App\Domain\Service\DijkstraAlgorithm;
use App\Domain\ValueObject\PathResult;
use PHPUnit\Framework\TestCase;

class DijkstraAlgorithmTest extends TestCase
{
    private DijkstraAlgorithm $algorithm;

    protected function setUp(): void
    {
        $this->algorithm = new DijkstraAlgorithm();
    }

    public function testFindPathSuccess(): void
    {
        $graph = [
            'A' => ['B' => 5, 'C' => 10],
            'B' => ['A' => 5, 'C' => 3, 'D' => 20],
            'C' => ['A' => 10, 'B' => 3, 'D' => 2],
            'D' => ['B' => 20, 'C' => 2],
        ];
    
        $result = $this->algorithm->findPath($graph, 'A', 'D');
    
        $this->assertInstanceOf(PathResult::class, $result);
        $this->assertEquals(['A', 'B', 'C', 'D'], $result->getPath());
        $this->assertEquals(10, $result->getTotalDistance());
    }
    

    public function testFindPathNoPath(): void
    {
        $graph = [
            'A' => ['B' => 5],
            'B' => ['A' => 5],
            'C' => ['D' => 3],
            'D' => ['C' => 3],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No hay camino válido entre A y D.");

        $this->algorithm->findPath($graph, 'A', 'D');
    }

    public function testFindPathSingleNode(): void
    {
        $graph = [
            'A' => [],
        ];

        $result = $this->algorithm->findPath($graph, 'A', 'A');

        $this->assertInstanceOf(PathResult::class, $result);
        $this->assertEquals(['A'], $result->getPath());
        $this->assertEquals(0, $result->getTotalDistance());
    }

    public function testFindPathLargeGraph(): void
    {
        $graph = [
            'A' => ['B' => 1, 'C' => 4],
            'B' => ['A' => 1, 'C' => 2, 'D' => 7],
            'C' => ['A' => 4, 'B' => 2, 'D' => 1],
            'D' => ['B' => 7, 'C' => 1, 'E' => 3],
            'E' => ['D' => 3],
        ];

        $result = $this->algorithm->findPath($graph, 'A', 'E');

        $this->assertInstanceOf(PathResult::class, $result);
        $this->assertEquals(['A', 'B', 'C', 'D', 'E'], $result->getPath());
        $this->assertEquals(7, $result->getTotalDistance());
    }

    public function testFindPathUnreachableNode(): void
    {
        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1, 'C' => 2],
            'C' => ['B' => 2],
            'D' => [],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No hay camino válido entre A y D.");

        $this->algorithm->findPath($graph, 'A', 'D');
    }
}
