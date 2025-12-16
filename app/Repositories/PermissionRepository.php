<?php

namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PermissionRepository
{
    public function all(): LengthAwarePaginator
    {
        return Permission::paginate(10);
    }

    public function find(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(int $id, array $data): Permission
    {
        $permission = $this->find($id);
        $permission->update($data);

        return $permission;
    }

    public function delete(int $id): bool
    {
        return Permission::findOrFail($id)->delete();
    }

    public function groupedPermissions(){
        return DB::table('permissions')
                ->where('guard_name', 'api')
                ->select('id', 'name', 'module')
                ->orderBy('module')
                ->orderBy('name')
                ->get()
                ->groupBy('module')
                ->map(fn ($items, $module) => [
                    'module' => $module,
                    'permissions' => $items->map(fn ($p) => [
                        'id'    => $p->id,
                        'name'  => $p->name,
                        'label' => ucwords(str_replace('-', ' ', $p->name)),
                    ])->values()
                ])
                ->values();
    }
}
