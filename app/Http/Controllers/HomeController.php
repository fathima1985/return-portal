<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\DB;
use App\Models\Shipments;
use App\Models\ShipmentDetails;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItems;
use App\Models\PortalLogs;
use App\Http\Controllers\PaymentController;
use App\Models\languages;
use App\Models\Language_keys;
use App\Models\language_details;
use App\Models\stores;
use App\Models\shipping_methods;
use App\Models\stroe_shipping;

use File;

class HomeController extends Controller
{
    public function home()
    {
        //return redirect('dashboard');

		/*$file = fopen(resource_path("en.csv"),"r");
		$json_data	= array();
		while (! feof($file)) {
			$data = fgetcsv($file);
			if(!empty($data)){
				
				if($data['0'] != ''){
					$key = trim(str_replace(' ','_',strtolower($data[0])));
					$json_data[$key] =trim($data['1']);
				}
			}
		}
		

		$input = mb_convert_encoding($json_data, 'UTF-8', 'UTF-8');
				
		echo htmlspecialchars(response()->json($input, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE));
				echo json_encode($json_data, JSON_UNESCAPED_UNICODE );
				echo json_last_error();
				echo "<pre>";
				print_r($json_data);
				echo "</pre>";
				
				//
			fclose($file);
			die;
		*/
		$data				= $this->PageDefaults();
		$data['title'] 		= $data['lang']['return_portal'];
		$stored				= array();
		$retriveData		= isset($_GET['return']) ? 1 : 0;
		$orderjson			= array();
		$data['product']	= array();
		if(Session::get('return_data')){
			$stored 	= Session::get('return_data');
			$stored = json_decode($stored,true);		
			if(isset($stored['orderjson']))	
				$orderjson = json_decode($stored['orderjson'],true)['data'];
				
			$data['product']	= isset($stored['product']) ? $stored['product'] : null ;
		}
		
		$data['retrive']	= $retriveData;
		$data['stored']		= $stored;
		$data['orders']		= $orderjson;		
		return view('front-page/index',$data);
    }

	public function PageDefaults(){
		$langCode 			= isset($_REQUEST['lang']) ? $_REQUEST['lang'] : '';						
		$locale 			= $this->getLocale($langCode); 
		$data['languages']	= $this->getLanguage();
	
		$data['shipping']	= $this->getShippingPartners();

		$data['language']	= $locale['langs'];		
		if($langCode == ''){
			$langCode	= $locale['landcode'];	
		}
		$data['langCode']	= $locale['landcode'];	
		$data['selected']	= $locale['language_name'];	
		$data['lang']	= $this->getLanuageLocale($langCode);
		
		$data['return']		= $this->returnResons($data['lang']);

		return $data;
	}

	public function getLanuageLocale($langCode = 'en'){		
		if($langCode == '')
			$langCode = 'en';
		$dataJson = $this->openJSONFile($langCode);	
		return $dataJson;

	}

	private function openJSONFile($code){
		$jsonString = [];
		$languages        =  language_details::leftjoin('languages','language_details.language_id','=','languages.id')->leftjoin('language_keys','language_details.language_key','=','language_keys.id')->where('languages.language_code',$code)->select(['language_details.*','languages.language_code','languages.language_name','language_keys.language_index'])->get();

		if(!empty($languages)){
			foreach($languages as $_lang){
				$values = $_lang->language_string;
				$index 	= $_lang->language_index;
				$jsonString[$index] = $values;
				if (is_string($values) && is_array(json_decode($values, true))){
					$jsonString[$index] = json_decode($values,true);
				}
			}
		}        
        return $jsonString;
    }


	public function getLocale($langCode = ''){

		if($langCode != ''){			
			setcookie("locale", $langCode, time() + 3600);			
		}else{
			$langCode = (isset($_COOKIE['locale'])) ? $_COOKIE['locale'] : '';			
		}
		if($langCode == ''){
			$langCode = 'en';
		}
		

		$country = $this->getLanguage();

		if(isset($country[$langCode])){				 
			$selected	=  $country[$langCode];
			unset($country[$langCode]);	
		}	
		return array('landcode' => $langCode, 'langs' => $country,'language_name' => $selected);
	}
	
	public function returnResons($lang = null){
		$return_reason = isset($lang['select_the_return_reason']) ? $lang['select_the_return_reason'] : 'Please select the return reason';
		$hygiene_seal = isset($lang['hygiene_seal_on_the_packaging']) ? $lang['hygiene_seal_on_the_packaging'] : 'Please select the return reason';
		$return_type = isset($lang['select_return_type']) ? $lang['select_return_type'] : 'Please select the return reason';	

		$package_opend = isset($lang['have_you_opened_the_packaging']) ? $lang['have_you_opened_the_packaging'] : 'Have you opened the packaging?';
		$_resons = array(); /*'small-size'	=> $lang['reason_1'],
						'material-issue'=> $lang['reason_2'],
						'damaged-item'	=> $lang['reason_3'],
						'mismatch'		=> $lang['reason_4'],
						'big-size'		=> $lang['reason_5'],
						'diffrent-item'	=> $lang['reason_6']); */
								
		
		$reasons = isset($lang['return_reasons']) ? $lang['return_reasons'] : $_resons;
		
		//print_r($lang['return_reasons']);
		
		$return_questions = array('return_reason' => array('label' =>  $return_reason,
								  'options' => $reasons),
								  'hygiene_seal' => array('label' => $hygiene_seal,
														  'options' => array('yes' => $lang['yes'],'no' => $lang['no'])),
								  'package_opend' => array('label' => $package_opend,
														  'options' => array('yes' => $lang['yes'],'no' => $lang['no'])),
								
								 'return_type' => array('label' =>$return_type,
														'options' => array('store_coupon' => $lang['store_coupon'],
														'exchange' => $lang['exchange'],
														'refund' => $lang['refund'])),	
														
								);
								
		return $return_questions;						
		
	}


	public function ConfirmShipping(request $request){		
		$_data = $request->input();
		if(!empty($_data)){
			$submission = json_encode($_data);
			//$request->session()->put('return',$submission);
			Session::put('return_data', $submission);
		}
		//$data = array();
		return Redirect::to('/confirm-shipping');
		//$this->ConfirmShippingMethod();
		//return view('front-page/confirm-shipping',$data);
	}

	public function ShippingForm($lang = null){
		$formFields = array('collection_date' => array('label' => isset($lang['collection_date']) ? $lang['collection_date'] : 'Desired Collection Date',
														'type' => 'date',
														'required' => 1),
							'order_no' 			=> array('label' => isset($lang['order_number']) ? $lang['order_number'] : 'Order Number',
														'type' => 'text',
														'required' => 1),
							'email_address' 	=> array('label' => isset($lang['e_mail']) ? $lang['e_mail'] : 'E-mail',
														'type' => 'email',
														'required' => 1),	
							'name_surname' 		=> array('label' => isset($lang['name_surname']) ? $lang['name_surname'] : 'Name & Surname',
														'type' => 'text',
														'maxLength' => 30,
														'required' => 1),
							'country' 			=> array('label' => isset($lang['country']) ? $lang['country'] : 'Country',
														'type' => 'text',
														'readonly' => 1,	
														'maxLength' => 2,
														'required' => 1),
							'post_code' 		=> array('label' => isset($lang['zipcode']) ? $lang['zipcode'] : 'ZIP/Post Code',
														'type' => 'text',
														'maxLength' => 8,
														'required' => 1),
							'street' 			=> array('label' => isset($lang['street']) ? $lang['street'] : 'Street',
														'type' => 'text',
														'maxLength' => 40,
														'required' => 1),
							'house_no' 			=> array('label' => isset($lang['house_no']) ? $lang['house_no'] : 'House Number',
														'type' => 'text',
														'maxLength' => 10,
														'required' => 1),
							'extension' 		=> array('label' => isset($lang['extension']) ? $lang['extension'] : 'Extension',
														'type' => 'text',
														'maxLength' => 10,
														'required' => 0),
							'city' 				=> array('label' => isset($lang['city']) ? $lang['city'] : 'City',
														'type' => 'text',
														'maxLength' => 30,
														'required' => 1),
							'phone_no' 			=> array('label' => isset($lang['phone_no']) ? $lang['phone_no'] : 'Phone Number',
														'type' => 'text',
														'maxLength' => 25,
														'required' => 0),				
							'gls_note' 				=> array('label' => isset($lang['pickupnote']) ? $lang['pickupnote'] : 'Note',
														'type' => 'text',
														'required' => 0)	
							);
		return $formFields;					
	}

	public function ConfirmShippingMethod(){		
		
		$stored 	= Session::get('return_data');	
		
		if(!empty($stored)){
			$data = json_decode($stored,true);
		}
		$orderjson = isset($data['orderjson']) ? json_decode($data['orderjson'],true)['data'] : null;
		
		if(empty($orderjson))
			return Redirect::to('/');
		
		$currency	= array('currency' => $orderjson['currency'],
							'currency_symbol' =>  $orderjson['currency_symbol']);
		
		$country	= isset($orderjson['billing']['country']) ? $orderjson['billing']['country'] : 'nl';		
		$order	 	= json_decode($data['orderjson'],true)['data'];	
		$shipdata   = $order['shipping'];
		$title 		= 'Return Portal - Confirm Shipping';
		$languages	= $this->getLanguage();

		$values['name_surname'] = $shipdata['first_name'].' '.$shipdata['last_name'];
		$values['street'] = $shipdata['address_1'];
		$values['city'] = $shipdata['city'];
		$values['country'] = $shipdata['country'];
		$values['phone_no'] = $shipdata['phone'];
		$values['post_code'] = $shipdata['postcode'];
		

		$retriveData		= isset($_GET['return']) ? 1 : 0;
		$data['retrive']	= $retriveData;

		//$langCode 		= isset($_REQUEST['locale']) ? $_REQUEST['locale'] : '';		
		$locale 		= $this->getLocale(); 
		$langCode		= isset($locale['landcode']) ? $locale['landcode'] : 'nl';
		$lang			= $this->getLanuageLocale($langCode);
		$shipping		= self::getShippingPartners($country,$lang);
		$data['title'] 	= $lang['return_portal'];
		$language		= $locale['langs'];		
		if($langCode == ''){
			$langCode		= $locale['landcode'];	
		}
		$data['langCode']	= $locale['landcode'];	
		$selected	= $locale['language_name'];	
		$formFields = $this->ShippingForm($lang);

		return view('front-page/confirm-shipping',compact('shipping','data','title','languages','formFields','order','lang','selected','langCode','values'));
	}	

	public function getLanguage(){
		$country = array();
		$languages      = languages::orderBy('position')->where('status',1)->get();		
		foreach($languages as $lang){
			$country[$lang->language_code] = $lang->language_name;
		}
		return $country;
	}

	public function getCurrencies(){

		
	}

	public function getShippingMethod($store_id,$store_creadit = 0){
		$store      = stores::find($store_id);         
		$shiping	= array();
		if($store_creadit){
			$shiping    = stores::leftjoin('stroe_shippings','stores.id','=','stroe_shippings.store_id')                    
			->leftjoin('shipping_methods','stroe_shippings.shipping_method','=','shipping_methods.id')                    
			->where('stroe_shippings.is_free',1)->where('stroe_shippings.store_id',$store_id)->where('stroe_shippings.is_active',1)->select(['stores.*','stroe_shippings.shipping_price','stroe_shippings.is_default','stroe_shippings.is_free','stroe_shippings.id as ship_id','shipping_methods.shipping_title','shipping_methods.shipping_name','shipping_methods.shipping_logo','shipping_methods.is_pickup','stroe_shippings.shipping_method','stroe_shippings.is_default'])->orderBy('stroe_shippings.is_active','DESC')->get();

		}

		if(empty($shiping)){
       		 $shiping    = stores::leftjoin('stroe_shippings','stores.id','=','stroe_shippings.store_id')                    
                    ->leftjoin('shipping_methods','stroe_shippings.shipping_method','=','shipping_methods.id')                    
                    ->where('stroe_shippings.store_id',$store_id)->where('stroe_shippings.is_active',1)->select(['stores.*','stroe_shippings.shipping_price','stroe_shippings.is_default','stroe_shippings.is_free','stroe_shippings.id as ship_id','shipping_methods.shipping_title','shipping_methods.shipping_name','shipping_methods.shipping_logo','shipping_methods.is_pickup','stroe_shippings.shipping_method','stroe_shippings.is_default'])->orderBy('stroe_shippings.is_active','DESC')->get();
		}

		return $shiping;
	}
	
	public function getShippingPartners($country = 'nl',$lang = null){	
		$store_id 			= Session::get('store_id');	
		$shippartners		= array();
		$is_creadit			= 0;
		$is_exchange		= 0;
		if(Session::get('return_data')){
			$stored 			= json_decode(Session::get('return_data'),true);
			$return_type		= isset($stored['return_type']) ? $stored['return_type'] : null;
			
			if(!empty($return_type)){
				foreach($return_type as $key => $val){
					if($val == 'exchange'){
						//$is_exchange = 1;
					}elseif($val == 'store_coupon'){
						$is_creadit = 1;
					}	
				}
			}
		}		
		
		$store_id = 1;
		$ship_free	= 0;

		if($is_creadit && !$is_exchange){
			$ship_free = 1;
		}

		$shipping_method = $this->getShippingMethod($store_id,$ship_free);

		
		
		if(!empty($shipping_method)){
			foreach($shipping_method as $_method){
				$_index	 = $_method->shipping_name;
				$_name 			= isset($lang[$_index]) ? $lang[$_index] : $_method->shipping_title;
				$instruction	= isset($lang[$_index.'_instruction']) ? $lang[$_index.'_instruction'] : $_method->shipping_title;
				$shippartners[$_index]	=	array(	'name'			=> $_name,
												 	'instruction' 	=> $instruction,
													 'default' 		=> $_method->is_default,
													 'rate' 		=> $_method->shipping_price,
													 'pickup' 		=> $_method->is_pickup,
										  			 'free' 		=> $_method->is_free,
													 'icon'			=> $_method->shipping_logo);
			}
		}
		

		if(!$ship_free){	
			$_index			= 'own';
			$_name 			= isset($lang[$_index]) ? $lang[$_index] : $_method->shipping_title;
			$instruction	= isset($lang[$_index.'_instruction']) ? $lang[$_index.'_instruction'] : '';

			$shippartners['own']	=	array(	'name'			=> $_name,
												'instruction' 	=> $instruction,
												'rate' 			=> '0.00',
												'default' 		=>  0,
												'pickup' 		=>  0,
												'free' 			=>  0,
												'icon'			=> '');
		}
		
		
		/*

		$shipping_partners['nl'] = 	array(	'homerr' => "4.99",
											'gls' => "9.99",
											'ups' => "7.99",
											'gls_return' => "6.99",
										);

		$shipping_partners['be'] = 	array(	'homerr' => "5.99",
											'gls' => "13.99",
											'ups' => "8.99",
											'gls_return' => "7.99");

		$shipping_partners['de'] = 	array(	'gls' => "13.99",
											'ups' => "9.99",
											'gls_return' => "8.99",);
	
		$shipping_partners['at'] = 	array(	'gls' => "13.99",
											'ups' => "13.99",
											'gls_return' => "10.99");
															
		$shipping_partners['fr'] = 	array(	'gls' => "13.99",
											'ups' => "13.99",
											'gls_return' => "11.99");
											
		$shipping_partners['dk'] = 	array(	'gls' => "99",
											'ups' => "125",
											'gls_return' => "75");
		
		$shipping_partners['es'] = 	array(	'gls' => "16.99",
											'ups' => "14.99",
											'gls_return' => "13.99",);

		$shipping_partners['it'] = 	array(	'gls' => "14.99",
											'ups' => "13.99",
											'gls_return' => "12.99");

		$shipping_partners['se'] = 	array(	'gls' => "199",
											'ups' => "189",
											'gls_return' => "159");
											
		$shipping_partners['fi'] = 	array(	'gls' => "17.99",
											'ups' => "16.99",
											'gls_return' => "13.99");

		$shipping_partners['pt'] = 	array(	'gls' => "14.99",
											'ups' => "14.99",
											'gls_return' => "12.99");

		$shipping_partners['cz'] = 	array(	'ppl' => "120");

		//$shipping_partners['hu'] = 	array(	'ppl' => "2500");

		$shipping_partners['hu'] 	= 	array('gls_hu' => "2500");
		
		$shipping_partners['ro'] 	= 	array('gls_ro' => "24.99");
		
		$shipping_partners['sk'] 	= 	array('gls_sk' => "6.99");	

		if($country == '')
			$country = 'nl';
		
		

		$country = strtolower($country);	

		$cntryshiping =	isset($shipping_partners[$country])	 ? $shipping_partners[$country] : $shipping_partners['nl'];
		
		

		$free_shipping = array(	'nl' => 'gls_return',
								'be' => 'gls_return',
								'de' => 'gls_return',
								'at' => 'gls_return',
								'fr' => 'gls_return',
								'dk' => 'gls_return',
								'es' => 'gls_return',
								'it' => 'gls_return',
								'se' => 'gls_return',
								'fi' => 'gls_return',
								'pt' => 'gls_return',
								'hu' => 'gls_hu',
								'cz' => 'ppl');

		$partners = array(/*'homerr' 	=> array('name' 		=> isset($lang['homerr']) ? $lang['homerr'] : 'HOMERR',
											'instruction' 	=> isset($lang['homerr_instruction']) ? $lang['homerr_instruction'] : '',
											'rate' 			=> '4.99',
											'icon'			=> 'Homerr-Logo.png'), 
							'ups' 	=> array('name' 		=> isset($lang['ups']) ? $lang['ups'] : 'UPS',
											'instruction' 	=> isset($lang['ups_instruction']) ? $lang['ups_instruction'] : '',
											'rate' 			=> '7.99',
											'icon'			=> 'UPS-logo.png'),	
							'gls' 	=> array('name' 		=> isset($lang['gls']) ? $lang['gls'] : 'GLS',
											'instruction' 	=> isset($lang['gls_instruction']) ? $lang['gls_instruction'] : '',
											'rate' 			=> '8.99',
											'icon'			=> 'GLS_Logo.png'),
							'gls_hu' 	=> array('name' 	=> isset($lang['gls']) ? $lang['gls'] : 'GLS',
											'instruction' 	=> isset($lang['gls_instruction']) ? $lang['gls_instruction'] : '',
											'rate' 			=> '8.99',
											'icon'			=> 'GLS_Logo.png'),	
							'gls_ro' 	=> array('name' 	=> isset($lang['gls']) ? $lang['gls'] : 'GLS',
											'instruction' 	=> isset($lang['gls_instruction']) ? $lang['gls_instruction'] : '',
											'rate' 			=> '8.99',
											'icon'			=> 'GLS_Logo.png'),						
							'gls_sk' 	=> array('name' 	=> isset($lang['gls']) ? $lang['gls'] : 'GLS SK',
											'instruction' 	=> isset($lang['gls_instruction']) ? $lang['gls_instruction'] : '',
											'rate' 			=> '6.99',
											'icon'			=> 'GLS_Logo.png'),	
							'gls_return' 	=> array('name' 	=> isset($lang['gls_return']) ? $lang['gls_return'] : 'GLS Shop Return',
												'instruction' 	=> isset($lang['gls_return_instruction']) ? $lang['gls_return_instruction'] : '',
												'rate' 			=> '6.99',
												'icon'			=> 'GLS_Logo.png'),														
							'ppl' 	=> array('name' 		=> isset($lang['ppl']) ? $lang['ppl'] : 'PPL',
											'instruction' 	=> isset($lang['ppl_instruction']) ? $lang['ppl_instruction'] : '',
											'rate' 			=> '8.99',
											'icon'			=> 'ppl-logo.png'));

		$shippartners	= array();

		

		foreach($partners as $key => $item){
			if(isset($cntryshiping[$key])){
				$shippartners[$key]	= $item;				
				if($is_creadit && !$is_exchange)
					$shippartners[$key]['rate'] = '0.0';
				else
					$shippartners[$key]['rate'] = $cntryshiping[$key];
			}
		}
		*/
		
		/*if($is_creadit && !$is_exchange){
			$method = isset($free_shipping[$country]) ? $free_shipping[$country] : '';
			if($method != ''){
				$_shippartners[$method] = $shippartners[$method];
				$_shippartners[$method]['rate'] = '0.0';
				$shippartners = $_shippartners;
			}
		}elseif(isset($lang['own_expense'])){
			$shippartners['own']	=	array('name'=> $lang['own_expense'],
											'instruction' 	=> $lang['own_expensetext'],'rate' 			=> '0.00',
											'icon'			=> '');

		} */
		return $shippartners;

	}

	public function fechOrdersInfo($order_id,$email_address){

		$siteReference 	= substr($order_id, 0, 2);		
		$store 			= stores::where('order_prefix',$siteReference)->get()->first();	
		$response_data	= '';	
		$data			= array('order_id' => $order_id ,'order-email' => $email_address);
		$url 			= $store->site_url;

		Session::put('store_id', $store->id);

		if($url != ''){
			$url = 'https://dev1.deluxerie.com';
		}
		if($url != ''){
			$response = Http::withBody(json_encode($data),'application/json')->post($url.'/wp-json/order/get_order');
			if ($response->getStatusCode() == 200) { // 200 OK
				$response_data = $response->getBody()->getContents();					
				
			}
		}
		return $response_data;
	}

	public function PullOrdersdetails(request $request ){
		//
		$data = $request->input();
		$order_id = $data['order_id'];
		$_res = array('status'=> 'false','message' => 'invalid','response' => 'invalid-response');	
		$shipments  = new Shipments();     		        
        $_shipment = $shipments->where('order_id',$order_id)->where('is_deleted','0')->first();
		if(!empty($_shipment)){
			$details  		= new ShipmentDetails();
			$ship_details 	= $details->where('shipment_id',$_shipment->id)->first();
			
			if($ship_details->payment_method == 'refund-dedection' || $ship_details->payment_status == 1){				
				$_res['redirect'] = '/return-request/'.$_shipment->id.'/'.$_shipment->order_id;				
			}else{
				$_res['redirect'] = 'return-summary?request_id='.$_shipment->id;
			}	
			$_res['message'] = 'Requset exists';				
			$_res['status'] = 'exsist';
			
		}else{
			$_res['response'] = 'New Orders';
			$order_email = $data['order-email'];
			$response_data = $this->fechOrdersInfo($order_id,$order_email);
			$results =  json_decode($response_data,true);	
			
			if(!empty($results)){
				$completed_date = isset($results['data']['completed_date']) ? $results['data']['completed_date'] : null;
				if(!empty($completed_date)){
					$timezone 	= $completed_date['timezone'];
					$_date 		= $completed_date['date'];
					$date_now	= date('Y-m-d H:i:s');
					//$timeZone 	= new \DateTimeZone($timezone);
					
					//$date_1 	= new \DateTime(strtotime($_date), $timeZone);
					//$date_now 	= new \DateTime('now', $timeZone);
					
					$startDate = Carbon::parse($_date);
					$endDate = Carbon::parse($date_now);
					$dateDiff = $endDate->diffInDays($startDate);
					$dateHours = $endDate->diffInHours($startDate);					
					/*if($dateDiff > 30){	
						$langCode 	= isset($_REQUEST['lang']) ? $_REQUEST['lang'] : '';
						$lang		= $this->getLanuageLocale($langCode);
						$message = isset($lang['order_return_exceed']) ? $lang['order_return_exceed'] : 'Your order is not eligible for return as it has exceeded the 30-day return or replace window as per our return policy.';
						$_res = array('status'=> false,'data' => null ,'error' => 'time_exceed','message' => $message);
						echo json_encode($_res); 
						die;
					}*/ 
				}
			}
				
			if($response_data != '' && $results['status'] != 0)
				$_res = array('status'=> 'true','data' => $results);
		}
		echo json_encode($_res); 
		die;
	}

	public function ReturnSummary(request $request){
		
		$data = $this->PageDefaults();	
		$data['title']		= 'Return Portal - Summary';
		$pickup 			=  array("_gls","gls","gls_hu","gls_ro","gls_sk");				
		if($request->input('request_id')){
			$shipments  = new Shipments(); 
			$shipmentId 	= $request->input('request_id');
			$shipmentinfo  	= $shipments->where('id',$shipmentId)->first();
			if(empty($shipmentinfo))
				return Redirect::to('/');

			$details  		= new ShipmentDetails();
			$ship_details 	= $details->where('shipment_id',$shipmentId)->first();

			

			$shipment_items = new ShipmentItems(); 
			$order_item 	= $shipment_items->where('shipment_id',$shipmentId)->get(); 


			
			
			$store_item	= array();
			if(!empty($order_item)){
				foreach($order_item as $key => $_item){
					$store_item[$_item->line_id] = $_item;
				}
			}

			
			$address_item	= array();  
			if($ship_details->shiping_method == 'gls'){
				$address = new ShipmentAddress();  
				$address_item = $address->where('shipment_id',$shipmentId)->first();  
			}	
				
			$orderjson			= $this->fechOrdersInfo($shipmentinfo->order_id,$shipmentinfo->order_email);			
			$orders 			= json_decode($orderjson,true);
			$_json				= $orders['data'];
			$country			= isset($_json['billing']['country']) ? $_json['billing']['country'] : 'nl';
			$data['shipping']	= self::getShippingPartners($country,$data['lang']);
			$data['orderjson']		= $orderjson;	
			$data['orders']			= $orders['data'];		
			$data['shipmentId']		= $shipmentId;
			$data['shipmentinfo']	= $shipmentinfo;
			$data['ship_details']	= $ship_details;
			$data['product']		= $store_item;	
			$data['address']		= $address_item;			
			$data['formFields']		= $this->ShippingForm($data['lang']);
			$ship_method			= $ship_details['ship_method'];
			$data['is_pickup']		= (in_array($ship_method,$pickup) !== false) ? 1 : 0;
			
			
			return view('front-page/summary-retrive',$data);

		}else{	
			$stored 			= json_decode(Session::get('return_data'),true);				
			$orders 			= json_decode($stored['orderjson'],true)['data'];
			$country			= isset($orders['billing']['country']) ? $orders['billing']['country'] : 'nl';
			$data['languages']	= $this->getLanguage();
			//$data['return']		= $this->returnResons();
			$lang				= $data['lang'];
			$data['formFields']	= $this->ShippingForm($lang);
			$data['shipping']	= $this->getShippingPartners($country,$lang);
			$data['data']		= $stored;
			$data['orders']		= $orders;		
			$data['title']		= 'Return Portal - Summary';
			$ship_method		= $stored['ship_method'];
			$data['is_pickup']	= (in_array($ship_method,$pickup) !== false) ? 1 : 0;
			
			if(isset($_GET['cancel'])){	
				$logs = array(  'source_id' => $shipmentId,
								'type'      => '2',
								'note'      => 'Payment failed or canceled');
				$log = new PortalLogs();
				$res = $log->create($logs);
			}  

			return view('front-page/summary',$data);
		}

	}

	public function ConfirmSummary(request $request){
        $formData       = $request->input();
		$order_id		= 0;
		if($request->input('shipment_id')){
			$shipmentinfo 				= $request->input();
		}elseif(Session::get('return_data')){
			$shipmentinfo 				= json_decode(Session::get('return_data'),true);
			$formData['orderjson']      = $shipmentinfo['orderjson'];
			$formData['order_id']       = $shipmentinfo['order_id'];
			$formData['order_email']    = $shipmentinfo['order_email'];
		}	
		
		if(empty($formData))
			return Redirect::to('/');

		$order_id		= $formData['order_id'];
        Session::put('return_data', json_encode($formData));

		$data = $this->PageDefaults();
		$data['title']	 = $data['lang']['return_portal'].' - '.$data['lang']['payment_method'];

		$is_creadit			= 0;
		$is_exchange		= 0;		
		$return_type		= isset($formData['return_type']) ? $formData['return_type'] : null;		
		if(!empty($return_type)){
			foreach($return_type as $key => $val){
				if($val == 'exchange'){
					//$is_exchange = 1;
				}elseif($val == 'store_coupon'){
					$is_creadit = 1;
				}	
			}
		}
		$data['is_creadit']		= $is_creadit;
		$data['is_exchange']	= $is_exchange;

		

		if($is_creadit && !$is_exchange){
			$payment 	= new PaymentController();
			$shipmentinfo['payment_method'] = '2';	
			$payment_id     = $order_id.'-'.time();		
			$shipmentinfo['payment_id']     = $payment_id;
			$shipment_id    = $payment->buildObject($shipmentinfo);	
			return Redirect::to('/return-complete/'.$shipment_id.'/'.$payment_id);

		}elseif($formData['ship_method'] == 'own'){
			$payment 	= new PaymentController();
			$response	= $payment->CompleteOwn();	
			return Redirect::to('/return-complete/'.$response['shipment_id'].'/'.$response['order_id']);			
			//return view('front-page/complete-payment',$data);
		}

        return view('front-page/confirm-payment',$data);
    }

	public function DoPayment(request $request){
		$ship_data = $request->input();
		
		if(!empty($ship_data)){
			$stored 	= json_decode(Session::get('return_data'),true);	
			$paydata 	= array_merge($stored,$ship_data);	
			$submission = json_encode($paydata);
			$request->session()->put('return',$submission);
			Session::put('return_data', $submission);

			return Redirect::to('/return-summary');
			//
		}
		return Redirect::to('/confirm-shipping');
	}

	public function thanks($ship_id, $order_id){
		$data 					= $this->PageDefaults();

		//Clear session on final step
		Session::forget('return_data');
		
		
		$shipLabels 			= array('__ups','ups','ppl','homerr','gls_hu','gls_ro','gls_sk','gls_return');

		$data['title']	 		= 'Return Portal - Thanks';
		$data['shipment_id']	= $ship_id;
		$data['order_id']		= $order_id;
		$shipments  = new Shipments(); 
		$shipmentinfo  	= $shipments->where('id',$ship_id)->where('payment_id',$order_id)->first();
		if(empty($shipmentinfo))
			return Redirect::to('/');

		$details  		= new ShipmentDetails();
		$ship_details 	= $details->where('shipment_id',$ship_id)->first();	
		$data['shiping_method']	= $ship_details->shiping_method;

		$language		= $data['lang'];
		$order_success	= 0;		
		
		if($ship_details->payment_method == 'online-payment' && $ship_details->txn_id !='' ){
			$order_success = 1;
		}elseif($ship_details->payment_method == 'refund-deduction' || $ship_details->payment_method == 'store-creadit' || $ship_details->payment_method == '2'){
			$order_success = 1;
		}
		$data['show_button']		= (in_array($ship_details->shiping_method,$shipLabels) !== false) ? 1 : 0;
		$data['is_complete'] = $order_success;

		/*$address        = new ShipmentAddress(); 
		$address_item   = $address->where('shipment_id',$ship_id)->first();
		$order_country 	= $address_item->country;
		$country 		= strtolower($order_country); */

		if($ship_details->shiping_method != 'homerr' && $ship_details->mail_sent == 0){
			$footer		=	$language['email_thanks_footer'];
			$button		=	'';			
			$message	=	$language['email_thanks_title1'];
			if($ship_details->shiping_method == 'ups' || $ship_details->shiping_method == 'ppl' || $ship_details->shiping_method == 'homerr'){
				$subject	=	$language['email_thanks_subject1'];
				$title		=	$language['email_thanks_title1'];
				$body		=	$language['email_thanks_body1'];
				$footer		=	$footer;
				$button		=	$language['email_thanks_button'];			
				$message	=	$language['email_thanks_title1'];
			}elseif($ship_details->shiping_method == 'own'){
				$subject	=	$language['email_thanks_subject2'];
				$title		=	$language['email_thanks_title2'];
				$body		=	$language['email_thanks_body2'];
				$footer		=	$language['email_thanks_footer'];			
				$message	=	$language['email_thanks_title1'];
			}elseif($ship_details->shiping_method == 'gls'){
				$subject	=	$language['email_thanks_subject3'];
				$title		=	$language['email_thanks_title3'];
				$body		=	$language['email_thanks_body3'];
				$message	=	$language['email_thanks_title3'];
			}elseif($ship_details->shiping_method == 'gls_hu' || $ship_details->shiping_method == 'gls_sk'){
				$subject	=	$language['email_thanks_subject3'];
				$title		=	$language['email_thanks_title3'];
				$body		=	$language['email_thanks_body3'];
				$message	=	$language['email_thanks_title3'];
				$footer		=	$footer;
				$button		=	$language['email_thanks_button'];	
			}elseif($ship_details->shiping_method == 'gls_ro'){
				$subject	=	$language['email_thanks_subject3'];
				$title		=	$language['email_thanks_title3'];
				$body		=	$language['email_thanks_body3'];
				$message	=	$language['email_thanks_title3'];
				$footer		=	$footer;
				$button		=	$language['email_thanks_button'];	
			}else if($ship_details->shiping_method == 'gls_return'){
				$subject	=	$language['email_thanks_gls_return_subject'];
				$title		=	$language['email_thanks_gls_return_title'];
				$body		=	$language['email_thanks_gls_return_body'];
				$message	=	$language['thanks_gls_return'];
				$footer		=	$footer;
				$button		=	$language['email_thanks_button'];	
			}
			
			$details = array('mail_view'=> 'emailbody', 
							'subject'   =>  $subject,
							'title'     =>  $title,
							'body'      =>  $body,
							'site'      =>  '',
							'footer'    =>  $footer,
							'url'       => '',
							'button'    => $button,
							'message'   => $message,
							'link'      => 'https://return.deluxerie.net/return-complete/'.$ship_id.'/'.$order_id);   

				$to_email = $shipmentinfo->order_email;								
				$ship_details['mail_sent'] = 1;
				$ship_details->save();
			\Mail::to($to_email)->send(new \App\Mail\ReturnMail($details)); 
		}

        return view('front-page/thanks',$data);
	}


	public function returnView($ship_id, $order_id){
		$data 					= $this->PageDefaults();
		$data['title']	 		= 'Return Portal - Thanks';
		$data['shipment_id']	= $ship_id;
		$data['order_id']		= $order_id;
		$shipments  = new Shipments(); 
		$shipmentinfo  	= $shipments->where('id',$ship_id)->where('order_id',$order_id)->first();
		if(empty($shipmentinfo))
			return Redirect::to('/');

		$details  		= new ShipmentDetails();
		$ship_details 	= $details->where('shipment_id',$ship_id)->first();	
		$data['shiping_method']	= $ship_details->shiping_method;

		$language	= $data['lang'];
        return view('front-page/view-request',$data);
	}

	public function ConfirmPayment(){

		echo "LOAD PAY NOW HERE";

	}
	
	public function getReturns(Request $request){
		
		
		
		$postInput 	= file_get_contents('php://input');		
		$req_data 	= json_decode($postInput, true);
		$order_id 	= (isset($req_data['order_id']) && $req_data['order_id'] != '') ? $req_data['order_id'] : '';
		$email 		= isset($req_data['email']) ? $req_data['email'] : '';
		$site 		= isset($req_data['site']) ? $req_data['site'] : '';
		$langCode 	= isset($req_data['locale']) ? $req_data['locale'] : 'en';		
		$response = array('data' => [],'options' => '','message' => '','status' => 'failed');		
		$shipmentinfo = array();
		$shipment_details = array();
		$shipments = DB::table('shipments')
		->leftJoin('shipment_details', 'shipments.id', '=', 'shipment_details.shipment_id')
		//->leftJoin('shipment_items', 'shipments.id', '=', 'shipment_items.shipment_id')
		->leftJoin('shipment_labels', 'shipments.id', '=', 'shipment_labels.shipment_id')
		//->join('shipment_addresses', 'shipments.id', '=', 'shipment_addresses.shipment_id')
		->select('shipments.*', 'shipment_details.shiping_method','shipment_details.shiping_price','shipment_details.payment_method','shipment_details.currency','shipment_details.payment_status','shipment_details.txn_id','shipment_details.store_note','shipment_labels.TrackingCode','shipment_labels.label_pdf','shipment_labels.TrackingLink','shipment_labels.label_info')->where('shipments.order_site','like', '%' . $site . '%');
		
		
		
		if($email != '' && $order_id != ''){
			$shipmentinfo  	= $shipments->where('shipments.order_email',$email)->where('shipments.order_id',$order_id)->where('is_deleted',0)->orderBy('id','ASC')->get();
		}else{
			//echo $shipments->where('shipments.order_email',$email)->where('is_deleted', 0)->orderBy('id','ASC')->toSql();			
			$shipmentinfo  	= $shipments->where('shipments.order_email',$email)->where('is_deleted', 0)->orderBy('id','ASC')->get();
			
			//print_r($shipmentinfo);
		}
		if(!empty($shipmentinfo)){
			foreach($shipmentinfo as $ship_items){		
				$shipmentId 	=  	$ship_items->id;
				$order_id 		= 	$ship_items->order_id;
				$shipment_details[$order_id] = $ship_items;
				$shipment_items = new ShipmentItems(); 
				$order_item 	= $shipment_items->where('shipment_id',$shipmentId)->get(); 
				$shipment_details[$order_id]->items = $order_item; 
				if($ship_items->shiping_method == 'gls'){
					$address = new ShipmentAddress();  
					$shipment_details[$order_id]->address = $address->where('shipment_id',$shipmentId)->first();  
				}
			}	
			//$langCode			= 'en';
			$lang				= $this->getLanuageLocale($langCode);		
			//$returnResons		= $this->returnResons($lang);

			$message 	= "Total ".count($shipmentinfo).' records found';
			$response 	= array('data' => $shipment_details,'lang' => $lang,'message' => $message,'status' => 'success','total' => count($shipmentinfo));	
		}		
		return response()->json($response);
	}
	
	public function getReturnById($id = ''){
		echo $id;
		
	}
}
