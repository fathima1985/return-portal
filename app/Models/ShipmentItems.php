<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItems extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id','product_id','product_title','line_id','product_sku','line_price','total_tax','total','attributes','product_thumb','return_reason','hygiene_seal','is_opened','return_type','note','quantity'];
}
