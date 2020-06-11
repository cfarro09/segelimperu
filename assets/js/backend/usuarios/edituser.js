$(document).ready(function() {
    $("#form_change_password").validate({
        rules:
        {
            password: {
                required: true
            },
            "#confirm_password": {equalTo: "#password"},
        },
        messages:
        {
             password: {
                required: "Por favor, ingrese una constraseña"
            },
             "#confirm_password": {equalTo: "Las contraseñan no coinciden"},
        },
        submitHandler: function (form) {
            $.ajax({
                url: site_url + '/usuarios/send_update_user',
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
                        toast_success('¡Muy bien!',res['msg']);
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
    $("#form_usu_info").validate({
        rules:
        {
            nombre_usu: {required: true},
            apellido_pat: {required: true},
            apellido_mat: {required: true},
            id_type: {required: true},
            nro_documento: {required: true},
            email_usu: {required: true}
        },
        messages:
        {
            nombre_usu: {required: "Por favor, ingrese su nombre"},
            apellido_pat: {required: "Por favor, ingrese su apellido paterno"},
            apellido_mat: {required: "Por favor, ingrese su apellido materno"},
            id_type: {required: "Por favor, seleccione su tipo de documento"},
            nro_documento: {required: "Por favor, ingrese el número de documento"},
            email_usu: {required: "Por favor, ingrese su email"}
        },
        submitHandler: function (form) {
            $.ajax({
                url: site_url + '/usuarios/send_update_user',
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
                        toast_success('¡Muy bien!',res['msg']);
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
    $("#form_change_estado").validate({
        submitHandler: function (form) {
            $.ajax({
                url: site_url + '/usuarios/send_update_user',
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
                        toast_success('¡Muy bien!',res['msg']);
                        if ($('#estado_usu').val() == "AC") {
                            $("#form_change_estado").find('button').removeClass('btn-success');
                            $("#form_change_estado").find('button').addClass('btn-danger');
                            $("#form_change_estado").find('button').html('INHABILITAR USUARIO')
                        }else{
                            $("#form_change_estado").find('button').removeClass('btn-danger');
                            $("#form_change_estado").find('button').addClass('btn-success');
                            $("#form_change_estado").find('button').html('HABILITAR USUARIO')
                        }
                        $('#estado_usu').val(($('#estado_usu').val() == "AC" ? "IN" : "AC"));
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
    $("#form_change_category").validate({
        rules:
        {
            tipo_usu: {required: true},
        },
        messages:
        {
            tipo_usu: {required: "Por favor, ingrese una categoria de trabajador"},
        },
        submitHandler: function (form) {
            $.ajax({
                url: site_url + '/usuarios/send_update_user',
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
                        toast_success('¡Muy bien!',res['msg']);
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
