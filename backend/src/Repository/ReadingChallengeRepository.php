<?php

namespace App\Repository;

use App\Entity\ReadingChallenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReadingChallenge>
 *
 * @method ReadingChallenge|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReadingChallenge|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReadingChallenge[]    findAll()
 * @method ReadingChallenge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReadingChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadingChallenge::class);
    }

    public function save(ReadingChallenge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReadingChallenge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return ReadingChallenge[] Returns an array of active ReadingChallenge objects
     */
    public function findActiveChallenges(): array
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('rc')
            ->andWhere('rc.startDate <= :now')
            ->andWhere('rc.endDate >= :now')
            ->setParameter('now', $now)
            ->orderBy('rc.endDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ReadingChallenge[] Returns an array of ReadingChallenge objects for a specific user
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('rc')
            ->andWhere('rc.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('rc.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ReadingChallenge[] Returns an array of upcoming ReadingChallenge objects
     */
    public function findUpcomingChallenges(): array
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('rc')
            ->andWhere('rc.startDate > :now')
            ->setParameter('now', $now)
            ->orderBy('rc.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 