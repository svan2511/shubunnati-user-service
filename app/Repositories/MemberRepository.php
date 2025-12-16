<?php

namespace App\Repositories;

use App\Jobs\DeleteMemberImage;
use App\Models\Member;
use Cloudinary\Cloudinary;

class MemberRepository
{
    public function find($id)
    {
        return Member::find($id);
    }

    public function findOrFail($id)
    {
        return Member::findOrFail($id);
    }

    public function create(array $data)
    {
        return Member::create($data);
    }

    public function update(Member $member, array $data)
    {
        return $member->update($data) ? $member : null ;
    }

    public function delete($id)
    {
        $member = Member::find($id);
        DeleteMemberImage::dispatch($member->img_public_id);
        return Member::destroy($id);
    }

    public function uplaodAndSaveImage($path) {
        $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => env('CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ]
      ]);

    $result = $cloudinary->uploadApi()->upload(
        $path,
        ['folder' => 'members']
    );

    return $result;

    ;
    }

     public function deleteImage($publicId) {
         $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => env('CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ]
    ]);
    return $cloudinary->uploadApi()->destroy($publicId);
    }
}
