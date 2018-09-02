function MsgOkCanceldrop( msg, redirect )
{
	if ( confirm( msg ) )
	{
		document.forms.FormUnidad.redirect.value = redirect;
		document.forms.FormUnidad.submit();
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
	
	function checar_codigo(codigo)
   {	   	
		contenedor = document.getElementById('codigo_validate');
		if(codigo != '')
		{
			contenedor.innerHTML = "Verificando ...";
			unidadid = document.getElementById('numeroshow').value;
			clienteid = document.getElementById('clienteid').value;
			
			ajax5 = nuevoAjax();
			ajax5.open( "GET", "validatecodigo.php?codigo=" + codigo + "&unidadid=" + unidadid + "&clienteid=" + clienteid, true);
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
	
	function Trim( str ) 
{
	var resultStr = str;
	resultStr = resultStr.replace( /^\s*|\s*$/g, "" ); 	
	return resultStr;
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

function validCode(el){
	if (document.getElementById("codigo_validado").value != '1') {
		el.errors.push("El n&uacute;mero ec&oacute;nomico debe ser &uacute;nico");
		return false;
	} else {
		return true;
	}
}
	
function validate(val){
	document.forms.Form2.redirect.value = val;
	if(document.getElementById('codigo_validate').innerHTML == 'Verificando ...')
		alert('Validando número ecónomico, espere un momento y vuelva a intentar');
	else
		document.forms.Form2.submitBtn.click();
}	