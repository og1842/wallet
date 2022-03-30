<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Security\WalletValidator;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletValidatorTest extends KernelTestCase
{
    public function testValidateRequestParamsForCreation(): void
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

        $walletValidator = $container->get(WalletValidator::class);

        try {
            $walletValidator->validateRequestParamsForCreation('name1', 'credit_card');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertTrue($status, 'Wallet params for creation is valid');

        try {
            $walletValidator->validateRequestParamsForCreation('name2', 'cash');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertTrue($status, 'Wallet params for creation is valid');

        try {
            $walletValidator->validateRequestParamsForCreation('name2', 'cash1');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertFalse($status, 'Wallet params for creation is not valid');
    }

    public function testValidateRequestParamsForFillBalance(): void
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

        $walletValidator = $container->get(WalletValidator::class);

        try {
            $walletValidator->validateRequestParamsForFillBalance('88645711a0134467bcc1a3f6797b2340', 'credit_card', '100.55');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertTrue($status, 'Fill balance params is valid');

        try {
            $walletValidator->validateRequestParamsForFillBalance('88645711a0134467bcc1a3f6797b2340', 'cash', '115');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertTrue($status, 'Fill balance params is valid');

        try {
            $walletValidator->validateRequestParamsForFillBalance('88645711a0134467bcc1a3f6797b2340', '', '');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertFalse($status, 'Fill balance params is not valid');
    }

    public function testValidateRequestParamsForTransfer(): void
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

        $walletValidator = $container->get(WalletValidator::class);

        try {
            $walletValidator->validateRequestParamsForTransfer('88645711a0134467bcc1a3f6797b2340', 'a41225c57f0e4a82abbc3a0e995a7c5f','Transfer', '100.55');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertTrue($status, 'Transfer params is valid');

        try {
            $walletValidator->validateRequestParamsForTransfer('88645711a0134467bcc1a3f6797b2340', 'a41225c57f0e4a82abbc3a0e995a7c5f','Transfer', '100.55');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertTrue($status, 'Transfer params is valid');

        try {
            $walletValidator->validateRequestParamsForTransfer('88645711a0134467bcc1a3f6797b2340', '88645711a0134467bcc1a3f6797b2340','Transfer', '100.55');
            $status = true;
        } catch (RuntimeException $ex) {
            $status = false;
        }

        $this->assertFalse($status, 'Transfer params is not valid');
    }
}