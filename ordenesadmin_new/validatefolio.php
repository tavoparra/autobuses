<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Ordenes( '../' );

if(!isset($_GET['ordenid']))
	$ordenid = '0';
else
	$ordenid = $_GET['ordenid'];
	
if($ESObject->validatefolio($_GET['folio'], $ordenid) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>