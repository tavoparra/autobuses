<?php

define( 'LOCAL', 0 );
define( 'WEB', 1 );

$modo = LOCAL;

if ( $modo == LOCAL )
{
    define( 'CONTROLER', 'mysql' );
    define( 'DBNAME', 'siul_carrier' );
    define( 'DBPREFIX', 'ca_' );
    define( 'SERVERNAME', 'localhost' );
    define( 'USERNAME', 'root' );
    define( 'PASSWORD', 'RefriBus2016' );
}
else
{
    define( 'CONTROLER', 'mysql' );
    define( 'DBNAME', 'carrier' );
    define( 'DBPREFIX', 'ca_' );
    define( 'SERVERNAME', '205.178.146.81' );
    define( 'USERNAME', 'carrier' );
    define( 'PASSWORD', 'Book2kA-Res' );
}

?>
