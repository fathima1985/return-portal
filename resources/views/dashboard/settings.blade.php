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
          <!--<div class="card-header pb-0 px-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Settings</h6>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
            <a class="btn bg-gradient-dark mb-0 mr-2" href="javascript:;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Option</a>
            
            </div>
          </div> --->
		  <div class="add-lanaguage col-md-12 card-body">
		  <form action="/add-settings" method="POST" role="form text-left">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="language_code" class="form-control-label">Option Name</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" value="" type="text" placeholder="Option Name" id="option_name" name="option_name">
                                        @error('name')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="language_name" class="form-control-label">Option Value</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" value="" type="text" placeholder="Option Value" id="option_value" name="option_value">
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="language_id" value="" />
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Add Option' }}</button>
                    </div>
                </form>
	
		  </div>

          @if(!empty($settings))
            
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
            @foreach($settings as $key => $setting)
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-column">
                        <h6 class="mb-3 text-sm">{{$setting->option_name}}</h6>                    
                    </div>
                    <div class="ms-auto text-end">
                    <h6 class="mb-3 text-sm">{{$setting->option_value}}</h6>                    
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

