<?php

namespace App\Repositories;

use App\Models\Page;

class PageRepository
{
    public function getBySlug(string $slug): ?Page
    {
        return (new Page())->where('slug', $slug)->first();
    }

    public function create(array $input): Page
    {
        return Page::create([
            'title' => $input['title'],
            'content' => $input['content'],
        ]);
    }
}
