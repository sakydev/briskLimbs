<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function get(int $userId): ?User {
        return (new User())->where('id', $userId)->first();
    }

    public function create(array $input): User {
        return User::create([
            'username' => $input['username'],
            'email' => $input['email'],
            'status' => User::ACTIVE_STATE,
            'level' => User::DEFAULT_LEVEL,
            'password' => Hash::make($input['password']),
        ]);
    }

    public function update(int|User $user, array $fieldValuePairs): bool {
        if ($user instanceof User) {
            return $user->update($fieldValuePairs);
        }

        return (new User())->where('id', $user)->update($fieldValuePairs);
    }
}
