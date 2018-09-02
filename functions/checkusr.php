<?php
session_start();

$name = $_POST["usrname"];
$pass = $_POST["pass"];

include_once('functions.inc.php');
$DBObject=new DBFunciones('../');
$DBObject->verificarUsuario( $name, $pass );
$DBObject->Close();
?>