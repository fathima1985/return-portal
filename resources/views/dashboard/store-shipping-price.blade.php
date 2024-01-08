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
          <form action="/config-store-method" enctype="multipart/form-data" method="POST" role="form text-left">
          <div class="add-method col-md-12 card-body">
		 
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="language_code" class="form-control-label">Store URL</label>
                                <div class="">
                                    <input required class="form-control" value="{{$store->site_url}}"" type="text" placeholder="Store URL" id="site_url" name="site_url">
                                        @error('name')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="order_prefix" class="form-control-label">Order PreFix</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input required class="form-control" value="{{$store->order_prefix}}" type="text" placeholder="Order PreFix" id="order_prefix" name="order_prefix">
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="language_id" class="form-control-label">Site Language</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <select required class="form-control" id="language_id" name="language_id">
                                        <option value="0">Select Language</option>
                                        @if(!empty($languages))
                                            @foreach($languages as $index => $name)
                                                <option value="{{$index}}" @if ($index == $store->language_id) selected @endif >{!!$name!!}</option>
                                            @endforeach
                                        @endif
                                    </select>    
                                </div>
                            </div>
                        </div>
                    </div>
		        </div>
                <input type="hidden" name="store_id" value="{{$store->id}}" />

            @if(!empty($shiping))            
            <div class="card-body pt-4 p-3">
              <ul class="list-group">
              @foreach($shiping as $key => $shiping)              
                  <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">                    
                      <div class="d-flex flex-row col-md-3 align-items-center justify-content-start">
                      <h6>{{$shiping->shipping_title}} <span class="badge bg-danger">{{$shiping->shipping_name}}</span></h6>
                      </div>
                      <div class="d-flex flex-column col-md-3">                            
                            <input class="form-control" value="{{$shiping->shipping_price}}" type="text" placeholder="Shipping Price"  name="shipping_price[{{$shiping->shipping_method}}]" />
                      </div>
                      
                      <div class="ms-auto text-end col-md-6 d-flex flex-row p-2">
                            <div class="text-end d-flex align-items-center col-2">
                                <input type="checkbox" name="is_active[{{$shiping->shipping_method}}]" value="1" id="is_active{{$shiping->shipping_method}}}" @if($shiping->is_active) checked @endif />
                                <label for="is_active{{$shiping->shipping_method}}">Active</label>
                            </div>
                            <div class="text-end d-flex  align-items-center col-2 text-end">
                                <input type="checkbox" name="is_default[{{$shiping->shipping_method}}]" value="1" id="is_default_{{$shiping->shipping_method}}" @if($shiping->is_default) checked @endif />
                                <label for="is_default_{{$shiping->shipping_method}}}">Default</label>
                            </div>    
                                          
                            <div class="ms-auto text-end flex-row col-md-3">
                                <input type="checkbox" name="is_free[{{$shiping->shipping_method}}]" value="1" id="is_free{{$shiping->shipping_method}}" @if($shiping->is_free) checked @endif />
                                <label for="is_free{{$shiping->shipping_method}}}">Store Credit</label>
                            </div>
                        </div>

                      <input type="hidden" name="method_id[{{$shiping->shipping_method}}]" value="{{$shiping->shipping_method}}" />
                      <input type="hidden" name="ship_id[{{$shiping->shipping_method}}]" value="{{$shiping->ship_id}}" /> 

                  </li>           
                  
                  @php
                    unset($methods[$shiping->shipping_method])         
                  @endphp
                  @endforeach

                  @if(!empty($methods))
                    @foreach($methods as $key => $method)
                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">                    
                        <div class="d-flex flex-row col-md-3 align-items-center justify-content-start">
                           <h6>{{$method->shipping_title}} <span class="badge bg-danger">{{$method->shipping_name}}</span></h6>
                           
                        </div>
                        <div class="d-flex flex-column col-md-3">
                                <label class="form-control-label">Price</label>
                                <input required class="form-control" value="{{$method->price}}" type="text" placeholder="Shipping Price"  name="shipping_price[{{$key}}]" />
                                    
                        </div>

                        <div class="ms-auto text-end col-md-6 d-flex flex-row p-2">
                            <div class="text-end d-flex align-items-center col-2">
                                <input type="checkbox" name="is_active[{{$key}}]" value="1" id="is_active{{$key}}}" @if($method->is_active) checked @endif />
                                <label for="is_active{{$key}}}">Active</label>
                            </div>
                            <div class="text-end d-flex  align-items-center col-2 text-end">
                                <input type="checkbox" name="is_default[{{$key}}]" value="1" id="is_default_{{$key}}}" @if($method->is_default) checked @endif />
                                <label for="is_default_{{$key}}}">Default</label>
                            </div>                                               
                            <div class="ms-auto text-end flex-row col-md-3">
                                <input type="checkbox" name="is_free[{{$key}}]" value="1" id="is_free{{$key}}}" @if($method->is_free) checked @endif />
                                <label for="is_free{{$key}}">Store Credit</label>
                            </div>
                        </div>  
                        <input type="hidden" name="method_id[{{$key}}]" value="{{$method->id}}" /> 
                        
                                             
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

