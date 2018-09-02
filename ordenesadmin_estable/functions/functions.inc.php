<?php

//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class Ordenes
{

    //Funcion utilizada para conectar la base de datos

    function Ordenes( $dist )
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

    function verordenes( $taman, $pag, $filtro = '1' )
    {
		$reg1 = ( $pag - 1 ) * $taman;
        $strSQL  = "SELECT o.* FROM ".DBPREFIX."ordenes o
						LEFT JOIN ".DBPREFIX."units u ON o.unidadid = u.unidadID
						LEFT JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
						LEFT JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)";
		$strSQL .= " WHERE ".$filtro." ORDER BY o.ordenid DESC";
        $rs     = $this->dbc->SelectLimit( $strSQL, $taman, $reg1 );
        return $rs;
    }
	
	function countordenes($filtro = '1' )
    {
        $strSQL  = "SELECT count(DISTINCT o.ordenid) as numreg FROM ".DBPREFIX."ordenes o
						LEFT JOIN ".DBPREFIX."units u ON o.unidadid = u.unidadID
						LEFT JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
						LEFT JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)";
		$strSQL .= " WHERE ".$filtro;
        $rs     =  $this->dbc->Execute( $strSQL );
        return $rs->fields["numreg"];
    }

	function searcharticles( $valor, $campo, $precios = 1)
    {
		if($precios == 1)
			$precios = "";
	
        $strSQL = "Select id, code, name, price".$precios." AS price, `desc` FROM ".DBPREFIX."articles where `".$campo."` like '%".$valor."%'";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function getclientelistaprecios( $clienteid)
    {
        $strSQL = "Select lista_precios FROM ".DBPREFIX."clientes where clienteid = '".$clienteid."'";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields['lista_precios'];
    }

    function getordendata( $ordenid )
    {
        $strSQL = "SELECT t.tallerid AS taller, c.clienteID AS cliente, o.* FROM ".DBPREFIX."ordenes o
						LEFT JOIN ".DBPREFIX."units u ON o.unidadid = u.unidadID
						LEFT JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
						LEFT JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
						WHERE o.ordenid = ".$ordenid." ";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function renombrarArchivo( $nombre )
    {
        $largo    = strlen( $nombre );
        $posPunto = strrpos( $nombre, "." );
        $ext      = substr( $nombre, $posPunto, $largo );
        $nombre   = substr( $nombre, 0, $posPunto );
		$nombre   = $this->Reemplazar($nombre);
        $completo = time( ).$nombre.$ext;
        return $completo;
    }
	
	function Reemplazar($texto){ 
		return str_replace("º","", str_replace(chr(13),"_", str_replace("'", "", 
		str_replace("ñ", "n;", str_replace("Ñ", "N", str_replace("à", "a", str_replace("á", "a", 
		str_replace("À", "A", str_replace("Á", "A", str_replace("é", "e", str_replace("è", "e", 
		str_replace("È", "E;", str_replace("É", "E", str_replace("í", "i", str_replace("Í", "I", 
		str_replace("ó", "o", str_replace("ò", "o", str_replace("Ó", "O", str_replace("Ò", "O", 
		str_replace("ú", "u", str_replace("Ú", "U", str_replace("ü", "u;", str_replace("'", "", 
		str_replace('"', "", str_replace("ç", "c", str_replace("Ç", "C", str_replace("¿", "", 
		str_replace("¡", "", str_replace("º", "deg", str_replace(".", "", $texto)))))))))))))))))))))))))))))); 
	}  
	
    function addorder( $ordenid, $folio, $factura_num, $unidadid, $fecha_orden, $hora_orden, $fecha_orden2, $hora_orden2, $operador, $kilometraje, $reporta, $dias_estimado, $tiempo_est, $dias_real, $tiempo_real, $tipo_mantenimiento, $observaciones, $indicaciones, $trabajos, $horas_equipo_motor, $horas_equipo_diesel, $horas_stand_by, $lugar_servicio, $forma_pago, $diascredito, $tipo_cambio, $taller_servicio)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."ordenes` (`folio` , `factura_num` , `unidadid`,`fecha_captura`,`fecha_orden`,`fecha_orden2`, `operador`, `kilometraje`, `reporta`, `dias_estimado`, `tiempo_estimado`, `dias_real`, `tiempo_real`, `tipo_mantenimiento`, `observaciones`, `indicaciones`, `trabajos`, `horas_equipo_motor`, `horas_equipo_diesel`, `horas_stand_by`, `lugar_servicio`, `forma_pago`, `diascredito`, `tipo_cambio`, `taller_servicio`) VALUES";
        $strSQL .= "('".$folio."','".$factura_num."', '".$unidadid."',NOW(), '".$fecha_orden." ".$hora_orden."', '".$fecha_orden2." ".$hora_orden2."', '".$operador."', '".$kilometraje."', '".$reporta."', '".$dias_estimado."', '".$tiempo_est."', '".$dias_real."', '".$tiempo_real."', '".$tipo_mantenimiento."', '".$observaciones."', '".$indicaciones."', '".$trabajos."', '".$horas_equipo_motor."','".$horas_equipo_diesel."','".$horas_stand_by."', '".$lugar_servicio."', '".$forma_pago."', '".$diascredito."', '".$tipo_cambio."', '".$taller_servicio."')";
		//echo $strSQL;		die();
        $rs      = $this->dbc->Execute( $strSQL );
    }
	
	function editorder( $ordenid, $folio, $factura_num, $unidadid, $fecha_orden, $hora_orden, $fecha_orden2, $hora_orden2, $operador, $kilometraje, $reporta, $dias_estimado, $tiempo_est, $dias_real, $tiempo_real, $tipo_mantenimiento, $observaciones, $indicaciones, $trabajos, $horas_equipo_motor, $horas_equipo_diesel, $horas_stand_by, $lugar_servicio, $forma_pago, $diascredito, $tipo_cambio, $taller_servicio)
    {
        $strSQL  = "UPDATE ".DBPREFIX."ordenes SET folio ='".$folio."'";
		$strSQL .= ", factura_num = '".$factura_num."'";
		$strSQL .= ", unidadid = '".$unidadid."'";
		$strSQL .= ", fecha_orden = '".$fecha_orden." ".$hora_orden."'";
		$strSQL .= ", fecha_orden2 = '".$fecha_orden2." ".$hora_orden2."'";
		$strSQL .= ", operador = '".$operador."'";
		$strSQL .= ", kilometraje = '".$kilometraje."'";
		$strSQL .= ", reporta = '".$reporta."'";
		$strSQL .= ", dias_estimado = '".$dias_estimado."'";
		$strSQL .= ", tiempo_estimado = '".$tiempo_est."'";
		$strSQL .= ", dias_real = '".$dias_real."'";
		$strSQL .= ", tiempo_real = '".$tiempo_real."'";
		$strSQL .= ", tipo_mantenimiento = '".$tipo_mantenimiento."'";
		$strSQL .= ", observaciones = '".$observaciones."'";
		$strSQL .= ", indicaciones = '".$indicaciones."'";
		$strSQL .= ", horas_equipo_motor = '".$horas_equipo_motor."'";
		$strSQL .= ", horas_equipo_diesel = '".$horas_equipo_diesel."'";
		$strSQL .= ", horas_stand_by = '".$horas_stand_by."'";
		$strSQL .= ", lugar_servicio = '".$lugar_servicio."'";
		$strSQL .= ", forma_pago = '".$forma_pago."'";
		$strSQL .= ", diascredito = '".$diascredito."'";
		$strSQL .= ", trabajos = '".$trabajos."'";
		$strSQL .= ", tipo_cambio = '".$tipo_cambio."'";
		$strSQL .= ", taller_servicio = '".$taller_servicio."'";
		$strSQL .= " WHERE ordenid = ".$ordenid;
		//echo $strSQL;
		//die();
        $rs      = $this->dbc->Execute( $strSQL );
		//die();
    }
	
	function addordenitem($ordenid, $articuloid, $cantidad, $precio, $descuento, $dollars)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."orden_item` ( `ordenid` , `articuloid` , `cantidad` , `precio`,  `descuento` , `dollars` ) VALUES";
        $strSQL .= "('".$ordenid."','".$articuloid."','".$cantidad."','".$precio."','".$descuento."','".$dollars."')";
		 //echo $strSQL;		
        $rs      = $this->dbc->Execute( $strSQL );
    }
	
	function addtecnico( $ordenid, $tecnicoid)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."orden_tecnico` ( `ordenid` , `tecnicoid` ) VALUES";
        $strSQL .= "('".$ordenid."','".$tecnicoid."')";
		// echo $strSQL;		
        $rs      = $this->dbc->Execute( $strSQL );
    }

    function droptecnicos( $orden_id )
    {
		
        $strSQL = "DELETE FROM ".DBPREFIX."orden_tecnico where ordenid=".$orden_id;
        $rs = $this->dbc->Execute( $strSQL );
    }
	
	function droporderitem( $orden_id )
    {
		
        $strSQL = "DELETE FROM ".DBPREFIX."orden_item where ordenid=".$orden_id;
        $rs = $this->dbc->Execute( $strSQL );
    }
	
	function droporder( $orden_id )
    {
		
        $strSQL = "DELETE FROM ".DBPREFIX."ordenes where ordenid=".$orden_id;
        $rs = $this->dbc->Execute( $strSQL );
    }
	
	function newid()
    {
		$stringcode = '';
        $strSQL = "Select MAX(ordenid) as maxid FROM ".DBPREFIX."ordenes";
        $rs = $this->dbc->Execute( $strSQL );
				
		if($rs->fields['maxid'] > 0)
			return 1 + $rs->fields['maxid'];
		else
			return 1;
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
	
	function getpagos($actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select * FROM ".DBPREFIX."tipos_pago";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['id_pago'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['id_pago'].'" '.$selected.'>'.$rs->fields['tipo_pago'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getmantenimientos($actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select * FROM ".DBPREFIX."mantenimientos";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['mantenimientoid'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['mantenimientoid'].'" '.$selected.'>'.$rs->fields['mantenimiento'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function gettecnicos($actual = 0)
    {
		$stringcode = '<option value="0" >- SELECCIONAR -</option>';
        $strSQL = "Select tecnicoid, CONCAT(nombre, ' ', apeido_pat, ' ', apeido_mat) as nombre FROM ".DBPREFIX."tecnicos where status = 1 ORDER BY nombre";
		//echo $strSQL;
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['tecnicoid'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['tecnicoid'].'" '.$selected.'>'.$rs->fields['nombre'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getdroptalleres($clienteid = 0, $actual = 0)
    {
		$stringcode = '<select name="taller_servicio" id="taller_servicio">
		<option value="0" >- SELECCIONAR -</option>';
		
        $strSQL = "Select tallerid, taller_cod, nombre FROM ".DBPREFIX."talleres where 1";
		if($clienteid != 0)
			$strSQL .= " AND clienteid = -1 OR clienteid =".$clienteid;
		//echo $strSQL;
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['tallerid'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['tallerid'].'" '.$selected.'>'.$rs->fields['taller_cod'].' - '.$rs->fields['nombre'].'</option>';
			$rs->MoveNext();
		};
		
		$stringcode .= '</select>';
		
        return $stringcode;
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
	
	function getunidades($tallerid, $actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select unidadID, numEconomico, tipo, marca FROM ".DBPREFIX."units where tallerID = ".$tallerid;
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['unidadID'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['unidadID'].'" '.$selected.'>'.$rs->fields['numEconomico'].' ('.$rs->fields['tipo'].' '.$rs->fields['marca'].')</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function getordentecnicos($ordenid)
    {
		$stringcode = '';
        $strSQL = "Select tecnicoid FROM ".DBPREFIX."orden_tecnico where ordenid = ".$ordenid;
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			$temp_array['tecnicoid'] = $rs->fields['tecnicoid'];
			$temp_array['tecnico'] = $this->gettecniconame($rs->fields['tecnicoid']);

			$_SESSION['tecnicos'][] = $temp_array;

	  	  $rs->MoveNext();
		};
		
		$stringcode = '<center><table border="0">
			<tr>';
		for($i = 0; $i < count($_SESSION['tecnicos']); $i++)
		{
			$stringcode .= '<td>'.$_SESSION['tecnicos'][$i][tecnico].'</td>';
			$stringcode .= '<td><strong><a href="javascript:eliminar('.$i.')">X</a></strong></td>';
			$stringcode .= '</tr></r>';
		}
		$stringcode .= '</tr>
		</table></center>';	
		
        return $stringcode;
    }
	
	function getordenarticulos($ordenid, $tipo_cambio)
    {
		$tasa_impuesto = $this->gettasa();;
        $strSQL = "Select oi.articuloid, a.name, oi.cantidad, oi.precio, a.mano_obra, oi.descuento, oi.dollars, a.code FROM ".DBPREFIX."orden_item oi
					inner join ".DBPREFIX."articles a on a.id = oi.articuloid where oi.ordenid = ".$ordenid;
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			$temp_array['articuloid'] = $rs->fields['articuloid'];
			$temp_array['cantidad'] = $rs->fields['cantidad'];
			$temp_array['refaccion'] = $rs->fields['name'];
			$temp_array['precio'] = $rs->fields['precio'];
			$temp_array['code'] = $rs->fields['code'];
			$temp_array['mano_obra'] = $rs->fields['mano_obra'];
			$temp_array['descuento'] = $rs->fields['descuento'];
			$temp_array['dollars'] = $rs->fields['dollars'];
			
			$_SESSION['refacciones'][] = $temp_array;

	  	  $rs->MoveNext();
		};
		
		$stringcode = '<table width="100%" border="1">
				<tr>
				<td>&nbsp;</td>
				<td><div align="center"><strong>Cantidad</strong></div></td>
				<td><div align="center"><strong>Refacci&oacute;n</strong></div></td>
				<td><div align="center"><strong>C&oacute;d.</strong></div></td>
				<td><div align="center"><strong>Precio</strong></div></td>
				<td><div align="center"><strong>Desc.</strong></div></td>
				<td style="width:55px;"><div align="center"><strong>Importe</strong></div></td>
			  </tr>
			  <tr>';
			  
		$total_ref = 0;
		$total_mano = 0;
		for($i = 0; $i < count($_SESSION['refacciones']); $i++)
		{
					$importe = $_SESSION['refacciones'][$i]['precio'] * $_SESSION['refacciones'][$i]['cantidad'];
		
					if($_SESSION['refacciones'][$i]['dollars'] == true)
					{
						$importe = $importe * $tipo_cambio;
						$precio_mostrar = $_SESSION['refacciones'][$i]['precio'] * $tipo_cambio;
					}
					else
						$precio_mostrar = $_SESSION['refacciones'][$i]['precio'];
					
					if($_SESSION['refacciones'][$i]['descuento'] > 0)
						$importe = $importe - ($importe * ($_SESSION['refacciones'][$i]['descuento']/100));
		
					$stringcode .= '<td><strong><a href="javascript:quitar_articulo('.$i.')">X</a></strong></td>';
					$stringcode .= '<td>'.$_SESSION['refacciones'][$i][cantidad].'</td>';
					$stringcode .= '<td>'.$_SESSION['refacciones'][$i][refaccion].'</td>';
					$stringcode .= '<td>'.$_SESSION['refacciones'][$i][code].'</td>';
					$stringcode .= '<td><div align="right">$'.$precio_mostrar.'</div></td>';
					$stringcode .= '<td><div align="right">'.$_SESSION['refacciones'][$i][descuento].'%</div></td>';
					$stringcode .= '<td><div align="right">$'.$importe.'</div></td>';
					$stringcode .= '</tr><tr>';
					
					if($_SESSION['refacciones'][$i][mano_obra] == 0)
						$total_ref += $importe;
					else
						$total_mano += $importe;
				}
				
			$stringcode .= '</tr>
				</table>';
				
			$subtotal = $total_ref + $total_mano;
			$impuesto = $subtotal * ($tasa_impuesto / 100);
			$total = $subtotal + $impuesto;
			$stringcode .= '<table align="right" border="1">
					<tr>
						<td>Total mano de obra:</td>
						<td style="width:55px;"><div align="right"><strong>$'.$total_mano.'</strong></div></td>
					</tr>
					<tr>
						<td>Total refacciones:</td>
						<td style="width:55px;"><div align="right"><strong>$'.$total_ref.'</strong></div></td>
					</tr>
					<tr>
						<td>Tasa de impuesto:</td>
						<td style="width:55px;"><div align="right"><strong>'.$tasa_impuesto.'%</strong></div></td>
					</tr>
					<tr>
						<td>Subtotal:</td>
						<td style="width:55px;"><div align="right"><strong>$'.$subtotal.'</strong></div></td>
					</tr>
					<tr>
						<td>Impuesto:</td>
						<td style="width:55px;"><div align="right"><strong>$'.$impuesto.'</strong></div></td>
					</tr>
					<tr>
						<td>Total:</td>
						<td style="width:55px;"><div align="right"><strong>$'.$total.'</strong></div></td>
					</tr>
				  </table>';
		
        return $stringcode;
    }
	
	function getunidadinfo($unidadid)
    {
		$stringcode = '';
        $strSQL = "Select numEconomico, tipo, marca FROM ".DBPREFIX."units where unidadID = ".$unidadid;
        $rs = $this->dbc->Execute( $strSQL );
		
			$unidad .=$rs->fields['numEconomico'].' ('.$rs->fields['tipo'].' '.$rs->fields['marca'].')';
			$rs->MoveNext();
		
        return $unidad;
    }
	
	function gettasa($tipo = 1)
    {
		$stringcode = '';
        $strSQL = "Select iva FROM ".DBPREFIX."iva where tipo = ".$tipo;
        $rs = $this->dbc->Execute( $strSQL );

        return $rs->fields['iva'];
    }
	
	function getdescuento_cliente($clienteid)
    {
		if($clienteid == "")
			return "0";
		else
		{
			$stringcode = '';
			$strSQL = "Select descuento FROM ".DBPREFIX."clientes where clienteid = ".$clienteid;
			$rs = $this->dbc->Execute( $strSQL );
			
			return $rs->fields['descuento'];
		}
    }
	
	function getarticuloinfo($articuloid)
    {
		$stringcode = '';
        $strSQL = "Select id, name, price, mano_obra, dollars, code FROM ".DBPREFIX."articles where code = '".$articuloid."'";
        $rs = $this->dbc->Execute( $strSQL );
		
		if(!$rs->EOF)
		{
			$articulo['id'] = $rs->fields['id'];
			$articulo['name'] = $rs->fields['name'];
			$articulo['price'] = $rs->fields['price'];
			$articulo['mano_obra'] = $rs->fields['mano_obra'];
			$articulo['dollars'] = $rs->fields['dollars'];
			$articulo['code'] = $rs->fields['code'];
			
			
			return $articulo;
		}
		else
		{
			return 0;
		}
    }
	
	function gettecniconame($tecnicoid)
    {
		$stringcode = '';
        $strSQL = "Select tecnicoid, CONCAT(nombre, ' ', apeido_pat, ' ', apeido_mat) as nombre FROM ".DBPREFIX."tecnicos where tecnicoid = ".$tecnicoid;
        $rs = $this->dbc->Execute( $strSQL );
		
        return $rs->fields["nombre"];
    }
	
	function getarticulos($actual = 0)
    {
		$stringcode = '';
        $strSQL = "Select id, name FROM ".DBPREFIX."articles";
        $rs = $this->dbc->Execute( $strSQL );
		
		while(!$rs->EOF)
		{
			if($actual == $rs->fields['id'])
				$selected = 'selected="selected"';
			else
				$selected = '';
			$stringcode .='<option value="'.$rs->fields['id'].'" '.$selected.'>'.$rs->fields['name'].'</option>';
			$rs->MoveNext();
		};
		
        return $stringcode;
    }
	
	function gethoras($actual = 0)
    {	
		$stringcode = '';
		for($i=0; $i < 24; $i++)
		{
			if($actual == $i)
				$selected = 'selected="selected"';
			else
				$selected = '';
				
			if($i < 10)
				$valor = '0'.$i;
			else
				$valor = $i;
			
			$stringcode .='<option value="'.$i.'" '.$selected.'>'.$valor.'</option>';
		};
		
        return $stringcode;
    }
	
	function getdias($actual = 0)
    {	
		$stringcode = '';
		for($i=1; $i < 31; $i++)
		{
			if($actual == $i)
				$selected = 'selected="selected"';
			else
				$selected = '';
			
			$stringcode .='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		};
		
        return $stringcode;
    }
	
	function getminutos($actual = 0)
    {	
		$stringcode = '';
		for($i=0; $i < 60; $i++)
		{
			if($actual == $i)
				$selected = 'selected="selected"';
			else
				$selected = '';
				
			if($i < 10)
				$valor = '0'.$i;
			else
				$valor = $i;
			
			$stringcode .='<option value="'.$i.'" '.$selected.'>'.$valor.'</option>';
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
	
	function searchunidades( $valor, $campo, $clienteid = '')
    {
        $strSQL = "Select u.unidadid, u.numEconomico, u.placas, u.marca, u.tipo, u.modelo, u.numSerie
					FROM ".DBPREFIX."units u
					LEFT JOIN ".DBPREFIX."talleres t ON u.tallerID = t.tallerid
					LEFT JOIN ".DBPREFIX."clientes c ON c.clienteid = IF(t.clienteid = -1, u.clienteID, t.clienteid)
					where u.".$campo." like '%".$valor."%'";
		if($clienteid != '') $strSQL .= " AND c.clienteid = ".$clienteid;

        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
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
	function getunidadnombre($unidadid)
    {
        $strSQL = "Select numEconomico FROM ".DBPREFIX."units where unidadID = ".$unidadid;
        $rs = $this->dbc->Execute( $strSQL );
		
        return $rs->fields['numEconomico'];
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
	
	
	function paginarfiltro( $actual, $total, $por_pagina, $date1, $date2, $clienteid, $tallerid, $unidadid, $tallerservicioid, $folio )
    {
        $total_paginas = ceil( $total / $por_pagina );
        $anterior      = $actual - 1;
        $posterior     = $actual + 1;
        if ( $total_paginas > 1 )
        {
            $texto .= '<table cellpadding="0" cellspacing="0" align="center"><tr>';
            if ( $actual > 1 )
            {
                $texto .= "<td><a href='javascript:submitbusqueda(\"$fecha1\", \"$fecha2\", \"$clienteid\", \"$tallerid\", \"$tallerid\", \"$folio\", \"$tallerservicioid\", 1);'><img src='../imagenes/flechabegin.png' height='20' width='33' border='0'></a>&nbsp;";
                $texto .= "<a href='javascript:submitbusqueda(\"$fecha1\", \"$fecha2\", \"$clienteid\", \"$tallerid\", \"$tallerid\", \"$folio\", \"$tallerservicioid\", $anterior);'><img src='../imagenes/flechaback.png' height='20' width='25' border='0'></a></td>";
            }
            else
            {
                $texto .= "<td><img src='../imagenes/flechanobegin.png' height='20' width='33' border='0'>&nbsp;";
                $texto .= "<img src='../imagenes/flechanoback.png' height='20' width='25' border='0'></td>";
            }
            $texto .= "<td style='width:70px;'>$actual / $total_paginas</td>";
            if ( $actual < $total_paginas )
            {
                $texto .= "<td><a href='javascript:submitbusqueda(\"$fecha1\", \"$fecha2\", \"$clienteid\", \"$tallerid\", \"$tallerid\", \"$folio\", \"$tallerservicioid\", $posterior);'><img src='../imagenes/flechanext.png' height='20' width='25' border='0'></a>&nbsp;";
                $texto .= "<a href='javascript:submitbusqueda(\"$fecha1\", \"$fecha2\", \"$clienteid\", \"$tallerid\", \"$tallerid\", \"$folio\", \"$tallerservicioid\", $total_paginas);'><img src='../imagenes/flechaend.png' height='20' width='33' border='0'></a></td>";
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

	function validatefolio($folio, $order_id = '0')
    {
        $strSQL = 'SELECT count(*) + 1 as cantidad
					FROM '.DBPREFIX.'ordenes o
					WHERE o.folio = "'.$folio.'" and o.ordenid != '.$order_id;
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