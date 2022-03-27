<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\RecordService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecordController extends AbstractController
{
    private RecordService $recordService;

    public function __construct(RecordService $recordService)
    {
        $this->recordService = $recordService;
    }

    #[Route('/wallet/records/{walletId}', name: 'app_wallet_records', methods: ['GET'])]
    public function walletRecords(string $walletId): Response
    {
        $walletRecords = $this->recordService->getWalletRecords($walletId);

        return $this->render('wallet/wallet-records.html.twig', ['walletId' => $walletId, 'walletRecords' => $walletRecords]);
    }

    #[Route('/records', name: 'app_records', methods: ['GET'])]
    public function records(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $records = $this->recordService->getUserRecords($user->getId());

        return $this->render('record/index.html.twig', ['records' => $records]);
    }

}