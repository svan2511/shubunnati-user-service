<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Services\PermissionService;
use Exception;

class PermissionController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected PermissionService $permissionService
    ) {}


    // GET /api/permissions
    public function index()
    {
        try {
            $permissions = $this->permissionService->list();
            $result = [
                    'permissions' => $permissions->items(),
                    'pagination' => [
                        'current_page' => $permissions->currentPage(),
                        'last_page'    => $permissions->lastPage(),
                        'per_page'     => $permissions->perPage(),
                        'total'        => $permissions->total(),
                    ]
                    ];

            return $this->successResponse(
                data: $result,
                message: 'Permissions retrieved successfully',
                status: 200,
                success:true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to retrieve permissions.',
                status: 500,
                success: false
            );
        }
    }

    // POST /api/permissions
    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = $this->permissionService->create($request->validated());

            return $this->successResponse(
                data: ["permission" => $permission],
                message: 'Permission created successfully',
                status: 201,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to create permission.',
                status: 500,
                success: false
            );
        }
    }

    // GET /api/permissions/{id}
    public function show(int $id)
    {
        try {
            $permission = $this->permissionService->get($id);

            return $this->successResponse(
                data: ['permission' => $permission ],
                message: 'Permission retrieved successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Permission not found.',
                status: 404,
                success: false
            );
        }
    }

    // PUT /api/permissions/{id}
    public function update(UpdatePermissionRequest $request, int $id)
    {
        try {
            $permission = $this->permissionService->update($id, $request->validated());

            return $this->successResponse(
                data: ['permission' => $permission ],
                message: 'Permission updated successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to update permission.',
                status: 500,
                success: false
            );
        }
    }

    // DELETE /api/permissions/{id}
    public function destroy(int $id)
    {
        try {
            $this->permissionService->delete($id);

            return $this->successResponse(
                data: null,
                message: 'Permission deleted successfully',
                status: 200,
                success: true
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Failed to delete permission.',
                status: 500,
                success: false
            );
        }
    }

     public function getGroupedPermissions() {
        $groupedPermissions = $this->permissionService->getGroupedPermissions();
         return $this->successResponse(
                data: ['permissions' => $groupedPermissions ],
                message: 'Permissions reterive successfully',
                status: 200,
                success: true
            );
    }
}
