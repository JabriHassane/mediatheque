<?php

namespace App\Controller\Users;

use App\DTO\Users\ChangePasswordDto;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class ChangePasswordController extends AbstractController
{
    public function __invoke(
        Request $request,
        Security $security,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Get the current user
        /** @var Users|null $user */
        $user = $security->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', 'You must be logged in.');
        }

        // Deserialize request data into a DTO
        $dto = $serializer->deserialize($request->getContent(), ChangePasswordDto::class, 'json');

        // Verify current password
        if (!$passwordHasher->isPasswordValid($user, $dto->currentPassword)) {
            throw new BadRequestHttpException('Current password is incorrect.');
        }

        // Hash and set the new password
        $hashedPassword = $passwordHasher->hashPassword($user, $dto->newPassword);
        $user->setPassword($hashedPassword);
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Persist changes
        $entityManager->flush();

        return new JsonResponse(['message' => 'Password changed successfully'], 200);
    }
}