<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new talleres( '../' );

$tallerid = (isset($_GET['tallerid'])) ? $_GET['tallerid'] : '0';
$clienteid = (isset($_GET['clienteid'])) ? $_GET['clienteid'] : '0';
	
if($ESObject->validatecodigo($_GET['codigo'], $tallerid, $clienteid) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>