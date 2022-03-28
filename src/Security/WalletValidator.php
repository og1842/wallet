<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\Wallet;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WalletValidator
{
    /**
     * Validate request params for creation
     *
     * @param string $walletName
     * @param string $walletType
     *
     * @return void
     */
    public function validateRequestParamsForCreation(string $walletName, string $walletType): void
    {
        if (!$walletName) {
            throw new RuntimeException('Wallet name must be set.');
        }

        if (!$walletType) {
            throw new RuntimeException('Invalid wallet type.');
        }

        if (!isset(Wallet::WALLET_TYPES[$walletType])) {
            throw new RuntimeException('Invalid wallet type.');
        }
    }

    /**
     * Validate request params for fill balance
     *
     * @param string $id
     * @param string $name
     * @param string $amountStr
     *
     * @return void
     */
    public function validateRequestParamsForFillBalance(string $id, string $name, string $amountStr): void
    {
        if (!Uuid::isValid($id)) {
            throw new NotFoundHttpException();
        }

        if (!$name) {
            throw new RuntimeException('Record name must be set.');
        }

        if (!$amountStr) {
            throw new RuntimeException('Amount name must be set.');
        }
    }

    /**
     * Validate request params for transfer between wallets
     *
     * @param string $fromWalletId
     * @param string $toWalletId
     * @param string $name
     * @param string $amountStr
     *
     * @return void
     */
    public function validateRequestParamsForTransfer(string $fromWalletId, string $toWalletId, string $name, string $amountStr): void
    {
        if (!$name) {
            throw new RuntimeException('Record name must be set.');
        }

        if (!Uuid::isValid($fromWalletId)) {
            throw new NotFoundHttpException();
        }

        if (!Uuid::isValid($toWalletId)) {
            throw new NotFoundHttpException();
        }

        if ($fromWalletId === $toWalletId) {
            throw new RuntimeException('Transfer between same wallets not allowed.');
        }

        if (!$amountStr) {
            throw new RuntimeException('Amount name must be set.');
        }
    }

}