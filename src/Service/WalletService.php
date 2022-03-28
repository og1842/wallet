<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Psr\Log\LoggerInterface;
use Throwable;

class WalletService
{
    private WalletRepository $repository;
    private LoggerInterface $logger;

    public function __construct(WalletRepository $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
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
    public function getUserWallets(int $userId, int $offset = 0, int $limit = 0): array
    {
        return $this->repository->getUserWallets($userId, $offset, $limit);
    }

    /**
     * Crate wallet by name and type
     *
     * @param string $name
     * @param string $type
     * @param User $user
     *
     * @return bool
     */
    public function createByNameAndType(string $name, string $type, User $user): bool
    {
        $entity = new Wallet();

        $entity->setName($name);
        $entity->setWalletType($type);
        $entity->setUser($user);

        try {
            $this->repository->save($entity);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to create wallet', ['message' => $ex->getMessage()]);

            return false;
        }

        return true;
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
        return $this->repository->getUserWalletById($id, $userId);
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
        $this->repository->deleteUserWalletById($id, $userId);
    }

    /**
     * Fill user balance by increasing wallet balance and adding record with transaction
     *
     * @param int $userId
     * @param string $id
     * @param int $amount
     * @param string $name
     *
     * @return bool
     */
    public function fillUserBalance(int $userId, string $id, int $amount, string $name): bool
    {
        $wallet = $this->getUserWalletById($id, $userId);

        if (!$wallet) {
            return false;
        }

        try {
            $success = $this->repository->fillBalance($wallet, $amount, $name);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to fill balance', ['id' => $id, 'amount' => $amount, 'message' => $ex->getMessage()]);

            return false;
        }

        return $success;
    }

    /**
     * Transfer between user wallets and add record with transaction
     *
     * @param int $userId
     * @param string $fromWalletId
     * @param string $toWalletId
     * @param int $amount
     * @param string $name
     *
     * @return bool
     */
    public function transfer(int $userId, string $fromWalletId, string $toWalletId, int $amount, string $name): bool
    {
        if ($fromWalletId === $toWalletId) {
            return false;
        }

        $fromWallet = $this->getUserWalletById($fromWalletId, $userId);

        if (!$fromWallet) {
            return false;
        }

        $toWallet = $this->getUserWalletById($toWalletId, $userId);

        if (!$toWallet) {
            return false;
        }

        $isValid = $this->validateWalletBalanceForTransfer($fromWallet, $amount);

        if (!$isValid) {
            return false;
        }

        try {
            $this->repository->transfer($fromWallet, $toWallet, $amount, $name);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to fill balance', [
                    'fromWalletId' => $fromWalletId, 'toWalletId' => $toWalletId, 'amount' => $amount, 'message' => $ex->getMessage()
                ]
            );

            return false;
        }

        return true;
    }

    /**
     * Validate wallet balance for transfer
     *
     * @param Wallet $fromWallet
     * @param int $amount
     *
     * @return bool
     */
    private function validateWalletBalanceForTransfer(Wallet $fromWallet, int $amount): bool
    {
        return $fromWallet->getBalance() >= $amount;
    }

}