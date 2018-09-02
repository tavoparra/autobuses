<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new clientes( '../' );

if(!isset($_GET['clienteid']))
	$clienteid = '0';
else
	$clienteid = $_GET['clienteid'];
	
if($ESObject->validatecodigo($_GET['codigo'], $clienteid) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>