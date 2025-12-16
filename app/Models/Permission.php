<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    protected $fillable =  ['name','desc','guard_name','module'];

    //  public function module(){
    //     return $this->belongsTo(Module::class,'slug','module');
    // }
}
