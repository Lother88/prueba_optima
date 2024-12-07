<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Entity\PointUnion;
use App\Domain\Repository\PointRepositoryInterface;
use App\Domain\Repository\PointUnionRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/points/unions')]
class PointUnionController
{
    public function __construct(
        private PointUnionRepositoryInterface $unionRepository,
        private PointRepositoryInterface $pointRepository
    ) {}

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create or update a union between two points',
        requestBody: new OA\RequestBody(
            description: 'Details of the union',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'point1', type: 'string', description: 'ID of the first point'),
                    new OA\Property(property: 'point2', type: 'string', description: 'ID of the second point'),
                    new OA\Property(property: 'distance', type: 'number', format: 'float', description: 'Distance between the two points')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Union created or updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'point1', type: 'string'),
                        new OA\Property(property: 'point2', type: 'string'),
                        new OA\Property(property: 'distance', type: 'number', format: 'float')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'One or both points not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function createOrUpdateUnion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $point1 = $this->pointRepository->findById($data['point1']);
        $point2 = $this->pointRepository->findById($data['point2']);

        if (!$point1 || !$point2) {
            return new JsonResponse(['error' => 'One or both points not found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $union = $this->unionRepository->findUnion($point1, $point2);

        if (!$union) {
            $union = new PointUnion(
                id: '',
                point1: $point1,
                point2: $point2,
                distance: $data['distance']
            );
        } else {
            $union->distance = $data['distance'];
        }

        $this->unionRepository->save($union);

        return new JsonResponse([
            'id' => $union->getId(),
            'point1' => $point1->getId(),
            'point2' => $point2->getId(),
            'distance' => $union->getDistance()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'Retrieve all unions',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of all unions',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string'),
                            new OA\Property(property: 'point1', type: 'string'),
                            new OA\Property(property: 'point2', type: 'string'),
                            new OA\Property(property: 'distance', type: 'number', format: 'float')
                        ]
                    )
                )
            )
        ]
    )]
    public function getAllUnions(): JsonResponse
    {
        $unions = $this->unionRepository->findAll();

        $response = array_map(fn(PointUnion $union) => [
            'id' => $union->getId(),
            'point1' => $union->getPoint1()->getId(),
            'point2' => $union->getPoint2()->getId(),
            'distance' => $union->getDistance()
        ], $unions);

        return new JsonResponse($response);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Retrieve a specific union by ID',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Union details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'point1', type: 'string'),
                        new OA\Property(property: 'point2', type: 'string'),
                        new OA\Property(property: 'distance', type: 'number', format: 'float')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Union not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function getUnionById(string $id): JsonResponse
    {
        $union = $this->unionRepository->findById($id);

        if (!$union) {
            return new JsonResponse(['error' => 'Union not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $union->getId(),
            'point1' => $union->getPoint1()->getId(),
            'point2' => $union->getPoint2()->getId(),
            'distance' => $union->getDistance()
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a specific union by ID',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Union deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Union not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function deleteUnion(string $id): JsonResponse
    {
        $union = $this->unionRepository->findById($id);

        if (!$union) {
            return new JsonResponse(['error' => 'Union not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->unionRepository->delete($union);

        return new JsonResponse(['message' => 'Union deleted successfully']);
    }
}
