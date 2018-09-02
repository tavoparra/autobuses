<?php
//Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos

class Usuarios
{

    //Funcion utilizada para conectar la base de datos

    function Usuarios( $dist )
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

    // FUNCIONES DE USUARIO

    function getLevelselect( $level )
    {
        $combodata = "";
        $strSQL    = "Select * from ".DBPREFIX."niveles";
        $rs        = &$this->dbc->Execute( $strSQL );
        while ( !$rs->EOF )
        {
            $combodata .= "<option value='".$rs->fields[0]."'";
            if ( $rs->fields[0] == $level )
            {
                $combodata .= " selected";
            }
            $combodata .= ">".$rs->fields[2]."</option>";
            $rs->MoveNext( );
        }
        return $combodata;
    }

    function getUsers( $nivel, $taman, $pag )
    {
        $reg1    = ( $pag - 1 ) * $taman;
        $strSQL  = "Select perfil from ".DBPREFIX."niveles where nivel=".$nivel;
        $rs      = $this->dbc->Execute( $strSQL );
        $profile = $rs->fields[0];
        if ( $profile <= 4 )
        {
            $strSQL = "select ".DBPREFIX."usuarios.*,".DBPREFIX."contactos.* from ".DBPREFIX."usuarios,".DBPREFIX."contactos where ".DBPREFIX."usuarios.userid=".DBPREFIX."contactos.idUsuario and nivel=".$nivel." ORDER BY username";
        }
        else
        {
            $strSQL = "select ".DBPREFIX."usuarios.*,".DBPREFIX."clientes.* from ".DBPREFIX."usuarios,".DBPREFIX."clientes where ".DBPREFIX."usuarios.userid=".DBPREFIX."clientes.userid and nivel=".$nivel." ORDER BY username";
        }
        $rs = $this->dbc->SelectLimit( $strSQL, $taman, $reg1 );
        return $rs;
    }

    function countUsersreg( $nivel )
    {
        $strSQL  = "Select perfil from ".DBPREFIX."niveles where nivel=".$nivel;
        $rs      = $this->dbc->Execute( $strSQL );
        $profile = $rs->fields[0];
        if ( $profile <= 3 )
        {
            $strSQL = "select COUNT(*) from ".DBPREFIX."usuarios,".DBPREFIX."contactos where ".DBPREFIX."usuarios.userid=".DBPREFIX."contactos.idUsuario and nivel=".$nivel;
        }
        else
        {
            $strSQL = "select COUNT(*) from ".DBPREFIX."usuarios,".DBPREFIX."clientes where ".DBPREFIX."usuarios.userid=".DBPREFIX."clientes.userid and nivel=".$nivel;
        }
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getProfile( $mode )
    {
        $strSQL = "Select perfil from ".DBPREFIX."niveles where nivel=".$mode;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }
	
	function getRol( $idrol )
    {
        $strSQL = "Select nombre from ".DBPREFIX."roles where idrol=".$idrol;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getLevelName( $mode )
    {
        $strSQL = "Select nombre from ".DBPREFIX."niveles where nivel=".$mode;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getUsersperadmin( $userid, $taman, $pag )
    {
        $reg1      = ( $pag - 1 ) * $taman;
        $strSQL    = "Select idContacto from ".DBPREFIX."contactos,".DBPREFIX."usuarios where idUsuario=userid and userid=".$userid;
        $rs        = $this->dbc->Execute( $strSQL );
        $idcontact = $rs->fields[0];
        $strSQL    = "Select ".DBPREFIX."clientes.userid, ".DBPREFIX."clientes.nombre, ".DBPREFIX."usuarios.nivel from ".DBPREFIX."clientes, ".DBPREFIX."contactocliente,".DBPREFIX."usuarios where ".DBPREFIX."clientes.idCliente=".DBPREFIX."contactocliente.idCliente ";
        $strSQL   .= " and ".DBPREFIX."usuarios.userid=".DBPREFIX."clientes.userid and ";
        $strSQL   .= DBPREFIX."contactocliente.idContacto=".$idcontact." ";
        $rs        = $this->dbc->SelectLimit( $strSQL, $taman, $reg1 );
        $rs        = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function countClientreg( $userid )
    {
        $strSQL    = "Select idContacto from ".DBPREFIX."contactos,".DBPREFIX."usuarios where idUsuario=userid and userid=".$userid;
        $rs        = $this->dbc->Execute( $strSQL );
        $idcontact = $rs->fields[0];
        $strSQL    = "Select COUNT(*) from ".DBPREFIX."clientes,".DBPREFIX."contactocliente where ".DBPREFIX."clientes.idCliente=".DBPREFIX."contactocliente.idCliente and ";
        $strSQL   .= DBPREFIX."contactocliente.idContacto=".$idcontact;
        $rs        = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getUserlevelname( $iduser )
    {
        $strSQL = "Select nivel from ".DBPREFIX."usuarios where userid=".$iduser;
        $rs     = $this->dbc->Execute( $strSQL );
        $grupo  = $rs->fields['nivel'];
        $strSQL = "Select nombre from ".DBPREFIX."niveles where nivel=".$grupo;
        $rs     = $this->dbc->Execute( $strSQL );
        return $rs->fields['nombre'];
    }

    function getUserdata( $usuario )
    {
        $strSQL  = "select ".DBPREFIX."usuarios.* from ".DBPREFIX."usuarios where userid=".$usuario." ";
        $rs      = $this->dbc->Execute( $strSQL );
        $strSQL  = "Select perfil from ".DBPREFIX."niveles where nivel=".$rs->fields['nivel'];
        $rs      = $this->dbc->Execute( $strSQL );
        $profile = $rs->fields['perfil'];
        if ( $profile <= 3 )
        {
            $strSQL = "select ".DBPREFIX."usuarios.*,".DBPREFIX."contactos.* from ".DBPREFIX."usuarios,".DBPREFIX."contactos where ".DBPREFIX."usuarios.userid=".DBPREFIX."contactos.idUsuario and userid=".$usuario;
        }
        else
        {
            $strSQL = "select ".DBPREFIX."usuarios.*,".DBPREFIX."clientes.* from ".DBPREFIX."usuarios,".DBPREFIX."clientes where ".DBPREFIX."usuarios.userid=".DBPREFIX."clientes.userid and ".DBPREFIX."usuarios.userid=".$usuario;
        }
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }
	
	function getsecciones()
    {
        $strSQL  = "select * from ".DBPREFIX."secciones";
        $rs      = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function dropUsuario( $usuario )
    {
        $strSQL = "DELETE FROM ".DBPREFIX."usuarios where userid=".$usuario;
        $rs = $this->dbc->Execute( $strSQL );

        $strSQL = "DELETE FROM ".DBPREFIX."contactos where idUsuario=".$usuario;
        $rs     = $this->dbc->Execute( $strSQL );
		
        $this->dropPermisos($usuario);
    }
	
	function dropPermisos( $usuario )
    {
        $strSQL = "DELETE FROM ".DBPREFIX."permisos where usuarioid=".$usuario;
        $rs     = $this->dbc->Execute( $strSQL );
    }
	
	

    function addUsuario( $firstname, $lastname, $clave, $login, $password, $select )
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."usuarios` ( `username` , `password` , `fecha`, `nivel`) VALUES ('".$login."','".$password."', NOW(),".$select.")";
        $rs      = $this->dbc->Execute( $strSQL );
        $strSQL  = "SELECT MAX( userid ) as id FROM ".DBPREFIX."usuarios";
        $rs      = $this->dbc->Execute( $strSQL );
        $userid  = $rs->fields['id'];
        $strSQL  = "INSERT INTO `".DBPREFIX."contactos` ( `idUsuario` , `nombre` , `apellido`, `clave`)";
        $strSQL .= " VALUES ('".$userid."','".$firstname."','".$lastname."','".$clave."')";
        $rs      = $this->dbc->Execute( $strSQL );
		return $userid;
    }
	
	function addpermiso( $userid, $seccionid, $permiso)
    {
        $strSQL  = "INSERT INTO `".DBPREFIX."permisos` ( `usuarioid` , `seccionid` , `permiso`) VALUES (".$userid.",".$seccionid.",".$permiso.")";
        $rs      = $this->dbc->Execute( $strSQL );
    }

    function addCliente( $username, $email, $login, $password, $select, $level, $tipo )
    {
        $strSQL   = "INSERT INTO `".DBPREFIX."usuarios` ( `username` , `password` , `fecha`, `nivel` ) VALUES ('".$login."','".$password."', NOW(),".$level.")";
        $rs       = $this->dbc->Execute( $strSQL );
        $strSQL   = "SELECT MAX( userid ) as id FROM ".DBPREFIX."usuarios";
        $rs       = $this->dbc->Execute( $strSQL );
        $userid   = $rs->fields['id'];
        $strSQL   = "INSERT INTO `".DBPREFIX."clientes` ( `userid` , `nombre` , `email`, `tipo` )";
        $strSQL  .= " VALUES (".$userid.",'".$username."','".$email."',".$tipo.")";
        $rs       = $this->dbc->Execute( $strSQL );
        $strSQL   = "Select idCliente from ".DBPREFIX."clientes where userid=".$userid;
        $rs       = $this->dbc->Execute( $strSQL );
        $idclient = $rs->fields[0];
        $strSQL   = "insert into ".DBPREFIX."contactocliente (`idCliente`,`idContacto`) VALUES(".$idclient.",".$select.")";
        $rs       = $this->dbc->Execute( $strSQL );
    }

    function editUsuario( $userid, $firstname, $lastname, $clave, $login, $password, $select )
    {
        $strSQL  = "UPDATE `".DBPREFIX."usuarios` set `username`='".$login."', `password`='".$password."', `nivel`='".$select."' where userid=".$userid." ";
		$rs      = $this->dbc->Execute( $strSQL );
        $strSQL  = "UPDATE `".DBPREFIX."contactos` set `nombre`='".$firstname."', `apellido`='".$lastname."', `clave`='".$clave."'";
        $strSQL .= ", `email`='".$email."', `telefono`='".$phonenum."', `celular`='".$cellphone."', `nextel`='".$nextel."' where idUsuario=".$userid." ";
        $rs      = $this->dbc->Execute( $strSQL );
    }

    function editCliente( $userid, $username, $email, $login, $password, $select, $selectc, $tipo )
    {
        $strSQL   = "UPDATE `".DBPREFIX."usuarios` set `username`='".$login."', `password`='".$password."', nivel=".$select." where userid=".$userid." ";
        $rs       = $this->dbc->Execute( $strSQL );
        $strSQL   = "Select idCliente from ".DBPREFIX."clientes where userid=".$userid."";
        $rs       = $this->dbc->Execute( $strSQL );
        $idclient = $rs->fields[0];
        $strSQL   = "UPDATE `".DBPREFIX."contactocliente` set idContacto=".$selectc." where idCliente=".$idclient;
        $rs       = $this->dbc->Execute( $strSQL );
        $strSQL   = "UPDATE `".DBPREFIX."clientes` set `nombre`='".$username."'";
        $strSQL  .= ", `email`='".$email."', tipo=".$tipo." where userid=".$userid." ";
        $rs       = $this->dbc->Execute( $strSQL );
    }

    function getSuppadmin( $User )
    {
        $strSQL  = "Select ".DBPREFIX."contactos.idContacto,".DBPREFIX."contactos.Nombre FROM ".DBPREFIX."contactos , ".DBPREFIX."contactocliente,".DBPREFIX."clientes where ".DBPREFIX."contactos.idContacto=".DBPREFIX."contactocliente.idContacto";
        $strSQL .= " and ".DBPREFIX."contactocliente.idCliente=".DBPREFIX."clientes.idCliente and ".DBPREFIX."clientes.idCliente=".$User;
        $rs      = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function selectDinamico( $nivel )
    {

        //Primero obtenemos el perfil del nivel seleccionado

        if ( $nivel != "" )
        {
            $strSQL  = "Select perfil from ".DBPREFIX."niveles where nivel=".$nivel;
            $rs      = $this->dbc->Execute( $strSQL );
            $profile = $rs->fields[0];
        }
        else
        {
            $profile = 3;
        }
        $tmp    = '<select name="selected" >';
        $strSQL = "Select * from ".DBPREFIX."niveles where perfil=".$profile;
        $rs     = $this->dbc->Execute( $strSQL );
        while ( !$rs->EOF )
        {
            $tmp .= "<option value='".$rs->fields[0]."'";
            if ( $rs->fields[0] == $nivel )
            {
                $tmp .= " selected";
            }
            $tmp .= ">".$rs->fields[2].'</option>';
            $rs->MoveNext( );
        }
        return $tmp.'</select>';
    }

	function llenarRoles($idusuario = 0)
    {
        $linea = "";
        $stringcombo = "";
        if ( $idusuario != 0 )
        {
            $strSQL = "Select rolid from ".DBPREFIX."usuarios where userid=".$idusuario;
            $rs     = $this->dbc->Execute( $strSQL );
			$rolactual = $rs->fields[0];
        }
        $strSQL = "Select * from ".DBPREFIX."roles";
        $rs = $this->dbc->Execute( $strSQL );
        if ( !$rs->EOF )
        {
            do
            {
                $rolid = $rs->fields[0];
                $rolname = $rs->fields[1];
                if ( $rolid == $rolactual )
                {
                    $select = "selected";
                }
                else
                {
                    $select = "";
                }
                $stringcombo .= "<option value='".$rolid."' ".$select." >";
                $stringcombo .= $rolname;
                $stringcombo .= "</option>";
                $rs->MoveNext( );
            }
            while ( !$rs->EOF );
        }
        return $stringcombo;
    }
	
	function buildpermisos($idusuario = 0)
    {
        $stringcode = "";
		$tipo_permisos = array(1=>"Altas", 2=>"Modificaciones", 3=>"Bajas", 4=>"Consultas");
        
        $strSQL = "Select * from ".DBPREFIX."secciones";
        $rs = $this->dbc->Execute( $strSQL );

            do{
				if($rs->fields["seccionid"] < 8)
				{
					$stringcode .= '<strong>'.$rs->fields['nombre'].':</strong><br/>';
					for($i = 1; $i < 5; $i++)
					{
							$strSQL = "Select * from ".DBPREFIX."permisos where usuarioid = ".$idusuario." AND seccionid = ".$rs->fields['seccionid']." AND permiso = ".$i;
							$existe = $this->dbc->Execute( $strSQL );
							
							if ( !$existe->EOF )
								$select = 'checked = "checked"';
							else
								$select = "";
							
							$stringcode .= '<input name="'.$rs->fields['seccion'].'[]" '.$select.' type="checkbox" id="'.$rs->fields['seccion'].'_'.$i.'" value="'.$i.'" /> '.$tipo_permisos[$i].' ';
					}
					$stringcode .= '<br/>';
				
				}elseif($rs->fields["seccionid"] == 8)
				{	
					$stringcode .= '<br/>';
					$tipo_permisos = array(1=>"Servicios por cliente por # econ&oacute;nomico ", 2=>"Acomulado de mantenimientos ", 3=>"Recepci&oacute;n de unidad",
											4=>"Consumo de refacciones ", 5 => "Pr&oacute;ximos mantenimientos preventivos");
					
					$stringcode .= '<strong>'.$rs->fields['nombre'].':</strong><br/>';
					for($i = 1; $i < 6; $i++)
					{
							$strSQL = "Select * from ".DBPREFIX."permisos where usuarioid = ".$idusuario." AND seccionid = ".$rs->fields['seccionid']." AND permiso = ".$i;
							$existe = $this->dbc->Execute( $strSQL );
							
							if ( !$existe->EOF )
								$select = 'checked = "checked"';
							else
								$select = "";
							
							$stringcode .= '<input name="'.$rs->fields['seccion'].'[]" '.$select.' type="checkbox" id="'.$rs->fields['seccion'].'_'.$i.'" value="'.$i.'" /> '.$tipo_permisos[$i].' <br/>';
					}
					$stringcode .= '<br/>';
				}elseif($rs->fields["seccionid"] == 9)
					{	
					$stringcode .= '<br/>';
					$tipo_permisos = array(1=>"Valor del IVA", 2=>"Unidades de medida  ");
					
					$stringcode .= '<strong>'.$rs->fields['nombre'].':</strong><br/>';
					for($i = 1; $i < 3; $i++)
					{
							$strSQL = "Select * from ".DBPREFIX."permisos where usuarioid = ".$idusuario." AND seccionid = ".$rs->fields['seccionid']." AND permiso = ".$i;
							$existe = $this->dbc->Execute( $strSQL );
							
							if ( !$existe->EOF )
								$select = 'checked = "checked"';
							else
								$select = "";
							
							$stringcode .= '<input name="'.$rs->fields['seccion'].'[]" '.$select.' type="checkbox" id="'.$rs->fields['seccion'].'_'.$i.'" value="'.$i.'" /> '.$tipo_permisos[$i].' <br/>';
					}$stringcode .= '<br/><br/>';
				}
                $rs->MoveNext( );
            }while ( !$rs->EOF );
        
        return $stringcode;
    }
	
    function selectAdminsoport( $userid )
    {
        $tmp = '<select name="selectedc" >';
        if ( $userid != "" )
        {
            $strSQL  = "Select ".DBPREFIX."contactos.idContacto from ".DBPREFIX."contactos,".DBPREFIX."contactocliente,".DBPREFIX."clientes where ".DBPREFIX."contactos.idContacto=".DBPREFIX."contactocliente.idContacto and ";
            $strSQL .= DBPREFIX."clientes.idCliente=".DBPREFIX."contactocliente.idCliente and ".DBPREFIX."clientes.userid=".$userid;
            $rs      = $this->dbc->Execute( $strSQL );
            $default = $rs->fields[0];
        }
        else
        {
            $default = 0;
        }
        $strSQL  = "Select idContacto, ".DBPREFIX."contactos.Nombre, ".DBPREFIX."niveles.nombre from ".DBPREFIX."contactos,".DBPREFIX."usuarios,".DBPREFIX."niveles where ".DBPREFIX."contactos.idUsuario=".DBPREFIX."usuarios.userid and ".DBPREFIX."usuarios.nivel=".DBPREFIX."niveles.nivel and";
        $strSQL .= " ".DBPREFIX."niveles.perfil=2";
        $rs      = $this->dbc->Execute( $strSQL );
        while ( !$rs->EOF )
        {
            $tmp .= "<option value='".$rs->fields[0]."'";
            if ( $rs->fields[0] == $default )
            {
                $tmp .= "selected";
            }
            $tmp .= ">".$rs->fields[1]." -- ".$rs->fields[2].'</option>';
            $rs->MoveNext( );
        }
        return $tmp.'</select>';
    }

    function showAdminname( $userid )
    {
        $strSQL = "Select Nombre from ".DBPREFIX."contactos,".DBPREFIX."usuarios where idUsuario=userid and userid=".$userid;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getAdmincontact( $userid )
    {
        $strSQL = "Select idContacto from ".DBPREFIX."contactos,".DBPREFIX."usuarios where idUsuario=userid and userid=".$userid;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getLeveladselect( $level )
    {
        $strSQL = "Select * from ".DBPREFIX."niveles where perfil=2";
        $rs = $this->dbc->Execute( $strSQL );
        while ( $rs->EOF )
        {
            $combodata .= "<option value='".$rs->fields[0]."'";
            if ( $rs->fields[0] == $level )
            {
                $combodata .= " selected";
            }
            $combodata .= ">".$rs->fields[2]."</option>";
        }
        return $combodata;
    }

    function getUsername( $usuario )
    {
        $strSQL  = "select ".DBPREFIX."usuarios.* from ".DBPREFIX."usuarios where userid=".$usuario." ";
        $rs      = $this->dbc->Execute( $strSQL );
        $strSQL  = "Select perfil from ".DBPREFIX."niveles where nivel=".$rs->fields[3];
        $rs      = $this->dbc->Execute( $strSQL );
        $profile = $rs->fields[0];
        if ( $profile < 3 )
        {
            $strSQL = "select ".DBPREFIX."usuarios.*,".DBPREFIX."contactos.* from ".DBPREFIX."usuarios, ".DBPREFIX."contactos where ".DBPREFIX."usuarios.userid=".DBPREFIX."contactos.idUsuario and userid=".$usuario;
        }
        else
        {
            $strSQL = "select ".DBPREFIX."usuarios.*,".DBPREFIX."clientes.* from ".DBPREFIX."usuarios,".DBPREFIX."clientes where ".DBPREFIX."usuarios.userid=".DBPREFIX."clientes.userid and ".DBPREFIX."usuarios.userid=".$usuario;
        }
        $rs = $this->dbc->Execute( $strSQL );
        if ( $profile < 3 )
        {
            return $rs->fields[8];
        }
        else
        {
            return $rs->fields[7];
        }
    }

    function getshipping( $idclient )
    {
        $strSQL = "Select * from ".DBPREFIX."consignacion where idcliente=".$idclient." order by consignatario";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function getshippingdata( $id )
    {
        $strSQL = "Select * from ".DBPREFIX."consignacion where folio=".$id;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function getpaymentdata( $id )
    {
        $strSQL = "Select * from ".DBPREFIX."facturacion where folio=".$id;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function getclientname( $idclient )
    {
        $strSQL = "Select nombre from ".DBPREFIX."clientes where idCliente=".$idclient;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getClientId( $iduser )
    {
        $strSQL = "Select idcliente from ".DBPREFIX."clientes where userid = ".$iduser;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    function getpayment( $idclient )
    {
        $strSQL = "Select * from ".DBPREFIX."facturacion where idcliente=".$idclient." order by razonsocial";
        $rs = $this->dbc->Execute( $strSQL );
        return $rs;
    }

    function addShipping( $idclient, $consig, $empresa, $direccion1, $direccion2, $ciudad, $estado, $codigopostal, $telefono, $contactof )
    {
        $strSQL = "Insert into ".DBPREFIX."consignacion(idcliente,consignatario,nombreemp,direccion1,direccion2,ciudad,estado,codigopostal,telefono,contactoform) VALUES(".$idclient.",'".$consig."','".$empresa."','".$direccion1."','".$direccion2."','".$ciudad."','".$estado."','".$codigopostal."','".$telefono."','".$contactof."')";
        $rs = $this->dbc->Execute( $strSQL );
    }

    function editShipping( $folio, $consig, $empresa, $direccion1, $direccion2, $ciudad, $estado, $codigopostal, $telefono, $contactof )
    {
        $strSQL = "Update ".DBPREFIX."consignacion set consignatario='".$consig."',nombreemp='".$empresa."',direccion1='".$direccion1."',direccion2='".$direccion2."',ciudad='".$ciudad."',estado='".$estado."',codigopostal='".$codigopostal."',telefono='".$telefono."',contactoform='".$contactof."' where folio=".$folio;
        $rs = $this->dbc->Execute( $strSQL );
    }

    function dropShipping( $folio )
    {
        $strSQL = "Delete from ".DBPREFIX."consignacion where folio=".$folio;
        $rs = $this->dbc->Execute( $strSQL );
    }

    function addPayment( $idclient, $razonso, $rfc, $direccion, $ciudad, $estado, $zipcode )
    {
        $strSQL = "Insert into ".DBPREFIX."facturacion(idcliente,razonsocial,rfc,direccion,ciudad,estado,codigopostal) VALUES(".$idclient.",'".$razonso."','".$rfc."','".$direccion."','".$ciudad."','".$estado."','".$zipcode."')";
        $rs = $this->dbc->Execute( $strSQL );
    }

    function editPayment( $folio, $razonso, $rfc, $direccion, $ciudad, $estado, $zipcode )
    {
        $strSQL = "Update ".DBPREFIX."facturacion set razonsocial='".$razonso."',rfc='".$rfc."',direccion='".$direccion."',ciudad='".$ciudad."',estado='".$estado."',codigopostal='".$zipcode."' where folio=".$folio;
        $rs = $this->dbc->Execute( $strSQL );
    }

    function dropPayment( $id )
    {
        $strSQL = "Delete from ".DBPREFIX."facturacion where folio=".$id;
        $rs = $this->dbc->Execute( $strSQL );
    }

    function dropShipandPay( $idclient )
    {
        $strSQL = "Delete from ".DBPREFIX."consignacion where idcliente=".$idclient;
        $rs     = $this->dbc->Execute( $strSQL );
        $strSQL = "Delete from ".DBPREFIX."facturacion where idcliente=".$idclient;
        $rs     = $this->dbc->Execute( $strSQL );
    }

    function llenarcomboPais( $idpais )
    {
        $text   = "";
        $strSQL = "Select * from ".DBPREFIX."paises";
        $rs     = $this->dbc->Execute( $strSQL );
        while ( !$rs->EOF )
        {
            $text .= "<option value='".$rs->fields[0]."' ";
            if ( $rs->fields[0] == $idpais )
            {
                $text .= "selected";
            }
            $text .= ">".$rs->fields[1]."</option>";
            $rs->MoveNext( );
        }
        return $text;
    }

    function getpaisname( $idpais )
    {
        $strSQL = "Select nombre from ".DBPREFIX."paises where pais=".$idpais;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
    }

    // FUNCIONES DE USUARIO

    function cuentaReg( $table )
    {
        $strSQL = "Select COUNT(*) from ".DBPREFIX.$table;
        $rs = $this->dbc->Execute( $strSQL );
        return $rs->fields[0];
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
}
?>