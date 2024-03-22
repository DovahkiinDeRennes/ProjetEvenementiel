<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function PHPUnit\Framework\throwException;
use App\Entity\User as AppUser;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getActif()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est plus actif');
        }
    }

    public function checkPostAuth(UserInterface $user):void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if($user->getActif()){
            throw new AccountExpiredException('testgg');
        }

//        if($user->getRoles()=='[ROLE_BANNED]'){
//            throw new AccountExpiredException('Vous n\'avez plus accès au site web : http://127.0.0.1:8000/. Veuillez contacter un administrateur par mail : [exempledemail@unjour.com]');
//        }
    }


}