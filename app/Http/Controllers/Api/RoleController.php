<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RoleController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected RoleService $roleService
    ) {}

    // GET /api/roles
    public function index(Request $request)
    {
        try {

            if($request->page === "ALL") {
                return $this->roleService->getAll();
            }
            $roles = $this->roleService->list();
             $result = [
                    'roles' => $roles->items(),
                    'pagination' => [
                        'current_page' => $roles->currentPage(),
                        'last_page'    => $roles->lastPage(),
                        'per_page'     => $roles->perPage(),
                        'total'        => $roles->total(),
                    ]
                    ];

            return $this->successResponse(
                data: $result,
                message: 'Roles retrieved successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to retrieve roles.',
                status: 500,
                success: false
            );
        }
    }

    // POST /api/roles
    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->roleService->create($request->validated());

            return $this->successResponse(
                data: ['role' => $role ],
                message: 'Role created successfully',
                status: 201,
                success: true
            );

        } catch (Exception $e) {
            Log::info('ERROR$%:---'.$e->getMessage());
            return $this->errorResponse(
                message: 'Failed to create role.',
                status: 500,
                success: false
            );
        }
    }

    // GET /api/roles/{id}
    public function show(int $id)
    {
        try {
            $role = $this->roleService->get($id);

            return $this->successResponse(
                data: ['role' => $role ],
                message: 'Role retrieved successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Role not found.',
                status: 404,
                success: false
            );
        }
    }

    // PUT /api/roles/{id}
    public function update(UpdateRoleRequest $request, int $id)
    {
        try {
            $role = $this->roleService->update($id, $request->validated());

            return $this->successResponse(
                data: ['role' => $role ],
                message: 'Role updated successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to update role.',
                status: 500,
                success: false
            );
        }
    }

    // DELETE /api/roles/{id}
    public function destroy(int $id)
    {
        try {
            $this->roleService->delete($id);

            return $this->successResponse(
                data: null,
                message: 'Role deleted successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to delete role.',
                status: 500,
                success: false
            );
        }
    }
}
