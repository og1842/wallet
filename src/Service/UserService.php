<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Security\Core\Security;

class UserService
{
    private Security $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function isLoggedIn(): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }

    public function someMethod()
    {
        //todo
        $user = $this->security->getUser();

        // ...
    }
}