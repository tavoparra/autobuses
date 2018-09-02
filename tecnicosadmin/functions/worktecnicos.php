<?php
session_start();
include_once( 'functions.inc.php' );

$ESObject = new tecnicos( '../../' );

$tecnicoid = $_POST['tecnicoid'];	

if ( $_POST['redirect'] == 0 )
{
		
    $ESObject->addtecnico( $tecnicoid, $_POST['tecnico_cod'], $_POST['nombre'], $_POST['apeido_pat'], $_POST['apeido_mat'], $_POST['status'], $_POST['puesto'], $_POST['salario'],
							$_POST['nss'], $_POST['rfc'], $_POST['curp']);
							
							
	$tecnicoid = mysql_insert_id();
	$ESObject->savelog($tecnicoid, 7, $_SESSION['idUsuario'], 1);

    echo '<script language="Javascript">';
    echo 'location.href="../tecnicosadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{

	$ESObject->edittecnico( $tecnicoid, $_POST['tecnico_cod'], $_POST['nombre'], $_POST['apeido_pat'], $_POST['apeido_mat'], $_POST['status'], $_POST['puesto'], $_POST['salario'],
							$_POST['nss'], $_POST['rfc'], $_POST['curp']);
	
		$ESObject->savelog($tecnicoid, 7, $_SESSION['idUsuario'], 2);
	    echo '<script language="Javascript">';
        echo 'location.href="../tecnicosadmin.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->droptecnico( $_POST['tecnicoid'] );
	$ESObject->savelog($_POST['tecnicoid'], 7, $_SESSION['idUsuario'], 3);
	
	echo '<script language="Javascript">';
	echo 'location.href="../tecnicosadmin.php";</script>';
}

?>