<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletServiceTest extends KernelTestCase
{
    public function testCreateByNameAndType(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel([
            'debug' => false
        ]);

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $entityManager = $container->get(EntityManagerInterface::class);

        $userRepository = $entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => 'test@test.test']);

        if (!$user instanceof User) {
            $this->fail('User not found. Please run bin/console --env=test doctrine:fixtures:load');
        }

        $walletService = $container->get(WalletService::class);
        $status = $walletService->createByNameAndType('n', 'credit_card', $user);

        $this->assertTrue($status, 'Is waller created');
    }
}