<?php declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase {
    use RefreshDatabase;

    private const BASE_URL = 'api/V1/users';
    private const UPDATE_URL = 'api/V1/users/%d';
    private const ACTIVATE_URL = 'api/V1/users/%d/activate';
    private const DEACTIVATE_URL = 'api/V1/users/%d/deactivate';

        private const USER_SUCCESSFUL_DATA_STRUCTURE = [
            'id',
            'username',
            'email',
            'status',
            'created_at',
            'updated_at',
        ];

        private const SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE = [
            'success',
            'messages',
            'data' => self::USER_SUCCESSFUL_DATA_STRUCTURE,
        ];

        private const LIST_SUCCESSFUL_RESPONSE_STRUCTURE = [
            'success',
            'messages',
            'data' => [
                self::USER_SUCCESSFUL_DATA_STRUCTURE
            ],
        ];

    private const UPDATE_VALID_INPUT = [
        'bio' => 'Hello world',
        'channel_name' => 'TheDarkEra29',
    ];

    private const UPDATE_INVALID_INPUT = [
        'bio' => 'Hello world',
        'channel_name' => '$$odja',
    ];

    private const UPDATE_TOO_LONG_INPUT = [
        'bio' => 'Hello world',
        'channel_name' => 'XDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDXDCXDXDXDXDXDX'
    ];

    private const UPDATE_TOO_SHORT_INPUT = [
        'bio' => '',
        'channel_name' => ''
    ];

    public function setUp(): void {
        parent::setUp();
    }

    /**
     * @dataProvider usersCanListUsersDataProvider
     */
    public function testUserCanListUsers(
        string $actingUserType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $this->be($actingUser);

        $response = $this->getJson(self::BASE_URL);

        $response->assertStatus($expectedStatus)->assertJsonFragment([
            'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
        ]);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function usersCanListUsersDataProvider(): array {
        return [
            self::USER_TYPE_ADMIN => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.find.list',
                'expectedJsonStructure' => self::LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.find.list',
                'expectedJsonStructure' => self::LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_INACTIVE => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.find.permissions',
                'expectedJsonStructure' => null,
            ],
        ];
    }

    /**
     * @dataProvider usersCanActivateUsersDataProvider
     */
    public function testUserCanActivateUsers(
        string $actingUserType,
        string $subjectUserType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $response = $this->putJson(sprintf(self::ACTIVATE_URL, $subjectUser->id));
        $response->assertStatus($expectedStatus)->assertJsonFragment([
            'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
        ]);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function usersCanActivateUsersDataProvider(): array
    {
        return [
            // ADMIN user test cases
            self::USER_TYPE_ADMIN . ' -> ADMIN: activate.inactive.user:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.update.activate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: activate.active.user:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'user.failed.update.already.active',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: activate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: activate.himself:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => null,
            ],
            // BASIC active user test cases
            self::USER_TYPE_BASIC . ': activate.inactive.user:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_BASIC . ': activate.active.user:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_BASIC . ': activate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
            // BASIC inactive user test cases
            self::USER_TYPE_INACTIVE . ': activate.inactive.user:bad' => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.find.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_INACTIVE . ': activate.active.user:bad' => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.find.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_INACTIVE . ': activate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
        ];
    }

    /**
     * @dataProvider usersCanDeactivateUsersDataProvider
     */
    public function testUserCanDeactivateUsers(
        string $actingUserType,
        string $subjectUserType,
        int $expectedStatus,
        string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $response = $this->putJson(sprintf(self::DEACTIVATE_URL, $subjectUser->id));
        $response->assertStatus($expectedStatus)->assertJsonFragment([
            'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
        ]);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function usersCanDeactivateUsersDataProvider(): array
    {
        return [
            // ADMIN user test cases
            self::USER_TYPE_ADMIN . ': deactivate.active.user:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.update.deactivate',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ': deactivate.deactive.user:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'user.failed.update.already.inactive',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_ADMIN . ': deactivate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_ADMIN . ': deactivate.himself:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => null,
            ],
            // BASIC active user test cases
            self::USER_TYPE_BASIC . ': deactivate.active.user:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_BASIC . ': deactivate.deactive.user:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_BASIC . ': deactivate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
            // BASIC inactive user test cases
            self::USER_TYPE_INACTIVE . ': deactivate.active.user:bad' => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.find.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_INACTIVE . ': deactivate.deactive.user:bad' => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.find.permissions',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_INACTIVE . ': activate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_INACTIVE,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
        ];
    }

    /**
     * @dataProvider usersCanUpdateUsersDataProvider
     */
    public function testUserCanUpdateUsers(
        string $actingUserType,
        string $subjectUserType,
        array $input,
        int $expectedStatus,
        ?string $expectedMessageKey,
        ?array $expectedJSONStructure,
    ) {
        $actingUser = $this->createUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->createUserByType($subjectUserType);

        $this->be($actingUser);

        $response = $this->putJson(sprintf(self::UPDATE_URL, $subjectUser->id), $input);
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

    public function usersCanUpdateUsersDataProvider(): array
    {
        $adminOnAdminCases = [
            self::USER_TYPE_ADMIN . ' -> ADMIN: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.update.single',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> ADMIN: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_ADMIN,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnBasicCases = [
            self::USER_TYPE_ADMIN . ' -> BASIC: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.update.single',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> BASIC: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $adminOnInvalidCases = [
            self::USER_TYPE_ADMIN . ' -> INVALID: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INVALID: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INVALID: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ' -> INVALID: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicCases = [
            self::USER_TYPE_BASIC . ' -> BASIC: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.update.single',
                'expectedJsonStructure' => self::SINGLE_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_BAD_REQUEST,
                'expectedMessageKey' => null,
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnBasicAnotherCases = [
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> BASIC_ANOTHER: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_BASIC_ANOTHER,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedMessageKey' => 'user.failed.update.permissions',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        $basicOnInvalidCases = [
            self::USER_TYPE_BASIC . ' -> INVALID: valid.input:ok' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_VALID_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INVALID: invalid.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_INVALID_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INVALID: long.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_TOO_LONG_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
            self::USER_TYPE_BASIC . ' -> INVALID: short.input:bad' => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'input' => self::UPDATE_TOO_SHORT_INPUT,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => self::ERROR_MESSAGE_WITH_EMPTY_DATA_STRUCTURE,
            ],
        ];

        return array_merge(
            $adminOnAdminCases,
            $adminOnBasicCases,
            $adminOnInvalidCases,
            $basicOnBasicCases,
            $basicOnBasicAnotherCases,
            $basicOnInvalidCases,
        );
    }

    // TODO: testUserCanDeleteUser
}
