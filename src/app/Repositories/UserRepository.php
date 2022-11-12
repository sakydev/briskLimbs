<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function get(int $userId): ?User {
        return (new User())->where('id', $userId)->first();
    }

    public function list(array $parameters, int $page, int $limit): Collection {
        $skip = ($page * $limit) - $limit;

        $users = new User();
        foreach ($parameters as $name => $value) {
            $users = $users->where($name, $value);
        }

        return $users->skip($skip)->take($limit)->orderBy('id', 'DESC')->get();
    }

    public function create(array $input): User {
        return User::create([
            'username' => $input['username'],
            'email' => $input['email'],
            'status' => User::ACTIVE_STATE,
            'level' => $input['level'] ?? User::DEFAULT_LEVEL,
            'password' => Hash::make($input['password']),
        ]);
    }

    public function update(User $user, array $fieldValuePairs): User {
        foreach ($fieldValuePairs as $field => $value) {
            $user->$field = $value;
        }

        $user->save();

        return $user;
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
