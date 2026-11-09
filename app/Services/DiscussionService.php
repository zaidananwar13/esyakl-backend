<?php

namespace App\Services;

use App\Models\ClassDiscussion;
use App\Models\Comment;
use App\Models\DiscussionView;
use App\Models\Kelas;
use App\Models\LikePost;
use App\Models\User;

class DiscussionService
{
    public function getClasses(): array
    {
        return Kelas::select('id_class as id', 'title as class')->get()->toArray();
    }

    public function markAsViewed(int $userId, int $discussionId): bool
    {
        return (bool) DiscussionView::updateOrCreate(
            ['id_user' => $userId, 'id_discussion' => $discussionId],
            ['id_user' => $userId, 'id_discussion' => $discussionId]
        );
    }

    public function getDiscussionsByClass(int $classId, int $userId): array
    {
        $discussions = ClassDiscussion::where('id_class', $classId)->get();
        $result = [];

        foreach ($discussions as $discussion) {
            $result[] = $this->formatDiscussion($discussion, $userId);
        }

        return $result;
    }

    public function createDiscussion(int $userId, int $classId, string $title, string $content): array
    {
        $created = ClassDiscussion::create([
            'owner' => $userId,
            'id_class' => $classId,
            'title' => $title,
            'content' => $content,
        ]);

        if ($created) {
            return ['success' => true, 'message' => 'Discussion Created!'];
        }

        return ['success' => false, 'code' => 401, 'message' => 'Opps, something went wrong!'];
    }

    public function addComment(int $userId, string $commented, int $postId, string $content): array
    {
        $created = Comment::create([
            'owner' => $userId,
            'commented' => $commented,
            'id_commented' => $postId,
            'content' => $content,
        ]);

        if ($created) {
            return ['success' => true, 'message' => 'Comment Added!'];
        }

        return ['success' => false, 'code' => 500, 'message' => 'Oops, something went wrong!'];
    }

    public function likePost(int $userId, string $postType, int $postId, string $status): array
    {
        $updated = LikePost::updateOrCreate(
            ['likeable' => $postType, 'id_likeable' => $postId, 'owner' => $userId],
            ['likeable' => $postType, 'id_likeable' => $postId, 'owner' => $userId, 'type' => $status]
        );

        if ($updated) {
            return ['success' => true, 'message' => 'Like Success!'];
        }

        return ['success' => false, 'code' => 500, 'message' => 'Oops, something went wrong!'];
    }

    public function deleteComment(int $userId, int $commentId): array
    {
        $comment = Comment::where('id_comment', $commentId)
            ->where('owner', $userId)
            ->first();

        if (!$comment) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden: You can only delete your own comments'
            ];
        }

        if ($comment->delete()) {
            return ['success' => true, 'message' => 'Comment Deleted!'];
        }

        return ['success' => false, 'code' => 500, 'message' => 'Oops, something went wrong!'];
    }

    private function formatDiscussion(ClassDiscussion $discussion, int $userId): array
    {
        $user = User::where('id_user', $discussion->owner)->first();
        $views = DiscussionView::where('id_discussion', $discussion->id_class_discussion)->count();
        $viewed = DiscussionView::where('id_discussion', $discussion->id_class_discussion)
            ->where('id_user', $userId)
            ->exists();

        $likeData = $this->getLikeData('class_discussion', $discussion->id_class_discussion, $userId);
        $comments = $this->getDiscussionComments($discussion->id_class_discussion, $userId);

        return [
            'user' => [
                'name' => $user->name,
                'img' => $user->avatar_original,
                'time' => $discussion->created_at,
                'is_teacher' => $user->type === 'teacher',
            ],
            'id_discuss' => $discussion->id_class_discussion,
            'title' => $discussion->title,
            'content' => $discussion->content,
            'views' => $views,
            'viewed' => $viewed,
            'like' => $likeData['like'],
            'dislike' => $likeData['dislike'],
            'liked' => $likeData['liked'],
            'unliked' => $likeData['unliked'],
            'answer_count' => count($comments),
            'answers' => $comments,
        ];
    }

    private function getDiscussionComments(int $discussionId, int $userId): array
    {
        $comments = Comment::where('commented', 'class_discussion')
            ->where('id_commented', $discussionId)
            ->get();

        $result = [];
        foreach ($comments as $comment) {
            $user = User::where('id_user', $comment->owner)->first();
            $likeData = $this->getLikeData('comment', $comment->id_comment, $userId);

            $result[] = [
                'user' => [
                    'name' => $user->name,
                    'img' => $user->avatar_original,
                    // ISO-8601 string like the Laravel 8 API (a raw timestamp breaks the
                    // frontend's date rendering).
                    'time' => $comment->created_at instanceof \DateTimeInterface
                        ? \Illuminate\Support\Carbon::instance($comment->created_at)->toJSON()
                        : \Illuminate\Support\Carbon::createFromTimestamp((int) $comment->created_at)->toJSON(),
                    'is_teacher' => $user->type === 'teacher',
                ],
                'id_comment' => $comment->id_comment,
                'content' => $comment->content,
                'commented' => false,
                'like' => $likeData['like'],
                'dislike' => $likeData['dislike'],
                'liked' => $likeData['liked'],
                'unliked' => $likeData['unliked'],
            ];
        }

        return $result;
    }

    private function getLikeData(string $type, int $id, int $userId): array
    {
        $posts = LikePost::where('likeable', $type)
            ->where('id_likeable', $id)
            ->get();

        $like = 0;
        $dislike = 0;
        $liked = false;
        $unliked = false;

        foreach ($posts as $post) {
            if ($post->type === 'like') {
                $like++;
                if ($post->owner === $userId) {
                    $liked = true;
                }
            } elseif ($post->type === 'dislike') {
                $dislike++;
                if ($post->owner === $userId) {
                    $unliked = true;
                }
            }
        }

        return compact('like', 'dislike', 'liked', 'unliked');
    }
}
