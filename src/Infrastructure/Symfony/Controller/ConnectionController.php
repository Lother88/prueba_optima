<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Service\PathFindingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ConnectionController
{
    private PathFindingService $pathFindingService;

    public function __construct(PathFindingService $pathFindingService)
    {
        $this->pathFindingService = $pathFindingService;
    }

    #[Route('/api/connections', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/connections",
     *     summary="Crear o actualizar una conexión",
     *     description="Crea o actualiza una conexión entre dos puntos con una distancia especificada",
     *     tags={"Connections"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="start", type="string", description="ID del punto de inicio"),
     *             @OA\Property(property="end", type="string", description="ID del punto de destino"),
     *             @OA\Property(property="distance", type="number", description="Distancia entre los puntos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Conexión creada o actualizada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Conexión creada o actualizada correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetros faltantes o inválidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Faltan parámetros start, end o distance.")
     *         )
     *     )
     * )
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

    #[Route('/api/connections', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/connections",
     *     summary="Eliminar una conexión",
     *     description="Elimina una conexión entre dos puntos especificados",
     *     tags={"Connections"},
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
     *         description="Conexión eliminada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Conexión eliminada correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetros faltantes o inválidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Faltan parámetros start y end.")
     *         )
     *     )
     * )
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
