function MsgOkCanceldrop( msg, redirect ){
	if ( confirm( msg )){
		document.forms.Form1.redirect.value = redirect;
		document.forms.Form1.submit();
	}
}

function Trim( str ){
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
	
function checar_codigo(codigo){	   	
	var contenedor;
	contenedor = document.getElementById('codigo_validate');
	if(codigo != ''){
		contenedor.innerHTML = "Verificando ...";
		tecnicoid = document.getElementById('numeroshow').value;
		ajax5 = nuevoAjax();
		ajax5.open( "GET", "validatecodigo.php?codigo=" + codigo + "&tecnicoid=" + tecnicoid, true);
		ajax5.onreadystatechange = function(){	
			if ( ajax5.readyState == 4 ) 
			{	
				if(ajax5.responseText == "EXISTE"){
					contenedor.innerHTML = '<img src="../imagenes/red_x.png" width="15" height="15"/> Ese c&oacute;digo ya existe';
					document.getElementById("codigo_validado").value = 0;
				}
				else{
					contenedor.innerHTML = '<img src="../imagenes/green_check.png" width="15" height="15"/>';
					document.getElementById("codigo_validado").value = 1;
				}
			}
			else{
				contenedor.innerHTML = "Verificando ...";			
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
	
function validRFC(el){
	if (!el.value.test(/[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?/)) {
		el.errors.push("El RFC es inv&aacute;lido");
		return false;
	} else {
		return true;
	}
}