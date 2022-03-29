<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): RedirectResponse
    {
        if ($this->userService->isLoggedIn()) {
            return $this->redirectToRoute('app_wallet_index');
        }

        return $this->redirectToRoute('app_login');
    }

}