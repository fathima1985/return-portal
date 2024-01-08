
const pickup = ['gls','gls_hu','gls_ro','gls_sk'];
jQuery(document).ready(function($){	

	$(document.body).on('click','a.return-prev',function(e){
		history.back();
		//alert("asdasd");
	});
	
	$(document.body).on('click','.dropdown-language .dropdown-toggle',function(e){
		$('.dropdown-language .dropdown-menu').toggleClass('show');
	});

	$(document).mouseup(function(e){
    	var container = $(".dropdown-languag");    
		if (!container.is(e.target) && container.has(e.target).length === 0){
			$('.dropdown-language .dropdown-menu').removeClass('show');
		}
	});


	if (document.querySelector('.datepicker')) {

		/*const _today = new Date();		
		let _tomorrow =  new Date();    	
		_tomorrow.setDate(_today.getDate() + 1)
		minDate = _tomorrow.toISOString().substring(0,10);
		*/
		flatpickr('.datepicker', {
			dateFormat: "Y-m-d",
			minDate: new Date().fp_incr(1),
			"disable": [
				function(date) {
					// return true to disable
					return (date.getDay() === 0 || date.getDay() === 6);
		
				}
			],
			"locale": {
				"firstDayOfWeek": 1 // start week on Monday
			}
		});
	}
	
	$(document.body).on('blur','.field_collection_date',function(){
		var slected_date  = $(this).val();
		const _today = new Date();
		var varDate = new Date(slected_date); 
		let _tomorrow =  new Date();    	
		_tomorrow.setDate(_today.getDate() + 1)
		minDate = _tomorrow.toISOString().substring(0,10);
		_tomorrow.setHours(0,0,0,0);
		varDate.setHours(0,0,0,0);
		if(varDate < _tomorrow) {		
			$(this).addClass('error-field');
			$('.error-message').html(lang.validation_error).show();
			$('.field_collection_date').val(minDate);
		}else{
			$(this).removeClass('error-field');
			$('.error-message').html('').hide();
		}
	});

	if($(document.body).find('.field_collection_date').length  > 0){
		const today = new Date();
		let tomorrow =  new Date();    	
		tomorrow.setDate(today.getDate() + 1)
		minDate = tomorrow.toISOString().substring(0,10);
		$('.field_collection_date').prop('min', minDate);
	}
	
	$(document.body).on('change','input.ship_method',function(e){
		e.preventDefault();
		var shipmethod = $(this).val();		
		if($(this).is(':checked') && $(this).hasClass('method_pickup')){
			$('.gls-wrapper').fadeIn();
			$('.gls-wrapper .form-control').each(function(){
				if($(this).hasClass('required')){
					$(this).prop('required',true);
				}
			});
		}else{
			$('.gls-wrapper').fadeOut();
			$('.gls-wrapper .form-control').each(function(){
				if($(this).hasClass('required')){
					$(this).prop('required',false);
				}
			});
		}

	});

	$('form.confirm-payment').submit(function(e){		
		var currentShip = $('input.ship_method:checked').val();
		var ship_method  =  $('input.ship_method');
		method_pickup
		
		if($('input.ship_method:checked').length == 0){
			e.preventDefault();
			$('.error-message').html(lang.shipping_error).show();
			$("html, body").animate({ scrollTop: $('form.confirm-payment').offset().top });
			return false;
		}else if(ship_method.is(':checked') && ship_method.hasClass('method_pickup')){
			var error = '';
			$('.gls-wrapper .form-control').each(function(){
				var _val = $(this).val();
				if($(this).hasClass('required') && _val.trim() == ''){
					$(this).addClass('error-field');
				}else{
					$(this).removeClass('error-field');
				}
			});
			if($('.gls-wrapper .error-field').length > 0){
				$('.error-message').html(lang.validation_error).show();
				$("html, body").animate({ scrollTop: $('form.confirm-payment').offset().top });
				return false;
			}
		}
	})

	$('.gls-wrapper .form-control').blur(function(){
		var _val = $(this).val();
		if($(this).hasClass('required') && _val.trim() == ''){
			$(this).addClass('error-field');
		}else{
			$(this).removeClass('error-field');
		}
	});


	$(document.body).on('change','.form-check-input',function(){
		$(this).prop('checked',true);
		$(this).parent().parent().find('.text-input').val($(this).val());
	});

	$(document.body).on('click','.btn-summary-item',function(){
		$('.result-section .product-item').each(function(){
			$(this).removeClass('d-none');
			$(this).find('.confirm-return').removeClass('d-none');
		});
		$(this).parent().addClass('d-none');
	});

	$(document.body).on('click','.btn-summary-shipment',function(){
		$('.shipping-list .form-group').each(function(){
			$(this).removeClass('d-none').removeClass('active-selection');			
		});
		$(this).parent().addClass('d-none');
	});

	
	

	$(document.body).on('click','a.proceed-shipping',function(e){
		var order = $('#rorder_id').val();
		var orderEmail  = $('#rorder_email').val();
		var _formdata = '<input type="hidden" name="order_id" value="'+order+'" /><input type="hidden" name="order_email" value="'+orderEmail+'" />';
		var haseInput = 0;
		if($('.return-submission .form-control').length == 0){
			$('.alert-success').addClass('d-none');
			$('.alert-danger').removeClass('d-none').html('No item(s) selected for return');
			$("html, body").animate({ scrollTop: $('.section-form').offset().top });
			return false;
		}

		$('.return-submission .submission-data').append(_formdata);		
		$('.return-submission').submit();
	});

	$(document.body).on('click','a.confirm-return',function(e){
		var _parent = $(this).data('index');
		var parentWrapper = $('#item-'+_parent);
		//parentWrapper.find('.error_message').html('');
		var _error = 0;
		//alert("asdasd");
		parentWrapper.find('.form-control').each(function(){
			var _v = $(this).val();
			if((_v == '' || _v == null )&& typeof $(this).attr('required') != 'undefined'){
				$(this).addClass('validation-error');
				$(this).parent().addClass('error-group');
				_error = 1;
			}else if(_v != null && _v.trim() == '' && typeof $(this).attr('required') != 'undefined'){
				$(this).addClass('validation-error');
				$(this).parent().addClass('error-group');
				_error = 1;
			}else{
				$(this).removeClass('validation-error');
				$(this).parent().removeClass('error-group');
			}
		});
		
		parentWrapper.find('.input-radio').each(function(){
			if($(this).parent().css('display') != 'none'){
				if($(this).find('.form-check-input:checked').length == 0){
					_error = 1;
					$(this).parent().addClass('error-group');
				}else{
					$(this).parent().removeClass('error-group');
				}
			}
		});
		
		if(_error){			
			parentWrapper.find('.error-message').removeClass('d-none').html(lang.validation_error).show();
			$("html, body").animate({ scrollTop: $('.error-message').offset().top });
		}else{
			parentWrapper.find('.return-order').prop('checked',true);
			parentWrapper.find('.return-submission').hide();
			parentWrapper.find('.product-data').show().removeClass('d-none');
			parentWrapper.find('.return-item').hide();
			parentWrapper.find('.return-item.return-placed').removeClass('d-none').show();
			parentWrapper.addClass('return-requested');
			parentWrapper.find('.error-message').addClass('d-none').html('').hide();
		}

	});
	
	if($(document.body).find('.confirm-payment') && $(document.body).find('input.ship_method').length == 1){
		$('input.ship_method').trigger('click').prop('checked',true);
	}
	
	$(document.body).on('click','.product-item a.return-item',function(e){
		var _itemId 	= $(this).data('line');
		var _wrapper	= $('#item-'+_itemId);
		$('.alert-success').addClass('d-none');
		$('.alert-danger').addClass('d-none');
			
		if($(this).hasClass('return-placed')){					
			_wrapper.removeClass('return-requested');
			_wrapper.find('.product-data').show();	
			_wrapper.find('.confirm-return').addClass('d-none');
			_wrapper.find('.return-submission').html('');
			$(this).addClass('d-none');
			_wrapper.find('a.return-item').removeClass("d-none").show();
			_wrapper.find('a.return-item.edit-return').addClass("d-none");
			
		}else{	
			_wrapper.addClass('return-this');
			_wrapper.find('.product-data').hide();	

			if($(this).hasClass('edit-return')){
				_wrapper.addClass('return-this');
				_wrapper.find('.confirm-return').removeClass('d-none');
				
			}else{
				
				var formData = $('.return-form-index').html();
				_wrapper.find('.return-submission').html(formData).removeClass('d-none').show();
				_wrapper.find('.form-control').each(function(){
					var _name = $(this).attr('name');
					$(this).attr('name',_name+'['+_itemId+']');
					if($(this).hasClass('product-code')){
						$(this).val(_itemId);
					}
				});
				_wrapper.find('.form-check-input').each(function(){
					var _name = $(this).attr('name');
					$(this).attr('name',_name+'['+_itemId+']');
				});
				_wrapper.find('.confirm-return').data('index',_itemId);
			}
		}
	});

	$('.get-orders .form-control').blur(function(){
		var val = $(this).val();
		
		if($(this).attr('type') == 'email' && !ValidateEmail(val) == ''){
			//$(this).addClass('error-field');
		}else if(val.trim() == '' && $(this).hasClass('required')){
			$(this).addClass('error-field');
		}else{
			$(this).removeClass('error-field');
		}
	});

	$('#order-form').submit(function(e){
		e.preventDefault();
		/*$(this).find('.form-control').each(function(){
			if($(this).hasClass('required')){
				$(this).prop('required',true);
				var val = $(this).val();
				if(val.trim() == ''){
					$(this).addClass('error-field');
				}else{
					$(this).removeClass('error-field');
				}
			}     
		});*/
		
		
		
		if($(this).find('.error-field').length == 0){		
			var form = $("#order-form");
			//var formData = new FormData();
			//var _form = document.getElementById('order-form'); 
			//var _data = new FormData(_form);	
			var _data = form.serialize();
			var container = $('.section-form');
			var method = 'GET';
			var url = '/get-order-details';
			ajaxAction(_data, container, method, url );			    
			
			//var orderinfo = JSON.parse(_results);
			//rendor_products(orderinfo);
			
		}
	});
	//return false;    
	
	
	var current_url	= window.location.href;
	//console.log(current_url);
	if(current_url.indexOf('?doreturn') > -1 && $(document.body).find('#rorder_id').length > 0){
		
		var queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		urlPath = current_url.replace(queryString,'');
		const orderno = urlParams.get('id');		
		const orderemail = urlParams.get('email');
		$('#rorder_id').val(orderno);
		$('#rorder_email').val(orderemail);
		$('#order-form').trigger('submit');
		window.history.pushState({"html":"","pageTitle":""},"", urlPath);
	}


	var _ship_method = '';
	if($(document.body).find('#confirm_shiping_method').length > 0){
		var _confirm_status = $('#confirm_status').val();
		_ship_method = $('#confirm_shiping_method').val();
		if(_ship_method == 'gls' && _confirm_status == 1){
			//alert("you are rignt");
			setTimeout(function(){
				$('.proceed-label').trigger('click');
			},1000);
		}
	}

	$('form.create-label').submit(function(e){
		e.preventDefault();
		var form = $("#create-label");
		var _data = form.serialize();
		var container = $('.label-results');
		var method = 'GET';
		var url = $(this).attr('action');
		ajaxAction(_data, container, method, url );	
		return false;

	});

	/*$(document.body).on('click','a.proceed-label',function(e){
		e.preventDefault();
		var shipment = $(this).data('shipment');
		var order = $(this).data('order');		
	})*/

	$(document.body).on('click','a.popup-policy',function(e){
		e.preventDefault();		
		$('#triggerPopup').trigger('click');
	})
	
	
	
	$(document.body).on('change','.form-check-input.hygiene_seal',function(){
		var parent = $(this).parent().parent().parent().parent();		
		var _val = $(this).val();		
		if(_val.trim() == 'yes'){			
			parent.find('.field_package_opend').removeClass('d-none');	
		}else{
			parent.find('.field_package_opend').addClass('d-none');	
		}	
	});
	
	if($(document.body).find('.form-check-input.hygiene_seal').length > 0){
		$('.form-control.hygiene_seal').each(function(){
			var tval = $(this).val();
			var parent = $(this).parent().parent().parent().parent();	
			if(tval == 'yes'){
				parent.find('.field_package_opend').removeClass('d-none');	
			}else{
				parent.find('.field_package_opend').addClass('d-none');	
			}	
			
		});
	}
	
	
	
});

function rendor_products(_object){
	//$.each(_object,function(''))
	var _items  = _object.data.items;
	var _lineItems = '';
	var curSyb 		= _object.data.currency_symbol;
	var currency 	= _object.data.currency;
	
	$.each(_items,function(line_id,product){
		var _attr = '';
		var attributes = product.attributes;
		$.each(attributes,function(attr,dataAttr){
			if(dataAttr.object.name != 'undefined'){
				_attr += '<p class="attributes product-'+dataAttr.object.taxonomy+'"><span>'+dataAttr.label+':</span>'+dataAttr.object.name+'</p>';
			}
		});
		
		var _total 		= product.total;
		var _tax 		= product.total_tax;
		var quantity 	= product.quantity;
		var _total 		= parseFloat(_total) +  parseFloat(_tax);
		_total			= _total / quantity;
		_total			= Number.parseFloat(_total).toFixed(2);
		
		for(i = 1;i<= quantity;i++){			
			var index = line_id+'_'+i;
			_lineItems += '<div class="product-item col-md-12 d-flex mb-4 flex-wrap"  id="item-'+index+'">'+
								'<div class="product-thumb col-md-2 col-sm-3 col-xs-12 text-center">'+
									'<img src="'+product.product_thumb+'" alt="'+product.product_name+'" />'+
								'</div>'+
								'<div class="product-information col-md-10 col-sm-9 col-xs-12">'+
									'<h3 class="product-title pb-2 mb-3">'+product.product_name+'</h3>'+					
									'<div class="return-submission"></div>'+
									'<div class="product-data">'+
									'<p class="product-sku"><span>'+lang.item_sku+':</span>'+product.sku+'</p>'+_attr+
									'<p class="product-ampunt"><span>'+lang.amount_paid+':</span>'+curSyb+' '+_total+'</p>'+
									'<a href="javascript:void(0)" data-id="item-'+index+'" data-line="'+index+'" class="return-item">'+lang.return_click+'</a>'+
									'<a href="javascript:void(0)" data-id="item-'+index+'" data-line="'+index+'" class="return-item return-placed edit-return d-none">'+lang.cancel_return+'</a>'+
									'<input type="checkbox" class="d-none return_'+index+' return-order" name="return-order['+index+']" data-id="'+index+'" value="'+index+'" /></div>'+
								'</div>'+
							'<input type="hidden" name="line_id['+index+']" value="'+line_id+'"/></div>';
		}
		
	});
	//console.log(_lineItems);
	
	$('.section-form').hide();
	$('.section-form.result-wrapper').show();
	$('.section-form.result-wrapper .alert-success').removeClass('d-none');
	$('.section-form.result-wrapper #order-result').html(_lineItems);
	
}
function ValidateEmail(emailaddress){
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailaddress)){
		return true;
	}    
	return false;
}

function ajaxAction($data,$container,$method,$url){
    $.ajax({
        type: $method,
        url: $url,
        dataType: "JSON",
        data: $data,
		contentType:'application/json',
		//secure: true,
		/*headers: {
			'Access-Control-Allow-Origin': '*',
		},*/
        beforeSend:function(){
			$('.loader').addClass('loading');
			$('.alert-success').each(function(){
				$(this).addClass('d-none');
			})
			$('.alert-danger').each(function(){
				$(this).addClass('d-none');
			})
			
        },
        success: function (res){  
			var _ship_method = $('#confirm_shiping_method').val();
			
			if(typeof(res.error) == 'string'){
								
				console.log("_____12345",typeof(res.error));	
				
				$('.alert-success').each(function(){
					$(this).addClass('d-none');
				})
				$('.alert-danger').each(function(){
					$(this).removeClass('d-none');
				})
				$('.alert-danger').html(res.message).show();
				$("html, body").animate({ scrollTop: $('form.get-orders').offset().top });
				return false;
				
			}else if(typeof(res.TrackingCode) != 'undefined'){
				if(res.is_link == 'gls_return'){
					$('#create-label').addClass('d-none');
					$('.label-results').html(res.html).removeClass('d-none');
					var a = document.createElement('a');
							a.href = res.label_pdf;
							a.download = res.basename;
							document.body.appendChild(a);
							a.click();
						document.body.removeChild(a);
				}else if(_ship_method != 'gls'){
					$('#create-label').addClass('d-none');
					$('.label-results').html(res.html).removeClass('d-none');
					var a = document.createElement('a');
							a.href = res.TrackingLink;
							a.download = res.basename;
							document.body.appendChild(a);
							a.click();
						document.body.removeChild(a);
				} 						
			}else if(res.status == 'exsist'){
				window.location.href = res.redirect;
			}else if(res.status == 'true'){
				var orderinfo = res.data;				
				rendor_products(orderinfo);
				$('.order_information').html('<textarea name="orderjson" class="d-none">'+JSON.stringify(orderinfo)+'</textarea>');
			}else if(res.status == 'false'){
				
				$('.alert-success').each(function(){
					$(this).addClass('d-none');
				})
				$('.alert-danger').each(function(){
					$(this).removeClass('d-none');
				})
				$('.alert-danger').removeClass('d-none');
				$("html, body").animate({ scrollTop: $('form.get-orders').offset().top });
			}
        },
        complete:function(){
			$('.loader').removeClass('loading');
        }
    });
}