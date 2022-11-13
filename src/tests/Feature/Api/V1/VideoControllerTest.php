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

    // public function testCanUploadVideos() {}
    // testCanViewVideos
    // testCanUpdateVideos
    // testCanDeleteVideos
    // testCanSearchVideos

}
