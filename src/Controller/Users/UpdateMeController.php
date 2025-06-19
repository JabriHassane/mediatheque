<?php

namespace App\Controller\Users;

use App\DTO\Users\UserUpdateDto;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UpdateMeController extends AbstractController
{
    public function __invoke(
        Request $request,
        Security $security,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        // Get the current user
        /** @var Users|null $user */
        $user = $security->getUser();

        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', 'You must be logged in.');
        }

        // Deserialize request data into a DTO
        $dto = $serializer->deserialize($request->getContent(), UserUpdateDto::class, 'json');

        // Update allowed fields (prevent updating email, password, roles, etc.)
        if ($dto->userName !== null) {
            $user->setUserName($dto->userName);
        }
        if ($dto->birthDay !== null) {
            $user->setBirthDay($dto->birthDay);
        }
        if ($dto->phone !== null) {
            $user->setPhone($dto->phone);
        }
        if ($dto->country !== null) {
            $user->setCountry($dto->country);
        }
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Persist changes
        $entityManager->flush();

        // Serialize the updated user to JSON with the read:user group
        $json = $serializer->serialize($user, 'json', ['groups' => ['read:user']]);

        // Return a JsonResponse
        return new JsonResponse($json, 200, [], true);
    }
}
