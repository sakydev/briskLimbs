<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase {
    use RefreshDatabase;

    private const BASE_URL = 'api/V1/users';
    private const ACTIVATE_URL = 'api/V1/users/%d/activate';
    private const DEACTIVATE_URL = 'api/V1/users/%d/deactivate';

    private User $adminUser;
    private User $basicUser;
    private User $inactiveUser;

    private const USER_TYPE_ADMIN = 'Admin user';
    private const USER_TYPE_BASIC = 'Basic user';
    private const USER_TYPE_INACTIVE = 'Inactive user';
    private const USER_TYPE_INVALID = 'Non-existing user';

    private const ADMIN_USERNAME = 'daemon';
    private const BASIC_USERNAME = 'tully';
    private const INACTIVE_USERNAME = 'ned';

    private const USER_SUCCESSFUL_DATA_STRUCTURE = [
        'id',
        'username',
        'email',
        'status',
        'created_at',
        'updated_at',
    ];

    private const USER_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => self::USER_SUCCESSFUL_DATA_STRUCTURE,
    ];

    private const USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => [
            self::USER_SUCCESSFUL_DATA_STRUCTURE
        ],
    ];

    public function setUp(): void {
        parent::setUp();
    }

    private function getUserByType(string $userType): ?User {
        switch ($userType) {
            case self::USER_TYPE_ADMIN:
                return $this->createAdminUser(self::ADMIN_USERNAME);
            case self::USER_TYPE_BASIC:
                return $this->createBasicUser(self::BASIC_USERNAME);
            case self::USER_TYPE_INACTIVE:
                return $this->createBasicInactiveUser(self::INACTIVE_USERNAME);
            case self::USER_TYPE_INVALID:
                $user = new User();
                $user->id = 99;

                return $user;

        }

        return null;
    }

    private function getExpectedMessage(string $messageKey, int $stauts): array|string {
        $translated = __($messageKey);
        if ($stauts !== Response::HTTP_OK && $stauts !== Response::HTTP_CREATED) {
            return [$translated];
        }

        return $translated;
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
        $actingUser = $this->getUserByType($actingUserType);
        $this->be($actingUser);

        $response = $this->getJson(self::BASE_URL);

        $response->assertStatus($expectedStatus)->assertJsonFragment([
            'messages' => $this->getExpectedMessage($expectedMessageKey, $expectedStatus),
        ]);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function usersCanListUsersDataProvider(): array
    {
        return [
            self::USER_TYPE_ADMIN => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.find.list',
                'expectedJsonStructure' => self::USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC => [
                'actingUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.find.list',
                'expectedJsonStructure' => self::USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
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
        $actingUser = $this->getUserByType($actingUserType);
        $subjectUser = $actingUserType === $subjectUserType ? $actingUser : $this->getUserByType($subjectUserType);

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
            self::USER_TYPE_ADMIN . ': activate.inactive.user:ok' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_OK,
                'expectedMessageKey' => 'user.success.update.activate',
                'expectedJsonStructure' => self::USER_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_ADMIN . ': activate.active.user:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedMessageKey' => 'user.failed.update.already.active',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_ADMIN . ': activate.invalid.user:bad' => [
                'actingUserType' => self::USER_TYPE_ADMIN,
                'subjectUserType' => self::USER_TYPE_INVALID,
                'expectedStatus' => Response::HTTP_NOT_FOUND,
                'expectedMessageKey' => 'user.failed.find.fetch',
                'expectedJsonStructure' => null,
            ],
            self::USER_TYPE_ADMIN . ': activate.himself:bad' => [
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

    // TODO: testUserCanActivateUser
    // TODO: testUserCanDeactivateUser
    // TODO: testUserCanUpdateUser
    // TODO: testUserCanDeleteUser
}
