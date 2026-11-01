<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'User';
    protected const API_NAME_PROFILE = 'Profile';
    protected const API_NAME_MENTOR = 'Mentor';

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->getAllUsers();
    }

    public function register()
    {
        return view('test.register');
    }

    public function registerPost(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
            'repassword' => 'required',
            'agreeTerms' => 'checked'
        ]);

        if ($request->password != $request->repassword) {
            return redirect('api/register')->with('warning', 'Password harus diisi sama');
        }

        $result = $this->userService->registerUser($request->email, $request->password);

        if (!$result['success']) {
            return redirect('api/register')->with('info', $result['message']);
        }

        return redirect('api/register')->with('success', $result['message']);
    }

    public function profile(Request $request)
    {
        $result = $this->userService->getProfile($request->input("api_token"));

        if (!$result['success']) {
            if ($result['code'] === 403) {
                return $this->forbiddenResponse('Quiz', $result['message']);
            }
            return $this->unauthorizedResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, $result['data'], $result['message']);
    }

    public function getMentor()
    {
        $result = $this->userService->getMentors();

        return $this->successResponse(self::API_NAME_MENTOR, $result['data'], $result['message']);
    }

    public function userProfile(Request $request)
    {
        $result = $this->userService->getUserProfile($request->input('api_token'));

        if (!$result['success']) {
            return $this->unauthorizedResponse(self::API_NAME_PROFILE, $result['message']);
        }

        return $this->successResponse(self::API_NAME_PROFILE, $result['data'], $result['message']);
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
            return $this->validationErrorResponse(self::API_NAME_PROFILE, $validator->errors()->all());
        }

        $result = $this->userService->updateUserProfile(
            $request->input('api_token'),
            $input,
            $request->file('profile')
        );

        if (!$result['success']) {
            if ($result['code'] === 409) {
                return $this->conflictResponse(self::API_NAME_PROFILE, $result['message']);
            }
            return $this->serverErrorResponse(self::API_NAME_PROFILE, $result['message']);
        }

        return $this->successResponse(self::API_NAME_PROFILE, null, $result['message']);
    }

    public function userPasswordUpdate(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'password' => 'required',
            'password_confirm' => 'required',
        ]);

        if ($request->input('old_password') !== null) {
            $validator = Validator::make($input, [
                'password' => 'required',
                'password_confirm' => 'required',
                'old_password' => 'required',
            ]);
        }

        if ($validator->fails()) {
            return $this->validationErrorResponse(self::API_NAME_PROFILE, $validator->errors()->all());
        }

        $result = $this->userService->updatePassword(
            $request->input('api_token'),
            $request->input('password'),
            $request->input('password_confirm'),
            $request->input('old_password')
        );

        if (!$result['success']) {
            if ($result['code'] === 422) {
                return $this->errorResponse(self::API_NAME_PROFILE, 422, $result['message']);
            }
            return $this->serverErrorResponse(self::API_NAME_PROFILE, $result['message']);
        }

        return $this->successResponse(self::API_NAME_PROFILE, null, $result['message']);
    }

    public function userCourseUpdate(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required',
            'class' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(self::API_NAME_PROFILE, $validator->errors()->all());
        }

        $result = $this->userService->updateCourseStatus(
            $request->input('api_token'),
            $request->input('class'),
            $request->input('status')
        );

        if (!$result['success']) {
            return $this->serverErrorResponse(self::API_NAME_PROFILE, $result['message']);
        }

        return $this->successResponse(self::API_NAME_PROFILE, null, $result['message']);
    }

    public function editUser()
    {
        return view('user.form');
    }

    public function generateForgotMail(Request $request)
    {
        $result = $this->userService->generateForgotPasswordMail($request->input('email'));

        if (!$result['success']) {
            return $this->notFoundResponse(self::API_NAME_PROFILE, $result['message']);
        }

        return $this->successResponse(self::API_NAME_PROFILE, null, $result['message']);
    }

    public function newPassword(Request $request, $token)
    {
        $result = $this->userService->setNewPassword($token, $request->input('password'));

        if (!$result['success']) {
            return $this->unauthorizedResponse(self::API_NAME_PROFILE, $result['message']);
        }

        return $this->successResponse(self::API_NAME_PROFILE, null, $result['message']);
    }

    public function progress(Request $request)
    {
        $result = $this->userService->getProgress($request->input("api_token"));

        if (!$result['success']) {
            return $this->unauthorizedResponse('Progress', $result['message']);
        }

        return $this->successResponse('Progress', $result['data'], $result['message']);
    }

}
