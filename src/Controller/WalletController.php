<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WalletController extends AbstractController
{
    #[Route('/index')]
    public function index(): Response
    {
        $number = random_int(100, 1000);

        return $this->render('wallet/index.html.twig', [
            'number' => $number,
        ]);
    }
}