<?php declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoScopeControllerTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLIC_URL = 'api/V1/videos/%d/public';
    private const PRIVATE_URL = 'api/V1/videos/%d/private';
    private const UNLISTED_URL = 'api/V1/videos/%d/unlisted';

    private const VIDEO_SUCCESSFUL_DATA_STRUCTURE = [
        'id',
        'vkey',
        'filename',
        'title',
        'description',
        'state',
        'status',
        'duration',
        'directory',
        'default_thumbnail',
        'allow_comments',
        'allow_embed',
        'allow_download',
        'original_meta',
        'converted_at',
        'created_at',
        'updated_at',
    ];

    private const SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => self::VIDEO_SUCCESSFUL_DATA_STRUCTURE,
    ];

    /**
     * @dataProvider canMakeVideosPublicDataProvider
     */
    public function testCanMakeVideosPublic(
        string $actingUserType,
        string $subjectUserType,
        string $videoType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $video = $this->createVideoByType($videoType, $subjectUser->id);

        $response = $this->putJson(sprintf(self::PUBLIC_URL, $video->id));
        $response->assertStatus($expectedStatus);

        if ($expectedMessageKey) {
            $response->assertJsonFragment([
                'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
            ]);
        }

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function canMakeVideosPublicDataProvider(): array
    {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: private:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: public:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.public',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnBasicCases = [
            self::USER_TYPE_ADMIN . ' -> BASIC: private:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: public:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.public',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnInactiveCases = [
            self::USER_TYPE_ADMIN . ' -> INACTIVE: private:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: public:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.public',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicCases = [
            self::USER_TYPE_BASIC . ' -> BASIC: private:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.public',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.public',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicAnotherCases = [
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: private:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $inactiveOnInactiveCases = [
            self::USER_TYPE_BASIC . ' -> INACTIVE: private:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        return array_merge(
            $adminOnAdminCases,
            $adminOnBasicCases,
            $adminOnInactiveCases,
            $basicOnBasicCases,
            $basicOnBasicAnotherCases,
            $inactiveOnInactiveCases,
        );
    }

    /**
     * @dataProvider canMakeVideosPrivateDataProvider
     */
    public function testCanMakeVideosPrivate(
        string $actingUserType,
        string $subjectUserType,
        string $videoType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $video = $this->createVideoByType($videoType, $subjectUser->id);

        $response = $this->putJson(sprintf(self::PRIVATE_URL, $video->id));
        $response->assertStatus($expectedStatus);

        if ($expectedMessageKey) {
            $response->assertJsonFragment([
                'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
            ]);
        }

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function canMakeVideosPrivateDataProvider(): array
    {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: public:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: private:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.private',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnBasicCases = [
            self::USER_TYPE_ADMIN . ' -> BASIC: public:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: private:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.private',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnInactiveCases = [
            self::USER_TYPE_ADMIN . ' -> INACTIVE: public:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: private:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.private',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicCases = [
            self::USER_TYPE_BASIC . ' -> BASIC: public:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: unlisted:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.private',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: private:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.private',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicAnotherCases = [
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: private:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $inactiveOnInactiveCases = [
            self::USER_TYPE_BASIC . ' -> INACTIVE: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: private:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        return array_merge(
            $adminOnAdminCases,
            $adminOnBasicCases,
            $adminOnInactiveCases,
            $basicOnBasicCases,
            $basicOnBasicAnotherCases,
            $inactiveOnInactiveCases,
        );
    }

    /**
     * @dataProvider canMakeVideosUnlistedDataProvider
     */
    public function testCanMakeVideosUnlisted(
        string $actingUserType,
        string $subjectUserType,
        string $videoType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $video = $this->createVideoByType($videoType, $subjectUser->id);

        $response = $this->putJson(sprintf(self::UNLISTED_URL, $video->id));
        $response->assertStatus($expectedStatus);

        if ($expectedMessageKey) {
            $response->assertJsonFragment([
                'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
            ]);
        }

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function canMakeVideosUnlistedDataProvider(): array
    {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: public:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: private:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.unlisted',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnBasicCases = [
            self::USER_TYPE_ADMIN . ' -> BASIC: public:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: private:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.unlisted',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnInactiveCases = [
            self::USER_TYPE_ADMIN . ' -> INACTIVE: public:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: private:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.unlisted',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicCases = [
            self::USER_TYPE_BASIC . ' -> BASIC: public:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: private:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.unlisted',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_UNLISTED,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.unlisted',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicAnotherCases = [
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: private:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $inactiveOnInactiveCases = [
            self::USER_TYPE_BASIC . ' -> INACTIVE: public:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PUBLIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: private:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: unlisted:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_PRIVATE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: invalid:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'video.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        return array_merge(
            $adminOnAdminCases,
            $adminOnBasicCases,
            $adminOnInactiveCases,
            $basicOnBasicCases,
            $basicOnBasicAnotherCases,
            $inactiveOnInactiveCases,
        );
    }
}
