<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Entity\Point;
use App\Domain\Repository\PointRepositoryInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PointController
{
    private PointRepositoryInterface $pointRepository;

    public function __construct(PointRepositoryInterface $pointRepository)
    {
        $this->pointRepository = $pointRepository;
    }

    #[Route('/api/points', methods: ['GET'])]
    #[OA\Get(
        path: '/api/points',
        summary: 'Listar puntos',
        description: 'Obtiene una lista de todos los puntos creados',
        tags: ['Points'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de puntos',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', description: 'ID del punto'),
                            new OA\Property(property: 'x', type: 'number', description: 'Coordenada X'),
                            new OA\Property(property: 'y', type: 'number', description: 'Coordenada Y'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string', example: 'Error al recuperar los puntos.')]
                )
            ),
        ]
    )]
    public function listPoints(): JsonResponse
    {
        $points = $this->pointRepository->findAll();

        return new JsonResponse(array_map(function (Point $point) {
            return [
                'id' => $point->id,
                'x' => $point->x,
                'y' => $point->y,
            ];
        }, $points));
    }

    #[Route('/api/points', methods: ['POST'])]
    #[OA\Post(
        path: '/api/points',
        summary: 'Crear un nuevo punto',
        description: 'Crea un nuevo punto con coordenadas X e Y',
        tags: ['Points'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'string', description: 'ID único del punto'),
                    new OA\Property(property: 'x', type: 'number', description: 'Coordenada X'),
                    new OA\Property(property: 'y', type: 'number', description: 'Coordenada Y'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Punto creado correctamente',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'message', type: 'string', example: 'Punto creado correctamente.')]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Parámetros faltantes',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string', example: 'Faltan parámetros id, x o y.')]
                )
            ),
        ]
    )]
    public function createPoint(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $id = $data['id'] ?? null;
        $x = $data['x'] ?? null;
        $y = $data['y'] ?? null;

        if (!$id || null === $x || null === $y) {
            return new JsonResponse(['error' => 'Faltan parámetros id, x o y.'], 400);
        }

        $point = new Point($id, $x, $y);
        $this->pointRepository->save($point);

        return new JsonResponse(['message' => 'Punto creado correctamente.'], 201);
    }

    #[Route('/api/points/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/points/{id}',
        summary: 'Eliminar un punto',
        description: 'Elimina un punto por su ID',
        tags: ['Points'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID del punto a eliminar',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Punto eliminado correctamente',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'message', type: 'string', example: 'Punto eliminado correctamente.')]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Punto no encontrado',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string', example: 'Punto no encontrado.')]
                )
            ),
        ]
    )]
    public function deletePoint(string $id): JsonResponse
    {
        $point = $this->pointRepository->findById($id);

        if (!$point) {
            return new JsonResponse(['error' => 'Punto no encontrado.'], 404);
        }

        $this->pointRepository->delete($point);

        return new JsonResponse(['message' => 'Punto eliminado correctamente.']);
    }
}
