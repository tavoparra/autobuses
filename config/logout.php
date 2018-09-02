<?php
session_start();
if(!$_SESSION["Logged"]){
header("location: ../login.php");
} else {
include("../functions/functions.inc.php");
$ESObject=new DBFunciones('../');
$ESObject->dropshoplogout();
session_unset();
session_destroy(); 
?>
<script language="Javascript">

location.href="../login.php";

</script>
<?php
}
?>