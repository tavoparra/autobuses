<?php

include_once( 'functions.inc.php' );
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
$ESObject = new articles( '../../' );

session_start();

if ( $_POST['redirect'] == 0 )
{
	
	if($_POST['moneda'] == 1)
		$dollars = true;
	else
		$dollars = false;
		
	$mano_obra = $_POST['mano_obra'] == true ? '1' : '0';

    $ESObject->addarticle($_POST['code'], $_POST['name'], $_POST['desc'], $_POST['medida_id'], $_POST['weight'], $_POST['price'], $_POST['price2'], $_POST['price3'],
	$_POST['price4'],$_POST['price5'], $_POST['costo'], $mano_obra, $dollars, $_POST['equivale']);
	$idarticle = mysql_insert_id();
	
	$ESObject->savelog($idarticle, 5, $_SESSION['idUsuario'], 1);
	
    echo '<script language="Javascript">';
    echo 'location.href="../articlesadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{
	if($_POST['moneda'] == 1)
		$dollars = true;
	else
		$dollars = false;
		
	$mano_obra = $_POST['mano_obra'] == true ? '1' : '0';

	$ESObject->editarticle( $_POST['numero'], $_POST['code'], $_POST['name'], $_POST['desc'], $_POST['medida_id'], $_POST['weight'], $_POST['price'], $_POST['price2'], $_POST['price3'], $_POST['price4'],$_POST['price5'], $_POST['costo'],
	 $mano_obra, $dollars, $_POST['equivale']);
	
	$ESObject->savelog($_POST['numero'], 5, $_SESSION['idUsuario'], 2);
	
	    echo '<script language="Javascript">';
        echo 'location.href="../articlesadmin.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->droparticle( $_POST['numero'] );
	$ESObject->savelog($_POST['numero'], 5, $_SESSION['idUsuario'], 3);
	
	echo '<script language="Javascript">';
	echo 'location.href="../articlesadmin.php";</script>';

}
elseif ( $_POST['redirect'] == 3 )
{
    //print_r($_FILES);
	
	require("reader.php");
	
	if ( $_FILES['excel_file']['name'] != "" ) {
        //$file = $ESObject->renombrarArchivo( $_FILES['fotografia']['name'] );
		$file = 'archivo.xls';
        //Nombre que se actualizará en la BD
        move_uploaded_file( $_FILES['excel_file']['tmp_name'], $file);
    }

	$datos = new Spreadsheet_Excel_Reader();
	
	$datos->read($file);
	
	$celdas = $datos->sheets[0]['cells'];
	
	
	$i = 2;
	
	while($celdas[$i][1] != '')
	{
		$tipo = $ESObject->actualizar_art($celdas[$i]);
		
		echo "Artículo ".$celdas[$i][1];
		if($tipo == 1) echo " actualizado correctamente</br>"; else echo " añadido correctamente</br>";
		
		$i++;
	};
	
	
	echo '<script language="Javascript"> alert("Actualizacion exitosa");';
	echo 'location.href="../articlesadmin.php";</script>';
}

?>