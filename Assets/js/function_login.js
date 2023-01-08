$('.login-content [data-toggle="flip"]').click(function() {
	$('.login-box').toggleClass('flipped');
	return false;
});

$("#formLogin").submit(function(e){
	e.preventDefault();
	let user = $("#user").val();
	let pass = $("#password").val();
	if (user == "" || pass == ""){
		swal("Error","Complete los campos!","error");
		return false;
	}
	else {
		var form = $("#formLogin").serialize();
		// console.log(form);
		$.ajax({
		  	url: base_url+"Login/loginUser",
		  	type: 'POST',
		  	dataType: 'json',
		  	data: form,
		  	success: function(response) {
		  		if(response.status){
		  			swal("Bien hecho",response.message,"success").then(function(isConfirm){
		  				window.location = base_url+"dashboard";
		  			});
		  		}
		  		else {
		  			swal("Mal ahí pe!",response.message,"error");
		  			pass.val("");
		  		}
		  	},
		  	error: function() {
		  		swal("Error","Algo malio sal","error");
		  	}
		});
		return false;
		
	}
});