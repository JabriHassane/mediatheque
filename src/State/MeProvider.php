<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\UsersRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MeProvider implements ProviderInterface
{

    public function __construct(private Security $security)
    {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return null;
    }
}
