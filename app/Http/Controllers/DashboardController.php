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
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\PortalLogs;
use Carbon\Carbon;

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
}
