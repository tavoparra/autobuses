<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Ordenes( '../' );


$articulos = $ESObject->searcharticles($_GET['valor'], $_GET['campo']);


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
		<input type="hidden" name="article'.$articulos->fields["id"].'" id="article'.$articulos->fields["id"].'" value = "'.$articulos->fields["name"].'"/>
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