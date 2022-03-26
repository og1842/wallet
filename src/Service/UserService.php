<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\FacebookUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Throwable;

class UserService
{
    private Security $security;
    private UserRepository $repository;
    private LoggerInterface $logger;

    public function __construct(Security $security, UserRepository $userRepository, LoggerInterface $logger)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
        $this->repository = $userRepository;
        $this->logger = $logger;
    }

    public function isLoggedIn(): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }

    public function getById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    /**
     * Register with facebook user
     *
     * @param FacebookUser $facebookUser
     *
     * @return User|null
     */
    public function registerWithFacebookUser(FacebookUser $facebookUser): ?User
    {
        $facebookId = $facebookUser->getId();

        if (!$facebookId) {
            return null;
        }

        $email = $facebookUser->getEmail();

        if (!$email) {
            return null;
        }

        // 1) have they logged in with Facebook before?
        $existingFbUser = $this->repository->findOneBy(['facebookId' => $facebookId]);

        if ($existingFbUser) {
            return $existingFbUser;
        }

        // 2) do we have a matching user by email?
        $user = $this->repository->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();

            $user->setEmail($email);

            $firstName = $facebookUser->getFirstName();
            $lastName = $facebookUser->getLastName();

            if ($firstName) {
                $user->setFirstName($firstName);
            }

            if ($lastName) {
                $user->setLastName($lastName);
            }
        }

        $user->setFacebookId($facebookId);

        try {
            $this->repository->save($user);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to save user.', ['email' => $email, 'message' => $ex->getMessage()]);

            return null;
        }

        return $user;
    }
}