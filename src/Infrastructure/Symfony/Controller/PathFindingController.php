<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Service\PathFindingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PathFindingController
{
    private PathFindingService $pathFindingService;

    public function __construct(PathFindingService $pathFindingService)
    {
        $this->pathFindingService = $pathFindingService;
    }

    /**
     * @Route("/path", methods={"POST"})
     */
    public function calculateShortestPath(Request $request): JsonResponse
    {
        // Decodificar el cuerpo de la solicitud
        $data = json_decode($request->getContent(), true);

        // Validar parámetros de entrada
        $startId = $data['start'] ?? null;
        $endId = $data['end'] ?? null;

        if (!$startId || !$endId) {
            return new JsonResponse(['error' => 'Faltan parámetros start y end.'], 400);
        }

        try {
            // Llamar al servicio para calcular el camino más corto
            $pathResult = $this->pathFindingService->calculateShortestPath($startId, $endId);

            // Crear y devolver respuesta JSON
            return new JsonResponse([
                'path' => $pathResult->getPath(),
                'totalDistance' => $pathResult->getTotalDistance()
            ]);
        } catch (\Exception $e) {
            // Manejar excepciones (por ejemplo, puntos no encontrados)
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
