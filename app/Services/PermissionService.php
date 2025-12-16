<?php

namespace App\Services;

use App\Repositories\PermissionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PermissionService
{
    public function __construct(
        protected PermissionRepository $permissionRepo
    ) {}

    public function list(): LengthAwarePaginator
    {
        $permissions = $this->permissionRepo->all();
         $permissions->getCollection()->transform(function ($permission) {
            return [
                'id'    => $permission->id,
                'name'  => $permission->name,
                'desc' => $permission->desc,
                'module' => $permission->module,
            
            ];
            });
        return $permissions;
    }

    public function create(array $data)
    {
        
        $data['guard_name'] = $data['guard_name'] ?? 'api';
        $data['name'] = Str::slug($data['name']);
        return $this->permissionRepo->create($data);
    }

    public function get(int $id)
    {
        return $this->permissionRepo->find($id);
    }

    public function update(int $id, array $data)
    {
        $data['name'] = Str::slug($data['name']);
        return $this->permissionRepo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->permissionRepo->delete($id);
    }

    public function getGroupedPermissions() {
        return $this->permissionRepo->groupedPermissions();
    }
}
