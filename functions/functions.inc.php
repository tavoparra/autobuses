<?php
 //Esta libreria es la mas importante, ya que contiene todas las funciones de la base de datos
 class DBFunciones{
 
        //Funcion utilizada para conectar la base de datos
      	function DBFunciones ($dist)
		{		
			require($dist.'config/adodb/adodb.inc.php');
			require($dist.'config/dbconfig.php');		
			$db = &ADONewConnection(CONTROLER); # eg 'mysql' o 'postgres'
			//$db->debug = true;
			if(CONTROLER=='mysql')
			{
			  $db->Connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);
			}
			elseif(CONTROLER=='access')
			{
			  $db->PConnect(DBNAME);
			}		
			$this->dbc= $db;
	    }
		
		function selectqry($str,$campos='*',$where='1',$mode=0,$tamano=0,$pag=0)
		{
			$str1=str_replace(" ","",$str);
			$str2=DBPREFIX.$str1;
			$tabla=str_replace(",",",".DBPREFIX,$str2);		  
			$strSQL="Select ".$campos." from ".$tabla." where ".$where;
			if($tamano!=0 && $pag!=0)
			{
				$reg1=($pag-1)*$tamano;
				$rs=$this->dbc->SelectLimit($strSQL,$tamano,$reg1);
			}
			else
			{
				$rs=$this->dbc->Execute($strSQL);		
			}
			if($mode==0)
			{
				return $rs;
			}
			else
			{
				$fld=$rs->Fetchfield(0);
				return $rs->fields[$fld->name];
			}		
		}
		
		function Close()
		{
		  $this->dbc->Close();
		}
		
		//Funcion utilizada para verificar si el usuario puede accesar al panel de control
	    function verificarUsuario($usuario,$clave)
		{
			$strSQL = "SELECT * FROM ".DBPREFIX."usuarios,".DBPREFIX."niveles where ".DBPREFIX."usuarios.nivel=".DBPREFIX."niveles.nivel and username='".$usuario."' AND password='".$clave."'";	
			$rs = $this->dbc->Execute($strSQL);

			if(!$rs->EOF)
			{ // si existe el usuario	
				$this->grupo=$rs->fields[3];
				$this->idUsuario=$rs->fields[4];
				$this->level=$rs->fields[7];
				$this->rolusuario=$rs->fields['rolid'];
	            $_SESSION["Logged"]= "TRUE";
	            $_SESSION["idUsuario"]= $this->idUsuario;
				$_SESSION["level"]= $this->level;
				$_SESSION["grupo"]= $this->grupo;
				$_SESSION["rolusuario"]= $this->rolusuario;
				
				$strSQL = "SELECT seccionid, permiso FROM ".DBPREFIX."permisos where usuarioid='".$this->idUsuario."'";	
				$rs2 = $this->dbc->Execute($strSQL);
				
				
				while(!$rs2->EOF && $this->level == 2)
				{
					$_SESSION["permisos"][$rs2->fields["seccionid"]][$rs2->fields["permiso"]] = true;
					$rs2->MoveNext();
				}
				
					echo '<script language="Javascript">location.href="../index.php";</script>'; //lo redirecciona aqui.
			}
			else
			{ // Si no existe entonces envia una alerta y redirecciona al login
				echo '<script language="Javascript">location.href="../login.php?er=1";</script>'; 
			}		   
		}		
		
		function dropshoplogout(){
		  $sid=session_id();
		  $strSQL="Delete from ".DBPREFIX."carttemp where session_id='".$sid."'";
		  $rs=$this->dbc->Execute($strSQL);
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
