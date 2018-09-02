<?
class Conectar{
function Conectar (){
	$idConexion=mysql_connect("localhost","root","") or die(mysql_error);
	//$idConexion=mysql_connect("216.247.255.142","user1262232","ynbvfw8cfs") or die(mysql_error);
	mysql_select_db("dbfisher",$idConexion);
//		mysql_select_db("fisher_yongercommx",$idConexion);
	$this->idConexion= $idConexion;
}

function verTodos($tabla,$campo,$identificador){
		$strSQL="select * from " . $tabla. " limit " .$this->inicio . "," . $this->TAMANO_PAGINA; 
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		while($liga_row=mysql_fetch_array($this-> resultado)){
			 echo  '<tr><td align="right"><input name="extras[]" type="checkbox" id="idContacto" value="' .$liga_row[$identificador] . '"></td><td >'.$liga_row[$campo] . "</td></tr>";
		}
		echo "<tr><td>";
		if ($this->total_paginas > 1){
	echo "<span class='Estilo2'><b>ver :</b></span> ";
    for ($i=1;$i<=$this->total_paginas;$i++){
       if ($this->pagina == $i)
      
          echo $this->pagina . " ";
       else
         
          echo "<span class='Estilo2'><a href='".$archivo."?pagina=" . $i . $txt_criterio . "'><u>" . $i . "</u></a>" . " " . "</span>";
    }
}
echo "</tr></td>";
		
  
}
function seleccionar($tabla,$campo,$identificador,$URL){

		$strSQL="select * from " . $tabla. " limit " .$this->inicio . "," . $this->TAMANO_PAGINA; 
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		while($liga_row=mysql_fetch_array($this-> resultado)){
			 echo  '<tr><td align="center"><a href="'.$URL.'?'.$identificador.'='.$liga_row[$identificador].'">'.$liga_row[$campo] . "</a></td></tr>";
		}
echo "<tr><td>";
		if ($this->total_paginas > 1){
	echo "<span class='Estilo2'><b>ver :</b></span> ";
    for ($i=1;$i<=$this->total_paginas;$i++){
       if ($this->pagina == $i)
      
          echo $this->pagina . " ";
       else
         
          echo "<span class='Estilo2'><a href='".$archivo."?pagina=" . $i . $txt_criterio . "'><u>" . $i . "</u></a>" . " " . "</span>";
    }
}
echo "</tr></td>";
		
  
}
function insertarNoticia($titulo,$contenido,$fecha,$categoria,$portada,$foto1,$foto2,$foto3){
		
		$strSQL="INSERT INTO `tblnoticias` ( `idNoticia` , `titulo` , `contenido` , `fecha` , `foto` , `categoria` , `portada`, `foto1`,`foto2`,`foto3`) VALUES ('', '".$titulo."', '".$contenido."', '".$fecha."', '', '".$categoria."', '".$portada."', '".$foto1."', '".$foto2."', '".$foto3."')";
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		
		
		
  
}

function insertarTip($titulo,$contenido,$categoria,$vigencia){
$strSQL="INSERT INTO `tblarticulos` ( `idArticulo` , `titulo` , `contenido` , `categoria` , `vigencia` ) VALUES ( '', '".$titulo."', '".$contenido."', ".$categoria.", '".$vigencia."')";
$this-> resultado=mysql_query($strSQL,$this->idConexion);
}

function insertarTienda($nombre,$direccion,$estado,$ciudad,$telefono){
$strSQL="INSERT INTO `tbltiendas` ( `idTienda` , `nombre` , `direccion` , `estado` , `ciudad` , `telefono` ) VALUES ('', '".$nombre."', '".$direccion."', '".$estado."', '".$ciudad."', '".$telefono."')";
$this-> resultado=mysql_query($strSQL,$this->idConexion);
}

function restaurarPortadas(){
	$strSQL="UPDATE tblnoticias SET portada=0 where portada=1";
	$this-> resultado=mysql_query($strSQL,$this->idConexion);
}

function borrar($tabla,$idCampo,$registro){
	$strSQL="delete from " .  $tabla . " where " .$idCampo . "=" . $registro;
	$this-> resultado=mysql_query($strSQL,$this->idConexion);

}

function QueryNoticia($criterio){
		$strSQL="select * from tblnoticias where idNoticia=" . $criterio;
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		$resultados_row = mysql_fetch_row($this-> resultado);
		return $resultados_row;
		
		
}
function QueryProducto($criterio){
		$strSQL="select * from tempProductos where tidProducto=" . $criterio;
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		$resultados_row = mysql_fetch_row($this-> resultado);
		return $resultados_row;
		
		
}
function QueryTienda($criterio){
		$strSQL="select * from tbltiendas where idTienda=" . $criterio;
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		$resultados_row = mysql_fetch_row($this-> resultado);
		return $resultados_row;
		
		
}
function QueryArticulo($criterio){
		$strSQL="select * from tblarticulos where idArticulo=" . $criterio;
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		$resultados_row = mysql_fetch_row($this-> resultado);
		return $resultados_row;
		
		
}

function updateNoticia($titulo,$contenido,$fecha,$categoria,$portada,$fuePortada,$registro,$foto1,$foto2,$foto3){
	if($fuePortada==50){// si la noticia q modificamos fue portada, agregamos normalmente
		$strSQL="UPDATE `tblnoticias` SET `titulo` = '".$titulo."',`contenido` = '".$contenido."',`fecha` = '".$fecha."',`portada` = '".$portada."',`foto1` = '".$foto1."',`foto2` = '".$foto2."',`foto3` = '".$foto3."' WHERE `idNoticia` = ".$registro;
	$this-> resultado=mysql_query($strSQL,$this->idConexion);
	}
	else{ // si no fue portada
		if ($portada==50){ // si la queremos volver portada, restauramos todas
//		$this->restaurarPortadas();
		$strSQL="UPDATE `tblnoticias` SET `titulo` = '".$titulo."',`contenido` = '".$contenido."',`fecha` = '".$fecha."',`portada` = '".$portada."',`foto1` = '".$foto1."',`foto2` = '".$foto2."',`foto3` = '".$foto3."' WHERE `idNoticia` = ".$registro;
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		
		
		}
		else{//si no la queremos como portada, agregamos normalmente
		$strSQL="UPDATE `tblnoticias` SET `titulo` = '".$titulo."',`contenido` = '".$contenido."',`fecha` = '".$fecha."',`portada` = '".$portada."',`foto1` = '".$foto1."',`foto2` = '".$foto2."',`foto3` = '".$foto3."' WHERE `idNoticia` = ".$registro;
		$this-> resultado=mysql_query($strSQL,$this->idConexion);
		
		}
	
	}

}
function updateTip($titulo,$contenido,$categoria,$vigencia,$registro){
$strSQL="UPDATE `tblarticulos` SET `titulo` = '".$titulo."',`contenido` = '".$contenido."',`categoria` = '".$categoria."',`vigencia` = '".$vigencia."' WHERE `idArticulo` = ".$registro;
$this-> resultado=mysql_query($strSQL,$this->idConexion);
}

function updateTienda($nombre,$direccion,$estado,$ciudad,$telefono,$registro){
$strSQL="UPDATE `tbltiendas` SET `nombre` = ' ".$nombre."',`direccion` = '".$direccion."',`estado` = '".$estado."',`ciudad` = '".$ciudad."',`telefono` = '".$telefono."' WHERE `idTienda` = ".$registro;
$this-> resultado=mysql_query($strSQL,$this->idConexion);

}

function updateProducto($modelo,$img1,$linea,$id){
$strSQL2="UPDATE `tempProductos` SET `tmodelo` = '".$modelo."',`tfoto` = '".$img1."', `tlinea` = '".$linea."' WHERE `tidProducto` = '".$id."'";
//echo $strSQL2;
$this-> resultado=mysql_query($strSQL2,$this->idConexion);

}

function consultaPaginacion($tabla,$archivo){
$this->TAMANO_PAGINA = 20;
$this->pagina = $_GET["pagina"];
if (!$this->pagina) {
    	$this->inicio = 0;
	    $this->pagina=1;
	}
	else {
    	$this->inicio = ($this->pagina - 1) * $this->TAMANO_PAGINA;
	}

$strSQL="select * from " . $tabla;
$this-> resultado=mysql_query($strSQL,$this->idConexion);
$this->num_total_registros = mysql_num_rows($this-> resultado);
$this->total_paginas = ceil($this->num_total_registros / $this->TAMANO_PAGINA);


}
function insertarProd($modelo,$foto,$linea){
$strSQL="INSERT INTO `tempProductos` ( `tidProducto` , `tmodelo` , `tfoto` , `tlinea` ) VALUES ('', '".$modelo."', '".$foto."', '".$linea."')";
$this-> resultado=mysql_query($strSQL,$this->idConexion);

}
}


class Seguridad{
var $param;
var $param2;
function Seguridad($pagina,$valor){
$this->param=$pagina;
$this->param2=$valor;

}
}
?>