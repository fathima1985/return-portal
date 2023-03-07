<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentAddress extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id','collection_date','name','street','house_no','city','country','phone_no','extension','note','post_code'];
}
