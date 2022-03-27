<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\Wallet;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class WalletValidator
{
    /**
     * Validate request params for creation
     *
     * @param Request $request
     * @return void
     *
     * @throws RuntimeException
     */
    public function validateRequestParamsForCreation(Request $request): void
    {
        $walletName = (string)$request->request->get('wallet_name');

        if (!$walletName) {
            throw new RuntimeException('Wallet name must be set.');
        }

        $walletType = (string)$request->request->get('wallet_type');

        if (!$walletType) {
            throw new RuntimeException('Invalid wallet type.');
        }

        if (!isset(Wallet::WALLET_TYPES[$walletType])) {
            throw new RuntimeException('Invalid wallet type.');
        }
    }

}