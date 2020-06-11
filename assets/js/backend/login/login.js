$(document).ready(function() {
    $("#form_login").validate({
        rules:
        {
            email_login: {
                required: true,
                email: true
            },
            password:{
                required: true
            }
        },
        messages:
        {
            email_login: {
                required: "Por favor, ingrese su correo",
                email: "Por favor, ingrese un email valido",
            },
            password: {
                required: "Por favor, ingrese su password",
            },
        },
        submitHandler: function (form) {
            validate_email(form);
        }
    });
    // $("#email_login").blur(function(event) {
    //     validate_email()
    // });
    $('#a_forgot_password').click(function(event) {
        event.preventDefault();
        $('#modal_forgotpassword').modal();
    });
    $("#form_forgot_password").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: site_url + 'security/forgot_password',
            data : {email_usu: $('#email_forgot').val()},
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
            success: function(res)
            {
                if (res) {
                    if (res['success']) {
                        $('#modal_forgotpassword').removeClass('show')
                        toast_success('¡Muy bien!',res['msg']);
                    }else{
                        if ($('#email_forgot-error').length > 0) {
                            $('#email_forgot-error').text("El correo no está registrado.");
                            $('#email_forgot-error').show();
                        }else{
                            $('#email_forgot').closest('div').append('<label id="email_forgot-error" class="error" for="email_forgot">El correo no está registrado.</label>')
                        }
                    }
                }else{
                    toast_error('¡Oh, hubo un problema!',"Vuelva a intentarlo en minutos");
                }
                $("#loading-circle-overlay").hide();
            },
            error:function(e){
                toast_error('¡Oh, hubo un problema!',"Vuelva a intentarlo en minutos");
                $("#loading-circle-overlay").hide();
            }
        });
    });
});
function validate_email($submit = false){
    $.ajax({
        url: site_url + 'login/validate_email',
        data : {email_usu: $('#email_login').val()},
        type: 'post',
        dataType: 'json',
        beforeSend: function(){
          $("#loading-circle-overlay").show();
        },
        success: function(res)
        {
            $("#loading-circle-overlay").hide();
            aux = res['success'];
            if (!aux) {
                if ($('#email_login-error').length > 0) {
                    $('#email_login-error').text("El correo no está registrado.");
                    $('#email_login-error').show();
                }else{
                    $('#email_login').closest('div').append('<label id="email_login-error" class="error" for="email_login">El correo no está registrado.</label>')
                }
            }else{
                if ($submit) {
                    $submit.submit();
                }
            }
        },
        error:function(e){
            $("#loading-circle-overlay").hide();
        }
    });
}