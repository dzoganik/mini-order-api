<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

class UserService
{
    public function register(
        string $email,
        string $plainPassword
    ): User {
        return new User(
            $email,
            password_hash(
                $plainPassword,
                PASSWORD_BCRYPT
            )
        );
    }
}
