$(document).ready(function() {
	var table = $('#datatable').DataTable({
		"columnDefs": [
	      { "width": "40%", "targets": 0 },
	      { "width": "40%", "targets": 1 },
	      { "width": "20%", "targets": 2 },
	    ],
        "scrollX": true
	});
	$('tr td a').on('click', function (e) {
    	e.preventDefault();
        if ($(this).closest('tr').find('.aliastable input').val() != "") {
        	$selected = $(this);
            $nametable = $(this).closest('tr').find('.nametable').text();
            $aliastable = $(this).closest('tr').find('.aliastable input').val();
            $estado = "";
        	if ($(this).find('span').text() == "Inactivo") {
        		$estado = "AC";
        	}else{
        		$estado = "IN";
        	}
        	$.ajax({
                url: site_url + '/admin/update_estado',
                data : {name_table: $nametable, estado: $estado, alias_table: $aliastable},
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
                  $("#loading-circle-overlay").show();
                },
                success: function(res)
                {
                	$("#loading-circle-overlay").hide();
                	if (res) {
                        toast_success('¡Muy bien!',res.msg);
                        if ($estado == "AC") {
                            $selected.hide('slow/400/fast', function() {
                                $selected.html('<span class="pr-2 pl-2 label label-success">Activo</span>');
                                $selected.show('slow/400/fast');
                                $('#navigation').after(
                                    '<li id="'+$nametable+'" >'+
                                      '<a href="javascript: void(0);">'+
                                          '<i class="fi-paper"></i><span>'+$aliastable+'</span>'+
                                      '</a>'+
                                      '<ul class="nav-second-level collapse" aria-expanded=false>'+
                                          '<li><a class="menu_dynamic" onclick="redirect_menu('+"'"+site_url+'form/create'+"'"+', '+"'"+$nametable+"'"+', '+"'"+$aliastable+"'"+')" href="#">Registrar</a></li>'+
                                          '<li><a class="menu_dynamic" onclick="redirect_menu('+"'"+site_url+'form/listar'+"'"+', '+"'"+$nametable+"'"+', '+"'"+$aliastable+"'"+')" href="#">Listar </a></li>'+
                                      '</ul>'+
                                    '</li>'
                                );
                            });
                        }else{
                            $('#'+$nametable).hide('slow/400/fast', function() {
                                $(this).remove();
                            });
                            $selected.hide('slow/400/fast', function() {
                                $selected.html('<span class="label pr-2 pl-2 label-danger">Inactivo</span>');
                                $selected.show('slow/400/fast');
                            });
                        }
                        if ($selected.closest('tr').find('.save_alias').length != 0) {
                                $selected.closest('tr').find('.save_alias').hide('slow/400/fast', function() {
                                $selected.closest('tr').find('.save_alias').remove();
                            });
                        }
                        toast_success('¡Muy bien!','Recargue la pagina para que vea los cambios.');
                	}
                },
                error:function(e){
                    $("#loading-circle-overlay").hide();
    				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
                }
            });
        }else{
            toast_error('¡Oh, hubo un error!','El ailas de la table no debe estar vacio!');
        }
    })
    $('.aliastable input').bind("input",function() {
        if ($(this).val() == $(this).data('old')) {
            if ($(this).closest('td').find('.save_alias').length != 0) {
                    $(this).closest('td').find('.save_alias').hide('slow/400/fast', function() {
                    $(this).closest('td').find('.save_alias').remove();
                });
            }
        }else{
            if ($(this).val() == "") {
                if ($(this).closest('td').find('.save_alias').length != 0) {
                    $(this).closest('td').find('.save_alias').hide('slow/400/fast', function() {
                    $(this).closest('td').find('.save_alias').remove();
                    });
                }
            }if ($(this).closest('td').find('.save_alias').length == 0) {
                $(this).closest('td').append('<div class="save_alias col-md-4 col-xs-2 pl-0" style="display:none"><i class="action fa fa-save"/></div>')
                $(this).closest('td').find('.save_alias').show('slow/400/fast');
                //START EVENT OF SAVE ALIAS TABLE, HERE WORK ONCE TIME*******
                $('.save_alias i').on('click', function () {
                    $tr_selected = $(this).closest('tr');
                    $estado = "";
                    if ($tr_selected.find('td a span').text() == "Inactivo") {
                        $estado = "IN";
                    }else{
                        $estado = "AC";
                    }
                    $.ajax({
                        url: site_url + '/admin/update_estado',
                        data : {name_table: $tr_selected.find('.nametable').text(), estado: $estado, alias_table: $tr_selected.find('.aliastable input').val()},
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function(){
                          $("#loading-circle-overlay").show();
                        },
                        success: function(res)
                        {
                            $("#loading-circle-overlay").hide();
                            if (res) {
                                toast_success('¡Muy bien!',res.msg);
                                $tr_selected.find('.save_alias').hide('slow/400/fast', function() {
                                    $tr_selected.find('.save_alias').remove();
                                });
                                $tr_selected.find('.aliastable input').data("old", $tr_selected.find('.aliastable input').val()); 
                                toast_success('¡Muy bien!','Recargue la pagina para que vea los cambios.');
                            }
                        },
                        error:function(e){
                            $("#loading-circle-overlay").hide();
                            toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
                        }
                    });
                })
                //END EVENT OF SAVE ALIAS TABLE*******
            }
        }
    });
});