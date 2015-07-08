<?php
set_time_limit(300);
require_once("./lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
$ruta = $RUTA_POLIZA;
$rarchivos = $ruta;
$rbackup = $rarchivos."backup/";
$genera = false;
$conexion = new conexion();
$sqlViaje = "SELECT 
						dc_id AS idDetalle,
						(select count(*) from detalle_comprobacion join comprobaciones ON dc_comprobacion = co_id where co_mi_tramite = t_id) as contador,
						CONCAT(u.u_nombre, u_paterno, u_materno) as user,
						cc.cp_concepto as concepto,
						c.co_tramite as t,
						DATE_FORMAT(t.t_fecha_cierre, '%d.%m.%Y') AS fecha,
						'DR' AS tipoDoc,
						e.e_codigo sociedad,
						'MXN' moneda,
						t.t_id AS tramite,
						u.u_usuario AS usuario,
						dc.dc_tipo_comprobacion AS descTipo,
						cc.cp_cuenta AS contable,
						dc.dc_total AS total,
						IF(e.e_codigo = '310' OR e.e_codigo = '315' OR e.e_codigo = '320', 'SERV', 'AGUA') AS division,
						catc.cc_centrocostos as cecos,
						 dc.id_factura as idFactura,
						cc.cp_deducible,
						fact.uuid
					FROM tramites t
					INNER JOIN comprobaciones c ON t.t_id = c.co_mi_tramite
					INNER JOIN detalle_comprobacion dc ON c.co_id = dc.dc_comprobacion
                    LEFT OUTER JOIN factura as fact on fact.id_factura = dc.id_factura
					INNER JOIN cat_cecos catc ON c.co_cc_clave = catc.cc_id
					INNER JOIN cat_conceptos cc ON dc.dc_concepto = cc.cp_id
					INNER JOIN usuario u ON t.t_iniciador = u.u_id
					INNER JOIN empresas e ON u.u_empresa = e.e_codigo
					WHERE t_fecha_cierre > 0 
					AND t_flujo = 3
					AND dc.dc_enviado_sap = 0";
$resViaje = $conexion->consultar($sqlViaje);
$sqlGasto = "SELECT
						dc_id AS idDetalle,
						(select count(*) from detalle_comprobacion_gastos  join comprobacion_gastos on dc_comprobacion = co_id where co_mi_tramite = t_id) as contador,
						CONCAT(u.u_nombre, u_paterno, u_materno) as user,
						cc.cp_concepto as concepto,
						c.co_tramite as t,
						DATE_FORMAT(t.t_fecha_cierre, '%d.%m.%Y') AS fecha,
						'DR' AS tipoDoc, 
						e.e_codigo sociedad, 
						'MXN' moneda, 
						t.t_id AS tramite, 
						u.u_usuario AS usuario, 
						dc.dc_tipo AS descTipo, 
						cc.cp_cuenta AS contable, 
						dc.dc_total AS total,
						IF(e.e_codigo = '310' OR e.e_codigo = '315' OR e.e_codigo = '320', 'SERV', 'AGUA') AS division,
						catc.cc_centrocostos as cecos,
						dc.id_factura as idFactura,
						cc.cp_deducible,
						fact.uuid
						FROM tramites  t 
						INNER JOIN comprobacion_gastos c ON t.t_id = c.co_mi_tramite
						INNER JOIN detalle_comprobacion_gastos dc ON c.co_id = dc.dc_comprobacion
						LEFT OUTER JOIN factura as fact on fact.id_factura = dc.id_factura
						INNER JOIN cat_cecos catc ON c.co_cc_clave = catc.cc_id
						INNER JOIN cat_conceptos cc ON dc.dc_concepto = cc.cp_id
						INNER JOIN usuario u ON t.t_iniciador = u.u_id
						INNER JOIN empresas e ON u.u_empresa = e.e_codigo
						WHERE t_fecha_cierre > 0
						AND t_flujo = 4
						AND dc.dc_enviado_sap = 0";
$resGasto = $conexion->consultar($sqlGasto);
$page_print = "";
$contador = 1;
$sumTotal = false;
$sumTotalG = false;
if(mysql_num_rows($resViaje)){
	$genera = true;
}
if(mysql_num_rows($resGasto)){
	$genera = true;
}
while($rowViaje = mysql_fetch_assoc($resViaje)) {
$detalleViaje[] = $rowViaje['idDetalle'];
$tipo1 = $rowViaje['concepto'];
if($tipo1 == "Impuesto de IVA"){
	$cecos = "";
	$reg = "EI";
	$totEI = $rowViaje['total'];
	$contable = $rowViaje['contable'];
	$var = "40";
}
elseif($tipo1 == "Retenciones IVA"){
	$cecos = "";
	$reg = "RS";
	$totRI = $rowViaje['total'];
	$contable = $rowViaje['contable'];
	$rowViaje['total'] = $rowViaje['total'] * (-1);
		$var = "50";
}
elseif($tipo1 == "Retenciones ISR"){
	$cecos = "";
	$reg = "RI";
	 $totRS = $rowViaje['total'];
	 $contable = $rowViaje['contable'];
	 $rowViaje['total'] = $rowViaje['total'] * (-1);
	 	$var = "50";
}
elseif($tipo1 == "Descuento"){
	$cecos = $rowViaje['cecos'];
	$reg = "D";
	$totD = $rowViaje['total'];
	$rowViaje['total'] = $rowViaje['total'] * (-1);
	$contable = $contableDesc;
	$var = "50";
}
elseif($tipo1 == "IEPS"){
	$cecos = $rowViaje['cecos'];
	$reg = "IE";
	$totIEPS = $rowViaje['total'];
	$contable = $contableDesc;
	$var = "40";
}else{
	$tram = $rowViaje['tramite'];
	$val1 = false;
	$foliof = $rowViaje['uuid'];
	if($tipo1 == 'Gasolina con CFDI XML'){
 	$queryViajeIfIEPS = "SELECT  *
											FROM
												tramites t
													INNER JOIN
												comprobaciones c ON t.t_id = c.co_mi_tramite
													INNER JOIN
												detalle_comprobacion dc ON c.co_id = dc.dc_comprobacion
													INNER JOIN
												cat_cecos catc ON c.co_cc_clave = catc.cc_id
													INNER JOIN
												cat_conceptos cc ON dc.dc_concepto = cc.cp_id
													INNER JOIN
												usuario u ON t.t_iniciador = u.u_id
													INNER JOIN
												empresas e ON u.u_empresa = e.e_codigo
												    INNER JOIN
												factura as fact on fact.id_factura = dc.id_factura    
											WHERE
												t_fecha_cierre > 0 AND t_flujo = 3 AND t.t_id = $tram AND cp_concepto = 'IEPS'
												AND fact.uuid = '$foliof'";
    $resViajeIfIEPS = $conexion->consultar($queryViajeIfIEPS);
		if(mysql_num_rows($resViajeIfIEPS) > 0){
			$idFact = $rowViaje['idFactura'];
		$facturaViaje = "SELECT * FROM factura WHERE id_factura = $idFact";
			$resfacturaViaje = $conexion->consultar($facturaViaje);
			while($rowfactViaje = (mysql_fetch_array($resfacturaViaje))){
				$totalRealGasolina = $rowfactViaje['c_total'];
				$rowViaje['total'] = $totalRealGasolina;
			}
	$sqlViajeTram = $sqlViaje." AND t.t_id = $tram AND fact.uuid = '$foliof'";
			$resViaje2 = $conexion->consultar($sqlViajeTram);
			while($rowTotales=mysql_fetch_array($resViaje2)){
				if($rowTotales['concepto'] != "Gasolina con CFDI XML"){
					$totalesRet += $rowTotales['total'];
				}
			}
			$rowViaje['total']  = $rowViaje['total'] - $totalesRet;
			$sumTotal = true;
			unset($totalesRet);
			$val1 = true;
		}else{
			$rowViaje['total'];
		}	
	}else{
		$rowViaje['total'];
	}
	$rowViaje['total'];
	$cecos = $rowViaje['cecos'];
	$reg = "E";
	$totE = $rowViaje['total'];
	$user = $rowViaje['user'];
	$contable = $rowViaje['contable'];
	$contableDesc = $rowViaje['contable'];
	$var = "40";
}
	$t = $rowViaje['t'];
	if($t == "-1"){
		switch (trim($rowViaje['descTipo'])) {
			case "Comprobacion de AMEX":
				$tipoCom = "comamex";
				break;
			case "Comprobacion de Reembolso":
				$tipoCom = "reembol";
				break;
		}
	}else{
		$tipoCom = "comviaj";
	}
	$id_fact_viaje = $rowViaje['idFactura'];
	if($id_fact_viaje > 0){
			$sqlfacturaViaje = "SELECT * FROM factura WHERE id_factura = $id_fact_viaje";
			$ressqlfacturaViaje = $conexion->consultar($sqlfacturaViaje);
			while($rowsqlfactViaje = (mysql_fetch_array($ressqlfacturaViaje))){
				$uuidViaje = $rowsqlfactViaje['uuid'];
				$rfcViaje = $rowsqlfactViaje['e_rfc'];
			}
	}
	$deducible = $rowViaje['cp_deducible'];
	if($deducible == 0){
		$uuidViaje = '';
		$rfcViaje= '';
	}
	$ttl[] = $rowViaje['total'];
	$page_print .= $rowViaje['fecha'].chr(9).$rowViaje['tipoDoc'].chr(9).$rowViaje['sociedad'].chr(9).$rowViaje['moneda'].chr(9).$rowViaje['tramite'].chr(9).$rowViaje['usuario']." ".$tipoCom.chr(9).$var.chr(9).$contable.chr(9).''.chr(9).abs($rowViaje['total']).chr(9).$rowViaje['division'].chr(9).$cecos.chr(9).$reg.chr(9).$rowViaje['concepto'].chr(9).$uuidViaje.chr(9).$rfcViaje."\n";
	$contador++;
	if ($contador > $rowViaje['contador']){
						$totalViaje = "SELECT
												SUM(if(cc.cp_concepto = 'Retenciones IVA' OR cc.cp_concepto = 'Retenciones ISR' OR cc.cp_concepto = 'Descuento', dc.dc_total*-1,dc.dc_total)) AS total
												FROM tramites t
												INNER JOIN comprobaciones c ON t.t_id = c.co_mi_tramite
												INNER JOIN detalle_comprobacion dc ON c.co_id = dc.dc_comprobacion
												INNER JOIN cat_cecos catc ON c.co_cc_clave = catc.cc_id
												INNER JOIN cat_conceptos cc ON dc.dc_concepto = cc.cp_id
												INNER JOIN usuario u ON t.t_iniciador = u.u_id
												INNER JOIN empresas e ON u.u_empresa = e.e_codigo
												WHERE t_fecha_cierre > 0
												AND t_flujo = 3
												AND t.t_id = ".$rowViaje['tramite']."
												GROUP BY t.t_id ";
						$restotalViaje = $conexion->consultar($totalViaje);
							while($rowtotalViaje = mysql_fetch_assoc($restotalViaje)) {
								$totrow = $rowtotalViaje['total'];
							}
							$OperacionViaje ="SELECT
																				CASE
																					WHEN cc.cp_concepto = 'Impuesto de IVA' THEN '+EI'
																					WHEN cc.cp_concepto = 'Retenciones IVA' THEN '-RS'
																					WHEN cc.cp_concepto = 'Retenciones ISR' THEN '-RI'
																					WHEN cc.cp_concepto = 'Descuento' THEN '-D'
																					WHEN cc.cp_concepto = 'IEPS' THEN '+IE'
																					ELSE '+E'
																				END as operacion
																			FROM tramites t
																			INNER JOIN comprobaciones c ON t.t_id = c.co_mi_tramite
																			INNER JOIN detalle_comprobacion dc ON c.co_id = dc.dc_comprobacion
																			INNER JOIN cat_cecos catc ON c.co_cc_clave = catc.cc_id
																			INNER JOIN cat_conceptos cc ON dc.dc_concepto = cc.cp_id
																			INNER JOIN usuario u ON t.t_iniciador = u.u_id
																			INNER JOIN empresas e ON u.u_empresa = e.e_codigo
																			WHERE t_fecha_cierre > 0
																			AND t_flujo = 3
																			AND t.t_id = ".$rowViaje['tramite'];
						$resOperacion = $conexion->consultar($OperacionViaje);
						$desc = '';
							while($rowOperacion = mysql_fetch_assoc($resOperacion)) {
								$oper = $rowOperacion['operacion'];
								$desc .= $oper;
							}
							if($sumTotal == 1){
								$sumasTotales = array_sum($ttl);
							}else{
								$sumasTotales = $totrow;
							}
						$page_print .= $rowViaje['fecha'].chr(9).$rowViaje['tipoDoc'].chr(9).$rowViaje['sociedad'].chr(9).$rowViaje['moneda'].chr(9).$rowViaje['tramite'].chr(9).$rowViaje['usuario']." emplead".chr(9).'19'.chr(9).$rowViaje['usuario'].chr(9).'2'.chr(9).$sumasTotales.chr(9).$rowViaje['division'].chr(9).''.chr(9).substr($desc,1).chr(9).$user."\n";
						unset($oper);
						unset($ttl);
			$contador = 1;
		}
}
if($detalleViaje > 0){
	$idv = implode(",",$detalleViaje);
	$sql = "UPDATE detalle_comprobacion SET dc_enviado_sap = 1 WHERE dc_id in (".$idv.")";
	$conexion->ejecutar($sql);
}

$contador2 = 1;
while($rowGasto = mysql_fetch_array($resGasto)) {
$detalleGasto[] = $rowGasto['idDetalle'];
$tipo2 = $rowGasto['concepto'];
if($tipo2 == "Impuesto de IVA"){
	$cecos = "";
	$reg = "GI";
	$totEI = $rowGasto['total'];
	$contable = $rowGasto['contable'];
	$var = "40";
}
elseif($tipo2 == "Retenciones IVA"){
	$cecos = "";
	$reg = "RS";
	$totRI = $rowGasto['total'];
	$contable = $rowGasto['contable'];
	$var = "50";
	$rowGasto['total'] = $rowGasto['total'] * (-1);
}
elseif($tipo2 == "Retenciones ISR"){
	$cecos = "";
	$reg = "RI";
	 $totRS = $rowGasto['total'];
	 $contable = $rowGasto['contable'];
	 $var = "50";
	 $rowGasto['total'] = $rowGasto['total'] * (-1);
}
elseif($tipo2 == "Descuento"){
	$cecos = $rowGasto['cecos'];
	$reg = "D";
	$totD = $rowGasto['total'];
	$contable = $contableDesc;
	$var = "50";
	$rowGasto['total'] = $rowGasto['total'] * (-1);
}
elseif($tipo2 == "IEPS"){
	$cecos = $rowGasto['cecos'];
	$reg = "IE";
	$totIEPS = $rowGasto['total'];
	$contable = $contableDesc;
	$var = "40";
}else{
	$tram1 = $rowGasto['tramite'];
	$val2 = false;
	$foliofg = $rowGasto['uuid'];
	if($tipo2 == 'Gasolina con CFDI XML'){
	$queryGastoIfIEPS = "SELECT  *
									FROM
										tramites t
											INNER JOIN
										comprobacion_gastos c ON t.t_id = c.co_mi_tramite
											INNER JOIN
										detalle_comprobacion_gastos dc ON c.co_id = dc.dc_comprobacion
											INNER JOIN
										cat_cecos catc ON c.co_cc_clave = catc.cc_id
											INNER JOIN
										cat_conceptos cc ON dc.dc_concepto = cc.cp_id
											INNER JOIN
										usuario u ON t.t_iniciador = u.u_id
											INNER JOIN
										empresas e ON u.u_empresa = e.e_codigo
										    INNER JOIN
								    	factura as fact on fact.id_factura = dc.id_factura  
									WHERE
										t_fecha_cierre > 0 AND t_flujo = 4 AND t.t_id = $tram1 AND cp_concepto = 'IEPS'
										AND fact.uuid = '$foliofg'";
    $resGastoIfIEPS = $conexion->consultar($queryGastoIfIEPS);
		if(mysql_num_rows($resGastoIfIEPS) > 0){
			$idFactGasto = $rowGasto['idFactura'];
			$facturaGasto = "SELECT * FROM factura WHERE id_factura = $idFactGasto";
			$resfacturaGasto = $conexion->consultar($facturaGasto);
			while($rowfactGasto = (mysql_fetch_array($resfacturaGasto))){
				$totalRealGasolinaGasto = $rowfactGasto['c_total'];
				$rowGasto['total'] = $totalRealGasolinaGasto;
			}
			$sqlGastoTram = $sqlGasto." AND t.t_id = $tram1 AND fact.uuid= '$foliofg'";
			$resGasto2 = $conexion->consultar($sqlGastoTram);
			while($rowTotalesGasto=mysql_fetch_array($resGasto2)){
				if($rowTotalesGasto['concepto'] != "Gasolina con CFDI XML"){
					$totalesRetGasto += $rowTotalesGasto['total'];
				}	
			}
			$rowGasto['total']  = $rowGasto['total'] - $totalesRetGasto;
			$sumTotalG = true;
			unset($totalesRetGasto);
			$val2 = true;
		}else{
			$rowGasto['total'];
		}	
	}else{
		$rowGasto['total'];
	}
	$cecos = $rowGasto['cecos'];
	$reg = "G";
	$totE = $rowGasto['total'];
	$user = $rowGasto['user'];
	$contable = $rowGasto['contable'];
	$contableDesc = $rowGasto['contable'];
	$var = "40";
}
	$t1 = $rowGasto['t'];
	if($t1 == "-1"){
		switch (trim($rowGasto['descTipo'])) {
			case "Comprobacion de AMEX":
				$tipoCom1 = "comamex";
				break;
			case "Comprobacion de Reembolso":
				$tipoCom1 = "reembol";
				break;
		}
	}else{
		$tipoCom1 = "comgast";
	}
	$id_fact_gasto = $rowGasto['idFactura'];
	if($id_fact_gasto > 0){
			$sqlfacturaGasto = "SELECT * FROM factura WHERE id_factura = $id_fact_gasto";
			$ressqlfacturaGasto = $conexion->consultar($sqlfacturaGasto);
			while($rowsqlfactGasto = (mysql_fetch_array($ressqlfacturaGasto))){
				$uuidGasto = $rowsqlfactGasto['uuid'];
				$rfcGasto = $rowsqlfactGasto['e_rfc'];
			}
	}
	$deducible2 = $rowGasto['cp_deducible'];
	if($deducible2 == 0){
		$uuidGasto = '';
		$rfcGasto= '';
	}
	$ttl1[] = $rowGasto['total'];
	$page_print .= $rowGasto['fecha'].chr(9).$rowGasto['tipoDoc'].chr(9).$rowGasto['sociedad'].chr(9).$rowGasto['moneda'].chr(9).$rowGasto['tramite'].chr(9).$rowGasto['usuario']." ".$tipoCom1.chr(9).$var.chr(9).$contable.chr(9).''.chr(9).abs($rowGasto['total']).chr(9).$rowGasto['division'].chr(9).$cecos.chr(9).$reg.chr(9).$rowGasto['concepto'].chr(9).$uuidGasto.chr(9).$rfcGasto."\n";
	 $contador2++;
	if ($contador2 > $rowGasto['contador']){
						$gastototalViaje = "SELECT 
												SUM(if(cc.cp_concepto = 'Retenciones IVA' OR cc.cp_concepto = 'Retenciones ISR' OR cc.cp_concepto = 'Descuento',
													dc.dc_total * - 1,
													dc.dc_total)) AS total
											FROM tramites t
																	INNER JOIN comprobacion_gastos c ON t.t_id = c.co_mi_tramite
																	INNER JOIN detalle_comprobacion_gastos dc ON c.co_id = dc.dc_comprobacion
																	INNER JOIN cat_cecos catc ON c.co_cc_clave = catc.cc_id
																	INNER JOIN cat_conceptos cc ON dc.dc_concepto = cc.cp_id
																	INNER JOIN usuario u ON t.t_iniciador = u.u_id
																	INNER JOIN empresas e ON u.u_empresa = e.e_codigo
																	WHERE t_fecha_cierre > 0
																	AND t_flujo = 4
																	AND t.t_id = ".$rowGasto['tramite']."
																	GROUP BY t.t_id ";
						$gastorestotalViaje = $conexion->consultar($gastototalViaje);
							while($gastorowtotalViaje = mysql_fetch_assoc($gastorestotalViaje)) {
								$gastototrow = $gastorowtotalViaje['total'];
							}
							$gastoOperacionViaje ="SELECT 
																						CASE
																						WHEN cc.cp_concepto = 'Impuesto de IVA' THEN '+GI'
																						WHEN cc.cp_concepto = 'Retenciones IVA' THEN '-RS'
																						WHEN cc.cp_concepto = 'Retenciones ISR' THEN '-RI'
																						WHEN cc.cp_concepto = 'Descuento' THEN '-D'
																						WHEN cc.cp_concepto = 'IEPS' THEN '+IE'
																						ELSE '+G'
																						END as operacion
																						FROM tramites t
																						INNER JOIN comprobacion_gastos c ON t.t_id = c.co_mi_tramite
																						INNER JOIN detalle_comprobacion_gastos dc ON c.co_id = dc.dc_comprobacion
																						INNER JOIN cat_cecos catc ON c.co_cc_clave = catc.cc_id
																						INNER JOIN cat_conceptos cc ON dc.dc_concepto = cc.cp_id
																						INNER JOIN usuario u ON t.t_iniciador = u.u_id
																						INNER JOIN empresas e ON u.u_empresa = e.e_codigo
																						WHERE t_fecha_cierre > 0 
																						AND t_flujo = 4 
																						AND t.t_id = ".$rowGasto['tramite'];
						$gastoresOperacion = $conexion->consultar($gastoOperacionViaje);
						$desc1 = '';
							while($gastorowOperacion = mysql_fetch_assoc($gastoresOperacion)) {
								$oper1 = $gastorowOperacion['operacion'];
								$desc1 .= $oper1;
							}
							if($sumTotalG == 1){
								$sumasTotalesG = array_sum($ttl1);
							}else{
								$sumasTotalesG = $gastototrow;
							}
						$page_print .= $rowGasto['fecha'].chr(9).$rowGasto['tipoDoc'].chr(9).$rowGasto['sociedad'].chr(9).$rowGasto['moneda'].chr(9).$rowGasto['tramite'].chr(9).$rowGasto['usuario']." emplead".chr(9).'19'.chr(9).$rowGasto['usuario'].chr(9).'2'.chr(9).$sumasTotalesG.chr(9).$rowGasto['division'].chr(9).''.chr(9).substr($desc1,1).chr(9).$user."\n";
						unset($oper1);
						unset($ttl1);
			$contador2 = 1;
		}	
}
if($detalleGasto > 0){
	$idg= implode(",",$detalleGasto);
	$sql1 = "UPDATE detalle_comprobacion_gastos SET dc_enviado_sap = 1 WHERE dc_id in (".$idg.")";
	$conexion->ejecutar($sql1);
}
if($genera == true){
	$consecutivo = leer_archivos_y_directorios($rarchivos);
	$myFile = date("Ymd")."_".$consecutivo."_comprobaciones.txt";
	$fh = fopen($ruta.$myFile, 'w');
	$stringData = trim(utf8_encode($page_print));
	fwrite($fh, $stringData);
	fclose($fh);
}

function leer_archivos_y_directorios($ruta) {
    // comprobamos si lo que nos pasan es un direcotrio
	$total = 0;
    if (is_dir($ruta))
    {
        // Abrimos el directorio y comprobamos que
        if ($aux = opendir($ruta))
        {
            while (($archivo = readdir($aux)) !== false)
            {
                if ($archivo!="." && $archivo!="..")
                {
                    $ruta_completa = $ruta . '/' . $archivo;
                    if (is_dir($ruta_completa))
                    {
                    }
                    else
                    {
						$total;
						$total++;
                        //echo '<br />' . $archivo . '<br />';
                    }
                }
            }
            closedir($aux);
        }
    }
    else
    {
        echo $ruta;
        echo "<br />No es ruta valida";
    }
	return($total);
}
?>