<?php

namespace App\DataFixtures;

use App\Entity\Record;
use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WalletFixture extends Fixture
{
    /**
     * Load dummy data to db for test
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFirstName('test');
        $user->setLastName('test');
        $user->setEmail('test@test.test');

        $manager->persist($user);

        $fromWallet = new Wallet('88645711a0134467bcc1a3f6797b2340');
        $fromWallet->setName('from');
        $fromWallet->setWalletType('credit_card');
        $fromWallet->setBalance(100000);
        $fromWallet->setUser($user);
        $manager->persist($fromWallet);

        $wallet = new Wallet('a41225c57f0e4a82abbc3a0e995a7c5f');
        $wallet->setName('to');
        $wallet->setWalletType('cash');
        $wallet->setBalance(100000);
        $wallet->setUser($user);

        $manager->persist($wallet);

//        $record = new Record();
//        $record->setFromWallet($fromWallet);
//        $record->setToWallet($wallet);
//        $record->setName('transfer');
//        $record->setAmount(100000);

//        $manager->persist($record);

        $manager->flush();
    }
}