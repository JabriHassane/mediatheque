<?php

namespace App\DTO\Users;

class UserUpdateDto
{

    public ?string $userName = null;
    public ?\DateTime $birthDay = null;
    public ?string $phone = null;
    public ?string $country = null;

}