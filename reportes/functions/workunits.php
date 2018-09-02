<?php

include_once( 'functions.inc.php' );

$ESObject = new units( '../../' );

session_start();

if ( $_POST['redirect'] == 0 )
{
    $ESObject->addunit($_POST['unidadID'],$_POST['clienteID'], $_POST['tallerID'], $_POST['numEconomico'], $_POST['tipo'], $_POST['marca'], $_POST['modelo'], $_POST['placas'], $_POST['numSerie'], $_POST['marcaCaja'],$_POST['modeloCaja'],$_POST['serieCaja'],$_POST['marcaCondensador'],$_POST['modeloCondensador'],$_POST['serieCondensador'],$_POST['marcaEvaporador'],$_POST['modeloEvaporador'],$_POST['serieEvaporador'],$_POST['marcaCompresor'],$_POST['modeloCompresor'],$_POST['serieCompresor']);
	
    echo '<script language="Javascript">';
    echo 'location.href="../unitsadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{
	$ESObject->editunit($_POST['unidadID'],$_POST['clienteID'], $_POST['tallerID'], $_POST['numEconomico'], $_POST['tipo'], $_POST['marca'], $_POST['modelo'], $_POST['placas'], $_POST['numSerie'], $_POST['marcaCaja'],$_POST['modeloCaja'],$_POST['serieCaja'],$_POST['marcaCondensador'],$_POST['modeloCondensador'],$_POST['serieCondensador'],$_POST['marcaEvaporador'],$_POST['modeloEvaporador'],$_POST['serieEvaporador'],$_POST['marcaCompresor'],$_POST['modeloCompresor'],$_POST['serieCompresor']);

	    echo '<script language="Javascript">';
        echo 'location.href="../unitsadmin.php";</script>';    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->dropunit( $_POST['unidadID'] );

	echo '<script language="Javascript">';
	echo 'location.href="../unitsadmin.php";</script>';
}

?>