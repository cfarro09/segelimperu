/*
 * Validaciones para los inputs
 *
 * Libreria de validaciones de input tipo de texto.
 *
 * @autor Yeinso Blanco
 * @date 09/09/2016
 *
 * @version 0.1
 */


/*
 * Valida las teclas de control.
 *
 * @param string key evento keypres
 *
 * @return boolean
 */
function keyControl(key){
    var control = [0,8];
    var result;
    if(control.indexOf(key.which) >= 0){
        result = true;
    }else{
        result =  false;
    }
    return result;
}
/*
 * Valida las  entradas por teclado y las expresiones regulares
 *
 * @param string key evento keypres
 *
 * @return boolean
 */
function pathKey(eReg, key){
    var letra = String.fromCharCode(key.which);
    return keyControl(key) || eReg.test(letra);
}

/*
 * Validacion de clases de inputs
 *
 * @returns boolean
 */
(function($) {
    $(document).ready(function () {
        $(document).on('keypress', '.sololetras', function (key) {
            return pathKey(/^[a-z]| |[ñÑáéíóúÁÉÍÓÚ]$/i, key);
        });
        $(document).on('keypress', '.solonumeros', function (key) {
            return pathKey(/^[0-9]/i, key);
        });
        $(document).on('keypress', '.nospace', function (key) {
            return pathKey(/^\S/i, key);
        });
        //agregado por NN 27/09/
        $(document).on('keypress', '.cantidades', function (key) {
           return pathKey(/^[0-9]|[.]$/i, key);
        });
        $(document).on('keypress', '.correo', function (key) {
           return pathKey(/^[a-z]|[0-9]|[-_.@]/i, key);
        });
        $(document).on('keypress', '.especiales', function (key) {
           return pathKey(/^[-a-zA-Z0-9_.ñÑÁÉÍÓÚáéíóú\s]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros', function (key) {
           return pathKey(/^[-a-zA-Z0-9]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros', function (key) {
           return pathKey(/^[-a-zA-Z0-9]+$/i, key);
        });
        $(document).on('keypress', '.address', function (key) {
           return pathKey(/^[-a-zA-Z0-9_.,#\s]+$/i, key);
        });
        $(document).on('keypress', '.letras_especiales', function (key) {
           return pathKey(/^[-a-zA-Z_.,#@()\s]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros_especiales', function (key) {
           return pathKey(/^[-a-zA-Z0-9_.,#@\s]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros_coma', function (key) {
            if ($('.letrasnumeros_coma').val().length > 0) {
                var last_caracter = $('.letrasnumeros_coma').val().substring($('.letrasnumeros_coma').val().length - 1, $('.letrasnumeros_coma').val().length);
                if (last_caracter == "," && last_caracter == key.key) {
                    return false;
                }else{
                    return pathKey(/^[a-zA-Z0-9,]+$/i, key);
                }
            }else{
                    return pathKey(/^[a-zA-Z0-9]+$/i, key);
            }

        });
    });
})(jQuery);
$(document).on('contextmenu', 'input, select, textarea',function(){ return false; });
