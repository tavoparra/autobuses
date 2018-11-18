<?php

//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class exportacion
{

    //Funcion utilizada para conectar la base de datos

    function exportacion( $dist )
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
	
	function getrefaccionesinfo($filtro = ''){
		
        $strSQL = 'SELECT c.nombre AS cliente, ts.nombre AS taller, a.id, a.code, a.name as \'desc\', SUM(oi.cantidad) AS cantidad, m.medida, u.numEconomico
					FROM '.DBPREFIX.'ordenes o
					INNER JOIN '.DBPREFIX.'orden_item oi ON o.ordenid = oi.ordenid
					INNER JOIN '.DBPREFIX.'articles a ON oi.articuloid = a.id AND a.mano_obra != 1
					INNER JOIN '.DBPREFIX.'medidas m ON a.medida_id = m.medida_id
					LEFT JOIN '.DBPREFIX.'units u ON o.unidadid = u.unidadID
					LEFT JOIN '.DBPREFIX.'talleres t ON u.tallerID = t.tallerid
					LEFT JOIN '.DBPREFIX.'talleres ts ON o.taller_servicio = ts.tallerid
					LEFT JOIN '.DBPREFIX.'clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					WHERE '.$filtro.'
					GROUP BY a.id ORDER BY a.code ASC';
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function getordenesinfo($filtro)
    {
        $strSQL = 'SELECT o.ordenid, c.cliente_cod, c.nombre, u.numEconomico, u.tipo, u.marca, u.modelo, u.placas,
					u.marcaEquipo, u.modeloEquipo, u.serieEquipo,
					o.folio, o.fecha_orden, o.factura_num, o.trabajos, GROUP_CONCAT(" ", tec.nombre, " ", tec.apeido_pat, " ", tec.apeido_mat) as tecnicos,
					ts.nombre as tallerservicio, o.horas_equipo_motor as horas_totales, o.horas_equipo_diesel, o.horas_stand_by
					FROM '.DBPREFIX.'ordenes o
					LEFT JOIN '.DBPREFIX.'units u ON o.unidadid = u.unidadID
					LEFT JOIN '.DBPREFIX.'talleres t ON u.tallerID = t.tallerid
					LEFT JOIN '.DBPREFIX.'clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					LEFT JOIN '.DBPREFIX.'orden_tecnico ot ON o.ordenid = ot.ordenid
					LEFT JOIN '.DBPREFIX.'tecnicos tec ON ot.tecnicoid = tec.tecnicoid
					LEFT JOIN '.DBPREFIX.'talleres ts ON o.taller_servicio = ts.tallerid
					WHERE '.$filtro.'
					GROUP BY o.ordenid ORDER BY c.cliente_cod,u.ordenamiento, LPAD(u.numEconomico, 50, "0"), o.fecha_orden ASC';
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function getordenesinfo2($ordenid, $order_type = 'ordenid')
    {
        $strSQL = 'SELECT o.ordenid, c.nombre, c.cliente_cod, o.folio, u.tipo, u.marca, u.modelo, u.placas, u.numSerie, u.numEconomico, o.operador, u.serieEquipo, u.modeloEquipo,
					u.serieCompresor, o.fecha_orden, o.fecha_orden2, o.dias_estimado, o.tiempo_estimado, o.dias_real, o.tiempo_real, o.reporta,
					GROUP_CONCAT(cc.nombre, ": ", cc.telefono) AS telefono,	o.trabajos, GROUP_CONCAT(tec.nombre, " ", tec.apeido_pat, " ", tec.apeido_mat) AS tecnico
					FROM '.DBPREFIX.'ordenes o
					LEFT JOIN '.DBPREFIX.'units u ON o.unidadid = u.unidadID
					LEFT JOIN '.DBPREFIX.'talleres t ON u.tallerID = t.tallerid
					LEFT JOIN '.DBPREFIX.'clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					LEFT JOIN '.DBPREFIX.'clientes_contactos cc ON c.clienteid = cc.clienteid
					LEFT JOIN '.DBPREFIX.'orden_tecnico ot ON o.ordenid = ot.ordenid
					LEFT JOIN '.DBPREFIX.'tecnicos tec ON ot.tecnicoid = tec.tecnicoid
					WHERE o.'.$order_type.' = "'.$ordenid.'"
					GROUP BY o.ordenid';
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function getrefacciones($ordenid)
    {
        $strSQL = 'SELECT oi.cantidad, a.name, c.medida AS sz_of_weight, a.code FROM '.DBPREFIX.'orden_item oi
					INNER JOIN '.DBPREFIX.'articles a ON oi.articuloid = a.id
					INNER JOIN '.DBPREFIX.'medidas c ON a.medida_id = c.medida_id
					WHERE oi.ordenid ='.$ordenid;
        $rs = $this->dbc->Execute( $strSQL );
		
		$string = "";
		do{
			$string .= "* ".$rs->fields['cantidad']." ".$rs->fields['sz_of_weight']." ".$rs->fields['name']." (".$rs->fields['code'].") ";
			$rs->MoveNext();
		}while (!$rs->EOF );
		
        return $string;
    }
	
	function getordentotal($ordenid)
    {	
        $strSQL = 'SELECT cantidad,precio, descuento, dollars FROM '.DBPREFIX.'orden_item WHERE ordenid ='.$ordenid;
        $rs = $this->dbc->Execute( $strSQL );
		
		
		$strSQL = 'SELECT tipo_cambio FROM '.DBPREFIX.'ordenes WHERE ordenid ='.$ordenid;
        $rs2 = $this->dbc->Execute( $strSQL );
	
		$tipo_cambio = $rs2->fields['tipo_cambio'];
	
		$total = 0;
		while(!$rs->EOF)
		{
			$importe = $rs->fields['cantidad'] * $rs->fields['precio'];
			
			if($rs->fields['dollars'] == 1)
			{
				$importe = $importe * $tipo_cambio;
			}
			//echo $importe." <br/>";
			
			if($rs->fields['descuento'] > 0)
				$importe = $importe - ($importe * ($rs->fields['descuento'] /100));
			
	
			$total += $importe;

			//echo "total:".$total."<br/>";	
			$rs->MoveNext();
		}
	
		$total = $total * 1.16;
		//echo $total;
        return $total;
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
	
	function searchunidades( $valor, $campo, $clienteid = ''){
        $strSQL = "Select u.unidadid, u.numEconomico, u.placas, u.marca, u.tipo, u.modelo, u.numSerie
					FROM ".DBPREFIX."units u
					LEFT JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
					LEFT JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					where u.".$campo." like '%".$valor."%'";
		if($clienteid != '') $strSQL .= " AND c.clienteid = ".$clienteid;

        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
	}
	
	function cargaranhios()
	{
		$strSQL = "Select min(fecha_orden) as principio, max(fecha_orden) as fin FROM ".DBPREFIX."ordenes WHERE fecha_orden != '0000-00-00 00:00:00'";
        $rs = $this->dbc->Execute( $strSQL );
		
		if($rs->fields['principio'] != '')
		{
			$inicio	 = substr($rs->fields['principio'],0,4);
			$fin	 = substr($rs->fields['fin'],0,4);
			
			$code = '';
			
			for($i = $fin; $i >= $inicio; $i--)
			{
				$code .= '
				<option value="'.$i.'">'.$i.'</option>';
			}
		}
		
		return $code;
	}
	
	function total_mantenimientos($filtro)
	{
		$strSQL = "SELECT COUNT(DISTINCT o.unidadid) AS unidades, COUNT(DISTINCT(o.ordenid)) AS servicios,
					ROUND(SUM(
						IF(oi.descuento = 0,
							IF(oi.dollars = 0, oi.cantidad * oi.precio, (oi.cantidad * oi.precio)*o.tipo_cambio),
							IF(oi.dollars = 0, (oi.cantidad * oi.precio) * ((100 - oi.descuento)/100), (oi.cantidad * oi.precio) * ((100 - oi.descuento)/100)*o.tipo_cambio)
						)
					) * ((100 + o.iva)/100), 2) AS costo
					FROM ".DBPREFIX."ordenes o
					INNER JOIN ".DBPREFIX."units u ON o.unidadid = u.unidadID
					INNER JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
					INNER JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					LEFT JOIN ".DBPREFIX."orden_item oi ON o.ordenid = oi.ordenid
					WHERE ".$filtro;
        $rs = $this->dbc->Execute( $strSQL );
				
		return $rs;
	}
	
	function getmantenimientos($field_name = 'tipo_mantenimiento')
    {
		$stringcode = '<table style="text-align: left;"><tr>';
		$break = '';
        $strSQL = "Select * FROM ".DBPREFIX."mantenimientos";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF){
			$stringcode .='<td><input type="checkbox" name="'.$field_name.'[]" value="'.$rs->fields['mantenimientoid'].'" checked >'.$rs->fields['mantenimiento']."</td>$break\r\n";
			$break = ($break == '') ? '</tr></tr>' :  '';
			$rs->MoveNext();
		};
		
		$stringcode .= '</tr></table>';
		
        return $stringcode;
    }
	
	
	function mantenimientos_info($filtro, $separarEquipo = false) {
		//Si se eligió separar por equipos, ordenamos por el modelo sin espacios para que no haya problemas por ellos
		$groupByEquipo = ($separarEquipo == true) ? 'REPLACE(u.modeloEquipo, " ", ""),' : '';
		$strSQL = "SELECT YEAR(o.fecha_orden) as anhio, t.tallerid, c.nombre as cliente, ts.nombre as taller, u.numEconomico, 
					SUM(IF(MONTH(o.fecha_orden) = 1, 1, 0)) AS ene,
					SUM(IF(MONTH(o.fecha_orden) = 2, 1, 0)) AS feb,
					SUM(IF(MONTH(o.fecha_orden) = 3, 1, 0)) AS mar,
					SUM(IF(MONTH(o.fecha_orden) = 4, 1, 0)) AS abr,
					SUM(IF(MONTH(o.fecha_orden) = 5, 1, 0)) AS may,
					SUM(IF(MONTH(o.fecha_orden) = 6, 1, 0)) AS jun,
					SUM(IF(MONTH(o.fecha_orden) = 7, 1, 0)) AS jul,
					SUM(IF(MONTH(o.fecha_orden) = 8, 1, 0)) AS ago,
					SUM(IF(MONTH(o.fecha_orden) = 9, 1, 0)) AS sep,
					SUM(IF(MONTH(o.fecha_orden) = 10, 1, 0)) AS oc,
					SUM(IF(MONTH(o.fecha_orden) = 11, 1, 0)) AS nov,
					SUM(IF(MONTH(o.fecha_orden) = 12, 1, 0)) AS dic,
					COUNT(DISTINCT o.ordenid) AS total,
					u.modeloEquipo
					FROM ".DBPREFIX."ordenes o
					INNER JOIN ".DBPREFIX."units u ON o.unidadid = u.unidadID
					INNER JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
					LEFT JOIN ".DBPREFIX."talleres ts ON o.taller_servicio = ts.tallerid
					INNER JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					WHERE ".$filtro."
					GROUP BY YEAR(o.fecha_orden) ASC, u.unidadID ORDER BY anhio ASC, $groupByEquipo u.ordenamiento, LPAD(u.numEconomico, 50, '0');";
        $rs = $this->dbc->Execute( $strSQL ); 
				
		return $rs;
	}
	
	function totalesMantenimientos($filtro)
	{
		$strSQL = "SELECT  m.mantenimiento , count(*) as conteo
					FROM ".DBPREFIX."ordenes o
					INNER JOIN ".DBPREFIX."units u ON o.unidadid = u.unidadID
					INNER JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
					LEFT JOIN ".DBPREFIX."talleres ts ON o.taller_servicio = ts.tallerid
					INNER JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					INNER JOIN ".DBPREFIX."mantenimientos m on o.tipo_mantenimiento = m.mantenimientoid
					WHERE ".$filtro."
					GROUP BY o.tipo_mantenimiento;";
        $rs = $this->dbc->Execute( $strSQL ); 
				
		return $rs;
	}


	function mantenimiento_name($id)
	{
		$strSQL = "SELECT mantenimiento
					FROM ".DBPREFIX."mantenimientos
					WHERE mantenimientoid = ".$id;
        $rs = $this->dbc->Execute( $strSQL ); 
				
		return $rs->fields['mantenimiento'];
	}
	
	function getProximosServicios($filtro = ''){
		
        $strSQL = 'SELECT o.unidadid, u.numEconomico, u.placas, u.marca, IFNULL(c.nombre, "") AS cliente,
					u.tipo, u.modelo, u.modeloEquipo, u.serieEquipo, DATE_FORMAT(fecha_prox,"%d-%m-%Y") as fecha_prox, tm.mantenimiento as prox_mantenimiento
					FROM ca_ordenes o
					LEFT JOIN '.DBPREFIX.'units u ON o.unidadid = u.unidadID
					LEFT JOIN '.DBPREFIX.'talleres t ON u.tallerID = t.tallerid
					LEFT JOIN '.DBPREFIX.'talleres ts ON o.taller_servicio = ts.tallerid
					LEFT JOIN '.DBPREFIX.'clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					INNER JOIN '.DBPREFIX.'mantenimientos tm ON o.tipo_prox = tm.mantenimientoid
					WHERE '.$filtro.' ORDER BY o.fecha_prox ASC';
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function getUltimoServicio($unidadid){
		$sql = "SELECT DATE_FORMAT(o.fecha_orden,\"%d-%m-%Y\") as fecha_orden, m.mantenimiento FROM ca_ordenes o
				LEFT JOIN ca_mantenimientos m ON o.tipo_mantenimiento = m.mantenimientoid
				WHERE o.unidadid = $unidadid ORDER BY fecha_orden DESC LIMIT 1";
		$ultimoServicio = $this->dbc->Execute($sql);
		
		if($ultimoServicio->EOF){
			$data['tipoServicio'] = "";
			$data['fechaServicio'] = "";
		}
		else{
			$data['tipoServicio'] = $ultimoServicio->fields['mantenimiento'];
			$data['fechaServicio'] = $ultimoServicio->fields['fecha_orden'];
		}
		
		return $data;
	}
	
	function getClienteName($clienteid){
		$strSQL = "SELECT nombre
					FROM ".DBPREFIX."clientes
					WHERE clienteid = ".$clienteid;
        $rs = $this->dbc->Execute( $strSQL ); 
				
		return $rs->fields['nombre'];
	}
	
	function getTallerName($tallerid){
		$strSQL = "SELECT nombre
					FROM ".DBPREFIX."talleres
					WHERE tallerid = ".$tallerid;
        $rs = $this->dbc->Execute( $strSQL ); 
				
		return $rs->fields['nombre'];
	}
	
	function formatSqlDate($unformatedDate){
		$dia = substr($unformatedDate, 0, 2);
		$mes   = substr($unformatedDate, 3, 2);
		$ano = substr($unformatedDate, -4);
		
		// fechal final realizada el cambio de formato a las fechas europeas
		return $ano . '-' . $mes . '-' . $dia;
	}

	public function getExternalLogo(){
		$archivo = $_FILES["logo"]['name'];

		$prefijo = substr(md5(uniqid(rand())),0,6);
	
		if ($archivo != "") {
			$destino =  "../imagenes/tmp/".$prefijo."_".$archivo;
			if (copy($_FILES['logo']['tmp_name'],$destino)) 
				$logo = $prefijo."_".$archivo;
			else
			{
				$logo = "";
			}
		} else {
			$logo = "";
		}

		return ($logo == '') ? '&nbsp;' : '<img src="../imagenes/tmp/'.$logo.'" height="92" />';
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
}
?>