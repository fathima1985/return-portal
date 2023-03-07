<!DOCTYPE html>
@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif
@php
	$url = \config('values.url'); 
@endphp
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{$url}}/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{$url}}/assets/images/deluxerie-favicon-512x512.png">
  <title>{{$title}}</title>
  <meta name="robots" content="noindex">
  <meta name="googlebot" content="noindex">
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,400;0,500;0,600;0,700;0,800;1,300&display=swap" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{$url}}/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="{{$url}}/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{$url}}/assets/css/nucleo-svg.css" rel="stylesheet" />  
  <link id="pagestyle" href="{{$url}}/assets/css/soft-ui-dashboard.css?v=<?php echo time(); ?>" rel="stylesheet" />
  <link id="pagestyle" href="{{$url}}/assets/css/front-end.css?v=<?php echo time(); ?>" rel="stylesheet" />
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-168815167-16"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-168815167-16');
</script>
</head>
<body class="front-page">	
		<header class="header-section">
			<div class="container-header position-relative">
				<nav class="navbar navbar-expand-lg d-flex align-items-center justify-content-center">
					<div class="logo p-2">
						<a href="{{$url}}"><img src="{{$url}}/assets/images/Logo.png" alt="" /></a>
					</div>	
				</nav>
				@if($languages)		   
					<div class="dropdown-language position-absolute">
						<div class="dropdown">
						<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						{{$languages[$langCode]}}
						</button>
						<ul class="dropdown-menu">
							@foreach ($languages as $lang_code => $_lang)		
							<li class="dropdown-item"><a href="/?lang={{$lang_code}}">{{$_lang}}</a></li>						
							@endforeach
						</ul>
						</div>
					</div>
				@endif
			</div>
		</header>
		<div class="page-wrapper-container">
		<div class="page-container">
			<div class="row">
				<div class="container-fluid px-0">
					<div class="header-row d-flex align-items-center justify-content-between">
						<h1 class="h1 text-uppper">{{$lang['return_portal']}}</h1>
						<img src="{{$url}}/assets/images/Return-Portal-Head-Banne.png" alt="{{$lang['return_portal']}}" />
						
					</div>
				</div>
			</div>	
			@yield('content')
	</div>
	<div class="loader">
		<div class="loader-animation">
		<svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 45 45" stroke="#fff">
			<g fill="none" fill-rule="evenodd" transform="translate(1 1)" stroke-width="2">
				<circle cx="22" cy="22" r="6" stroke-opacity="0">
					<animate attributeName="r" begin="1.5s" dur="3s" values="6;22" calcMode="linear" repeatCount="indefinite"/>
					<animate attributeName="stroke-opacity" begin="1.5s" dur="3s" values="1;0" calcMode="linear" repeatCount="indefinite"/>
					<animate attributeName="stroke-width" begin="1.5s" dur="3s" values="2;0" calcMode="linear" repeatCount="indefinite"/>
				</circle>
				<circle cx="22" cy="22" r="6" stroke-opacity="0">
					<animate attributeName="r" begin="3s" dur="3s" values="6;22" calcMode="linear" repeatCount="indefinite"/>
					<animate attributeName="stroke-opacity" begin="3s" dur="3s" values="1;0" calcMode="linear" repeatCount="indefinite"/>
					<animate attributeName="stroke-width" begin="3s" dur="3s" values="2;0" calcMode="linear" repeatCount="indefinite"/>
				</circle>
				<circle cx="22" cy="22" r="8">
					<animate attributeName="r" begin="0s" dur="1.5s" values="6;1;2;3;4;5;6" calcMode="linear" repeatCount="indefinite"/>
				</circle>
			</g>
		</svg>
		</div>
		</div>
	</div>	
	
	<footer class="footer pt-3  ">
		<div class="container-fluid">
			<div class="row align-items-center justify-content-lg-between">
				<div class="col-lg-12 mb-lg-0 mb-2 pb-3">
					<div class="copyright text-center">
						Deluxerie ©               
					</div>
				</div>            
			</div>
		</div>
	</footer>

	<div class="modal fade" id="policyPopup" tabindex="-1" role="dialog" aria-labelledby="policyPopup" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>	
				<div class="policy-header py-3 border-bottom">
					@if($lang['return_policy_title']) 
						<h3 class="text-center">{!!$lang['return_policy_title']!!}</h3>
					@endif
					@if($lang['return_policy_date']) 
						<p class="text-center">{!!$lang['return_policy_date']!!}</p>
					@endif
				</div>			
				@if($lang['policy_content']) 
					<div class="policy-content border-bottom p-3">{!!$lang['policy_content']!!}</div>
				@endif	
				
				@if($lang['return_other_title']) 
					<h3 class="text-center  pt-3">{!!$lang['return_other_title']!!}</h3>
				@endif

				@if($lang['return_other_content']) 
					<div class="policy-content  p-3">{!!$lang['return_other_content']!!}</div>
				@endif
				
			</div>
		</div>
	</div>

	<div class="modal fade" id="ContactPopup" tabindex="-1" role="dialog" aria-labelledby="ContactPopup" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>	
				<div class="policy-header pt-4 pb-3 border-bottom">
					@if($lang['title_contactus']) 
						<h3 class="text-center">{!!$lang['title_contactus']!!}</h3>
					@endif					
				</div>	
				@if($lang['contact_text']) 
					<div class="policy-content border-bottom p-4 text-center">{!!$lang['contact_text']!!}</div>
				@endif		
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var lang = {amount_paid:"{{$lang['amount_paid']}}",
					return_click:"{{$lang['click_here_to_return_this_product']}}",
					cancel_return:"{{$lang['cancel_return']}}",
					item_sku:"{{$lang['sku']}}",
					shipping_error:"{{$lang['shipping_error']}}",
					validation_error:"{{$lang['validation_error']}}",
					error_return_type:"{{$lang['select_return_type']}}"
				};
	</script>	
   	<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
   	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.min.js" integrity="sha512-1/RvZTcCDEUjY/CypiMz+iqqtaoQfAITmNSJY17Myp4Ms5mdxPS5UV7iOfdZoxcGhzFbOm6sntTKJppjvuhg4g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js" integrity="sha512-i9cEfJwUwViEPFKdC1enz4ZRGBj8YQo6QByFTF92YXHi7waCqyexvRD75S5NVTsSiTv7rKWqG9Y5eFxmRsOn0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   	<script src="{{$url}}/assets/js/core/popper.min.js" type="text/javascript"></script>
    <script src="{{$url}}/assets/js/core/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="{{$url}}/assets/js/plugins/flatpickr.min.js" type="text/javascript"></script>
	<script src="{{$url}}/assets/js/plugins/moment.min.js" type="text/javascript"></script>
	
   <script src="{{$url}}/assets/js/frontend.js?t={{time()}}"></script>  

</body>
</html>
