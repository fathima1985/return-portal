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
                  <p class="text-dark text-sm opacity-8 mb-0">Unique No</p>
                  <h6 class="text-dark mb-0">{!!$shipment['payment_id']!!}</h6>   
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

            @if($details['shiping_method'] == 'gls' || $details['shiping_method'] == 'gls_hu' || $details['shiping_method'] == 'ppl')
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
			  @elseif($details['shiping_method'] == 'gls_hu' || $details['shiping_method'] == 'ppl')         
				<a href="{!!$label['label_pdf']!!}" download>
					<span class="icon" style="position: relative;width: 100%;height: 19rem;text-align: center;">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="256" height="256" viewBox="0 0 256 256" xml:space="preserve">
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
				</a>
				    <a href="{!!$label['label_pdf']!!}" target="_blank" download><strong>{!!$label['TrackingCode']!!}</stong></a>	
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