<?php

namespace App\Repository;

use App\Entity\LibraryBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LibraryBook>
 *
 * @method LibraryBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method LibraryBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method LibraryBook[]    findAll()
 * @method LibraryBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibraryBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LibraryBook::class);
    }

    public function save(LibraryBook $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LibraryBook $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return LibraryBook[] Returns an array of LibraryBook objects
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('lb')
            ->andWhere('lb.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('lb.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return LibraryBook[] Returns an array of LibraryBook objects
     */
    public function findByCategory($categoryId): array
    {
        return $this->createQueryBuilder('lb')
            ->innerJoin('lb.categories', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('lb.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 