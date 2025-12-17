<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /* ---------------- USERS ---------------- */

    public function getAllUsers(): LengthAwarePaginator
    {
         $users =  $this->userRepository->all();
        // Transform users collection
    $users->getCollection()->transform(function ($user) {
        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
          
        ];
    });

    return $users;
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->create($data);

        // OPTIONAL: assign roles during user creation
        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user->load('roles');
    }

    public function getUserById(int $id): User
    {
        return $this->userRepository
            ->findOrFail($id)
            ->load('roles');
    }

    public function updateUser(User $user, array $data): User
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }

        // if (isset($data['password'])) {
        //     $updateData['password'] = Hash::make($data['password']);
        // }

        $this->userRepository->update($user, $updateData);
       
        // Sync roles if provided
        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user->refresh()->load('roles');
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    /* ---------------- AUTH ---------------- */

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [];
        }

        $token = $user->createToken('auth_token')->accessToken;

      return [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,

            // ✅ clean RBAC data
            'roles' => $user->getRoleNames(), // collection of strings
            'permissions' => $user
                ->getAllPermissions()
                ->pluck('name')
                ->unique()
                ->values(),
        ],
        'access_token' => $token,
        'token_type' => 'Bearer',
    ];
    }

    public function logout(User $user): void
    {
        $user->token()->revoke();
    }

    public function getAuthenticatedUser($request): array
    {
         return [
        'user_id' => $request->user()->id,
        'roles' => $request->user()->roles->pluck('name'),
        'permissions' => $request->user()->permissions->pluck('name'),
         ];
    }

    /* ---------------- ROLE MANAGEMENT ---------------- */

    public function syncUserRoles(int $userId, array $roles): User
    {
        return DB::transaction(function () use ($userId, $roles) {
            $user = $this->userRepository->findOrFail($userId);
            $user->syncRoles($roles);
            return $user->load('roles');
        });
    }

    public function removeUserRole(int $userId, int|string $role): User
    {
        $user = $this->userRepository->findOrFail($userId);
        $user->removeRole($role);
        return $user->load('roles');
    }

    public function getUserRoles(int $userId): User
    {
        return $this->userRepository
            ->findOrFail($userId)
            ->load('roles');
    }
}
