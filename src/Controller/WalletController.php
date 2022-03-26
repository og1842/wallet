<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WalletController extends AbstractController
{
    #[Route('/wallet', name: 'app_wallet_index')]
    public function index(): Response
    {
        return $this->render('wallet/index.html.twig', [
        ]);
    }
}