<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Service\PathFindingService;
use OpenApi\Attributes as OA;
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

    #[Route('/api/path', methods: ['POST'])]
    #[OA\Post(
        path: '/api/path',
        summary: 'Calcular el camino más corto',
        description: 'Calcula el camino más corto entre dos puntos dados',
        tags: ['Pathfinding'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'start', type: 'string', description: 'ID del punto de inicio'),
                    new OA\Property(property: 'end', type: 'string', description: 'ID del punto de destino'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Camino más corto encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'path', type: 'array', items: new OA\Items(type: 'string')),
                        new OA\Property(property: 'totalDistance', type: 'number', format: 'float'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Error en los parámetros o el cálculo',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string')]
                )
            ),
        ]
    )]
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
                'path' => $pathResult->path,
                'totalDistance' => $pathResult->totalDistance,
            ]);
        } catch (\Exception $e) {
            // Manejar excepciones (por ejemplo, puntos no encontrados)
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
