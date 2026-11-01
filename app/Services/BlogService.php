<?php

namespace App\Services;

use App\Helpers\MediaHelper;
use App\Models\Blog;
use App\Models\BlogLike;
use App\Models\Comment;
use App\Models\LikePost;
use App\Models\User;
use Illuminate\Support\Carbon;

class BlogService
{
    public function getAllBlogs(?int $userId = null): array
    {
        $blogs = Blog::all();
        $result = [];

        foreach ($blogs as $blog) {
            $result[] = $this->formatBlog($blog, $userId);
        }

        return $result;
    }

    public function getBlogById(int $blogId, ?int $userId = null): ?array
    {
        $blog = Blog::where('id_blog', $blogId)->first();

        if (!$blog) {
            return null;
        }

        $data = $this->formatBlog($blog, $userId);
        $data['comments'] = $this->getBlogComments($blogId, $userId);

        return $data;
    }

    public function likeBlog(int $blogId, int $userId): array
    {
        $existing = BlogLike::where('id_blog', $blogId)
            ->where('id_user', $userId)
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'code' => 406,
                'message' => 'Not acceptable'
            ];
        }

        $like = new BlogLike();
        $like->id_user = $userId;
        $like->id_blog = $blogId;
        $like->save();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Success'
        ];
    }

    public function getCategories(): array
    {
        return Blog::select('category')->distinct()->get()->toArray();
    }

    private function formatBlog(Blog $blog, ?int $userId = null): array
    {
        $likesCount = LikePost::where('likeable', 'blog')
            ->where('type', 'like')
            ->where('id_likeable', $blog->id_blog)
            ->count();

        $liked = false;
        if ($userId) {
            $liked = LikePost::where('id_likeable', $blog->id_blog)
                ->where('likeable', 'blog')
                ->where('type', 'like')
                ->where('owner', $userId)
                ->exists();
        }

        return [
            'id_blog' => $blog->id_blog,
            'title' => $blog->title,
            'content' => $blog->content,
            'category' => $blog->category,
            'image' => MediaHelper::getMediaUrl($blog->image),
            'likes' => $likesCount,
            'liked' => $liked,
            'posted_at' => Carbon::createFromTimestamp(Carbon::parse($blog->created_at)->format('U'))->format('d M Y'),
        ];
    }

    private function getBlogComments(int $blogId, ?int $userId = null): array
    {
        $comments = Comment::select('id_comment as id', 'owner', 'content', 'created_at as time')
            ->where('commented', 'blog')
            ->where('id_commented', $blogId)
            ->get();

        $result = [];
        foreach ($comments as $comment) {
            $user = User::where('id_user', $comment->owner)->first();

            $result[] = [
                'user' => [
                    'name' => $user->name,
                    'img' => $user->avatar_original,
                    'time' => Carbon::createFromTimestamp($comment->time)->format('Y F d | H:m:s'),
                ],
                'id_comment' => $comment->id,
                'is_commented' => $userId ? ($user->id_user == $userId) : false,
                'content' => $comment->content,
            ];
        }

        return $result;
    }
}
