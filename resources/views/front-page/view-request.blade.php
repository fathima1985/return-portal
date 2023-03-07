@extends('layouts.frontend')
@section('content')	
	<div class="row section-wrapper thanks-wrapper mb-5">
		<div class="container-fluid section-form">		
			<div class="card ">
				<div class="card-body px-0 pb-0">
				<div class="alert alert-danger text-center mb-4" role="alert">{!!str_replace('{order_id}',$order_id,$lang['return_exist_message'])!!}</div>		
					<h2 class="h2 text-center mb-4">{!!str_replace('{order_id}',$order_id,$lang['return_exist_title'])!!}</h2>
					<div class="error-message alert alert-danger mt-5 mb-5" role="alert" style="display:none"></div>
					<div class="text-center d-flex justify-content-center mt-5 mb-5">
						<div class="success-image">
							<img src="/assets/images/checkmark.png" alt="thanks for payment" />
						</div>
					</div>	
					<div class="text-center text-center d-flex justify-content-center flex-wrap">
						<div class="col-md-8 col-xss-12 col-sm-12">
							<div class="address mt-3 pb-3 pt-4">{!!str_replace('{order_id}',$order_id,$lang['return_exist_content'])!!}</div>
						</div>

						<div class="col-md-8 col-xss-12 mt-5 mb-5 col-sm-12">
							<p>{!!$lang['return_exist_footer']!!}</p>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>		
@endsection