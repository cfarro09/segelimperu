$(document).ready(function() {
	var table = $('#datatable-buttons').DataTable({
		lengthChange: false,
		buttons: [ 'excel', 'pdf', 'colvis'],
		"scrollX": true
	});
	table.buttons().container()
	.appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

	$('.edit_list').on('click', function (e) {
		e.preventDefault();
	})
	$('.remove_list').on('click', function (e) {
		e.preventDefault();
	})
});
function redirect_arg($valueid, $nameid, $nametable, $aliastable){
	if ($('#form_dynamic').length > 0) {
		$('#form_dynamic').remove();
	}
	$.redirectPost(base_url + "form/edit_form", {valueid: $valueid, nameid: $nameid, nametable : $nametable, aliastable: $aliastable});
}
function delete_reg($valueid, $nameid, $nametable){
	swal({
		title: '¿Estás seguro?',
		text: "No podrás revertir este cambio!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#4fa7f3',
		cancelButtonColor: '#d57171',
		confirmButtonText: 'Si, eliminar!'
	}).then(function () {
		$.ajax({
			url: site_url + 'form/delete_data',
			data : {valueid: $valueid, nametable: $nametable, nameid: $nameid},
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
							'Deleted!',
							res.msg,
							'success'
							)
						$('#'+$valueid).hide('slow/400/fast', function() {
							$('#valueid').remove();
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
}
$.extend(
{
	redirectPost: function(location, args)
	{
		var form = '';
		$.each( args, function( key, value ) {
			form += '<input type="hidden" name="'+key+'" value="'+value+'">';
		});
		$(document.body).append('<form target="_blank"  id="form_dynamic" action="'+location+'" method="POST">'+form+'</form>');
		$('#form_dynamic').submit();
	}
});