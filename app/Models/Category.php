<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    use Uuid;

    /** 
     *  @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    protected $date = ['deleted_at'];
}