@extends('layouts.user_type.auth')

@section('content')

@php
	$url = \config('values.url'); 
@endphp

  <div class="row">
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <a href="{{route('shipment-lists')}}?type=new">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">New Return Orders</p>
                  <h5 class="font-weight-bolder mb-0">{!!$shipment!!}</h5>
                </div>
              </a>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <a href="{{route('shipment-lists')}}?type=paid">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Online Payment</p>
                  <h5 class="font-weight-bolder mb-0">{!!$paid_count!!}</h5>
                </div>
              </a> 
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($myreturn)
    <div class="col-xl-6 col-sm-6 mb-xl-0 mt-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
            <a href="{{route('shipment-lists')}}?type=assigned">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Assigned to me</p>
                <h5 class="font-weight-bolder mb-0">{!!$myreturn!!}</h5>
              </div>
            </a>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-tag text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>  
    @endif

    <div class="col-xl-6 col-sm-6 mb-xl-0 mt-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
            <a href="{{route('shipment-lists')}}?type=deduction">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Refund Deduction</p>
                <h5 class="font-weight-bolder mb-0">{!!$refund_count!!}</h5>
              </div>
            </a>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>  
    
    <div class="col-xl-6 col-sm-6 mb-xl-0 mt-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <a href="{{route('shipment-lists')}}?type=own">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Own Shipping</p>
                  <h5 class="font-weight-bolder mb-0">{!!$ship_own!!}</h5>
                </div>
              </a>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>   

  </div>
@endsection

