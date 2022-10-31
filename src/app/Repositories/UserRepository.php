<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function get(int $videoId): ?User
    {
        return (new User())->where('id', $videoId)->first();
    }

    public function create(array $input): User
    {
        return User::create([
            'username' => $input['username'],
            'email' => $input['email'],
            'status' => User::ACTIVE_STATE,
            'level' => User::DEFAULT_LEVEL,
            'password' => Hash::make($input['password']),
        ]);
    }
}
