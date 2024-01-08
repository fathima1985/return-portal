@extends('layouts.user_type.auth')

@section('content')

@php
	$url = \config('values.url'); 
@endphp

  <div class="container-fluid py-4">   
    <div class="row">
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
    <div class="row">
      <div class="col-md-12 mt-4">
        <div class="card">
          <div class="card-header pb-0 px-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Shipping Partners</h6>
                <div class="col-md-6 d-flex align-items-center justify-content-end">           
            </div>
          </div>
          <form action="/config-method" enctype="multipart/form-data" method="POST" role="form text-left">
          <div class="add-method col-md-12 card-body">
		 
                    @csrf
                    <div class="row">
                    <div class="col-md-3">
                            <div class="form-group d-flex flex-column">
                                <label for="language_id" class="form-control-label">Logo</label>
                                @if($method->shipping_logo != '')
                                <img src="{{$method->shipping_logo}}" style="width:50px; margin-bottom:1rem" />
                                @endif                                
                                <div class="">
                                       <input type="file" name="shipping_logo"> 
                                </div>

                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="shipping_name" class="form-control-label">Shipping Handle</label>
                                <div class="">
                                    <input required class="form-control"  type="text" placeholder="Handle Name" id="shipping_name" readonly value="{{$method->shipping_name}}">
                                        @error('name')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="order_prefix" class="form-control-label">Title</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input required class="form-control" value="{{$method->shipping_title}}" type="text" placeholder="Title" id="shipping_title" name="shipping_title">
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="price" class="form-control-label">Default Price</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input required class="form-control" value="{{$method->price}}" type="number" placeholder="price" id="price" name="price">
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end d-flex  align-items-center form-group col-md-3">
                            <input type="checkbox" name="status" value="1" id="status_{{$method->status}}" @if($method->status) checked @endif />
                            <label for="status_{{$method->status}}">Status</label>
                        </div> 
                        <div class="text-end d-flex  align-items-center col-3 text-end">
                            <input type="checkbox" name="is_pickup" value="1" id="is_pickup{{$method->id}}" @if($method->is_pickup) checked @endif />
                            <label for="is_pickup{{$method->id}}">Pickup</label>
                        </div>  
                        
                        <div class="text-end d-flex  align-items-center col-3 text-end">
                            <input type="checkbox" name="ship_label" value="1" id="ship_label{{$method->id}}" @if($method->is_pickup) checked @endif />
                            <label for="ship_label{{$method->id}}">Pickup</label>
                        </div>

                        

                    </div>
                    <input type="hidden" name="method_id" value="{{$method->id}}" />
                   


		  </div>


            @if(!empty($stores))            
            <div class="card-body pt-4 p-3">
              <ul class="list-group">
              @foreach($stores as $key => $store)
                  <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">                    
                      <div class="d-flex flex-row col-md-3 align-items-center justify-content-start">
                        {{$store->site_url}}
                      </div>
                      <div class="d-flex flex-column col-md-3">
                            <label class="form-control-label">Price</label>
                            <input class="form-control" value="{{$store->shipping_price}}" type="text" placeholder="Shipping Price"  name="shipping_price[{{$store->id}}]" />
                                
                      </div>


                      <div class="ms-auto text-end col-md-6 d-flex flex-row p-2">
                            <div class="text-end d-flex align-items-center col-2">
                                <input type="checkbox" name="is_active[{{$store->id}}]" value="1" id="is_active{{$store->id}}" @if($store->is_default) checked @endif />
                                <label for="is_active{{$store->id}}">Active</label>
                            </div>
                            <div class="text-end d-flex  align-items-center col-2 text-end">
                                <input type="checkbox" name="is_default[{{$store->id}}]" value="1" id="is_default_{{$store->id}}" @if($store->is_active) checked @endif />
                                <label for="is_default_{{$store->id}}">Default</label>
                            </div>    
                                             
                            <div class="ms-auto text-end flex-row col-md-3">
                                <input type="checkbox" name="is_free[{{$store->id}}]" value="1" id="is_free{{$store->id}}" @if($store->is_free) checked @endif />
                                <label for="is_free{{$store->id}}">Store Credit</label>
                            </div>
                        </div>
                      <input type="hidden" name="ship_id[{{$store->id}}]" value="{{$store->ship_id}}" />
                  </li>           
                  
                  @php
                    unset($our_stores[$store->id])         
                  @endphp
                  @endforeach

                  @if(!empty($our_stores))
                    @foreach($our_stores as $key => $store)
                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">                    
                        <div class="d-flex flex-row col-md-3 align-items-center justify-content-start">
                            {{$store->site_url}}
                        </div>
                        <div class="d-flex flex-column col-md-3">
                                <label class="form-control-label">Price</label>
                                <input required class="form-control" value="{{$store->price}}" type="text" placeholder="Shipping Price"  name="shipping_price[{{$store->id}}]" />
                                    
                        </div>
                        <div class="ms-auto text-end col-md-6 d-flex flex-row p-2">
                            <div class="text-end d-flex align-items-center col-2">
                                <input type="checkbox" name="is_active[{{$store->id}}]" value="1" id="is_active{{$store->id}}" @if($store->is_default) checked @endif />
                                <label for="is_active{{$store->id}}">Active</label>
                            </div>
                            <div class="text-end d-flex  align-items-center col-2 text-end">
                                <input type="checkbox" name="is_default[{{$store->id}}]" value="1" id="is_default_{{$store->id}}" @if($store->is_active) checked @endif />
                                <label for="is_default_{{$store->id}}">Default</label>
                            </div>                                                 
                            <div class="ms-auto text-end flex-row col-md-3">
                                <input type="checkbox" name="is_free[{{$store->id}}]" value="1" id="is_free{{$store->id}}" @if($store->is_free) checked @endif />
                                <label for="is_free{{$store->id}}">Store Credit</label>
                            </div>
                        </div>                     
                    </li>   
                    @endforeach
                  @endif
                  </ul>
            </div>          
            @endif
            <div class="card-body pt-4 p-3">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Update Details' }}</button>
                </div>
            </div>
        </form>
        </div>
      </div>
  </div>
@endsection

