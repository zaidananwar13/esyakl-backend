<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BlogService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Blog';

    private BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index(Request $request, $id = null)
    {
        $userId = null;
        if ($request->input('api_token')) {
            $user = User::where('api_token', $request->input('api_token'))->first();
            $userId = $user?->id_user;
        }

        if ($id === null) {
            $blogs = $this->blogService->getAllBlogs($userId);

            if (count($blogs) > 0) {
                return $this->successResponse(self::API_NAME, $blogs, 'Blogs retrieved successfully');
            }

            return $this->notFoundResponse(self::API_NAME, 'No blogs found');
        }

        // Validate ID is numeric
        if (!is_numeric($id)) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid blog ID']);
        }

        $blog = $this->blogService->getBlogById((int) $id, $userId);

        if (!$blog) {
            return $this->notFoundResponse(self::API_NAME, 'Blog not found');
        }

        return $this->successResponse(self::API_NAME, $blog, 'Blog retrieved successfully');
    }

    public function like(Request $request, $id = null)
    {
        // SECURITY FIX: Removed direct header() call, using Laravel response instead
        $user = User::where('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        if (!is_numeric($id)) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid blog ID']);
        }

        $result = $this->blogService->likeBlog((int) $id, $user->id_user);

        if ($result['success']) {
            return $this->successResponse(self::API_NAME, null, $result['message']);
        }

        return $this->errorResponse(self::API_NAME, $result['message'], $result['code']);
    }

    public function category(Request $request)
    {
        // SECURITY FIX: Removed direct header() call, using Laravel response instead
        $categories = $this->blogService->getCategories();

        if (count($categories) > 0) {
            return $this->successResponse(self::API_NAME, $categories, 'Blog categories found');
        }

        return $this->notFoundResponse(self::API_NAME, 'No categories found');
    }
}
