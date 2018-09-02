<?php
//Si esta variable no esta definida, entonces estan tratando de accesar por medio de URL
if (!defined('IN_EMADMIN')){
 die("Intento de Hackeo");
}
session_start();
if(!isset($_SESSION["Logged"])){
?>
<script language="Javascript">
   top.location.href="login.php";
   </script>
<?php
}
?>