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
        'data' => [
            '*' => self::USER_SUCCESSFUL_DATA_STRUCTURE,
        ],
    ];

    public function setUp(): void {
        parent::setUp();

        $this->adminUser = $this->createAdminUser('daemon');
        $this->basicUser = $this->createBasicUser('tully');
        $this->inactiveUser = $this->createBasicInactiveUser('ned');
    }

    /**
     * @dataProvider usersCanListUsersDataProvider
     */
    public function testUserCanListUsers(
        string $userProperty,
        int $expectedStatus,
        ?array $expectedJSONStructure,
    ) {
        $this->be($this->$userProperty);

        $response = $this->getJson(self::BASE_URL);
        $response->assertStatus($expectedStatus);

        if ($expectedJSONStructure) {
            $response->assertJsonStructure($expectedJSONStructure);
        }
    }

    public function usersCanListUsersDataProvider(): array
    {
        return [
            'Admin User' => [
                'user' => 'adminUser',
                'expectedStatus' => Response::HTTP_OK,
                'expectedJsonStructure' => self::USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            'Basic user' => [
                'user' => 'basicUser',
                'expectedStatus' => Response::HTTP_OK,
                'expectedJsonStructure' => self::USER_LIST_SUCCESSFUL_RESPONSE_STRUCTURE,
            ],
            'Inactive user' => [
                'user' => 'inactiveUser',
                'expectedStatus' => Response::HTTP_FORBIDDEN,
                'expectedJsonStructure' => null,
            ],
        ];
    }
}
