$(document).ready(function() {
    $('#tipo_doc').change(function(event) {
        $("#nro_doc").val("");
        $("#nro_doc").attr('maxlength',$("#tipo_doc").find(":selected").data('nro'));
        $("#nro_doc").attr('minlength',($("#tipo_doc").find(":selected").data('longitud') == "M" ? 0 : $("#tipo_doc").find(":selected").data('nro')));
    });
    $("#form_register").validate({
        rules:
        {
            names_user: {required: true},
            apellido_pa: {required: true},
            apellido_ma: {required: true},
            tipo_doc: {required: true},
            nro_doc: {
                required: true,
                maxlength: $("#tipo_doc").find(":selected").data('nro'),
                minlength: ($("#tipo_doc").find(":selected").data('longitud') == "M" ? 0 : $("#tipo_doc").find(":selected").data('nro'))
            },
            email_reg: {required: true, email: true},
            new_password: {
                required: true,
                minlength: 8,
                maxlength: 19,
                atLeastOneLowercaseLetter: true,
                atLeastOneUppercaseLetter: true,
                atLeastOneNumber: true,
                atLeastOneSymbol: true
            },
            confirm_password: {equalTo: "#new_password"},
        },
        messages:
        {
            names_user: {required: "Por favor, ingrese su nombre"},
            apellido_pa: {required: "Por favor, ingrese su apellido paterno"},
            apellido_ma: {required: "Por favor, ingrese su apellido materno"},
            tipo_doc: {required: "Por favor, seleccione su tipo de documento"},
            nro_doc: {
                required: "Por favor, ingrese el numero de su documento",
                minlength: jQuery.validator.format("Al menos {0} caracteres son requeridos!"),
                maxlength: jQuery.validator.format("{0} caracteres son demasiados!")
            },
            email_reg: {
                required: "Por favor, ingrese una constraseña",
                email: "Por favor, ingrese un email valido"
            },
            new_password: {
                required: "Por favor, ingrese una constraseña",
                minlength: jQuery.validator.format("Al menos {0} caracteres son requeridos!"),
                maxlength: jQuery.validator.format("{0} caracteres son demasiados!")
            },
            confirm_password: {equalTo: "Las contraseñan no coinciden"},
        },
        submitHandler: function (form) {
            $.ajax({
                url: site_url + '/register',
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
                        toast_success('¡Muy bien!','Se ha registrado con exito, espere que el administrador apruebe su usuario;');
                    }else if(res['msg']){
                        toast_error('¡Oh, hubo un problema!',res['msg']);
                    }else{
                        toast_error('¡Oh, hubo un error!','Vuelva a intentarlo');
                    }
                },
                error:function(e){
                    $("#loading-circle-overlay").hide();
                    toast_error('¡Oh, hubo un error!','Vuelva a intentarlo');
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
