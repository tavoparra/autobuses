<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Units( '../' );


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

$talleres = $ESObject->gettalleres($_GET['clienteid']);

echo '<select name="tallerid" id="tallerid"  onchange="cargarunidades(this.value);">
		  <option value="0">- SELECCIONAR -</option>
          	'.$talleres.'
          </select>';
?>

