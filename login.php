<?php
//Iniciamos la sesion
session_start();
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define('IN_EMADMIN', true);
//Agregamos la libreria de plantillas
require_once('functions/templates.inc.php');
//Agregamos el archivo de variables comunes
include_once('config/varload.inc.php');
if(isset($_GET['er'])){
  $err=$_GET['er'];
}else{
  $err="";
}
$dir="";

//**         CABECERA Y MENU IZQ            **//
$Contenido=new template();
$Contenido->addTemplate("header");
$Contenido->asigna_variables(array ("PAGE_NAME" => $arreglo['LOGTITLE'],
									"BGCOLOR" => "#E0E1E3", "DIR" => $dir

							) );
$Contenido->compileandgo();


//**             PAGINA PRINCIPAL             **//
	if($err==1){
		$errormsg='<img src="imagenes/errorlogin.png" width="305" height="45">';
	}else{
		$errormsg="&nbsp;";
	}
	
$Contenido->addTemplate("logbody");
$Contenido->asigna_variables(array("ERROR" => $errormsg ));
$Contenido->compileandgo();


//**             PIE DE PAGINA             **//

$Contenido->addTemplate("footer");
$Contenido->asigna_variables(array("DIR" => $dir,"MAP" => ""));
$Contenido->compileandgo();
?>