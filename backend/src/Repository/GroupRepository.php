<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function save(Group $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Group $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Group[] Returns an array of Group objects for a specific user
     */
    public function findByMember($userId): array
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.members', 'm')
            ->andWhere('m.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Group[] Returns an array of Group objects created by a specific user
     */
    public function findByCreator($userId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.createdBy = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 