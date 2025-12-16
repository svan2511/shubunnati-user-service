<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => 'required|string|unique:permissions,name,' . $this->id,
            'desc'       => 'nullable|string',
            'module'     => 'required|string'
        ];
    }
}
