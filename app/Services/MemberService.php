<?php

namespace App\Services;

use App\Repositories\MemberRepository;

class MemberService
{
    public function __construct(protected MemberRepository $repo) {}

    public function createMember(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateMember($id, array $data)
    {
        $member = $this->repo->findOrFail($id);
        return $this->repo->update($member, $data);
    }

    public function deleteMember($id)
    {
        return $this->repo->delete($id);
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function findOrFail($id)
    {
        return $this->repo->findOrFail($id);
    }

    public function uploadImage($image)
    {
        return $this->repo->uplaodAndSaveImage($image);
    }

    public function deleteImage($id)
    {
        return $this->repo->deleteImage($id);
    }
}
