<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Profile';

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function userProfile(Request $request)
    {
        $token = $request->input('api_token');

        if (!$token) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        $result = $this->userService->getUserProfile($token);

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            return $this->notFoundResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, $result['data'], $result['message']);
    }

    public function userProfileUpdate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'language' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = [];
            foreach ($validator->getMessageBag()->toArray() as $msg) {
                $messages[] = $msg[0];
            }
            // Legacy contract: validator errors were HTTP 200 {status: 200, message: [...]}.
            return $this->legacyResponse(self::API_NAME, ['status' => 200, 'message' => $messages], 200);
        }

        $data = [
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'about' => $request->input('about'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'country' => $request->input('country'),
            'language' => $request->input('language'),
            'profile' => $request->input('profile'),
        ];

        $image = $request->file('profile');

        $result = $this->userService->updateUserProfile($request->input('api_token'), $data, $image);

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            if ($result['code'] === 409) {
                return $this->conflictResponse(self::API_NAME, $result['message']);
            }
            return $this->serverErrorResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, null, 'Ok. Update data successfull');
    }

    public function userPasswordUpdate(Request $request)
    {
        $input = $request->all();

        if (strlen($request->input('password')) < 6 || strlen($request->input('password_confirm')) < 6) {
            return $this->legacyResponse(self::API_NAME, ['code' => 422, 'status' => 422, 'message' => 'Invalid Input. Please check password length!'], 422);
        }

        $rules = [
            'password' => 'required',
            'password_confirm' => 'required',
        ];

        if ($request->input('old_password') !== null) {
            $rules['old_password'] = 'required';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $messages = [];
            foreach ($validator->getMessageBag()->toArray() as $msg) {
                $messages[] = $msg[0];
            }
            // Legacy contract: validator errors were HTTP 200 {status: 200, message: [...]}.
            return $this->legacyResponse(self::API_NAME, ['status' => 200, 'message' => $messages], 200);
        }

        $result = $this->userService->updatePassword(
            $request->input('api_token'),
            $request->input('password'),
            $request->input('password_confirm'),
            $request->input('old_password')
        );

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            // Legacy contract: a wrong current password was HTTP 500 with this exact message.
            if ($result['message'] === 'Current password is incorrect') {
                return $this->legacyResponse(self::API_NAME, ['code' => 500, 'message' => 'Sorry, you may type the wrong password.'], 500);
            }
            if ($result['code'] === 422) {
                return $this->legacyResponse(self::API_NAME, ['code' => 422, 'status' => 422, 'message' => $result['message']], 422);
            }
            return $this->serverErrorResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, null, 'Ok. Update password successfull');
    }

    public function userCourseUpdate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'status' => 'required',
            'class' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = [];
            foreach ($validator->getMessageBag()->toArray() as $msg) {
                $messages[] = $msg[0];
            }
            // Legacy contract: validator errors were HTTP 200 {status: 200, message: [...]}.
            return $this->legacyResponse(self::API_NAME, ['status' => 200, 'message' => $messages], 200);
        }

        $result = $this->userService->updateCourseStatus(
            $request->input('api_token'),
            (int) $request->input('class'),
            $request->input('status')
        );

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            return $this->serverErrorResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, null, 'Ok. Update course successfull');
    }

    public function generateForgotMail(Request $request)
    {
        $result = $this->userService->generateForgotPasswordMail($request->input('email'));

        if (!$result['success']) {
            return $this->notFoundResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, null, $result['message']);
    }

    public function newPassword(Request $request, $token)
    {
        $result = $this->userService->setNewPassword($token, $request->input('password'));

        if (!$result['success']) {
            return $this->unauthorizedResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, null, $result['message']);
    }
}
