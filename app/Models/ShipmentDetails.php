<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDetails extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id','shiping_method','shiping_price','currency','payment_method','payment_status','payment_status_details','txn_id','customer_details','mail_sent'];
}
