@extends('layouts.frontend')
@section('content')	
	<div class="row section-wrapper thanks-wrapper mb-5">
		<div class="container-fluid section-form">		
			<div class="card ">
				<div class="card-body px-0 pb-0">	
					<h2 class="h2 text-center mb-4">{!!$lang['thank_you']!!}</h2>
					<div class="error-message alert alert-danger mt-5 mb-5" role="alert" style="display:none"></div>
					<div class="text-center d-flex justify-content-center mt-5 mb-5">
						<div class="success-image">
							<img src="/assets/images/checkmark.png" alt="thanks for payment" />
						</div>
					</div>	
					<div class="text-center text-center d-flex justify-content-center flex-wrap">
						<div class="col-md-8 col-xss-12 col-sm-12">
							@if($shiping_method == 'own')
								<p class="mb-0">{!!$lang['dear_customer']!!}</p>
								<p>{!!$lang['thanks_ownship']!!}</p>
								<div class="address mt-3 pb-3 pt-4">{!!$lang['return_address']!!}</div>
							@elseif($shiping_method == 'gls')	
								<p class="mb-0">{!!$lang['dear_customer']!!}</p>
								<p>{!!$lang['thanks_gls']!!}</p>
							@elseif($shiping_method == 'homerr')	
								<p class="mb-0">{!!$lang['dear_customer']!!}</p>
								<p>{!!$lang['thanks_homerr']!!}</p>										
							@else
								<p class="mb-0">{!!$lang['dear_customer']!!}</p>
								<p>{!!$lang['thanks_text']!!}</p>
							@endif
						</div>
						
							<div class="col-md-10 col-xss-12 mt-5 col-sm-12" @if($shiping_method == 'ups' or $shiping_method == 'ppl' or $shiping_method == 'homerr') style="" @else style="display:none;" @endif>
								<form action="/confirm/create-label" class="create-label" id="create-label" method="post">
									@csrf
									<input type="hidden" name="shipment" value="{{$shipment_id}}" />
									<input type="hidden" name="order" value="{{$order_id}}" />
									<input type="hidden" id="confirm_shiping_method" name="ship_method" value="{{$shiping_method}}" />
									<input type="hidden" id="confirm_status" name="ship_method" value="{{$is_complete}}" />
									<button type="submit" data-shipment="{{$shipment_id}}" data-order="{{$order_id}}" class="btn btn-primary return-next proceed-label mx-3">{!!$lang['download_label']!!}</button>
								</form>
								<div class="label-results d-none"></div>
							</div>
						
						<div class="col-md-8 col-xss-12 mt-5 mb-5 col-sm-12">
							<p>{!!$lang['thanks_note']!!}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>		
@endsection