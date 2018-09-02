function MsgOkCanceldrop( msg, redirect){
	if ( confirm( msg ) )
	{
		document.forms.Form1.redirect.value = redirect;
		document.forms.Form1.submit();
	}
}

function cargarciudades(estadoid){
	    var contenedor;
		contenedor = document.getElementById('ciudadid');
		ajax = nuevoAjax();
		ajax.open( "GET", "cargarciudades.php?estadoid=" + estadoid, true );
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
	
	function eliminar(position)
   {	   	
		var contenedor;
		contenedor = document.getElementById('contactos_table');
		ajax = nuevoAjax();
		ajax.open( "GET", "mostrartabla.php?delete=1&position=" + position);
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
	
	function checar_codigo(codigo)
   {	   	
		var contenedor = document.getElementById('codigo_validate');
		if(codigo != '')
		{
			contenedor.innerHTML = "Verificando ...";
			var tallerid = document.getElementById('numeroshow').value;
			var clienteid = document.getElementById('clienteid').value;
			
			if(document.getElementById('local').checked) clienteid = '-1';
			
			ajax5 = nuevoAjax();
			ajax5.open( "GET", "validatecodigo.php?codigo=" + codigo + "&tallerid=" + tallerid + "&clienteid=" + clienteid, true);
			ajax5.onreadystatechange = function() 
			{	
				if ( ajax5.readyState == 4 ) 
				{	
					if(ajax5.responseText == "EXISTE")
					{
						contenedor.innerHTML = '<img src="../imagenes/red_x.png" width="15" height="15"/> Ese c&oacute;digo ya existe';
						document.getElementById("codigo_validado").value = 0;
					}
					else
					{
						contenedor.innerHTML = '<img src="../imagenes/green_check.png" width="15" height="15"/>';
						document.getElementById("codigo_validado").value = 1;
					}
				}
				else
				{
					contenedor.innerHTML = "Cargando ...";			
				}
			}
			ajax5.send( null);
		}
		else
		{
			contenedor.innerHTML = '';
		}
	}
	
	function mostrar_ocultar_cliente(taller_refri)
	{
		var eldiv = document.getElementById("cliente_div");
		
		eldiv.style.display = (taller_refri) ? "none" : "block";
		document.getElementById('taller_cod').onchange();
	}

function Trim( str ) 
{
	var resultStr = str;
	resultStr = resultStr.replace( /^\s*|\s*$/g, "" ); 	
	return resultStr;
}


function validate(val){
	document.forms.Form1.redirect.value = val;
	if(document.getElementById('codigo_validate').innerHTML == 'Verificando ...')
		alert('Validando código, espere un momento y vuelva a intentar');
	else
		document.forms.Form1.submitBtn.click();
}	

function Mostrar()
   {	   	
		var nombre;
		nombre = document.getElementById("nombre_contacto").value;
		document.getElementById("nombre_contacto").value  = "";
		var email;
		email = document.getElementById("contacto_email").value;
		document.getElementById("contacto_email").value  = "";
		var telefono;
		telefono = document.getElementById("contacto_tel").value;
		document.getElementById("contacto_tel").value  = "";
		var extension;
		extension = document.getElementById("extension").value;
		document.getElementById("extension").value  = "";
		var contenedor;
		contenedor = document.getElementById('contactos_table');
		ajax = nuevoAjax();
		ajax.open( "GET", "mostrartabla.php?nombre=" + nombre + "&email=" + email + "&telefono=" + telefono + "&extension=" + extension, true );
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
	
function validCode(el){
	if (document.getElementById("codigo_validado").value != '1') {
		el.errors.push("El c&oacute;digo debe ser &uacute;nico");
		return false;
	} else {
		return true;
	}
}

function validCliente(el){
	if (!el.checked & document.getElementById('clienteid').value == '') {
		el.errors.push("Si no es un taller RefriServicio debes seleccionar un cliente");
		return false;
	} else {
		return true;
	}
}
