<?php

namespace App\DTO\Users;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePictureDto
{
    #[Assert\Image(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Please upload a valid JPEG or PNG image"
    )]
    public ?UploadedFile $profilePicture = null;
}