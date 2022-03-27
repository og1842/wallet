<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * Get wallet records
     *
     * @param string $walletId
     *
     * @return Record[]
     */
    public function getWalletRecords(string $walletId): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.fromWalletId = :walletId')
            ->orWhere('r.toWalletId = :walletId')
            ->setParameter('walletId', $walletId)
            ->addOrderBy('r.createdAt', 'DESC');

        return $qb->getQuery()->getResult();

    }

}