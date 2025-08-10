<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflows extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function scans()
    {
        return $this->hasMany(Scans::class, 'workflow_id', 'id');
    }
}
