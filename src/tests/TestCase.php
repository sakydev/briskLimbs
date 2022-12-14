<?php

namespace Tests;

use App\Models\Comment;
use App\Models\User;
use App\Models\Video;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;
use App\Repositories\VideoRepository;
use App\Services\Videos\VideoService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public const SUCCESS_MESSAGE_WITH_EMPTY_DATA_STRUCTURE = [
        'success',
        'messages',
        'data',
    ];

    public const ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE = [
        'error',
        'messages',
    ];

    protected const USER_TYPE_ADMIN = 'Admin user';
    protected const USER_TYPE_BASIC = 'Basic user';
    protected const USER_TYPE_BASIC_ANOTHER = 'Another basic user';
    protected const USER_TYPE_INACTIVE = 'Inactive user';
    protected const USER_TYPE_INVALID = 'Non-existing user';

    protected const VIDEO_TYPE_ACTIVE = 'Active video';
    protected const VIDEO_TYPE_INACTIVE = 'Inactive video';
    protected const VIDEO_TYPE_PUBLIC = 'Public video';
    protected const VIDEO_TYPE_PRIVATE = 'Private video';
    protected const VIDEO_TYPE_UNLISTED = 'Unlisted video';
    protected const VIDEO_TYPE_INVALID = 'Invalid video';

    private VideoService $videoService;
    private UserRepository $userRepository;
    private VideoRepository $videoRepository;
    private CommentRepository $commentRepository;
    protected function setUp(): void {
        parent::setUp();

        $this->videoService = $this->app->make(VideoService::class);
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->videoRepository = $this->app->make(VideoRepository::class);
        $this->commentRepository = $this->app->make(CommentRepository::class);
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

    protected function createPrivateVideo(int $userId): ?Video {
        return $this->createVideo(
            ['scope' => 'private'],
            $userId,
        );
    }

    protected function createUnlistedVideo(int $userId): ?Video {
        return $this->createVideo(
            ['scope' => 'unlisted'],
            $userId,
        );
    }

    protected function createActiveVideo(int $userId): ?Video {
        return $this->createVideo(
            ['state' => 'active'],
            $userId,
        );
    }

    protected function createInactiveVideo(int $userId): ?Video {
        return $this->createVideo(
            ['state' => 'inactive'],
            $userId,
        );
    }

    protected function createVideoByType(string $videoType, int $userId): ?Video {
        switch ($videoType) {
            case self::VIDEO_TYPE_ACTIVE:
            case self::VIDEO_TYPE_PUBLIC:
                return $this->createActiveVideo($userId);
            case self::VIDEO_TYPE_INACTIVE:
                return $this->createInactiveVideo($userId);
            case self::VIDEO_TYPE_PRIVATE:
                return $this->createPrivateVideo($userId);
            case self::VIDEO_TYPE_UNLISTED:
                return $this->createUnlistedVideo($userId);
            case self::VIDEO_TYPE_INVALID:
                $video = new Video();
                $video->id = 99;

                return $video;
        }

        return null;
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
        return $this->createUser($username, 5, User::INACTIVE_STATE);
    }

    protected function createUserByType(string $userType): ?User {
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

    protected function createVideoComment(string $content, int $userId, int $videoId): Comment {
        return $this->commentRepository->create(['content' => $content], $userId, $videoId);
    }
}
