<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Ordenes( '../' );

$nombre = $ESObject->gettecniconame($_GET['tecnicoid']);


header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

if($_GET['delete'] == 1)
{
	$position = $_GET['position'];
	unset($_SESSION['tecnicos'][$position]);
	$_SESSION['tecnicos'] = array_values($_SESSION['tecnicos']);
}
elseif($_GET['tecnicoid']!=0)
{
	$temp_array['tecnicoid'] = $_GET['tecnicoid'];
	$temp_array['tecnico'] = $nombre;

	$_SESSION['tecnicos'][] = $temp_array;
}

echo '<center><table border="0">
      <tr>';

	  
	for($i = 0; $i < count($_SESSION['tecnicos']); $i++)
	{
		echo '<td>'.$_SESSION['tecnicos'][$i][tecnico].'</td>';
		echo '<td><strong><a href="javascript:eliminar('.$i.')">X</a></strong></td>';
		echo '</tr></r>';
	}
	
echo '</tr>
    </table></center>';	

?>