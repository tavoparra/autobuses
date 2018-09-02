<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new clientes( '../' );

if(!isset($_GET['clienteid']))
	$clienteid = '0';
else
	$clienteid = $_GET['clienteid'];
	
if($ESObject->validaterfc($_GET['rfc'], $clienteid) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>