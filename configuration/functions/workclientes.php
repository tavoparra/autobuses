<?php
session_start();
include_once( 'functions.inc.php' );

$ESObject = new clientes( '../../' );


if ( $_POST['redirect'] == 0 )
{
	
		// obtenemos los datos del archivo
		$tamano = $_FILES["logotipo"]['size'];
		$tipo = $_FILES["logotipo"]['type'];
		$archivo = $_FILES["logotipo"]['name'];
		$prefijo = substr(md5(uniqid(rand())),0,6);
	   
		if ($archivo != "") {
			$destino =  "../logotipos/".$prefijo."_".$archivo;
			if (copy($_FILES['logotipo']['tmp_name'],$destino)) 
				$logo = $prefijo."_".$archivo;
			else
			{
				echo '<scrtip>alert("Error al subir el archivo");</script>';
				$logo = "";
			}
		} else {
			$logo = "";
		}

	$clienteid = $_POST['numero'];	
		
    $ESObject->addcliente( $clienteid, $_POST['cliente_cod'], $_POST['nombre'], $_POST['calle'], $_POST['num_ext'], $_POST['num_int'], $_POST['colonia'], $_POST['cod_postal'],
							$_POST['municipio'], $_POST['estadoid'], $_POST['ciudadid'], $_POST['rfc'], $_POST['telefono'], $_POST['url'], $_POST['contrato'], $logo);
	
	for($i = 0; $i < count($_SESSION['contactos']); $i++)
	{
		$ESObject->addcontacto($clienteid, $_SESSION['contactos'][$i][nombre],$_SESSION['contactos'][$i][email], $_SESSION['contactos'][$i][telefono],
								$_SESSION['contactos'][$i][extension]);
	}
	
    echo '<script language="Javascript">';
    echo 'location.href="../clientesadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{
	$tamano = $_FILES["logotipo"]['size'];
	$tipo = $_FILES["logotipo"]['type'];
	$archivo = $_FILES["logotipo"]['name'];
	$prefijo = substr(md5(uniqid(rand())),0,6);
   
	if ($archivo != "") {
		$destino =  "../logotipos/".$prefijo."_".$archivo;
		if (copy($_FILES['logotipo']['tmp_name'],$destino)) 
			$logo = $prefijo."_".$archivo;
		else
		{
			echo '<scrtip>alert("Error al subir el archivo");</script>';
			$logo = "";
		}
	} else {
		$logo = "";
	}

	$ESObject->editcliente( $_POST['clienteid'], $_POST['cliente_cod'], $_POST['nombre'], $_POST['calle'], $_POST['num_ext'], $_POST['num_int'], $_POST['colonia'], $_POST['cod_postal'],
							$_POST['municipio'], $_POST['estadoid'], $_POST['ciudadid'], $_POST['rfc'], $_POST['telefono'], $_POST['url'], $_POST['contrato'], $logo);
							
	$ESObject->dropcontactos( $_POST['clienteid'] );
	for($i = 0; $i < count($_SESSION['contactos']); $i++)
	{
		$ESObject->addcontacto($clienteid, $_SESSION['contactos'][$i][nombre],$_SESSION['contactos'][$i][email], $_SESSION['contactos'][$i][telefono],
								$_SESSION['contactos'][$i][extension]);
	}						
							
	
	    echo '<script language="Javascript">';
        echo 'location.href="../clientesadmin.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->dropcliente( $_POST['clienteid'] );
	$ESObject->dropcontactos( $_POST['clienteid'] );

	echo '<script language="Javascript">';
	echo 'location.href="../clientesadmin.php";</script>';
}

?>