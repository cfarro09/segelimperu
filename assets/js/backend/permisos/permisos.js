$(document).ready(function() {
	$( "#select_category" ).change(function() {
		$.ajax({
	        url: site_url + 'permisos/get_permisos_user',
	        data : {category: $(this).val()},
	        type: 'post',
	        dataType: 'json',
	        beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
	        success: function(res)
	        {
				$("#loading-circle-overlay").hide();
				if (res) {
					$('#tbody_permisos').empty();
					for (var table in res){
						$check_lectura = res[table]['lectura'] == 0 ? "" : "checked"; 
						$check_escritura = res[table]['escritura'] == 0 ? "" : "checked"; 
						$('#tbody_permisos').append(
							'<tr data-table="'+table+'">'+
								'<td>'+table+'</td>'+
								'<td class="text-center"><input type="checkbox" class="check" data-type="lectura" '+$check_lectura+'></td>'+
								'<td class="text-center"><input type="checkbox" class="check" data-type="escritura" '+$check_escritura+'></td>'+
							'</tr>'
						);
					}
				}
	        },
	        error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo');
	        }
	    });
	});
	$("#table_permisos").on ("click", ".check", function (event) { 
    	$checked = ($(this).is(':checked') ? 1 : 0);
    	$id_work = $('#select_category').val();
    	$nameTable = $(this).closest('tr').data('table');
    	$type = $(this).data('type');
    	$.ajax({
	        url: site_url + 'permisos/manage_state_permiso',
	        data : {view: $checked, id_work: $id_work, nameTable: $nameTable, type: $type},
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
					}else{
            			toast_error('¡Oh, hubo un error!',res.msg);
					}
				}
	        },
	        error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo');
	        }
	    });
    	
	})
});