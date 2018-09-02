<?php
session_start();
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

//print_r($_SESSION['contactos']);

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

$temp_array['nombre'] = $_GET['nombre'];
$temp_array['email'] = $_GET['email'];
$temp_array['telefono'] = $_GET['telefono'];
$temp_array['extension'] = $_GET['extension'];


$_SESSION['contactos'][] = $temp_array;

echo '<table width="100%" border="1">
      <tr>
        <td><div align="center"><strong>Nombre</strong></div></td>
        <td><div align="center"><strong>Tel&eacute;fono</strong></div></td>
        <td><div align="center"><strong>E-mail</strong></div></td>
        <td>&nbsp;</td>
      </tr>
      <tr>';
	  
	for($i = 0; $i < count($_SESSION['contactos']); $i++)
	{
		echo '<td>'.$_SESSION['contactos'][$i][nombre].'</td>';
			if ($_SESSION['contactos'][$i][extension])
				$ext = " Ext. ".$_SESSION['contactos'][$i][extension];
			else
				$ext = '';
		echo '<td>'.$_SESSION['contactos'][$i][telefono].$ext.'</td>';
		echo '<td>'.$_SESSION['contactos'][$i][email].'</td>';
		echo '<td></td>';
		echo '</tr></r>';
	}
	
echo '</tr>
    </table>';

?>