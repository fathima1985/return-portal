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

/*
use Rawilk\Ups\Entity\Shipment\Shipment;
use Rawilk\Ups\Entity\Shipment\ReturnService;
use Rawilk\Ups\Entity\Shipment\Shipper;
use Rawilk\Ups\Entity\Shipment\ShipTo;
use Rawilk\Ups\Entity\Shipment\ShipFrom;
use Rawilk\Ups\Entity\Payment\PaymentInformation;
use Rawilk\Ups\Entity\Shipment\Package;
use Rawilk\Ups\Entity\Shipment\PackagingType;
use Rawilk\Ups\Entity\Shipment\ReferenceNumber;
use Rawilk\Ups\Entity\Shipment\PackageWeight;
use Rawilk\Ups\Entity\Address\Address;
use Rawilk\Ups\Apis\Shipping\ShipConfirm;
use Rawilk\Ups\Entity\Shipment\Label\LabelSpecification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Rawilk\Ups\Entity\Payment\RateInformation;
use Rawilk\Ups\Entity\Payment\Charge;*/

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
use Ups\Shipping;


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
            $ship_data = $ShipmentLabels->where('shipment_id',$_shipment->id)->first();    
			
            if(empty($ship_data)){
                switch($shiping_method){
                    case 'gls':
                        $ship_data = $this->getGLSLabels($details_shipment,$_shipment);
                        break;
                    case 'ups':
                        $ship_data = $this->getUpsGBLabels($details_shipment,$_shipment);
                        break;  
                    case 'homerr':
                        $ship_data = $this->getHomerLabels($details_shipment,$_shipment);
                        break;  
                    case 'ppl':
                        //$ship_data = $this->getUpsLabels($details_shipment,$_shipment);
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
               }
            }     
            
            $ship_data['html'] = $this->GenerateHtmlResponse($ship_data,$shiping_method);            
            echo json_encode($ship_data);
            die; 
        }
    }

    public function GenerateHtmlResponse($data,$method){
        $labelInfo  = $data['label_info'];
        $content    = '';
        switch($method):
            case 'gls':
                $content .= '<div class=""><h2>Return Request has been placed Shipping will contact.</h2></div>';
                break;
            case 'ups':
                $content .= '<div class=""><h2>Return Request has been placed Shipping will contact.</h2></div>';
                break;  
            case 'homerr':
                $content .= '<div class="result-container"><img src="'.$data['label_pdf'].'" alt="'.$data['TrackingCode'].'"/><span>'.$data['TrackingCode'].'</span></div>';
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

       /* $consumer = array(  "firstName"             =>  $customer->first_name,
                            "lastName"              =>  $customer->last_name,
                            "phoneNumber"           =>  $customer->phone,
                            "email"                 =>  $shipment->order_email,
                            "streetName"            =>  $customer->address_1,
                            "houseNumber"           =>  $customer->address_2,
                            "houseNumberAddition"   =>  '',
                            "zipCode"               =>  $customer->postcode,
                            "city"                  =>  $customer->city,
                            "country"               =>  $customer->country);

        $webshop = array(  "externalId"             =>  $customer->first_name,
                            "name"                  =>  "BTA Online",                                                        
                            "streetName"            =>  "Eerste Zeine",
                            "houseNumber"           =>  "142",
                            "houseNumberAddition"   =>  '',
                            "zipCode"               =>  "5144AM",
                            "city"                  =>  "Waalwijk",
                            "country"               =>  "NL"); 

        $homerParams    = array("orderNumber"       => $shipment->order_id,
                                "dropOffHomerrId"   => 0,
                                "sendEmailNotificationToConsumer"   => false,
                                "webshop"           => $webshop,
                                "consumer"          => $consumer
                            ); */
         
        $response   = Http::withHeaders([
                                'Content-Type'  => 'application/json',
                                'Cache-Control' => 'no-cache',                                
                                'X-Api-Key' => $this->HomerrApiKey
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

    public function getGLSLabels($details_shipment = null,$shipment = null){
        $shipmentId = $shipment->id;
        $customer  = json_decode($details_shipment->customer_details);

       
        $address = new ShipmentAddress();  
        $address_item = $address->where('shipment_id',$shipmentId)->first(); 

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
        foreach($Line_Items as $key => $line_itm){
           
            $units[] = array("unitId"               => $line_itm->product_sku,
                            "unitType"              =>  "CO",
                            "customerUnitReference" =>  $line_itm->line_id,
                            "weight"                =>  "0.2",
                            "additionalInfo1"       =>  "",
                            "additionalInfo2"       =>  "");

        }  

        $gls_params     = array("trackingLinkType"      => 'S',
                                "pickupDate"            => $address_item->collection_date,
                                "addresses"             => $gls_address,
                                "customerNo"            => '51430014',
                                "customerSubjectName"   => "",
                                "reference"             => $shipment->order_id, 
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
		$customer = json_decode($details_shipment->customer_details);	
		
		$shipperNumber 	= \config('ups.shipper_number');
		$accessKey 		= \config('ups.access_key');
		$userId 		= \config('ups.user_id');
		$password 		= \config('ups.password');
		
		$shipment = new \Ups\Entity\Shipment;
		
		$shipper = $shipment->getShipper();
		$shipper->setShipperNumber($shipperNumber);		
		$shipper->setName('Deluxerie');
		$shipper->setAttentionName('BTA ONLINE');
		$shipperAddress = $shipper->getAddress();
		$shipperAddress->setAddressLine1('Eerste Zeine 142');
		$shipperAddress->setPostalCode('5144AM');
		$shipperAddress->setCity('Waalwijk');
		$shipperAddress->setStateProvinceCode(''); // required in US
		$shipperAddress->setCountryCode('NL');
		$shipper->setAddress($shipperAddress);
		$shipper->setEmailAddress('operation@deluxerie.com'); 
		$shipper->setPhoneNumber('31627082823');
		$shipment->setShipper($shipper);
		
		// To address
		$address = new \Ups\Entity\Address();
		$address->setAddressLine1('Eerste Zeine 142');
		$address->setPostalCode('5144AM');
		$address->setCity('Waalwijk');
		$address->setStateProvinceCode('');  // Required in US
		$address->setCountryCode('NL');
		$shipTo = new \Ups\Entity\ShipTo();
		$shipTo->setAddress($address);
		$shipTo->setCompanyName('BTA ONLINE');
		$shipTo->setAttentionName('Deluxerie');
		$shipTo->setEmailAddress('operation@deluxerie.com'); 
		$shipTo->setPhoneNumber('31627082823');
		$shipment->setShipTo($shipTo);
			
		// From address
		$address = new \Ups\Entity\Address();
		$address->setAddressLine1($customer->address_1);
		$address->setPostalCode($customer->postcode);
		$address->setCity($customer->city);
		$address->setStateProvinceCode('');  
		$address->setCountryCode($customer->country);
		$shipFrom = new \Ups\Entity\ShipFrom();
		$shipFrom->setAddress($address);
		$shipFrom->setName($customer->first_name.' '.$customer->last_name);
		$shipFrom->setAttentionName($shipFrom->getName());
		$shipFrom->setCompanyName($shipFrom->getName());
		$shipFrom->setEmailAddress($customer->email);
		$shipFrom->setPhoneNumber($customer->phone);
		$shipment->setShipFrom($shipFrom);
		
		// Sold to
		$address = new \Ups\Entity\Address();
		$address->setAddressLine1($customer->address_1);
		$address->setPostalCode($customer->postcode);
		$address->setCity($customer->city);
		$address->setStateProvinceCode('');  
		$address->setCountryCode($customer->country);		
		$soldTo = new \Ups\Entity\SoldTo;
		$soldTo->setAddress($address);
		$soldTo->setAttentionName($customer->first_name.' '.$customer->last_name);
		$soldTo->setCompanyName($soldTo->getAttentionName());
		$soldTo->setEmailAddress($customer->email);
		$soldTo->setPhoneNumber($customer->phone);
		$shipment->setSoldTo($soldTo);
		
		// Set service
		$service = new \Ups\Entity\Service;
		$service->setCode(\Ups\Entity\Service::S_STANDARD);
		$service->setDescription($service->getName());
		$shipment->setService($service);
		$return = 1;
		// Mark as a return (if return)
		if ($return) {
			$returnService = new \Ups\Entity\ReturnService;
			$returnService->setCode(8);
			$shipment->setReturnService($returnService);
		}
		
		$shipment->setDescription('Return '.$_shipment->order_id);
		
		
		// Add Package
		$package = new \Ups\Entity\Package();
		$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
		$package->getPackageWeight()->setWeight(0.1);
		$unit = new \Ups\Entity\UnitOfMeasurement;
		$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_KGS);
		$package->getPackageWeight()->setUnitOfMeasurement($unit);
		
		// Set Package Service Options
		/*$packageServiceOptions = new \Ups\Entity\PackageServiceOptions();
		$packageServiceOptions->setShipperReleaseIndicator(false);
		$package->setPackageServiceOptions($packageServiceOptions); */
		
		
		// Set dimensions
		/*$dimensions = new \Ups\Entity\Dimensions();
		$dimensions->setHeight(50);
		$dimensions->setWidth(50);
		$dimensions->setLength(50);
		$unit = new \Ups\Entity\UnitOfMeasurement;
		$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_CM);
		$dimensions->setUnitOfMeasurement($unit);
		$package->setDimensions($dimensions); */
		
		// Add descriptions because it is a package
		$package->setDescription('XX');
		
		// Add this package
		$shipment->addPackage($package);
		
		// Set Reference Number
		$referenceNumber = new \Ups\Entity\ReferenceNumber;
		if ($return) {
			$referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_RETURN_AUTHORIZATION_NUMBER);
			$referenceNumber->setValue($_shipment->order_id);
		} else {
			$referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
			$referenceNumber->setValue($_shipment->order_id);
		}
		$shipment->setReferenceNumber($referenceNumber);
		
		
		$shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object) ['AccountNumber' => $shipper->getShipperNumber()]));
		
		// Ask for negotiated rates (optional)
		$rateInformation = new \Ups\Entity\RateInformation;
		$rateInformation->setNegotiatedRatesIndicator(1);
		$shipment->setRateInformation($rateInformation);
		
		try {
			$api = new \Ups\Shipping($accessKey, $userId, $password); 

			$confirm = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $shipment);
		
			echo "confirm<pre>";
			var_dump($confirm); // Confirm holds the digest you need to accept the result
			echo "</pre>";
			
			if ($confirm) {
				$accept = $api->accept($confirm->ShipmentDigest);
				echo "<pre>";
				var_dump($accept); // Accept holds the label and additional information
				echo "<pre>";
			}
		} catch (\Exception $e) {
			echo "<pre>";
			var_dump($e);
			echo "</pre>";
		}
		
	}
	
    public function getUpsLabels($details_shipment = null,$_shipment = 0){       
        
		$customer = json_decode($details_shipment->customer_details);	
		
        $shipperNumber = \config('ups.shipper_number');
		
		//echo $shipperNumber; die; 
		
        $shipment = new Shipment([
            'shipper' => new Shipper([
                'shipper_number' => $shipperNumber,
                'name' => 'BTA ONLINE',
                'address' => new Address([
                    'address_line1' => ' Eerste Zeine 142',
                    //'address_line2' => '',
                    'city' => 'Waalwijk',
                   //'state' => 'CA',
                    'postal_code' => '5144AM',
                    'country_code' => 'NL',
                ]),
            ]),
        
            'ship_to' => new ShipTo([
                'company_name' => 'BTA ONLINE',
                'attention_name' => 'BTA ONLINE',
                'address' => new Address([
                    'address_line1' => ' Eerste Zeine 142',
                    //'address_line2' => '',
                    'city' => 'Waalwijk',
                   //'state' => 'CA',
                    'postal_code' => '5144AM',
                    'country_code' => 'NL',
                ]),
            ]),
        
           'ship_from' => new ShipFrom([
                'company_name' => $customer->first_name,
                'attention_name' => $customer->first_name.' '.$customer->last_name,
                'address' => new Address([
                    'address_line1' => $customer->address_1,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'postal_code' => $customer->postcode,
                    'country_code' => $customer->country,
                ]),
            ]),
        
            'description' => 'Return Shipment for Order '.$_shipment->order_id,
			'payment_information' => PaymentInformation::prepaidForAccount($shipperNumber),
			'return_service' => new ReturnService, //::ELECTRONIC_RETURN_LABEL,
			// defaults to ReturnService::PRINT_RETURN_LABEL for the 'code'
            'packages' => [
                new Package([
                    'packaging_type' => new PackagingType, // Default: Customer supplied package
                    'description' => 'Package description', // Required for return shipments
                    'reference_number' => new ReferenceNumber([
                        'value' => $_shipment->order_id,
                        // 'barcode' => true, // Uncomment to have outputted as barcode on bottom of label
                    ]),
                    'package_weight' => new PackageWeight([
                        'weight' => '0.100', // UOM defaults to LBS
                    ]),
                    //'is_large_package' => false,
                ]),
            ],
			
        ]);
		
		//  
        
           
		   
        
       if (\config('ups.negotiated_rates')) {
            $shipment->rate_information = new RateInformation([
                'negotiated_rates' => true,
            ]);
        }



        try {
            $response = (new ShipConfirm)
		//		->withoutAddressValidation()
                ->withShipment($shipment)
                ->withLabelSpecification(LabelSpecification::asGIF()) // omit if you don't need the label
                ->getDigest();
        } catch (\Rawilk\Ups\Exceptions\RequestFailed $e) {
			 dd($e);
       }
        
        // Get the new shipment's identification number
        $response->shipment_identification_number;
        
        // Get the shipment digest
        $shipmentDigest = $response->shipment_digest;



        try {
            $response = (new ShipAccept)
                ->usingShipmentDigest($shipmentDigest)
                ->createShipment();
        } catch (\Rawilk\Ups\Exceptions\RequestFailed $e) {
           
        }
        
        // Get the new shipment's identification number
        $response->shipment_identification_number;
        
        // Returns a collection of packages returned from the api
        // Wrapped in our \Rawilk\Ups\Entity\Shipment\PackageResult entity.
        $package = $response->packages;
        
        // Each package has a tracking number
        // The first package's tracking number should match the shipment identification number.
        $response->packages->first()->tracking_number;
		
		
		/*ANY CHARGES*/
		$charge = $response->charges->first();

		$charge->monetary_value;

		$charge->description;


        $image = $package->label_image;

        // Base 64 encoded graphic image
        $image->graphic_image;

        // Base 64 encoded html browser image rendering software. This is only returned for GIF image formats.
        $image->html_image;

        // This is only returned if the label link is requested to be returned and only at the first package result.
        echo $image->url;


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
