function toast_success(title,msj){
  $.toast({
        heading: title,
        text: msj,
        position: 'top-right',
        loaderBg: '#bf441d',
        icon: 'success',
        hideAfter: 3000,
        stack: 1
    });
}

function toast_error(title,msj){
  $.toast({
        heading: title,
        text: msj,
        position: 'top-right',
        loaderBg: '#bf441d',
        icon: 'error',
        hideAfter: 3000,
        stack: 1
    });
}
