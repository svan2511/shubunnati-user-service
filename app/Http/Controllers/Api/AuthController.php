<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authService;

    public function __construct(UserService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $result = $this->authService->register($request->only('name', 'email', 'password'));

            return $this->successResponse(
                data: $result,
                message: 'User created successfully!',
                status: 201
            );
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed. Please try again.', 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->login($request->only('email', 'password'));

            return $this->successResponse(
                data: $result,
                message: 'Login successful!',
                status: 200
            );
        } catch (ValidationException $e) {
            return $this->errorResponse('The provided credentials are incorrect.', 401, $e->errors());
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return $this->successResponse(
            message: 'Successfully logged out'
        );
    }

    public function user(Request $request)
    {
        $user = $this->authService->getAuthenticatedUser($request->user());

        return $this->successResponse(
            data: $user,
            message: 'Authenticated user retrieved successfully'
        );
    }
}