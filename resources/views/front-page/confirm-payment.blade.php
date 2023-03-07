@extends('layouts.frontend')

@section('content')	
	<div class="row section-wrapper">
		<div class="container-fluid section-form">
		<form class="proceed-payment" action="complete-payment" method="post">
			@csrf
			<div class="card ">
				<div class="card-body px-0 pb-0">	
					<h2 class="h2 text-center mb-4">{!!$lang['payment_method']!!}</h2>
					<div class="error-message alert alert-danger mt-5 mb-5" role="alert" style="display:none"></div>					
					<div class="shipping-list">
						<div class="form-group py-3 px-4">								
							<input type="radio" name="pay_method" class="pay_method" value="online-payment" id="online-payment" checked="checked" />
							<label for="online-payment" class="d-flex align-items-center justify-content-between position-relative">
								<span class="ship-name">
									<h3>{!!$lang['online_payment']!!}</h3>
									<p class="">{!!$lang['online_payment_text']!!}</p>
								</span>								
							</label>							
						</div>
						@if(!$is_exchange)
						<div class="form-group py-3 px-4">								
							<input type="radio" name="pay_method" class="pay_method" value="refund-deduction" id="refund-dedection" />
							<label for="refund-dedection" class="d-flex align-items-center justify-content-between position-relative">
								<span class="ship-name">
									<h3>{!!$lang['deduction_from_the_refund']!!}</h3>
									<p class="">{!!$lang['deduction_payment_text']!!}</p>
								</span>								
							</label>							
						</div>
						@endif
					</div>
				</div>
			</div>	
			
			<div class="text-center proceed-next p-3 mt-4">
				<a href="{{url('/return-summary?return=1')}}" class="btn btn-bordered return-prev mx-3">{!!$lang['return_to_previous_step']!!}</a>
				<button href="javascript:void(0)" type="submit" class="btn btn-primary return-next proceed-payment mx-3">{!!$lang['proceed_to_the_next_step']!!}</button>
			</div>
			</form>
		</div>
	</div>		
@endsection