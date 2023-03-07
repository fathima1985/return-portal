@extends('layouts.frontend')

@section('content')
	<div class="row section-wrapper pb-0">
		<div class="container-fluid row">
			<h2 class="h2 text-center">{!! $lang['return_step_title']!!}</h2>
			<div class="instrunctions pb-0">
				<ul class="list-items">
					<li>{!! $lang['step_1']!!} <div class="text-instruction">{!! $lang['step_text1']!!}</div></li>
					<li>{!! $lang['step_2']!!} <div class="text-instruction">{!! $lang['step_text2']!!}</div></li>
					<li>{!! $lang['step_3']!!} <div class="text-instruction">{!! $lang['step_text3']!!}</div></li>
					<li>{!! $lang['step_4']!!} <div class="text-instruction">{!! $lang['step_text4']!!}</div></li>
					<li>{!! $lang['step_5']!!} <div class="text-instruction">{!! $lang['step_text5']!!}</div></li>
					<li>{!! $lang['step_6']!!} <div class="text-instruction">{!! $lang['step_text6']!!}</div></li>
					<li>{!! $lang['step_7']!!} <div class="text-instruction">{!! $lang['step_text7']!!}</div></li>
					<li>{!! $lang['step_8']!!} <div class="text-instruction">{!! $lang['step_text8']!!}</div></li>
				</ul>
			</div>			
		</div>
	</div>	

	<div class="row section-wrapper">
		<div class="container-fluid row">
			<div class="contact-us text-center">
				<h3 class="h2 text-center pb-3">{!! $lang['have_questions']!!}</h3>
				<a href="javascript:void(0)" id="triggerContactPopup" data-bs-toggle="modal" data-bs-target="#ContactPopup">{!! $lang['contact_us']!!}</a>
			</div>
		</div>
	</div>
	<div class="row section-form section-wrapper pb-5 @if($retrive == 1) d-none @endif">
		<div class="container-fluid">
			<h3 class="h2 text-center pb-3">{!! $lang['find_your_order']!!}</h3>

			<div class="alert alert-success text-center d-none" role="alert">{!! $lang['order_success']!!}</div>
			<div class="alert alert-danger text-center d-none" role="alert">{!! $lang['order_failed']!!}</div>

			<form action="/get-order-details" class="get-orders text-center" method="post" id="order-form">
				@csrf
				<div class="col-md-12">
				  <div class="form-group">
					<label class="form-label">{!! $lang['order_number']!!}<span>*</span></label>
					<input type="text" placeholder="{!! $lang['order_number']!!}" id="rorder_id" name="order_id" class="form-control" required value="@if($retrive == 1){{$stored['order_id']}}@endif" />
				  </div>				
				  <div class="form-group">
					<label class="form-label">{!! $lang['email']!!} ({!! $lang['used_during_your_order']!!})<span>*</span></label>
					<input type="email" placeholder="{!! $lang['email_address']!!}" id="rorder_email" name="order-email" class="form-control" required value="@if($retrive == 1){{$stored['order_email']}}@endif"/>
				  </div>
				  <button type="submit" class="btn btn-primary btn-fetch-order" name="find-orders">{!! $lang['find_my_order']!!}</button>
				</div>
			</form>
		</div>
	</div>	
	<div class="row result-wrapper section-form section-wrapper pb-5" @if($retrive == 0) style="display:none" @endif>
		<div class="container-fluid">
			<h3 class="h2 text-center pb-3">{!! $lang['find_your_order']!!}</h3>

			<div class="alert alert-success text-center d-none" role="alert">{!! $lang['order_success']!!}</div>
			<div class="alert alert-danger text-center d-none" role="alert">{!! $lang['order_failed']!!}</div>

			<form class="return-submission" action="/confirm-shipping" method="post">
				@csrf					
				<div class="result-section pt-4" id="order-result">
					@if($retrive == 1)
					@foreach($orders['items'] as $key => $order)
						<div class="product-item col-md-12 d-flex mb-4 flex-wrap @if(!isset($product[$key])) d-none-123 @endif"  id="item-{{$key}}">
							
							<div class="product-thumb col-md-2 col-sm-3 col-xs-12 text-center">
								<img src="{{$order['product_thumb']}}" alt="{{$order['product_name']}}" />
							</div>
							<div class="product-information col-md-8 col-sm-8 col-xs-12">

								<h3 class="product-title pb-2 mb-3">{!!$order['product_name']!!}</h3>

								<div class="error-message alert alert-danger" role="alert" style="display:none"></div>
								<div class="return-submission @if(!isset($product[$key])) d-none @endif">												
								@if(isset($product[$key]))
									<input type="hidden" class="form-control product-code" name="product[{{$key}}]" value="{{$key}}" />
									@foreach($return as $rkey => $fdata)
										<div class="form-group field_{{$rkey}} @if($rkey == 'package_opend') d-none @endif">
											<label>{!!$fdata['label']!!}<span>*</span></label>
											<input type="hidden" class="form-control" name="label_{{$rkey}}[{{$key}}]" value="{{$fdata['label']}}" />
											@php
												$selected = ''
											@endphp		
											@if(isset($stored[$rkey][$key]))
												@php
													$selected = $stored[$rkey][$key]
												@endphp		
											@endif
											
											@if($rkey == 'return_type' || $rkey == 'package_opend' || $rkey == 'hygiene_seal')
												<div class="option_group input-radio">
												@foreach($fdata['options'] as $fkey => $rdata)
													<div class="form-check form-switch">
														<input name="{{$rkey}}[{{$key}}]" class="form-check-input {{$rkey}}" type="radio" id="{{$rkey}}-{{$fkey}}" value="{{$fkey}}" @if($selected == $fkey ) checked="checked" @endif>
														<label class="form-check-label" for="{{$rkey}}-{{$fkey}}">{!!$rdata!!}</label>
													</div>
												@endforeach
												</div>
											@else
												<select name="{{$rkey}}[{{$key}}]" class="form-control {{$rkey}}" required>
													
													@foreach($fdata['options'] as $fkey => $rdata)
														<option value="{{$fkey}}" @if($selected == $fkey ) selected="selected" @endif>{!!$rdata!!} </option>
													@endforeach
												</select>
											@endif
										</div>	
									@endforeach
									<div class="form-group">
										<label for="itemNote">{!!$lang['note']!!}</label>
										<textarea class="form-control" name="note[{{$key}}]" rows="3">@if(isset($stored['note'][$key])) {!!$stored['note'][$key]!!} @endif</textarea>
									</div>

									<a href="javascript:void(0)" class="confirm-return" data-index="{{$key}}">{!!$lang['save_details']!!}</a>
									@endif
								</div>
								<div class="product-data @if(isset($product[$key])) d-none @endif">
								<p class="product-sku"><span>{!!$lang['sku']!!}:</span>{!!$order['sku']!!}</p>
								@if(!empty($order['attributes']))
									@foreach($order['attributes'] as $akey => $attr)
									<p class="attributes product-{{$attr['object']['taxonomy']}}"><span>{{$attr['label']}}:</span>{!!$attr['object']['name']!!}</p>
									@endforeach
								@endif
								<p class="product-ampunt"><span>{!!$lang['amount_paid']!!}:</span>{!!$orders['currency_symbol']!!} {!!number_format(($order['total_tax'] + $order['total']),2)!!} </p>
								<a href="javascript:void(0)" data-id="item-{{$key}}" data-line="{{$key}}" class="return-item @if(isset($data['product'][$key])) edit-return @endif">{!!$lang['click_here_to_return_this_product']!!}</a>
								<a href="javascript:void(0)" data-id="item-{{$key}}" data-line="{{$key}}" class="return-item return-placed d-none">Cancel return</a>
								<input type="checkbox" class="d-none return_{{$key}} return-order" name="return-order[{{$key}}]" data-id="{{$key}}" value="{{$key}}" /></div>
							</div>
						</div>
						@endforeach

					<div class="order_information d-none">
						<textarea name="orderjson" class="d-none">{!!$stored['orderjson']!!}</textarea>
					</div>

					@endif
				</div>
				<div class="order_information d-none"></div>
				<div class="submission-data"></div>
			</form>	
			<div class="text-center p-3">
				<a href="javascript:void(0)" class="return-next proceed-shipping">{!! $lang['proceed_to_the_next_step']!!}</a>
			</div>
			
		</div>
	</div>

	<button type="button" class="btn btn-primary d-none" id="triggerPopup" data-bs-toggle="modal" data-bs-target="#policyPopup">Launch demo modal</button>


	<div class="return-form-index d-none">
		<input type="hidden" class="form-control product-code" name="product" value="" />
		<div class="error-message alert alert-danger" role="alert" style="display:none"></div>
		@foreach($return as $rkey => $fdata)
			<div class="form-group field_{{$rkey}} @if($rkey == 'package_opend') d-none @endif">
				<label>{{$fdata['label']}}<span>*</span></label>
				<input type="hidden" class="form-control" name="label_{{$rkey}}" value="{{$fdata['label']}}" />
				
				@if($rkey == 'return_type' || $rkey == 'package_opend' || $rkey == 'hygiene_seal')
					<div class="option_group input-radio">
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