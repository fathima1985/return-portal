<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Support\Facades\Http;
use App\Models\ShipmentLabels;
use Illuminate\Http\Request;
use App\Models\Shipments;
use App\Models\ShipmentDetails;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItems;
use Intervention\Image\Facades\Image as Image;
use Ups\Entity\Shipment;
use Ups\Entity\Address;
use Ups\Entity\ShipTo;
use Ups\Entity\ShipFrom;
use Ups\Entity\SoldTo;
use Ups\Entity\Service;
use Ups\Entity\ReturnService;
use Ups\Entity\Package;
use Ups\Entity\UnitOfMeasurement;
use Ups\Entity\PackageServiceOptions;
use Ups\Entity\Dimensions;
use Ups\Entity\ReferenceNumber;
use Ups\Entity\PaymentInformation;
use Ups\Entity\RateInformation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Ups\Shipping;
use Ups\Rate; 
use App\Models\PortalLogs;


class ShipmentLabelsController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $GLS_Endpoint    = 'https://api.gls.nl/V1/api/Pickup/Create?api-version=1.0';
    protected $GLS_UserName    = 'btaonline-api';
    protected $GLS_Password    = 'A5S9Kcge';


    protected $HomerrEndpoint  = 'https://homerr-functions-production.azurewebsites.net/api/v1/return/create-order';
    protected $HomerrApiKey    = '8e18f63e-559a-48a0-b25f-18e231c73a36';

    protected $pactic_Endpoint = 'https://api.allpacka.com​/webservices/webshop.ashx'; 
    protected $pactic_UserName = 'operation@deluxerie.com'; 
    protected $pactic_Password = 'Deluxerie.2020'; 

 
    public function index($order_id)
    {
        
        $shipments      = new Shipments(); 
        $details        = new ShipmentDetails();        
        $_shipment = $shipments->where('order_id',$order_id)->first();
        if(!empty($_shipment)){
            $details_shipment = $details->where('shipment_id',$_shipment->id)->first();
            $this->getUpsLabels($details_shipment);
        }
    }


    public function CreateLabel(request $request ){
        $data = $request->input();
        //$order_id = $data['order_id'];
		
        $_res = array('status'=> 'false','message' => 'invalid');
        $shipments      = new Shipments(); 
        $details        = new ShipmentDetails(); 
        $order_id       = $request->input('order');
        $shipment       = $request->input('shipment');
        $_shipment = $shipments->where('payment_id',$order_id)->where('id',$shipment)->first();
		
        if(!empty($_shipment)){
            $details_shipment = $details->where('shipment_id',$_shipment->id)->first();
            $shiping_method   = $details_shipment->shiping_method;
            $ShipmentLabels = new ShipmentLabels();
            $ship_data = $ShipmentLabels->where('shipment_id',$_shipment->id)->get()->first();    
            if(!$ship_data){
				
				$log = new PortalLogs();			
				$logs = array(  'source_id' => $_shipment->id,
								'type'      => '3',
								'note'      => 'Return label create request placed');                        
			//	$log->create($logs);
				
				
				
                switch($shiping_method){
                    case 'gls':						
                        $ship_data = $this->getGLSLabels($details_shipment,$_shipment);                       
                        break;
                    case 'gls_hu':		                       				
                        $ship_data = $this->pacticLabel($details_shipment,$_shipment);                       
                        break;
                    break;    
                    case 'ups':
                        $ship_data = $this->getUpsGBLabels($details_shipment,$_shipment);
                        break;  
                    case 'homerr':
                        $ship_data = $this->getHomerLabels($details_shipment,$_shipment);
                        break;  
                    case 'ppl':
                        $ship_data = $this->pacticLabel($details_shipment,$_shipment,1);
                        break;         
                    default:
                        break;
                }
                if(!empty($ship_data)){
                    $ShipmentLabels = new ShipmentLabels();
                    $hasLabel = $ShipmentLabels->where('shipment_id',$_shipment->id)->first();
                    if(empty($hasLabel))
                        $ShipmentLabels->create($ship_data);
                    else
                        $hasLabel->update($ship_data);

                        $logs = array(  'source_id' => $_shipment->id,
                                        'type'      => '3',
                                        'note'      => 'Return label created with '.$shiping_method.' Tracking  Code :'.$ship_data['TrackingCode']);
                      //  $log = new PortalLogs();
                        $res = $log->create($logs);      
               }
            }
            
           

            $ship_data['html'] = $this->GenerateHtmlResponse($ship_data,$shiping_method);            
			$ship_data['basename'] = basename($ship_data['TrackingLink']);           
            echo json_encode($ship_data);
            die; 
        }
    }

    public function GenerateHtmlResponse($data,$method){		
        $labelInfo  = isset($data['label_info']) ? $data['label_info'] : array();
        $content    = '';
        switch($method):
            case 'gls':
                $content .= '<div class=""><h2>Return Request has been placed Shipping will contact.</h2></div>';
                break;
            case 'ups':
                $content .= '<div class="result-container"><img src="'.$data['label_pdf'].'" alt="'.$data['TrackingCode'].'"/><span>'.$data['TrackingCode'].'</span></div>';
                break;  
            case 'homerr':
                $content .= '<div class="result-container"><img src="'.$data['label_pdf'].'" alt="'.$data['TrackingCode'].'"/><span>'.$data['TrackingCode'].'</span></div>';
                break; 
            case 'gls_hu':
                $content .= '<div class="result-container">
                <span class="icon" style="position: relative;width: 100%;height: 19rem;text-align: center;"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="256" height="256" viewBox="0 0 256 256" xml:space="preserve">
                <defs>
                </defs>
                <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                    <path d="M 78.806 62.716 V 20.496 c 0 -1.214 -0.473 -2.356 -1.332 -3.216 L 61.526 1.332 C 60.667 0.473 59.525 0 58.31 0 H 15.742 c -2.508 0 -4.548 2.04 -4.548 4.548 V 43.16 v 19.556 C 34.114 65.376 56.665 65.47 78.806 62.716 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(220,223,225); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <path d="M 11.194 62.716 v 11.23 v 11.506 c 0 2.508 2.04 4.548 4.548 4.548 h 58.517 c 2.508 0 4.548 -2.04 4.548 -4.548 V 62.716 H 11.194 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(234,84,64); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <polygon points="60.27,18.41 78.81,36.88 78.73,19.73 " style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(196,203,210); fill-rule: nonzero; opacity: 1;" transform="  matrix(1 0 0 1 0 0) "/>
                    <path d="M 77.474 17.28 L 61.526 1.332 c -0.675 -0.676 -1.529 -1.102 -2.453 -1.258 v 15.382 c 0 2.358 1.919 4.277 4.277 4.277 h 15.382 C 78.576 18.81 78.15 17.956 77.474 17.28 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(171,178,184); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <path d="M 33.092 68.321 h -4.374 c -0.69 0 -1.25 0.56 -1.25 1.25 v 8.091 v 5.541 c 0 0.69 0.56 1.25 1.25 1.25 s 1.25 -0.56 1.25 -1.25 v -4.291 h 3.124 c 2.254 0 4.088 -1.834 4.088 -4.088 v -2.415 C 37.18 70.155 35.346 68.321 33.092 68.321 z M 34.68 74.824 c 0 0.876 -0.712 1.588 -1.588 1.588 h -3.124 v -5.591 h 3.124 c 0.876 0 1.588 0.712 1.588 1.588 V 74.824 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <path d="M 45.351 84.453 H 41.27 c -0.69 0 -1.25 -0.56 -1.25 -1.25 V 69.571 c 0 -0.69 0.56 -1.25 1.25 -1.25 h 4.082 c 2.416 0 4.38 1.965 4.38 4.38 v 7.371 C 49.731 82.488 47.767 84.453 45.351 84.453 z M 42.52 81.953 h 2.832 c 1.037 0 1.88 -0.844 1.88 -1.881 v -7.371 c 0 -1.036 -0.844 -1.88 -1.88 -1.88 H 42.52 V 81.953 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <path d="M 61.282 68.321 H 54.07 c -0.69 0 -1.25 0.56 -1.25 1.25 v 13.632 c 0 0.69 0.56 1.25 1.25 1.25 s 1.25 -0.56 1.25 -1.25 v -5.566 h 3.473 c 0.69 0 1.25 -0.56 1.25 -1.25 s -0.56 -1.25 -1.25 -1.25 H 55.32 v -4.315 h 5.962 c 0.69 0 1.25 -0.56 1.25 -1.25 S 61.973 68.321 61.282 68.321 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <path d="M 60.137 40.012 c -0.154 -0.374 -0.52 -0.617 -0.924 -0.617 h -4.805 V 27.616 c 0 -0.552 -0.447 -1 -1 -1 H 40.592 c -0.552 0 -1 0.448 -1 1 v 11.778 h -4.805 c -0.404 0 -0.769 0.244 -0.924 0.617 c -0.155 0.374 -0.069 0.804 0.217 1.09 l 12.213 12.213 c 0.195 0.195 0.451 0.293 0.707 0.293 s 0.512 -0.098 0.707 -0.293 L 59.92 41.102 C 60.206 40.815 60.292 40.386 60.137 40.012 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(196,203,210); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    <path d="M 58.137 38.012 c -0.154 -0.374 -0.52 -0.617 -0.924 -0.617 h -4.805 V 25.616 c 0 -0.552 -0.447 -1 -1 -1 H 38.592 c -0.552 0 -1 0.448 -1 1 v 11.778 h -4.805 c -0.404 0 -0.769 0.244 -0.924 0.617 c -0.155 0.374 -0.069 0.804 0.217 1.09 l 12.213 12.213 c 0.195 0.195 0.451 0.293 0.707 0.293 s 0.512 -0.098 0.707 -0.293 L 57.92 39.102 C 58.206 38.815 58.292 38.386 58.137 38.012 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(234,84,64); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                </g>
                </svg></span>
                <span>'.$data['TrackingCode'].'</span></div>';
                break;     
            case 'ppl':
                $content .= '<div class=""><h2>Return Request has been placed Shipping will contact.</h2></div>';
                break;         
            default:
                break;
        endswitch;        
        return $content;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function getHomerLabels($details_shipment = null,$shipment = null){
        $shipmentId = $shipment->id;
        $customer  = json_decode($details_shipment->customer_details);

        
       $homerParams = array (   "numberOfParcels"   => "1",
                                "name"              => $customer->first_name,
                                "email"             => $shipment->order_email,
                                "zipCode"           => $customer->postcode,
                                "houseNumber"       => $customer->address_2,
                                "addition"          => "",
                                "street"            => $customer->address_2,
                                "city"              => $customer->city,
                                "countryName"       => $customer->country,
                                "phoneNumber"       => $customer->phone,
                                "orderNumbersList"  => array($shipment->order_id),
                                "homerrId"          => "",
                                "webshopId"         => "163",
                                "returnCode"        => "",
                                "sendEmail"         => "true",
                                "reason"            => "",
                                "item_type"         => "");
      
         
        $response   = Http::withHeaders([
                                'Content-Type'  => 'application/json',
                                'Cache-Control' => 'no-cache'
                        ])->send('POST',$this->HomerrEndpoint,['body' => json_encode($homerParams)]);  
						
		
   
        $res_data = json_decode($response->getBody()->getContents(),true);							
		
		
        if(isset($res_data['qrCode'])){
            $url = config('values.url'); 
            QrCode::generate($res_data['qrCode'], public_path('labelqr/'.$shipment->order_id.'.svg'));
            $response_data['TrackingCode']  = $res_data['qrCode'];
            $response_data['TrackingLink']  =  $url.'/labelqr/'.$shipment->order_id.'.svg';   
            $response_data['label_pdf']     =  $url.'/labelqr/'.$shipment->order_id.'.svg';
            $response_data['is_link']       = 0;   
            $response_data['is_sent']       = 0;   
            $response_data['shipment_id']   = $shipment->id;   
            $response_data['label_info']    = 'Homerr Return'; 
            return $response_data;
        }
        return false;                                               
    }


    public function pacticLabel($details_shipment = null,$shipment = null,$ppl = 0){
        $shipmentId     = $shipment->id;
        $customer       = json_decode($details_shipment->customer_details);
        $address        = new ShipmentAddress();  
        $address_item   = $address->where('shipment_id',$shipmentId)->first();
        $requset = array();

       
        $requset['flDebug'] 	= "false";
		$requset['txEmail'] 	= $this->pactic_UserName;
		$requset['txPassword'] 	= $this->pactic_Password;
		$requset['cdLang'] 		= "EN";
		$requset['flSendWaybill'] = "true";
        
        $idCarrier  = 2;
        $idService  = 1;

        if($ppl){
            $idCarrier  = 35;
            $idService  = 5;
        }

        $reference = $shipment->order_id.'-RR'; 

        $book = array("dtPickup"  => $address_item->collection_date,
					  "idCarrier" => $idCarrier,
					  "idService" => $idService, 
					  "flBookCheapest" => "false");

        $_order = array( "nmLast" => "Returns",
                        "nmFirst" => "Deluxerie",
                        "txPhone" => "+31627082823",
                        "txEmail" => "return@deluxerie.net"
                    );  
                    
        $quote = array( "ORDERER"                   => array(),
                        "tyCOD"						=> "",
                        "nmRecipientCOD"			=> "BTA Online",
                        "nmBankCOD"					=> 'ING',
                        "txBankAccountNumberCOD"	=> 'NL79INGB0391463632',
                        "flNothingProhibited"		=> "true",
                        "flAgreedToTermsAndConditions"	=> "true",
                        "flInsured"	                => "false",
                        "flSendWaybill"             =>  "true",
        );

        $collection	 	= array( "nmCompanyOrPerson" 	=> $address_item->name,
                                "nmContact" 			=> $address_item->name,
                                "txPhoneContact" 		=> $address_item->phone_no,
                                "txEmailContact" 		=> $shipment->order_email,
                                "txAddress" 			=> $address_item->street,
                                "txAddressNumber" 	    => $address_item->house_no,
                                "txPost" 				=> $address_item->post_code,
                                "txCity"				=> $address_item->city,
                                "cdCountry" 			=> $address_item->country,                                
                                "txInstruction" 		=> '',
                                "cdDropOffPoint" 		=> ''
                             );

	   $invoice_emial = 'operation@deluxerie.com';
	   if($ppl){
		   $invoice_emial = 'payments@deluxerie.com';
	   }
	   $invoice  = array("nmCompanyOrPerson" => "BTA Online",
                       "txAddress" 			=> "Eerste Zeine",
                       "txAddressNumber"	=> "142",
                       "txPost"				=> "5144AM",
                       "txCity"				=> "Waalwijk",
                       "cdCountry"			=> "NL",
                       "flCompany"			=> "true",
                       "nmContact"          => "Ece Eker",
                       "txPhoneContact"     => "+31627082823",
                       "txVAT"				=> "NL004275051B52",
                       "txInvoiceEmail"		=> $invoice_emial);    
        

				
		if($ppl){
			$destination	 = array("nmCompanyOrPerson" 	=> 'BTA Online / Deluxerie',
                                "nmContact" 			=> 'Deluxerie Returns',
                                "txPhoneContact" 		=> '+31627082823',
                                "txEmailContact" 		=> 'return@deluxerie.net',
                                "txAddress" 			=> 'Starovice 900',
                                "txAddressNumber" 		=> '.',
                                "txPost" 				=> '65999',
                                "txCity"				=> 'Depo Hustopece',
                                "cdCountry" 			=> 'CZ',
                                "cdProvince" 			=> "",
                                "txInstruction" 		=> "",
                                "cdDropOffPoint" 		=> "");	  

		}else{		
			$destination	 = array("nmCompanyOrPerson" 	=> 'BTA Online / Deluxerie',
									"nmContact" 			=> 'Deluxerie Returns',
									"txPhoneContact" 		=> '+31627082823',
									"txEmailContact" 		=> 'return@deluxerie.net',
									"txAddress" 			=> 'N\u00e1das utca 4',
									"txAddressNumber" 		=> '.',
									"txPost" 				=> '2142',
									"txCity"				=> 'Nagytarcsa',
									"cdCountry" 			=> 'HU',
									"cdProvince" 			=> "",
									"txInstruction" 		=> "",
									"cdDropOffPoint" 		=> "");	  
		}
        $packages = array("tyPackage"	    => "PARCEL",
                            "ctPackage"	    => 1,
                            "cmWidth"		=> 10,
                            "cmHeight"	    => 10,
                            "cmLength"	    => 10,
                            "gWt"			=> 1000,
                            "amContent"	    => 1,
                            "txContent"	    => "Clothing",
                            "idOrder"		=> $reference); 
                            
        $requset['BOOK'] 				                = $book;
        $requset['QUOTE']		 		                = $quote;
        $requset['QUOTE']['ORDERER'] 	                = $_order;
        $requset['QUOTE']['ADDRESSES']['COLLECTION'] 	= $collection;
        $requset['QUOTE']['ADDRESSES']['DESTINATION'] 	= $destination;
        $requset['QUOTE']['ADDRESSES']['INVOICE'] 		= $invoice;
        $requset['QUOTE']['PACKAGES']['PACKAGE'] 		= array($packages);
       // $requset['QUOTE']['EXTRAS'] 		            = $extras;   
        $pactic_data =  array('REQUEST' => $requset);
       // $pacticinfo = $this->getRequest('​​',$pactic_data,'POST');
      

        $response   = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache',                                            
        ])->send('POST',$this->pactic_Endpoint,['body' => json_encode($pactic_data)]);  
	
        $res_data = json_decode($response->getBody()->getContents(),true);	
		
		

        $url = config('values.APP_URL');     
        $response_data = array();                            
        $_messages =  $res_data['Messages'];
        if(!empty($_messages)){	
           $_loginfo =  $_messages[0]['Text'];
        }elseif($response && $res_data['Quotes'] != null){            
            $_Waybills = $response['Quotes'][0]['WayBills'];
            $Waybills  = $_Waybills[0];            
            $Labels	 = $response['Quotes'][0]['Labels'];
            $print_label	= '';
            if(!empty($Labels)){
                foreach($Labels as $key => $_label){
                    $pdf_data = base64_decode($_label);
                    $_name = $shipment->order_id.'-'.time();
                    $_filename = 'deluxerie-label-'.$_name.'.pdf';                   
                    $path = public_path().'/labels/'.$_filename;
                    if(file_exists($path))
                        unlink($path);	
                    $pdf = fopen ($path,'w');
                    fwrite ($pdf,$pdf_data);					
                    fclose ($pdf);
                    $print_label =  $url.'/labels/'.$_filename; 
                    
                    $pdf_path = public_path().'/labels/'.$_filename;
                    $note = "Pactic label created with Waybill No: {$Waybills} and  Label ".$path;                    
                    $label_pdf[] = $print_label;
                    $response_data['TrackingCode']  = $Waybills;
                    $response_data['TrackingLink']  = $print_label;   
                    $response_data['label_pdf']     = $print_label;
                    $response_data['is_link']       = 0;   
                    $response_data['is_sent']       = 0;   
                    $response_data['shipment_id']   = $shipment->id;   
                    $response_data['label_info']    = 'Pactic Pickup';

                    $log = new PortalLogs();			
                    $logs = array(  'source_id' => $shipment->id,
                                    'type'      => '3',
                                    'note'      => $note);                        
                    $log->create($logs);

                }	
            }
        }
        return $response_data;
    }

    public function getGLSLabels($details_shipment = null,$shipment = null){
        $shipmentId = $shipment->id;
        $customer  = json_decode($details_shipment->customer_details);
        $address = new ShipmentAddress();  
        $address_item = $address->where('shipment_id',$shipmentId)->first(); 
        $order_country 	= $address_item->country;
		$country 		= strtolower($order_country);        
        $LineItems = new ShipmentItems();  
        $Line_Items = $LineItems->where('shipment_id',$shipmentId)->get(); 

        
        if(empty($address_item) || empty($Line_Items))
            return false;

        $requesterAddress = array(  'name1'             => 'BTA Online',
                                    'name2'             => 'Deluxerie',
                                    'name3'             => '',
                                    'street'            => 'Eerste Zeine',
                                    'houseNo'           => '142',
                                    'houseNoExt'        => '',
                                    'zipCode'           => '5144AM',
                                    'city'              => 'Waalwijk',
                                    'countryCode'       => 'NL',
                                    'contact'           => 'Deluxerie Returns',
                                    'phone'             => '31627082823',
                                    'email'             => 'operation@deluxerie.com');
        
        $deliveryAddress = array(  'addresseeType'      => 'B',
                                    'name1'             => 'BTA Online',
                                    'name2'             => 'Deluxerie',
                                    'name3'             => '',
                                    'street'            => 'Wijnruitstraat',
                                    'houseNo'           => '4',
                                    'houseNoExt'        => '',
                                    'zipCode'           => '5143AJ',
                                    'city'              => 'Waalwijk',
                                    'countryCode'       => 'NL',
                                    'contact'           => 'Deluxerie Returns',
                                    'phone'             => '31627082823',
                                    'email'             => 'operation@deluxerie.com');        

        $pickupAddress = array(     'name1'             => $address_item->name,
                                    'name2'             => '',
                                    'name3'             => '',
                                    'street'            => $address_item->street,
                                    'houseNo'           => $address_item->house_no,
                                    'houseNoExt'        => $address_item->extension,
                                    'zipCode'           => $address_item->post_code,
                                    'city'              => $address_item->city,
                                    'countryCode'       => $address_item->country,
                                    'contact'           => $address_item->name,
                                    'phone'             => $address_item->phone_no,
                                    'email'             => $shipment->order_email);                            
      
        $gls_address   = array( 'requesterAddress'      => $requesterAddress,
                                'pickupAddress'         => $pickupAddress,
                                'deliveryAddress'       => $deliveryAddress);

        $units          = array();
         //foreach($Line_Items as $key => $line_itm){
           
            $units[] = array("unitId"               => "Deluxerie Return - ".$shipment->order_id,
                            "unitType"              => "CO",
                            "customerUnitReference" => $shipment->order_id."-RR",
                            "weight"                => "0.5",
                            "additionalInfo1"       => "",
                            "additionalInfo2"       => "");

        //}  

        $gls_params     = array("trackingLinkType"      => 'S',
                                "pickupDate"            => $address_item->collection_date,
                                "addresses"             => $gls_address,
                                "customerNo"            => '51430014',
                                "customerSubjectName"   => "",
                                "reference"             => $shipment->order_id."-RR", 
                                "units"                 => $units,  
                                "shippingSystemName"    => "",                                
                                "shippingSystemVersion" => "",
                                "shiptype"              =>  "P",
                                "username"              => $this->GLS_UserName,
                                "password"              => $this->GLS_Password);
                         
        $response = Http::withBody(json_encode($gls_params),'application/json')->post($this->GLS_Endpoint);
        $response_data = array();
		
		
		
				
        if ($response->getStatusCode() == 200) { // 200 OK
            $res_data = json_decode($response->getBody()->getContents(),true); 

			
            $response_data['TrackingCode']  = $res_data['units'][0]['unitNo'];
            $response_data['TrackingLink']  = $res_data['shipmentTrackingLink'];   
            $response_data['label_pdf']     = $res_data['shipmentTrackingLink'];
            $response_data['is_link']       = 1;   
            $response_data['is_sent']       = 0;   
            $response_data['shipment_id']   = $shipment->id;   
            $response_data['label_info']    = 'GLS Pickup';   
        }
        return $response_data;               
    }
	
	public function getUpsGBLabels($details_shipment = null,$_shipment = 0){
		$customer		= json_decode($details_shipment->customer_details);
		$shipperNumber 	= \config('ups.shipper_number');
		$accessKey 		= \config('ups.access_key');
		$userId 		= \config('ups.user_id');
		$password 		= \config('ups.password');
	
		$xmlRequest1 = '<?xml version="1.0"?>
								<AccessRequest xml:lang="en-NL">
									<AccessLicenseNumber>'.$accessKey.'</AccessLicenseNumber>
									<UserId>'.$userId.'</UserId>
									<Password>'.$password.'</Password>
								</AccessRequest>
								<ShipmentConfirmRequest xml:lang="en-NL">
									<Request>
										<TransactionReference>
											<CustomerContext>Deluxerie RP</CustomerContext>
											<XpciVersion/>
										</TransactionReference>
										<RequestAction>ShipConfirm</RequestAction>
										<RequestOption>validate</RequestOption>
									</Request>									
									<Shipment>										
										<Description/>
										<Shipper>
											<Name>BTA Online</Name>
											<AttentionName>Deluxerie Returns</AttentionName>
											<CompanyDisplayableName>BTA Online</CompanyDisplayableName>
											<PhoneNumber>31627082823</PhoneNumber>
											<ShipperNumber>E7442R</ShipperNumber>
											<TaxIdentificationNumber>NL004275051B52</TaxIdentificationNumber>
											<Address>
												<AddressLine1>Eerste Zeine 142</AddressLine1>
												<City>Waalwijk</City>
												<StateProvinceCode></StateProvinceCode>
												<PostalCode>5144AM</PostalCode>
												<CountryCode>NL</CountryCode>
											</Address>
										</Shipper>
										<ShipTo>
											<CompanyName>BTA Online</CompanyName>
											<AttentionName>Deluxerie Returns</AttentionName>
											<PhoneNumber>31627082823</PhoneNumber>
											<Address>
												<AddressLine1>Wijnruitstraat 4</AddressLine1>
												<City>Waaliwjk</City>
												<StateProvinceCode></StateProvinceCode>
												<PostalCode>5143AJ</PostalCode>
												<CountryCode>NL</CountryCode>
											</Address>
										</ShipTo>
										<ShipFrom>
											<CompanyName>'.$customer->first_name.'</CompanyName>
											<AttentionName>'.$customer->first_name.' '.$customer->last_name.'</AttentionName>
											<PhoneNumber>'.$customer->phone.'</PhoneNumber>
											<Address>
												<AddressLine1>'.$customer->address_1.'</AddressLine1>
												<City>'.$customer->city.'</City>
												<StateProvinceCode></StateProvinceCode>
												<PostalCode>'.$customer->postcode.'</PostalCode>
												<CountryCode>'.$customer->country.'</CountryCode>
											</Address>
										</ShipFrom>										
										<PaymentInformation>
											<Prepaid>
												<BillShipper>
													<AccountNumber>'.$shipperNumber.'</AccountNumber>
												</BillShipper>
											</Prepaid>
										</PaymentInformation>
										<Service>
											<Code>11</Code>
											<Description>UPS Standard</Description>
										</Service>
										<Package>
											<PackagingType>
												<Code>02</Code>
												<Description>Customer Supplied</Description>
											</PackagingType>
											<Description>Package Description</Description>
											<ReferenceNumber>
												<Code>00</Code>
												<Value>'.$_shipment->order_id.'</Value>
											</ReferenceNumber>
											<PackageWeight>
												<UnitOfMeasurement/>
												<Weight>0.50</Weight>
											</PackageWeight>											
											<AdditionalHandling>0</AdditionalHandling>
										</Package>
										<ReturnService>
											<Code>8</Code>
										</ReturnService>
										<ShipmentServiceOptions>
											<LabelDelivery>
												<EmailAddress>'.$customer->email.'</EmailAddress>
												<FromEmailAddress>return@deluxerie.net</FromEmailAddress>
											</LabelDelivery>
										</ShipmentServiceOptions>
									</Shipment>	
									<LabelSpecification>
										<LabelPrintMethod>
											<Code>GIF</Code>
											<Description>GIF</Description>
										</LabelPrintMethod>
										<LabelImageFormat>
											<Code>GIF</Code>
											<Description>GIF</Description>
										</LabelImageFormat>
									</LabelSpecification>								
								</ShipmentConfirmRequest>';
					
						
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "https://onlinetools.ups.com/ups.app/xml/ShipConfirm");
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
						curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
						$xmlResponse = curl_exec ($ch); // SHIP CONFORMATION RESPONSE
						//echo curl_errno($ch);

						$xml = $xmlResponse;

						preg_match_all( "/\<ShipmentConfirmResponse\>(.*?)\<\/ShipmentConfirmResponse\>/s",
						$xml, $bookblocks );

						foreach( $bookblocks[1] as $block )
						{
							preg_match_all( "/\<ShipmentDigest\>(.*?)\<\/ShipmentDigest\>/",$block, $author ); 							
						}

		if(!isset($author[1][0]) || $author[1][0] == '')				
			return false;

		$xmlRequest2='<?xml version="1.0" encoding="ISO-8859-1"?>
						<AccessRequest xml:lang="en-NL">
						<AccessLicenseNumber>9DCC5018EEA74961</AccessLicenseNumber>
						<UserId>BTAONLINE</UserId>
						<Password>Deluxerie.2020</Password>
						</AccessRequest>						
						<ShipmentAcceptRequest>
						<Request>
						<TransactionReference>
						<CustomerContext>Customer Comment</CustomerContext>
						</TransactionReference>
						<RequestAction>ShipAccept</RequestAction>
						<RequestOption>1</RequestOption>
						</Request>
						<ShipmentDigest>'.$author[1][0].'</ShipmentDigest>
						</ShipmentAcceptRequest>';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onlinetools.ups.com/ups.app/xml/ShipAccept");		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
		$xmlResponse = curl_exec ($ch); // SHIP ACCEPT RESPONSE
		$xml = $xmlResponse;
		preg_match_all( "/\<ShipmentAcceptResponse\>(.*?)\<\/ShipmentAcceptResponse\>/s",
		$xml, $bookblocks );
		foreach( $bookblocks[1] as $block )
		{
			preg_match_all( "/\<GraphicImage\>(.*?)\<\/GraphicImage\>/",
			$block, $author ); // GET LABEL

			preg_match_all( "/\<TrackingNumber\>(.*?)\<\/TrackingNumber\>/",
			$block, $tracking ); // GET TRACKING NUMBER		
		}		
		$tracking_key = $tracking[1][0];
		
		$LabelRecovery = 'https://onlinetools.ups.com/ups.app/xml/LabelRecovery';

		$recoverLabel = '<?xml version="1.0" encoding="UTF-8"?>
						<AccessRequest xml:lang="en-US">
						<AccessLicenseNumber>'.$accessKey.'</AccessLicenseNumber>
						<UserId>'.$userId.'</UserId>
						<Password>'.$password.'</Password>
						</AccessRequest>
						<?xml version="1.0" encoding="UTF-8"?>
						<LabelRecoveryRequest>
						<Request>
						<TransactionReference>
						<CustomerContext>Your Customer Context</CustomerContext>
						</TransactionReference>
						<RequestAction>LabelRecovery</RequestAction>
						</Request>
						<LabelSpecification>
						<LabelImageFormat>
						<Code>GIF</Code>
						</LabelImageFormat>
						</LabelSpecification>
						<LabelDelivery>
						<LabelLinkIndicator/>
						</LabelDelivery>
						<TrackingNumber>'.$tracking_key.'</TrackingNumber>
						</LabelRecoveryRequest>';
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $LabelRecovery);

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $recoverLabel);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
			$xmlResponse = curl_exec ($ch); 
			//echo $xmlResponse;
			preg_match_all( "/\<LabelRecoveryResponse\>(.*?)\<\/LabelRecoveryResponse\>/s",
			$xmlResponse, $bookblocks );
			foreach( $bookblocks[1] as $block )
			{
				preg_match_all( "/\<GraphicImage\>(.*?)\<\/GraphicImage\>/",
				$block, $labelImage ); // GET LABEL
			}	
			$label_file = 'ups-label-'.$_shipment->order_id.'.gif';			
			$path = public_path().'/labels/'.$label_file;

		$img  = Image::make($labelImage[1][0])->save($path); 	
		$url = config('values.APP_URL'); 
		$response_data['TrackingCode']  = $tracking_key;
		$response_data['TrackingLink']  = $url.'/labels/'.$label_file;   
		$response_data['label_pdf']     = $url.'/labels/'.$label_file;
		$response_data['is_link']       = 0;   
		$response_data['is_sent']       = 0;   
		$response_data['shipment_id']   = $_shipment->id;   
		$response_data['label_info']    = 'UPS Return'; 
		return $response_data;
    }

                    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShipmentLabels  $shipmentLabels
     * @return \Illuminate\Http\Response
     */
    public function show(ShipmentLabels $shipmentLabels)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShipmentLabels  $shipmentLabels
     * @return \Illuminate\Http\Response
     */
    public function edit(ShipmentLabels $shipmentLabels)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShipmentLabels  $shipmentLabels
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShipmentLabels $shipmentLabels)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShipmentLabels  $shipmentLabels
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShipmentLabels $shipmentLabels)
    {
        //
    }
}
