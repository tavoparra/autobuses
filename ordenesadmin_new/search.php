<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define( 'IN_EMADMIN', true );

header( "Cache-Control: no-store, no-cache, must-revalidate" );


require_once( 'common.inc.php' );

$Contenido = new template( );
$Contenido->addtemplate( "search".$_GET['search'] );
$Contenido->asigna_variables( array( "CLIENTEID" => $_GET['clienteid'], "TALLERID" => $_GET['tallerid'], "INSTANCIA" => $_GET['instancia'] ) );
$Contenido->compileandgo( );

?>

