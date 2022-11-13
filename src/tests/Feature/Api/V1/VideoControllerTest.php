<?php

namespace Tests\Feature\Api\V1;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoControllerTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL = 'api/V1/videos';
    private const SEARCH_URL = 'api/V1/videos/search';
    private const VIEW_URL = 'api/V1/videos/%d';
    private const UPDATE_URL = 'api/V1/videos/%d';

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
        'qualities',
        'tags',
        'total_views',
        'total_comments',
        'allow_comments',
        'allow_embed',
        'allow_download',
        'server_url',
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

    private const LIST_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => [
            self::VIDEO_SUCCESSFUL_DATA_STRUCTURE
        ],
    ];

    private const SINGLE_ERROR_RESPONSE_STRUCTURE = [
        'error',
        'messages',
    ];

    private const UPDATE_VALID_INPUT = [
        'title' => 'Hello world',
        'description' => 'The world of hellos',
        'scope' => 'public',
        'state' => 'active',
    ];

    private const UPDATE_INVALID_INPUT = [
        'scope' => 'everyoneCanSee',
        'state' => 'activated',
    ];

    private const UPDATE_TOO_LONG_INPUT = [
        'title' => 'XDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDCXDXDXDXDXDX
        XDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDCXDXDXDXDXDX
        XDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDCXDXDXDXDXDX',
    ];

    private const UPDATE_TOO_SHORT_INPUT = [
        'title' => 'hello',
        'description' => 'wow',
    ];

    /**
     * @dataProvider canListVideosDataProvider
     */
    public function testCanListVideos(
        string $actingUserType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->getUserByType($actingUserType);
        $this->be($actingUser);

        $this->createActiveVideo($actingUser->id);

        $response = $this->getJson(self::BASE_URL);

        $response->assertStatus($expectedStatus)->assertJsonFragment([
            'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
        ]);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function canListVideosDataProvider(): array {
        return [
            self::USER_TYPE_ADMIN => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.find.list',
                'expectedJsonStructure' => self::LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.find.list',
                'expectedJsonStructure' => self::LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_INACTIVE => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.find.list',
                'expectedJsonStructure' => self::LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
        ];
    }

    /**
     * @dataProvider canViewVideosDataProvider
     */
    public function testCanViewVideos(
        string $actingUserType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->getUserByType($actingUserType);
        $this->be($actingUser);

        $video = $this->createActiveVideo($actingUser->id);
        $response = $this->getJson(sprintf(self::VIEW_URL, $video->id));

        $response->assertStatus($expectedStatus)->assertJsonFragment([
            'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
        ]);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function canViewVideosDataProvider(): array {
        return [
            self::USER_TYPE_ADMIN => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.find.fetch',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.find.fetch',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_INACTIVE => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.find.fetch',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
        ];
    }

    /**
     * @dataProvider canUpdateVideosDataProvider
     */
    public function testCanUpdateVideos(
        string $actingUserType,
        string $subjectUserType,
        array $input,
        int $expectedStatus,
        ?string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->getUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->getUserByType($subjectUserType);

        $video = $this->createActiveVideo($subjectUser->id);

        $this->be($actingUser);

        $response = $this->putJson(sprintf(self::UPDATE_URL, $video->id), $input);
        dump($response->getContent());
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

    public function canUpdateVideosDataProvider() {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.single',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
        ];

        $adminOnBasicCases = [
            self::USER_TYPE_ADMIN . ' -> BASIC: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.single',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
        ];

        $basicOnBasicCases = [
            self::USER_TYPE_BASIC . ' -> BASIC: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'video.success.update.single',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
        ];

        $basicOnBasicAnotherCases = [
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'video.failed.update.permissions',
                'expectedJsonStructure' => self::SINGLE_ERROR_RESPONSE_STRUCTURE,
            ],
        ];

        return array_merge(
            $adminOnAdminCases,
            $adminOnBasicCases,
            $basicOnBasicCases,
            $basicOnBasicAnotherCases,
        );
    }

    // testCanUploadVideos
    // testCanUpdateVideos
    // testCanDeleteVideos
    // testCanSearchVideos

}
