<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new units( '../' );

$unidadid = isset($_GET['unidadid']) ? $_GET['unidadid'] : '0';
$clienteid = isset($_GET['clienteid']) ? $_GET['clienteid'] : '0';
	
if($ESObject->validatecodigo($_GET['codigo'], $unidadid, $clienteid) > 1)
	echo "EXISTE";
else
	echo "NO EXISTE";
?>