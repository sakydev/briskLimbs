<?php

namespace Tests;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private UserRepository $userRepository;
    public function setUp(): void {
        parent::setUp();

        $this->userRepository = $this->app->make(UserRepository::class);
    }

    public function createUser(string $username, int $level, string $status = 'active'): User {
        $user = $this->userRepository->create([
            'username' => $username,
            'level' => $level,
            'email' => sprintf('%d%d@gmail.com', time(), rand(99, 999)),
            'password' => 'hello',
        ]);

        if ($status == User::INACTIVE_STATE) {
            $this->userRepository->deactivate($user);
        }

        return $user;
    }

    public function createAdminUser(string $username): User {
        return $this->createUser($username, 1);
    }

    public function createBasicUser(string $username): User {
        return $this->createUser($username, 5);
    }

    public function createBasicInactiveUser(string $username): User {
        return $this->createUser($username, 5, USER::INACTIVE_STATE);
    }

}
