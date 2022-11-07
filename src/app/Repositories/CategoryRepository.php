<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function get(int $categoryId): ?Category {
        return (new Category())->where('id', $categoryId)->first();
    }

    public function list(array $parameters, int $page, int $limit): Collection {
        $skip = ($page * $limit) - $limit;

        $categories = new Category();
        foreach ($parameters as $name => $value) {
            $categories = $categories->where($name, $value);
        }

        return $categories->skip($skip)->take($limit)->orderBy('id', 'DESC')->get();
    }

    public function create(array $input): Category
    {
        return Category::create([
            'name' => $input['name'],
            'description' => $input['description'] ?? '',
            'state' => Category::STATE_PUBLISHED,
        ]);
    }

    public function update(Category $page, $fields): Category {
        foreach ($fields as $name => $value) {
            $page->$name = $value;
        }

        $page->save();

        return $page;
    }

    public function delete(Category $page): bool {
        return $page->delete();
    }

    public function publish(Category $page): Category {
        $page->state = Category::STATE_PUBLISHED;
        $page->save();

        return $page;
    }

    public function unpublish(Category $page): Category {
        $page->state = Category::STATE_UNPUBLISHED;
        $page->save();

        return $page;
    }
}
