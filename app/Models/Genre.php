<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, Uuid;

    /** 
     * @var array
     */
    protected $fillable = [
        'name',
        'is_active'
    ];

    /** 
     * @var array
     */
    protected $casts = ['id' => 'string'];

    /** 
     * @var array
     */
    protected $dates = ['deleted_at'];
}
