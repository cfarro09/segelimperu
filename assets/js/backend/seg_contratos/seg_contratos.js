var end_count_change = "";
$(document).ready(function() {
	$('.input-daterange-datepicker').daterangepicker({
		buttonClasses: ['btn', 'btn-sm'],
		applyClass: 'btn-success',
		cancelClass: 'btn-default',
		autoclose: true
	});
	var table = $('#datatable').DataTable({
		lengthChange: false,
		buttons: [ 'excel', 'pdf', 'colvis'],
		"scrollX": true,
		"order": [[ 3, "desc" ]],
		"columnDefs": [
	      { "width": "35%", "targets": 0 },
	      { "width": "15%", "targets": 1 },
	      { "width": "22%", "targets": 2 },
	      { "width": "10%", "targets": 3 },
	      { "width": "18%", "targets": 4 }
	    ]
	});
	table.buttons().container()
	.appendTo('#datatable_wrapper .col-md-6:eq(0)');

	$('#range_date_cont').change(function(event) {
		$startdate = $('#range_date_cont').data('daterangepicker').startDate._d.toISOString().substring(0, 10);
		$enddate = $('#range_date_cont').data('daterangepicker').endDate._d.toISOString().substring(0, 10);
		$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
	    	var createdAt = data[2] || 0; // Our date column in the table
	    	if (($startdate == "" || $enddate == "") ||(moment(createdAt).isSameOrAfter($startdate) && moment(createdAt).isSameOrBefore($enddate))) {
	    		return true;
	    	}else{
	    		return false;
	    	}
	    });
		table.draw();
	});
	$("#datatable").on ("click", ".show_historial", function (event) {
		event.preventDefault();
		$td_select = $(this).parent();
		$('#datatablehistory').DataTable({
            "ajax": {
                "type": "POST",
                "url": base_url+"contratos/get_history_empl",
                "dataType": "json",
                "data": {id_usu: $(this).parent().data("id")}
            },
            "sAjaxDataProp": "",
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json"
                },
            "columnDefs": [
                    {className: 'text-center', targets: '_all'}
                ],
            "columns": [
                    {"data": "time_meses", "title": "Cont. Mess"},
                    {"data": "time_dias", "title": "Cont. Dias"},
                    {"data": "start_cont", "title": "Inicio Contrato"},
                    {"data": "end_cont", "title": "Fin Contrato"},
                    {"data": "tipo_con", "title": "Tipo Cont"},
                    {"data": "empresa_cont", "title": "Empresa Cont"},
                ],
			"destroy" : true,
			"order": [[ 3, "desc" ]]
        });
        $('#title-historial').html("Historial de " + $td_select.data("name"));
		$('#modal_historial').modal();
	});
	$("#datatable").on ("click", ".show_renovar", function (event) {
		event.preventDefault();
		$('#id_usu').val($(this).parent().data("id"));
		$('#nombre_usu').val($(this).parent().data("name"));
		$('#modal_renovar').modal();
		end_count_change = $(this).closest('tr').find('.end_cont');
		$('.datepicker-autoclose').datepicker({
			autoclose: true,
			todayHighlight: true,
			startDate: $(this).parent().data("endcont")
		});
	});
	$('#form_renovar').submit(function(event) {
		event.preventDefault();
		$.ajax({
			url: site_url + 'contratos/renovar',
			data : $(this).serialize(),
			type: 'post',
			dataType: 'json',
			beforeSend: function(){
				$("#loading-circle-overlay").show();
			},
			success: function(res){
				$("#loading-circle-overlay").hide();
				if (res) {
					$('#modal_renovar').modal('hide');
					toast_success('¡Muy Bien!',res['msg']);
					start_date = new Date($('#start_cont').val());
					$endatex =  new Date(start_date.setDate(start_date.getDate() + parseInt($('#int_dias').val())) )
					$endatex =  new Date($endatex.setMonth($endatex.getMonth() + parseInt($('#int_meses').val()) )); 
					end_count_change.html($endatex.toISOString().substring(0, 10))
					document.getElementById("form_renovar").reset();
					$('.datepicker-autoclose').datepicker('setStartDate',$endatex.toISOString().substring(0, 10));

				}else{
					toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
				}
			},error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
			}
		});
	});
	$("#datatable").on ("click", ".desafiliar", function (event) {
		event.preventDefault();
		$id_usu = $(this).parent().data("id");
		$tr_selected_1 = $(this).closest('tr');
		swal({
			title: '¿Estás seguro?',
			text: "Daras de baja a un trabajo!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#4fa7f3',
			cancelButtonColor: '#d57171',
			confirmButtonText: 'Si, dar de baja!'
		}).then(function () {
			$.ajax({
				url: site_url + 'contratos/desafiliar_personal',
				data : {id_usu: $id_usu},
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
							swal(
								'Desafiliado!',
								res.msg,
								'success'
								)
							$tr_selected_1.hide('slow/400/fast', function() {
								$tr_selected_1.remove();
							});

						}else{
							swal(
								'¡Oh, hubo un error',
								res.msg,
								'error'
								)
						}
					}else{
						swal(
							'¡Oh, hubo un error',
							'Vuelva a intentarlo en un momento.',
							'error'
							)
					}
				},
				error:function(e){
					$("#loading-circle-overlay").hide();
					swal(
						'¡Oh, hubo un error',
						'Vuelva a intentarlo en un momento.',
						'error'
						)
				}
			});
		})
	});
	$("#datatable").on ("click", ".editar_cont", function (event) {
		event.preventDefault();
		if ($('#menu_dynamic').length > 0) {
			$('#menu_dynamic').remove();
		}
		$.redirectPostmenu(site_url + "form/edit_form",{valueid: $(this).data("idcont"), nameid: "id_cont", nametable : "Contratos", aliastable: "Contratos"}, 'target="_blank"');
	});
	$('#add_register_cont').click(function(event) {
		event.preventDefault();
		if ($('#menu_dynamic').length > 0) {
			$('#menu_dynamic').remove();
		}
		$.redirectPostmenu(site_url + "form/create",{name_table: "Contratos", alias_table: "Contratos"}, 'target="_blank"');
	});
});