<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\RecordService;
use App\Service\WalletService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecordController extends AbstractController
{
    private RecordService $recordService;
    private WalletService $walletService;

    public function __construct(RecordService $recordService, WalletService $walletService)
    {
        $this->recordService = $recordService;
        $this->walletService = $walletService;
    }

    #[Route('/wallet/records/{walletId}', name: 'app_wallet_records', methods: ['GET'])]
    public function walletRecords(string $walletId): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $walletRecords = $this->recordService->getWalletRecords($walletId);
        $wallet = $this->walletService->getUserWalletById($walletId, $user->getId());
        $userBalance = $this->walletService->getUserBalance($user->getId());

        return $this->render('wallet/wallet-records.html.twig', [
                'walletId' => $walletId, 'walletRecords' => $walletRecords, 'wallet' => $wallet, 'userBalance' => $userBalance]
        );
    }

    #[Route('/records', name: 'app_records', methods: ['GET'])]
    public function records(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $records = $this->recordService->getUserRecords($user->getId());
        $userBalance = $this->walletService->getUserBalance($user->getId());

        return $this->render('record/index.html.twig', ['records' => $records, 'userBalance' => $userBalance]);
    }

}