@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 px-0">
          <div class="card mb-4">
              <div class="card-header pb-3">              
              <form class="filter_shipment col-12" action="" method="get">     
                <input type="hidden" name="type" value="{{$type}}" />            
                <div class="card-filters d-flex justtidy-content-end align-items-center">
                    <div class="col-12 col-md-3">
                      <label>Order Site</label>
                      <select name="site" class="form-control select2">
						<option value="">Select</option>
                          @foreach($data['sites'] as $sitekey => $site )
                            <option value="{{$sitekey}}" @if(isset($filterdata['site']) && $filterdata['site'] == $sitekey) selected @endif>{{$site}}</option>
                          @endforeach
                      </select>
                    </div>
                
                    <div class="col-12 col-md-2 px-2">
                      <label>Return Status</label>
                      <select name="status" class="form-control select2">
						            <option value="">Select</option>
                         @foreach($data['shipment_status'] as $sitekey => $site )
                            <option value="{{$sitekey}}" @if(isset($filterdata['status']) && $filterdata['status'] == $sitekey) selected @endif>{{$site}}</option>
                          @endforeach
                      </select>
                    </div>

                    <div class="col-12 col-md-2 px-2">
                      <label>Payment Method</label>
                      <select name="payment_type" class="form-control select2">
						          <option value="">Select</option>
                      @foreach($data['payments'] as $sitekey => $payment )
                          
                          <option value="{{$sitekey}}" @if(isset($filterdata['payment_type']) && $filterdata['payment_type'] == $sitekey) selected @endif>{{$payment}}</option>
                          
                      @endforeach
                      </select>
                    </div>
					 <div class="col-12 col-md-3 px-2">
                      <label>Search By Order</label>
					  <input type="text" class="form-control" placeholder="Order No" value="@if(isset($filterdata['orderNo'])){{$filterdata['orderNo']}}@endif" name="orderNo" />
                    </div>

                    <div class="col-12 col-md-2 px-2">
                        <label class="col-12">&nbsp;</label>
                        <button type="submit" class="btn bg-gradient-danger btn-md mt-4 mb-4">{{ 'Search  ' }}</button>
                    </div>
                
                    <!--<div class="col-12 col-md-3 px-2">
                      <label>Payment Method</label>
                      <select name="payment_type" class="form-control select2">
                      @foreach($data['sites'] as $sitekey => $site )
                            <option value="{{$sitekey}}">{{$site}}</option>
                          @endforeach
                      </select>
                    </div>-->                    
                </div>
              </form>
            </div>
          </div>
        </div>  
        <div class="col-12 px-0">
          <div class="card mb-4">            
            <div class="card-body px-0 pt-0 pb-2">              
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">OrderID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Site</th>
                     
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Return Method</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Return Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Payment Status</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(!empty($shipments))
                      @foreach($shipments as $key => $shipment)
                    <tr class="shipment_{{$shipment->id}}">
                      <td class="align-middle text-center"><p class="text-xs text-secondary mb-0">
                      <a href="{{ route('return-item',$shipment['id']) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user" target="_blank">{{$shipment['order_id']}}</a></p></td>
                      <td>
                        <div class="d-flex px-2 py-1">                          
                          <div class="d-flex flex-column justify-content-center">
							@php
								$customer = json_decode($shipment['customer_details'],true);
							@endphp
						
                            <h6 class="mb-0 text-xs">{{$customer['first_name']}} {{$customer['last_name']}}</h6>
                            <p class="text-xs text-secondary mb-0">{{$shipment['order_email']}}</p>
                          </div>
                        </div>
                      </td>
                      <td>                        
                        <a href="{{$shipment['order_site']}}" target="_blank" class="text-xs text-secondary mb-0"><span class="badge badge-sm bg-gradient-info">{{$shipment['order_site']}}<span></a>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{date('j M, Y', strtotime($shipment['created_at']))}}</span>
                       
                      </td>
                      <td class="align-middle text-center">   
                      <span class="text-secondary text-xs font-weight-bold">
						@php
							$shipName = $shipment['shiping_method']
						@endphp
						@if($shipName)	 
							{{$data['shipping'][$shipName]['name']}}
						@endif	
						</span>                                     
                      </td>
                      <td class="align-middle text-center">					 
                        @if($shipment['status'] == 0 )
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
                      </td>
                      <td class="align-middle text-center">  
						
                        @if($shipment['shiping_method'] == 'own')
                          <span class="badge badge-sm text-xxs bg-gradient-info">Own</span>
                        @elseif($shipment['payment_method'] == 'online-payment' && $shipment['txn_id'] != '')
                        <span class="badge badge-sm text-xxs bg-gradient-success">MultiSafe</span>
                        @elseif($shipment['payment_method'] == 'online-payment' && $shipment['txn_id'] == '')
                        <span class="badge badge-sm text-xxs bg-gradient-danger">MultiSafe</span>
                        @elseif($shipment['payment_method'] == '2')
						  <span class="badge badge-sm  text-xxs bg-info">Store credit</span>	
						@elseif($shipment['payment_method'] == 'refund-deduction')
                          <span class="badge badge-sm  text-xxs bg-success">Refund Deduction</span>
                        @elseif($shipment['txn_id'] == '')
                          <span class="badge badge-sm  text-xxs bg-gradient-danger">Pending</span>
                        @endif

                      </td>
                      <td class="align-middle text-center action-items">
                       <a href="/delete-request/{{$shipment->id}}" class="delete-shipment mx-3" data-bs-toggle="tooltip" data-bs-original-title="Delete this return request" data-id="{{$shipment->id}}" > 
                          <span>
                              <i class="cursor-pointer fas fa-trash text-secondary"></i>
                          </span>
                        </a>
                        
                        @if($shipment->assign_id)
                          <a href="javascript:void(0)" class="mx-3 assign-tasks task_{{$shipment->id}}" title="Assigned to {{$shipment->name}}" data-id="{{$shipment->id}}" data-user="{{$shipment->user_id}}" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> 
                            <span>
                            <i class="far fa-check-square text-success"></i>
                            </span>
                          </a>
                        @else
                          <a href="javascript:void(0)" class="mx-3 assign-tasks task_{{$shipment->id}}" title="Assign task to an user" data-id="{{$shipment->id}}" data-user="0" data-bs-toggle="modal" data-bs-target="#staticBackdrop"> 
                            <span><i class="fa fa-tasks" aria-hidden="true"></i></span>                            
                          </a>
                        @endif
                      </td>
                     <!-- <td class="align-middle">
                        <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                          Edit
                        </a>
                      </td> -->
                    </tr>
                      @endforeach
                    @endif
                  </tbody>
                </table>
				<div class="pagination">{!!$paginate!!}</div>
              </div>
            </div>
          </div>
        </div>
      </div>      
    </div>
  </main>
  @include('dashboard.modal')
  @endsection
