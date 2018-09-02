<?php

include_once( 'functions.inc.php' );

$ESObject = new Roles( '../../' );

$agregar = 1;
$eliminar = 2;
$modificar = 3;
$tipotabla = 19;

session_start();

if ( $_POST['redirect'] == 0 )
{
	$rolnombres = $ESObject->checarRoles($_POST['nombre']);
	
	if($rolnombres->EOF)
	{    
		$ESObject->addRol( $_POST['nombre']);
		$idrol = mysql_insert_id();
		$subareas = $ESObject->todassubareas();
		
		if(!$subareas->EOF)
		{
			do
			{
				$subareaid = $subareas->fields('idsubarea');
				if($_POST[$subareaid] == 'on')
				{
					$ESObject->crearpermiso($idrol,$subareaid);
				}
				$subareas->MoveNext();
			}while(!$subareas->EOF);
		}
		
			echo '<script language="Javascript">';
			echo 'location.href="../rolesAdmin.php";</script>';
	}
	else
	{
		echo '<script language="Javascript">alert("Ya existe un rol con ese nombre"); ';
		echo 'location.href="javascript:history.back();";</script>';
	}
}
elseif ( $_POST['redirect'] == 1 )
{
    
    //Aqui se llama a la función para editar la noticia
	
	$ESObject->editRol( $_POST['rolid'], $_POST['nombre']);
	$ESObject->borrarpermisos($_POST['rolid']);
	
	$subareas = $ESObject->todassubareas();
	
	if(!$subareas->EOF)
	{
		do
		{
			$subareaid = $subareas->fields('idsubarea');
			if($_POST[$subareaid] == 'on')
			{
				$ESObject->crearpermiso($_POST['rolid'],$subareaid);
			}
			$subareas->MoveNext();
		}while(!$subareas->EOF);
	}
	
	    echo '<script language="Javascript">';
        echo 'location.href="../rolesAdmin.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
    $ESObject->dropRol( $_POST['rolid'] );

	echo '<script language="Javascript">';
	echo 'location.href="../rolesAdmin.php";</script>';
}

?>