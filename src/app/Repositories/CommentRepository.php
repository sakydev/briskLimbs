<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentRepository
{
    public function list(array $parameters, int $page, int $limit): LengthAwarePaginator {
        $comments = new Comment();
        foreach ($parameters as $name => $value) {
            $comments = $comments->where($name, $value);
        }

        return $comments->orderBy('id', 'DESC')->paginate($limit, $page, $page);
    }

    public function create(array $input, int $userId, int $videoId): Comment {
        $comment = new Comment();

        $comment->user_id = $userId;
        $comment->video_id = $videoId;
        $comment->content = $input['content'];

        $comment->save();

        return $comment;
    }
}
