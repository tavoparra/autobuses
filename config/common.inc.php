<?php
//Si esta variable no esta definida, entonces estan tratando de accesar por medio de URL
if (!defined('IN_EMADMIN')){
 die("Intento de Hackeo");
}

//Carga el archivo de seguridad
include_once('security.inc.php');
//Carga el archivo de variables
include_once('varload.inc.php');
//Carga el archivo que contiene las funciones de los templates
include('templates.inc.php');

?>
