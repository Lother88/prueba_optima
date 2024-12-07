<?php

namespace App\Tests\Application\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Service\PathFindingService;
use App\Domain\Repository\PointRepositoryInterface;
use App\Domain\Repository\PointUnionRepositoryInterface;
use App\Domain\Service\DijkstraAlgorithm;
use App\Domain\Entity\Point;
use App\Domain\Entity\PointUnion;
use App\Domain\ValueObject\PathResult;

class PathFindingServiceTest extends TestCase
{
    private PointRepositoryInterface $pointRepository;
    private PointUnionRepositoryInterface $unionRepository;
    private PathFindingService $pathFindingService;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks para los repositorios
        $this->pointRepository = $this->createMock(PointRepositoryInterface::class);
        $this->unionRepository = $this->createMock(PointUnionRepositoryInterface::class);

        // Crear la instancia del servicio bajo prueba
        $this->pathFindingService = new PathFindingService(
            $this->pointRepository,
            $this->unionRepository,
            new DijkstraAlgorithm()
        );
    }

    public function testCalculateShortestPathSuccess(): void
    {
        // Crear puntos simulados
        $pointA = new Point('A', 0, 0);
        $pointB = new Point('B', 1, 1);
        $pointC = new Point('C', 2, 2);

        // Crear uniones simuladas usando el método estático `create`
        $unionAB = PointUnion::create($pointA, $pointB, 1.5);
        $unionBC = PointUnion::create($pointB, $pointC, 2.0);

        // Configurar el mock de PointRepository
        $this->pointRepository->method('findById')
            ->willReturnMap([
                ['A', $pointA],
                ['B', $pointB],
                ['C', $pointC],
            ]);

        $this->pointRepository->method('findAll')
            ->willReturn([$pointA, $pointB, $pointC]);

        // Configurar el mock de PointUnionRepository
        $this->unionRepository->method('findAll')
            ->willReturn([$unionAB, $unionBC]);

        // Ejecutar el servicio
        $result = $this->pathFindingService->calculateShortestPath('A', 'C');

        // Verificar resultados
        $this->assertInstanceOf(PathResult::class, $result);
        $this->assertSame(['A', 'B', 'C'], $result->path);
        $this->assertEquals(3.5, $result->totalDistance);
    }


    public function testCalculateShortestPathNoPath(): void
    {
        // Crear puntos simulados
        $pointA = new Point('A', 0, 0);
        $pointB = new Point('B', 1, 1);

        // Configurar el mock de PointRepository
        $this->pointRepository->method('findById')
            ->willReturnMap([
                ['A', $pointA],
                ['B', $pointB],
            ]);

        $this->pointRepository->method('findAll')
            ->willReturn([$pointA, $pointB]);

        // No configurar uniones, lo que simula que no hay conexión entre los puntos
        $this->unionRepository->method('findAll')
            ->willReturn([]);

        // Esperar una excepción
        $this->expectException(\Exception::class);

        // Ejecutar el servicio
        $this->pathFindingService->calculateShortestPath('A', 'B');
    }

    public function testCalculateShortestPathPointNotFound(): void
    {
        // Configurar el mock de PointRepository para que no encuentre un punto
        $this->pointRepository->method('findById')
            ->willReturn(null);

        // Esperar una excepción por punto no encontrado
        $this->expectException(\InvalidArgumentException::class);

        // Ejecutar el servicio
        $this->pathFindingService->calculateShortestPath('A', 'B');
    }
}
