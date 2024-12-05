<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Entity\Point;
use App\Domain\Repository\PointRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class PointController
{
    private PointRepositoryInterface $pointRepository;

    public function __construct(PointRepositoryInterface $pointRepository)
    {
        $this->pointRepository = $pointRepository;
    }

    #[Route('/api/points', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/points",
     *     summary="Crear un punto",
     *     description="Crea un nuevo punto con coordenadas x e y",
     *     tags={"Points"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", description="ID único del punto"),
     *             @OA\Property(property="x", type="number", description="Coordenada X"),
     *             @OA\Property(property="y", type="number", description="Coordenada Y")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Punto creado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Punto creado correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetros faltantes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Faltan parámetros id, x o y.")
     *         )
     *     )
     * )
     */
    public function createPoint(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $id = $data['id'] ?? null;
        $x = $data['x'] ?? null;
        $y = $data['y'] ?? null;

        if (!$id || $x === null || $y === null) {
            return new JsonResponse(['error' => 'Faltan parámetros id, x o y.'], 400);
        }

        $point = new Point($id, $x, $y);
        $this->pointRepository->save($point);

        return new JsonResponse(['message' => 'Punto creado correctamente.'], 201);
    }

    #[Route('/api/points', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/points",
     *     summary="Listar puntos",
     *     description="Obtiene una lista de todos los puntos creados",
     *     tags={"Points"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de puntos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="string", description="ID del punto"),
     *                 @OA\Property(property="x", type="number", description="Coordenada X"),
     *                 @OA\Property(property="y", type="number", description="Coordenada Y")
     *             )
     *         )
     *     )
     * )
     */
    public function listPoints(): JsonResponse
    {
        $points = $this->pointRepository->findAll();

        return new JsonResponse(array_map(function (Point $point) {
            return [
                'id' => $point->getId(),
                'x' => $point->getX(),
                'y' => $point->getY(),
            ];
        }, $points));
    }

    #[Route('/api/points/{id}', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/points/{id}",
     *     summary="Eliminar un punto",
     *     description="Elimina un punto por su ID",
     *     tags={"Points"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del punto a eliminar",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Punto eliminado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Punto eliminado correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Punto no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Punto no encontrado.")
     *         )
     *     )
     * )
     */
    public function deletePoint(string $id): JsonResponse
    {
        $point = $this->pointRepository->findById($id);

        if (!$point) {
            return new JsonResponse(['error' => 'Punto no encontrado.'], 404);
        }

        $this->pointRepository->delete($id);

        return new JsonResponse(['message' => 'Punto eliminado correctamente.']);
    }
}
