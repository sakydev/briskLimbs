<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase {
    use RefreshDatabase;

    private const BASE_URL = 'api/V1/users';

    private User $adminUser;
    private User $basicUser;
    private User $inactiveUser;

    private const USER_TYPE_ADMIN = 'Admin user';
    private const USER_TYPE_BASIC = 'Basic user';
    private const USER_TYPE_INACTIVE = 'Inactive user';

    private const ADMIN_USERNAME = 'daemon';
    private const BASIC_USERNAME = 'tully';
    private const INACTIVE_USERNAME = 'ned';

    private const USER_SUCCESSFUL_DATA_STRUCTURE = [
        [
            'id',
            'username',
            'email',
            'status',
            'created_at',
            'updated_at',
        ]
    ];

    private const USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE = [
        'success',
        'messages',
        'data' => self::USER_SUCCESSFUL_DATA_STRUCTURE,
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
        }

        return null;
    }

    /**
     * @dataProvider usersCanListUsersDataProvider
     */
    public function testUserCanListUsers(
        string $userType,
        int $expectedStatus,
        ?array $expectedJSONStructure,
    ) {
        $user = $this->getUserByType($userType);
        $this->be($user);

        $response = $this->getJson(self::BASE_URL);
        $response->assertStatus($expectedStatus);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function usersCanListUsersDataProvider(): array
    {
        return [
            self::USER_TYPE_ADMIN => [
                'userType' => self::USER_TYPE_ADMIN,
                'expectedStatus' => Response::HTTP_OK,
                'expectedJsonStructure' => self::USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_BASIC => [
                'user' => self::USER_TYPE_BASIC,
                'expectedStatus' => Response::HTTP_OK,
                'expectedJsonStructure' => self::USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            self::USER_TYPE_INACTIVE => [
                'user' => self::USER_TYPE_INACTIVE,
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedJsonStructure' => null,
            ],
        ];
    }
}
