<?php

//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class talleres
{

    //Funcion utilizada para conectar la base de datos

    function talleres( $dist )
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

    function verTalleres($taman, $pag, $filtro = "1")
    {
		$reg1 = ( $pag - 1 ) * $taman;
		$strSQL  = "SELECT * FROM ".DBPREFIX."talleres where ".$filtro." ORDER BY tallerid";
        $rs     = $this->dbc->SelectLimit( $strSQL, $taman, $reg1 );
        return $rs;
    }
	
	function getubicacion($ciudadid)
    {
		$strSQL  = "SELECT c.ciudad, e.estado FROM ".DBPREFIX."ciudades c inner join
					".DBPREFIX."estados e on c.estado = e.id_estado WHERE c.id_ciudad =".$ciudadid;
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs->fields['ciudad'].", ".$rs->fields['estado'];
    }
	
	function infotalleres($tallerid)
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."talleres where tallerid =". $tallerid;
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }

	function newid()
    {
		$stringcode = '';
        $strSQL = "Select MAX(tallerid) as maxid FROM ".DBPREFIX."talleres";
        $rs = $this->dbc->Execute( $strSQL );
				
		if($rs->fields['maxid'] > 0)
			return 1 + $rs->fields['maxid'];
		else
			return 1;
    }
	
	function newcode()
    {
		$divider = "-";
        $strSQL = "Select MAX(taller_cod) as max_cod FROM ".DBPREFIX."talleres";
        $rs = $this->dbc->Execute($strSQL);
				
		$cod_divided = explode($divider, $rs->fields['max_cod']);
		$part1 = $cod_divided[0];
		$part2 = $cod_divided[1];
		
		$newpart2 = $part1.$divider.str_pad($part2 + 1, strlen($part2), '0', STR_PAD_LEFT);
		//echo $newpart2;				
			return $newpart2;
    }
	
    function getestados($actual = 0)
    {
		$stringcode = '<option value="0" >- SELECCIONAR -</option>';
        $strSQL = "Select * FROM ".DBPREFIX."estados ORDER BY estado";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['id_estado'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['id_estado'].'" '.$selected.'>'.$rs->fields['estado'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getciudades($id_estado, $actual = 0)
    {
		$stringcode = '<option value="0" >- SELECCIONAR -</option>';
        $strSQL = "Select * FROM ".DBPREFIX."ciudades where estado = ".$id_estado." ORDER BY ciudad";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['id_ciudad'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['id_ciudad'].'" '.$selected.'>'.$rs->fields['ciudad'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getDireccion($tallerid){
		$sql = 'SELECT t.calle, t.num_ext, t.num_int, t.colonia, t.cod_postal, t.municipio, tc.ciudad, e.estado
				FROM '.DBPREFIX.'talleres t
				INNER JOIN '.DBPREFIX.'ciudades tc on t.ciudadid = tc.id_ciudad
				INNER JOIN '.DBPREFIX.'estados e on t.estadoid = e.id_estado
				WHERE t.tallerid = '.$tallerid; //die(nl2br($sql));
		$rs  = $this->dbc->Execute($sql);
		
		$DireccionFormateada = $rs->fields['calle']. " #:".$rs->fields['num_ext'];
		if($rs->fields['num_int'] != '') $DireccionFormateada .= " Interior ".$rs->fields['num_int'];
		$DireccionFormateada .= '<br/>'.
								'Colonia: '.$rs->fields['colonia'].'<br/>'.
								$rs->fields['ciudad'].', '.$rs->fields['estado'].'<br/>'.
								'C.P.: '.$rs->fields['cod_postal'];
								
		return $DireccionFormateada;
	}
	
	function print_contactos($tallerid)
    {
		$stringcode = '';
        $strSQL = "Select * FROM ".DBPREFIX."talleres_contactos where tallerid = ".$tallerid;
        $rs = $this->dbc->Execute( $strSQL );
		
		if($rs->EOF)
			return '<tr><td colspan="3"><center>No hay contactos para este taller</center></td></tr>';
		
		$stringcode = '';
			  
			while(!$rs->EOF){
				$stringcode .= '<tr><td>'.$rs->fields[nombre].'</td>';
					if ($rs->fields['extension'] != '0')
						$ext = " Ext. ".$rs->fields['extension'];
					else
						$ext = '';
				$stringcode .= '<td>'.$rs->fields[telefono].$ext.'</td>';
				$stringcode .= '<td>'.$rs->fields[email].'</td>';
				$stringcode .= '</tr>';
				
				$rs->MoveNext();
			}
				
        return $stringcode;
    }
	
	function getclientes($actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select clienteid, nombre FROM ".DBPREFIX."clientes";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['clienteid'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['clienteid'].'" '.$selected.'>'.$rs->fields['nombre'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getclientenombre($clienteid)
    {
        $strSQL = "Select nombre FROM ".DBPREFIX."clientes where clienteid = ".$clienteid;
        $rs = $this->dbc->Execute( $strSQL );
		
        return $rs->fields['nombre'];
    }
	
	function addtaller($numero, $taller_cod, $nombre, $calle, $num_ext, $num_int, $colonia, $cod_postal, $municipio, $estadoid, $ciudadid, $telefono, $clienteid)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."talleres` (`taller_cod`, `nombre`, `calle`, `num_ext`, `num_int`, `colonia`, `cod_postal`, `municipio`, `ciudadid`, `estadoid`, `telefono`, `clienteid`)
				VALUES('".$taller_cod."','".$nombre."','".$calle."','".$num_ext."','".$num_int."','".$colonia."','".$cod_postal."','".$municipio."','".$ciudadid."','".$estadoid."','".$telefono."','".$clienteid."')";
		//echo $strSQL;
        $rs      = $this->dbc->Execute( $strSQL );

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

    function edittaller( $tallerid, $taller_cod, $nombre, $calle, $num_ext, $num_int, $colonia, $cod_postal,
							$municipio, $estadoid, $ciudadid, $telefono, $clienteid )
    {
        $strSQL  = "UPDATE ".DBPREFIX."talleres set nombre ='".$nombre."'";
		$strSQL .= ", taller_cod = '".$taller_cod."'";
		$strSQL .= ", calle = '".$calle."'";
		$strSQL .= ", num_ext = '".$num_ext."'";
		$strSQL .= ", num_int = '".$num_int."'";
		$strSQL .= ", colonia = '".$colonia."'";
		$strSQL .= ", cod_postal = '".$cod_postal."'";
		$strSQL .= ", municipio = '".$municipio."'";
		$strSQL .= ", estadoid = '".$estadoid."'";
		$strSQL .= ", ciudadid = '".$ciudadid."'";
		$strSQL .= ", telefono = '".$telefono."'";
		$strSQL .= ", clienteid = '".$clienteid."'";
		$strSQL .= " where tallerid = ".$tallerid;
        $rs      = $this->dbc->Execute( $strSQL );
    }

    function addRol($nombre)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."roles` (`nombre`) VALUES('".$nombre."')";		
        $rs      = $this->dbc->Execute( $strSQL );

    }
	
	function addcontacto($idtaller, $nombre, $email, $telefono, $extension)
	{
        $strSQL  = "INSERT INTO `".DBPREFIX."talleres_contactos` (`tallerid`, `nombre`, `email`, `telefono`, `extension`)
				VALUES(".$idtaller.",'".$nombre."','".$email."','".$telefono."','".$extension."')";
				//die($strSQL);
        $rs      = $this->dbc->Execute( $strSQL );

    }
	
	function get_contactos($tallerid)
    {
		$stringcode = '';
        $strSQL = "Select * FROM ".DBPREFIX."talleres_contactos where tallerid = ".$tallerid;
        $rs = $this->dbc->Execute( $strSQL );
		
		$stringcode = '';
		$i = 0;
			  
			while(!$rs->EOF)
			{
				$temp_array['nombre'] = $rs->fields['nombre'];
				$temp_array['email'] = $rs->fields['email'];
				$temp_array['telefono'] = $rs->fields['telefono'];
				$temp_array['extension'] = $rs->fields['extension'];

				
				$_SESSION['contactos'][] = $temp_array;
				
				$stringcode .= '<td>'.$_SESSION['contactos'][$i][nombre].'</td>';
					if ($_SESSION['contactos'][$i][extension])
						$ext = " Ext. ".$_SESSION['contactos'][$i][extension];
					else
						$ext = '';
				$stringcode .= '<td>'.$_SESSION['contactos'][$i][telefono].$ext.'</td>';
				$stringcode .= '<td>'.$_SESSION['contactos'][$i][email].'</td>';
				$stringcode .= '<td><strong><a href="javascript:eliminar('.$i.')">X</a></strong></td>';
				$stringcode .= '</tr></r>';
				
				$rs->MoveNext();
				$i++;
			}
				
        return $stringcode;
    }
	
	function dropcontactos( $tallerid )
    {
			$strSQL = "DELETE FROM ".DBPREFIX."talleres_contactos where tallerid=".$tallerid;
			$rs = $this->dbc->Execute( $strSQL );
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
	
    function droptaller( $tallerid )
    {
			$strSQL = "DELETE FROM ".DBPREFIX."talleres where tallerid=".$tallerid;
			$rs = $this->dbc->Execute( $strSQL );
    }

    function allNewsdata( )
    {
        $strSQL = "Select * FROM ".DBPREFIX."noticias order by noticia desc limit 0,2";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function searchclientes( $valor, $campo )
    {
        $strSQL = "Select clienteid, cliente_cod, nombre, rfc FROM ".DBPREFIX."clientes where `".$campo."` like '%".$valor."%'";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
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
	
	function paginarfiltro( $actual, $total, $por_pagina, $nombre, $campo, $estadoid )
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
                $texto .= "<td><a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $estadoid,  1);'><img src='../imagenes/flechabegin.png' height='20' width='33' border='0'></a>&nbsp;";
                $texto .= "<a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $estadoid, $anterior);'><img src='../imagenes/flechaback.png' height='20' width='25' border='0'></a></td>";
            }
            else
            {
                $texto .= "<td><img src='../imagenes/flechanobegin.png' height='20' width='33' border='0'>&nbsp;";
                $texto .= "<img src='../imagenes/flechanoback.png' height='20' width='25' border='0'></td>";
            }
            $texto .= "<td style='width:70px;'>$actual / $total_paginas</td>";
            if ( $actual < $total_paginas )
            {
                $texto .= "<td><a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $estadoid, $posterior);'><img src='../imagenes/flechanext.png' height='20' width='25' border='0'></a>&nbsp;";
                $texto .= "<a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $estadoid, $total_paginas);'><img src='../imagenes/flechaend.png' height='20' width='33' border='0'></a></td>";
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
	
	function buildmenu($dir)
    {
        $stringcode = '<div class="dock" id="dock">   	
						<div class="dock-container">   		
							
							<a class="dock-item" href="'.$dir.'index.php">			
								<img src="'.$dir.'imagenes/images/INICIO.png" alt="Inicio" />			
								<span class="menutxt">Inicio 			
								</span>
							</a>     ';
							
		if(isset($_SESSION['permisos']['1']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'users/userAdmin.php">			
			<img src="'.$dir.'imagenes/images/USUARIOS.png" alt="Administrar Usuarios" />			
			<span class="menutxt">Usuarios 			
			</span>
		</a>';
		}
		if(isset($_SESSION['permisos']['2']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'clientesadmin/clientesadmin.php">			
			<img src="'.$dir.'imagenes/images/clientes.png" alt="Clientes" />			
			<span class="menutxt">Clientes
			</span>
		</a>   ';
		}
		if(isset($_SESSION['permisos']['3']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'talleresadmin/talleresadmin.php">			
			<img src="'.$dir.'imagenes/images/talleres.png" alt="Talleres" />			
			<span class="menutxt">Talleres
			</span>
		</a>   ';
		}
		if(isset($_SESSION['permisos']['4']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'UnitsAdmin/unitsadmin.php">			
			<img src="'.$dir.'imagenes/images/unidades_ico_menu.png" alt="Administrar Unidades" />			
			<span class="menutxt">Unidades			
			</span>
		</a>';
		}
		if(isset($_SESSION['permisos']['5']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'ArticlesAdmin/articlesadmin.php">	
			<img src="'.$dir.'imagenes/images/articulos_ico_menu02.png" alt="Administrar Articulos" />			
			<span class="menutxt">Articulos			
			</span>
		</a>';
		}
		if(isset($_SESSION['permisos']['6']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'ordenesadmin/ordenesadmin.php">			
			<img src="'.$dir.'imagenes/images/order_icon.png" alt="Ordenes de reparaci&oacute;n" />			
			<span class="menutxt">Ordenes de reparaci&oacute;n			
			</span>
		</a>';
		}
		if(isset($_SESSION['permisos']['7']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'tecnicosadmin/tecnicosadmin.php">	
			<img src="'.$dir.'imagenes/images/tecnicos.png" alt="Administrar T&eacute;cnicos" />			
			<span class="menutxt">T&eacute;cnicos			
			</span>
		</a>';
		}
		if(isset($_SESSION['permisos']['8']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'exportacion/ordenesexportadmin.php">			
			<img src="'.$dir.'imagenes/images/informes.png" alt="Reportes" />			
			<span class="menutxt">Reportes			
			</span>
		</a>';
		}
		if(isset($_SESSION['permisos']['9']))
		{
			$stringcode .= '<a class="dock-item" href="'.$dir.'configuration/configuration.php">			
			<img src="'.$dir.'imagenes/images/configuration.gif" alt="Configuraci&oacute;n" />			
			<span class="menutxt">Configuraci&oacute;n			
			</span>
		</a>';
		}
        
		$stringcode .= '		<a class="dock-item" href="'.$dir.'config/logout.php">			
								<img src="'.$dir.'imagenes/images/SALIR.png" alt="Cerrar Sesion" />			
								<span class="menutxt">Salir 			
								</span>
							</a>
								
						</div>
					</div>';
		
        return $stringcode;
    }

	function validatecodigo($codigo, $tallerid = '0', $clienteid = '0')
    {
        $strSQL = 'SELECT count(*) + 1 as cantidad
					FROM '.DBPREFIX.'talleres t
					WHERE t.taller_cod = "'.$codigo.'" and t.tallerid != '.$tallerid;
		if($clienteid > 0)
			$strSQL .= ' AND t.clienteid = '.$clienteid;

        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields['cantidad'];
    }
	
	function get_historial($seccion_id, $registro_id)
    {
        $strSQL  = "SELECT DATE_FORMAT(l.date, '%d-%m-%y %h:%i %p') as moment, l.type, u.username, CONCAT(c.nombre, ' ', c.apellido) AS nombre FROM ".DBPREFIX."logs l
			INNER JOIN ca_usuarios u ON l.user_id = u.userid
			INNER JOIN ca_contactos c ON c.idUsuario = u.userid
			WHERE l.registro_id =". $registro_id." AND l.seccion_id = ".$seccion_id;
		$rs     = $this->dbc->Execute( $strSQL);
		
		$acciones = array(1 => "Creado", 2 => "Actualizado");
		$stringcode = '';
		
		while(!$rs->EOF)
		{
			$stringcode .= $rs->fields['moment'].' - '.$acciones[$rs->fields['type']].' por '.$rs->fields['nombre'].'('.$rs->fields['username'].')';
			if(!$rs->EOF)
				$stringcode .= '<br/>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function savelog($registro_id, $seccion_id, $user_id, $type)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."logs` (`registro_id`, `seccion_id`, `user_id`, `date`, `type`, `massive`)
				VALUES('".$registro_id."','".$seccion_id."','".$user_id."',NOW(),'".$type."',0)";
        $rs      = $this->dbc->Execute( $strSQL );

    }
}
?>