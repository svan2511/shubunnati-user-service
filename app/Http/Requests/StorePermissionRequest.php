<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => 'required|string|unique:permissions,name',
            'desc'       => 'nullable|string',
            'module'     => 'required|string'
        ];
    }
}
