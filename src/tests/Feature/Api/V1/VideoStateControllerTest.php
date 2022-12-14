<?php declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoStateControllerTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVATE_URL = 'api/V1/videos/%d/activate';
    private const DEACTIVATE_URL = 'api/V1/videos/%d/deactivate';

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
     * @dataProvider canActivateVideosDataProvider
     */
    public function testCanActivateVideos(
        string $actingUserType,
        string $subjectUserType,
        string $videoType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ): void {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $video = $this->createVideoByType($videoType, $subjectUser->id);

        $response = $this->putJson(sprintf(self::ACTIVATE_URL, $video->id));
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

    public function canActivateVideosDataProvider(): array {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: inactive:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.activate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: active:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.active',
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
            self::USER_TYPE_ADMIN . ' -> BASIC: inactive:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.activate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: active:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.active',
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
            self::USER_TYPE_ADMIN . ' -> INACTIVE: inactive:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.activate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: active:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.active',
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
            self::USER_TYPE_BASIC . ' -> BASIC: inactive:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.activate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: active:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.active',
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
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: inactive:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: active:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
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
            self::USER_TYPE_BASIC . ' -> INACTIVE: inactive:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: active:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
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
     * @dataProvider canDeactivateVideosDataProvider
     */
    public function testCanDeactivateVideos(
        string $actingUserType,
        string $subjectUserType,
        string $videoType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ): void {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $video = $this->createVideoByType($videoType, $subjectUser->id);

        $response = $this->putJson(sprintf(self::DEACTIVATE_URL, $video->id));
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

    public function canDeactivateVideosDataProvider(): array {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: active:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.deactivate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: inactive:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.inactive',
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
            self::USER_TYPE_ADMIN . ' -> BASIC: active:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.deactivate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: inactive:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.inactive',
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
            self::USER_TYPE_ADMIN . ' -> INACTIVE: active:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.deactivate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INACTIVE: inactive:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.inactive',
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
            self::USER_TYPE_BASIC . ' -> BASIC: active:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.deactivate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: inactive:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'video.failed.update.already.inactive',
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
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: active:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: inactive:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
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
            self::USER_TYPE_BASIC . ' -> INACTIVE: inactive:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INACTIVE: active:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'videoType' => self::VIDEO_TYPE_ACTIVE,
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
