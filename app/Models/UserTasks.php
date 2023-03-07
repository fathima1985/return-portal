<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipment_id',
        'status',
    ];
}
