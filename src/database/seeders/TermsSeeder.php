<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class TermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::create([
            'title' => 'Terms and policy',
            'slug' => 'terms',
            'content' => '<h1>Sample Headingx</h1><p>Update this page to include your own terms</p>',
        ]);
    }
}
