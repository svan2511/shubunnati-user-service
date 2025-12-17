<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponse;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        try {
            
            $users = $this->userService->getAllUsers();
            $result = [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page'    => $users->lastPage(),
                        'per_page'     => $users->perPage(),
                        'total'        => $users->total(),
                    ]
                    ];

            return $this->successResponse(
                data: $result,
                message: 'Users retrieved successfully',
                status: 200,
                success:true
            );
            
        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to retrieve users.',
                status: 500,
                success:false
            );
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'roles'    => 'required|array|min:1',
                'roles.*'  => 'exists:roles,id',
            ]);
           
            $user = $this->userService->createUser($request->only('name', 'email', 'password','roles'));

            return $this->successResponse(
                data: ['user' => $user],
                message: 'User created successfully',
                status: 201
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation failed');
        } catch (Exception $e) {
            Log::info('ERROR---'.$e->getMessage());
            return $this->errorResponse(
                message: 'Failed to create user.',
                status: 500
            );
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        try {
            $user = $this->userService->getUserById($user->id);

            return $this->successResponse(
                data: ['user' => $user],
                message: 'User retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'User not found.',
                status: 404
            );
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|min:8',
            ]);

            $updatedUser = $this->userService->updateUser($user, $request->only('name', 'email', 'password','roles'));

            return $this->successResponse(
                data: ['user' => $updatedUser],
                message: 'User updated successfully'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation failed');
        } catch (Exception $e) {
            Log::info('ERROR----'. $e->getMessage());
            return $this->errorResponse(
                message: 'Failed to update user.',
                status: 500
            );
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        try {
            
            $this->userService->deleteUser($user);

            return $this->successResponse(
                message: 'User deleted successfully',
                status: 200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to delete user.',
                status: 500
            );
        }
    }

    /**
     * Login user and issue token
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $result = $this->userService->login($request->only('email', 'password'));
            if(count($result) > 0 ) {
                return $this->successResponse(
                data: $result,
                message: 'Login successful!',
                success:true
            );
            }else {
                 return $this->errorResponse(
                message: 'The provided credentials are incorrect or invalid.',
                status: 200,
                success:false
            );
            }
           
        } catch (ValidationException $e) {
            // This catches both form validation and credential errors from service
            return $this->errorResponse(
                message: 'The provided credentials are incorrect or invalid.',
                status: 401,
                errors: $e->errors(),
                success:false
            );
        } catch (Exception $e) {
            Log::info('ERROR:123 --' .  $e->getMessage());
            return $this->errorResponse(
                message: 'Login failed. Please try again later.',
                status: 500,
                success:false
            );
        }
    }

    /**
     * Logout authenticated user
     */
    public function logout(Request $request)
{
    try {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Logout failed'
        ], 500);
    }
}


    /**
     * Get authenticated user profile
     */
    public function user(Request $request)
    {
        try {
            $user = $this->userService->getAuthenticatedUser($request);

            return $this->successResponse(
                data: ['user' => $user],
                message: 'User profile retrieved successfully',
               success:true
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to retrieve user profile.',
                success:false,
                status: 500
            );
        }
    }
}