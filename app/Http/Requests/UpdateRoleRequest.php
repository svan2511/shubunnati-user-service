<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => 'required|string|unique:roles,name,' . $this->id,
            'desc'       => 'nullable|string',
            'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id'
        ];
    }
}
