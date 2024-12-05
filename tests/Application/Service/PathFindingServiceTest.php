<?php

namespace App\Tests\Application\Service;

use App\Application\Service\PathFindingService;
use App\Domain\Entity\Point;
use App\Infrastructure\Persistence\InMemory\Repository\InMemoryPointRepository;
use App\Domain\Service\DijkstraAlgorithm;
use PHPUnit\Framework\TestCase;

class PathFindingServiceTest extends TestCase
{

    public function testCalculateShortestPathNoConnection(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 10, 10)); // Sin conexión explícita
    
        $connections = []; // No hay conexiones definidas entre A y B
    
        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
    
        $service->setConnections($connections); // Establecer las conexiones manuales
    
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No hay camino válido entre A y B");
    
        $service->calculateShortestPath('A', 'B');
    }
    
    public function testCalculateShortestPath(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));
        $repository->save(new Point('C', 2, 2));

        $connections = [
            ['A', 'B', 1.41],
            ['B', 'C', 1.41],
        ]; // Define conexiones manuales
    
        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $result = $service->calculateShortestPath('A', 'C');
    
        $this->assertEquals(['A', 'B', 'C'], $result->getPath());
        $this->assertEquals(2.82, $result->getTotalDistance());
    }

    public function testCalculateShortestPathDirectConnection(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));

        $connections = [
            ['A', 'B', 1.41],
        ]; // Define conexión directa

        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $result = $service->calculateShortestPath('A', 'B');

        $this->assertEquals(['A', 'B'], $result->getPath());
        $this->assertEquals(1.41, $result->getTotalDistance());
    }

    public function testCalculateShortestPathInvalidPoints(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));

        $connections = []; // No conexiones necesarias

        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Puntos de inicio o destino no encontrados.");

        $service->calculateShortestPath('A', 'C'); // 'C' no existe
    }

    public function testCalculateShortestPathWithCycle(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));
        $repository->save(new Point('C', 2, 2));
        $repository->save(new Point('D', 3, 3));
    
        $connections = [
            ['A', 'B', 1.41],
            ['B', 'C', 1.41],
            ['C', 'D', 1.41],
            ['D', 'B', 10.0], // Aumentar la distancia para evitar el camino directo B -> D
        ];
    
        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);
    
        $result = $service->calculateShortestPath('A', 'D');
    
        $this->assertEquals(['A', 'B', 'C', 'D'], $result->getPath());
        $this->assertEquals(4.23, $result->getTotalDistance());
    }
    

    public function testCalculateShortestPathLargeGraph(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));
        $repository->save(new Point('C', 2, 2));
        $repository->save(new Point('D', 3, 3));
        $repository->save(new Point('E', 4, 4));

        $connections = [
            ['A', 'B', 1.41],
            ['B', 'C', 1.41],
            ['C', 'D', 1.41],
            ['D', 'E', 1.41],
        ];

        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $result = $service->calculateShortestPath('A', 'E');

        $this->assertEquals(['A', 'B', 'C', 'D', 'E'], $result->getPath());
        $this->assertEquals(5.64, $result->getTotalDistance());
    }

    public function testCalculateShortestPathMultipleEqualDistances(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));
        $repository->save(new Point('C', 2, 2));
        $repository->save(new Point('D', 3, 3));

        $connections = [
            ['A', 'B', 2.0],
            ['A', 'C', 2.0],
            ['B', 'D', 2.0],
            ['C', 'D', 2.0],
        ];

        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $result = $service->calculateShortestPath('A', 'D');

        // Ambos caminos son válidos: A -> B -> D o A -> C -> D
        $this->assertTrue(in_array($result->getPath(), [['A', 'B', 'D'], ['A', 'C', 'D']]));
        $this->assertEquals(4.0, $result->getTotalDistance());
    }

    public function testCalculateShortestPathDisconnectedGraph(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));
        $repository->save(new Point('C', 10, 10));
        $repository->save(new Point('D', 11, 11));

        $connections = [
            ['A', 'B', 1.41],
            ['C', 'D', 1.41],
        ]; // Grafo tiene dos componentes desconectadas

        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No hay camino válido entre A y D");

        $service->calculateShortestPath('A', 'D');
    }

    public function testCalculateShortestPathDifferentWeights(): void
    {
        $repository = new InMemoryPointRepository();
        $repository->save(new Point('A', 0, 0));
        $repository->save(new Point('B', 1, 1));
        $repository->save(new Point('C', 2, 2));
        $repository->save(new Point('D', 3, 3));

        $connections = [
            ['A', 'B', 1.0],
            ['B', 'C', 5.0],
            ['A', 'C', 2.0],
            ['C', 'D', 1.0],
        ];

        $algorithm = new DijkstraAlgorithm();
        $service = new PathFindingService($repository, $algorithm);
        $service->setConnections($connections);

        $result = $service->calculateShortestPath('A', 'D');

        $this->assertEquals(['A', 'C', 'D'], $result->getPath());
        $this->assertEquals(3.0, $result->getTotalDistance());
    }

}
