<?php
include_once("../config/dbconfig.php");
date_default_timezone_set("America/Mexico_City");

$backup_file = "RESPALDO_AUTOBUSES_".date("Y_m_d_H_i_s").'.sql';
 
$database = DBNAME;
$user = USERNAME;
$pass = PASSWORD;
$host = SERVERNAME;
$dir = dirname(__FILE__).'/backups/'.$backup_file;

exec("rm ".dirname(__FILE__)."/backups/*");
exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} --result-file={$dir} 2>&1", $output);