<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = $this->userService->register(
            'user1@example.com',
            'password1'
        );

        $manager->persist($user1);
        $manager->flush();
    }
}
