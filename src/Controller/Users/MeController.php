<?php

namespace App\Controller\Users;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

final class MeController extends AbstractController
{

    public function __construct(private Security $security){}

    public function __invoke()
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        return $this->json($user, 200, [], ['groups' => ['read:user']]);
    }

}
