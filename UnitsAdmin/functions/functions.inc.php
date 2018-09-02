<?php

//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class units
{

    //Funcion utilizada para conectar la base de datos

    function units( $dist )
    {
        include( $dist.'config/adodb/adodb.inc.php' );
        require( $dist.'config/dbconfig.php' );
        $db = &ADONewConnection( CONTROLER );

        //# eg 'mysql' o 'postgres'
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

    // Funciones para Unidades

    function seeUnits($taman, $pag, $filtro = "1")
    {
		$reg1 = ( $pag - 1 ) * $taman;
		$strSQL  = "SELECT * FROM ".DBPREFIX."units where ".$filtro." ORDER BY unidadID";
		$rs     = $this->dbc->SelectLimit( $strSQL, $taman, $reg1 );
        return $rs;
    }
	
	function infoUnits($unidadID)
    {
		$strSQL  = "SELECT IF(t.clienteid = -1, u.clienteID, t.clienteid) as idCliente, u.* FROM ".DBPREFIX."units u
					left join ".DBPREFIX."talleres t on u.tallerID = t.tallerid
					WHERE u.unidadID =". $unidadID;
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }

	function newID()
    {
        $strSQL = "Select MAX(unidadID) as maxid FROM ".DBPREFIX."units";
        $rs = $this->dbc->Execute( $strSQL );
				
		if($rs->fields['maxid'] > 0)
			return 1 + $rs->fields['maxid'];
		else
			return 1;
    }
	
	function addUnit($clienteID,$tallerID,$numEconomico,$tipo,$marca,$modelo,$placas,$numSerie,$garantiaID,$marcaEquipo,$modeloEquipo,$serieEquipo,$marcaCaja,$modeloCaja,$serieCaja,$marcaCondensador,$modeloCondensador,$serieCondensador,$marcaEvaporador,$modeloEvaporador,$serieEvaporador,$marcaCompresor,$modeloCompresor,$serieCompresor,$marcaMotor,$modeloMotor,$serieMotor,$marcaMicro,$modeloMicro,$serieMicro)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."units` (`clienteID`, `tallerID`, `numEconomico`, `tipo`, `marca`, `modelo`,`placas`,
				`numSerie`,`garantiaID`,`marcaEquipo`,`modeloEquipo`,`serieEquipo`,`marcaCaja`,`modeloCaja`,`serieCaja`,`marcaCondensador`,
				`modeloCondensador`,`serieCondensador`,`marcaEvaporador`,`modeloEvaporador`,`serieEvaporador`,`marcaCompresor`,
				`modeloCompresor`,`serieCompresor`,`marcaMicro`,`modeloMicro`,`serieMicro`)
				VALUES('".$clienteID."','".$tallerID."','".$numEconomico."','".$tipo."','".$marca."','".$modelo."','".$placas."',
				'".$numSerie."','".$garantiaID."','".$marcaEquipo."','".$modeloEquipo."','".$serieEquipo."','".$marcaCaja."','".$modeloCaja."',
				'".$serieCaja."','".$marcaCondensador."','".$modeloCondensador."','".$serieCondensador."','".$marcaEvaporador."',
				'".$modeloEvaporador."','".$serieEvaporador."','".$marcaCompresor."','".$modeloCompresor."','".$serieCompresor."',
				'".$marcaMicro."','".$modeloMicro."','".$serieMicro."');";
		//echo $strSQL;
		//die();
        $rs      = $this->dbc->Execute( $strSQL );
    }

    function editUnit($unidadID,$clienteID,$tallerID,$numEconomico,$tipo,$marca,$modelo,$placas,$numSerie,$garantiaID,$marcaEquipo,$modeloEquipo,$serieEquipo,$marcaCaja,$modeloCaja,$serieCaja,$marcaCondensador,$modeloCondensador,$serieCondensador,$marcaEvaporador,$modeloEvaporador,$serieEvaporador,$marcaCompresor,$modeloCompresor,$serieCompresor,$marcaMotor,$modeloMotor,$serieMotor,$marcaMicro,$modeloMicro,$serieMicro)
    {
        $strSQL  = "UPDATE ".DBPREFIX."units SET tallerID = '".$tallerID."'";
		$strSQL .= ", clienteID = '".$clienteID."'";
		$strSQL .= ", numEconomico = '".$numEconomico."'";
		$strSQL .= ", tipo = '".$tipo."'";
		$strSQL .= ", marca = '".$marca."'";
		$strSQL .= ", modelo = '".$modelo."'";
		$strSQL .= ", placas = '".$placas."'";
		$strSQL .= ", numSerie = '".$numSerie."'";
		$strSQL .= ", garantiaID = '".$garantiaID."'";
		$strSQL .= ", marcaEquipo = '".$marcaEquipo."'";
		$strSQL .= ", modeloEquipo = '".$modeloEquipo."'";
		$strSQL .= ", serieEquipo = '".$serieEquipo."'";
		$strSQL .= ", marcaCaja = '".$marcaCaja."'";
		$strSQL .= ", modeloCaja = '".$modeloCaja."'";
		$strSQL .= ", serieCaja = '".$serieCaja."'";
		$strSQL .= ", marcaCondensador = '".$marcaCondensador."'";
		$strSQL .= ", modeloCondensador = '".$modeloCondensador."'";
		$strSQL .= ", serieCondensador = '".$serieCondensador."'";
		$strSQL .= ", marcaEvaporador = '".$marcaEvaporador."'";
		$strSQL .= ", modeloEvaporador = '".$modeloEvaporador."'";
		$strSQL .= ", serieEvaporador = '".$serieEvaporador."'";
		$strSQL .= ", marcaCompresor = '".$marcaCompresor."'";
		$strSQL .= ", modeloCompresor = '".$modeloCompresor."'";
		$strSQL .= ", serieCompresor = '".$serieCompresor."'";
		$strSQL .= ", marcaMotor = '".$marcaMotor."'";
		$strSQL .= ", modeloMotor = '".$modeloMotor."'";
		$strSQL .= ", serieMotor = '".$serieMotor."'";
		$strSQL .= ", marcaMicro = '".$marcaMicro."'";
		$strSQL .= ", modeloMicro = '".$modeloMicro."'";
		$strSQL .= ", serieMicro = '".$serieMicro."'";
		$strSQL .= " WHERE unidadID = ".$unidadID;
		//echo $strSQL;
		
        $rs      = $this->dbc->Execute( $strSQL );
		//die();
    }
		
    function dropUnit( $unidadID )
    {
			$strSQL = "DELETE FROM ".DBPREFIX."units WHERE unidadID=".$unidadID;
			//echo $strSQL;
			//die();
			$rs = $this->dbc->Execute( $strSQL );
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
	function gettallernombre($tallerid)
    {
        $strSQL = "Select nombre FROM ".DBPREFIX."talleres where tallerid = ".$tallerid;
        $rs = $this->dbc->Execute( $strSQL );
		
        return $rs->fields['nombre'];
    }
	function gettalleres($clienteid, $actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select tallerid, nombre FROM ".DBPREFIX."talleres where clienteid = ".$clienteid;
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['tallerid'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['tallerid'].'" '.$selected.'>'.$rs->fields['nombre'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	function searchclientes( $valor, $campo )
    {
        $strSQL = "Select clienteid, cliente_cod, nombre, rfc FROM ".DBPREFIX."clientes where `".$campo."` like '%".$valor."%'";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	function searchtalleres( $valor, $campo, $clienteid = '')
    {
        $strSQL = "Select tallerid, taller_cod, nombre FROM ".DBPREFIX."talleres where `".$campo."` like '%".$valor."%'";
		if($clienteid != '') $strSQL .= " AND clienteid = ".$clienteid;
        $rs = $this->dbc->Execute( $strSQL );

        return $rs;
    }

	function cuentareg( $table, $where = 1 )
    {
        $strSQL = "Select COUNT(*) as numreg from ".DBPREFIX.$table." where ".$where;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields["numreg"];
    }
	

	//                   2,      17,      10,        ?pag=
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

	function validatecodigo($codigo, $unidadid = '0', $clienteid = '0')
    {
        $strSQL = 'SELECT count(*) + 1 as cantidad
					FROM '.DBPREFIX.'units u
					LEFT JOIN '.DBPREFIX.'talleres t on u.tallerID = t.tallerid
					WHERE u.numEconomico = "'.$codigo.'" and u.unidadID != '.$unidadid;
		if($clienteid > 0)
			$strSQL .= ' AND '.$clienteid.' = IF(t.clienteid = -1, u.clienteID, u.clienteID)';

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
		
		$acciones = array(1 => "Creada", 2 => "Actualizada");
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