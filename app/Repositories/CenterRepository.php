<?php

namespace App\Repositories;

use App\Models\Center;

class CenterRepository
{
    public function find($id)
    {
        return Center::find($id);
    }

    public function create(array $data)
    {
        return Center::create($data);
    }

    public function update(Center $center, array $data)
    {
        return $center->update($data);
    }

    public function delete($id)
    {
        $center = Center::find($id);
        return $center?->delete();
    }
}
