@extends('layouts.user_type.auth')

@section('content')

<div>   
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0 pt-3 px-3 d-flex flex-wrap justify-content-between">
                <h6 class="mb-0 col-9">{{ __('Return Details') }}</h6>


                <div class="col-3 d-flex justify-content-end align-items-center">
                  @if(!empty($assgiend))
                      <a href="javascript:void(0)" class="mx-3 assign-tasks task_{{$assgiend->id}}" title="Assigned to {{$assgiend->name}}" data-id="{{$shipment->id}}" data-user="{{$assgiend->user_id}}" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> 
                        <span><i class="far fa-check-square text-success"></i></span>
                        <span class="name text-secondary text-xs">Assigned to {{$assgiend->name}}</span>
                      </a>
                  @else
                    <a href="javascript:void(0)" class="mx-3 assign-tasks task_{{$shipment->id}}" title="Assign task to an user" data-id="{{$shipment->id}}" data-user="0" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> 
                      <span><i class="fa fa-tasks" aria-hidden="true"></i></span>
                      <span class="name text-secondary text-xs">Assign to an user</span>                            
                    </a>
                  @endif
                </div>


            </div>            
            <div class="card-body pt-0 pb-3">
            @if($errors->any())
                  <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                      <span class="alert-text text-white">
                      {{$errors->first()}}</span>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                          <i class="fa fa-close" aria-hidden="true"></i>
                      </button>
                  </div>
              @endif
              @if(session('success'))
                  <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                      <span class="alert-text text-white">
                      {{ session('success') }}</span>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                          <i class="fa fa-close" aria-hidden="true"></i>
                      </button>
                  </div>
              @endif
            </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-8 col-12">            
            <div class="card p-3">     
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Order No</p>
                  <h6 class="text-dark mb-0">{!!$shipment['order_id']!!}</h6>   
                </div>
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Order Email</p>
                  <h6 class="text-dark mb-0">{!!$shipment['order_email']!!}</h6>   
                </div>
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Order Date</p>
                  <h6 class="text-dark mb-0">{{date('j M, Y', strtotime($shipment['order_date']))}}</h6>   
                </div>
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Return Date</p>
                  <h6 class="text-dark mb-0">{{date('j M, Y', strtotime($shipment['created_at']))}}</h6>   
                </div>
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Order Site</p>
                  <h6 class="text-dark mb-0"><a href="{{$shipment['order_site']}}" target="_blank" class="text-xs text-secondary mb-0"><span class="badge badge-sm bg-gradient-info">{{$shipment['order_site']}}<span></a></h6>   
                </div>   
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Shipping Method</p>
                  <h6 class="text-dark mb-0 text-xxs">
                    
                  {{$data['shipping'][$details['shiping_method']]['name']}}  
                  </h6>   
                </div>
                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Payment</p>
                  <h6 class="text-dark mb-0">
                       @if($details['shiping_method'] == 'own')
                          <span class="badge badge-sm text-xxs bg-gradient-info">Own</span>
                        @elseif($details['payment_method'] == 'online-payment' && $details['txn_id'] != '')
                        <span class="badge badge-sm text-xxs bg-gradient-success">MultiSafe</span>
                        @elseif($details['payment_method'] == 'online-payment' && $details['txn_id'] == '')
                        <span class="badge badge-sm text-xxs bg-gradient-danger">MultiSafe - Pending</span>
                        @elseif($details['payment_method'] == '2')
                           <span class="badge badge-sm  text-xxs bg-info">Store credit</span>	
						@elseif($details['payment_method'] == 'refund-deduction')
                          <span class="badge badge-sm  text-xxs bg-success">Refund Deduction</span>  
                        @elseif($details['shiping_method'] != 'own' && $details['txn_id'] == '')
                          <span class="badge badge-sm  text-xxs bg-gradient-danger">Pending</span>
                        @endif
                  </h6>   
                </div> 

                <div class="mb-2 d-flex align-items-center justify-content-between"> 
                  <p class="text-dark text-sm opacity-8 mb-0">Current Status</p>
                    @if($shipment['status'] == 0)
						 <span class="badge badge-sm text-xxs bg-gradient-dark">New</span>
                      @elseif($shipment['status'] == 2)
                        <span class="badge badge-sm text-xxs bg-gradient-success">Shipped</span>                      
                      @elseif($shipment['status'] == 3)
                        <span class="badge badge-sm text-xxs bg-gradient-success">Approved</span>                      
                      @elseif($shipment['status'] == 4)
                        <span class="badge badge-sm  text-xxs bg-gradient-warning">On Hold</span>  
                      @elseif($shipment['status'] == 5)
                        <span class="badge badge-sm text-xxs bg-gradient-danger">Approved</span>
                      @elseif($shipment['status'] == 1)
                        <span class="badge badge-sm text-xxs bg-gradient-info">Waiting Action</span>
                      @endif 
                </div>
            </div> 
            
             
              @foreach($items as $item)
              <div class="card p-3 mt-3 mb-3"> 
                 <a href="javascript:void(0)" class="h6 text-dark mb-0 col-12 d-flex justify-content-between product-line">{!!$item['product_sku']!!} -  {!!$item['product_title']!!} <span><i class="fa fa-angle-down"></i></span></a> 
                 <div class="content-product col-12"> 
                  <div class="d-flex col-12 px-0 p-2 align-items-center">
                      @php
                        $order_details = json_decode($item['attributes'],true);
                      @endphp
                      <div class="col-4 col-md-2">
                          <img src="{{$item['product_thumb']}}" alt="{{$item['product_sku']}}" style="max-height:150px" />
                      </div>
                      <div class="col-8 col-md-10 px-4">
                        <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                          <p class="text-dark text-sm opacity-8 mb-0 col-3">SKU</p>
                          <h6 class="text-dark mb-0 col-9"><a href="{{$shipment['order_site']}}" target="_blank" class="text-xs text-secondary mb-0">{!!$item['product_sku']!!}</a></h6>   
                        </div>
                        <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                          <p class="text-dark text-sm opacity-8 mb-0 col-3">Title</p>
                          <h6 class="text-dark mb-0 text-xs text-secondary mb-0 col-9">{!!$item['product_title']!!}</h6>   
                        </div>
                        @if(!empty($order_details))
                          @foreach($order_details as $_key => $info_order)
                          <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                            <p class="text-dark text-sm opacity-8 mb-0 col-3">{{$info_order['label']}}</p>
                            <h6 class="text-dark mb-0 text-xs text-secondary mb-0 col-9">{{$info_order['object']['name']}}</h6>   
                          </div>
                          @endforeach
                        @endif
                        <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                          <p class="text-dark text-sm opacity-8 mb-0 col-3">Qty</p>
                          <h6 class="text-dark mb-0  text-secondary mb-0 col-9">{!!$item['quantity']!!}</h6>   
                        </div>
                        <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                          <p class="text-dark text-sm opacity-8 mb-0 col-3 text-secondary mb-0">Total</p>
                          <h6 class="text-dark mb-0 col-9">{!!number_format(($item['total_tax'] + $item['total']),2)!!}</h6>   
                        </div>
                      </div>                   
                  </div>                    
                    <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                    <h6 class="text-dark mb-0  text-secondary mb-0 col-12">{!!$defaults['lang']['select_the_return_reason']!!}</h6>   
                    <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$defaults['lang']['return_reasons'][$item['return_reason']]!!}</p>
                    </div>

                    <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                    <h6 class="text-dark mb-0  text-secondary mb-0 col-12">{!!$defaults['lang']['hygiene_seal_on_the_packaging']!!}</h6>   
                    <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$defaults['lang'][$item['hygiene_seal']]!!}</p>   
                    </div>

					@if(isset($defaults['lang'][$item['is_opened']])) 
                    <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                      <h6 class="text-dark mb-0  text-secondary mb-0 col-12">{!!$defaults['lang']['have_you_opened_the_packaging']!!}</h6>   
                      <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$defaults['lang'][$item['is_opened']]!!} </p>     
                    </div>
					@endif

                    <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                    <h6 class="text-dark mb-0  text-secondary mb-0 col-12">{!!$defaults['lang'][$item['return_type']]!!}</h6>   
                    <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$defaults['lang'][$item['return_type']]!!}</p>      
                    </div>

                    <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap"> 
                      <h6 class="text-dark mb-0  text-secondary mb-0 col-12">Note</h6>   
                      <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$item['note']!!}</p>   
                    </div>
                  </div>
                </div>
              @endforeach

            @if($details['shiping_method'] == 'gls')
            <div class="card p-3 mt-3 mb-3"> 
              <h6 class="mb-3">{{ __('Pickup Details') }}</h6>
            
              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0  text-sm text-secondary mb-0 col-12">Collection Date</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['collection_date']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm  text-secondary mb-0 col-12">Full Name</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['name']!!}</p>   
                </div>
              </div>
              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0  text-sm text-secondary mb-0 col-12">Address</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['house_no']!!}, {!!$address['street']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm  text-secondary mb-0 col-12">Phone No & Ext</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['phone_no']!!} & {!!$address['extension']!!}</p>   
                </div>
              </div>

              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0  text-sm text-secondary mb-0 col-12">City</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['city']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm  text-secondary mb-0 col-12">Country</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['country']!!} & {!!$address['post_code']!!}</p>   
                </div>
              </div>
              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-12">
                  <h6 class="text-dark mb-0  text-sm text-secondary mb-0 col-12">Note</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$address['note']!!}</p>   
                </div>
              </div>
            </div>
            @endif

            <div class="card p-3 mt-3 mb-3"> 
              <h6 class="mb-3">{{ __('Customer Details') }}</h6>
              @php
                $customer_details = json_decode($details['customer_details'],true);
              @endphp

              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0  text-sm text-secondary mb-0 col-12">First Name</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['first_name']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm  text-secondary mb-0 col-12">Last Name</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['last_name']!!}</p>   
                </div>
              </div>
              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm  text-secondary mb-0 col-12">Company</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['company']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm  text-secondary mb-0 col-12">Address</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['address_1']!!}, {!!$customer_details['address_1']!!}</p>   
                </div>
              </div>
              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm text-secondary mb-0 col-12">Phone</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['phone']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm text-secondary mb-0 col-12">City</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['city']!!}</p>   
                </div>
              </div>
              <div class="mb-2 d-flex align-items-center justify-content-between  flex-wrap mb-2"> 
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm text-secondary mb-0 col-12">State</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['state']!!}</p>   
                </div>
                <div class="col-6">
                  <h6 class="text-dark mb-0 text-sm text-secondary mb-0 col-12">Postcode</h6>   
                  <p class="text-dark text-sm opacity-8 mb-0 col-12">{!!$customer_details['postcode']!!}, {!!$customer_details['country']!!}</p>   
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-12">            
            <div class="card p-3 mb-3"> 
            <h6 class="mb-3">{{ __('Update Order') }}</h6>  
            <form action="/update-shipment" method="POST" role="form text-left">
                @csrf
                <input type="hidden" name="shipment_id" value="{{$shipment['id']}}" />
                <div class="col-12">
                  <label>Return Status</label>
                  <select name="status" class="form-control select2">
                      @foreach($data['shipment_status'] as $sitekey => $site )
                      @if($shipment['status'] == $sitekey)
                            <option value="{{$sitekey}}" selected>{{$site}}</option>
                          @else  
                          <option value="{{$sitekey}}">{{$site}}</option>
                          @endif
                      @endforeach
                  </select>
                </div>

                   <!-- <div class="col-12">
                      <label>Return Comments</label>
                      <textarea class="form-control" name="return-comments">{{$details['store_note']}}</textarea>
                    </div> 

                    <div class="col-12">
                      <label>Payment Method</label>
                      <select name="payment_type" class="form-control select2">
                      @foreach($data['payments'] as $sitekey => $payment )
                          @if($details['payment_status'] == $sitekey)
                            <option value="{{$sitekey}}" selected>{{$payment}}</option>
                          @else  
                          <option value="{{$sitekey}}">{{$payment}}</option>
                          @endif
                      @endforeach
                      </select>
                    </div>-->
                    <div class="d-flex justify-content-end col-12">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Save Changes' }}</button>
                    </div>
                </form>   
            </div> 
            @if(!empty($label))
            <div class="card p-3 mb-3"> 
              <h6 class="mb-3">{{ __('ReturnLabel Details') }}</h6>  
              <div class="text-center">              
              @if($details['shiping_method'] == 'homerr' ) 
                  <a href="{!!$label['label_pdf']!!}" download><img src="{!!$label['label_pdf']!!}" alt="{{$label['TrackingCode']}}" style="width:75%; max-width:150px"/></a>
                  <a href="https://track.homerr.com/info;barcode={{$label['TrackingCode']}}" target="_blank"><strong>{!!$label['TrackingCode']!!}</stong></a>
              @elseif($details['shiping_method'] == 'ups') 
				<a href="{!!$label['label_pdf']!!}" download><img src="{!!$label['label_pdf']!!}" alt="{{$label['TrackingCode']}}" style="width:100%; max-width:100%;transform:rotate(90deg);margin:5rem 0px"/></a>
				<a href="https://www.ups.com/track?loc=en_NL&requester=QUIC&tracknum={{$label['TrackingCode']}}/trackdetails" target="_blank"><strong>{!!$label['TrackingCode']!!}</stong></a>
              @elseif($details['shiping_method'] == 'gls')
              <a href="{!!$label['label_pdf']!!}" download><strong>{!!$label['TrackingCode']!!}</stong></a> 
              @endif
            </div>
            </div>
          @endif
          <div class="card p-3 mb-3"> 
            <h6 class="mb-3">{{ __('Comments') }}</h6>              
            <div class="comment_list col-12 @if(empty($logs)) d-none @else mb-3 @endif">
              @if(!empty($logs))
                @foreach($logs as $key => $log)

                  <div role="alert" aria-live="assertive" aria-atomic="true" class="toast show mb-2" data-autohide="false">                  
                    
                    <div class="toast-header text-right">                             
                      <strong class="mr-auto text-right">{!!$log['note']!!}</strong>                   
                    </div>
                    <div class="toast-body text-xxs opacity-8">
						{!!$log->created_at->format('d-m-Y H:i:s A')!!}
						@if($log['name'] != '')
							By {{$log['name']}}	
						@endif
					</div>
                </div>
                @endforeach  
              @endif
            </div>  
            <form action="/update-comments" method="POST" role="form text-left">
                  @csrf
                  <input type="hidden" name="shipment_id" value="{{$shipment['id']}}" />
                  <div class="col-12">
                    <label>Comment type</label>
                    <select name="status" class="form-control select2">
                        @foreach($data['comment_type'] as $sitekey => $site )
                            <option value="{{$sitekey}}">{{$site}}</option>                          
                        @endforeach
                    </select>
                  </div>
                  <div class="col-12">
                    <label>Return Comments</label>
                    <textarea class="form-control" name="return-comments"></textarea>
                  </div>
                  <!--<div class="form-check form-switch mt-3">
                      <input class="form-check-input" type="checkbox" id="rememberMe" name="sendmail">
                      <label class="form-check-label" for="rememberMe">Send email to customer</label>
                    </div> --->
                  <div class="d-flex justify-content-end col-12">
                      <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Add Comment' }}</button>
                  </div>
              </form>   
          </div>
        </div>
        </div>
    </div>
</div>
@include('dashboard.modal')
@endsection