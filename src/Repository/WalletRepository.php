<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

/**
 * @method Wallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wallet[]    findAll()
 * @method Wallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    /**
     * Get user wallets by user id
     *
     * @param int $userId
     * @param int $offset
     * @param int $limit
     *
     * @return Wallet[]
     */
    public function getUserWallets(int $userId, int $offset, int $limit): array
    {
        $qb = $this->createQueryBuilder('w')
            ->where('w.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('w.createdAt', 'DESC')
            ->setFirstResult($offset);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws Throwable
     */
    public function save(Wallet $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * @throws Throwable
     */
    public function remove(Wallet $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * Get user wallet by id
     *
     * @param string $id
     * @param int $userId
     *
     * @return Wallet|null
     */
    public function getUserWalletById(string $id, int $userId): ?Wallet
    {
        $qb = $this->createQueryBuilder('w')
            ->where('w.id = :id')
            ->andWhere('w.userId = :userId')
            ->setParameter('id', $id)
            ->setParameter('userId', $userId);

        $res = $qb->getQuery()->getResult();

        if (!$res) {
            return null;
        }

        return $res[0];
    }

    /**
     * Delete user wallet by id
     *
     * @param string $id
     * @param int $userId
     *
     * @return void
     */
    public function deleteUserWalletById(string $id, int $userId): void
    {
        $qb = $this->createQueryBuilder('w')
            ->delete()
            ->where('w.id = :id')
            ->andWhere('w.userId = :userId')
            ->setParameter('id', $id)
            ->setParameter('userId', $userId);

        $qb->getQuery()->execute();
    }
}
