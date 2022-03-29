<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GoogleUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Throwable;

class UserService
{
    private Security $security;
    private UserRepository $repository;
    private LoggerInterface $logger;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(Security $security, UserRepository $userRepository, LoggerInterface $logger, UserPasswordHasherInterface $userPasswordHasher)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
        $this->repository = $userRepository;
        $this->logger = $logger;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Is user logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }

    /**
     * Get user by id
     *
     * @param int $id
     *
     * @return User|null
     */
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
        $user->setIsVerified(true);

        try {
            $this->repository->save($user);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to save facebook user.', ['email' => $email, 'message' => $ex->getMessage()]);

            return null;
        }

        return $user;
    }

    /**
     * Register with google user
     *
     * @param GoogleUser $googleUser
     *
     * @return User|null
     */
    public function registerWithGoogleUser(GoogleUser $googleUser): ?User
    {
        $googleId = $googleUser->getId();

        if (!$googleId) {
            return null;
        }

        $email = $googleUser->getEmail();

        if (!$email) {
            return null;
        }

        // 1) have they logged in with Google before?
        $existingGoogleUser = $this->repository->findOneBy(['googleId' => $googleId]);

        if ($existingGoogleUser) {
            return $existingGoogleUser;
        }

        // 2) do we have a matching user by email?
        $user = $this->repository->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();

            $user->setEmail($email);

            $firstName = $googleUser->getFirstName();
            $lastName = $googleUser->getLastName();

            if ($firstName) {
                $user->setFirstName($firstName);
            }

            if ($lastName) {
                $user->setLastName($lastName);
            }
        }

        $user->setGoogleId($googleId);
        $user->setIsVerified(true);

        try {
            $this->repository->save($user);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to save google user.', ['email' => $email, 'message' => $ex->getMessage()]);

            return null;
        }

        return $user;
    }

    /**
     * Verify user
     *
     * @param User $user
     *
     * @return void
     */
    public function verifyUser(User $user): void
    {
        $user->setIsVerified(true);

        try {
            $this->repository->save($user);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to verify user', ['message' => $ex->getMessage()]);
        }
    }

    /**
     * Register user
     *
     * @param User $user
     * @param string $plainPassword
     *
     * @return bool
     */
    public function register(User $user, string $plainPassword): bool
    {
        // encode the plain password
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );

        try {
            $this->repository->save($user);
        } catch (Throwable $ex) {
            $this->logger->error('Unable to register user', ['message' => $ex->getMessage()]);

            return false;
        }

        return true;
    }
}