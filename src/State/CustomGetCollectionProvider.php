<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\PostsRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomGetCollectionProvider implements ProviderInterface
{
    public function __construct(
        private PostsRepository $postsRepository,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $provider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $data =  $this->provider->provide($operation, $uriVariables, $context);
        dd($data);
        return $data;
    }
}
