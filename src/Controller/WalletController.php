<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Security\WalletValidator;
use App\Service\WalletService;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        if (!$wallets) {
            return $this->redirectToRoute('app_wallet_create');
        }

        return $this->render('wallet/index.html.twig', ['wallets' => $wallets]);
    }

    #[Route('/wallet/create', name: 'app_wallet_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if ($request->isMethod('get')) {
            return $this->render('wallet/create.html.twig', ['wallet_types' => Wallet::WALLET_TYPES]);
        }

        try {
            $this->walletValidator->validateRequestParamsForCreation($request);
        } catch (RuntimeException $ex) {
            $this->addFlash('error', $ex->getMessage());

            return $this->redirectToRoute('app_wallet_index');
        }

        $walletName = (string)$request->request->get('wallet_name');

        $walletType = (string)$request->request->get('wallet_type');

        /** @var User $user */
        $user = $this->getUser();

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
        /** @var User $user */
        $user = $this->getUser();

        $wallet = $this->walletService->getUserWalletById($id, $user->getId());

        return $this->render('wallet/detail.html.twig', ['wallet' => $wallet]);
    }

    #[Route('/wallet/delete/{id}', name: 'app_wallet_delete', methods: ['GEt', 'POST'])]
    public function delete(string $id): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->walletService->deleteUserWalletById($id, $user->getId());

        $this->addFlash('success', 'Wallet successfully deleted.');

        return $this->redirectToRoute('app_wallet_index');
    }
}