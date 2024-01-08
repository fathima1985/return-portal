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
            <h6 class="mb-0">Language List</h6>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
            <a class="btn bg-gradient-dark mb-0 mr-2" href="javascript:;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Language</a>
            <a class="btn bg-gradient-dark mb-0 ms-1" href="/languages/keys">Language Keys</a>
            </div>
          </div>
		  <div class="add-lanaguage col-md-12 card-body">
		  <form action="/add-language" method="POST" role="form text-left">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="language_code" class="form-control-label">Language Code</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" value="" type="text" placeholder="Language Code" id="language_code" name="language_code">
                                        @error('name')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="language_name" class="form-control-label">Language Name</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" value="" type="text" placeholder="Language Name" id="language_name" name="language_name">
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="language_id" value="" />
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Add' }}</button>
                    </div>
                </form>
	
		  </div>

          @if(!empty($languages))
            
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
            @foreach($languages as $key => $language)
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">{{$language->language_name}} ({{$language->language_code}})</h6>                    
                    </div>
                    <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-refresh me-2"></i>Status</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="/languages/{{$language->id}}"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>View Details</a>
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

