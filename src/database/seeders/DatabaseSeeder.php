<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // admin user with all access
        User::factory()->create([
            'username' => 'brisklimbs',
            'email' => 'brisklimbs@local.com',
            'level' => 1,
            'status' => 'active',
            'password' => Hash::make(env('DEFAULT_USER_PASSWORD')),
        ]);

        // normal user with frontend access only
        User::factory()->create([
            'username' => 'snow',
            'email' => 'snow@local.com',
            'level' => 5,
            'status' => 'active',
            'password' => Hash::make(env('DEFAULT_USER_PASSWORD')),
        ]);

        User::factory()->count(10)->create();
        Category::factory()->count(10)->create();
        Video::factory()->count(10)->create();

        $this->call([
            TermsSeeder::class,
        ]);
    }
}
