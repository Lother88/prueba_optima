<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Point;
use App\Domain\Repository\PointRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class DoctrinePointRepository implements PointRepositoryInterface
{
    private ObjectRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Point::class);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function save(Point $point): void
    {
        $this->entityManager->persist($point);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Point
    {
        return $this->repository->find($id);
    }
}
