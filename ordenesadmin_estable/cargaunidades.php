<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Ordenes( '../' );


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

$talleres = $ESObject->gettalleres($_GET['tallerid']);

echo '<select name="unidadid" id="unidadid">
          	'.$talleres.'
          </select>';
?>

