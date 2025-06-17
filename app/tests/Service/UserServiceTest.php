<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * @return array<string, array{
     *     email: string,
     *     plainPassword: string
     * }>
     */
    public static function registerDataProvider(): array
    {
        return [
            'valid credentials' => [
                'email' => 'test@example.com',
                'plainPassword' => 'password123',
            ],
            'another valid credentials' => [
                'email' => 'user@example.com',
                'plainPassword' => 'secret456',
            ],
        ];
    }

    #[DataProvider('registerDataProvider')]
    public function testRegister(
        string $email,
        string $plainPassword
    ): void {
        $userService = new UserService();

        $user = $userService->register(
            $email,
            $plainPassword
        );

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($email, $user->getEmail());
        $this->assertNotEquals($plainPassword, $user->getPassword());
        $this->assertTrue(password_verify($plainPassword, $user->getPassword()));
    }
}
