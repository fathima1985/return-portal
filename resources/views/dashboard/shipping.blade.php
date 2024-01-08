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
		  <div class="add-method col-md-12 card-body">
		  <form action="/add-method" enctype="multipart/form-data" method="POST" role="form text-left">
                    @csrf
                    
                    <div class="row">
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="language_id" class="form-control-label">Logo</label>
                                <div class="">
                                       <input type="file" name="shipping_logo"> 
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="shipping_name" class="form-control-label">Handle Name</label>
                                <div class="">
                                    <input required class="form-control" value="" type="text" placeholder="Handle Name" id="shipping_name" name="shipping_name">
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
                                    <input required class="form-control" value="" type="text" placeholder="Title" id="shipping_title" name="shipping_title">
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
                                    <input required class="form-control" value="" type="number" placeholder="price" id="price" name="price">
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Add Method' }}</button>
                    </div>
                </form>
	
		  </div>

          @if(!empty($methods))
            
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
            @foreach($methods as $key => $method)
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-row col-md-3 align-items-center justify-content-start">
                        @if($method->shipping_logo != '')
                            <img src="{{$method->shipping_logo}}" style="width:50px" />
                        @endif
                        @if($method->status)
                            <span class="badge bg-success">{{$method->shipping_name}}</span>
                        @else
                            <span class="badge bg-light">{{$method->shipping_name}}</span>
                        @endif

                    </div>
                    <div class="d-flex flex-column col-md-3">
                        <h6 class="mb-3 text-sm">{{$method->shipping_title}} </h6>                    
                    </div>
                    <div class="d-flex flex-column col-md-3"><h6 class="mb-3 text-sm">{{$method->price}}</h6> </div>
                    <div class="d-flex flex-column col-md-3">
                        <a href="/config-stores/?id={{$method->id}}">Config Stores</a>
                    </div>
                    <div class="ms-auto text-end col-md-3">
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

