<?php
//Definimos IN_EMADMIN, que nos permitir� llamar scripts desde aqui
define( 'IN_EMADMIN', true );

header( "Cache-Control: no-store, no-cache, must-revalidate" );


require_once( 'common.inc.php' );

$Contenido = new template( );
$Contenido->addtemplate( "searchtalleres" );
$Contenido->asigna_variables( array( "CLIENTEID" => $_GET['clienteid'], "TALLERID" => $_GET['tallerid'], "INSTANCE" => $_GET['instance']) );
$Contenido->compileandgo( );

?>

