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

    public function update(User $user, array $fieldValuePairs): bool {
        return $user->update($fieldValuePairs);
    }

    public function updateById(int $userId, array $fieldValuePairs): bool {
        return (new User())->where('id', $userId)->update($fieldValuePairs);
    }

    public function activate(User $user): User {
        $user->status = User::ACTIVE_STATE;
        $user->save();

        return $user;
    }

    public function deactivate(User $user): User {
        $user->status = User::INACTIVE_STATE;
        $user->save();

        return $user;
    }
}
