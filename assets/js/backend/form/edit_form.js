$(document).ready(function() {
	$('#crate_form').empty();
	var dependencia = {};
	var dependenciafk2 = {};
	// var	clave_foranea_change = false;
	$.ajax({
		url: site_url + 'form/get_html_edit',
		data : {name_table: $('#name_table').val(), valueid: $('#valueid').val(), nameid: $('#nameid').val()},
		type: 'post',
		dataType: 'json',
		beforeSend: function(){
			$("#loading-circle-overlay").show();
		},
		success: function(res){
			$("#loading-circle-overlay").hide();
			if (res) {
				var array_fk = [];
				for (var resinput of Object.values(res)){
					var input = "";
					var name_input = ' name="'+resinput.name_input+'" ';
					var id_input = ' id="'+resinput.name_input+'" ';
					var id_div = ' id="div_'+resinput.name_input+'" ';
					var label_input = resinput.namelabel;
					switch (resinput.type.input) { // OBTENER EL INPUT EN HTML
						case "text":
						input = '<div '+id_div+' style="display: none" class="col-lg-6 col-xs-12 mb-2"><label >'+label_input+'</label><input required'+name_input+'type="text" value="'+resinput.value_input+'" class="form-control '+resinput.type.type_text+'" maxlength="'+resinput.type.maxlength+'"></div>'
						break;
						case "clave_foranea":
						$.ajax({
							url: site_url + 'form/get_data_table',
							data : {name_table: resinput.type.table, name_column: resinput.type.name, id_column: resinput.type.id},
							type: 'post',
							dataType: 'json',
							async: false,
							success: function(res_foranea)
							{
								$input_foranea = '<div class="mb-2 col-lg-6 col-xs-12"'+id_div+' style="display: none">'+
								'<label >'+label_input+'</label>'+
								'<select class="select2 form-control"'+name_input+' required>';
								$input_foranea += 		'<option value="" disabled selected>Select a choose</option>';
								for (item of Object.values(res_foranea)){
									$input_foranea += 	'<option value="'+item[resinput.type.id]+'" '+(resinput.value_input == item[resinput.type.id] ? "selected" : "")+" ";
						        		for (column_item of Object.keys(item)){ //LLENAMOS SUS DATAS CONSUS RESPECTIVO FK 
						        			if (resinput.type.id != column_item && resinput.type.name != column_item) {
						        				$input_foranea +=' data-'+column_item+'="'+item[column_item]+'" ';
						        			}
						        		}
						        		$input_foranea += 	'" >'+item[resinput.type.name]+'</option>';
						        	}
						        	$input_foranea += 	'</select></div>';
						        	input = $input_foranea
						        },
						        error:function(e){
						        }
						    });
						break;
						case "radio":
						$input_radio = '<div class="mb-2 col-lg-6 col-xs-12"'+id_div+' style="display: none"><label >'+label_input+'</label>';
						for (key of Object.keys(resinput.type.names)){
							$input_radio += '<input required'+name_input+' type="radio" value="'+key+'" class="form-control">'+resinput.type.names[key]+'</div>';
						}
						input = $input_radio
						break;
						case "checkbox":
						$input_checkbox = '<div '+id_div+'class="mb-2 col-lg-6 col-xs-12 custom-control custom-checkbox">';
						$input_checkbox += '<input'+name_input+'type="checkbox" class="custom-control-input"'+id_input+'/><label class="custom-control-label" for="'+resinput.name_input+'">'+label_input+'</label></div>';
						input = $input_checkbox;
						break;
						case "select":
						if (resinput.type.names) {
							$input_select = '<div class="mb-2 col-lg-6 col-xs-12"'+id_div+' style="display: none"><label>'+label_input+'</label><select required class="select2 form-control"'+name_input+'>';
							$input_select += '<option value="" disabled selected>Select a choose</option>';
							for (key of Object.keys(resinput.type.names)){
								$input_select += '<option value="'+key+'" '+(resinput.value_input == key ? "selected": "")+'>'+resinput.type.names[key]+'</option>';
							}
							$input_select += '</select></div>';
							input = $input_select
						}
						break;
						case "date":
						input='<div '+id_div+' class="mb-3 col-lg-6 col-xs-12" style="display: none">'+
								'<label>'+label_input+'</label>'+
								'<div class="input-group">'+
									'<input required '+name_input+' type="text" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" value="'+resinput.value_input+'" data-date-format="yyyy-mm-dd">'+
									'<div class="input-group-append">'+
										'<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>'+
									'</div>'+
								'</div>'+
							'</div>';
						break;
						case "textarea":
							input = '<div '+id_div+' style="display: none" class="col-lg-12 col-xs-12 mb-2">'+
										'<label >'+label_input+'</label>'+
										'<textarea required'+name_input+'type="text" class="form-control" rows="5">'+resinput.value_input+'</textarea>'+
									'</div>'
							break;
						default:
						input = '<div class="mb-2 col-lg-6 col-xs-12"'+id_div+' style="display: none"><label >'+label_input+'</label><input required'+name_input+'type="'+resinput.type.input+'" class="form-control" value="'+resinput.value_input+'"></div>';
						break;
					}
					if (resinput.parent.depend == "internal") {
						$('#edit_form').append(input);
						$('#div_'+resinput.name_input).show('slow/400/fast');

					}else if(resinput.parent.depend == resinput.parent.depend_column_fk){//depende de una clave primaria
						// adds(resinput.parent.depend_column_fk, resinput.parent.value, resinput.name_input, input);
						if (!dependencia[resinput.parent.depend_column_fk] || Object.keys(dependencia[resinput.parent.depend_column_fk]).length == 0) {
							dependencia[resinput.parent.depend_column_fk] = {}
						}
						dependencia[resinput.parent.depend_column_fk][resinput.name_input] = {}
						dependencia[resinput.parent.depend_column_fk][resinput.name_input]['input'] = input
						dependencia[resinput.parent.depend_column_fk][resinput.name_input]['values'] = resinput.parent.value
						// clave_foranea_change.push()
						// $("select[name="+ resinput.parent.depend_column_fk +"]").change();
					}else{//depende de una clave foranea
						if (!dependenciafk2[resinput.parent.depend] || Object.keys(dependenciafk2[resinput.parent.depend]).length == 0) {
								dependenciafk2[resinput.parent.depend] = {}
							}
							dependenciafk2[resinput.parent.depend][resinput.name_input] = {};
							dependenciafk2[resinput.parent.depend][resinput.name_input]['input'] = input;
							dependenciafk2[resinput.parent.depend][resinput.name_input]['values'] = resinput.parent.value;
							dependenciafk2[resinput.parent.depend][resinput.name_input]['fk'] = resinput.parent.depend_column_fk;
					}
					$('.datepicker-autoclose').datepicker({
						autoclose: true,
						todayHighlight: true
					});
					$(".select2").select2();
				}
				for (var select_change of Object.keys(dependencia)){
					$('select[name='+select_change+']').on("change",function() {
						for (var div_id of Object.keys(dependencia[select_change])){
							$('#div_'+div_id).remove();
							for (var value of Object.values(dependencia[select_change][div_id]['values'])){
								if ($(this).val() == value) {
									$('#edit_form').append(dependencia[select_change][div_id]['input'])
									$('#div_'+div_id).show('slow/400/fast');
									break;
								}
							}
						}
						//definir librerias en select y datepicker por siacaso!!
						$('.datepicker-autoclose').datepicker({
							autoclose: true,
							todayHighlight: true
						});
					});
					$('select[name='+select_change+']').change();
				}
				for (var select_change of Object.keys(dependenciafk2)){
					$('select[name='+select_change+']').on("change",function() {
						for (var div_id of Object.keys(dependenciafk2[select_change])){
							$('#div_'+div_id).remove();
							for (var value of Object.values(dependenciafk2[select_change][div_id]['values'])){
								if ($(this).find(":selected").data(dependenciafk2[select_change][div_id]['fk'].toLowerCase()) == value) {
									$('#edit_form').append(dependenciafk2[select_change][div_id]['input'])
									$('#div_'+div_id).show('slow/400/fast');
									break;
								}
							}
						}
						// definir librerias en select y datepicker por siacaso!!
						$('.datepicker-autoclose').datepicker({
							autoclose: true,
							todayHighlight: true
						});
					});
					$('select[name='+select_change+']').change();
				}
			}
		},
		error:function(e){
			$('#crate_form').empty();
			$("#loading-circle-overlay").hide();
			toast_error('¡Oh, hubo un error!',"Vuelva a intentarlo");
		}
	});
	$("#form_edit").submit(function( event ) {
		event.preventDefault();
		$.ajax({
			url: site_url + 'form/edit_dynamic',
			data : $(this).serialize(),
			type: 'post',
			dataType: 'json',
			beforeSend: function(){
				$("#loading-circle-overlay").show();
			},
			success: function(res)
			{
				$("#loading-circle-overlay").hide();
				if (res.success) {
					swal({
	                    title: '¡Muy bien!!',
	                    text: res.msg,
	                    type: 'success',
	                    confirmButtonColor: '#4fa7f3'
	                }).then(function () {
	                	$.redirectPostmenu(site_url + "form/listar", {name_table: $('#name_table').val(), alias_table: $('#alias_table').val()});
		            })
				}else{
					toast_error('¡Oh, hubo un error!',res.msg);
				}
			},
			error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
			}
		});
	});
});