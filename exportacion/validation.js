var nav4 = window.Event ? true : false;
function IsNumber(evt){
// Backspace = 8, Enter = 13, '0' = 48, '9' = 57, '.' = 46
var key = nav4 ? evt.which : evt.keyCode;
return (key <= 13 || (key >= 48 && key <= 57));
}

jQuery(function(){
	jQuery(".abrir_mantenimientos").change(function(){
		jQuery(this).nextUntil().toggle("slow");
	});
})							

window.addEvent('domready', function(){
	new FormCheck('parametros');
});

window.addEvent('domready', function(){
	new FormCheck('parametros2');
});

window.addEvent('domready', function(){
	new FormCheck('parametros4');
});