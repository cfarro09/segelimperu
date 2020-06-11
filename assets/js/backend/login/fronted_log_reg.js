$(document).ready(function() {
    if ($(window).width() > 768) {
        var div_register = $('#div_register');
        var div_login = $('#div_login');
        var back_login = $('#back_login');
        var back_register = $('#back_register');

        $('#redirect_login').on('click', function (e) {
            e.preventDefault();
            back_register.show('slow/400/fast');
            div_register.show('slow/400/fast');
            div_login.hide('slow/400/fast');
            back_login.hide('slow/400/fast');
        })
        $('#redirect_register').on('click', function (e) {
            e.preventDefault();
            back_login.show('slow/400/fast');
            div_login.show('slow/400/fast');
            div_register.hide('slow/400/fast');
            back_register.hide('slow/400/fast');
        })
    }else{
        $('#div_login').attr( "class", "col-12");
        $('#div_register').attr( "class", "col-12");
        $('.account-logo-box').removeClass('pl-0 pr-0')
        $('.account-content').removeClass('pl-0 pr-0')

        $('#div_register').hide('slow/400/fast');
        $('#back_login').hide('slow/400/fast');
        $('#back_register').hide('slow/400/fast');

        $('#redirect_login').on('click', function (e) {
            e.preventDefault();
            $('#div_register').show('slow/400/fast');
            $('#div_login').hide('slow/400/fast');
        })
        $('#redirect_register').on('click', function (e) {
            e.preventDefault();
            $('#div_login').show('slow/400/fast');
            $('#div_register').hide('slow/400/fast');
        })
    }
});