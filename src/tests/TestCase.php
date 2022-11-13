<?php

namespace Tests;

use App\Models\User;
use App\Models\Video;
use App\Repositories\UserRepository;
use App\Repositories\VideoRepository;
use App\Services\Videos\VideoService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected const USER_TYPE_ADMIN = 'Admin user';
    protected const USER_TYPE_BASIC = 'Basic user';
    protected const USER_TYPE_BASIC_ANOTHER = 'Another basic user';
    protected const USER_TYPE_INACTIVE = 'Inactive user';
    protected const USER_TYPE_INVALID = 'Non-existing user';

    private VideoService $videoService;
    private UserRepository $userRepository;
    private VideoRepository $videoRepository;
    public function setUp(): void {
        parent::setUp();

        $this->videoService = $this->app->make(VideoService::class);
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->videoRepository = $this->app->make(VideoRepository::class);
    }

    protected function getExpectedMessage(string $messageKey, int $stauts): array|string {
        $translated = __($messageKey);
        if ($stauts !== Response::HTTP_OK && $stauts !== Response::HTTP_CREATED) {
            return [$translated];
        }

        return $translated;
    }

    private function createVideo(
        array $input,
        int $userId,
    ): Video {
        $input['title'] = 'The Dance of Dragons';
        $input['description'] = 'It began and took away many';

        $video = $this->videoRepository->create(
            $input,
            $this->videoService->generateFilename(),
            $this->videoService->generateVkey(),
            $this->videoService->getMeta('', true),
            $userId,
        );

        return $video;
    }

    protected function createPublicVideo(int $userId): Video {
        return $this->createVideo(
            ['scope' => 'public'],
            $userId,
        );
    }

    protected function createPrivateVideo(int $userId) {
        return $this->createVideo(
            ['scope' => 'private'],
            $userId,
        );
    }

    protected function createUnlistedVideo(int $userId) {
        return $this->createVideo(
            ['scope' => 'unlisted'],
            $userId,
        );
    }

    protected function createActiveVideo(int $userId) {
        return $this->createVideo(
            ['state' => 'active'],
            $userId,
        );
    }

    protected function createInactiveVideo(int $userId) {
        return $this->createVideo(
            ['state' => 'inactive'],
            $userId,
        );
    }

    protected function createUser(string $username, int $level, string $status = 'active'): User {
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

    protected function createAdminUser(string $username): User {
        return $this->createUser($username, 1);
    }

    protected function createBasicUser(string $username): User {
        return $this->createUser($username, 5);
    }

    protected function createBasicInactiveUser(string $username): User {
        return $this->createUser($username, 5, USER::INACTIVE_STATE);
    }

    protected function getUserByType(string $userType): ?User {
        switch ($userType) {
            case self::USER_TYPE_ADMIN:
                return $this->createAdminUser('daemon');
            case self::USER_TYPE_BASIC:
                return $this->createBasicUser('tully');
            case self::USER_TYPE_BASIC_ANOTHER:
                return $this->createBasicUser('stark');
            case self::USER_TYPE_INACTIVE:
                return $this->createBasicInactiveUser('ned');
            case self::USER_TYPE_INVALID:
                $user = new User();
                $user->id = 99;

                return $user;
        }

        return null;
    }
}
