<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use App\Models\Shipments;
use App\Models\ShipmentDetails;
use Session;
use App\Models\PortalLogs;

class ClientController extends Controller
{
    public function index(request $request){			
		$payload = @file_get_contents('php://input');
		$status  = 0;
		if(!is_array($payload)){
			$data       = preg_split("/\r\n|\n|\r/", $payload);	
			$response   = json_decode($payload,true);	
		}

		$post_request = $request->input();
		$payment_status = '';

		$txn_id        = isset($post_request['transactionid']) ? $post_request['transactionid'] : 0;
		$details       = new ShipmentDetails();
		$shipments     = new Shipments();

		if($txn_id != ''){
			$_shipment = $shipments->where('payment_id',$txn_id)->first();
			if(!empty($_shipment)){				 
				$_details = $details->where('shipment_id',$_shipment->id)->first();	
				if(isset($post_request['type']) && $post_request['type'] == 'redirect'){					 
					$payment_status = 'payment-complete';	
					$status 	= 1;
					//$_shipment->status = 1;
					$_shipment->save();
				}elseif(isset($post_request['type']) && $post_request['type'] == 'cancel'){					 
					$payment_status = 'payment-canceled';	
				}				 
				if($payment_status != ''){					
					$_details->payment_status_details = $payment_status;
					$_details->txn_id = $txn_id;
					$_details->payment_status = $status;					
					$_details->save();

					$logs = array(  'source_id' => $shipmentId,
                            		'type'      => '2',
                            		'note'      => 'Multisafe Payment completed with '.$_shipment->id);
					$log = new PortalLogs();
					$res = $log->create($logs); 
				}
			}

			
		}
		//if($status == 1){
			//return Redirect::to('/confirm/create-label/'.$_shipment->id.'?status='.$status);	
			return Redirect::to('/return-complete/'.$_shipment->id.'/'.$txn_id.'?status='.$status);	
		//}
	}
}
