<?php

namespace App\Repository;

use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishlist>
 *
 * @method Wishlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wishlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wishlist[]    findAll()
 * @method Wishlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlist::class);
    }

    public function save(Wishlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Wishlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Wishlist[] Returns an array of Wishlist objects for a specific user
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('w.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Wishlist|null Returns a Wishlist object if the book is in the user's wishlist
     */
    public function findByUserAndBook($userId, $bookId): ?Wishlist
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :userId')
            ->andWhere('w.book = :bookId')
            ->setParameter('userId', $userId)
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 