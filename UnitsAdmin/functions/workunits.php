<?php

include_once( 'functions.inc.php' );

$ESObject = new units( '../../' );

session_start();

if ( $_POST['redirect'] == 0 )
{
    $ESObject->addunit($_POST['clienteid'], $_POST['tallerid'], $_POST['numEconomico'], $_POST['tipo'], $_POST['marca'],$_POST['modelo'],
				$_POST['placas'], $_POST['numSerie'], $_POST['garantiaID'], $_POST['marcaEquipo'],$_POST['modeloEquipo'],
				$_POST['serieEquipo'], $_POST['marcaCaja'],$_POST['modeloCaja'],$_POST['serieCaja'],$_POST['marcaCondensador'],
				$_POST['modeloCondensador'],$_POST['serieCondensador'],$_POST['marcaEvaporador'],$_POST['modeloEvaporador'],
				$_POST['serieEvaporador'],$_POST['marcaCompresor'],$_POST['modeloCompresor'],$_POST['serieCompresor'],
				$_POST['marcaMotor'],$_POST['modeloMotor'],$_POST['serieMotor'],$_POST['marcaMicro'],$_POST['modeloMicro'],
				$_POST['serieMicro']);
	
	$unidadID = mysql_insert_id();
	
	$ESObject->savelog($unidadID, 4, $_SESSION['idUsuario'], 1);
	
    echo '<script language="Javascript">';
    echo 'location.href="../unitsadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{
	$ESObject->editunit($_POST['unidadID'],$_POST['clienteid'], $_POST['tallerid'], $_POST['numEconomico'], $_POST['tipo'],
		$_POST['marca'], $_POST['modelo'], $_POST['placas'], $_POST['numSerie'], $_POST['garantiaID'], $_POST['marcaEquipo'],$_POST['modeloEquipo'],
		$_POST['serieEquipo'],$_POST['marcaCaja'],$_POST['modeloCaja'],$_POST['serieCaja'],$_POST['marcaCondensador'],
		$_POST['modeloCondensador'],$_POST['serieCondensador'],$_POST['marcaEvaporador'],$_POST['modeloEvaporador'],
		$_POST['serieEvaporador'],$_POST['marcaCompresor'],$_POST['modeloCompresor'],$_POST['serieCompresor'],$_POST['marcaMotor'],
		$_POST['modeloMotor'],$_POST['serieMotor'],$_POST['marcaMicro'],$_POST['modeloMicro'],$_POST['serieMicro']);

		$ESObject->savelog($_POST['unidadID'], 4, $_SESSION['idUsuario'], 2);
		
	    echo '<script language="Javascript">';
        echo 'location.href="../unitsadmin.php";</script>';    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->dropunit( $_POST['unidadID'] );

	$ESObject->savelog($_POST['unidadID'], 4, $_SESSION['idUsuario'], 3);
	
	echo '<script language="Javascript">';
	echo 'location.href="../unitsadmin.php";</script>';
}

?>