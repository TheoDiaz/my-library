<?php

namespace App\Repository;

use App\Entity\ChallengeParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChallengeParticipant>
 *
 * @method ChallengeParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChallengeParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChallengeParticipant[]    findAll()
 * @method ChallengeParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChallengeParticipant::class);
    }

    public function save(ChallengeParticipant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChallengeParticipant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return ChallengeParticipant[] Returns an array of ChallengeParticipant objects for a specific challenge
     */
    public function findByChallenge($challengeId): array
    {
        return $this->createQueryBuilder('cp')
            ->andWhere('cp.challenge = :challengeId')
            ->setParameter('challengeId', $challengeId)
            ->orderBy('cp.progress', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ChallengeParticipant[] Returns an array of ChallengeParticipant objects for a specific user
     */
    public function findByUser($userId): array
    {
        return $this->createQueryBuilder('cp')
            ->andWhere('cp.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('cp.progress', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ChallengeParticipant|null Returns a ChallengeParticipant object if the user is participating in the challenge
     */
    public function findByUserAndChallenge($userId, $challengeId): ?ChallengeParticipant
    {
        return $this->createQueryBuilder('cp')
            ->andWhere('cp.user = :userId')
            ->andWhere('cp.challenge = :challengeId')
            ->setParameter('userId', $userId)
            ->setParameter('challengeId', $challengeId)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 