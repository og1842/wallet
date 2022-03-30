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

    public function testFillUserBalance(): void
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
        $status = $walletService->fillUserBalance($user->getId(), '88645711a0134467bcc1a3f6797b2340', 10408, 'Fill balance');

        $this->assertTrue($status, 'User balance is filled');

        $status = $walletService->fillUserBalance($user->getId(), '1111', 10408, 'Fill balance');

        $this->assertFalse($status, 'User balance is not filled');
    }

    public function testTransfer(): void
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
        $status = $walletService->transfer($user->getId(), '88645711a0134467bcc1a3f6797b2340', 'a41225c57f0e4a82abbc3a0e995a7c5f', '10408', 'Transfer');

        $this->assertTrue($status, 'Transfer done');

        $status = $walletService->transfer($user->getId(), '88645711a0134467bcc1a3f6797b2340', '88645711a0134467bcc1a3f6797b2340', '10408', 'Transfer');

        $this->assertFalse($status, 'Transfer failed');
    }
}