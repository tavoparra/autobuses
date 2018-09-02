<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new tecnicos( '../' );

if(!isset($_GET['tecnicoid']))
	$tecnicoid = '0';
else
	$tecnicoid = $_GET['tecnicoid'];
	
if($ESObject->validatecodigo($_GET['codigo'], $tecnicoid) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>