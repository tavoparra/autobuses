<?php
session_start();
if(!isset($_SESSION["Logged"]))
{
	header("location: ../login.php");
} 
else 
{

	include_once('functions.inc.php');
	$ESobject=new Usuarios('../../');

	if( $_POST['redirect'] < 2 ){
		$userid = $ESobject->addUsuario($_POST['firstname'], $_POST['lastname'],$_POST['clave'],$_POST['login'],$_POST['password'], $_POST['selected']);
		if($_POST['selected'] == 2)
		{
			$secciones = $ESobject->getsecciones();
			while(!$secciones->EOF)
			{
				$seccion_actual = $secciones->fields['seccion'];
				$array_permisos = $_POST[$seccion_actual];

				if(is_array($array_permisos))
				{
					foreach($array_permisos as $permiso_actual)
						$ESobject->addpermiso($userid, $secciones->fields['seccionid'], $permiso_actual);
				}
				$secciones->MoveNext();
			}
		}
	}
	elseif( $_POST['redirect']==3 )
	{
		$userid = $_POST['userid'];
		$ESobject->editUsuario($userid,$_POST['firstname'],$_POST['lastname'],$_POST['clave'],$_POST['login'],$_POST['password'],$_POST['selected']);
		$ESobject->dropPermisos($userid);
		if($_POST['selected'] == 2)
		{
			$secciones = $ESobject->getsecciones();
			while(!$secciones->EOF)
			{
				$seccion_actual = $secciones->fields['seccion'];
				$array_permisos = $_POST[$seccion_actual];

				if(is_array($array_permisos))
				{
					foreach($array_permisos as $permiso_actual)
						$ESobject->addpermiso($userid, $secciones->fields['seccionid'], $permiso_actual);
				}
				$secciones->MoveNext();
			}
		}
	}

	elseif( $_POST['redirect']==2 )
		$ESobject->dropUsuario($_POST['userid']);
	

	if($_POST['redirect']==0 || $_POST['redirect']==2 || $_POST['redirect']==3)
	{
	 	echo '<script language="Javascript">';
		echo 'location.href="../userAdmin.php";</script>';	
	}
	elseif($_POST['redirect']==1)
	{
		echo '<script language="Javascript">';
		echo 'location.href="../userForm.php?mode=add&type='.$_POST['type'].'&level='.$_POST['level'].'";</script>';
	}
}
?>
