<?php
session_start();
include_once( 'functions.inc.php' );

$ESObject = new configuration( '../../' );


if ( $_POST['redirect'] == 0 )
{
		
    $ESObject->addmedida($_POST['nombre']);
	
	
    echo '<script language="Javascript">';
    echo 'location.href="../configuration.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{

	$ESObject->editcliente( $_POST['clienteid'], $_POST['cliente_cod'], $_POST['nombre'], $_POST['calle'], $_POST['num_ext'], $_POST['num_int'], $_POST['colonia'], $_POST['cod_postal'],
							$_POST['municipio'], $_POST['estadoid'], $_POST['ciudadid'], $_POST['rfc'], $_POST['telefono'], $_POST['url'], $_POST['contrato'], $logo);
							
				
							
	
	    echo '<script language="Javascript">';
        echo 'location.href="../configuration.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->dropcliente( $_POST['clienteid'] );
	$ESObject->dropcontactos( $_POST['clienteid'] );

	echo '<script language="Javascript">';
	echo 'location.href="../clientesadmin.php";</script>';
}

?>