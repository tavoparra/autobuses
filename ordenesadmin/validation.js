var nav4 = window.Event ? true : false;
function IsNumber(evt){
// Backspace = 8, Enter = 13, '0' = 48, '9' = 57, '.' = 46
var key = nav4 ? evt.which : evt.keyCode;
return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function MsgOkCanceldrop(msg, redirect){
	if ( confirm( msg ) ){
		document.forms.Form1.redirect.value = redirect;
		document.forms.Form1.submit();
	}
}

function ismaxlength(obj){
	var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
	if (obj.getAttribute && obj.value.length>mlength)
		obj.value=obj.value.substring(0,mlength)
}

function validate(val){
	document.forms.Form1.redirect.value = val;
	if(document.getElementById('folio_validate').innerHTML == 'Verificando ...')
		alert('Validando folio, espere un momento y vuelva a intentar');
	else
		document.forms.Form1.submitBtn.click();
}	

function agregar_tecnico(){
	var contenedor;
	contenedor = document.getElementById('tecnicos_div');	
	tecnicoid = document.getElementById('tecnicoid').value;
	
	ajax = nuevoAjax();
	ajax.open( "GET", "cargartecnicos.php?tecnicoid=" + tecnicoid, true );
	ajax.onreadystatechange = function(){	
		if ( ajax.readyState == 4 ) {
			contenedor.innerHTML = ajax.responseText
		}
		else{
			contenedor.innerHTML = "... Cargando ...";
		}
	}
	ajax.send( null); 
}
	
jQuery(document).ready(function(){
	jQuery("#forma_pago").change(function(){
		if(jQuery(this).val() == 3)
			jQuery("#dias_credito").show();
		else
			jQuery("#dias_credito").hide();
	});
});
	
function eliminar(tecnicoPosition){
	jQuery.ajax({
		type: "GET",
		url: "cargartecnicos.php",
		dataType: "html",
		data: {delete : 1, position: tecnicoPosition},
		success: function(data){
			jQuery("#tecnicos_div").html(data);
		}
	});
}
		
function quitar_articulo(position){ 	
	var contenedor;
	contenedor = document.getElementById('refacciones_table');
	tipo_cambio = document.getElementById('tipo_cambio').value;
	ajax = nuevoAjax();
	ajax.open( "GET", "cargararticulos.php?delete=1&position=" + position + "&tipo_cambio=" + tipo_cambio, true);
	ajax.onreadystatechange = function() 
	{	
		if ( ajax.readyState == 4 ) 
		{
			contenedor.innerHTML = ajax.responseText
		}
		else
		{
			contenedor.innerHTML = "Cargando ...";			
		}
	}
	ajax.send( null); 
}
	
function checar_folio(folio){	   	
	var contenedor;
	contenedor = document.getElementById('folio_validate');
	if(folio != ''){
		contenedor.innerHTML = "Verificando ...";
		ordenid = document.getElementById('numeroshow').value;
		ajax5 = nuevoAjax();
		ajax5.open( "GET", "validatefolio.php?folio=" + folio + "&ordenid=" + ordenid, true);
		ajax5.onreadystatechange = function() 
		{	
			if ( ajax5.readyState == 4 ) 
			{	
				if(ajax5.responseText == "EXISTE")
				{
					contenedor.innerHTML = '<img src="../imagenes/red_x.png" width="15" height="15"/> Ese folio ya existe';
					document.getElementById("folio_validado").value = 0;
				}
				else
				{
					contenedor.innerHTML = '<img src="../imagenes/green_check.png" width="15" height="15"/>';
					document.getElementById("folio_validado").value = 1;
				}
			}
			else
			{
				contenedor.innerHTML = "Verificando ...";			
			}
		}
		ajax5.send( null);
	}
	else{
		contenedor.innerHtml = "";
	}
}

function validFolio(el){
	if (document.getElementById("folio_validado").value != '1') {
		el.errors.push("El folio debe ser &uacute;nico");
		return false;
	} else {
		return true;
	}
}