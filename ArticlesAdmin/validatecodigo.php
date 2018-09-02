<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new articles( '../' );

if(!isset($_GET['id']))
	$id = '0';
else
	$id = $_GET['id'];
	
if($ESObject->validatecodigo($_GET['codigo'], $id) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>