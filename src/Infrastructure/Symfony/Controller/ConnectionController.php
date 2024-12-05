<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Service\PathFindingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConnectionController
{
    private PathFindingService $pathFindingService;

    public function __construct(PathFindingService $pathFindingService)
    {
        $this->pathFindingService = $pathFindingService;
    }

    /**
     * @Route("/connections", methods={"POST"})
     */
    public function createOrUpdateConnection(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;
        $distance = $data['distance'] ?? null;

        if (!$start || !$end || $distance === null) {
            return new JsonResponse(['error' => 'Faltan parámetros start, end o distance.'], 400);
        }

        $connections = $this->pathFindingService->getConnections();
        $connections[] = [$start, $end, $distance];

        $this->pathFindingService->setConnections($connections);

        return new JsonResponse(['message' => 'Conexión creada o actualizada correctamente.'], 201);
    }

    /**
     * @Route("/connections", methods={"DELETE"})
     */
    public function deleteConnection(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;

        if (!$start || !$end) {
            return new JsonResponse(['error' => 'Faltan parámetros start y end.'], 400);
        }

        $connections = $this->pathFindingService->getConnections();
        $connections = array_filter($connections, function ($connection) use ($start, $end) {
            return !($connection[0] === $start && $connection[1] === $end);
        });

        $this->pathFindingService->setConnections($connections);

        return new JsonResponse(['message' => 'Conexión eliminada correctamente.']);
    }
}
