var jQuery = jQuery.noConflict();
jQuery(document).ready(
	function(){
		jQuery('#dock').Fisheye({
			maxWidth: 50,
			items: 'a',
			itemsText: 'span',
			container: '.dock-container',
			itemWidth: 40,
			proximity: 90,
			halign : 'center'
	})
});

function nuevoAjax(){
	var xmlhttp=false;
 	try {
 		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
 	}catch(e){
 		try{
 			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
 		} catch (E) {
 			xmlhttp = false;
 		}
  	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
 		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function checkEnter(e, formname){
	var characterCode; characterCode = e.keyCode; 
	if ( characterCode == 13 ){  
		document.forms[formname].submit();  
	}; 
}; 

function toUpper(campo){
	cadena = campo.value.toUpperCase();
	campo.value = cadena;
}

function sendForm( redirect ){
	document.forms[0].redirect.value = redirect;
	document.forms[0].submit();
}

function Trim( str ){
	var resultStr = str;
	resultStr = resultStr.replace( /^\s*|\s*$/g, "" ); 	
	return resultStr;
}