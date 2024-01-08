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
            <h6 class="mb-0">Store Lists</h6>
                <div class="col-md-6 d-flex align-items-center justify-content-end">           
            </div>
          </div>
		  <div class="add-store col-md-12 card-body">
		  <form action="/add-store" method="POST" role="form text-left">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="language_code" class="form-control-label">Store URL</label>
                                <div class="">
                                    <input required class="form-control" value="" type="text" placeholder="Store URL" id="site_url" name="site_url">
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
                                    <input required class="form-control" value="" type="text" placeholder="Order PreFix" id="order_prefix" name="order_prefix">
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
                                                <option value="{{$index}}">{!!$name!!}</option>
                                            @endforeach
                                        @endif
                                    </select>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Add Store' }}</button>
                    </div>
                </form>
	
		  </div>

          @if(!empty($stores))
            
          <div class="card-body pt-4 p-3">
            <ul class="list-group">

                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-column col-md-3"><h6 class="mb-3 text-sm">Store URL</h6></div>
                    <div class="d-flex flex-column col-md-3"><h6 class="mb-3 text-sm">Order Prefix</h6></div>
                    <div class="d-flex flex-column col-md-3"><h6 class="mb-3 text-sm">Store Language</h6></div>
                    <div class="d-flex flex-column col-md-3 text-end"><h6 class="mb-3 text-sm">&nbsp;</h6></div>
                </li>
            @foreach($stores as $key => $store)
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-column col-md-3">
                        <h6 class="mb-3 text-sm">{{$store->site_url}}</h6>                    
                    </div>
                    <div class="d-flex flex-column col-md-3"><h6 class="mb-3 text-sm">{{$store->order_prefix}}</h6> </div>
                    <div class="d-flex flex-column col-md-3"><h6 class="mb-3 text-sm">{{$languages[$store->language_id]}}</h6> </div>
                    <div class="ms-auto text-end col-md-3">                    
                        <a href="/store-shipping-config?id={{$store->id}}" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit Store">
                            <i class="fas fa-edit text-secondary" aria-hidden="true"></i>
                        </a>
                        <a href="javascript:void(0)" data-id="{{$store->id}}" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Delete Store">
                            <span><i class="cursor-pointer fas fa-trash text-secondary" aria-hidden="true"></i></span>
                        </a>
                    </div>
                </li>             
                @endforeach
                </ul>
          </div>          
          @endif
        </div>
      </div>
  </div>
@endsection

