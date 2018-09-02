<?php

//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class articles
{

    //Funcion utilizada para conectar la base de datos

    function articles( $dist )
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

    function verArticulos($taman, $pag, $filtro = "1")
    {
		$reg1 = ( $pag - 1 ) * $taman;
		$strSQL  = "SELECT * FROM ".DBPREFIX."articles where ".$filtro;
		$rs     = $this->dbc->SelectLimit( $strSQL, $taman, $reg1 );
		//$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }
	
/*	function getubicacion($ciudadid)
    {
		$strSQL  = "SELECT c.ciudad, e.estado FROM ".DBPREFIX."ciudades c inner join
					".DBPREFIX."estados e on c.estado = e.id_estado WHERE c.id_ciudad =".$ciudadid;
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs->fields['ciudad'].", ".$rs->fields['estado'];
    }
*/	
	function infoArticles($articleid)
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."articles WHERE id =". $articleid;
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }

    function CverNoticias( )
    {
        $strSQL = "select COUNT(*) from ".DBPREFIX."noticias";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

	function newid()
    {
		$stringcode = '';
        $strSQL = "Select MAX(id) as maxid FROM ".DBPREFIX."articles";
        $rs = $this->dbc->Execute( $strSQL );
				
		if($rs->fields['maxid'] > 0)
			return 1 + $rs->fields['maxid'];
		else
			return 1;
    }
	
    function getestados($actual = 0)
    {
		$stringcode = '';
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
		$stringcode = '';
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
	
	function getmedidas($actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select * FROM ".DBPREFIX."medidas";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['medida_id'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['medida_id'].'" '.$selected.'>'.$rs->fields['medida'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getmedidaname($medidaid = 0){
        $strSQL = "Select medida FROM ".DBPREFIX."medidas WHERE medida_id = ".$medidaid;
        $rs = $this->dbc->Execute( $strSQL );
		
        return $rs->fields['medida'];
    }
	
	function addarticle($code, $name, $desc, $medida_id, $weight, $price, $price2, $price3, $price4, $price5, $costo, $mano_obra, $dollars, $equivale)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."articles` (`code`, `name`, `desc`, `medida_id`, `weight`, `price`, `price2`, `price3`, `price4`, `price5`, `costo`, `dollars`, `equivale`)
	    VALUES('".$code."','".$name."','".$desc."','".$medida_id."','".$weight."','".$price."','".$price2."','".$price3."','".$price4."','".$price5."','".$costo."','".$dollars."','".$equivale."')";
		//echo $strSQL;
		//die();
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

    function editarticle( $articleid, $code, $name, $desc, $medida_id, $weight, $price, $price2, $price3, $price4, $price5, $costo, $mano_obra, $dollars, $equivale)
    {
        $strSQL  = "UPDATE ".DBPREFIX."articles set code ='".$code."'";
		$strSQL .= ", name = '".$name."'";
		$strSQL .= ", `desc` = '".$desc."'";
		$strSQL .= ", medida_id = '".$medida_id."'";
		$strSQL .= ", weight = '".$weight."'";
		$strSQL .= ", price = '".$price."'";
		$strSQL .= ", price2 = '".$price2."'";
		$strSQL .= ", price3 = '".$price3."'";
		$strSQL .= ", price4 = '".$price4."'";
		$strSQL .= ", price5 = '".$price5."'";
		$strSQL .= ", costo = '".$costo."'";
		$strSQL .= ", price4 = '".$price4."'";
		$strSQL .= ", price5 = '".$price5."'";
		$strSQL .= ", costo = '".$costo."'";
		$strSQL .= ", mano_obra = '".$mano_obra."'";
		//$strSQL .= ", dollars = '".$dollars."'";
		$strSQL .= ", equivale = '".$equivale."'";
		$strSQL .= " WHERE id = ".$articleid;

        $rs      = $this->dbc->Execute( $strSQL );
    }
	
	
	function actualizar_art($article_info)
    {
		$code = $article_info[1];
		$name = $article_info[2];
		$description = $article_info[3];
		
		$strSQL = "Select medida_id FROM ".DBPREFIX."medidas where medida = '".$article_info[4]."'";
        $medidaResult = $this->dbc->Execute( $strSQL );
		if(!$medidaResult->EOF) $medida_id = $medidaResult->fields['medida_id']; else $medida_id = 0;
		
		$peso = $article_info[5];
		$precio1 = $article_info[6];
		$precio2 = $article_info[7];
		$precio3 = $article_info[8];
		$precio4 = $article_info[9];
		$precio5 = $article_info[10];
		$costo = $article_info[11];
		$equivale = $article_info[13];
		
		if($article_info[12] == 'DLS' || $article_info[12] == 'DOLARES')
			$dollars = 1;
		else
			$dollars = 0;
		
		$strSQL = "Select id FROM ".DBPREFIX."articles where code = '".$code."'";
        $rs = $this->dbc->Execute( $strSQL );
		
		if(!$rs->EOF)
		{
			$articleid = $rs->fields['id'];
		
        $strSQL  = "UPDATE ".DBPREFIX."articles set code ='".$code."'";
		$strSQL .= ", name = '".$name."'";
		$strSQL .= ", `desc` = '".$description."'";
		$strSQL .= ", medida_id = '".$medida_id."'";
		$strSQL .= ", weight = '".$peso."'";
		$strSQL .= ", price = '".$precio1."'";
		$strSQL .= ", price2 = '".$precio2."'";
		$strSQL .= ", price3 = '".$precio3."'";
		$strSQL .= ", price4 = '".$precio4."'";
		$strSQL .= ", price5 = '".$precio5."'";
		$strSQL .= ", costo = '".$costo."'";
		$strSQL .= ", dollars = '".$dollars."'";
		$strSQL .= ", equivale = '".$equivale."'";
		$strSQL .= " WHERE id = ".$articleid;
		
		$this->savelog($articleid, 5, $_SESSION['idUsuario'], 2, 1);
		
		$tipo = 1;
		}
		else
		{
			
        $strSQL  = "INSERT INTO `".DBPREFIX."articles`
					(`code`, `name`, `desc`, `medida_id`, `weight`, `price`,
					`price2`, `price3`, `price4`, `price5`, `costo`, `dollars`, `equivale`)
					VALUES('".$code."','".$name."','".$description."','".$medida_id."','".$peso."','".$precio1."',
					'".$precio2."','".$precio3."','".$precio4."','".$precio5."','".$costo."','".$dollars."',
					'".$equivale."')"; 
		$this->dbc->Execute( $strSQL );
		$articleid = MySql_Insert_Id();
		$this->savelog($articleid, 5, $_SESSION['idUsuario'], 1, 1);
		
		$tipo = 2;
		}
		//echo $strSQL;
		//die();
        $rs      = $this->dbc->Execute( $strSQL );
		//die();
		return $tipo;
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
	
    function droparticle( $articleid )
    {
			$strSQL = "DELETE FROM ".DBPREFIX."articles WHERE id=".$articleid;
			//echo $strSQL;
			//die();
			$rs = $this->dbc->Execute( $strSQL );
    }

    function allNewsdata( )
    {
        $strSQL = "Select * FROM ".DBPREFIX."noticias order by noticia desc limit 0,2";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function getNews( )
    {
        $strSQL = "Select * FROM ".DBPREFIX."noticias order by noticia desc limit 0,3";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    /***************************************/
    /***************************************/
    //Aqui estan las funciones de las noticias

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
	
	function paginarfiltro( $actual, $total, $por_pagina, $nombre, $campo )
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
                $texto .= "<td><a href='javascript:submitbusqueda(\"$nombre\", \"$campo\",  1);'><img src='../imagenes/flechabegin.png' height='20' width='33' border='0'></a>&nbsp;";
                $texto .= "<a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $anterior);'><img src='../imagenes/flechaback.png' height='20' width='25' border='0'></a></td>";
            }
            else
            {
                $texto .= "<td><img src='../imagenes/flechanobegin.png' height='20' width='33' border='0'>&nbsp;";
                $texto .= "<img src='../imagenes/flechanoback.png' height='20' width='25' border='0'></td>";
            }
            $texto .= "<td style='width:70px;'>$actual / $total_paginas</td>";
            if ( $actual < $total_paginas )
            {
                $texto .= "<td><a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $posterior);'><img src='../imagenes/flechanext.png' height='20' width='25' border='0'></a>&nbsp;";
                $texto .= "<a href='javascript:submitbusqueda(\"$nombre\", \"$campo\", $total_paginas);'><img src='../imagenes/flechaend.png' height='20' width='33' border='0'></a></td>";
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
	
	function validatecodigo($codigo, $id = '0')
    {
        $strSQL = 'SELECT count(*) + 1 as cantidad
					FROM '.DBPREFIX.'articles a
					WHERE a.code = "'.$codigo.'" and a.id != '.$id;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields['cantidad'];
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
	
		function get_historial($seccion_id, $registro_id)
    {
        $strSQL  = "SELECT DATE_FORMAT(l.date, '%d-%m-%y %h:%i %p') as moment, l.type, u.username, CONCAT(c.nombre, ' ', c.apellido) AS nombre, l.massive FROM ".DBPREFIX."logs l
			INNER JOIN ca_usuarios u ON l.user_id = u.userid
			INNER JOIN ca_contactos c ON c.idUsuario = u.userid
			WHERE l.registro_id =". $registro_id." AND l.seccion_id = ".$seccion_id;
		$rs     = $this->dbc->Execute( $strSQL);
		
		$acciones = array(1 => "Creado", 2 => "Actualizado");
		$stringcode = '';
		
		while(!$rs->EOF)
		{
			if($rs->fields['massive'] == 1)
				$mtext = "archivo por ";
			else
				$mtext = "";
		
			$stringcode .= $rs->fields['moment'].' - '.$acciones[$rs->fields['type']].' por '.$mtext.$rs->fields['nombre'].'('.$rs->fields['username'].')';
			if(!$rs->EOF)
				$stringcode .= '<br/>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function savelog($registro_id, $seccion_id, $user_id, $type, $massive = '0')
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."logs` (`registro_id`, `seccion_id`, `user_id`, `date`, `type`, `massive`)
				VALUES('".$registro_id."','".$seccion_id."','".$user_id."',NOW(),'".$type."',".$massive.")";
        $rs      = $this->dbc->Execute( $strSQL );

    }
}
?>