<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

class PageRepository
{
    public function get(int $pageId): ?Page {
        return (new Page())->where('id', $pageId)->first();
    }

    public function getBySlug(string $slug): ?Page
    {
        return (new Page())->where('slug', $slug)->first();
    }

    public function list(array $parameters, int $page, int $limit): Collection {
        $skip = ($page * $limit) - $limit;

        $pages = new Page();
        foreach ($parameters as $name => $value) {
            $pages = $pages->where($name, $value);
        }

        return $pages->skip($skip)->take($limit)->orderBy('id', 'DESC')->get();
    }

    public function create(array $input): Page
    {
        return Page::create([
            'title' => $input['title'],
            'slug' => $input['slug'],
            'content' => $input['content'],
        ]);
    }

    public function update(Page $page, $fields): Page {
        foreach ($fields as $name => $value) {
            $page->$name = $value;
        }

        $page->save();

        return $page;
    }

    public function delete(Page $page): bool {
        return $page->delete();
    }

    public function publish(Page $page): Page {
        $page->state = PAGE::STATE_PUBLISHED;
        $page->save();

        return $page;
    }

    public function unpublish(Page $page): Page {
        $page->state = Page::STATE_UNPUBLISHED;
        $page->save();

        return $page;
    }
}
