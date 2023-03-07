<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalLogs extends Model
{
    use HasFactory;
    protected $fillable = ['source_id','type','note','modified_by'];
}
