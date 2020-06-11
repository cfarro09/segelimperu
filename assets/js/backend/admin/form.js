var continius = false;
$(document).ready(function() {
	$( "#select_tables" ).change(function() {
		$.ajax({
	        url: site_url + 'form/get_columns_name',
	        data : {name_table: $(this).val()},
	        type: 'post',
	        dataType: 'json',
	        beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
	        success: function(res)
	        {
				$("#loading-circle-overlay").hide();
				if (res) {
					$('#columns_table').empty();
					for (var i = 0; i < res.length; i++) {
						if (res[i]['Comment'] && res[i]['Comment'] != "") {
							try {
								var comment_check = JSON.parse(res[i]['Comment'])
								checked = comment_check.list ? "checked" : "";
								name_label_tr = comment_check.namelabel ? comment_check.namelabel : res[i]['Field'];
							}catch(error) {
							}
						}else{
							checked = "";
							name_label_tr = res[i]['Field']; 
						}
						if (res[i]['Key'] != "PRI" && !res[i]['Default']) {
							$('#columns_table').append(
								'<tr class="column">'+
									'<td class="text-center field">'+res[i]['Field']+'</td>'+
									'<td class="text-center type">'+res[i]['Type']+'</td>'+
									'<td class="text-center null">'+res[i]['Null']+'</td>'+
									'<td style="display:none;" class="text-center comment">'+res[i]['Comment']+'</td>'+
									'<td class="text-center fk">'+(res[i]['Key']=="PRI"?"PK":(res[i]['Key']=="MUL")?"FK":"")+'</td>'+
									'<td class="text-center list">'+(res[i]['Comment'] == '' || Object.keys(JSON.parse(res[i]['Comment'])).length <= 2 ? '<div><input type="text" style="width: 100px; border: none; text-align: center; color: #797979" class="name_label_tr pr-2" value="'+name_label_tr+'"></div><input type="checkbox" class="checkbox_td"'+checked+'></td>' : '<input type="checkbox" class="checkbox_td"'+checked+'></td>')+
									'<td class="text-center status">'+(res[i]['Comment'] == ''? '<span class="label label-danger">Inactivo</span>':(Object.keys(JSON.parse(res[i]['Comment'])).length <= 2 ?'<span class="label label-danger">Inactivo</span>':'<span class="label label-success">Activo</span>'))+'</td>'+
								'</tr>)');
						}
					}
				}
	        },
	        error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo');
	        }
	    });
	});
	$("#columns_table").on ("click", "tr.column", function (event) { //SELECT LA TUERCA DE CADA COLUMNA
        var tr_sel = $(this);
        var null_select = tr_sel.find(".null").html();
        var type_select = tr_sel.find(".type").html();
        var field_select = tr_sel.find(".field").html();
        var fk = tr_sel.find(".fk").html();
        if (tr_sel.find(".comment").html()) {
        	try {
        		$options = JSON.parse($(this).find(".comment").html());
        		if(Object.keys($options).length <= 2){
        			$options = "";
        		}
			}catch(error) {
				$options = "";
			}
        }else{
        	$options = "";
        }
		$(".tr_selected").removeClass('tr_selected');
		$checked = "";
		$display ="style='display: none'";
		$value_display = "style='display: none'";
		$inputs_column = "style='display: none'";
		$clave_foranea_display = "style='display: none'";
		$button_guardar = "";
		$maxlength = "";
		$value = "";
		$columns_name ="";
		$depend = "internal";
		$type_text = "";
		$type_input = "";
		$fk_column = "";
		$disabled_by_fk ="";
		$depend_fk_table = "";
		$namelabel = "";
		$table_clave_foranea = "";

		if(jQuery(tr_sel).next().find('label').length != 0 && tr_sel.find("#check_create_input").length == 0 || continius == true){//validar si esta abierto el form_extra
			continius = false;
         	$('#info_extra').hide('fast', function(){ $(this).remove(); });
        }else if(tr_sel.find("#check_create_input").length == 0){
         	$('#info_extra').remove();
			if ($options !="") {
				$checked = "checked";
				$display ="";
				$depend = $options['parent']['depend'];
				$namelabel = $options['namelabel'];
				$value = $options['parent']['value'];
        		$depend_fk_table = $options['parent']['depend_column_fk'] ? $options['parent']['depend_fk_table'] : "";
				$value_display = ($options['parent']['value'] == "" ? 'style="display: none"' : "");
				$type_input = $options['type']['input'];
				$type_text = ($type_input == "text"? $options['type']['type_text'] : "");
				$maxlength = ($type_input == "text"? $options['type']['maxlength'] : "");
				$button_guardar = ($type_input == 'select' || $type_input == 'radio' ? 'style="display: none"': "" );
				$clave_foranea_display = ($type_input == "clave_foranea" ?  "" :  'style="display: none"');
				if ($type_input == "clave_foranea") {
					$table_clave_foranea = $options['type']['table'];
					get_columns_only_name($options['type']['table'],$options['type']['id'],$options['type']['name']);
				}
				if ($clave_foranea_display != "") {
					$inputs_column = (Object.keys($options['type']['names']).length >0 ? "" : 'style="display: none"');
				}else{
					$disabled_by_fk = 'disabled';
					$inputs_column = 'style="display: none"' ;
				}
				$columns_name = "";
				$index = 1;
				if ($options['type']['names']) { // VALIDAMOS SI VIENE DE UN SELECT O RADIO O CHECKBOX SI
					for (var name in $options['type']['names']){
					    if ($options['type']['names'].hasOwnProperty(name)) {
					    	$columns_name +=
					    		'<tr data-index="'+$index+'" class="fontawesome-icon-list p-1">'+
					    			'<td class="name_input" style="width: 40%">' + name +'</td>'+
					    			'<td class="alias_input" style="width: 40%">' + $options['type']['names'][name] +'</td>'+
					    			'<td class="fa-hover text-center" style="width: 20%">' +
					    				'<a class="edit_name_input manage_form" href="#"><i class="fa fa-edit"></i></a>'+
	    								'<a class="remove_name_input manage_form" href="#"><i class="mdi mdi-delete"></i></a>'+
					    			'</td>'+
					    		'</tr>'
					    }
					    $index++;
					}
				}
			}
			$option_names_column = '<option value="internal" '+ ($depend == "internal"? "selected" : "") +'>No Depend</option>';
         	$("#columns_table tr.column").each(function(){
				if (field_select != $(this).find('.field').html() && $(this).find('.fk').html() != "") {
		    		$option_names_column += '<option value="'+$(this).find('.field').html()+'" '+($depend == $(this).find('.field').html()? "selected" : "")+'>'+$(this).find('.field').html()+'</option>';
				}
			});
        	$form_extra = '<tr id="info_extra" style="display: none" style="background-color: #f5f5f5">'+
			        		'<td colspan="6" class="pr-4 pl-4">'+
			        			'<form class="form-horizontal form-label-left" id="manage_input">'+
			        				'<div class="row mb-3">'+
										'<div class="col-md-6 col-sm-6 col-xs-6 ">'+
											'<div class="custom-control custom-checkbox">'+
												'<input type="checkbox" name="check_create" id="check_create_input" class="custom-control-input" '+$checked+'  />'+
												'<label class="custom-control-label" for="check_create_input">Create Input</label>'+
											'</div>'+
										'</div>'+
										'<div class="col-md-6 col-sm-6 col-xs-6" '+ $button_guardar+ '>'+
											'<button id="save_input_column" class="btn btn-primary pull-right m-1">Guardar</button>'+
										'</div>'+
									'</div>'+
									'<div id="form_extra"' + $display +' class="mt-2">'+
										'<div class="col-md-12 col-sm-12 col-xs-12 mb-2">'+
					                        '<div class="row">'+
					                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Namelabel</label>' +
					                        	'<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
													'<input id="namelabel" type="text" class="form-control letrasnumeros_especiales" value="'+$namelabel+'" placeholder="Into name of label">'+
													'<span class="help-block"><small>Nombre de su input</small></span>'+
												'</div>'+
					                        '</div>'+
										'</div>'+
										'<div class="col-md-12 col-sm-12 col-xs-12 mb-2">'+
					                        '<div class="row">'+
					                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Depende</label>' +
					                        	'<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
													'<select class="form-control" name="select_depend_column" id="select_depend_column" >'+
														$option_names_column+
													'</select>'+
													'<span class="help-block"><small>Si este input depende de un valor de otro input(columna)</small></span>'+
												'</div>'+
					                        '</div>'+
										'</div>'+
										'<div id="value_depend" class="col-md-12 col-sm-12 col-xs-12 mb-2"'+  $value_display + '>'+
											'<div class="row mb-2" id="select_depend_fk"'+($depend_fk_table != "" ? "" : "style='display:none'" )+'>'+
												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
												'<div class="col-md-10 col-sm-10 col-xs-10" >'+
													'<div class="row">'+
						                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2" id="label_table_fk">'+$depend_fk_table+'</label>' +
						                        	'<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
														'<select class="form-control" id="cols_tableref_deped">'+
														'</select>'+
														'<span class="help-block"><small>Seleccione la columna del valor que se filtrará, solo admite FK o PK. </small></span>'+
													'</div>'+
													'</div>'+
						                        '</div>'+
											'</div>'+
											'<div class="row mb-2">'+
												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
												'<div  class="col-md-10 col-sm-10 col-xs-10">'+
													'<div class="row">'+
							                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">ValueS</label>' +
							                        	'<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
							                        		'<select class="select2 form-control select2-multiple" id="selmul_values_depend" multiple="multiple" multiple data-placeholder="Choose ...">'+
							                        		'</select>'+
															'<span class="help-block"><small>Seleccione los valores que admitirá para que se muestra el input seleccionado.</small></span>'+

														'</div>'+
													'</div>'+
						                        '</div>'+
											'</div>'+
										'</div>'+
										'<div class="mb-2 col-md-12 col-sm-12 col-xs-12">'+
					                        '<div class=" row">'+
					                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Type Input</label>' +
					                        	'<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
													'<select class="form-control" '+$disabled_by_fk+' name="select_type_input"  id="select_type_input">'+
						                        		'<option value="" selected disabled>Select a type input</option>'+
														'<option value="text" '+  ($type_input == 'text'? "selected" : "") +'>Text</option>'+
														'<option value="number" '+  ($type_input == 'number'? "selected" : "") +'>Number</option>'+
														'<option value="textarea" '+  ($type_input == 'textarea'? "selected" : "") +'>Textarea</option>'+
														'<option value="date" '+  ($type_input == 'date'? "selected" : "") +'>Date</option>'+
														'<option value="email" '+  ($type_input == 'email'? "selected" : "") +'>Email</option>'+
														'<option value="password" '+  ($type_input == 'password'? "selected" : "") +'>Password</option>'+
														'<option value="select" '+  ($type_input == 'select'? "selected" : "") +'>Select</option>'+
														'<option value="checkbox" '+  ($type_input == 'checkbox'? "selected" : "") +'>Checkbox</option>'+
														'<option value="radio" '+  ($type_input == 'radio'? "selected" : "") +'>Radio</option>'+
														($disabled_by_fk ? '<option value = "clave_foranea" selected>Clave Foranea<option>':'')+
													'</select>'+
												'</div>'+
					                        '</div>'+
										'</div>'+
										'<div class="mb-2 col-md-12 col-sm-12 col-xs-12" id="div_validate_text"'+ ($type_input == 'text'? '' : "style='display: none'") + '>'+ //table of select or radio
					                        '<div class="mb-2 row">'+

												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
						                        '<div class="col-md-10 col-sm-10 col-xs-10">'+
					                        		'<div class="row">'+

							                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Validate</label>' +
								                        '<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
								                        	'<select class="form-control pr-0" id="select_validate_text">'+
								                        		'<option value="" selected disabled>Select a validation</option>'+
								                        		'<option value="sololetras" '+($type_text == 'sololetras'? "selected" : "") +'>Solo letras</option>'+
								                        		'<option value="solonumeros" '+($type_text == 'solonumeros'? "selected" : "") +'>Solo números</option>'+
								                        		'<option value="letrasnumeros" '+($type_text == 'letrasnumeros'? "selected" : "") +'>Letras/Numeros</option>'+
								                        		'<option value="address" '+($type_text == 'address'? "selected" : "") +'>Address</option>'+
								                        		'<option value="especiales" '+($type_text == 'especiales'? "selected" : "") +'>Letras/LetrasEspeciales/Numeros</option>'+
								                        		'<option value="letrasnumeros_especiales" '+($type_text == 'letrasnumeros_especiales'? "selected" : "") +'>Letras/Numeros/CarEspeciales</option>'+
															'</select>'+
								                        '</div>'+
													'</div>'+
												'</div>'+
											'</div>'+
					                        '<div class="mb-2 row">'+
												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
						                        '<div class="col-md-10 col-sm-10 col-xs-10">'+
					                        		'<div class=" row">'+
							                        	'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">MaxText</label>' +
								                        '<div class="col-md-10 col-sm-10 col-xs-10 pr-0">'+
		                          							'<input id="maxlength" type="text" class="form-control solonumeros" value="'+$maxlength+'" placeholder="Ingrese MaxLength">'+
								                        '</div>'+
													'</div>'+
												'</div>'+
											'</div>'+
										'</div>'+
										'<div class="mb-2" id="table_type_input"'+ $inputs_column + '>'+ //table of select or radio
					                        '<div class="row">'+
												'<div class="col-md-2 col-sm-2 col-xs-12"></div>'+
						                        '<div class="col-md-10 col-sm-10 col-xs-12">'+
						                        	'<table class="table mb-0">'+
														'<thead class="cells_extra">'+
															'<tr class="icon-table">'+
																'<th class="">Name</th>'+
																'<th class="">Value</th>'+
																'<th class="text-center"><a href="#" id="add_value_input"><i class="mdi mdi-plus"></i></a></th>'+
															'</tr>'+
														'</thead>'+
														'<tbody id="columns_names" class="cells_extra">'+
															$columns_name+
														'</tbody>'+
													'</table>'+
						                        '</div>'+
											'</div>'+
										'</div>'+
										'<div class="mb-2" id="clave_foranea_input"'+ $clave_foranea_display + '>'+ //clave foranea
											'<div class="row mb-2">'+
												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
												'<div class="col-md-10 col-sm-10 col-xs-10">'+
													'<div class="row">'+
														'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Table FK</label>' +
								                        '<div class="col-md-10 col-sm-10 col-xs-10">'+
								                        	'<select class="form-control pr-0" id="select_table_cf">'+
															'</select>'+
								                        '</div>'+
							                    	'</div>'+
							                    '</div>'+
							                '</div>'+
											'<div class="row mb-2">'+
												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
												'<div class="col-md-10 col-sm-10 col-xs-10">'+
													'<div class="row">'+
														'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Id FK</label>' +
								                        '<div  class="col-md-10 col-sm-10 col-xs-10">'+
								                        	'<select class="form-control pr-0" id="select_id_fk" disabled>'+
								                        		'<option value="">Select a ID FK</option>'+
															'</select>'+
								                        '</div>'+
							                		'</div>'+
							                    '</div>'+
							                '</div>'+
											'<div class="row mb-2">'+
												'<div class="col-md-2 col-sm-2 col-xs-2"></div>'+
												'<div class="col-md-10 col-sm-10 col-xs-10">'+
													'<div class="row">'+
														'<label class="ph-0 control-label col-md-2 col-sm-2 col-xs-2">Value FK</label>' + //fin calve foranea
								                        '<div class="col-md-10 col-sm-10 col-xs-10">'+
								                        	'<select class="form-control pr-0" id="select_value_fk">'+
								                        		'<option value="">Select a Value FK</option>'+
															'</select>'+
								                        '</div>'+
							                    	'</div>'+

							                    '</div>'+
							                '</div>'+
										'</div>'+
									'</div>'+
				                '</form>'+
			        		'</td>'+
			        	'</tr>';
			tr_sel.addClass('tr_selected')
         	tr_sel.after($form_extra);
         	$('#info_extra').show('slow/400/fast');
         	//DEPENDENCIA, SI SU DEPENDENCIA ES UN FK Q HA SIDO REGISTRADO
         	if ($options) {
	         	if ($options['parent']['depend_column_fk']) {
	         		get_columns_only_name($options['parent']['depend_fk_table'], "", $options['parent']['depend_column_fk'], true ,$value) //cols_tableref_deped <= modifica
	         	}
         	}
         	if ($clave_foranea_display == "") { //si tiene clave foranea registrada
     			$('#select_table_cf').html('<option value="'+$table_clave_foranea+'" selected="true">'+$table_clave_foranea+'</option>');
            	$('#select_table_cf').prop('disabled','disabled');
         	}else if(fk){//SI EXISTE FK SE CARGA AUTOMATICAMENTE
         		get_referenced_table($('#select_tables').val() ,field_select)
         	}
        }
        $(".select2").select2();
	    $(".select2-limiting").select2({
	        maximumSelectionLength: 2
	    });
	    
        $("#check_create_input").click( function(){
        	if ($('#check_create_input').is(':checked')) {
	        	if(!$('.tr_selected .comment').html()){//LIMPIAR SI SE LIMPIO AL COMIENZO
	        		$('#select_depend_column').val("internal")
	        		$('#value_depend').hide()
	        		$('#table_type_input').hide()
	        		$('#div_validate_text').hide()
	        		$('#select_id_fk').val("")
	        		$('#select_value_fk').val("")
	        	}
	        	$('#form_extra').show('slow/400/fast');
	        	if (fk) { //SI LA COLUMNA ES UN FK SE AUTOMATIZA TODO SUS DATOS
					$('#select_type_input').html('<option value="clave_foranea" selected>Clave Foranea</option>')
	         		$('#select_type_input').attr("disabled", "disabled");
	         		$('#clave_foranea_input').show('slow/400/fast');
	         		get_columns_only_name($('#select_table_cf option[selected="true"]').text())
	         	}
	        	if($('#select_type_input').val() == "select"  || $('#select_type_input').val() == "radio"){
	        		$('#save_input_column').parent().hide('slow/400/fast');
	        	}else{
	        		$('#save_input_column').parent().show('slow/400/fast');
	        	}
	        }else{
	        	$('#form_extra').hide('slow/400/fast');
        		$('#save_input_column').parent().show('slow/400/fast');
	        }
        })
        $('#select_depend_column').on('change', function () {
        	if ($(this).val() != "internal") {
    			$("#select_depend_fk").show('slow/400/fast');
     			get_referenced_table($('#select_tables').val() ,$(this).val(), true);
        		// $('#selmul_values_depend').val("");
				$('#value_depend').show('slow/400/fast');
        	}else{
				$('#value_depend').hide('slow/400/fast');
        	}
        })
        $('#cols_tableref_deped').on('change', function () {
        	$('#selmul_values_depend').empty();
        	if ($(this).find(':selected').data('fk') == "PK") {
        		get_data_dependence($('#label_table_fk').text())
        	}else{
        		listar_referenced_table($('#label_table_fk').text(), $(this).val());
        	}
        })
        $('#select_type_input').on('change', function () {
        	if ($(this).val() == "select" || $(this).val() == "radio" ) {
        		$('#save_input_column').parent().hide('slow/400/fast');
				$('#clave_foranea_input').hide('slow/400/fast');
				$('#table_type_input').show('slow/400/fast');
				$('#div_validate_text').hide('slow/400/fast');
        	}else if($(this).val() == "text" ){
        		$('#save_input_column').parent().show('slow/400/fast');
				$('#table_type_input').hide('slow/400/fast');
				$('#div_validate_text').show('slow/400/fast');
        	}else{
				$('#div_validate_text').hide('slow/400/fast');
				$('#clave_foranea_input').hide('slow/400/fast');
        		$('#save_input_column').parent().show('slow/400/fast');
				$('#table_type_input').hide('slow/400/fast');
        	}
        })
        $('#select_table_cf').on('change', function () {
        	get_columns_only_name($(this).val());
        })
        $('#add_value_input').on('click', function (e) {
        	e.preventDefault();
        	if ($('.ultimo-input').length == 0) {
	        	$tr_column =
	        				'<tr data-index="'+($('#columns_names tr').length+1)+'" class="fontawesome-icon-list p-1 ultimo-input">'+
	        					'<td class="name_input" style="width: 40%"><input class="p-input form-control letrasnumeros " type="text"></td>'+
	        					'<td class="alias_input" style="width: 40%"><input class="p-input form-control letrasnumeros " type="text"></td>'+
	        					'<td class="fa-hover text-center" style="width: 20%">'+
	        						'<a href="#" class="save_input_name manage_form"><i class="fa fa-save font-20"></i><span class="text-muted"></span></a>'+
	        						'<a href="#" class="remove_name_input manage_form"><i class="mdi mdi-delete"></i><span class="text-muted"></span></a>'+
	        					'</td>'+
	        				'</tr>';
	        	$('#columns_names').append($tr_column);
        	}else{
        		$('.ultimo-input').addClass('alert-danger'); //alerta q ya se agregaron 1 nuevo registro
        	}
        })
        //Quitar el sombreado rojo si se hace click en el input de name input
		$("#columns_names").on ("click", ".p-input", function (event) {
        	$('.ultimo-input').removeClass('alert-danger')
		});
		//GUARDADO DIRECTO DEL SAVE DE LA TABLA de input de SELECT****************************************************************************
		$("#columns_names").on ("click", ".save_input_name", function (event) {//INTO TABLE OF SELCT/RADIO/CHECKBOC
			event.preventDefault();

			if ($(this).parent().siblings('.alias_input').find('input').val() != "" && $(this).parent().siblings('.name_input').find('input').val() != "") {
				if ($('#namelabel').val() != "" && $('#namelabel').val()) {
        			$('.alert-danger').removeClass('alert-danger') //quitar shadow red if first tag guardaren and then save
					if (($('#select_depend_column').val() != "internal"  &&  $('#selmul_values_depend').val() != "") || ($('#select_depend_column').val() == "internal")){
						$('.ultimo-input').removeClass('ultimo-input')
						$(this).parent().siblings('.name_input').html($(this).parent().siblings('.name_input').find('input').val());
						$(this).parent().siblings('.alias_input').html($(this).parent().siblings('.alias_input').find('input').val());
						$(this).parent().html('<a class="edit_name_input manage_form" href="#"><i class="fa fa-edit font-20"></i></a><a class="remove_name_input manage_form"  href="#"><i class="mdi mdi-delete"></i></a>');
						var arreglo ={}
						if ($('#select_depend_column').val() == "internal") {
							arreglo ={
								list: tr_sel.find('.checkbox_td').is(":checked"),
								namelabel: $('#namelabel').val(),
								parent: {
									"depend": "internal",
									"value": ""
								},
								type: {}
							}
						}else{
							if ($('#selmul_values_depend').val() == "" || !$('#selmul_values_depend').val()) {
								toast_error('¡Oh, hubo un error!','Ingrese un valor a su dependendia.');
				            	return;
							}else{
			                	arreglo ={
									namelabel: $('#namelabel').val(),
									parent: {
										"depend": $('#select_depend_column').val(),
										"depend_fk_table": $("#label_table_fk").text(),
										"depend_column_fk": $("#cols_tableref_deped").val(),
										"value": $('#selmul_values_depend').val()
									},
									type: {}
								}
							}
						}
						arreglo['type']['input'] = $('#select_type_input').val();
						arreglo['type']['names'] = {};
						$("#columns_names tr").each(function(){
								arreglo['type']['names'][$(this).find('.name_input').html()] = $(this).find('.alias_input').html();
						});
						send_comment_ajax($("#select_tables").val(),field_select,type_select, null_select, JSON.stringify(arreglo), tr_sel);
					}else{
							toast_error('¡Oh, hubo un error!','Ingrese un valor a su dependendia.');
					    return;
					}
				}else{
					toast_error('¡Oh, hubo un error!','Ingrese el nombre del label.');
					return;
				}
			}else{
					toast_error('¡Oh, hubo un error!','Ingrese valores a los inputs.');
			    return;
			}
		});
		$("#columns_names").on ("click", ".remove_name_input", function (event) {//INTO TABLE OF SELCT/RADIO/CHECKBOC
			event.preventDefault();
			if($('.remove_name_input').closest('tbody').find('tr').length <= 2){
					toast_error('¡Oh, hubo un error!','No se puede dejar menos de 2 options.');

			}else{
				if ($('#namelabel').val() != "" && $('#namelabel').val()) {
					if (($('#select_depend_column').val() != "internal"  &&  $('#selmul_values_depend').val() != "") || ($('#select_depend_column').val() == "internal")) {
						var arreglo = {};
						if ($('#select_depend_column').val() == "internal") {
							arreglo ={
								namelabel: $('#namelabel').val(),
								parent: {
									"depend": "internal",
									"value": ""
								},
								type: {}
							}
						}else{
							if ($('#selmul_values_depend').val() == "" || !$('#selmul_values_depend').val()) {
									toast_error('¡Oh, hubo un error!','Ingrese un valor a su dependendia.');
				          return;
							}else{
								var values = $('#selmul_values_depend').val();
			                	arreglo ={
									namelabel: $('#namelabel').val(),
									parent: {
										"depend": $('#select_depend_column').val(),
										"depend_fk_table": $("#label_table_fk").text(),
										"depend_column_fk": $("#cols_tableref_deped").val(),
										"value": values
									},
									type: {}
								}
							}
						}
						arreglo['type']['input'] = $('#select_type_input').val();
						arreglo['type']['names'] = {};
						$(this).closest('tr').hide('slow', function(){
							$(this).closest('tr').remove();
							$("#columns_names tr").each(function(){
									arreglo['type']['names'][$(this).find('.name_input').html()] = $(this).find('.alias_input').html();
							});
							send_comment_ajax($("#select_tables").val(),field_select,type_select, null_select, JSON.stringify(arreglo), tr_sel);
						});
					}else{
							toast_error('¡Oh, hubo un error!','Ingrese un valor a su dependendia.');
					    return;
					}
				}else{
						toast_error('¡Oh, hubo un error!','Ingrese un nombre a su label.');
				    return;
				}
			}
		});
		$("#columns_names").on ("click", ".edit_name_input", function (event) { //INTO TABLE OF SELCT/RADIO/CHECKBOC
			event.preventDefault();
			$(this).closest('tr').addClass('ultimo-input')
			$(this).parent().siblings('.name_input').html('<input class="p-input form-control letrasnumeros " value="'+$(this).parent().siblings('.name_input').html()+'" type="text">');
			$(this).parent().siblings('.alias_input').html('<input class="p-input form-control letrasnumeros " value="'+$(this).parent().siblings('.alias_input').html()+'" type="text">');
			$(this).parent().html('<a href="#" class="save_input_name manage_form"><i class="fa fa-save font-20"></i><span class="text-muted"></span></a><a href="#" class="manage_form remove_name_input"><i class="mdi mdi-delete"></i><span class="text-muted"></span></a>');
		});
		//GUARDADO Y CAMBIO DE DATOS CON BUTTON SAVE*******************************************************************
		$('#save_input_column').on('click', function (e) {
			e.preventDefault();
			var arreglo = {};
			//verificar si está check el create input
			if ($('#check_create_input').is(':checked')) {
				if($('#namelabel').val() != "" && $('#namelabel').val()){
					if ($('#select_depend_column').val() == "internal") {
						arreglo = {
							list: tr_sel.find('.checkbox_td').is(":checked"),
							namelabel: $('#namelabel').val(),
							parent: {
								"depend": "internal",
								"value": ""
							},
							type: {}
						}
						$('#columns_names').empty();
					}else{
						if ($('#selmul_values_depend').val() == "" || !$('#selmul_values_depend').val()) {
							$.toast({
						        heading: '¡Oh, hubo un error!',
						        text: 'Ingrese los diferentes valores a su dependencia',
						        position: 'top-right',
						        loaderBg: '#bf441d',
						        icon: 'error',
						        hideAfter: 3000,
						        stack: 1
						    });
			                return;
						}else{
							if ($('#cols_tableref_deped').val()){
								var values = $('#selmul_values_depend').val();
			                	arreglo ={
			                		list: tr_sel.find('.checkbox_td').is(":checked"),
									namelabel: $('#namelabel').val(),
									parent: {
										"depend": $('#select_depend_column').val(),
										"depend_fk_table": $("#label_table_fk").text(),
										"depend_column_fk": $("#cols_tableref_deped").val(),
										"value": values
									},
									type: {}
								}
								$('#columns_names').empty();
							}else{
								toast_error('¡Oh, hubo un error!','Seleccione columna de la dependencia.');
			            return;
							}
						}
					}
				}else{
					toast_error('¡Oh, hubo un error!','Ingrese un nombre a su label.');
				    return;
				}
				if($('#select_type_input').val() == "text"){
					if ($('#select_validate_text').val() && $('#maxlength').val() != "") {
						arreglo['type'] = {
							"input" : $('#select_type_input').val(),
							"type_text" : $('#select_validate_text').val(),
							"maxlength" : $('#maxlength').val(),
							"names": ""
						}
						send_comment_ajax($("#select_tables").val(),field_select,type_select, null_select, JSON.stringify(arreglo), tr_sel);
						tr_sel.removeClass('tr_selected')
						$('#info_extra').hide('fast', function(){ $(this).remove(); });
					}else{
						toast_error('¡Oh, hubo un error!','Ingrese su respectiva validación.');
					}
				}else if ($('#select_type_input').val() == "checkbox" || $('#select_type_input').val() == "number" || $('#select_type_input').val() == "date" || $('#select_type_input').val() == "textarea" || $('#select_type_input').val() == "email" || $('#select_type_input').val() == "password") {
					arreglo['type'] = {
						"input" : $('#select_type_input').val(),
						"names": ""
					}
					send_comment_ajax($("#select_tables").val(),field_select,type_select, null_select, JSON.stringify(arreglo), tr_sel);
					tr_sel.removeClass('tr_selected')
					$('#info_extra').hide('fast', function(){ $(this).remove(); });
				}else if($('#select_type_input').val() == "clave_foranea"){
					if ($('#select_table_cf').val() != "" && $('#select_id_fk').val() != "" && $('#select_value_fk').val() != "") {
						arreglo['type'] = {
							"input" : $('#select_type_input').val(),
							"table" : $('#select_table_cf').val(),
							"id" : $('#select_id_fk').val(),
							"name": $('#select_value_fk').val()
						}
						send_comment_ajax($("#select_tables").val(),field_select,type_select, null_select, JSON.stringify(arreglo), tr_sel);
						tr_sel.removeClass('tr_selected')
						$('#info_extra').hide('fast', function(){ $(this).remove(); });
					}else{
						toast_error('¡Oh, hubo un error!','Ingrese todos los datos de clave foranea.');
					}
				}else{
					toast_error('¡Oh, hubo un error!','Ingrese el tipo del input.');
				}
			}else{
				send_comment_ajax($("#select_tables").val(),field_select,type_select, null_select, '', tr_sel);
				tr_sel.removeClass('tr_selected')
				$('#info_extra').hide('fast', function(){ $(this).remove(); });
				$(this).find(".status").html('<span class="label label-danger">Internal</span>')
			}
		});
	});
	$("#columns_table").on ("click", ".checkbox_td", function (event) { //SELECT LA TUERCA DE CADA COLUMNA
		continius = true;
	})
	$("#columns_table").on ("click", ".name_label_tr", function (event) { //SELECT LA TUERCA DE CADA COLUMNA
		continius = true;
	})
	$("#columns_table").on ("change", ".checkbox_td", function (event) {
        if($(this).is(":checked")) {
        	if ($(this).closest('tr').find('.comment').html()) {
        		$comment = JSON.parse($(this).closest('tr').find('.comment').html())
        		$comment['list'] = true;
        		if ($(this).parent().find(".name_label_tr").length > 0) {
        			$comment['namelabel'] = $(this).parent().find(".name_label_tr").val();
        		}
        		send_comment_ajax($('#select_tables').val(),$(this).closest('tr').find('.field').html(),$(this).closest('tr').find('.type').html() , $(this).closest('tr').find('.null').html(), JSON.stringify($comment))
        	}else{
        		var arreglo = {list: true, namelabel: $(this).parent().find(".name_label_tr").val()}
        		send_comment_ajax($('#select_tables').val(),$(this).closest('tr').find('.field').html(),$(this).closest('tr').find('.type').html() , $(this).closest('tr').find('.null').html(), JSON.stringify(arreglo))
        	}
        }else{
        	if ($(this).closest('tr').find('.comment').html()) {
        		$comment = JSON.parse($(this).closest('tr').find('.comment').html(), )
        		$comment['list'] = false;
        		if ($(this).parent().find(".name_label_tr").length > 0) {
        			$comment['namelabel'] = $(this).parent().find(".name_label_tr").val();
        		}
        		send_comment_ajax($('#select_tables').val(),$(this).closest('tr').find('.field').html(),$(this).closest('tr').find('.type').html() , $(this).closest('tr').find('.null').html(), JSON.stringify($comment))
        	}else{
        		var arreglo = {list: false}
        		if ($(this).parent().find(".name_label_tr").length > 0) {
        			$comment['namelabel'] = $(this).parent().find(".name_label_tr").val();
        		}
        		send_comment_ajax($('#select_tables').val(),$(this).closest('tr').find('.field').html(),$(this).closest('tr').find('.type').html() , $(this).closest('tr').find('.null').html(), JSON.stringify(arreglo))
        	}
        }
    });
	
});
	function listar_referenced_table($name_table, $name_column, $values = false) {
		$.ajax({
            url: site_url + '/admin/get_referenced_table',
            data : {name_table: $name_table, name_column: $name_column},
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
            success: function(res)
            {
            	$("#loading-circle-overlay").hide();
            	if (res) {
            		get_data_dependence(res[0]['REFERENCED_TABLE_NAME'], $values)
            	}
            },
            error:function(e){
                $("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
            }
        });
	}
	function get_data_dependence($name_table, $values = false) {
		$.ajax({
            url: site_url + '/admin/get_data_dependence',
            data : {name_table: $name_table},
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
            success: function(res)
            {
				$("#loading-circle-overlay").hide();
            	if (res) {
					res.forEach(function(element) {
						$('#selmul_values_depend').append('<option value="'+Object.values(element)[0]+'">'+Object.values(element)[1]+'</option>');

					});
					if ($values) {
						$('#selmul_values_depend').val($values).change()
					}
            	}
            },
            error:function(e){
                $("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
            }
        });
	}
	function get_referenced_table($name_table, $name_column, $dependece = false) {
		$.ajax({
            url: site_url + '/admin/get_referenced_table',
            data : {name_table: $name_table, name_column: $name_column},
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
            success: function(res)
            {
				$("#loading-circle-overlay").hide();
                if (res) {
                	if (!$dependece) {
	                	$('#select_table_cf').html('<option value="'+res[0]['REFERENCED_TABLE_NAME']+'" selected="true">'+res[0]['REFERENCED_TABLE_NAME']+'</option>');
	                	$('#select_table_cf').prop('disabled','disabled');
                	}else{
                		$('#label_table_fk').text(res[0]['REFERENCED_TABLE_NAME']);
						get_columns_only_name(res[0]['REFERENCED_TABLE_NAME'], "","", true);

                	}
                }
            },
            error:function(e){
                $("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
            }
        });
	}
	function send_comment_ajax($select_table,field_select,type_select, null_select,$comment, $tr_sel = false) {
		$.ajax({
            url: site_url + '/admin/send_comment',
            data : {name_table: $select_table, name_column: field_select,type_column: type_select, null_column: null_select, comment_column: $comment},
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
            success: function(res)
            {
				$("#loading-circle-overlay").hide();
                if (res) {
                	if ($comment == "") {
                		if ($tr_sel) {
							$tr_sel.find(".status").html('<span class="label label-danger">Internal</span>')
                		}
                	}else{
                		if ($tr_sel){
							$tr_sel.find(".status").html('<span class="label label-success">Activo</span>')
                		}
                	}
                	if ($tr_sel){
                		$tr_sel.find(".comment").html($comment);
                	}
					toast_success('¡Muy bien!','¡Sus datos se guardaron correctamente!');
                }
            },
            error:function(e){
                $("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
            }
        });
	}
	function get_columns_only_name($name_table, $id_fk = "", $name_fk = "", $dependece = false, $values = false) {
		$.ajax({
	        url: site_url + 'form/get_columns_only_name',
	        data : {name_table: $name_table},
	        type: 'post',
	        dataType: 'json',
	        beforeSend: function(){
              $("#loading-circle-overlay").show();
            },
	        success: function(res){
				$("#loading-circle-overlay").hide();
				if (res) {
					if (!$dependece) {
						for (var i = 0; i < res.length; i++) {
							$('#select_id_fk').append('<option value="'+res[i]['Field']+'" '+(res[i]['Key'] =="PRI" ? "selected" : "")+'>'+res[i]['Field']+'</option>')
							$('#select_value_fk').append('<option value="'+res[i]['Field']+'" '+($name_fk == res[i]['Field']? "selected" : "")+'>'+res[i]['Field']+'</option>')
						}
						if ($('#select_id_fk').val() == "" || !$('#select_id_fk').val()) { //SI NO SE ENCONTRÓ EL PK EN LA TABLE, SE ACTIVA PARA Q SELECCIONE
							$('#select_id_fk').prop("disabled",false);
						}
					}else{
						$('#cols_tableref_deped').empty();
						$('#cols_tableref_deped').append('<option value="" selected disabled>Select a FK o PK</option>')
						for (var i = 0; i < res.length; i++) {
							if (res[i]['Key']) {
								res[i]['Key'] = (res[i]['Key']=="MUL"? "FK": "PK");
								$('#cols_tableref_deped').append('<option value="'+res[i]['Field']+'" '+($name_fk == res[i]['Field']? "selected" : "")+' data-fk="'+res[i]['Key']+'">'+res[i]['Field']+' ('+res[i]['Key']+')</option>')
							}
						}
						if ($('#cols_tableref_deped').find(':selected').data('fk') == "PK") {
							if ($values) {
			        			get_data_dependence($('#label_table_fk').text(), $values)
							}
			        	}else{
							if ($values) {
			        			listar_referenced_table($('#label_table_fk').text(), $('#cols_tableref_deped').val(), $values);
							}
			        	}
					}
				}
	        },
	        error:function(e){
				$("#loading-circle-overlay").hide();
				toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
	        }
	    });
	}