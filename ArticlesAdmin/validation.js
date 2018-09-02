function MsgOkCanceldrop( msg, redirect )
{
	if ( confirm( msg ) )
	{
		document.forms.Form1.redirect.value = redirect;
		document.forms.Form1.submit();
	}
}

function ismaxlength(obj){
	var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
	if (obj.getAttribute && obj.value.length>mlength)
		obj.value=obj.value.substring(0,mlength)
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

function checar_codigo(codigo)
{	   	
	var contenedor;
	contenedor = document.getElementById('codigo_validate');
	contenedor.innerHTML = "Verificando ...";
	if(codigo != '')
	{
		articleid = document.getElementById('numeroshow').value;
		ajax5 = nuevoAjax();
		ajax5.open( "GET", "validatecodigo.php?codigo=" + codigo + "&id=" + articleid, true);
		ajax5.onreadystatechange = function() 
		{	
			if ( ajax5.readyState == 4 ) 
			{	
				if(ajax5.responseText == "EXISTE")
				{
					contenedor.innerHTML = '<img src="../imagenes/red_x.png" width="15" height="15"/> Ese código ya existe';
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
	else{
		contenedor.innerHTML = '';
	}
}

function validCode(el){
	if (document.getElementById("codigo_validado").value != '1') {
		el.errors.push("El c&oacute;digo debe ser &uacute;nico");
		return false;
	} else {
		return true;
	}
}