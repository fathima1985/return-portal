<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipments extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','order_email','order_site','order_date','lang','expected_pickup','shipment_status','status','payment_id','site_code'];
}
