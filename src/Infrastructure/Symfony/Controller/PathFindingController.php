<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Service\PathFindingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class PathFindingController
{
    private PathFindingService $pathFindingService;

    public function __construct(PathFindingService $pathFindingService)
    {
        $this->pathFindingService = $pathFindingService;
    }

    #[Route('/api/path', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/path",
     *     summary="Calcular el camino más corto",
     *     description="Calcula el camino más corto entre dos puntos dados",
     *     tags={"Pathfinding"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="start", type="string", description="ID del punto de inicio"),
     *             @OA\Property(property="end", type="string", description="ID del punto de destino")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camino más corto encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="path", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="totalDistance", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en los parámetros o el cálculo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
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
