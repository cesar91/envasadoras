<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/functions/Tramite.php");

// Comprobación de Gastos
// Obtener CECOS
if (isset($_POST['obtenercecos'])){
	$cnn = new conexion();
	
	$query = sprintf("SELECT sg_motivo, sg_monto, sg_divisa, sg_ceco, sg_ciudad,
			%s AS sg_fecha_gasto, sg_lugar, cc_centrocostos,
			IF(t_etapa_actual = %s, sg_observaciones_edicion, sg_observaciones) AS observaciones,
			sg_observaciones, sg_observaciones_edicion, t_etapa_actual
			FROM solicitud_gastos
			JOIN tramites ON sg_tramite = t_id
			JOIN cat_cecos ON sg_ceco = cc_id
			WHERE sg_tramite = '%s'", "DATE_FORMAT(sg_fecha_gasto,'%d/%m/%Y')", SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR, $tramite);
	$rst = $cnn->consultar($query);
	
	while($fila = mysql_fetch_assoc($rst)){
		$_arreglo[] = array(
				'motivo' => utf8_encode($fila['sg_motivo']),
				'monto' => $fila['sg_monto'],
				'divisa' => $fila['sg_divisa'],
				'ceco' => $fila['cc_centrocostos'],
				'ciudad' => utf8_encode($fila['sg_ciudad']),
				'observaciones' => utf8_encode($fila['observaciones']),
				'observaciones_edicion' => utf8_encode($fila['sg_observaciones_edicion']),
				'fecha_gasto' => $fila['sg_fecha_gasto'],
				'lugar' => utf8_encode($fila['sg_lugar']),
				'etapaActual' => $fila['t_etapa_actual']
		);
	}
	echo json_encode($_arreglo);
}

// Obtener detalles de la Comprobación
if (isset($_POST['idTramiteComprobacionGastos'])){
	$cnn = new conexion();
	$idTramite = $_POST['idTramiteComprobacionGastos'];
	
	$query = sprintf("SELECT detalle_comprobacion_gastos.dc_id, dc_tipo, IFNULL((SELECT dc_notransaccion FROM amex WHERE idamex = dc_idamex_comprobado),'N/A') AS notransaccion,
						%s AS dc_factura,
						IF(cp_concepto = 'Alimentos',IF(dc_tipo_comida=1,CONCAT('Desayuno - ',cp_concepto),IF(dc_tipo_comida=2, CONCAT('Comida -', cp_concepto),CONCAT('Cena -',cp_concepto))),cp_concepto) AS cp_concepto,
						IF(dc_descripcion!='',dc_descripcion,'N/A') AS 'dc_descripcion', CONVERT(IF(dc_comensales=0,'N/A',dc_comensales),CHAR(3)) AS 'dc_comensales',
						CONVERT(IF(dc_proveedor=0,'N/A',(SELECT pro_proveedor FROM proveedores WHERE dc_proveedor = pro_id)),CHAR(30)) AS 'dc_proveedor', IF(dc_rfc='','N/A',dc_rfc) AS 'dc_rfc',
						%s AS 'dc_fecha',
						FORMAT(dc_monto,2) AS dc_monto,
						FORMAT(dc_iva,2) AS dc_iva,
						FORMAT(dc_propinas,2) AS dc_propinas,
						FORMAT(dc_imp_hospedaje,2) AS dc_imp_hospedaje,
						FORMAT(dc_total,2) AS dc_total,
						FORMAT(dc_divisa,2) AS dc_divisa,
						IF(dc_divisa = 1,'MXN',IF(dc_divisa = 2,'USD','EUR')) AS div_nombre,
						(SELECT	FORMAT((div_tasa * (dc_monto + dc_iva + dc_propinas + dc_imp_hospedaje)),2) FROM divisa WHERE div_id = dc_divisa) AS 'dc_total_pesos',dc_idamex_comprobado,
						FORMAT(dc_total_dolares,2) AS dc_total_dolares, ccb.dc_id AS 'concepto_id', dc_tipo_comida,
						CONVERT(IF((SELECT pro_id FROM proveedores WHERE pro_id = dc_proveedor) IS NOT NULL,'on','off'),CHAR(4)) AS tipoProveedor,
						CONVERT(IFNULL((SELECT(IF(notarjetacredito = tarjeta,1,IF(notarjetacredito_gas = tarjeta,2,'')))		
						FROM empleado, amex
						WHERE (tarjeta = notarjetacredito 
						OR tarjeta = notarjetacredito_gas)
						AND idamex = dc_idamex_comprobado LIMIT 1),'-1'),CHAR(4)) AS 'tipoTarjeta', IFNULL(dc_folio_factura,'N/A') AS dc_folio_factura, 
						(SELECT CONCAT(monto, ' ', moneda_local) FROM amex WHERE idamex = dc_idamex_comprobado) AS cargoAMEX 
						FROM detalle_comprobacion_gastos, comprobacion_gastos, tramites, cat_conceptosbmw ccb, divisa
						WHERE co_id = dc_comprobacion
						AND t_id = co_mi_tramite
						AND dc_concepto = ccb.dc_id
						AND div_id = dc_divisa  
						AND t_id = '%s' 
						ORDER BY detalle_comprobacion_gastos.dc_id", "DATE_FORMAT(dc_factura,'%d/%m/%Y')", "DATE_FORMAT(dc_factura,'%d/%m/%Y')", $idTramite);
	//error_log("---->>>>>>>>>>>>".$query);
	$rst = $cnn->consultar($query);
	
	while($fila = mysql_fetch_assoc($rst)){
		$_arreglo[] = array(
				'tipo_bd'=>$fila['dc_tipo'],
				'transaccion_bd'=>utf8_encode($fila['notransaccion']),
				'fecha_db'=>utf8_encode($fila['dc_factura']),
				'concepto_db'=>utf8_encode($fila['cp_concepto']),
				'comentario_db'=>utf8_encode($fila['dc_descripcion']),
				'asistentes_db'=>utf8_encode($fila['dc_comensales']),
				'proveedor_db'=>utf8_encode($fila['dc_proveedor']),
				'rfc_db'=>utf8_encode($fila['dc_rfc']),
				'factura_db'=>utf8_encode($fila['dc_folio_factura']),
				'monto_db'=>utf8_encode($fila['dc_monto']),
				'iva_db'=>utf8_encode($fila['dc_iva']),
				'propinas_db'=>utf8_encode($fila['dc_propinas']),
				'impHos_db'=>utf8_encode($fila['dc_imp_hospedaje']),
				'total_db'=>utf8_encode($fila['dc_total']),
				'divisa_db'=>utf8_encode($fila['dc_divisa']),
				'divisaTipo_db'=>utf8_encode($fila['div_nombre']),
				'totalPesos_db'=>utf8_encode($fila['dc_total_pesos']),					
				'idamex_db'=>utf8_encode($fila['dc_idamex_comprobado']),
				'totalUsd_db'=>utf8_encode($fila['dc_total_dolares']),	
				'conceptoId_db'=>utf8_encode($fila['concepto_id']),
				'tipoComida_db'=>utf8_encode($fila['dc_tipo_comida']),	
				'tipoTarjeta_db'=>utf8_encode($fila['tipoTarjeta']),
				'tipoProveedor_db'=>utf8_encode($fila['tipoProveedor']),
				'cargo_factura_db'=>utf8_encode($fila['cargoAMEX']),
				'detalleID'=>$fila['dc_id']
		);
	}
	echo json_encode($_arreglo);
}

// Obtener la información de la Solicitud de Gastos
if(isset($_POST['idTramiteSolicitudGastos'])){
	$cnn = new conexion();
	$tramiteSolicitud = $_POST['idTramiteSolicitudGastos'];

	$sql = sprintf("SELECT sg_monto_pesos, sg_concepto, sg_lugar, sg_ciudad, sg_requiere_anticipo, sg_ceco FROM solicitud_gastos WHERE sg_tramite = '%s'", $tramiteSolicitud);
	//error_log("--->>Consulta datos Solicitud: ".$sql);
	$rst = $cnn->consultar($sql);
	$filasRetornadas = mysql_num_rows($rst);
	
	if($filasRetornadas > 0){
		while($fila = mysql_fetch_assoc($rst)){
			$_arreglo[] = array(
				'anticipoSolicitado'=>$fila['sg_monto_pesos'],
				'exixteSolicitud'=>1,
				'conceptoSolicitud'=>$fila['sg_concepto'],
				'lugarComida'=>utf8_encode($fila['sg_lugar']),
				'ciudadComida'=>utf8_encode($fila['sg_ciudad']),
				'requiereanticipo'=>$fila['sg_requiere_anticipo'],
				'ceco'=>$fila['sg_ceco']
			);
		}
	}else{
		$_arreglo[] = array(
			'anticipoSolicitado'=>0,
			'exixteSolicitud'=>0,
			'conceptoSolicitud'=>0,
			'lugarComida'=>"",
			'ciudadComida'=>"", 
			'requiereanticipo'=>0,
			'ceco'=>-1
		);
	}
	//error_log("--->>Arreglo a retornar: ".print_r($_arreglo, true));
	echo json_encode($_arreglo);
}

if(isset($_POST['idTramiteINFO'])){
	$cnn = new conexion();
	$idTramite = $_POST['idTramiteINFO'];
	
	$query = sprintf("SELECT co_cc_clave, co_observaciones_edicion, co_lugar, co_ciudad FROM comprobacion_gastos WHERE co_mi_tramite = '%s'", $idTramite);
	//error_log("---->>>>>>>>>>>>".$query);
	$rst = $cnn->consultar($query);
	
	while($fila = mysql_fetch_assoc($rst)){
		$_arreglo[] = array(
			'ceco'=>$fila['co_cc_clave'], 
			'observaciones'=>utf8_encode($fila['co_observaciones_edicion']),
			'lugar_restaurante'=>utf8_encode($fila['co_lugar']),
			'ciudad'=>utf8_encode($fila['co_ciudad'])
		);
	}
	echo json_encode($_arreglo);
}

//Guardar las Tasas introducidas por Finanzas
if(isset($_POST['tasaUSD']) && isset($_POST['tasaEUR'])){
	$cnn = new conexion();
	$tasaDolar = $_POST['tasaUSD'];
	$tasaEuro = $_POST['tasaEUR'];
	$tramiteComp = $_POST['tramite'];

	$tasaDolar = str_replace(',', '', $tasaDolar);
	$tasaEuro = str_replace(',', '', $tasaEuro);
	
	$sql = sprintf("UPDATE comprobacion_gastos SET co_tasa_USD = '%s', co_tasa_EUR = '%s' WHERE co_mi_tramite = '%s';", $tasaDolar, $tasaEuro, $tramiteComp);
	//error_log($sql);
	$cnn->ejecutar($sql);
}

// Cargar conceptos
if(isset($_POST['obtenerConceptos'])){
	$cnn = new conexion();
	$query = sprintf("SELECT c.cp_id AS cp_id, cp_concepto, cp_cuenta FROM cat_conceptos AS c 
			INNER JOIN conceptos_flujos AS cf ON (c.cp_id = cf.cp_id) 
			WHERE cf.f_id = %s AND cp_activo = 1 ORDER BY cp_concepto", FLUJO_COMPROBACION_GASTOS);
	//error_log("--->>Consulta Conceptos según el flujo: ".$query);
	$res = $cnn->consultar($query);
	$opciones = '<option value="-1">Seleccione ...</option>';
	
	while($fila = mysql_fetch_assoc($res)){
		$opciones .= '<option value="'.$fila["dc_id"].'">'.utf8_encode($fila["cp_concepto"]).'</option>';
	}
	//error_log("--->>Opciones: ".$opciones);
	echo json_encode($opciones);
}

// Carga de invitados
if (isset($_POST['idTramite'])) {
	$cnn = new conexion();
	$t_id = $_POST['idTramite'];
	$_arreglo = array();
	$i=0;

	$query = sprintf("SELECT c_nombre_invitado, c_puesto_invitado, c_empresa_invitado, c_tipo_invitado
			FROM comensales
			INNER JOIN comprobacion_gastos ON (co_id = c_comprobacion)
			INNER JOIN tramites ON (t_id = co_mi_tramite)
			WHERE t_id = '%s'", $t_id);
	//error_log("--->>Consulta Invitados: ".$query);
	$rst2 = $cnn->consultar($query);
	$i=0;
	$j=0;
	while ($filaa = mysql_fetch_assoc($rst2)){
		$data=sprintf("%s:%s:%s:%s",utf8_encode($filaa['c_nombre_invitado']), utf8_encode($filaa['c_puesto_invitado']), utf8_encode($filaa['c_empresa_invitado']), $filaa['c_tipo_invitado']);
		$mayus=array("Á","É","Í","Ó","Ú","á","é","í","ó","ú");
		$mayus2=array("A","E","I","O","U","a","e","i","o","u");
		$data=str_replace($mayus,$mayus2,$data);
		$_arreglo[$i]=$data;
		$i++;
		//error_log("--->>CHECA!!! --->> ".$data);
	}
	echo json_encode($_arreglo);
}
?>