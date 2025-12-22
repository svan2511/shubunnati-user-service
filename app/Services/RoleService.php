<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct(
        protected RoleRepository $roleRepo
    ) {}

    public function list(): LengthAwarePaginator
    {
        $roles = $this->roleRepo->all();

    $roles->getCollection()->transform(function ($role) {
        return [
            'id'   => $role->id,
            'name' => $role->name,
            'desc' => $role->desc,
            'permissions' => $role->permissions->map(fn ($p) => [
                'id'    => $p->id,
                'name'  => $p->name
            ])->values(),
        ];
    });

    return $roles;
    }

    public function getAll() {
       return $this->roleRepo->allRoles();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $data['guard_name'] = $data['guard_name'] ?? 'api';

            $role = $this->roleRepo->create($data);

            // 🔥 Assign permissions
            $role->syncPermissions($permissions);

            return $role->load('permissions');
        });
    }

    public function get(int $id)
    {
        return $this->roleRepo->find($id)->load('permissions');
    }

   public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $role = $this->roleRepo->update($id, $data);
           
            // 🔥 Sync permissions
            $role->syncPermissions($permissions);
             foreach($role->users as $user) {
                 $user->increment('token_version');
             }

            return $role->load('permissions');
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->roleRepo->delete($id);
        });
    }
}
