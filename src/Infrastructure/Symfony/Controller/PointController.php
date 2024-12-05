<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Entity\Point;
use App\Domain\Repository\PointRepositoryInterface;
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

    /**
     * @Route("/points", methods={"POST"})
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

    /**
     * @Route("/points", methods={"GET"})
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

    /**
     * @Route("/points/{id}", methods={"DELETE"})
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
