<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = ['id'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
