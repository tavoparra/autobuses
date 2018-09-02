<?php

include_once( 'functions.inc.php' );

$ESObject = new talleres( '../../' );

session_start();

if ( $_POST['redirect'] == 0 )
{
	
	if($_POST['local'] == true)
		$cliente_id = "-1";
	else
		$cliente_id = $_POST['clienteid'];
		

    $ESObject->addtaller( $_POST['numero'], $_POST['taller_cod'], $_POST['nombre'], $_POST['calle'], $_POST['num_ext'], $_POST['num_int'], $_POST['colonia'], $_POST['cod_postal'],
							$_POST['municipio'], $_POST['estadoid'], $_POST['ciudadid'], $_POST['telefono'], $cliente_id);
	
	$tallerid = mysql_insert_id();
	
	for($i = 0; $i < count($_SESSION['contactos']); $i++)
	{
		$ESObject->addcontacto($tallerid, $_SESSION['contactos'][$i][nombre],$_SESSION['contactos'][$i][email], $_SESSION['contactos'][$i][telefono],
								$_SESSION['contactos'][$i][extension]);
	}
	
	$ESObject->savelog($tallerid, 3, $_SESSION['idUsuario'], 1);
	
    echo '<script language="Javascript">';
    echo 'location.href="../talleresadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{

	if($_POST['local'] == true)
		$cliente_id = "-1";
	else
		$cliente_id = $_POST['clienteid'];

	$ESObject->edittaller( $_POST['tallerid'], $_POST['taller_cod'], $_POST['nombre'], $_POST['calle'], $_POST['num_ext'], $_POST['num_int'], $_POST['colonia'], $_POST['cod_postal'],
							$_POST['municipio'], $_POST['estadoid'], $_POST['ciudadid'], $_POST['telefono'], $cliente_id );
	
	$ESObject->dropcontactos( $_POST['tallerid'] );
	for($i = 0; $i < count($_SESSION['contactos']); $i++)
	{
		$ESObject->addcontacto($_POST['tallerid'], $_SESSION['contactos'][$i][nombre],$_SESSION['contactos'][$i][email], $_SESSION['contactos'][$i][telefono],
								$_SESSION['contactos'][$i][extension]);
	}
	
	$ESObject->savelog($_POST['tallerid'], 3, $_SESSION['idUsuario'], 2);
	
	    echo '<script language="Javascript">';
        echo 'location.href="../talleresadmin.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->droptaller( $_POST['tallerid'] );

	$ESObject->savelog($_POST['tallerid'], 3, $_SESSION['idUsuario'], 3);
	
	echo '<script language="Javascript">';
	echo 'location.href="../talleresadmin.php";</script>';
}

?>