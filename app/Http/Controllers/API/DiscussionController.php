<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DiscussionService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscussionController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Discussion';

    private DiscussionService $discussionService;

    public function __construct(DiscussionService $discussionService)
    {
        $this->discussionService = $discussionService;
    }

    public function classes()
    {
        $classes = $this->discussionService->getClasses();
        return $this->successResponse(self::API_NAME, $classes, 'Success!');
    }

    /**
     * Mark a discussion as viewed.
     * SECURITY FIX: Added null check for user
     */
    public function viewDiscuss(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        // SECURITY FIX: Check if user exists
        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        $discussionId = $request->input('discussion');
        if (!is_numeric($discussionId)) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid discussion ID']);
        }

        $success = $this->discussionService->markAsViewed(
            $user->id_user,
            (int) $discussionId
        );

        if ($success) {
            return $this->successResponse(self::API_NAME, null, 'Success!');
        }

        return $this->serverErrorResponse(self::API_NAME, 'Failed to mark discussion as viewed');
    }

    /**
     * Get discussions for a class.
     * SECURITY FIX: Added null check for user
     */
    public function discuss(Request $request, $id)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        // SECURITY FIX: Check if user exists
        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        if (!is_numeric($id)) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid class ID']);
        }

        $discussions = $this->discussionService->getDiscussionsByClass((int) $id, $user->id_user);

        return $this->successResponse(self::API_NAME, $discussions, 'Success!');
    }

    /**
     * Create a new discussion.
     * SECURITY FIX: Added null check and input validation
     */
    public function createDiscussion(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        // SECURITY FIX: Check if user exists
        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'class' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(self::API_NAME, $validator->errors()->all());
        }

        $result = $this->discussionService->createDiscussion(
            $user->id_user,
            (int) $request->input('class'),
            $request->input('title'),
            $request->input('content')
        );

        if ($result['success']) {
            return $this->successResponse(self::API_NAME, null, $result['message']);
        }

        return $this->errorResponse(self::API_NAME, $result['message'], $result['code']);
    }

    /**
     * Add a comment to a discussion.
     * SECURITY FIX: Added null check and input validation
     */
    public function commentDiscussion(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        // SECURITY FIX: Check if user exists
        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            // Values the frontend sends in production (see comment.commented in the DB).
            'commented' => 'required|string|in:class_discussion,comment,project_student,blog',
            'post_id' => 'required|integer',
            'content' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(self::API_NAME, $validator->errors()->all());
        }

        $result = $this->discussionService->addComment(
            $user->id_user,
            $request->input('commented'),
            (int) $request->input('post_id'),
            $request->input('content')
        );

        if ($result['success']) {
            return $this->successResponse(self::API_NAME, null, $result['message']);
        }

        return $this->serverErrorResponse(self::API_NAME, $result['message']);
    }

    /**
     * Like or dislike a post.
     * SECURITY FIX: Added null check and input validation
     */
    public function likePost(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        // SECURITY FIX: Check if user exists
        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            // Values the frontend sends in production (see like_dislike in the DB);
            // 'neutral' is how the frontend removes a like/dislike.
            'postType' => 'required|string|in:class_discussion,comment,blog',
            'post_id' => 'required|integer',
            'status' => 'required|string|in:like,dislike,neutral',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(self::API_NAME, $validator->errors()->all());
        }

        $result = $this->discussionService->likePost(
            $user->id_user,
            $request->input('postType'),
            (int) $request->input('post_id'),
            $request->input('status')
        );

        if ($result['success']) {
            return $this->successResponse(self::API_NAME, null, $result['message']);
        }

        return $this->serverErrorResponse(self::API_NAME, $result['message']);
    }

    /**
     * Delete a comment.
     */
    public function deleteCommentDiscussion(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        $commentId = $request->input('id_comment');
        if (!is_numeric($commentId)) {
            return $this->validationErrorResponse(self::API_NAME, ['Invalid comment ID']);
        }

        $result = $this->discussionService->deleteComment(
            $user->id_user,
            (int) $commentId
        );

        if ($result['success']) {
            return $this->successResponse(self::API_NAME, null, $result['message']);
        }

        return $this->errorResponse(self::API_NAME, $result['message'], $result['code']);
    }
}
