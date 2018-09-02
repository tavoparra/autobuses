<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new units( '../' );


if($_GET['local'] == "true")
{
$cliente_id = "-1";
}
else
{
$cliente_id = $_GET['clienteid'];
}


$talleres = $ESObject->searchtalleres($_GET['valor'], $_GET['campo'], $cliente_id);


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

echo '<table border="0">
      <tr><input type="hidden" name="id" value="x"></tr>';
if ( !$talleres->EOF )
{
    do
    {
		echo '<tr>
        <td><label>
          <input type="radio" name="id" value="'.$talleres->fields["tallerid"].'" />
        <strong>'.$talleres->fields["nombre"].' ('.$talleres->fields["taller_cod"].')</label>
		<input type="hidden" name="taller'.$talleres->fields["tallerid"].'" id="taller'.$talleres->fields["tallerid"].'" value = "'.$talleres->fields["nombre"].'"/>
		</td>
      </tr>';
		$talleres->MoveNext( );
    }
    while ( !$talleres->EOF );
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
        <td><div align="center">No se encontraron talleres</div></td>
      </tr>';
}
	
echo '</table>';
?>