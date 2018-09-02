<?php

//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class Roles
{

    //Funcion utilizada para conectar la base de datos

    function Roles( $dist )
    {
        include( $dist.'config/adodb/adodb.inc.php' );
        require( $dist.'config/dbconfig.php' );
        $db = &ADONewConnection( CONTROLER );

        # eg 'mysql' o 'postgres'
        //$db->debug = true;

        if ( CONTROLER == 'mysql' )
        {
            $db->Connect( SERVERNAME, USERNAME, PASSWORD, DBNAME );
			$db->EXECUTE("set names 'utf8'");  
        }
        elseif ( CONTROLER == 'access' )
        {
            $db->PConnect( DBNAME );
        }
        $db->SetFetchMode( ADODB_FETCH_ASSOC );
        $this->dbc = $db;
    }

    function selectqry( $str, $campos = '*', $where = '1', $mode = 0, $tamano = 0, $pag = 0 )
    {
        $str1   = str_replace( " ", "", $str );
        $str2   = DBPREFIX.$str1;
        $tabla  = str_replace( ",", ",".DBPREFIX, $str2 );
        $strSQL = "Select ".$campos." from ".$tabla." where ".$where;
        if ( $tamano != 0 && $pag != 0 )
        {
            $reg1 = ( $pag - 1 ) * $tamano;
            $rs = $this->dbc->SelectLimit( $strSQL, $tamano, $reg1 );
        }
        else
        {
            $rs = $this->dbc->Execute( $strSQL );
        }
        if ( $mode == 0 )
        {
            return $rs;
        }
        else
        {
            $fld = $rs->Fetchfield( 0 );
            return $rs->fields[$fld->name];
        }
    }

    // Aqui estan las funciones de las noticias
    /*************************************/
    /*************************************/
    /*************************************/

    function verRoles()
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."roles order by nombre ASC";
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }
	
	function checarRoles($rolname)
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."roles where nombre='".$rolname."'";
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }
	
	function inforoles($idrol)
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."roles left join ".DBPREFIX."permisos on ".DBPREFIX."roles.idrol = ".DBPREFIX."permisos.idrol where ".DBPREFIX."roles.idrol =". $idrol;
		$rs     = $this->dbc->Execute( $strSQL);
		
        return $rs;
    }

    function traerAreas()
    {
        $strSQL = "Select * FROM ".DBPREFIX."areas order by nombre ASC";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function todassubareas()
    {
        $strSQL = "Select * FROM ".DBPREFIX."subareas order by nombre ASC";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function traersubareas($idarea, $idrol=0)
    {
        $scode = "Seleccione las subáreas a las que tendrá acceso este rol:<br/>";
		
		$strSQL = "Select * FROM ".DBPREFIX."subareas where idarea =". $idarea." order by nombre ASC";
        $rs = $this->dbc->Execute( $strSQL );
		
		if(!$rs->EOF)
		{
			do{
				$nombre = $rs->fields('nombre');
				$subareaid = $rs->fields('idsubarea');
				
				$strSQL="Select * from ".DBPREFIX."permisos where idrol =".$idrol." and idsubarea =".$subareaid;
				$resultado = $this->dbc->Execute($strSQL);
				if($resultado->EOF)
				{
					$checked = '';
				}
				else
				{
					$checked = 'checked = "checked"';
				}
				
				$scode .= '<input type="checkbox" name="'.$subareaid.'" id="'.$subareaid.'" '.$checked.' />'.$nombre.'<br/>';
				$rs->MoveNext();
			}while(!$rs->EOF);
		}
		else
		{
			$scode = "No se encontraron subáreas para esta área";
		}
        
		return $scode;
    }

    function renombrarArchivo( $nombre )
    {
        $largo    = strlen( $nombre );
        $posPunto = strrpos( $nombre, "." );
        $ext      = substr( $nombre, $posPunto, $largo );
        $nombre   = substr( $nombre, 0, $posPunto );
        $completo = time( ).$nombre.$ext;
        return $completo;
    }

    function editRol( $rolid, $nombre )
    {
        $strSQL  = "UPDATE `".DBPREFIX."roles` set `nombre`='".$nombre."' where idrol = ".$rolid;
        $rs      = $this->dbc->Execute( $strSQL );
    }

    function addRol($nombre)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."roles` (`nombre`) VALUES('".$nombre."')";		
        $rs      = $this->dbc->Execute( $strSQL );

    }
	
	function crearpermiso($idrol, $idsubarea)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."permisos` (`idrol`, `idsubarea`) VALUES(".$idrol.", ".$idsubarea.")";		
        $rs      = $this->dbc->Execute( $strSQL );
    }

	function borrarpermisos( $rolid )
    {
        $strSQL = "DELETE FROM ".DBPREFIX."permisos where idrol=".$rolid." ";
        $rs = $this->dbc->Execute( $strSQL );
    }
	
    function dropRol( $rolid )
    {
        $strSQL = "Select * FROM ".DBPREFIX."usuarios where rolid =". $rolid;
		$rs = $this->dbc->Execute( $strSQL );
		if($rs->EOF)
		{
			$strSQL = "DELETE FROM ".DBPREFIX."roles where idrol=".$rolid." ";
			$rs = $this->dbc->Execute( $strSQL );
		}
		else
		{
			echo "<script>alert('No es posible eliminar el Rol porque está asignado a uno o más usuarios');</script>";
		}
    }



    function cuentareg( $table, $where = 1 )
    {
        $strSQL = "Select COUNT(*) as numreg from ".DBPREFIX.$table." where ".$where;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields["numreg"];
    }

    function paginar( $actual, $total, $por_pagina, $enlace )
    {
        $texto         = "";
        $total_paginas = ceil( $total / $por_pagina );
        $anterior      = $actual - 1;
        $posterior     = $actual + 1;
        if ( $total_paginas > 1 )
        {
            $texto .= '<table cellpadding="0" cellspacing="0" align="center"><tr>';
            if ( $actual > 1 )
            {
                $texto .= "<td><a href=\"$enlace"."1"."\"><img src='../imagenes/flechabegin.png' height='20' width='33' border='0'></a>&nbsp;";
                $texto .= "<a href=\"$enlace$anterior\"><img src='../imagenes/flechaback.png' height='20' width='25' border='0'></a></td>";
            }
            else
            {
                $texto .= "<td><img src='../imagenes/flechanobegin.png' height='20' width='33' border='0'>&nbsp;";
                $texto .= "<img src='../imagenes/flechanoback.png' height='20' width='25' border='0'></td>";
            }
            $texto .= "<td style='width:70px;'>$actual / $total_paginas</td>";
            if ( $actual < $total_paginas )
            {
                $texto .= "<td><a href=\"$enlace$posterior\"><img src='../imagenes/flechanext.png' height='20' width='25' border='0'></a>&nbsp;";
                $texto .= "<a href=\"$enlace$total_paginas\"><img src='../imagenes/flechaend.png' height='20' width='33' border='0'></a></td>";
            }
            else
            {
                $texto .= "<td><img src='../imagenes/flechanonext.png' height='20' width='25' border='0'>&nbsp;";
                $texto .= "<img src='../imagenes/flechanoend.png' height='20' width='33' border='0'></td>";
            }
            $texto .= "</td></tr></table>";
        }
        elseif ( $total_paginas == 1 )
        {
            $texto = "1";
        }
        else
        {
            $texto = "";
        }
        
        return $texto;
        
    }
    
	//Saca y devuelve $n palabras del texto $text.
	
	function countWords( $text, $n )
	{
	    $auxText = explode( ' ', $text );
	    $result  = "";
	    $elipsis = " ...";
	    if ( $n > count( $auxText ) )
	    {
	        $n = count( $auxText );
	        $elipsis = "";
	    }
	    for ( $i = 0; $i < $n; $i++ )
	    {
	        $result .= $auxText[$i].' ';
	    }
	    return trim( $result ).$elipsis;
	}  
	
	function addLog( $idtipolog, $idtabla, $idusuario )
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."logs` ( `idtipolog` , `idtabla` , `idusuario`, `fecha` ) ";
        $strSQL .= "VALUES ( ".$idtipolog.", ".$idtabla.", ".$idusuario.", CURDATE() )";
        
        $rs = $this->dbc->Execute( $strSQL );
    }		
	
}
?>