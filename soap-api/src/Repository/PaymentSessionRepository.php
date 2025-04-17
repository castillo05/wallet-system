<?php

namespace App\Repository;

use App\Entity\PaymentSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentSession>
 *
 * @method PaymentSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentSession[]    findAll()
 * @method PaymentSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentSession::class);
    }

    public function save(PaymentSession $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaymentSession $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 