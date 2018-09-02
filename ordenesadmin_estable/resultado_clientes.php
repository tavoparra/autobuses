<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new ordenes( '../' );


$clientes = $ESObject->searchclientes($_GET['valor'], $_GET['campo']);


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

echo '<table border="0">
      <tr><input type="hidden" name="id" value="x"></tr>';
if ( !$clientes->EOF )
{
    do
    {
		echo '<tr>
        <td><label>
          <input type="radio" name="id" value="'.$clientes->fields["clienteid"].'" />
        <strong>'.$clientes->fields["nombre"].' ('.$clientes->fields["cliente_cod"].'):</strong> '.$clientes->fields["rfc"].'</label>
		<input type="hidden" name="cliente'.$clientes->fields["clienteid"].'" id="cliente'.$clientes->fields["clienteid"].'" value = "'.$clientes->fields["nombre"].'"/>
		</td>
      </tr>';
		$clientes->MoveNext( );
    }
    while ( !$clientes->EOF );
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
        <td><div align="center">No se encontraron clientes</div></td>
      </tr>';
}
	
echo '</table>';
?>