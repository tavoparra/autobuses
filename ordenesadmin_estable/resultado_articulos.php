<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Ordenes( '../' );

if($_GET['clienteid'] != '')
	$precios = $ESObject->getclientelistaprecios($_GET['clienteid']);
else
	$precios = 1;

	
$articulos = $ESObject->searcharticles($_GET['valor'], $_GET['campo'], $precios);


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

echo '<table border="0">
      <tr><input type="hidden" name="id" value="x"></tr>';
if ( !$articulos->EOF )
{
    do
    {
		echo '<tr>
        <td><label>
          <input type="radio" name="id" value="'.$articulos->fields["id"].'" />
        <strong>'.$articulos->fields["name"].' ('.$articulos->fields["code"].'):</strong> '.$articulos->fields["desc"].'</label>
		<input type="hidden" name="article'.$articulos->fields["id"].'" id="article'.$articulos->fields["id"].'" value = "'.$articulos->fields["code"].'"/>
		<input type="hidden" name="price'.$articulos->fields["id"].'" id="price'.$articulos->fields["id"].'" value = "'.$articulos->fields["price"].'"/>
		</td>
      </tr>';
		$articulos->MoveNext( );
    }
    while ( !$articulos->EOF );
	echo '<tr>
        <td><div align="center">
          <label>
          <input type="button" name="seleccionar" id="seleccionar" value="Seleccionar" onclick="returnvalue();" />
          </label>
        </div></td>
      </tr>';
}
else
{
	echo '<tr>
        <td><div align="center">No se encontraron articulos</div></td>
      </tr>';
}
	
echo '</table>';
?>