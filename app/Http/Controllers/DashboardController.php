<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Shipments;
use App\Models\ShipmentDetails;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItems;
use App\Models\ShipmentLabels;
use App\Models\UserTasks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\HomeController;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\PortalLogs;
use App\Models\languages;
use App\Models\Language_keys;
use App\Models\language_details;
use App\Models\stores;
use App\Models\shipping_methods;
use App\Models\stroe_shipping;
use App\Traits\Upload;
use App\Models\settings;

use Carbon\Carbon;
use File;
class DashboardController extends Controller
{
    public $user;


    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

  
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShipmentDetails  $shipmentDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShipmentDetails  $shipmentDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShipmentDetails  $shipmentDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }


    public function DashboardHome(request $request){
        $shipment       =   Shipments::where('shipment_status','new')->where('is_deleted','0')->get()->count();

        $paid_count     =   Shipments::leftjoin('shipment_details','shipments.id','=','shipment_details.shipment_id')
                            ->where('shipment_details.payment_status',1)
                            ->where('shipments.is_deleted','0')
                            ->where('shipment_details.payment_method','online-payment')
                            ->where('shipment_details.txn_id','!=','NULL')
                            ->where('shipments.shipment_status','new')
                            ->get()->count();


        $refund_count   =   Shipments::leftjoin('shipment_details','shipments.id','=','shipment_details.shipment_id')
                            //->where('shipment_details.payment_status',1)
                            ->where('shipment_details.payment_method','refund-deduction')   
                            ->where('shipments.is_deleted','0')                        
                            ->where('shipments.shipment_status','new')
                            ->get()->count();

                       

        $ship_own       =   Shipments::leftjoin('shipment_details','shipments.id','=','shipment_details.shipment_id')
                          //  ->where('shipment_details.payment_status',1)
                            ->where('shipment_details.shiping_method','own')  
                            ->where('shipments.is_deleted','0')                         
                            ->where('shipments.shipment_status','new')
                            ->get()->count();


        $myreturn = Shipments::leftjoin('user_tasks','shipments.id','=','user_tasks.shipment_id')
                                ->leftjoin('users','user_tasks.user_id','=','users.id')
                                ->select(['shipments.*','users.name','users.id as user_id','users.email','user_tasks.id as assign_id'])->where('users.id',$this->user->id)->get()->count();

        $home = new HomeController();
        $langCode  = 'en';
        $language  = $home->getLanuageLocale($langCode);

       
      
        
       /* $details = array('mail_view'=> 'emailbody', 
                        'subject'   =>  $language['email_thanks_subject1'],
                        'title'     =>  $language['email_thanks_title1'],
                        'body'      =>  $language['email_thanks_body1'],
                        'site'      =>  '',
                        'footer'    =>  $language['email_thanks_footer'],
                        'url'       => '',
                        'button'    => $language['email_thanks_button'],
                        'message'   => $language['email_thanks_title1'],
                        'link'      => 'https://return.deluxerie.net/return-complete/1/1020020');   

        \Mail::to('fatham09@gmail.com')->send(new \App\Mail\ReturnMail($params)); */

      
        return view('dashboard.index',compact('shipment','paid_count','refund_count','ship_own','myreturn'));

       
    }
    public function ShipmentLists(Request $request){       
		$offset 	= isset($_GET['page']) ? $_GET['page'] : 0;
        $assigned   = (isset($_GET['type']) && $_GET['type'] == 'assigned') ? 1 : 0;
		$limit		= 25;
		if($offset > 1){
			$offset = ($offset - 1) * $limit;
		}else{
			$offset = 0;
		}
		$filterdata = array();
		
        $data = $this->returnFilters();
        $lists = Shipments::leftjoin('shipment_details','shipments.id','=','shipment_details.shipment_id')
                    ->leftjoin('shipment_labels','shipments.id','=','shipment_labels.shipment_id')
                    ->leftjoin('user_tasks','shipments.id','=','user_tasks.shipment_id')                   
                    ->leftjoin('users','user_tasks.user_id','=','users.id')
                    ->select(['shipments.*','shipment_details.shiping_method','shipment_details.payment_method','shipment_details.payment_status','shipment_details.customer_details','shipment_details.txn_id','users.name','users.id as user_id','users.email','user_tasks.id as assign_id']);
					
		$filters = array('status' 		=> 'shipments.status',
						 'site'			=> 'shipments.site_code',
						 'payment_type' => 'shipment_details.payment_method',
						 'orderNo' 		=> 'shipments.order_id');
		
		foreach($filters as $key => $_filter){
			if(isset($_GET[$key]) && $_GET[$key] != ''){
				if($key == 'payment_type' && $_GET[$key] == '0'){
					$lists->where('payment_status_details','pending');
				}elseif($key == 'payment_type' && $_GET[$key] == '1'){
					$lists->where('payment_status_details','payment-complete');
				}elseif($key == 'payment_type' && $_GET[$key] == '2'){
					$lists->where('payment_status_details','store-creadit')->orWhere('payment_status','2');
				}elseif($key == 'payment_type' && $_GET[$key] == '3'){
					$lists->where('payment_status_details','deduction');
				}else{
					$lists->where($_filter,$_GET[$key]);
				}
				$filterdata[$key] = $_GET[$key];
			}
		}	
        $lists->where('shipments.is_deleted','0');
        if($assigned){
            $lists->where('users.id',$this->user->id);
        }
        /*$lists->where(function($query)
            {
                $query->where('user_tasks.status','=',1)
                ->orWhere('user_tasks.id','IS NULL');
        });*/

        // $lists->where('user_tasks.status','=','1')->orWhere('user_tasks.status','IS NULL');
		//print_r($filterdata);
		
		//$sql = $lists->toSql();	
		
		//dd($sql);
		
		$paginate 	= $lists->paginate($limit);
		
        $shipments = $lists->orderBy('id', 'DESC')->offset($offset)->limit($limit)->get();

        $users = User::all();

        $type = isset($_GET['type']) ? $_GET['type'] : '';
		
		//dd($paginate);

        return view('dashboard.shipments',compact('data','shipments','paginate','filterdata','users','type'));
    }

    public function returnFilters(){           
        $data['sites'] = array(	"10" =>	"deluxerie.nl",
                                "20" =>	"deluxerie.be",
                                "30" =>	"deluxerie.de",
                                "40" =>	"deluxerie.at",
                                "50" =>	"deluxerie.fr",
                                "24" =>	"fr.deluxerie.be",
                                "60" =>	"deluxerie.dk",
                                "70" =>	"deluxerie.es",
                                "80" =>	"deluxerie.it",
                                "90" =>	"deluxerie.se",
                                "64" =>	"deluxerie.fi",
                                "74" =>	"deluxerie.pt",
                                "46" =>	"deluxerie.cz",
                                "44" =>	"deluxerie.hu",
								"53" =>	"deluxerie.ro",
								"56" =>	"deluxerie.sk");

        $data['shipment_status'] = array(0 => 'New Requests',
                                         1 => 'Waiting Action',
                                         2 => 'Shipped',
                                         3 => 'Approved',
                                         4 => 'On Hold',
                                         5 => 'Rejected');

        $data['comment_type'] = array(0 => 'New Requests',
                                      1 => 'Waiting Action',
                                      2 => 'Return Shipped',
                                      3 => 'Return Approved',
                                      4 => 'Return On Hold',
                                      5 => 'Return Rejected',
                                      6 => 'Payment Rejected');

        $data['payments']       = array(0 => 'Pending',
                                        1 => 'Completed',
                                        2 => 'Store Credit',
                                        3 => 'Refund Deduction');                                 

        $data['shipping'] = array('homerr' 	    => array('name' 	=> 'HOMERR',
                                                    'icon'			=> 'Homerr-Logo.png'),
                                    'ups' 	    => array('name' 	=> 'UPS',
                                                    'icon'			=> 'UPS-logo.png'),	
                                    'gls' 	    => array('name' 	=> 'GLS NL - Pick&Return',
                                                    'icon'			=> 'GLS_Logo.png'),
									'gls_hu' 	=> array('name' 	=> 'Pactic - GLS HU',
                                                    'icon'			=> 'GLS_Logo.png'),
                                    'gls_return'=> array('name' 	=> 'GLS NL - ShopReturn',
                                                    'icon'			=> 'GLS_Logo.png'),
									'gls_ro' 	=> array('name' 	=> 'Pactic - GLS RO',
                                                    'icon'			=> 'GLS_Logo.png'),				
									'gls_sk' 	=> array('name' 	=> 'Pactic - GLS SK',
                                                    'icon'			=> 'GLS_Logo.png'),								
                                    'ppl' 	    => array('name' 	=> 'Pactic - PPL CZ',
                                                    'icon'			=> 'ppl-logo.png'),
                                    'own' 	    => array('name' 	=> 'Other',
                                                    'icon'			=> ''));

                                     
        
        /*
            Create Return Statuses: New Requests, Waiting Action (this status means that delivered to our warehouse - so we should add this only if its possible the automatically update status of the delivered returned package by tracking), Shipped (This status means that customer shipped their return package by the our return label so its same as the previous one.), Approved, On Hold, Rejected
        */
        return $data;
    }

    public function ViewShipment($shipment_id){
        $data       =   $this->returnFilters();
        $shipment   =   Shipments::find($shipment_id);
        $details    =   ShipmentDetails::where('shipment_id',$shipment_id)->first();
		$pickup 	=  array("_gls","gls","gls_hu","gls_ro","gls_sk");
        
        if(empty($details))
			return Redirect::to('/dashboard/');
        $items      =   ShipmentItems::where('shipment_id',$shipment_id)->get();
        $address    =   ShipmentAddress::where('shipment_id',$shipment_id)->get()->first();
        $label      =   ShipmentLabels::where('shipment_id',$shipment_id)->first();
        $home       =   new HomeController();
        $defaults	= $home->PageDefaults();
        $currentTime = Carbon::now();
        $logs       = PortalLogs::where('source_id',$shipment_id)
					  ->leftjoin('users','portal_logs.modified_by','=','users.id')
					  ->select(['portal_logs.*','users.name','users.email'])->get();

        $users = User::all();              
		
		
		$data['pickup']		= (in_array($details->shiping_method,$pickup) !== false) ? 1 : 0;
		
        $assgiend = UserTasks::where('shipment_id',$shipment_id)
                    ->leftjoin('users','user_tasks.user_id','=','users.id')
                    ->select(['user_tasks.*','users.name','users.id as user_id','users.email','user_tasks.id as assign_id'])->first();

        return view('dashboard.view-item',compact('shipment','details','items','address','label','data','defaults','logs','currentTime','users','assgiend'));

    }

    public  function UpdateComments(request $request)
    {
        //$status         = $request->input('status');
        $shipment_id    = $request->input('shipment_id');
        $returncomments = $request->input('return-comments');
        $options        = $this->returnFilters();
        $comment_type   = $options['comment_type'];
        $log            = new PortalLogs();
		
        $logs = array(  'source_id'     => $shipment_id,
                        'type'          => '4',
                        'modified_by'   => $this->user->id,
                        'note'          => $returncomments);        
        $res = $log->create($logs);
        Session::flash('message', 'Commenet add successfully'); 
        Session::flash('alert-class', 'alert-success'); 
        return Redirect::to('/return-item/'.$shipment_id);
       
    }


    public function DeleteRequest($shipmentId = 0){
        $log = new PortalLogs();
        if($shipmentId && $shipmentId != ''){
            $shipment       =   Shipments::find($shipmentId);
            if(!empty($shipment)){
                $shipment['is_deleted'] = 1;
                $shipment['deleted_by'] = $this->user->id;
                $shipment->save();
                $logs = array(  'source_id'     => $shipmentId,
                                'type'          => '5',
                                'modified_by'   => $this->user->id,
                                'note'          => "Return request deleted");        
                $res = $log->create($logs);
                Session::flash('message', 'Return request deleted successfully'); 
                Session::flash('alert-class', 'alert-success'); 
             
            }
        }
        return Redirect::to('/return-requests');
    }

    public function UpdateShipment(request $request){
        
        $shipment_id    = $request->input('shipment_id');
        $payment_type   = $request->input('payment_type');
      
        $shipstatus     = $request->input('status');        
        $data           = $this->returnFilters();

        $shipment       =   Shipments::find($shipment_id);
		
		
        $_Status        = $data['shipment_status'][$shipstatus];
		//  $comments       = $request->input('return-comments');
       // $pay_Status     = $data['payments'][$payment_type];

        $shipment['status'] = $shipstatus;
        $shipment['shipment_status'] = $_Status;
        $shipment->save();
        
        $details                            =   ShipmentDetails::where('shipment_id',$shipment_id)->first();

        //Log for shipment
        $log = new PortalLogs();
        $logs = array(  'source_id' => $shipment_id,
                        'type'      => '3',
                        'modified_by'  => $this->user->id,
                        'note'      => 'Return staus changed to '.$_Status);        
        $res = $log->create($logs);
		/*
        $details['payment_status']         = $payment_type;
        $details['payment_status_details'] = $pay_Status;
		
		//if($comments)
        //$details['store_note']             = $comments;
        $details->save();

        //Log for shipment
        $logs2 = array(  'source_id' => $shipment_id,
                        'type'      => '2',
                        'modified_by'  => $this->user->id,
                        'note'      => 'Return payment status changed to '.$pay_Status);
        $res = $log->create($logs2);*/

        Session::flash('message', 'Return details updated successfully'); 
        Session::flash('alert-class', 'alert-success'); 
        return Redirect::to('/return-item/'.$shipment_id);
    }

    public function assingTasks(request $request){
        $data = $request->input();
        $shipment_id = $request->input('shipment_id');
        $to_id = $request->input('to_user');
        $log = new PortalLogs();
        $assigned = UserTasks::where('shipment_id',$shipment_id)
                    ->where('status',1)->first();
        $entry = 1; 
        $_res = array('status'=> 'false','message' => 'invalid');  
        
        $new_user = User::find($to_id);
        $sentMail = 0;

        $CurrentUser = $this->user;

        if($assigned){            
            if($assigned->user_id != $to_id){
                $assigned_user = User::find($assigned['user_id']);
                $logs = array(  'source_id' => $shipment_id,
                                'type'      => '6',
                                'modified_by'  => $this->user->id);        
                if(!empty($assigned_user)){
                    $logs['note'] = 'Return requested removed from '.$assigned_user->name;
                    $res = $log->create($logs);
                }
                $assigned->status       = 1;
                $assigned->user_id      = $to_id;
                $assigned->save();
                $entry = 0;
                $logs = array(  'source_id' => $shipment_id,
                                'type'      => '6',
                                'modified_by'  => $this->user->id,
                                'note'      => 'Return requested re-assigned to '.$new_user->name);        
                $res = $log->create($logs);
                $_res = array('status'=> 'true','message' => 'updated','html' => '<a href="javascript:void(0)" class="mx-3 assign-tasks task_'.$shipment_id.'" title="Assigned to '.$new_user->name.'" data-id="'.$shipment_id.'" data-user="'.$to_id.'" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <span><i class="far fa-check-square text-success"></i></span><span class="name text-secondary text-xs">Assigned to '.$new_user->name.'</span></a>');
                $sentMail = 1;
            }else{
                $entry = 0;
            }
        }
        if($entry){
            $task_data = array('status' => 1,'user_id' => $to_id,'shipment_id' => $shipment_id);           
            UserTasks::create($task_data);
            $logs = array(  'source_id' => $shipment_id,
                                'type'      => '6',
                                'modified_by'  => $this->user->id,
                                'note'      => 'Return requested assigned to '.$new_user->name);        
            $res = $log->create($logs);
            $_res = array('status'=> 'true','message' => 'updated','html' => '<a href="javascript:void(0)" class="mx-3 assign-tasks task_'.$shipment_id.'" title="Assigned to '.$new_user->name.'" data-id="'.$shipment_id.'" data-user="'.$to_id.'" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <span><i class="far fa-check-square text-success"></i></span><span class="name text-secondary text-xs">Assigned to '.$new_user->name.'</span></a>');

            $sentMail = 1;
        }

       $title = $CurrentUser->name.' has assigned a return request case to you.';

       if($sentMail){
            $shipment       = Shipments::where('id',$shipment_id)->first()->toArray();
            $orderId        = $shipment['order_id'];
            $details = array('mail_view'=> 'emailUser', 
                                'subject'   =>  $title,
                                'title'     =>  $title,
                                'site'     =>  'https://return.deluxerie.net',
                                'body'      =>  '<p style="text-align-left">Hi '.$new_user->name.',</p><br><p>'.$CurrentUser->name.' has assigned a return request case to you. Please see the details below and click the button to check it on the portal.</p><br/>
                                <p><stonrg>Order number</strong>: '.$orderId.'</p>',                               
                                'url'       => '',
                                'button'    => 'SEE DETAILS',     
                                'button1'    => 'DETAYLARI GÖR',    
                                'body1'      =>  '<p style="text-align-left">Merhaba '.$new_user->name.',</p><br><p>'.$CurrentUser->name.' size bir iade talebi atadı. Lütfen aşağıdaki ayrıntılara bakın ve portalda kontrol etmek için butona tıklayın..</p><br/>
                                <p><stonrg>Sipariş no</strong>: '.$orderId.'</p>',                           
                                'link'      => 'https://return.deluxerie.net/return-item/'.$shipment_id);   

                $to_email = $new_user->email;		
                //print_r($details); die;     		
                //$to_email = 'fatham09@gmail.com';
                \Mail::to($to_email)->send(new \App\Mail\ReturnMail($details)); 

       }
       echo json_encode($_res); 
	   die;
    }


    public function DashboardSettings(request $request){    
      
        // $_languages  = new languages();
         $settings      = settings::all();
 
         return view('dashboard.settings',compact('settings'));
 
    }

    public function LanguageSettings(request $request){        
        $settings   = array();
       // $_languages  = new languages();
        $languages      = languages::all();

        return view('dashboard.languages',compact('languages'));

    }

    public function AddLanguage(request $request){
        $languages      = new languages();
        $language_id    = $request->input('language_id');
        $language_name  = $request->input('language_name');
        $language_code  = $request->input('language_code');
        $_language = array( 'language_code' => $language_code,
                            'language_name' => $language_name
                        );
        $languages->language_code = $language_code;
        $languages->language_name = $language_name;        
        if($language_id != '')
            $languages->id = $language_id;

        $res = $languages->save($_language);

        Session::flash('message', 'Language Added successfully'); 
        Session::flash('alert-class', 'alert-success'); 
        return Redirect::to('/languages/?status=1');

    }

    public static function LanguageKeyInsert(request $request){
        $language     = new Language_keys();
        $language->language_index = isset($_REQUEST['language_index']) ? $_REQUEST['language_index'] : '';
        $language->description = isset($_REQUEST['description']) ? $_REQUEST['description'] : '';
        $language->save();
        return Redirect::to('/languages/keys');
    }


    

   

    public function LanguageIndex(request $request){    
        $settings   = array();       
        $languages      = Language_keys::all();
        return view('dashboard.languagekeys',compact('languages'));
    }

    public function LanguageUpdate(request $request){
        $language_key = isset($_REQUEST['language_key']) ? $_REQUEST['language_key'] : null;
        $language_id = isset($_REQUEST['language_id']) ? $_REQUEST['language_id'] : 0;
        $index_id = isset($_REQUEST['index_id']) ? $_REQUEST['index_id'] : 0;
        $location_index = isset($_REQUEST['location_index']) ? $_REQUEST['location_index'] : 0;
       
        if(!empty($location_index)){            
            foreach($location_index as $langKey => $_lang){
                
                if(is_array($_lang)){
                    $_lang = json_encode($_lang);
                }


                $saved_index                = isset($index_id[$langKey]) ? $index_id[$langKey] : 0;

                if($saved_index){
                    //$details->id  = $saved_index;
                    $details = language_details::find($saved_index);
                }else{
                    $details = new language_details();
                }

                $details->language_key      = isset($language_key[$langKey]) ? $language_key[$langKey] : $langKey;
                $details->language_string   = $_lang;
                $details->language_id       = $language_id;
                
               // echo "saved_index".$saved_index; die; 
               // print_r($details);
                $details->save();
            }
        }
        $_res = array('status'=> 'true','message' => ''); 
        echo json_encode($_res);
        die;
    }

    public function LanguageView($_id){    
        $jsonString     = $languages = array();       
        $is_empty       = 0;
        $languagekeys   = Language_keys::all();
        $jsonString     = array();
        $count_languages       =  language_details::leftjoin('languages','language_details.language_id','=','languages.id')->leftjoin('language_keys','language_details.language_key','=','language_keys.id')->where('languages.id',$_id)->select(['language_details.*','languages.language_code','languages.language_name','language_keys.language_index'])->get()->count();
        
        if(empty($count_languages)){
            
            $item_languages      = languages::where('id',$_id)->first();
            if(!empty($item_languages)){
                $code = $item_languages->language_code;
                if(File::exists(base_path('resources/lang/'.$code.'.json'))){
                    $jsonString = file_get_contents(base_path('resources/lang/'.$code.'.json'));
                    $jsonString = json_decode($jsonString, true);                      
                }
            }
            $is_empty = 1;
            $languages = $languagekeys;            
        }else{

            $languages        =  Language_keys::leftjoin('language_details','language_keys.id','=','language_details.language_key')->leftjoin('languages','language_details.language_id','=','languages.id')->where('languages.id',$_id)->select(['language_details.*','languages.language_code','languages.language_name','language_keys.language_index'])->get();
        }       

        $lang_keys = array();
        foreach($languagekeys as $key_lang){
            $language_index = $key_lang->language_index;
            $lang_keys[$language_index]    = $key_lang;
        }
        $lang = $_id;
        return view('dashboard.languagedetails',compact('languagekeys','languages','is_empty','jsonString','lang','lang_keys'));
    }

    public function StoreSettings(){
        $_languages      = languages::all(); 
        $stores         = stores::all();
        $languages      = array();
        foreach($_languages as $lang){
            $_id            = $lang->id;
            $language_code  = $lang->language_code;
            $language_name  = $lang->language_name;
            $languages[$_id]= $lang->language_name;
        }
        return view('dashboard.stores',compact('languages','stores'));
    }
    
    public function AddNewStore(request $request){
        $store     = new stores();
        $store->site_url = isset($_REQUEST['site_url']) ? $_REQUEST['site_url'] : '';
        $store->order_prefix = isset($_REQUEST['order_prefix']) ? $_REQUEST['order_prefix'] : '';
        $store->language_id = isset($_REQUEST['language_id']) ? $_REQUEST['language_id'] : 0;
        $store->save();
        return Redirect::to('/stores');
    }

    public function ShippingMethod(){
        $_languages     = languages::all(); 
        $stores         = stores::all();
        $methods        = shipping_methods::all();
        return view('dashboard.shipping',compact('stores','methods'));
    }

    public function getAllStores(){        
        $portal_store   = array();
        $stores         = stores::all();
        if(!empty($stores)){
            foreach($stores as $_store){
                $order_prefix = $_store->order_prefix;
                $site_url = $_store->site_url; 
                $portal_store[$order_prefix] = $site_url;
            }
        }
        return $portal_store;
    }

    public function AddShippingMethod(request $request){
        $method     = new shipping_methods();
        $method->shipping_name  = isset($_REQUEST['shipping_name']) ? $_REQUEST['shipping_name'] : '';
        $method->shipping_title = isset($_REQUEST['shipping_title']) ? $_REQUEST['shipping_title'] : '';
        $method->price = isset($_REQUEST['price']) ? $_REQUEST['price'] : 0;
        if ($request->hasFile('shipping_logo')) {
            $logo = $request->file('shipping_logo');//use the method in the trait

            $_filename  =  $logo->getClientOriginalName();
            $_file      =  $logo->getPathname();

            
            $logo->move(public_path('/uploads'), $_filename);
            $path = '/uploads/'.$_filename;
            $method->shipping_logo = $path;          
        }
        $method->save();
        return Redirect::to('/shipping-method');
    }

    public function ConfigShippingMethod(){
        $our_stores = array();
        $_id        = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        $method     = shipping_methods::find($_id); 
        $all_stores = stores::all();
        $stores     = stores::leftjoin('stroe_shippings','stores.id','=','stroe_shippings.store_id')                    
                    ->where('stroe_shippings.shipping_method',$_id)->select(['stores.*','stroe_shippings.shipping_price','stroe_shippings.is_default','stroe_shippings.is_free','stroe_shippings.id as ship_id','stroe_shippings.is_active'])->get();

        if(!empty($all_stores)){
            foreach($all_stores as $store){
                $store_id = $store->id;
                $our_stores[$store_id] = $store;
            }
        }          
        return view('dashboard.store-shipping',compact('our_stores','stores','method'));
    }



    public function ShippingMethodPost(request $request){
        //$method     = new shipping_methods();
        $method_id  = isset($_REQUEST['method_id']) ? $_REQUEST['method_id'] : 0;
        $method     = shipping_methods::find($method_id);
        $method->shipping_name  = isset($_REQUEST['shipping_name']) ? $_REQUEST['shipping_name'] : '';
        $method->shipping_title = isset($_REQUEST['shipping_title']) ? $_REQUEST['shipping_title'] : '';
        $method->price = isset($_REQUEST['price']) ? $_REQUEST['price'] : 0;
        $method->status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0;
        $method->is_pickup = isset($_REQUEST['is_pickup']) ? $_REQUEST['is_pickup'] : 0;
        $method->ship_label = isset($_REQUEST['ship_label']) ? $_REQUEST['ship_label'] : 0;
        if ($request->hasFile('shipping_logo')) {
            $logo = $request->file('shipping_logo');//use the method in the trait

            $_filename  =  $logo->getClientOriginalName();
            $_file      =  $logo->getPathname();

            
            $logo->move(public_path('/uploads'), $_filename);
            $path = '/uploads/'.$_filename;
            $method->shipping_logo = $path;          
        }
        $method->save();

        $shipping_price = isset($_REQUEST['shipping_price']) ? $_REQUEST['shipping_price'] : '';
        $is_default     = isset($_REQUEST['is_default']) ? $_REQUEST['is_default'] : '';
        $is_free        = isset($_REQUEST['is_free']) ? $_REQUEST['is_free'] : '';
        $ship_id        = isset($_REQUEST['ship_id']) ? $_REQUEST['ship_id'] : '';

        $is_active        = isset($_REQUEST['is_active']) ? $_REQUEST['is_active'] : '';
       // $is_pickup        = isset($_REQUEST['is_pickup']) ? $_REQUEST['is_pickup'] : '';
        
        if(!empty($shipping_price)){
            foreach($shipping_price as $store_id => $_sip){
                $_sid = isset($ship_id[$store_id]) ? $ship_id[$store_id] : 0;
                if($_sid){
                    $_shipping     = stroe_shipping::find($_sid);                    
                }else{
                    $_shipping     = new stroe_shipping();                    
                }

                $_shipping->shipping_price = isset($shipping_price[$store_id]) ? $shipping_price[$store_id] : 0;
                $_shipping->shipping_method = $method_id;
                $_shipping->store_id = $store_id;
                $_shipping->is_default = isset($is_default[$store_id]) ? $is_default[$store_id] : 0;
                $_shipping->is_free = isset($is_free[$store_id]) ? $is_free[$store_id] : 0;
                $_shipping->is_active = isset($is_active[$store_id]) ? $is_active[$store_id] : 0;
             //   $_shipping->is_pickup = isset($is_pickup[$store_id]) ? $is_pickup[$store_id] : 0;
                $_shipping->save();
            }
        }        
        return Redirect::to('/config-stores/?id='.$method_id);
    }   


    public function UpdateStoreShipping(request $request){
        $store_id   = isset($_REQUEST['store_id']) ? $_REQUEST['store_id'] : 0;
        $store      = stores::find($store_id);
        $store->language_id     = isset($_REQUEST['language_id']) ? $_REQUEST['language_id'] : 0;
        $store->site_url        = isset($_REQUEST['site_url']) ? $_REQUEST['site_url'] : 0;
        $store->order_prefix    = isset($_REQUEST['order_prefix']) ? $_REQUEST['order_prefix'] : 0;
        $store->save();


        $shipping_price  = isset($_REQUEST['shipping_price']) ? $_REQUEST['shipping_price'] : '';
        $is_default      = isset($_REQUEST['is_default']) ? $_REQUEST['is_default'] : '';
        $is_free         = isset($_REQUEST['is_free']) ? $_REQUEST['is_free'] : '';
        $method_id       = isset($_REQUEST['method_id']) ? $_REQUEST['method_id'] : '';

        $is_active       = isset($_REQUEST['is_active']) ? $_REQUEST['is_active'] : '';
        $ship_id         = isset($_REQUEST['ship_id']) ? $_REQUEST['ship_id'] : '';
       // $is_pickup        = isset($_REQUEST['is_pickup']) ? $_REQUEST['is_pickup'] : '';


     
        if(!empty($shipping_price)){
            foreach($shipping_price as $method_id => $_sip){
                $_sid = isset($ship_id[$method_id]) ? $ship_id[$method_id] : 0;
                if($_sid){
                    $_shipping     = stroe_shipping::find($_sid);                    
                }else{
                    $_shipping     = new stroe_shipping();                    
                }


                $_shipping->shipping_price = $_sip;
                $_shipping->shipping_method = $method_id;
                $_shipping->store_id = $store_id;
                $_shipping->is_default = isset($is_default[$method_id]) ? $is_default[$method_id] : 0;
                $_shipping->is_free = isset($is_free[$method_id]) ? $is_free[$method_id] : 0;
                $_shipping->is_active = isset($is_active[$method_id]) ? $is_active[$method_id] : 0;
               // $_shipping->is_pickup = isset($is_pickup[$store_id]) ? $is_pickup[$store_id] : 0;
                $_shipping->save();
            }
        }

        return Redirect::to('/store-shipping-config/?id='.$store_id);

    }
    
    
    public function ConfigStoreShipping(){
        $our_stores     = array();
        $store_id            = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        $_languages     = languages::all();         
        $languages      = array();
        $methods        = array();
        $ship_method    = shipping_methods::all();
        foreach($_languages as $lang){
            $_id            = $lang->id;
            $language_code  = $lang->language_code;
            $language_name  = $lang->language_name;
            $languages[$_id]= $lang->language_name;
        }

        if(!empty($ship_method)){
            foreach($ship_method as $_method){
                $methods[$_method->id] = $_method;
            }
        }

        $store      = stores::find($store_id);         
        $shiping    = stores::leftjoin('stroe_shippings','stores.id','=','stroe_shippings.store_id')                    
                    ->leftjoin('shipping_methods','stroe_shippings.shipping_method','=','shipping_methods.id')                    
                    ->where('stroe_shippings.store_id',$store_id)->select(['stores.*','stroe_shippings.shipping_price','stroe_shippings.is_default','stroe_shippings.is_free','stroe_shippings.id as ship_id','shipping_methods.shipping_title','shipping_methods.shipping_title','stroe_shippings.shipping_method','stroe_shippings.is_active'])->orderBy('stroe_shippings.is_active','DESC')->get();
             
       
        return view('dashboard.store-shipping-price',compact('store','shiping','languages','methods'));
    }
}
