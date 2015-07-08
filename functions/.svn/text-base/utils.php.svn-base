<?php 
function __autoload($class_name) {
		global $RUTA_A;
		require_once $RUTA_A ."/functions/" . $class_name . '.php';
}

/**
 * LLenamos un combo desde una consulta
 *
 * @param text $query
 * @param text $nombre
 * @param integer $id_select
 * @param text $extra
 */
	function llena_combo_consulta($query,$nombre,$id_select="",$extra="",$activo=""){
			//en la consulta el indice debe de ser la columna 0 y la descripcion la columna 1 :)		
			$cn = new conexion();
			$rs = $cn->consultar($query);
			$filas=$cn->get_rows();
			?>
				<select name="<?php echo $nombre; ?>" id="<?php echo $nombre; ?>" <?php echo $extra;?>>
			<?php
				if(trim($id_select)==""){
					?><option value="">Seleccione....</option><?php
				}
				for($a=0; $a<$filas; $a++){
					?><option value="<?php echo mysql_result($rs,$a,0);?>" <?php if(mysql_result($rs,$a,1)==$id_select) echo "selected"; ?>>  <?php echo mysql_result($rs,$a,1)."-".mysql_result($rs,$a,2);?>   </option> <?php
				}
			?>
				</select>
			<?php
			if($activo==""){
			?>				
				$<input type="text" name="porcent" id="porcent" value="0" style="width:45px"  onkeypress="return validaNum (event)"/>
				<input name="addConcepto" type="button" id="addConcepto" value="    Agregar"  onclick="construyePartidaCeco();" style="background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
				

				<table id="cecos_table" class="tablesorter" cellspacing="1"> 
					<thead> 
						<tr> 
							<th width="2%">No.</th> 
							<th width="15%">CeCo</th>
						    <th width="8%">Monto asignado</th>
							<th width="9%">Herramientas</th>
						</tr>						
						</thead>
						<tbody>
						<!-- cuerpo tabla-->
					</tbody>						
				</table>				
			<?php
			}
	}
	function llena_combo_vector($nombre,$array,$id_select="",$extra=""){
			?>
			<select name="<?php echo $nombre;?>" <?php  echo $extra;?> id="<?php echo $nombre;?>" >
			<?php
			if(trim($id_select)==""){
				
			}
			foreach ($array as $key=>$valor){
				?>
					<option value="<?php echo $key; ?>" <?php if($key==$id_select && trim($id_select)!=""){ echo "selected";} ?>> <?php echo $valor;?>
					</option>
				<?php
			}
			?>
				</select>
			<?php
	}
	
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = ""){
		  $theValue	= trim($theValue);
		  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
		  
		  switch ($theType) {
			case "text":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;
			case "long":
			case "int":
			  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
			  break;
			case "double":
			  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			  break;
			case "date":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;
			case "defined":
			  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			  break;
		  }
		  return $theValue;
	}
	
	function Pinta_Calendario($nombre,$formato="%Y-%m-%d",$size="15"){
		global $RUTA_R;
		$text=$nombre;
		$link=$nombre."_link";
		?>
		<input type="text" name="<?php echo $text; ?>" id="<?php echo $text; ?>" readonly="readonly" size="<?php echo $size; ?>">  <a href="#"  id="<?php echo $link; ?>"> <img src="<?php echo $RUTA_R ?>imgs/cal.png" border="0"> </a>
											
					<script type="text/javascript">
							Calendar.setup({
														inputField     :    "<?php echo $text; ?>",      // id of the input field
														ifFormat       :    "<?php echo $formato; ?>",       // format of the input field
														showsTime      :    true,            // will display a time selector
														button         :    "<?php echo $link; ?>",   // trigger for the calendar (button ID)
														singleClick    :    true,           // double-click mode
														step           :    1                // show all years in drop-down boxes (instead of every other year as default)
							});
					</script>
		<?php
	}
	
	/**
	 * Generamos un xml sencillo a partir de una consulta
	 * la consulta debe arrojar un id(columna 0) y un nombre (columna 1)
	 * 
	 *
	 * @param text $query
	 * @param text $encabezado
	 * @return XML
	 */
	
	
	function Genera_xml_consulta($query,$encabezado){
		
		$cnn 	 = new conexion();
		$aux	 = $cnn->consultar($query);
		$xml	 = "<?xml version=\"1.0\"?>";
		
		$xml.="<Datos>";
		while($fila = mysql_fetch_array($aux)){
			$xml.="<" . $encabezado . ">";
				$xml.="<id>" 		. $fila[0] . "</id>";
				$xml.="<nombre>" 	. $fila[1] . "</nombre>";
			$xml.="</" . $encabezado . ">";
		}
		$xml.="</Datos>";
		return($xml);
	}
	
//convierte una fecha de dd/mm/aaaa a aaaa-mm-dd
function db_date($date){
  $toks=explode("/",$date);
  if(count($toks)!=3){
    return "";
  }
  return $toks[0]."-".$toks[1]."-".$toks[2];
}

function cambiarFormatoFecha($fecha){
	$toks=explode(" ",$fecha);
    list($anio,$mes,$dia)=explode("-",$toks[0]);
    return $dia."/".$mes."/".$anio;
}  

function fecha_to_mysql($fecha){
	$pieces = explode("/", $fecha);
	return $pieces[2]."-".$pieces[1]."-".$pieces[0];
}

function darFormatoFecha($fecha){
	$toks=explode(" ",$fecha);
	list($dia,$mes,$anio)=explode("-",$toks[0]);
	return $anio."/".$mes."/".$dia;
}

function agregarDelegadoRuta($titular, $delegado, $rutaAutorizacion){
	$rutaconDelegados = $rutaAutorizacion;
	if(!empty($delegado)){
		// Extraeremos los valores donde se encuentre |
		$ruta_tramite = explode("|", $rutaAutorizacion);
		$nuevaRuta = array();
		$new_authorizer = "";
		
		// Recorreremosla ruta, buscando el autorizador para remeplazarlo
		for($i=0;$i<count($ruta_tramite);$i++){
			error_log("Elemento: ".$i.": ".$ruta_tramite[$i]."<br />");
			error_log("Encontrado: ".strcmp($ruta_tramite[$i], $titular)."<br />");
			if(strcmp($ruta_tramite[$i], $titular) == 0){
				$new_authorizer = $delegado."*".$titular;
				$nuevaRuta[$i]= $new_authorizer;
			}else{
				$nuevaRuta[$i]= $ruta_tramite[$i];
			}
		}
		$rutaconDelegados = implode("|", $nuevaRuta);
	}
	return $rutaconDelegados;
}

function verificaSesion($titular, $delegado){
	$user = 0;
	if($delegado != 0 || $delegado != ""){
		$user = $delegado;
	}else{
		$user = $titular;
	}
	return $user;
}

function encuentraAutorizador($ruta, $iduser){
	$encontradoEnRuta = 0;
	$rutaGenerada = explode("|", $ruta);
	
	// Recorreremos la ruta, buscando el autorizador para reemplazarlo
	for($i=0;$i<count($rutaGenerada);$i++){
		if($rutaGenerada[$i] == $iduser){
			$encontradoEnRuta = 1;
			error_log("Encontrado....".$rutaGenerada[$i]);
			break;
		}
	}
	return $encontradoEnRuta;
}
?>