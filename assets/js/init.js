jQuery(document).ready(function () {
    // Select2
    $(".select2").select2();

	$('.menu_dynamic').on('click', function (e) {
		e.preventDefault();
	})
});
function redirect_menu($action, $name_table, $alias_table){
	if ($('#menu_dynamic').length > 0) {
		$('#menu_dynamic').remove();
	}
	$.redirectPostmenu($action, {name_table: $name_table, alias_table: $alias_table});
}
$.extend(
{
	redirectPostmenu: function(location, args, target = "")
	{
		var form = '';
		$.each( args, function( key, value ) {
			form += '<input type="hidden" name="'+key+'" value="'+value+'">';
		});
		$(document.body).append('<form '+target+' id="menu_dynamic" action="'+location+'" method="POST">'+form+'</form>');
		$('#menu_dynamic').submit();
	}
});