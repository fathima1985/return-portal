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

                        $logs = array(  'source_id' => $_shipment->id,
                                        'type'      => '3',
                                        'note'      => 'Return label created with '.$shiping_method.' Tracking  Code :'.$ship_data['TrackingCode']);
                        $log = new PortalLogs();
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
        $labelInfo  = $data['label_info'];
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
