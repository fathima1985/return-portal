@extends('layouts.frontend')

@section('content')	
	<div class="row section-wrapper">
		<div class="container-fluid section-form">
		<form class="confirm-payment" action="do-payment" method="post">
			@csrf
			<div class="card ">
				<div class="card-body px-0 pb-0">	
					<h2 class="h2 text-center mb-4">{!! $lang['return_shipment_method']!!}</h2>

						<div class="error-message alert alert-danger mt-5 mb-5" role="alert" style="display:none">
						</div>
						<div class="shipping-list">
							@php $i = 0 @endphp
							@foreach($shipping as $key => $value)
							<div class="form-group py-3 px-4 shipping-{{$key}}">								
								<input type="radio" name="ship_method" class="ship_method" value="{{$key}}" id="{{$key}}" @if($data['retrive'] == 1 && $data['ship_method'] == $key) checked   @endif />
								<label for="{{$key}}" class="d-flex align-items-center justify-content-between position-relative">
									<span class="ship-name">
										@if(isset($value['icon']) && $value['icon'] != '')	
										<img src="{{url('assets/images')}}/{{$value['icon']}}" alt="{{$key}}"/>
										@endif

										<h4 class="name">{!!$value['name']!!}</h4>
										<p class="">{!!$value['instruction']!!}</p>
									</span>
									<span class="ship-price">{!!$value['rate']!!} {{$order['currency_symbol']}}</span>	
								</label>							
							</div>
							@php $i++ @endphp
							@endforeach
						</div>
						<div class="gls-wrapper" @if($data['retrive'] == 1 && $data['ship_method'] == 'gls')style="display:block" @else style="display:none" @endif>
							<h3 class="text-center mt-4 mb-5">{!!$lang['packgae_form_instruction']!!}</h3>

							<div class="error-message alert alert-danger mt-5 mb-5" role="alert" style="display:none"></div>					
							
							<div class="shipping-field row pb-3">
								<div class="col-md-12 d-flex align-items-center justify-content-between flex-wrap">	
								@foreach($formFields as $key => $_value)
									<div class="form-group col-md-4 mb-4 col-sm-6 col-xs-12">								
										<label for="{{$key}}">{{$_value['label']}}@if($_value['required'] == 1) <span>*</span>@endif</label>	
										@php
											$value = ''
										@endphp
										@if ($key == 'order_no')
											@php
												$value = $data['order_id']
											@endphp
										@elseif($key == 'email_address')
											@php
												$value = $data['order_email']
											@endphp
										@elseif(isset($data[$key]))
											@php
												$value = $data[$key]
											@endphp		
										@elseif(isset($values[$key]))
											@php
												$value = $values[$key]
											@endphp													
										@endif
									
										@if($_value['type'] == 'date')
										<input type="text" name="{{$key}}" class="datepicker form-control field_{{$key}} @if($_value['required'] == 1) required @endif"  value="{{$value}}"  onfocus="focused(this)" onfocusout="defocused(this)" @if(isset($_value['readonly'])) readonly @endif/>
										
										@else
											<input type="{{$_value['type']}}" name="{{$key}}" class="form-control field_{{$key}} @if($_value['required'] == 1) required @endif" value="{{$value}}"  @if(isset($_value['readonly'])) readonly @endif/>
										@endif
									</div>
								@endforeach
								</div>
							</div>
						</div>						
				</div>
			</div>	
			
			<div class="text-center proceed-next p-3 mt-4">
				<a href="{{url('/?return=1')}}" class="btn btn-bordered return-prev mx-3">{!!$lang['return_to_previous_step']!!}</a>
				<button href="javascript:void(0)" type="submit" class="btn btn-primary return-next proceed-payment mx-3">{!!$lang['proceed_to_the_next_step']!!}</button>
			</div>
			</form>
		</div>
	</div>		
@endsection