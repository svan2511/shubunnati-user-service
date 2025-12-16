<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function all(): LengthAwarePaginator
    {
        return User::paginate(10);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}