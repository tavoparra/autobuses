<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new ordenes( '../' );


$unidades = $ESObject->searchunidades($_GET['valor'], $_GET['campo'], $_GET['clienteid']);


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

echo '<table border="0">
      <tr><input type="hidden" name="id" value="x"></tr>';
if ( !$unidades->EOF )
{
    do
    {
		echo '<tr>
        <td><label title="MARCA: '.$unidades->fields["marca"].'
TIPO: '.$unidades->fields["tipo"].'
MODELO: '.$unidades->fields["modelo"].'
SERIE UNIDAD: '.$unidades->fields["numSerie"].'">
          <input type="radio" name="id" value="'.$unidades->fields["unidadid"].'" />
        <strong>'.$unidades->fields["numEconomico"].' (Placas: '.$unidades->fields["placas"].')</label>
		<input type="hidden" name="unidad'.$unidades->fields["unidadid"].'" id="unidad'.$unidades->fields["unidadid"].'" value = "'.$unidades->fields["numEconomico"].'"/>
		</td>
      </tr>';
		$unidades->MoveNext( );
    }
    while ( !$unidades->EOF );
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
        <td><div align="center">No se encontraron unidades</div></td>
      </tr>';
}
	
echo '</table>';
?>