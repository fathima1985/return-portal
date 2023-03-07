<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentLabels extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id','TrackingCode','label_pdf','TrackingLink','is_sent','is_link','label_info'];
}
