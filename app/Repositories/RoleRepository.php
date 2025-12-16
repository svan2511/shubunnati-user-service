<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository
{
    public function all(): LengthAwarePaginator
    {
         return Role::where('guard_name', 'api')
        ->with('permissions:id,name')
        ->paginate(10);
    }

    public function allRoles() {
        return Role::select('id','name' ,'desc')->get();
    }

    public function find(int $id): Role
    {
        return Role::findOrFail($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->find($id);
        $role->update($data);

        return $role;
    }

    public function delete(int $id): bool
    {
        return Role::findOrFail($id)->delete();
    }
}
