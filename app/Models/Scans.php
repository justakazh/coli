<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scans extends Model
{
    protected $guarded = ['id'];
    

    public function scope()
    {
        return $this->belongsTo(Scopes::class);
    }

    public function workflow()
    {
        return $this->belongsTo(Workflows::class);
    }
}
