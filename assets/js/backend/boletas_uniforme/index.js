const fetch_post = async (url, data) => {
	$("#loading-circle-overlay").show();
	const response = await fetch(url, {
		method: 'POST',
		body: data,
		headers: {
			"Content-Type": "application/x-www-form-urlencoded",
		},
	});
	$("#loading-circle-overlay").hide();
	if (response.ok) {
		try{
			return await response.json();
		}catch(e){
			toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
		}
	}else{
		toast_error('¡Oh, hubo un error!','Vuelva a intentarlo.');
	}
}
$(document).ready(function() {
// JS REGISTER START*************************************************************
	$('#add_detail').on('click', e => {
		e.preventDefault();
		$('#btn_submit').text('AGREGAR');
		$('#modal_detail').modal();
	});
	$('#estado_boleta').on('change', e => {
		if ($('#estado_boleta').val() == "anulado") {
			$('#apellido_pat').val("ANULADO");
			$('#apellido_mat').val("ANULADO");
			$('#nombres').val("ANULADO");
			$('#dni').val(0);
		}else{
			$('#apellido_pat').val("ANULADO");
			$('#apellido_mat').val("ANULADO");
			$('#nombres').val("ANULADO");
			$('#dni').val("ANULADO");
		}
	})

	let index = 1;
	if ($('#form_add_detail').length > 0) {
		document.querySelector('#form_add_detail').addEventListener('submit', (e) => {
			e.preventDefault();
			const edit = document.querySelector('#index_to');
			const inputs = document.querySelectorAll('.input_detail');
			if (edit.value && parseInt(edit.value) > 0) {
				inputs.forEach(input => {
					$(`#tr_${edit.value}`).find(`.${input.name}`).text(input.value)
				})
				if ($('#thispage').length > 0 && $('#thispage').val() == "edit") {
					fetch_post(`${site_url}/BoletasUnif/edit_detalle_id`, $('#form_add_detail').serialize()).then(res => {
						if (res) {
							if (res.success) {
								swal('Bien!', res.msg, 'success' );
							}else{
								swal('Error!', 'Hubo un error, vuelva a intentarlo', 'error')
							}
						}
					});
				}
				edit.value = 0;
			}else{
				const tr = document.createElement('tr');
				tr.id = `tr_${index}`;
				tr.dataset.index = index;
				inputs.forEach(input => {
					tr.innerHTML += `<td class="input_detail_row ${input.name}" data-name="${input.name}">${input.value}</td>`
				})
				tr.innerHTML += `<td class="text-center">
				<a href="#" class="edit_row"><i class="fa fa-edit mr-2" style="font-size: 25px"></i></a>
				<a href="#" class="delete_row"><i class="fa fa-trash" style="font-size: 25px"></i></a> </td>`
				document.querySelector('#columns_table').appendChild(tr);
				index++;
				if ($('#thispage').length > 0 && $('#thispage').val() == "edit") {
					fetch_post(`${site_url}BoletasUnif/edit_detalle_insert`, $('#form_add_detail').serialize()+`&nro_boleta=${$('#nro_boleta').val()}`).then(res => {
						if (res) {
							if (res.success) {
								swal('Bien!', res.msg, 'success' );
							}else{
								swal('Error!', 'Hubo un error, vuelva a intentarlo', 'error')
							}
						}
					});
				}
			}
			document.querySelector('#form_add_detail').reset();
			$('#modal_detail').modal('hide');
			
		})
	}
	$("#columns_table").on ("click", ".edit_row", function (e) {
		e.preventDefault();

		$('#modal_detail').modal('show');
		$('#detalle').val($(this).closest('tr').find('.detalle').text());
		$('#estado').val($(this).closest('tr').find('.estado').text());
		$('#color').val($(this).closest('tr').find('.color').text());
		$('#cantidad').val($(this).closest('tr').find('.cantidad').text());
		$('#talla').val($(this).closest('tr').find('.talla').text());
		$('#index_to').val($(this).closest('tr').data('index'));
		$('#btn_submit').text('MODIFICAR');
	})
	$("#columns_table").on ("click", ".delete_row", function (e) {
		e.preventDefault();
		$(this).closest('tr').hide('slow/400/fast', () => {
			$(this).closest('tr').remove();
		});
		if ($(this).data('id_detalle')) {
			const id_detalle = $(this).data('id_detalle');
			fetch_post(`${site_url}/BoletasUnif/edit_vol_del_detalle`, `id_detalle=${id_detalle}`).then(res => {
				if (res) {
					if (res.success) {
						swal('Bien!', res.msg, 'success' );
						$('#columns_table').empty();
					}else{
						swal('Error!', 'Hubo un error, vuelva a intentarlo', 'error')
					}
				}
			});
		}
	});

	$('#form_insert_boleta').on('submit', e => {
		e.preventDefault();
		if ($('#columns_table tr').length > 0 || $('#estado_boleta').val() == "anulado") {
			let data = [];
			let index = 0;
			let to_send = "";
			if ($('#estado_boleta').val() == "anulado") {
				to_send = $('#form_insert_boleta').serialize();
			}else{
				document.querySelectorAll('#columns_table tr').forEach(tr => {
					data.push(index);
					data[index] = {}
					tr.querySelectorAll('.input_detail_row').forEach(td => {
						data[index][td.dataset.name] = td.textContent
					});
					data[index]['nro_boleta'] = $('#nro_boleta').val()
					index++;
				})
				to_send = $('#form_insert_boleta').serialize()+"&data_extra="+JSON.stringify(data);
			}
			fetch_post(`${site_url}/BoletasUnif/register_boleta`, to_send).then(res => {
				if (res) {
					if (res.success) {
						swal('Bien!', res.msg, 'success' );
						$('#columns_table').empty();
						document.querySelector('#form_insert_boleta').reset();
					}else{
						swal('Error!', 'Hubo un error, vuelva a intentarlo', 'error')
					}
				}else{
					swal('Error!', 'Hubo un erro vuelva a intentarlo', 'error')
				}
			});
		}else{
			swal('Error!', 'El detalle de la boleta esta vacia', 'error')
		}
	})
// JS REGISTER END*************************************************************	

// JS LISTAR START*****************************************************************
	if ($('#datatable').length > 0) {
		var table = $('#datatable').DataTable({
			lengthChange: false,
			buttons: [ 'excel', 'pdf', 'colvis'],
			"scrollX": true,
			"order": [[ 0, "desc" ]],
			
		});
		table.buttons().container()
		.appendTo('#datatable_wrapper .col-md-6:eq(0)');
	}
// JS LISTAR END*****************************************************************
// JS EDIT START*****************************************************************
	$('#form_edit_boleta').on('submit', e => {
		e.preventDefault();
		fetch_post(`${site_url}/BoletasUnif/edit_vol_nro_vol`, $('#form_edit_boleta').serialize()).then(res => {
			if (res) {
				if (res.success) {
					swal('Bien!', res.msg, 'success' );
					$('#columns_table').empty();
				}else{
					swal('Error!', 'Hubo un error, vuelva a intentarlo', 'error')
				}
			}else{
				swal('Error!', 'Hubo un erro vuelva a intentarlo', 'error')
			}
		});
	});
// JS EDIT END*****************************************************************
})

