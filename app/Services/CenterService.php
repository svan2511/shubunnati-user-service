<?php

namespace App\Services;

use App\Repositories\CenterRepository;

class CenterService
{
    public function __construct(
        private CenterRepository $repo
    ) {}

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $center = $this->repo->find($id);
        return $center ? $this->repo->update($center, $data) : false;
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
