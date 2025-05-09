<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loan>
 *
 * @method Loan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loan[]    findAll()
 * @method Loan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function save(Loan $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Loan $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Loan[] Returns an array of active Loan objects
     */
    public function findActiveLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.returnDate IS NULL')
            ->orderBy('l.loanDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Loan[] Returns an array of Loan objects for a specific borrower
     */
    public function findByBorrower($borrowerId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.borrower = :borrowerId')
            ->setParameter('borrowerId', $borrowerId)
            ->orderBy('l.loanDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Loan[] Returns an array of Loan objects that need reminders
     */
    public function findLoansNeedingReminder(): array
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('l')
            ->andWhere('l.returnDate IS NULL')
            ->andWhere('l.reminderDate <= :now')
            ->setParameter('now', $now)
            ->orderBy('l.reminderDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 