<?php
//Si esta variable no esta definida, entonces estan tratando de accesar por medio de URL
if (!defined('IN_EMADMIN')){
 die("Intento de Hacking");
}

//Carga el archivo de seguridad
include_once('../config/security.inc.php');
//Carga el archivo de variables
include_once('../config/varload.inc.php');
//Carga el archivo que contiene las funciones principales de la base de datos
include_once('functions/functions.inc.php');
//Carga el archivo que contiene las funciones de los templates
include_once('functions/templates.inc.php');

?>
