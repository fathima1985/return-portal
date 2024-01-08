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
            <h6 class="mb-0">Language Defenetion</h6>
            <div class="col-md-6 d-flex align-items-center justify-content-end d-none">
            <a class="btn bg-gradient-dark mb-0 mr-2" href="javascript:;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Key</a>            
            </div>
          </div>		 

          @if(!empty($languages))
            
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
            @foreach($languages as $key => $language)
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                    <div class="d-flex flex-column col-md-12">
                    @php
                        $values = ($is_empty && isset($jsonString[$language->language_index])) ? $jsonString[$language->language_index] : $language->language_string;  
                        $language_index = $language->language_index;
                        $index_id       = ($is_empty) ? $language->id : $language->language_key;   
                        $details_id     = (!$is_empty) ? $language->id : 0;                                                    
                    @endphp

                    @if (is_string($values) && is_array(json_decode($values, true)))
                        @php
                          $values =  json_decode($values, true);
                        @endphp
                    @endif
                    
                    <div class="form-group">
                        <label class="form-control-label">{{$language->language_index}}</label>
                    </div>
                   

                    <div class="ms-auto  col-md-12">    
                        <form class="save-language-info" action="" method="post">       
                            <div class="">
                                    @if(is_array($values))

                                        @foreach($values as $_key => $_value)
                                            <label class="form-control-label">{!!$_key!!}</label>
                                            <input type="text" class="form-control" type="text" placeholder="Location" id="{{$language_index}}" name="location_index[{{$language_index}}][{{$_key}}]" data-name="{{$language_index}}[{{$_key}}]" value="{!!$_value!!}"/>
                                        @endforeach
                                    @else
                                    <textarea class="form-control summernote" type="text" placeholder="Location" id="name" data-name="{{$language_index}}" name="location_index[{{$language_index}}]">{!!$values!!}</textarea>                                
                                    @endif
                                </div>

                        <div class="d-flex mt-3 text-end justify-content-end">                                
                           <input type="hidden" class="form-control" name="index_id[{{$language_index}}]" value="{{$details_id}}" />
                           <input type="hidden" class="form-control" name="language_key[{{$language_index}}]" value="{{$index_id}}" />                           
                           <button type="submit" class="save-keys btn-success"><i class="fas fa-save text-light cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Save Details"></i></button>              
                        </div>
                       <form> 
                    </div>
                </li>    
                @php
                    unset($lang_keys[$language->language_index])         
                @endphp
                @endforeach
                
                @if(!empty($lang_keys))
                    @foreach($lang_keys as $key => $language)
                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                        <div class="d-flex flex-column col-md-12">
                        @php
                            $values = ($is_empty && isset($jsonString[$language->language_index])) ? $jsonString[$language->language_index] : $language->language_string;  
                            $language_index = $language->language_index;
                            $index_id       = ($is_empty) ? $language->id : $language->language_key;   
                            $details_id     = (!$is_empty) ? $language->id : 0;                                                    
                        @endphp

                        @if (is_string($values) && is_array(json_decode($values, true)))
                            @php
                            $values =  json_decode($values, true);
                            @endphp
                        @endif
                        
                        <div class="form-group">
                            <label class="form-control-label">{{$language->language_index}}</label>
                        </div>
                    

                        <div class="ms-auto  col-md-12">    
                            <form class="save-language-info" action="" method="post">       
                                <div class="">
                                        @if(is_array($values))

                                            @foreach($values as $_key => $_value)
                                                <label class="form-control-label">{!!$_key!!}</label>
                                                <input type="text" class="form-control" type="text" placeholder="Location" id="{{$language_index}}" name="location_index[{{$language_index}}][{{$_key}}]" data-name="{{$language_index}}[{{$_key}}]" value="{!!$_value!!}"/>
                                            @endforeach
                                        @else
                                        <textarea class="form-control summernote" type="text" placeholder="Location" id="name" data-name="{{$language_index}}" name="location_index[{{$language_index}}]">{!!$values!!}</textarea>                                
                                        @endif
                                    </div>

                            <div class="d-flex mt-3 text-end justify-content-end">                                
                            <input type="hidden" class="form-control" name="index_id[{{$language_index}}]" value="{{$details_id}}" />
                            <input type="hidden" class="form-control" name="language_key[{{$language_index}}]" value="{{$index_id}}" />                           
                            <button type="submit" class="save-keys btn-success"><i class="fas fa-save text-light cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Save Details"></i></button>              
                            </div>
                        <form> 
                        </div>
                    </li>                   
                    @endforeach
                @endif
                <input type="hidden" class="form-control" name="language_id" value="{{$lang}}" /> 
             </ul>
          </div>   
            
         
            <div class="d-flex mt-3 text-end justify-content-end mb-0 p-3 ms-3 mr-3">                                
                <a href="javascript:void(0)" class="save-all-keys btn btn-dark btn-large"><i class="fas fa-save text-light cursor-pointer ms-3"></i>Save All Keys</a>              
            </div>
          @endif
        </div>
      </div>
  </div>
@endsection

