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

    /**
     * Get user records
     *
     * @param int $userId
     *
     * @return Record[]
     */
    public function getUserRecords(int $userId): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r', 'fromWallet', 'toWallet')
            ->leftJoin('r.fromWallet', 'fromWallet')
            ->leftJoin('r.toWallet', 'toWallet')
            ->where('toWallet.userId = :userId')
            ->setParameter('userId', $userId)
            ->addOrderBy('r.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

}