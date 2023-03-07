require('./bootstrap');
$(document.body).ready(function(){
	$(document.body).on('change','.form-control.hygiene_seal',function(){
		console.log($(this).val());	
	});
});
