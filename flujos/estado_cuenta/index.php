<?php
	session_start();
	require_once("../../lib/php/mnu_toolbar.php"); 
	
	$cnn = new conexion();	
	$u_id = $_SESSION["idusuario"];  
	$no_empleado = $_SESSION["idusuario"];	
	$url = "";	
	$parametros = "";
	
	function conviertefecha($fecha){	
		$fechaArr = explode("/",$fecha);		
		if(count($fechaArr) == 1){
			$fecha = $fecha;
		}else{
			$fecha = $fechaArr["2"]."-".$fechaArr["1"]."-".$fechaArr["0"];			
		}
		return $fecha;		
	}	
		
	if(isset($_POST["guardar"])){		
		if($con = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
			mysql_select_db($MYSQL_DATABASE);
		else
			die("Error de conexxion.");
		$cargos = preg_replace("/\|/"," OR idamex = ",$_POST["cargosEnviar"]);
		$sql = "UPDATE amex SET estatus = 2 WHERE idamex = $cargos";
		mysql_query($sql);
		mysql_close($con);
	}
	
	if(isset($_REQUEST["buscar"])){
		if(isset($_REQUEST["tarjeta"]) && !empty($_REQUEST["tarjeta"])){
			$parametros.= " AND tarjeta LIKE '%".$_REQUEST["tarjeta"]."%' ";
			$url.= "&tarjeta=".$_REQUEST["tarjeta"];
		}		
		if(isset($_REQUEST["finicial"]) && !empty($_REQUEST["finicial"])){
			$fecha = conviertefecha($_REQUEST["finicial"]);
			$parametros.= " AND fecha_cargo >= '$fecha' ";
			$url.= "&finicial=".$fecha;
		}
		if(isset($_REQUEST["ffinal"]) && !empty($_REQUEST["ffinal"])){
			$fecha = conviertefecha($_REQUEST["ffinal"]);
			$parametros.= " AND fecha_cargo <= '$fecha' ";
			$url.= "&ffinal=".$fecha;
		}	
	}	
	
	if(isset($_REQUEST["type"])){
		$url.= "&type=".$_REQUEST["type"];
		$condicioJoin = ($_REQUEST["type"] == 1) ? " notarjetacredito = tarjeta " : " notarjetacredito_gas = tarjeta " ;			
	}else{
		$condicioJoin = " notarjetacredito = tarjeta OR notarjetacredito_gas = tarjeta ";		
	}	
	
	$I  = new Interfaz("",true);	
	estado_cuenta($no_empleado);
	echo "<script src='script.js'></script>";
	$L	= new Lista("buscar=true$url");		
	$L->Cabeceras("Nombre");
	$L->Cabeceras("Tarjeta");
	$L->Cabeceras("Cargo");        
	$L->Cabeceras("Fecha");
	$L->Cabeceras("Monto");
	$L->Cabeceras("Estado");
	$L->Cabeceras("<input type='checkbox' name='cargos' onclick='seleccionarCargos();' id='cargos'>Cancelar");
	
	echo "<h2 align='left'>Cargos de tarjetas de gastos</h2>";		
	$sql = "SELECT dc_idamex, et_etapa_nombre
				FROM detalle_comprobacion, comprobaciones, tramites, etapas
				WHERE dc_comprobacion = co_id
				AND co_mi_tramite = t_id
				AND t_flujo = et_flujo_id
				AND t_etapa_actual = et_etapa_id
				AND dc_idamex != 0
				AND (et_etapa_nombre = 'Sin Enviar' OR et_etapa_nombre = 'Rechazada por Director' OR et_etapa_nombre = 'Rechazada')
				GROUP BY dc_idamex
			
			UNION ALL
			
			SELECT dc_idamex_comprobado as dc_idamex, et_etapa_nombre
				FROM detalle_comprobacion_gastos, comprobacion_gastos, tramites, etapas
				WHERE dc_comprobacion = co_id
				AND co_mi_tramite = t_id
				AND t_flujo = et_flujo_id
				AND t_etapa_actual = et_etapa_id
				AND dc_idamex_comprobado != 0
				AND (et_etapa_nombre = 'Sin Enviar' OR et_etapa_nombre = 'Rechazada por Director' OR et_etapa_nombre = 'Rechazada')
				GROUP BY dc_idamex_comprobado";
	$res = mysql_query($sql);
	$array = array();
	$cont = 0;
	$var = "";
	while($row = mysql_fetch_assoc($res)){
		$array[$cont] = array($row["dc_idamex"],$row["et_etapa_nombre"]);
		$var.= " OR idamex = ".$row["dc_idamex"];
		$cont++;
	}
	
	$sql = "SELECT nombre, tarjeta, concepto, DATE_FORMAT(fecha_cargo,'%d/%m/%Y') as fecha_cargo, 
					CONCAT(CONVERT(FORMAT(montoAmex,2),CHAR(20)),' ',monedaAmex) as monto, idamex,
					IF((amex.estatus = 0 AND amex.comprobacion_id = 0),'PENDIENTE', 'SIN ENVIAR') AS estado
			FROM amex
			LEFT JOIN empleado ON $condicioJoin
			WHERE (
				(amex.estatus = 0 AND amex.comprobacion_id=0)
				OR 
				(
					(amex.estatus = 0 AND amex.comprobacion_id > 0) 
					OR 
					(amex.estatus > 0 AND amex.comprobacion_id = 0 AND amex.estatus != 2) 
					$var
				)
			)
			$parametros
			GROUP BY idamex
			ORDER BY amex.fecha_cargo ASC";
	
	$res = mysql_query($sql);
	$array = array();
	$cont = 0;
	while($row = mysql_fetch_assoc($res)){
		if($row["estado"] == "SIN ENVIAR")
			$row["idamex"] = "<p align=center><a><input type=checkbox name=cargo".$row["idamex"]." class=cargo id=cargo".$row["idamex"]." value=".$row["idamex"]." onclick=verificaChecks(this.value,this.id); disabled></a></p>";
		else
			$row["idamex"] = "<p align=center><a><input type=checkbox name=cargo".$row["idamex"]." class=cargo id=cargo".$row["idamex"]." value=".$row["idamex"]." onclick=verificaChecks(this.value,this.id); ></a></p>";
			
		$array[$cont] = array($row["nombre"],$row["tarjeta"],$row["concepto"],$row["fecha_cargo"], $row["monto"], $row["estado"], $row["idamex"]);
		$cont++;
	}
	
	//print_r($array);
	
	$L->muestra_lista2($array, 0, false, -1, "", 10, "edoCuenta");				
	?>
	<div align="right">
		<input type="hidden" name="cargosEnviar" id="cargosEnviar">
		<br/>
		<input type="submit" name="guardar" id="guardar" style="display:none;" value="Guardar Cambios" onclick="return procesaCargos();">
	</div>
	</form>
	</center>	
	<?PHP	
	$I->Footer();
	?>