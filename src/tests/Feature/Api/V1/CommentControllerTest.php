<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL = 'api/V1/videos/%d/comments';
    private const VIEW_URL = 'api/V1/videos/%d/comments/%d';
    private const UPDATE_URL = 'api/V1/videos/%d/comments/%d';
    private const DELETE_URL = 'api/V1/videos/%d/comments/%d';

    private const TOO_LONG_LENGTH = 3100;

    private const COMMENT_SUCCESSFUL_DATA_STRUCTURE = [
        'id',
        'video_id',
        'user_id',
        'content',
        'created_at',
        'updated_at',
    ];

    private const SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => self::COMMENT_SUCCESSFUL_DATA_STRUCTURE,
    ];

    private const LIST_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => [
            self::COMMENT_SUCCESSFUL_DATA_STRUCTURE
        ],
    ];

    private const VALID_COMMENT = 'Hello World';

    private const INPUT_VALID_UPDATE = ['content' => 'Hello world'];
    private const INPUT_INVALID_UPDATE = [];
    private const INPUT_TOO_LONG_UPDATE = self::TOO_LONG_LENGTH;
    private const INPUT_TOO_SHORT_UPDATE = ['content' => 'x'];

    // TODO: testCanViewComments
    // TODO: testCanCreateComments
    // TODO: testCanUpdateComments
    // TODO: testCanDeleteComments
}
