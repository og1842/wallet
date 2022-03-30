<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Security\Uuid;
use App\Security\WalletValidator;
use App\Service\AmountConverter;
use App\Service\WalletService;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WalletController extends AbstractController
{
    private WalletValidator $walletValidator;
    private WalletService $walletService;

    public function __construct(WalletValidator $walletValidator, WalletService $walletService)
    {
        $this->walletValidator = $walletValidator;
        $this->walletService = $walletService;
    }

    #[Route('/wallet', name: 'app_wallet_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $wallets = $this->walletService->getUserWallets($user->getId());

        $userBalance = $this->walletService->calculateUserBalanceByWallets($wallets);

        if (!$wallets) {
            return $this->redirectToRoute('app_wallet_create');
        }

        return $this->render('wallet/index.html.twig', ['wallets' => $wallets, 'userBalance' => $userBalance]);
    }

    #[Route('/wallet/create', name: 'app_wallet_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $userWalletsCount = $this->walletService->getUserWalletsCount($user->getId());
        $userBalance = $this->walletService->getUserBalance($user->getId());

        if ($request->isMethod('get')) {
            return $this->render('wallet/create.html.twig',
                ['wallet_types' => Wallet::WALLET_TYPES, 'userWalletsCount' => $userWalletsCount, 'userBalance' => $userBalance]
            );
        }

        $walletName = (string)$request->request->get('wallet_name');

        $walletType = (string)$request->request->get('wallet_type');

        try {
            $this->walletValidator->validateRequestParamsForCreation($walletName, $walletType);
        } catch (RuntimeException $ex) {
            $this->addFlash('error', $ex->getMessage());

            return $this->redirectToRoute('app_wallet_index');
        }

        $success = $this->walletService->createByNameAndType($walletName, $walletType, $user);

        if ($success) {
            $this->addFlash('success', 'Wallet successfully created.');
        } else {
            $this->addFlash('error', 'Unable to create wallet.');
        }

        return $this->redirectToRoute('app_wallet_index');
    }

    #[Route('/wallet/detail/{id}', name: 'app_wallet_detail', methods: ['GET'])]
    public function detail(string $id): Response
    {
        if (!Uuid::isValid($id)) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $wallet = $this->walletService->getUserWalletById($id, $user->getId());
        $userBalance = $this->walletService->getUserBalance($user->getId());

        return $this->render('wallet/detail.html.twig', ['wallet' => $wallet, 'userBalance' => $userBalance]);
    }

    #[Route('/wallet/delete/{id}', name: 'app_wallet_delete', methods: ['GEt', 'POST'])]
    public function delete(string $id): RedirectResponse
    {
        if (!Uuid::isValid($id)) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $this->walletService->deleteUserWalletById($id, $user->getId());

        $this->addFlash('success', 'Wallet successfully deleted.');

        return $this->redirectToRoute('app_wallet_index');
    }

    #[Route('/wallet/transfer-from-to', name: 'app_wallet_transfer_from_to', methods: ['POST'])]
    public function transferFromWalletToWallet(Request $request): RedirectResponse
    {
        $fromWalletId = (string)$request->request->get('fromWalletId');
        $toWalletId = (string)$request->request->get('toWalletId');
        $name = (string)$request->request->get('name');
        $amountStr = (string)$request->request->get('amount');

        try {
            $this->walletValidator->validateRequestParamsForTransfer($fromWalletId, $toWalletId, $name, $amountStr);
        } catch (RuntimeException $ex) {
            $this->addFlash('error', $ex->getMessage());

            return $this->redirectToRoute('app_wallet_index');
        }

        $amount = AmountConverter::convertToDbValue($amountStr);

        if ($amount === 0) {
            $this->addFlash('error', 'incorrect amount.');

            return $this->redirectToRoute('app_wallet_index');
        }

        /** @var User $user */
        $user = $this->getUser();
        $status = $this->walletService->transfer($user->getId(), $fromWalletId, $toWalletId, $amount, $name);

        if ($status) {
            $this->addFlash('success', 'Translation completed successfully.');
        } else {
            $this->addFlash('error', 'Translation completion error.');
        }

        return $this->redirectToRoute('app_wallet_index');
    }

    #[Route('/wallet/fill/{id}', name: 'app_wallet_fill', methods: ['POST'])]
    public function fill(string $id, Request $request): RedirectResponse
    {
        $name = (string)$request->request->get('name');
        $amountStr = (string)$request->request->get('amount');

        try {
            $this->walletValidator->validateRequestParamsForFillBalance($id, $name, $amountStr);
        } catch (RuntimeException $ex) {
            $this->addFlash('error', $ex->getMessage());

            return $this->redirectToRoute('app_wallet_detail', ['id' => $id]);
        }

        $amount = AmountConverter::convertToDbValue($amountStr);

        if ($amount === 0) {
            $this->addFlash('error', 'incorrect amount.');

            return $this->redirectToRoute('app_wallet_detail', ['id' => $id]);
        }

        /** @var User $user */
        $user = $this->getUser();

        $status = $this->walletService->fillUserBalance($user->getid(), $id, $amount, $name);

        if ($status) {
            $this->addFlash('success', 'Balance successfully filled.');
        } else {
            $this->addFlash('error', 'Balance filling error.');
        }

        return $this->redirectToRoute('app_wallet_detail', ['id' => $id]);
    }
}