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

function Trim( str ){
	var resultStr = str;
	resultStr = resultStr.replace( /^\s*|\s*$/g, "" ); 	
	return resultStr;
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
	
function cambiar_pago(pago_id){
	if(pago_id == 3){
		document.getElementById("dias_credito").style.display = "inline";
	}	
	else{
		document.getElementById("dias_credito").style.display = "none";
	}
}

function cargartalleres(clienteid){
	var contenedor;
	contenedor = document.getElementById('talleres_div');
	ajax = nuevoAjax();
	ajax.open( "GET", "cargartalleres.php?clienteid=" + clienteid, true );
	ajax.onreadystatechange = function() 
	{	
		if ( ajax.readyState == 4 ) 
		{
			contenedor.innerHTML = ajax.responseText
		}
		else
		{
			contenedor.innerHTML = "... Cargando ...";
		}
	}
	ajax.send( null); 
}
	
	
function cargarunidades(tallerid){
	var contenedor;
	contenedor = document.getElementById('unidades_div');
	ajax = nuevoAjax();
	ajax.open( "GET", "cargarunidades.php?tallerid=" + tallerid, true );
	ajax.onreadystatechange = function() 
	{	
		if ( ajax.readyState == 4 ) 
		{
			contenedor.innerHTML = ajax.responseText
		}
		else
		{
			contenedor.innerHTML = "... Cargando ...";
		}
	}
	ajax.send( null); 
}
	
function eliminar(position){	   	
		var contenedor;
		contenedor = document.getElementById('tecnicos_div');
		ajax = nuevoAjax();
		ajax.open( "GET", "cargartecnicos.php?delete=1&position=" + position);
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
	
function agregar_articulo(){
	    var contenedor;
		contenedor = document.getElementById('refacciones_table');
		
		cantidad = document.getElementById('cantidad_art').value;
		clave = document.getElementById('articulo_name').value;
		precio = document.getElementById('precio').value;
		clienteid = document.getElementById('clienteid').value;		
		tipo_cambio = document.getElementById('tipo_cambio').value;		
		
		ajax = nuevoAjax();
		ajax.open( "GET", "cargararticulos.php?cantidad=" + cantidad + "&clave=" + clave + "&precio=" + precio + "&clienteid=" + clienteid + "&tipo_cambio=" + tipo_cambio, true );
		ajax.onreadystatechange = function() 
		{	
			if ( ajax.readyState == 4 ) 
			{
				contenedor.innerHTML = ajax.responseText
			}
			else
			{
				contenedor.innerHTML = "... Cargando ...";
			}
		}
		ajax.send( null); 
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