<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Session;
use MultiSafepay\Sdk;
use MultiSafepay\ValueObject\Customer\Country;
use MultiSafepay\ValueObject\Customer\Address;
use MultiSafepay\ValueObject\Customer\PhoneNumber;
use MultiSafepay\ValueObject\Customer\EmailAddress;
use MultiSafepay\ValueObject\Money;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\CustomerDetails;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\PluginDetails;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\PaymentOptions;
use MultiSafepay\Api\Transactions\OrderRequest;
use App\Http\Controllers\HomeController;
use App\Models\Shipments;
use App\Models\ShipmentDetails;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItems;
use App\Models\ShipmentLabels;
use Illuminate\Support\Facades\Redirect;
use App\Models\PortalLogs;

class PaymentController extends Controller
{
    protected $MultiSafepayApi  = 'cf1846cc7b03ca1a20e74b98935dec293b467f9b';    
    protected $MultiSafeTest    = 'e463bdcb9afaf32bd3afbc13946a3a2a7306e576';
    protected $Pay_Env          = 'live'; //test,live
    
    public function index(request $request){
        $_data = $request->input();   
        $payment_method = $request->input('pay_method'); 
        $shipmentinfo 	= json_decode(Session::get('return_data'),true);
        $order_id       = $shipmentinfo['order_id'];   
        $shipmentinfo['payment_method'] = $payment_method;
        $payment_id     = $order_id.'-'.time();
        $shipmentinfo['payment_id']     = $payment_id;
       
        $shipment_id    = $this->buildObject($shipmentinfo);
        
        if($payment_method == 'online-payment'){
            $this->CompletePayment($payment_id,$shipment_id);
        }else{                      
            header('Location: '.'/return-complete/'.$shipment_id.'/'.$payment_id);
		    die();
        }
    }

    public function CompleteOwn(){
        $shipmentinfo 	= json_decode(Session::get('return_data'),true);
        $order_id       = $shipmentinfo['order_id'];   
        $shipmentinfo['payment_method'] = 'own';
        $payment_id     = $order_id.'-'.time();
        $shipmentinfo['payment_id']     = $payment_id;
        $shipment_id    = $this->buildObject($shipmentinfo);
       
        return array('order_id' => $payment_id,'shipment_id' => $shipment_id);
    }

    public function ProcessPayment(request $request){
        $_data = $request->input();      
        $shipmentinfo 	= json_decode(Session::get('return_data'),true);          
        Session::put('return_data', json_encode($formData));

    }

    public function CompletePayment($order_id,$shipment_id){
        
        if(!$shipment_id || !$order_id)
            return false;

        $shipments      = new Shipments(); 
        $details        = new ShipmentDetails();
        $shipment_items = new ShipmentItems();  
        $address        = new ShipmentAddress();  

        $_shipment = $shipments->where('id',$shipment_id)->where('payment_id',$order_id)->first();
        
        if(!empty($_shipment)){
            $details_shipment   = $details->where('shipment_id',$shipment_id)->first();
            $price              = $details_shipment->shiping_price * 100;            
            $pay_key            = $this->MultiSafepayApi;
            $is_production      = true;
            if($this->Pay_Env == 'test'){
                $pay_key            = $this->MultiSafeTest;
                $is_production      = false;
            }
            
            $multiSafepaySdk    = new \MultiSafepay\Sdk($pay_key, $is_production);

            $amount             = new Money($price, $details_shipment->currency);
            $address            = json_decode($details_shipment->customer_details);
            $description        = 'Payment For order #'.$order_id;

            $country            = ($address->country != '') ? $address->country : 'NL';
            $customer_address = (new Address())
                            ->addStreetName($address->address_1)
                            ->addStreetNameAdditional($address->address_2)
                           // ->addHouseNumber('39')
                            ->addZipCode($address->postcode)
                            ->addCity($address->city)
                            ->addState($address->state)
                            ->addCountry(new Country($country)); 
							
			$country_code 	= strtolower($country);												
			
			$langSetup 		= array("nl"=>"nl_NL",
								    "be"=>"nl_BE",
								    "de"=>"de_DE",
								    "at"=>"de_AT",
								    "fr_be"=>"fr_BE",
								    "fr"=>"fr_FR",
								    "da"=>"da_DK",
								    "es"=>"es_ES",
								    "it"=>"it_IT",
								    "sv"=>"sv_SE",
								    "fi"=>"fi_FI",
								    "pt"=>"pt_PT",								    
								    "cs"=>"cs_CZ",
								    "hu"=>"en_HU",
								    "ro"=>"en_RO");
							   
            $locale   =  isset($langSetup[$country_code]) ? $langSetup[$country_code] : 'nl_NL';

            $customer = (new CustomerDetails())
                            ->addFirstName($address->first_name)
                            ->addLastName($address->last_name)
                            ->addAddress($customer_address)
                            ->addEmailAddress(new EmailAddress($_shipment->order_email))
                            ->addPhoneNumber(new PhoneNumber($address->phone))
                            ->addLocale($locale); 
            
            $pluginDetails = (new PluginDetails)
                            ->addApplicationName('Return Portal')
                            ->addApplicationVersion('0.0.1')
                            ->addPluginVersion('1.1.0');    
               
            $url = \config('values.url');  
				
            $paymentOptions = (new PaymentOptions())
                            ->addNotificationUrl($url.'/client/notification?type=notification')
                            ->addRedirectUrl($url.'/client/notification?type=redirect')
                            ->addCancelUrl($url.'/return-summary?type=cancel')
                            ->addCloseWindow(true); 
											
			$orderRequest = (new OrderRequest())
								->addType('redirect')
								->addOrderId($order_id)
								->addDescriptionText($description)
								->addMoney($amount)
								//->addGatewayCode('IDEAL')
								->addCustomer($customer)
								->addDelivery($customer)
								->addPluginDetails($pluginDetails)
								->addPaymentOptions( $paymentOptions);                

        }       
        $transactionManager = $multiSafepaySdk->getTransactionManager()->create($orderRequest);
        $url = $transactionManager->getPaymentUrl();

		header('Location: '.$url);
		die();
		

    }

    public function buildObject($shipmentinfo){
        
        $shipmentId     = isset($shipmentinfo['shipment_id']) ? $shipmentinfo['shipment_id'] : 0;
        $ship_method    = $shipmentinfo['ship_method'];
       
        $items          = $shipmentinfo['product'];           
        if(empty($items))
            return false;

        $order_data     = json_decode($shipmentinfo['orderjson'],true);
        $_json          = $order_data['data'];
        $country		= isset($_json['billing']['country']) ? $_json['billing']['country'] : 'nl';

        if($ship_method != 'own'){
            $shiping_wrap   = HomeController::getShippingPartners($country);        
            $ship_price     = $shiping_wrap[$ship_method]['rate'];
        }else{
            $ship_price     = 0;
        }

        $orderdata      = array();
        if(!empty($order_data))
            $orderdata = $order_data['data'];

        $customer       = $orderdata['billing'];    
        $site           = $orderdata['site'];
        $orderDate      = $orderdata['created'];
        $language       = 'en';
        $_symbol        = $orderdata['currency_symbol'];
        $currency       = $orderdata['currency'];
        $payment_method = $shipmentinfo['payment_method'];
        
        $siteReference = substr($shipmentinfo['order_id'], 0, 2);	
        
        $shipmentData = array(  'order_id'          => $shipmentinfo['order_id'],
                                'order_email'       => $shipmentinfo['email_address'],
                                'order_site'        => $site,
                                'order_date'        => date('Y-m-d H:i:s',$orderDate),
                                'lang'              => $language,
                                'site_code'         => $siteReference,
                                'payment_id'        => $shipmentinfo['payment_id'],
                                'shipment_status'   => 'new');

                              //  print_r($shipmentData); die; 
       
        $shipments  = new Shipments();     
        $currency       = $orderdata['currency'];
        $currency_symbol= $orderdata['currency_symbol'];
        $orderItems     = $orderdata['items']; 


        if($shipmentId)
            $_shipment = $shipments->where('id',$shipmentId)->first();
        else    
            $_shipment = $shipments->where('order_id',$shipmentinfo['order_id'])->first();

        if(empty($_shipment)){
            $shipment   = $shipments->create($shipmentData);    
            if(isset($shipment->id))
                $shipmentId = $shipment->id;
        }else{
            $shipmentId = $_shipment->id;
            $_shipment->update($shipmentData);
        }
        if($shipmentId){
            $payment_stautus_details = ($payment_method == 'refund-deduction') ? 'deduction' : 'pending';
            $payment_status = ($payment_method == 'refund-deduction') ? 1 : 0;
			
							
			
            
            $shipmentDetails = array('shipment_id'     => $shipmentId,
                                     'shiping_method'   => $ship_method,
                                     'shiping_price'    => $ship_price,
                                     'currency'         => $currency,
                                     'payment_method'   => $payment_method,
                                     'payment_status_details'  => $payment_stautus_details,
                                     'payment_status'  => $payment_status,
                                     'customer_details' => json_encode($customer));    
			if($payment_method == 2){
				$shipmentDetails['payment_status'] = 1;
				$shipmentDetails['paymepayment_status_detailsnt_status'] = 'store-creadit';
				$shipmentDetails['txn_id'] = $shipmentinfo['payment_id'];
			}							
            $details  = new ShipmentDetails();
            $ship_details = $details->where('shipment_id',$shipmentId)->first();
            if(empty($ship_details))
                $details->create($shipmentDetails);   
            else    
                $ship_details->update($shipmentDetails);   

            $order_items    = $orderdata['items'];
            $return_reason  = $shipmentinfo['return_reason'];
            $hygiene_seal   = $shipmentinfo['hygiene_seal'];
            $package_opend  = isset($shipmentinfo['package_opend']) ? $shipmentinfo['package_opend'] : '';
            $return_type    = $shipmentinfo['return_type'];
            $note           = $shipmentinfo['note'];
            $shipment_items = new ShipmentItems();   

           
                
            foreach($items as $_index => $item){
                $line_key       = explode('_',$_index);
                $index          = $line_key[0];    
                $lineItem       = $order_items[$index];
                $qty            = $lineItem['quantity'];
                $total          = $lineItem['total'] / $qty;
                $tax          = $lineItem['total_tax'] / $qty;

                $order_item     = array('shipment_id'    => $shipmentId,
                                        'product_id'    => $lineItem['product_id'],
                                        'line_id'       => $_index,
                                        'product_sku'   => $lineItem['sku'],
                                        'product_title' => $lineItem['product_name'],
                                        'quantity'      => 1,
                                        'line_price'    => $total,
                                        'total'         => $total,
                                        'total_tax'     => $tax,
                                        'product_thumb' => $lineItem['product_thumb'],
                                        'return_reason' => $return_reason[$_index],
                                        'hygiene_seal'  => $hygiene_seal[$_index],
                                        'is_opened'     => isset($package_opend[$_index]) ? $package_opend[$_index] : '',
                                        'return_type'   => $return_type[$_index],
                                        'note'          => isset($note[$_index]) ? $note[$_index] : '',
                                       // 'attributes'    => json_encode($lineItem['attributes'])); 
                                        'attributes'    => isset($lineItem['attributes']) ? json_encode($lineItem['attributes']) : null);
                                      
                $store_item = $shipment_items->where('shipment_id',$shipmentId)->where('line_id',$_index)->first();   

                if(empty($store_item))
                    $shipment_items->create($order_item);
                else    
                    $store_item->update($order_item);



                if($ship_method == 'gls'){
                      
                    $collection = array('shipment_id'       => $shipmentId,
                                        'collection_date'   => $shipmentinfo['collection_date'],
                                        'name'              => $shipmentinfo['name_surname'],
                                        'street'            => $shipmentinfo['street'],
                                        'house_no'          => $shipmentinfo['house_no'],
                                        'city'              => $shipmentinfo['city'],
                                        'phone_no'          => $shipmentinfo['phone_no'],
                                        'extension'         => $shipmentinfo['extension'],
                                        'country'           => $shipmentinfo['country'],
                                        'post_code'         => $shipmentinfo['post_code'],
                                        'customer_note'     => $shipmentinfo['gls_note']);   

                                      
                                 
					$address = new ShipmentAddress();  
					$address_item = $address->where('shipment_id',$shipmentId)->first();    
					if(empty($address_item))
						$address->create($collection);
                    else    
                        $address->update($collection);  
				}
            }

            //Session::forget('return_data');   
            
            //PortalLogs::create();         
        } 

        $logs = array(  'source_id' => $shipmentId,
                        'type'      => '1',
                        'note'      => 'New return request created');
        $log = new PortalLogs();
        $res = $log->create($logs);    
        return $shipmentId;
    }
	
	public function WebHookIndex(){
		
	}

	public function WebHookIndexPOst(){
		
	}	
}
