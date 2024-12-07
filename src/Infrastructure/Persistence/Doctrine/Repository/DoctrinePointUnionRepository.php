<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Point;
use App\Domain\Entity\PointUnion;
use App\Domain\Repository\PointUnionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePointUnionRepository implements PointUnionRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(PointUnion::class)
            ->findAll();
    }

    public function findById(string $id): ?PointUnion
    {
        return $this->entityManager
            ->find(PointUnion::class, $id);
    }

    public function findUnion(Point $point1, Point $point2): ?PointUnion
    {
        $query = $this->entityManager
            ->createQuery(
                'SELECT u FROM App\Domain\Entity\PointUnion u 
                 WHERE (u.point1 = :point1 AND u.point2 = :point2)
                    OR (u.point1 = :point2 AND u.point2 = :point1)'
            )
            ->setParameter('point1', $point1)
            ->setParameter('point2', $point2);

        return $query->getOneOrNullResult();
    }

    public function save(PointUnion $union): void
    {
        $this->entityManager->persist($union);
        $this->entityManager->flush();
    }

    public function delete(PointUnion $union): void
    {
        $this->entityManager->remove($union);
        $this->entityManager->flush();
    }
}
