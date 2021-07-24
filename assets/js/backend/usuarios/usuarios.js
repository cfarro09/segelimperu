var button;
$(document).ready(function() {
	$('.go_edit_usuario').click(function(event) {
		event.preventDefault();
		if ($('#menu_dynamic').length > 0) {
			$('#menu_dynamic').remove();
		}
		$.redirectPostmenu(site_url + "usuarios/edit_usuarios", {id_usu: $(this).data('id')}, 'target="_blank"');
	});
	$('.action_usu').click(function(event) {
		event.preventDefault();
		$action = $(this).data("action");
		button = $(this);
		if ($action == "AC") {
			$('#modal_insert_usu').modal();
			$('#id_usu').val($(this).parent().data("id"));
		}else{
			$.ajax({
				url: site_url + 'usuarios/action_suscribers',
				data : {action: $action, id_usu: $(this).parent().data("id"), tipo_usu: "US"},
				type: 'post',
				dataType: 'json',
				beforeSend: function(){
					$("#loading-circle-overlay").show();
				},
				success: function(res)
				{
					$("#loading-circle-overlay").hide();
					if (res) {
						if (res.success) {
							toast_success('¡Muy bien!',res.msg);
							if ($action == "AC") {
								button.next("a").hide('slow/400/fast', function() {
									button.next("a").remove();
								});
								button.removeClass('action_usu');
								button.find('span').text("APROBADO")
							}else{
								button.prev("a").hide('slow/400/fast', function() {
									button.prev("a").remove();
								});
								button.removeClass('action_usu');
								button.find('span').text("RECHAZADO");
							}

						}else{
							toast_error('¡Oh, hubo un error!',res.msg);
						}
					}else{
						toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
					}
				},
				error:function(e){
					$("#loading-circle-overlay").hide();
					toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
				}
			});
		}
	});
	$('#form_insert_usu').submit(function(event) {
		event.preventDefault();
		$.ajax({
			url: site_url + 'usuarios/action_suscribers',
			data : $(this).serialize(),
			type: 'post',
			dataType: 'json',
			beforeSend: function(){
				$("#loading-circle-overlay").show();
			},
			success: function(res)
			{
				$("#loading-circle-overlay").hide();
				if (res) {
					if (res.success) {
						toast_success('¡Muy bien!',res.msg);
						button.next("a").hide('slow/400/fast', function() {
							button.next("a").remove();
						});
						button.removeClass('action_usu');
					}else{
						toast_error('¡Oh, hubo un error!',res.msg);
					}
				}else{
					toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
				}
			},
			error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
			}
		});
	});
	
});
