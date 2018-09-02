<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new clientes( '../' );


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

$ciudades = $ESObject->getciudades($_GET['estadoid']);

echo 'Ciudad: <select name="ciudadid" id="ciudadid">
          	'.$ciudades.'
          </select>';
?>

