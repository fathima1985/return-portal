@extends('layouts.frontend')

@section('content')
<div class="row section-wrapper">
		<div class="container-fluid section-form">
		<form class="confirm-payment" action="post-summary" method="post">
			@csrf

			<div class="error-message alert alert-danger mt-5 mb-5" role="alert" style="display:none"></div>

			<div class="card ">
				<div class="card-body px-0 pb-0">	
					<h2 class="h2 text-center mb-4">{!!$lang['return_summary']!!}</h2>
					<div class="card ">
						<div class="card-body px-0 pb-0">	
							<div class="row result-wrapper section-form">
								<div class="container-fluid">				
									<div class="result-section" id="order-result">
									
									

									@foreach($orders['items'] as $_key => $order)
										@for($i = 1;$i<=$order['quantity'];$i++)
											@php
												$key = $_key.'_'.$i
											@endphp

											<div class="product-item col-md-12 d-flex mb-4 flex-wrap @if(!isset($data['product'][$key])) d-none @endif"  id="item-{{$key}}">
												<div class="product-thumb col-md-2 col-sm-3 col-xs-12 text-center">
													<img src="{{$order['product_thumb']}}" alt="{{$order['product_name']}}" />
												</div>
												
												
												<div class="product-information col-md-8 col-sm-8 col-xs-12">
													<h3 class="product-title pb-2 mb-3">{{$order['product_name']}}</h3>
													<div class="return-submission @if(!isset($data['product'][$key])) d-none @endif">
													
													@if(isset($data['product'][$key]))
														<input type="hidden" class="form-control product-code" name="product[{{$key}}]" value="{{$key}}" />
														@foreach($return as $rkey => $fdata)
															<div class="form-group field_{{$rkey}} @if($rkey == 'package_opend' && $data['hygiene_seal'][$key] != 'yes') d-none @endif">
																<label>{{$fdata['label']}}<span>*</span></label>
																<input type="hidden" class="form-control" name="label_{{$rkey}}[{{$key}}]" value="{{$fdata['label']}}" />
																@php
																	$selected = ''
																@endphp		
																@if(isset($data[$rkey][$key]))
																	@php
																		$selected = $data[$rkey][$key]
																	@endphp		
																@endif
																
																@if($rkey == 'return_type' || $rkey == 'package_opend' || $rkey == 'hygiene_seal')
																	<div class="option_group">
																	@foreach($fdata['options'] as $fkey => $rdata)
																		<div class="form-check form-switch">
																			<input name="{{$rkey}}[{{$key}}]" class="form-check-input {{$rkey}}" type="radio" id="{{$rkey}}-{{$fkey}}" value="{{$fkey}}" @if($selected == $fkey ) checked="checked" @endif>
																			<label class="form-check-label" for="{{$rkey}}-{{$fkey}}">{{$rdata}}</label>
																		</div>
																	@endforeach
																	</div>
																@else
																	<select name="{{$rkey}}[{{$key}}]" class="form-control {{$rkey}}" required>
																		@foreach($fdata['options'] as $fkey => $rdata)
																			<option value="{{$fkey}}" @if($selected == $fkey ) selected="selected" @endif>{{$rdata}} </option>
																		@endforeach
																	</select>
																@endif
															</div>	

															<input type="hidden" name="line_id[{{$key}}]" value="{{$_key}}"/>
														@endforeach
														<div class="form-group">
															<label for="itemNote">{!!$lang['note']!!}</label>
															<textarea class="form-control" name="note[{{$key}}]" rows="3">@if(isset($data['note'][$key])) {{$data['note'][$key]}} @endif</textarea>
														</div>

														<a href="javascript:void(0)" class="confirm-return d-none" data-index="{{$key}}">{!!$lang['save_details']!!}</a>
														@endif
													</div>
													<div class="product-data @if(isset($data['product'][$key])) d-none @endif">
													<p class="product-sku"><span>{!!$lang['sku']!!}:</span>{{$order['sku']}}</p>
													@if(!empty($order['attributes']))
														@foreach($order['attributes'] as $akey => $attr)
														@if(!empty($attr) && !empty($attr['object']))
															<p class="attributes product-{{$attr['object']['taxonomy']}}">
																<span>{{$attr['label']}}:</span>{{$attr['object']['name']}}
															</p>
														@endif
														@endforeach
													@endif


													@php
														$paid = ($order['total_tax'] + $order['total']) / $order['quantity']
													@endphp	
													


													<p class="product-ampunt"><span>{!!$lang['amount_paid']!!}:</span>{{$orders['currency_symbol']}} {!!number_format($paid,2)!!} </p>
													<a href="javascript:void(0)" data-id="item-{{$key}}" data-line="{{$key}}" class="return-item @if(isset($data['product'][$key])) edit-return @endif">{!!$lang['click_here_to_return_this_product']!!}</a>
													<a href="javascript:void(0)" data-id="item-{{$key}}" data-line="{{$key}}" class="return-item return-placed d-none">{!!$lang['cancel_return']!!}</a>
													<input type="checkbox" class="d-none return_{{$key}} return-order" name="return-order[{{$key}}]" data-id="{{$key}}" value="{{$key}}" />
													
												</div>
												</div>
											</div>
										@endfor
									@endforeach
									</div>
									<div class="text-center p-3 mb-3">
										<a href="javascript:void(0)" class="btn btn-bordered btn-summary-item">{!!$lang['edit_return_details']!!}</a>
									</div>
									
								</div>
							</div>
						</div>
					</div>	

					<h2 class="h2 text-center mt-5 mb-4">{!!$lang['return_shipment_method']!!}</h2>
						<div class="card ">
							<div class="card-body px-0 pb-0">	
								<div class="shipping-list">
									@foreach($shipping as $key => $value)
									<div class="form-group py-3 px-4 @if($data['ship_method'] != $key ) d-none @else active-selection @endif">								
										<input type="radio" name="ship_method" class="ship_method @if($value['pickup']) method_pickup @endif" value="{{$key}}" id="{{$key}}" @if($data['ship_method'] == $key ) checked="checked" @elseif ($value['default'] == 1)  checked="checked"  @endif  />
										<label for="{{$key}}" class="d-flex align-items-center justify-content-between position-relative">
											<span class="ship-name">
												@if($value['icon'] != '')	
												<img src="{{$value['icon']}}" alt="{{$key}}"/>
												@endif
												<h4 class="name">{!!$value['name']!!}</h4>
												<p class="">{!!$value['instruction']!!}</p>
											</span>
											<span class="ship-price">{{$value['rate']}} {{$orders['currency_symbol']}}</span>	
										</label>							
									</div>
									@endforeach
								</div>
								
								
								
								<div class="gls-wrapper" @if($is_pickup) style="display:block" @else style="display:none"  @endif>
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
												@endif			
												
												@if($_value['type'] == 'date')
												<input type="text" name="{{$key}}" class="datepicker form-control field_{{$key}} @if($_value['required'] == 1) required @endif" value="{{$value}}"  onfocus="focused(this)" onfocusout="defocused(this)" @if(isset($_value['readonly'])) readonly @endif @if(isset($_value['maxLength']))maxlength="{{$_value['maxLength']}}" @endif/>
												
												@else
													<input type="{{$_value['type']}}" name="{{$key}}" class="form-control field_{{$key}} @if($_value['required'] == 1) required @endif" value="{{$value}}" @if(isset($_value['readonly'])) readonly @endif @if(isset($_value['maxLength']))maxlength="{{$_value['maxLength']}}" @endif/>
												@endif
												
											</div>
										@endforeach
										</div>
									</div>
								</div>	
							</div>	
							<div class="text-center mt-3 p-3 mb-3">
								<a href="javascript:void(0)" class="btn btn-bordered btn-summary-shipment">{!!$lang['edit_shipment_method']!!}</a>
							</div>					
						</div>							
				</div>
			</div>	
			
			<div class="text-center proceed-next p-3 mt-4">
				<a href="{{url('/confirm-shipping?return=1')}}" class="btn btn-bordered return-prev mx-3">{!!$lang['return_to_previous_step']!!}</a>
				<button href="javascript:void(0)" type="submit" class="btn btn-primary return-next proceed-payment mx-3">{!!$lang['proceed_to_the_next_step']!!}</button>
			</div>
			</form>
		</div>
	</div>	
	
	<div class="return-form-index d-none">
		<input type="hidden" class="form-control product-code" name="product" value="" />
		@foreach($return as $rkey => $fdata)
			<div class="form-group">
				<label>{{$fdata['label']}}<span>*</span></label>
				<input type="hidden" class="form-control" name="label_{{$rkey}}" value="{{$fdata['label']}}" />
				
				@if($rkey == 'return_type' || $rkey == 'package_opend' || $rkey == 'hygiene_seal')
					<div class="option_group">
					<input type="hidden" class="form-control text-input" name="inp_{{$rkey}}" value="" />	
					@foreach($fdata['options'] as $fkey => $rdata)
						<div class="form-check form-switch">
						  <input name="{{$rkey}}" class="form-check-input {{$rkey}}" type="radio" id="{{$rkey}}-{{$fkey}}" value="{{$fkey}}">
						  <label class="form-check-label" for="{{$rkey}}-{{$fkey}}">{{$rdata}}</label>
						</div>
					@endforeach
					</div>
				@else
					<select name="{{$rkey}}" class="form-control {{$rkey}}" required>
					<option value="" disabled selected>{{$fdata['label']}}</option>
						@foreach($fdata['options'] as $fkey => $rdata)
							<option value="{{$fkey}}">{{$rdata}}</option>
						@endforeach
					</select>
				@endif
		  </div>	
		@endforeach
		<div class="form-group">
			<label for="itemNote">{!! $lang['note']!!}</label>
			<textarea class="form-control" name="note" rows="3"></textarea>			
		</div>
		<a href="javascript:void(0)" class="confirm-return" data-index="">{!! $lang['save_details']!!}</a>
	</div>
@endsection