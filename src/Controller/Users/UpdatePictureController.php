<?php

namespace App\Controller\Users;

use App\DTO\Users\UpdatePictureDto;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdatePictureController extends AbstractController
{
    public function __invoke(
        Request $request,
        Security $security,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        Filesystem $filesystem
    ): JsonResponse {
        // Get the current user
        /** @var Users|null $user */
        $user = $security->getUser();
        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', 'You must be logged in.');
        }

        // Create DTO and set the uploaded file
        $dto = new UpdatePictureDto();
        $dto->profilePicture = $request->files->get('profilePicture');

        // Validate DTO
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        // Handle profile picture upload
        if ($dto->profilePicture instanceof UploadedFile) {
            $uploadDir = $this->getParameter('profile_picture_upload_dir') . '/public/uploads/profiles';
            $filesystem->mkdir($uploadDir); // Create directory if it doesn't exist

            // Delete old profile picture if it exists
            if ($user->getProfilePicture()) {
                $oldFilePath = $this->getParameter('profile_picture_upload_dir') . '/public' . $user->getProfilePicture();
                if ($filesystem->exists($oldFilePath)) {
                    $filesystem->remove($oldFilePath);
                }
            }

            // Generate a unique filename
            $fileName = 'user_' . $user->getId() . '_' . uniqid() . '.' . $dto->profilePicture->guessExtension();
            $dto->profilePicture->move($uploadDir, $fileName);

            // Update the profile picture path
            $user->setProfilePicture('/uploads/profiles/' . $fileName);
        } else {
            throw new BadRequestHttpException('No profile picture uploaded.');
        }

        $user->setUpdatedAt(new \DateTimeImmutable());

        // Persist changes
        $entityManager->flush();

        // Serialize the updated user to JSON
        $json = $serializer->serialize($user, 'json', ['groups' => ['read:user']]);

        return new JsonResponse($json, 200, [], true);
    }
}