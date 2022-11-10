<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use App\Models\Video;
use Symfony\Component\HttpFoundation\Response;

class CommentValidationService extends ValidationService
{
    public function validateCanCreate(User $user, Video $video): bool {
        if (!$user->isActive() || !$video->allow_comments || !config('settings.allow_comments')) {
            $this->addError(__('comment.failed.store.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanUpdate(User $user, Video $video, Comment $comment): bool {
        if (!$this->canAccessComment($user, $video, $comment)) {
            $this->addError(__('comment.failed.update.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    public function validateCanDelete(User $user, Video $video, Comment $comment): bool {
        if (!$this->canAccessComment($user, $video, $comment)) {
            $this->addError(__('comment.failed.delete.permissions'));
            $this->setStatus(Response::HTTP_FORBIDDEN);

            return false;
        }

        return true;
    }

    private function canAccessComment(User $user, Video $video, Comment $comment): bool {
        return $user->isActive()
            && $comment->user_id === $user->getAuthIdentifier()
            && $comment->video_id === $video->id;
    }

    public function validateCreateRequest(array $input): ?array {
        $rules = [
            'content' => ['required', 'string', 'min:5', 'max:3000'],
        ];

        return $this->validateRules($input, $rules);
    }

    public function validateUpdateRequest(array $input): ?array {
        $rules = [
            'content' => ['required', 'string', 'min:5', 'max:3000'],
        ];

        return $this->validateRules($input, $rules);
    }
}
