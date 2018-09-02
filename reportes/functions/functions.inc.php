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

    function seeUnits()
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."units";
		$rs     = $this->dbc->Execute( $strSQL);
        return $rs;
    }
	
	function infoUnits($unidadID)
    {
		$strSQL  = "SELECT * FROM ".DBPREFIX."units WHERE unidadID =". $unidadID;
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
	
	function addUnit($unidadID,$clienteID,$tallerID,$numEconomico,$tipo,$marca,$modelo,$placas,$numSerie,$marcaCaja,$modeloCaja,$serieCaja,$marcaCondensador,$modeloCondensador,$serieCondensador,$marcaEvaporador,$modeloEvaporador,$serieEvaporador,$marcaCompresor,$modeloCompresor,$serieCompresor)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."units` (`unidadID`,`clienteID`, `tallerID`, `numEconomico`, `tipo`, `marca`, `modelo`,`placas`,`numSerie`,`marcaCaja`,`modeloCaja`,`serieCaja`,`marcaCondensador`,`modeloCondensador`,`serieCondensador`,`marcaEvaporador`,`modeloEvaporador`,`serieEvaporador`,`marcaCompresor`,`modeloCompresor`,`serieCompresor`)
				VALUES(".$unidadID.",'".$clienteID."','".$tallerID."','".$numEconomico."','".$tipo."','".$marca."','".$modelo."','".$placas."','".$numSerie."','"
.$marcaCaja."','".$modeloCaja."','".$serieCaja."','"
.$marcaCondensador."','".$modeloCondensador."','".$serieCondensador."','"
.$marcaEvaporador."','".$modeloEvaporador."','".$serieEvaporador."','"
.$marcaCompresor."','".$modeloCompresor."','".$serieCompresor."');";
		//echo $strSQL;
		//die();
        $rs      = $this->dbc->Execute( $strSQL );
		//echo $rs;
		//die();
    }

    function editUnit($unidadID,$clienteID,$tallerID,$numEconomico,$tipo,$marca,$modelo,$placas,$numSerie,$marcaCaja,$modeloCaja,$serieCaja,$marcaCondensador,$modeloCondensador,$serieCondensador,$marcaEvaporador,$modeloEvaporador,$serieEvaporador,$marcaCompresor,$modeloCompresor,$serieCompresor)
    {
        $strSQL  = "UPDATE ".DBPREFIX."units SET clienteID ='".$clienteID."'";
		$strSQL .= ", tallerID = '".$tallerID."'";
		$strSQL .= ", numEconomico = '".$numEconomico."'";
		$strSQL .= ", tipo = '".$tipo."'";
		$strSQL .= ", marca = '".$marca."'";
		$strSQL .= ", modelo = '".$modelo."'";
		$strSQL .= ", placas = '".$placas."'";
		$strSQL .= ", numSerie = '".$numSerie."'";
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
		$strSQL .= " WHERE unidadID = ".$unidadID;
		//echo $strSQL;
		//die();
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
}
?>