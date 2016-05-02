//var strength_actual_value = $('#strength_actual_value').siblings().children(':input').attr('id');
//var strength_actual_modifier = $('#strength_actual_modifier').siblings().children(':input').attr('id');
//
//$('#'+strength_actual_value).change(function(){
//	$('#'+strength_actual_modifier).val($('#'+strength_actual_value).val() / 2);
//})

$('[id-form="strength_actual_value"]').change(function(){
	$('[id-form="strength_actual_modifier"]').val($('[id-form="strength_actual_value"]').val() / 2);
})