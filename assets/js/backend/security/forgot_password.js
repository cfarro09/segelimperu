$(document).ready(function() {
	$("#form_reset_password").validate({
        rules:
        {
            password: {
                required: true,
                minlength: 8,
                maxlength: 19,
                atLeastOneLowercaseLetter: true,
                atLeastOneUppercaseLetter: true,
                atLeastOneNumber: true,
                atLeastOneSymbol: true
            },
            confirm_password: {equalTo: "#password"},
        },
        messages:
        {
             password: {
                required: "Por favor, ingrese una constraseña",
                minlength: jQuery.validator.format("Al menos {0} caracteres son requeridos!"),
                maxlength: jQuery.validator.format("{0} caracteres son demasiados!")
            },
             confirm_password: {equalTo: "Las contraseñan no coinciden"},
        },
        submitHandler: function (form) {
            $.ajax({
                url: base_url + 'security/reset_password',
                data : $(form).serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
                  $("#loading-circle-overlay").show();
                },
                success: function(res)
                {
                    $("#loading-circle-overlay").hide();
                    if (res['success']) {
                    	swal(
							'Exito!',
							res.msg,
							'success'
						).then(function(){
							location.href = base_url + "login";
						});
                    }else if(res['msg']){
                    	swal(
							'¡Oh, hubo un error',
							res.msg,
							'error'
							)
                    }else{
                    	swal(
							'¡Oh, hubo un error',
							"Hubo un error, intentenlo en un momento",
							'error'
							)
                    }
                },
                error:function(e){
                    $("#loading-circle-overlay").hide();
                    console.log("erooooooooo")
                    // toast_error('¡Oh, hubo un error!','Vuelva a intentarlo');
                }
            });
        }
    });
});
$.validator.addMethod("atLeastOneLowercaseLetter", function (value, element) {
    return this.optional(element) || /[a-z]+/.test(value);
}, "Debe tener al enos una letra minuscula");

$.validator.addMethod("atLeastOneUppercaseLetter", function (value, element) {
    return this.optional(element) || /[A-Z]+/.test(value);
}, "Debe tener al menos una letra mayuscula");

$.validator.addMethod("atLeastOneNumber", function (value, element) {
    return this.optional(element) || /[0-9]+/.test(value);
}, "Debe tener al menos un número");

$.validator.addMethod("atLeastOneSymbol", function (value, element) {
    return this.optional(element) || /[!@#,$%^&*()]+/.test(value);
}, "Debe tener al menos un simbolo");
