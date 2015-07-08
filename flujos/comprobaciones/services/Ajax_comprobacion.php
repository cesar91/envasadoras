<?php

/* * ******************************************************
 *            T&E Catálogo de Proveedores                *
 * Creado por:	  Jorge Usigli Huerta 11-Ene-2010		*
 * Modificado por: Jorge Usigli Huerta 04-Feb-2010		*
 * PHP, jQuery, JavaScript, CSS                          *
 * ******************************************************* */
 
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/functions/AgrupacionUsuarios.php");
require_once("$RUTA_A/functions/Tramite.php");
require_once("$RUTA_A/functions/Concepto.php");
require_once("$RUTA_A/functions/RutaAutorizacion.php");
require_once "$RUTA_A/functions/Notificacion.php";

	function scapeUTF8($cadena){
		$busca = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ");
		$sustituye = array("%","%","%","%","%","%","%","%","%","%","%","%");
		return str_replace($busca,$sustituye,$cadena);
	}

if (isset($_POST['submit'])) {

    $tramite = $_POST['submit'];
    if ($tramite != 'undefined') {
        $cnn = new conexion();
        $query = sprintf("select sa_anticipo from solicitud_anticipo where sa_tramite=%s", $tramite);
        $rst = $cnn->consultar($query);
        $fila = mysql_fetch_assoc($rst);
        echo $fila['sa_anticipo'];
    }
} elseif (isset($_POST['ant_viaje'])) {

    $tramite = $_POST['ant_viaje'];
    if ($tramite != 'undefined') {
        $cnn = new conexion();
        $query = sprintf("SELECT sv_total_anticipo FROM solicitud_viaje WHERE sv_tramite=%s", $tramite);
        $rst = $cnn->consultar($query);
        $fila = mysql_fetch_assoc($rst);
		if($fila){
			echo $fila['sv_total_anticipo'];
		}else{
			echo "0.00";
		}		
    }
} elseif (isset($_POST['delete'])) {

    $tramite = $_POST['idtramite'];
    $tipoComprobacion = $_POST['tipo'];
    if ($tramite != 'undefined') {
        $cnn = new conexion();
        $query = sprintf("update detalle_comprobacion set dc_estatus=2,dc_folio_factura=(dc_folio_factura*-1) where dc_id=%s", $tramite);
        $rst = $cnn->insertar($query);
    }
}

if (isset($_POST['asistentes'])) {
    $monto = $_POST['asistentes'];
    $monto = number_format($monto, 2, ".", ",");
    $divisa = $_POST['divisa'];

    if ($divisa == 'MXN') {
        $_parametros = new Parametro();
        $_parametros->Load(4);
        $tasa = $_parametros->Get_dato("p_cantidad");
        if ($tasa > $monto) {
            echo "El monto supera la politica de Alimentos.";
        } else {
            echo "";
        }
    } else {
        $_parametros = new Parametro();
        $_parametros->Load(5);
        $tasa = $_parametros->Get_dato("p_cantidad");
        if ($tasa > $monto) {
            echo "El monto supera la politica de Alimentos.";
        } else {
            echo "";
        }
    }
}

if (isset($_POST['idsolicitud'])) {
    $cnn = new conexion();
    $query = sprintf("select sv_id from solicitud_viaje where sv_tramite=%s", $_POST['idsolicitud']);
    $rst = $cnn->consultar($query);
    $fila = mysql_fetch_assoc($rst);
    $sv_id = $fila['sa_anticipo'];

    $cnn = new conexion();
    $query = sprintf("select svi_dias_viaje from sv_itinerario where svi_solicitud=%s", $sv_id);
    $rst = $cnn->consultar($query);
    $fila = mysql_fetch_assoc($rst);
    $totalDias = $fila['svi_dias_viaje'];
    echo $totalDias;
}

if (isset($_POST['getJefe'])) {

    $idcentrocosto = $_POST['getJefe'];
    $cnn = new conexion();
    $query = sprintf("select cc_responsable,cc_centrocostos from cat_cecos where cc_id=%s", $idcentrocosto);
    $rst = $cnn->consultar($query);
    $fila = mysql_fetch_assoc($rst);
    $cc = $fila['cc_centrocostos'];
    $rsp = $fila['cc_responsable'];
    echo $cc . "|" . $rsp;
}

if (isset($_POST['tipo_tarjeta']) && isset($_POST['usuario'])) {
    $tipo_tarjeta = $_POST['tipo_tarjeta'];
    $noEmpleado1 = $_POST['usuario'];
    //Amex corporativa Gastos
    if ($tipo_tarjeta == '1') {    	
        $cnn = new conexion();
        $query_card_gastos = sprintf("SELECT notarjetacredito FROM empleado WHERE estatus = 1 AND idfwk_usuario = '%s'", $noEmpleado1);
        //error_log($query_card_gastos);
        $rst2 = $cnn->consultar($query_card_gastos);
        $filaa = mysql_fetch_assoc($rst2);
        $noCreditCard = $filaa['notarjetacredito'];
        echo $noCreditCard;
    }
    if ($tipo_tarjeta == '2') {//Amex_corporativa_Gasolina
        $cnn = new conexion();
        $query_card_gas = sprintf("SELECT notarjetacredito_gas FROM empleado WHERE estatus = 1 AND idfwk_usuario = '%s'", $noEmpleado1);
        //error_log($query_card_gas);
        $rst3 = $cnn->consultar($query_card_gas);
        $filab = mysql_fetch_assoc($rst3);
        $noCreditCard = $filab['notarjetacredito_gas'];
        echo $noCreditCard;
    }
}

if (isset($_POST['no_tarjeta'])) {
    $no_tarjeta = $_POST['no_tarjeta'];
    $t_id = $_POST['comprobacion'];
    $t_etapa = 0;
    $t_flujo = 0;
    
    $_arreglo = array();
    $cnn = new conexion();
    $tramite = new Tramite();
    if($t_id != 0){
	    $tramite->Load_Tramite($t_id);
	    $t_etapa = $tramite->Get_dato("t_etapa_actual");
	    $t_flujo = $tramite->Get_dato("t_flujo");
    }
    
    if($t_flujo == FLUJO_COMPROBACION){
    	$query = sprintf("SELECT co_id FROM comprobaciones WHERE co_mi_tramite = '%s'", $t_id);
    	$rst = mysql_query($query);
    	$fila = mysql_fetch_assoc($rst);
    	$co_id = $fila['co_id'];
    }else if($t_flujo == FLUJO_COMPROBACION_INVITACION){
    	$query = sprintf("SELECT co_id FROM comprobacion_invitacion WHERE co_mi_tramite = '%s'", $t_id);
    	$rst = mysql_query($query);
    	$fila = mysql_fetch_assoc($rst);
    	$co_id = $fila['co_id'];
    }
	
	if($t_flujo == FLUJO_COMPROBACION && ($t_etapa == 1 || $t_etapa == 4 || $t_etapa == 5 || $t_etapa == 10)){ // Etapa devuelta con observaciones, no se utiliza la constante, debido a que para CI/CV tienen nombres diferentes
    	$query_cargostc = sprintf("SELECT idamex, DATE_FORMAT(fecha_cargo, '%%d/%%m/%%Y') AS fecha, concepto, CONVERT(monto, DECIMAL(10, 2)) AS monto, moneda_local 
			FROM amex WHERE tarjeta = '%s' AND estatus = '0' UNION
			SELECT idamex, DATE_FORMAT(fecha_cargo, '%%d/%%m/%%Y') AS fecha, concepto, CONVERT(monto, DECIMAL(10, 2)) AS monto, moneda_local 
			FROM amex WHERE tarjeta = '%s' AND estatus = '0' AND comprobacion_id = '%s'", $no_tarjeta, $no_tarjeta, $co_id);
    }else if($t_flujo == FLUJO_COMPROBACION_INVITACION && ($t_etapa == 1 || $t_etapa == 4 || $t_etapa == 5 || $t_etapa == 10)){ // Etapa devuelta con observaciones, no se utiliza la constante, debido a que para CI/CV tienen nombres diferentes
    	$query_cargostc = sprintf("SELECT idamex, DATE_FORMAT(fecha_cargo, '%%d/%%m/%%Y') AS fecha, concepto, CONVERT(monto, DECIMAL(10, 2)) AS monto, moneda_local 
			FROM amex WHERE tarjeta = '%s' AND estatus = '0' AND comprobacion_id = '%s' UNION
    		SELECT idamex, DATE_FORMAT(fecha_cargo, '%%d/%%m/%%Y') AS fecha, concepto, CONVERT(monto, DECIMAL(10, 2)) AS monto, moneda_local 
			FROM amex WHERE tarjeta = '%s' AND estatus = '0' AND comprobacion_id = '0'", $no_tarjeta, $co_id, $no_tarjeta);
    }else{
    	$query_cargostc = sprintf("SELECT idamex,date_format(fecha_cargo,'%%d/%%m/%%Y') as fecha,concepto,CONVERT(monto,DECIMAL(10,2)) as monto,moneda_local FROM amex WHERE tarjeta = '%s' AND estatus = '0' AND comprobacion_id = '0'", $no_tarjeta);
    }
    //error_log($query_cargostc);
    $rst2 = $cnn->consultar($query_cargostc);
	$i=0;
	$j=0;
    while ($filaa = mysql_fetch_assoc($rst2)) {
        $data=sprintf("%s:%s - %s - %s %s ",$filaa['idamex'], $filaa['fecha'], utf8_encode($filaa['concepto']), $filaa['monto'], $filaa['moneda_local']);
        $_arreglo[$i]=$data;
        $i++;
    }
	echo json_encode($_arreglo);
}
// seleccionar el centro de costos
if (isset($_POST['t_id'])) {
    $t_id = $_POST['t_id'];
	$_arreglo = array();

    $cnn = new conexion();
    $query_cargostc = sprintf("SELECT si_ceco,cc_centrocostos FROM solicitud_invitacion AS si INNER JOIN cat_cecos AS cc ON si.si_ceco = cc.cc_id WHERE si.si_tramite= '%s' ", $t_id);
    //error_log($query_cargostc);
    $rst2 = $cnn->consultar($query_cargostc);
$i=0;
$j=0;
    while ($filaa = mysql_fetch_assoc($rst2)) {
        $data=sprintf($filaa['cc_centrocostos']);
        $_arreglo[$i]=$data;
        $i++;
    }
echo json_encode($_arreglo);
}
// seleccionar el centro de costos de una comprobacion de invitacion
if (isset($_POST['t_id4'])) {
    $t_id4 = $_POST['t_id4'];
	$_arreglo = array();

    $cnn = new conexion();
    $query_cargostc = sprintf("SELECT co_cc_clave,cc_centrocostos FROM comprobacion_invitacion AS co INNER JOIN cat_cecos AS cc ON co.co_cc_clave = cc.cc_id WHERE co.co_tramite= '%s' ", $t_id4);
    //error_log($query_cargostc);
    $rst2 = $cnn->consultar($query_cargostc);
$i=0;
$j=0;
    while ($filaa = mysql_fetch_assoc($rst2)) {
        $data=sprintf($filaa['cc_centrocostos']);
        $_arreglo[$i]=$data;
        $i++;
    }
echo json_encode($_arreglo);
}
//seleccionar el centro de costos de viajes
if (isset($_POST['t_id3'])) {
    $t_id3 = $_POST['t_id3'];
	
	
    $cnn = new conexion();
    $query_cargostc = sprintf("SELECT cc_centrocostos FROM solicitud_viaje INNER JOIN cat_cecos ON solicitud_viaje.sv_ceco_paga = cat_cecos.cc_id WHERE sv_tramite = %s ", $t_id3);
    
    $rst2 = $cnn->consultar($query_cargostc);
  	$filab = mysql_fetch_assoc($rst2);
    $cc_centrocostos = $filab['cc_centrocostos'];
  
    echo $cc_centrocostos;
}

// seleccionar los invitados de la solicitud
if (isset($_POST['t_id2'])) {
    $t_id2 = $_POST['t_id2'];
	$_arreglo = array();

    $cnn = new conexion();
    $query_cargostc = sprintf("SELECT dci_nombre_invitado, dci_puesto_invitado, dci_empresa_invitado, dci_tipo_invitado FROM comensales_sol_inv WHERE dci_solicitud = '%s'", $t_id2);
    $rst2 = $cnn->consultar($query_cargostc);
	$i=0;
	$j=0;
    while ($filaa = mysql_fetch_assoc($rst2)) {
    	
        $data=sprintf("%s:%s:%s:%s",utf8_encode($filaa['dci_nombre_invitado']),utf8_encode($filaa['dci_puesto_invitado']),utf8_encode($filaa['dci_empresa_invitado']),$filaa['dci_tipo_invitado']);
		$mayus=array("Á","É","Í","Ó","Ú","á","é","í","ó","ú");
        $mayus2=array("A","E","I","O","U","a","e","i","o","u");
        $data=str_replace($mayus,$mayus2,$data);        
        $_arreglo[$i]=$data;
        $i++;
        //error_log("CHECA!!!".$data);
    }
echo json_encode($_arreglo);
}


// seleccionar los ciudad de la solicitud
if (isset($_POST['sol_ciud'])) {
	$sol_ciud = $_POST['sol_ciud'];

	$cnn = new conexion();
	$query_cargostc = sprintf("select si_ciudad from solicitud_invitacion where si_tramite='%s' ", $sol_ciud);
	$rst2 = $cnn->consultar($query_cargostc);
	$i=0;
	$j=0;
	$data="";
	while ($filaa = mysql_fetch_assoc($rst2)) {	 
		$data=utf8_encode($filaa['si_ciudad']);
		$i++;
		//error_log("dato: ".$data);
	}
	echo $data;
}



if (isset($_POST['no_cargo'])) {
    $no_cargo = $_POST['no_cargo'];

    $cnn = new conexion();
    $query_cargos = sprintf("SELECT idamex, TC,moneda_local,SUBSTRING(notransaccion,29,35) as notransaccion,corporacion,DATE_FORMAT(fecha_cargo,'%%d/%%m/%%Y') as fecha_cargo,concepto,moneda_local,conversion_pesos, moneda_fact,rfc_establecimiento,monto, montoAmex, monedaAmex, div_tasa*montoAmex as conversion_pesos 
							FROM amex 
							JOIN divisa ON div_nombre = monedaAmex
							WHERE idamex= '%s'", $no_cargo);
    //error_log($query_cargos,0);
    $rst2 = $cnn->consultar($query_cargos);
    $filaa = mysql_fetch_assoc($rst2);
    $_arreglo1[]=array('idamex'=>$filaa['idamex'],'corporacion'=>$filaa['corporacion'],'fecha_cargo'=>$filaa['fecha_cargo'],'concepto'=>$filaa['concepto'],'moneda_local'=>$filaa['moneda_local'],'moneda_fact'=>$filaa['moneda_fact'],'rfc_establecimiento'=>$filaa['rfc_establecimiento'],'monto'=>$filaa['monto'],'montoAmex'=>$filaa['montoAmex'],'monedaAmex'=>$filaa['monedaAmex'],'notransaccion'=>$filaa['notransaccion'],'conversion_pesos'=>$filaa['conversion_pesos']);
	echo json_encode($_arreglo1);
}

// seleccionar la tasa de la divisa
if (isset($_POST['divisa']) && isset($_POST['divisa2'])) {
    $divisa = $_POST['divisa'];
    $divisa2 = $_POST['divisa2'];

    $cnn = new conexion();
    $query_cargostc = sprintf("SELECT DIV_TASA FROM divisa WHERE DIV_NOMBRE = '%s' ", $divisa);
    $rst2 = $cnn->consultar($query_cargostc);
	$i=0;
	$_arreglo=array();
    while ($filaa = mysql_fetch_assoc($rst2)){
        $data=sprintf($filaa['DIV_TASA']);
        $_arreglo[$i]=$data;
        $i++;
    }
	
	if($divisa2 != ""){
		$query_cargostc = sprintf("SELECT DIV_TASA FROM divisa WHERE DIV_NOMBRE = '%s' ", $divisa2);
		$rst2 = $cnn->consultar($query_cargostc);
		while ($filaa = mysql_fetch_assoc($rst2)) {
			$data=sprintf($filaa['DIV_TASA']);
			$_arreglo[$i]=$data;
			$i++;
		}
	}
	
	echo json_encode($_arreglo);
}
 
if (isset($_POST['dc_id']) && isset($_POST['re_id'])) {
	$dc_id = $_POST['dc_id'];
	$re_id = $_POST['re_id'];
	$_arreglo = array();
    //Amex corporativa Gastos
	$cnn = new conexion();
	$query_card_gastos = sprintf("SELECT PRBMW.PR_CANTIDAD,
			D.DIV_NOMBRE 
			FROM cat_conceptosbmw AS CC
			INNER JOIN parametrosbmw AS PBMW
			ON CC.DC_ID = PBMW.P_CONCEPTO
			INNER JOIN parametro_regionbmw AS PRBMW
			ON PBMW.P_ID = PRBMW.P_ID
			INNER JOIN cat_regionesbmw AS CR
			ON CR.RE_ID = PRBMW.RE_ID
			INNER JOIN divisa AS D
			ON PRBMW.DIV_ID = D.DIV_ID
			WHERE PRBMW.RE_ID = '%s'
			AND CC.DC_ID = '%s'", $re_id, $dc_id);
	//error_log($query_card_gastos);
	$rst2 = $cnn->consultar($query_card_gastos);
	$filaa = mysql_fetch_assoc($rst2);
	
	$_arreglo[0]=$filaa['PR_CANTIDAD'];
	$_arreglo[1]=$filaa['DIV_NOMBRE'];
	echo json_encode($_arreglo);
}

// Actualizar estatus de comprobaciones (3)
if (isset($_POST['idamex'])) {
	$idamex = $_POST['idamex'];
	$estado = $_POST['status'];
	$cnn = new conexion();
	$query_estatus = sprintf("UPDATE amex SET estatus='%s' WHERE idamex='%s'", $estado, $idamex);
	error_log($query_estatus);
	$rst7 = $cnn->consultar($query_estatus);
	echo "realizado";
}

// Actualizar estatus de comprobaciones (0)
if (isset($_POST['idamex_dev'])){
	$idamex = $_POST['idamex_dev'];
	$cnn = new conexion();
	$query_estatus_dev = sprintf("UPDATE amex SET estatus = '0', comprobacion_id = '0' WHERE idamex = '%s'", $idamex);
	//error_log('query_estatus_dev: '.$query_estatus_dev);
	$rst8 = $cnn->consultar($query_estatus_dev);
	echo "realizado";
}

// Actualizar estatus de comprobaciones modificados de 3 a 0
if (isset($_POST['idamex_ini'])) {
	$cnn = new conexion();
	$query_estatus_dev = "UPDATE amex a
							LEFT JOIN empleado e1 ON tarjeta = e1.notarjetacredito
							LEFT JOIN empleado e2 ON tarjeta = e2.notarjetacredito_gas
							LEFT JOIN usuario u1 ON u1.u_id = e1.idfwk_usuario
							LEFT JOIN usuario u2 ON u2.u_id = e2.idfwk_usuario
							SET a.estatus = 0
							WHERE a.estatus = 3 AND u1.u_id = ".$_POST['usuario']."
							OR a.estatus = 3 AND u2.u_id = ".$_POST['usuario'];
	$rst8 = $cnn->consultar($query_estatus_dev);
	echo "realizado";
}

// Cargar lugar de invitación
if (isset($_POST['idsol'])) {
	$idsolicitud = $_POST['idsol'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT si_lugar FROM solicitud_invitacion WHERE si_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['si_lugar']);
	}
    echo $dato;
}

// Cargar lugar de invitación
if (isset($_POST['tramSolicitud'])) {
	$idsolicitud = $_POST['tramSolicitud'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT co_lugar FROM comprobacion_invitacion WHERE co_mi_tramite = '%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['co_lugar']);
	}
	echo $dato;
}

// Cargar Fecha de invitación
if (isset($_POST['tramiteid'])) {
	$tramite_fecha = $_POST['tramiteid'];
	$cnn = new conexion();
	$query_fecha_inv = sprintf("SELECT DATE_FORMAT(si_fecha_invitacion, '%%d-%%m-%%Y') AS si_fecha_invitacion FROM solicitud_invitacion WHERE si_tramite='%s'", $tramite_fecha);
	//error_log($query_fecha_inv);
	$rst = $cnn->consultar($query_fecha_inv);
	$fila = mysql_fetch_assoc($rst);
	$data=sprintf($fila['si_fecha_invitacion']);
    echo $data;
}

// Cargar monto en pesos
if (isset($_POST['idsolic'])) {
	$idsolicitud = $_POST['idsolic'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT si_monto_pesos FROM solicitud_invitacion WHERE si_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['si_monto_pesos'];
	}
	echo $dato;
}

// Cargar datos de la Solicitud
//carga los datos para el previo
if (isset($_POST['comprobacionSI'])) {
	$idTramite = $_POST['comprobacionSI'];
	$cnn = new conexion();
	
	$query = "SELECT co_tramite, 
		(CASE co_tipo 
		WHEN '1' THEN 'amex'
		WHEN '3' THEN 'reembolso_para_empleado'
		END) AS co_tipo, 
		dc_divisa, co_observaciones, co_observaciones_edicion, co_lugar, 
		SUM(dc_monto) AS dc_monto, DATE_FORMAT(dc_factura, '%d/%m/%Y') AS dc_factura, pro_proveedor, SUM(dc_iva) AS dc_iva, 
		SUM(dc_propinas) AS dc_propinas, DATE_FORMAT(si_fecha_invitacion, '%d/%m/%Y') AS si_fecha_invitacion, si_ciudad, si_lugar, 
		cc_centrocostos, DATE_FORMAT(si_fecha_invitacion, '%Y/%m/%d') AS si_fecha_invitacion_bd, dc_rfc, dc_folio_factura, t_etapa_actual, t_autorizaciones_historial  
		FROM comprobacion_invitacion 
		JOIN tramites ON t_id = co_mi_tramite
		JOIN detalle_comprobacion_invitacion ON dc_comprobacion = co_id 
		LEFT JOIN proveedores ON pro_rfc = dc_rfc 
		JOIN solicitud_invitacion ON si_tramite = co_tramite 
		JOIN cat_cecos ON cc_id = co_cc_clave 
		WHERE co_mi_tramite ='{$idTramite}'";
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
			'co_tramite' =>$fila['co_tramite'], 
			'co_tipo' =>$fila['co_tipo'], 
			'dc_divisa' =>$fila['dc_divisa'],
			'co_observaciones' =>utf8_encode($fila['co_observaciones']),
			'co_observaciones_edicion' =>utf8_encode($fila['co_observaciones_edicion']),
			'dc_monto' =>$fila['dc_monto'],
			'dc_factura' =>$fila['dc_factura'],
			'pro_proveedor' =>utf8_encode($fila['pro_proveedor']),
			'dc_iva' =>$fila['dc_iva'],
			'dc_propinas' =>$fila['dc_propinas'],
			'si_fecha_invitacion' =>$fila['si_fecha_invitacion'],
			'si_ciudad' =>utf8_encode($fila['si_ciudad']),
			'si_lugar' =>utf8_encode($fila['si_lugar']),
			'co_lugar' =>utf8_encode($fila['co_lugar']),
			'si_fecha_invitacion_bd' =>$fila['si_fecha_invitacion_bd'],
			't_etapa_actual' =>$fila['t_etapa_actual'],
			'dc_rfc' =>$fila['dc_rfc'],
			'dc_folio_factura' =>$fila['dc_folio_factura'],
			'cc_centrocostos' =>$fila['cc_centrocostos'],
			't_autorizaciones_historial' =>$fila['t_autorizaciones_historial']
		);
	}
	echo json_encode($_arreglo);
}

// Cargar id de solicitud
if (isset($_POST['solicitud_id'])) {
	$idsolicitud = $_POST['solicitud_id'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT co_tramite FROM comprobacion_invitacion WHERE co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['co_tramite'];
	}
	echo $dato;
}
// Cargar tipo
if (isset($_POST['soli_tipo'])) {
	$idsolicitud = $_POST['soli_tipo'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT co_tipo FROM comprobacion_invitacion WHERE co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['co_tipo'];
		//error_log($dato);
	}
	if((int)$dato==3){
		echo "reembolso_para_empleado";
	}else if((int)$dato==1){
		echo "amex";
	}
}
// Cargar monto
if (isset($_POST['soli_monto'])) {
	$idsolicitud = $_POST['soli_monto'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT SUM(dc_monto) AS dc_monto FROM detalle_comprobacion_invitacion AS dci JOIN comprobacion_invitacion AS ci ON dci.dc_comprobacion=ci.co_id WHERE ci.co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_monto'];
	}
	echo $dato;
}

// Cargar fecha de comprobacion
if (isset($_POST['id_comp'])) {
	$idsolicitud = $_POST['id_comp'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT dc_factura FROM detalle_comprobacion_invitacion as dci join comprobacion_invitacion as ci on dci.dc_comprobacion=ci.co_id WHERE ci.co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_factura'];
	}
	echo $dato;
}

if (isset($_POST['soli_propina'])) {
	$idsolicitud = $_POST['soli_propina'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT SUM(dc_propinas) AS dc_propinas FROM detalle_comprobacion_invitacion AS dci JOIN comprobacion_invitacion AS ci ON dci.dc_comprobacion=ci.co_id WHERE ci.co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_propinas'];
	}
	echo $dato;
}

if (isset($_POST['soli_iva'])) {
	$idsolicitud = $_POST['soli_iva'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT SUM(dc_iva) AS dc_iva FROM detalle_comprobacion_invitacion AS dci JOIN comprobacion_invitacion AS ci ON dci.dc_comprobacion=ci.co_id WHERE ci.co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_iva'];
	}
	echo $dato;
}

if (isset($_POST['soli_divisa'])) {
	$idsolicitud = $_POST['soli_divisa'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT dc_divisa FROM detalle_comprobacion_invitacion as dci join comprobacion_invitacion as ci on dci.dc_comprobacion=ci.co_id WHERE ci.co_mi_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_divisa'];
	}
// 	if($dato=="MXN"){
// 		$dato1=1;
// 		echo $dato1;
// 	}else if($dato=="USD"){
// 		$dato1=2;
// 		echo $dato1;
// 	}else if($dato=="EUR"){
// 		$dato1=3;
// 		echo $dato1;
// 	}
	echo $dato;
	
}

if (isset($_POST['soli_observaciones'])) {
	$idsolicitud = $_POST['soli_observaciones'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT co_observaciones FROM comprobacion_invitacion WHERE co_mi_tramite='%s'", $idsolicitud);
	$rst = $cnn->consultar($query_lugar);
while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['co_observaciones']);
	}
	echo $dato;
}

if (isset($_POST['tram_coment'])) {
	$idsolicitud = $_POST['tram_coment'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT MIN(dc_id), dc_comentarios FROM detalle_comprobacion_invitacion as dci join comprobacion_invitacion as ci on dci.dc_comprobacion=ci.co_id where ci.co_mi_tramite='%s'", $idsolicitud);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['dc_comentarios']);
	}
	echo $dato;
}

if (isset($_POST['soli_folio'])) {
	$idsolicitud = $_POST['soli_folio'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT dc_folio_factura FROM detalle_comprobacion_invitacion AS dci 
		JOIN comprobacion_invitacion AS ci ON dci.dc_comprobacion=ci.co_id 
		WHERE ci.co_mi_tramite = '%s' 
		GROUP BY ci.co_id", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_folio_factura'];
	}
	echo $dato;
}

if (isset($_POST['soli_razon'])) {
	$idsolicitud = $_POST['soli_razon'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT pro_proveedor FROM detalle_comprobacion_invitacion AS dci 
		JOIN comprobacion_invitacion AS ci ON dci.dc_comprobacion=ci.co_id 
		JOIN proveedores AS pro ON pro.pro_rfc = dci.dc_rfc
		WHERE ci.co_mi_tramite = '%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['pro_proveedor']);
	}
	echo $dato;
}

if (isset($_POST['soli_rfc'])) {
	$idsolicitud = $_POST['soli_rfc'];
	$cnn = new conexion();
	$dato=0;
	$query_lugar = sprintf("SELECT dc_rfc FROM detalle_comprobacion_invitacion AS dci 
		JOIN comprobacion_invitacion AS ci ON dci.dc_comprobacion=ci.co_id 
		WHERE ci.co_mi_tramite = '%s' 
		GROUP BY ci.co_id", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['dc_rfc'];
	}
	echo $dato;
}
//En este ajax se obtiene el id del cargo que se guardo en el previo
if (isset($_POST['id_de_comp'])) {
	$idcomprobacion = $_POST['id_de_comp'];
	$cnn = new conexion();
	$dato = 0;
	$query = sprintf("SELECT idamex FROM amex WHERE comprobacion_id = (SELECT co_id FROM comprobacion_invitacion WHERE co_mi_tramite = '%s')", $idcomprobacion);
	//error_log($query);
	$rst = $cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['idamex'];
	}
	echo $dato;
}
//Funcion que nos permitira hacer el calculo del monto de la politica correspondiente a Alimento
if (isset($_POST['conceptoId'])) {
	$cnn = new conexion();
	$parameter = new Parametro();
	$id = $_POST['conceptoId'];
	$tramiteId=$_POST['tramiteId'];	
	
	$idAlimentoNI=0;
	$parametroAlimento="";
	$_arreglo=array();
	$datosAlimento=array();
	
	//seleccionamos el viaje (Sencillo,redondo,Multidestinos)
	$query_viaje="SELECT sv_viaje FROM solicitud_viaje WHERE sv_tramite = {$tramiteId} ";
	//error_log($query_viaje);
	$rst=$cnn->consultar($query_viaje);
	
	while ($fila = mysql_fetch_assoc($rst)) {
		$sv_viaje = $fila['sv_viaje'];
	}
	//error_log("tipo viaje".$sv_viaje);
	//se obtinen a continuacion 3 datos importantes ( Tipo de viaje , La region y  Los dias de viaje )
	if($sv_viaje == 'Sencillo' || $sv_viaje == 'Redondo'){
		$datosAlimento=$parameter->datosViajeSencilloRedondo($tramiteId);
	}else{
		//cuando sea multidestinos se tomara los datos del ultimo itinerario		
		$datosAlimento=$parameter->datosViajeMultidestino($tramiteId);
	}
	
	//El orden de los datos obtenidos es el siguiente:
	// 	"Dato1" (Tipo de viaje) = $datosAlimento[0]
	// 	"Dato2" (Region) = $datosAlimento[1]
	// 	"Dato3" (Dias de viaje) = $datosAlimento[2]
	
	//Obtiene el id correspondiente del tipo de viaje		
	if($datosAlimento[0] == 'Nacional'){
		$parametroAlimento="TasaAlimentosDiariaNacional";		
	}else if($datosAlimento[0]='Intercontinental' || $datosAlimento[0]='Continental'){
		$parametroAlimento="TasaAlimentosDiariaInternacional";		
	}
	//se reliza el calculo del monto maximo de Tasa diaria de Alimento (Nacional/Internacional)
	$idAlimentoNI=$parameter->calculoTasaConceptos($id,$parametroAlimento);
	
	//Se obtiene la cantidad y la divisa correspondiente
	$query=sprintf("SELECT pr_cantidad,div_id FROM parametro_regionbmw WHERE re_id='%s' AND p_id='%s'",$datosAlimento[1],$idAlimentoNI);
	//error_log("query".$query);
	$var=$cnn->consultar($query);
	$i=0;
	while ($fila = mysql_fetch_assoc($var)) {
		$_arreglo[]=array(
				'pr_cantidad' =>$fila['pr_cantidad'],
				'div_id' =>$fila['div_id']
		);
		$i++;
	}
	//se regresa el monto calculado
	echo json_encode($_arreglo);
	
}
//realizara la tranformacion del parametro respecto a su divisa
if(isset($_POST['tasaDivisa'])){
	$divisa=$_POST['tasaDivisa'];
	$parametroCantidad=$_POST['cantidadParam'];
	$parametroMXN=0;
	$parametro = new Parametro();
	$parametroMXN=$parametro->calculoMontoDivisa($divisa, $parametroCantidad);
	echo $parametroMXN;
}

//funcion que se encargara de crear la tabla temporal
if(isset($_POST['crear'])){
	$cnn = new conexion();	
	$query_creaTE="CREATE TABLE excepciones_temp(
	id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	mensaje VARCHAR(725) CHARACTER SET latin1 DEFAULT NULL,
	diferencia DECIMAL(10,2) DEFAULT NULL,
	PRIMARY KEY(id)
	)ENGINE=MYISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci";
	$cnn->insertar($query_creaTE);
}



//funcion que permitira ingresar las excepciones encontradas por exceder el limite de politica, en la tabla temporal creada
if (isset($_POST['mensaje'])) {
	$cnn = new conexion();	
	$mensaje=$_POST['mensaje'];	
	//error_log($mensaje);	
	$calculoDiferencia=$_POST['calculoDiferencia'];	
	
	//error_log("Insertara datos en la tabla temp");
	//realizamos la inserccion de la excepcion en la BD temporal para hacer su llamado mas adelante		
		$query_ex=sprintf("INSERT INTO excepciones_temp(mensaje,diferencia)VALUES('%s','%s')",$mensaje,$calculoDiferencia);
		//error_log($query_ex); 
		$rst = $cnn->insertar($query_ex);		
}


/*
//funcion para hacer la actualizacion del concepto en la comprobacion que sea cambiada
if(isset($_POST['concepto'])){
	$cnn = new conexion();	
	$idconcepto = $_POST['concepto'];
	$tramite=$_POST['idtramite'];
	$totalSeleccionado=$_POST['total'];
	
	
	//query para obtener el valor de el campo dc_comprobacion
	$query="SELECT dc_comprobacion FROM detalle_comprobacion WHERE dc_comprobacion = (SELECT co_id FROM comprobaciones WHERE co_mi_tramite ={$tramite})";
	$rst=$cnn->consultar($query);
	
	while($fila=mysql_fetch_assoc($rst)){
		$dc_comprobacion=$fila['dc_comprobacion'];
	}
	//Actualizaremos el campo  	
	$query_concepto=sprintf("UPDATE detalle_comprobacion SET dc_concepto=%s WHERE dc_comprobacion=%s AND dc_total=%s",$idconcepto,$dc_comprobacion,$totalSeleccionado);
	error_log($query_concepto);
}*/

//funcion que creara una tabla temporal para conceptos
if(isset($_POST['tablaConceptostemp'])){
	//error_log("Se generara la tabla temporal");		
	$cnn = new conexion();
	$query_delete = sprintf("DROP TABLE IF EXISTS conceptos_temp");
	//$cnn->ejecutar($query_delete);
	$query_creaTE="CREATE TABLE conceptos_temp (
		  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  id_s bigint(20) DEFAULT NULL,
		  concepto_id bigint(20) DEFAULT NULL,
		  total decimal(10,2) DEFAULT NULL,
		  id_tramite bigint(20) DEFAULT NULL,
		  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
	//error_log($query_creaTE);
	$cnn->ejecutar($query_creaTE);		
}

//funcion que pasara los conceptos y se iran guardando en una tabla temporal
if(isset($_POST['idConcepto'])){
	$cnn = new conexion();
	$aux = array();
	$idConcepto=$_POST['idConcepto'];
	$total=$_POST['total'];
	$idSelectConcepto=$_POST['idSelectConcepto'];
	$visita=$_POST['visitas'];
	$idT = $_POST['tramite'];
	
	$query=sprintf("SELECT * FROM conceptos_temp");
	$conceptoTemp = $cnn->consultar($query);
	
	while ($fila = mysql_fetch_assoc($conceptoTemp)) {			
		array_push($aux,$fila);
	}
	//error_log("visita"+$visita);
	if($visita == 1){
		$queryInsertar=sprintf("INSERT INTO conceptos_temp(id_s,concepto_id,total,id_tramite)VALUES(%s,%s,%s,%s)",$idSelectConcepto,$idConcepto,$total,$idT);
		//error_log($queryInsertar);
		$rst = $cnn->insertar($queryInsertar);
	}else{
		//error_log("Visita 2 validacion");
		$sql="call actualizarInsertarConcepto(".$idConcepto.",".$idSelectConcepto.",".$total.",".$idT.");";
		//error_log($sql);
		$res=mysql_query($sql);
		//error_log($res);	
	}	
}

/*if(isset($_POST['tramiteCo_id'])){		
	//tomamos las variables enviadas
	$cnn = new conexion();
	$dc_id=0;
	$dc_idamex_comprobado=0;
	$idTramite=$_POST['tramiteCo_id'];
	$tipoComprobacion=$_POST['tipoComprobacion'];	
	$conceptoDes=$_POST['conceptoDes'];
	$montoBD=$_POST['montoBD'];
	$divisaBD=$_POST['divisaBD'];
	$propinaBD=$_POST['propinaBD'];
	$ivaBD=$_POST['ivaBD'];
	$imphospedajeBD=$_POST['imphospedajeBD'];
	$total=$_POST['total'];
	$totalUSD=$_POST['totalUSD'];
	$comentario=$_POST['comentario'];
	$fecha=$_POST['fecha'];
	$numero=$_POST['numero'];
	$comprobacion=new Comprobacion();
	
	//cambiamos el valor textual a numerico del tipo de comprobacion
	if($tipoComprobacion == "Amex"){
		$valComprobacion = 2;
		//obtenemos el valor del id_amexcomprobado
		$queryIdAmex=sprintf("SELECT dc_idamex_comprobado FROM detalle_comprobacion WHERE dc_notransaccion=%s",$numero);		
		$amexComprobado = $cnn->consultar($queryIdAmex);
		
		while ($fila = mysql_fetch_assoc($amexComprobado)) {			
				$dc_idamex_comprobado=$fila['dc_idamex_comprobado'];
		}
		//error_log("idamex".$dc_idamex_comprobado);
		//obtener el id del concepto de tipo personal
		$queryPersonal=sprintf("SELECT dc_id FROM cat_conceptosbmw WHERE cp_concepto='%s'",$conceptoDes);
		$conceptoPersonal = $cnn->consultar($queryPersonal);
		
		while ($fila = mysql_fetch_assoc($conceptoPersonal)) {			
				$dc_id=$fila['dc_id'];
		}		
		$comprobacion->AgregarConceptoFinanzasAmex($idTramite,$valComprobacion,$dc_id,$montoBD,$propinaBD,$divisaBD,$ivaBD,$imphospedajeBD,$total,$totalUSD,$comentario,$fecha,$dc_idamex_comprobado,$numero);		
	}else{
		if($tipoComprobacion == "Anticipo"){
			$valComprobacion = 1;
		}elseif($tipoComprobacion == "Reembolso"){
			$valComprobacion = 3;
		}
		
		$dc_idamex_comprobado=0;
		$dc_notransaccion=0;	
		$comprobacion->AgregarConceptoFinanzas($idTramite,$valComprobacion,$dc_idamex_comprobado,$dc_notransaccion,$conceptoDes,$montoBD,$propinaBD,$divisaBD,$ivaBD,$imphospedajeBD,$total,$totalUSD,$comentario,$fecha);
	}
	
}

*/
if(isset($_POST['compTramite'])){	
	$cnn = new conexion();
	$observacionesViejas="";	
	$compTramite=$_POST['compTramite'];
	$centroCosto=$_POST['centroCosto'];
	$observaciones=$_POST['observaciones'];
	$anticipoCABMW=$_POST['anticipoCABMW'];
	$personalDescuento=$_POST['personalDescuento'];
	//error_log("personal descuento...............".$personalDescuento);
	$amexCABMW=$_POST['amexCABMW'];
	$efectivoCXBMW=$_POST['efectivoCXBMW'];
	$montoDescontar=$_POST['montoDescontar'];
	$montoReembolsar=$_POST['montoReembolsar'];
	$idUsuario=$_POST['idUsuario'];
	
	$tramite = new Tramite();
	$tramite->Load_Tramite($compTramite);
	$t_dueno = $tramite->Get_dato('t_dueno');
	$t_iniciador = $tramite->Get_dato('t_iniciador');
	
	//observaciones
	if($observaciones != "" && $t_dueno != $t_iniciador){
		$tramite=new Tramite();
		$tramite->Load_Tramite($compTramite);
		$t_dueno = $tramite->Get_dato("t_dueno");
			
		$query = sprintf("SELECT co_observaciones FROM comprobaciones WHERE co_mi_tramite='%s'",$compTramite);
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$co_observaciones = $fila['co_observaciones'];
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $co_observaciones, $observaciones, FLUJO_COMPROBACION, COMPROBACION_ETAPA_EN_APROBACION);
		
		$queryInsertaObs=sprintf("UPDATE comprobaciones SET co_observaciones='%s' WHERE co_mi_tramite='%s'",$observaciones,$compTramite);
		$cnn->ejecutar($queryInsertaObs);
	}
	
	$comprobacion=new Comprobacion();
	$comprobacion->ActualizarResumenFinanzas($compTramite,$centroCosto,$anticipoCABMW,$personalDescuento,$amexCABMW,$efectivoCXBMW,$montoDescontar,$montoReembolsar);	
}

if(isset($_POST['idtramitefin'])){
	$cnn = new conexion();	
	$divisaId=0;
	
	$idtramitefin=$_POST['idtramitefin'];
	$tipoAnticipo=$_POST['tipoAnticipo'];
	$fecha=$_POST['fecha'];
	$comentario=$_POST['comentario'];
	$monto=$_POST['monto'];	
	$propina=$_POST['propina'];
	$iva=$_POST['iva'];
	$imphospedaje=$_POST['imphospedaje'];
	$total=$_POST['total'];
	$divisa=$_POST['divisaMXN'];	
	$notransaccion=$_POST['notransaccion'];
	$conceptoPersonal=$_POST['conceptoPersonal'];
	$detalleProcedente = $_POST['detallePersonal'];
	$idcargoAmex = $_POST['idcargoAmex'];
	
	//Id del concepto personal
	$concepto = new Concepto();
	$concepto->Load_Concepto_By_Nombre($conceptoPersonal);
	$conceptoP = $concepto->Get_Dato("dc_id");
	error_log("Id Concepto: ".$conceptoP);
	
	//conversion de divisa
	if($divisa == "MXN"){
		$divisaId=1;
	}elseif($divisa == "USD"){
		$divisaId=2;
	}elseif($divisa == "EUR"){
		$divisaId=3;
	}
	
	$comentarioCodificado=utf8_decode($comentario);
	$comprobacion= new Comprobacion();
	$comprobacion->ActualizarCamposAprobados($idtramitefin,$tipoAnticipo,$notransaccion,$fecha,$comentarioCodificado,$monto,$propina,$iva,$imphospedaje,$total,$divisaId,$conceptoP,$detalleProcedente,$idcargoAmex);
	
}

if(isset($_POST['tramitePersonales'])){
	$cnn = new conexion();
	
	$idtramitefin = $_POST['tramitePersonales'];
	
	$dc_id = 0;
	$query = sprintf("SELECT dc_id FROM detalle_comprobacion
			INNER JOIN comprobaciones ON co_id = dc_comprobacion
			WHERE dc_concepto = '31' AND co_mi_tramite = '%s'", $idtramitefin);
	//error_log($query);
	$rst = $cnn->consultar($query);
	while ($arr = mysql_fetch_assoc($rst)) {
		$dc_id = $arr['dc_id'];
		$query = sprintf("DELETE FROM detalle_comprobacion WHERE dc_id = '%s'", $dc_id);
		//error_log($query);
		$cnn->ejecutar($query);
	}
	
}


if(isset($_POST['datoMonto'])){
	$tramite = new Tramite();	
	$cnn = new conexion();
	$divisa_USD=0;
	$dc_id=0;
	$conceptoEncontrado=0;
	$datoMonto=$_POST['datoMonto'];
	$datoIva=$_POST['datoIva'];
	$datoPropina=$_POST['datoPropina'];
	$datoImphospedaje=$_POST['datoImphospedaje'];
	$datoTotal=$_POST['datoTotal'];
	$montoOriginal=$_POST['montoOriginal'];
	$ivaOriginal=$_POST['ivaOriginal'];
	$propinaOriginal=$_POST['propinaOriginal'];
	$imphosOriginal=$_POST['imphosOriginal'];
	$conceptoOld=$_POST['conceptoOriginal'];
	$conceptoSel=$_POST['conceptoSelec'];
	$dc_id = $_POST['dc_id'];
	//Tomamos el id del concepto para actualizarlo
	$concepto=new Concepto();	
	
	$concepto->Load_Concepto_By_Nombre(utf8_decode($conceptoOld));
	$conceptoIdOrig=$concepto->Get_Dato("dc_id");
	
	$concepto->Load_Concepto_By_Nombre(utf8_decode($conceptoSel));
	$conceptoNuevo=$concepto->Get_Dato("dc_id");
	
	if($conceptoNuevo != "" ){
		$conceptoEncontrado=$conceptoNuevo;
	}else{
		$conceptoEncontrado=$conceptoSel;
	}
	
	//Realizamos la conversion de pesos a dolares para su total
	$queryUSD=sprintf("SELECT div_tasa FROM divisa WHERE div_nombre='USD'");
	$USD=$cnn->consultar($queryUSD);
	while ($fila = mysql_fetch_assoc($USD)) {
		$divisa_USD=$fila['div_tasa'];
	}
	
	$totalDolares=$datoTotal/$divisa_USD;
	
	
	$queryAct="UPDATE detalle_comprobacion
		SET dc_monto=".$datoMonto.",
		dc_iva=".$datoIva.",
		dc_propina=".$datoPropina.",
		dc_impuesto_hospedaje=".$datoImphospedaje.",
		dc_total=".$datoTotal.",
		dc_total_partida=".$datoTotal.",
		dc_total_dolares=".$tramite->truncate($totalDolares,2)."
		WHERE dc_id=".$dc_id."";
		//error_log($queryAct);
		$rstAct = $cnn->insertar($queryAct);
		
	$queryConcepto=sprintf("UPDATE detalle_comprobacion SET dc_concepto='%s' WHERE dc_id='%s' AND dc_concepto='%s'",$conceptoEncontrado,$dc_id,$conceptoIdOrig);
	//error_log($queryConcepto);
	$rstConcepto = $cnn->insertar($queryConcepto);
	
}



if(isset($_POST['idtotaltemp'])){
	
	$cnn = new conexion();
	$queryTemp="CREATE TABLE total_temp (
		  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  id_t bigint(20) DEFAULT NULL,
		  diferencia decimal(10,2) DEFAULT NULL,
		  monto decimal(10,2) DEFAULT NULL,
		  iva decimal(10,2) DEFAULT NULL,
		  propina decimal(10,2) DEFAULT NULL,
		  imphospedaje decimal(10,2) DEFAULT NULL,
		  id_next bigint(20) DEFAULT NULL,
		  id_tramite bigint(20) DEFAULT NULL,
		  PRIMARY KEY (id)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
	//error_log($queryTemp);
	$cnn->insertar($queryTemp);	
}

if(isset($_POST['idTTemp'])){
	$cnn = new conexion();
	$idOriginal=$_POST['idTTemp'];
	$diferenciaVirtual=$_POST['diferencia'];
	$montoOriginal=$_POST['monto'];
	$ivaOriginal=$_POST['iva'];
	$propinaOriginal=$_POST['propina'];
	$imphospedajeOriginal=$_POST['imphospedaje'];
	$idNext=$_POST['id'];
	$idT = $_POST['tramite'];
	//$idOriginal,$diferenciaVirtual,$montoOriginal,$idNext
	//$queryInsertatemp=sprintf("INSERT INTO total_temp(id_t,diferencia,monto,id_next) VALUES (%s,%s,%s,%s)",$idOriginal,$diferenciaVirtual,$montoOriginal,$idNext);	
	//$cnn->insertar($queryInsertatemp);
	$sql="call actualizarInsertarCalculoV(".$idOriginal.",".$diferenciaVirtual.",".$montoOriginal.",".$ivaOriginal.",".$propinaOriginal.",".$imphospedajeOriginal.",".$idNext.",".$idT.")";
	//error_log($sql);
	$cnn->ejecutar($sql);
}

if(isset($_POST['id_next'])){
	$cnn = new conexion();
	$_arreglo=array();
	$id_next=$_POST['id_next'];
	$idT = $_POST['tramite'];
	$Comprobacion = $_POST['comp'];
	
	//error_log("ammm --->".$id_next);
	
	if($Comprobacion == 1){
		$dcReferenciado = $_POST['detalleConcepto'];
		$montoOriginal = 0;
		$ivaOriginal = 0;
		$propinasOriginal = 0;
		$hospedajeOriginal = 0;
		$totalOriginal = 0;
		
		$query = sprintf("SELECT dc_monto, dc_iva, dc_propinas, dc_imp_hospedaje, dc_total FROM detalle_comprobacion 
			JOIN comprobaciones ON co_id = dc_comprobacion WHERE co_mi_tramite = '%s' AND dc_id = '%s'", $idT, $dcReferenciado);
		//error_log($query);
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$montoConcepto = $fila['dc_monto'];
		$ivaConcepto = $fila['dc_iva'];
		$propinaConcepto = $fila['dc_propinas'];
		$hospedajeConcepto = $fila['dc_imp_hospedaje'];
		$totalConcepto = $fila['dc_total'];
		
		$query = sprintf("SELECT dc_monto, dc_iva, dc_propinas, dc_imp_hospedaje, dc_total FROM detalle_comprobacion 
			JOIN comprobaciones ON co_id = dc_comprobacion WHERE co_mi_tramite = '%s' AND dc_origen_personal = '%s'", $idT, $dcReferenciado);
		//error_log($query);
		$rst = $cnn->consultar($query);
		
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$montoConceptoPersonal = $fila['dc_monto'];
		$ivaConceptoPersonal = $fila['dc_iva'];
		$propinaConceptoPersonal = $fila['dc_propinas'];
		$hospedajeConceptoPersonal = $fila['dc_imp_hospedaje'];
		$totalConceptoPersonal = $fila['dc_total'];
		
		$montoOriginal = $montoConcepto + $montoConceptoPersonal;
		$ivaOriginal = $ivaConcepto + $ivaConceptoPersonal;
		$propinasOriginal = $propinaConcepto + $propinaConceptoPersonal;
		$hospedajeOriginal = $hospedajeConcepto + $hospedajeConceptoPersonal;
		$totalOriginal = $totalConcepto + $totalConceptoPersonal;
		
		$_arreglo[]=array(
			'monto' => $montoOriginal, 
			'iva' => $ivaOriginal, 
			'propinas' => $propinasOriginal, 
			'hospedaje' => $hospedajeOriginal, 
			'total' => $totalOriginal 
		);
		
	}else{		
		$query = sprintf("SELECT SUM(dc_monto) AS montoOriginal, SUM(dc_iva) AS ivaOriginal, SUM(dc_propinas) AS propinasOriginal, SUM(dc_total) AS totalOriginal
			FROM detalle_comprobacion_invitacion JOIN comprobacion_invitacion ON co_id = dc_comprobacion WHERE co_mi_tramite = '%s'", $idT);
		//error_log($query);
		$rst = $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			$_arreglo[]=array(
					'monto' =>$fila['montoOriginal'],
					'iva' =>$fila['ivaOriginal'],
					'propinas' =>$fila['propinasOriginal'],
					'total' =>$fila['totalOriginal']
			);
		}
	}	
	echo json_encode($_arreglo);
}

if(isset($_POST['eliminarID'])){
	$cnn = new conexion();
	//error_log("Datosss");
	$idNext=$_POST['eliminarID'];
	$tramiteId=$_POST['tramite'];
	$query=sprintf("DELETE FROM total_temp WHERE id_next='%s' AND id_tramite='%s'",$idNext,$tramiteId);
	//error_log($query);
	$cnn->ejecutar($query);
	
}



if(isset($_POST['conceptoNew'])){
	$cnn = new conexion();
	//error_log("Empieza el nombre concepto");
	$conceptoNewId="";
	$conceptoId=$_POST['conceptoNew'];
	//error_log("idconcepto"+$conceptoId);
	$query=sprintf("SELECT dc_id, cp_concepto FROM cat_conceptosbmw WHERE dc_id=%s",$conceptoId);
	//error_log($query);
	$rst = $cnn->consultar($query);
	$i=0;
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'conceptoNewId' =>utf8_encode($fila['cp_concepto']),
		'NewId' =>$fila['dc_id']);
		$i++;
	}
	//error_log($conceptoNewId);
	echo json_encode($_arreglo);
}


// Para solicitud de invitación
if(isset($_POST['conceptoNewSI'])){
	$cnn = new conexion();
	//error_log("Empieza el nombre concepto");
	$conceptoNewId="";
	$conceptoId=$_POST['conceptoNewSI'];
	//error_log("idconcepto"+$conceptoId);
	$query=sprintf("SELECT cp_concepto FROM cat_conceptosbmw WHERE dc_id=%s",$conceptoId);
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$conceptoNewId=$fila['cp_concepto'];
	}
	//error_log($conceptoNewId);
	echo json_encode($conceptoNewId);
}

// Función que pasará los conceptos y se iran guardando en una tabla temporal
if(isset($_POST['idConceptoSI'])){
	$cnn = new conexion();
	$aux = array();
	$idConcepto=$_POST['idConceptoSI'];
	$total=$_POST['total'];
	$idSelectConcepto=$_POST['idSelectConcepto'];
	$visita=$_POST['visitas'];
	$idT = $_POST['tramite'];

	$query=sprintf("SELECT * FROM conceptos_temp");
	$conceptoTemp = $cnn->consultar($query);

	while ($fila = mysql_fetch_assoc($conceptoTemp)) {
		array_push($aux,$fila);
	}
	//error_log("visita"+$visita);
	if($visita == 1){
		$queryInsertar=sprintf("INSERT INTO conceptos_temp(id_s,concepto_id,total,id_tramite)VALUES(%s,%s,%s,%s)",$idSelectConcepto,$idConcepto,$total,$idT);
		//error_log($queryInsertar);
		$rst = $cnn->insertar($queryInsertar);
	}else{
		//error_log("Visita 2 validacion");
		$sql="call actualizarInsertarConcepto(".$idConcepto.",".$idSelectConcepto.",".$total.",".$idT.");";
		//error_log($sql);
		$res=mysql_query($sql);
		//error_log($res);
	}
}

	//verifica el monto de la transacción amex
	if(isset($_POST['resultadoAmex'])){
		if(isset($_POST['transacionAmex'])){
			$cnn = new conexion();
			$query = "SELECT 
					CONVERT(montoAmex*(SELECT div_tasa FROM divisa WHERE div_nombre = monedaAmex),DECIMAL(10,2)) AS conversion_pesos, 
					SUBSTRING(notransaccion,29,35) AS notransaccion 
					FROM amex 
					WHERE idamex = ".$_POST['transacionAmex'];
			//error_log($query);
			$res = $cnn->consultar($query);
			$monto = '';
			$row = mysql_fetch_assoc($res);
			$monto = $row['conversion_pesos'];								
			$_POST['resultadoAmex'];
			if ($monto != $_POST['resultadoAmex']){
				echo $row['notransaccion'];				
			}else{
				echo "0";			
			}
		}
	}
	
	//carga los datos para el previo
	if(isset($_POST['id_tramite_comp_prev'])){
		$idtramitecompprev = $_POST['id_tramite_comp_prev'];
		$cnn = new conexion();
		$query_estatus_dev = "SELECT detalle_comprobacion.dc_id, dc_tipo, IFNULL((SELECT dc_notransaccion FROM amex WHERE idamex = dc_idamex_comprobado),'N/A') AS notransaccion,
						DATE_FORMAT(dc_factura,'%d/%m/%Y') AS dc_factura,
						IF(cp_concepto = 'Alimentos',IF(dc_tipo_comida=1,CONCAT('Desayuno - ',cp_concepto),IF(dc_tipo_comida=2, CONCAT('Comida -', cp_concepto),CONCAT('Cena -',cp_concepto))),cp_concepto) AS cp_concepto,
						IF(dc_descripcion!='',dc_descripcion,'N/A') AS 'dc_descripcion', CONVERT(IF(dc_comensales=0,'N/A',dc_comensales),CHAR(3)) AS 'dc_comensales',
						CONVERT(IF(dc_proveedor=0,'N/A',(SELECT pro_proveedor FROM proveedores WHERE dc_proveedor = pro_id)),CHAR(30)) AS 'dc_proveedor', IF(dc_rfc='','N/A',dc_rfc) AS 'dc_rfc',
						DATE_FORMAT(dc_factura,'%d/%m/%Y') AS 'dc_fecha',
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
						FROM detalle_comprobacion, comprobaciones, tramites, cat_conceptosbmw ccb, divisa
						WHERE co_id = dc_comprobacion
						AND t_id = co_mi_tramite
						AND dc_concepto = ccb.dc_id
						AND div_id = dc_divisa  
						AND t_id =".$idtramitecompprev."
						ORDER BY detalle_comprobacion.dc_id ";
		//error_log($query_estatus_dev);
		$rst8 = $cnn->consultar($query_estatus_dev);
		$arreglo_comprobacion[]=array();
		while ($fila = mysql_fetch_assoc($rst8)){
			$arreglo_comprobacion[]=array(
				'tipo_bd'=>utf8_encode($fila['dc_tipo']),
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
				'detalleID'=>$fila['dc_id'],
					
				);
		}
		echo json_encode($arreglo_comprobacion);
	}
	
	//muestra el factor para los modelos de autos
	if(isset($_POST['ma_id'])){
		$ma_id = $_POST['ma_id'];
		$cnn = new conexion();
		$sql = "SELECT ma_factor FROM modelo_auto WHERE ma_id = ".$ma_id;
		$res = $cnn->consultar($sql);
		while ($row = mysql_fetch_array($res)){
			echo $row["ma_factor"];
		}
	}
	
	//regresa el ceco para la edicion de partidas
	if(isset($_POST['tramiteSolicitud']) || isset($_POST['tramiteComprobacion']) ){
		$cnn = new conexion();
		if(isset($_POST['tramiteComprobacion'])){
			$sql = "SELECT co_cc_clave as ceco FROM comprobaciones WHERE co_mi_tramite = ".$_POST['tramiteComprobacion'];
		}else if(isset($_POST['tramiteSolicitud'])){
			$sql = "SELECT sv_ceco_paga as ceco FROM solicitud_viaje WHERE sv_tramite = ".$_POST['tramiteSolicitud'];
		}		
		//error_log($sql);
		$res = $cnn->consultar($sql);
		while ($row = mysql_fetch_array($res)){
			echo $row["ceco"];
		}
	}
	
	// recibe valores para la conversion de la divisa
	if(isset($_POST['convierteDivisa']) || isset($_POST['divisa']) ){
		$cnn = new conexion();
		$sql = "SELECT CONVERT(".$_POST['convierteDivisa']."*div_tasa,DECIMAL(20,2)) AS 'Total'	
				FROM divisa
				WHERE div_nombre = '".$_POST['divisa']."'";
		//error_log($sql);
		$res = $cnn->consultar($sql);
		while ($row = mysql_fetch_array($res)){
			echo $row["Total"];
		}
	}
			
// // Aprobaremos lsa comprobaciones 
// if(isset($_POST['tramiteComp'])){
// 	$idT = $_POST['tramiteComp'];
// 	error_log("-------------->>>>>>>>>>>>>>>tramite: ".$idT);
// 	$cnn = new conexion();
// 	$tramite = new Tramite();
// 	$rutaAuto = new RutaAutorizacion();
// // 	$query=sprintf("DELETE FROM total_temp WHERE id_tramite = '%s'", $idT);
// // 	$cnn->ejecutar($query);
// 	$t_dueno = $rutaAuto->getDueno($idT);
	
// 	$tramite->Modifica_Etapa($idT, COMPROBACION_INVITACION_ETAPA_VALIDADO_POR_SUPERVISOR_FINANZAS, FLUJO_COMPROBACION_INVITACION, "2000", "");
	
// 	//Envia notificacion al Supervisor/Gerente de Finanzas de la solicitud de invitación ----------------------------------
// 	$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>APROBADA</strong> por el Supervisor de Finanzas.", $idT);
// 	$remitente = $t_dueno;
// 	$destinatario = "2000";
// 	$tramite->EnviaNotificacion($idT, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
	
// 	echo "realizado";
// }

if(isset($_POST['usuarioFinanzas'])){
	$usuarioTipoFinanzas=$_POST['usuarioFinanzas'];
	$tramite=$_POST['idtramite'];
	$montoComprobado = $_POST['mntComprobacion'];
	$dueno_act_nombre="";
	$tu_id=0;
	$rutaAutorizacion=new RutaAutorizacion();
	
	if($usuarioTipoFinanzas == "Supervisor de finanzas"){
		$rutaAutorizacion->AutorizarporSupervisorFinanzas($tramite, $montoComprobado);		
	}elseif($usuarioTipoFinanzas == "Gerente de finanzas"){		
		$rutaAutorizacion->AutorizarporGerenteFinanzas($tramite, $montoComprobado);			
	}
	
	echo "realizado";
}

if(isset($_POST['devolverObservaciones'])){
	$cnn = new conexion();
	$tramiteId=$_POST['idtramite'];
	$ObservacionesNuevas=$_POST['devolverObservaciones'];	
	$idusuario=$_POST['idUsuario'];
	$observacionesViejas="";
	$tramite=new Tramite();
	$tramite->Load_Tramite($tramiteId);
	$t_dueno = $tramite->Get_dato("t_dueno");
	
	$rutaAuto=new RutaAutorizacion();
	$agrup_usu = new AgrupacionUsuarios();
	
	// Anotar observaciones
	if($ObservacionesNuevas != ""){
		$tramite=new Tramite();
		$tramite->Load_Tramite($tramiteId);
		$t_dueno = $tramite->Get_dato("t_dueno");
			
		$query = sprintf("SELECT co_observaciones FROM comprobaciones WHERE co_mi_tramite='%s'",$tramiteId);
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$co_observaciones = $fila['co_observaciones'];
		$notificacion = new Notificacion();
		$observacionesNuevasCodificadas=utf8_decode($ObservacionesNuevas);
		$observaciones = $notificacion->anotaObservacion($t_dueno, $co_observaciones, $observacionesNuevasCodificadas, FLUJO_COMPROBACION, COMPROBACION_ETAPA_EN_APROBACION);
	
		$queryInsertaObs=sprintf("UPDATE comprobaciones SET co_observaciones='%s' WHERE co_mi_tramite='%s'",$observaciones,$tramiteId);
		$cnn->ejecutar($queryInsertaObs);
	}
	
	//Notificaciones
	$remitente = $t_dueno;
	//enviar la notificacion al empleado ----> t_iniciador
	$destinatario = $tramite->Get_dato("t_iniciador");
				
	$mensaje=sprintf("<strong>Finanzas</strong> ha realizado observaciones a la comprobacion <strong>%05s</strong>",$tramiteId);
	$tramite->EnviaNotificacion($tramiteId, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	//cambio de etapa
	$tramite->Modifica_Etapa($tramiteId,COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES,FLUJO_COMPROBACION, $destinatario,"");
	
}


if(isset($_POST['limpiarRegistros'])){
	$cnn = new conexion();	
		$queryLimpieza=sprintf("DELETE FROM total_temp WHERE TRUE;");
		$cnn->ejecutar($queryLimpieza);	
}

//Nos regresara el presupuesto que esta destinado a el CECO.
if(isset($_POST['cecoPresupuesto'])){
	$cnn = new conexion();
	$presupuestoDisponible=0;
	$cecoPresupuesto=$_POST['cecoPresupuesto'];
	
	$queryPresupuesto=sprintf("SELECT SUM(pp_presupuesto_disponible) AS 'pp_presupuesto_disponible' FROM periodo_presupuestal WHERE pp_ceco='%s'",$cecoPresupuesto);
	$rstPresupuesto=$cnn->consultar($queryPresupuesto);
	while ($fila = mysql_fetch_array($rstPresupuesto)){
		 $presupuestoDisponible=$fila["pp_presupuesto_disponible"];
	}
	echo json_encode($presupuestoDisponible);
}


if(isset($_POST['tramitePresupuesto'])){
	$cnn = new conexion();
	$tramitePresupuesto=$_POST['tramitePresupuesto'];
	$co_totalPresupuesto=$_POST['co_totalP'];
	$presupuesto=$_POST['presupuestoDisponible'];
	$mensajePresupuesto=$_POST['mensajePresupuesto'];
	$cecoExcendente=$_POST['cecoExcedente'];
	
	$DPPdisponible=0;
	$DPPutilizado=0;
	
	$indicador=$_POST['excedente'];
	if($indicador == 1){
		//error_log("total de la comprobacion".$co_totalPresupuesto);
		//error_log("presupuesto".$presupuesto);
		$diferenciaPresupuesto= $co_totalPresupuesto - $presupuesto;		
		$queryExceP=sprintf("INSERT INTO excepciones (ex_id,ex_mensaje,ex_diferencia,ex_solicitud,ex_comprobacion,ex_comprobacion_detalle) VALUES(DEFAULT,'%s',%s,%s,'%s',%s)",$mensajePresupuesto,$diferenciaPresupuesto,0,$tramitePresupuesto,0);
		$cnn->ejecutar($queryExceP);
	}/*elseif($indicador == 0){
		/*
	}*/	
}

if(isset($_POST['tramitePresupuestoFinanzas'])){
	$cnn = new conexion();
	$conceptoId=0;
	$conceptoValor=$_POST['tramitePresupuestoFinanzas'];
	$cecoPresupuestoId=$_POST['cecoPresupuestoFinanzas'];
	$totalPresupuestoConcepto=$_POST['totalPresupuestoFinanzas'];	
	//Tomamos el valor del concepto para tomar si id
	$queryConcepto="SELECT dc_id FROM cat_conceptosbmw WHERE cp_concepto='{$conceptoValor}'";
	$rstConcepto=$cnn->consultar($queryConcepto);
	while ($fila = mysql_fetch_array($rstConcepto)){
		$conceptoId=$fila['dc_id'];
	}
	
	$DPPdisponible=0;
	$DPPutilizado=0;
	
	$queryPresupuestoE=sprintf("SELECT pp_presupuesto_disponible,pp_presupuesto_utilizado FROM periodo_presupuestal WHERE pp_ceco='%s' AND pp_id_concepto='%s'",$cecoPresupuestoId,$conceptoId);
	//error_log("querys".$queryPresupuestoE);
	$rstPresupuestoE=$cnn->consultar($queryPresupuestoE);
	while ($fila = mysql_fetch_array($rstPresupuestoE)){
		$DPPdisponible=($fila["pp_presupuesto_disponible"]-$totalPresupuestoConcepto);
		$DPPutilizado=($fila["pp_presupuesto_utilizado"]+$totalPresupuestoConcepto);
	}
	
	
	$queryActualizaE=sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible=%s,pp_presupuesto_utilizado=%s WHERE pp_ceco='%s' AND pp_id_concepto='%s'",$DPPdisponible,$DPPutilizado,$cecoPresupuestoId,$conceptoId);
	//error_log("querys".$queryActualizaE);
	$cnn->ejecutar($queryActualizaE);
	
}

if(isset($_POST['getStatus'])){
	$cnn = new conexion();
	$sql = "SELECT IF(t_etapa_actual=".COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES.",1,0) AS result
			FROM tramites 
			WHERE t_id = ".$_POST['getStatus'];
	$res = $cnn->consultar($sql);
	$row = mysql_fetch_array($res);
	echo $row['result'];
}

if(isset($_POST['activarConcepto'])){
	$tramiteActiva=$_POST['activarConcepto'];
	//error_log("idtramite".$tramiteActiva);
	$cnn = new conexion();
	$TipoViaje="";
	$tramite="";
	$numeroDias=0;
	$comboConceptos="";
	$_arreglo=array();
	
	$queryActiva="SELECT SUM(svi_dias_viaje) AS svi_dias_viaje FROM sv_itinerario WHERE svi_solicitud =(SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = {$tramiteActiva})";
	//error_log($queryActiva);
		
	//obtenemos el id del tramite en cuestion
	$query_tramite="SELECT co_tramite FROM comprobaciones WHERE co_mi_tramite =  {$tramiteActiva}";
	$res = $cnn->consultar($query_tramite);
	$row = mysql_fetch_array($res);
	$tramite = $row['co_tramite'];
	if($tramite == ""){
		$tramite = $tramiteActiva;
		//tipo de viaje en cuestion
		$queryTV="SELECT sv_viaje FROM solicitud_viaje
		WHERE sv_tramite ={$tramite}";
		$res = $cnn->consultar($queryTV);
		$row = mysql_fetch_array($res);
		$TipoViaje = $row['sv_viaje'];
		
		//Numero de dias en cuestion
		$queryActiva="SELECT SUM(svi_dias_viaje) AS svi_dias_viaje FROM sv_itinerario
		WHERE svi_solicitud =(SELECT sv_id FROM solicitud_viaje
		WHERE sv_tramite = {$tramite})";
		
		$rstActivaDias=$cnn->consultar($queryActiva);
		$row = mysql_fetch_array($rstActivaDias);
		$numeroDias=$row['svi_dias_viaje'];
				
	}else{
		//tipo de viaje en cuestion
		$queryTV="SELECT sv_viaje FROM solicitud_viaje
		WHERE sv_tramite =(SELECT co_tramite FROM comprobaciones
		WHERE co_mi_tramite = {$tramite})";
		$res = $cnn->consultar($queryTV);
		$row = mysql_fetch_array($res);
		$TipoViaje = $row['sv_viaje'];
		
		//error_log("dias".$numeroDias);
		//Numero de dias en cuestion
		$queryActiva="SELECT SUM(svi_dias_viaje) AS svi_dias_viaje FROM sv_itinerario
		WHERE svi_solicitud =(SELECT sv_id FROM solicitud_viaje
		WHERE sv_tramite = (SELECT co_tramite FROM comprobaciones
		WHERE co_mi_tramite = {$tramite}))";
		$rstActivaDias=$cnn->consultar($queryActiva);
		$row = mysql_fetch_array($rstActivaDias);
		$numeroDias=$row['svi_dias_viaje'];		
	}		
	
		//Reconocer el tipo de viaje que es Redondo = +1 dia.
		if($TipoViaje == 'Redondo'){
			$numeroDias = $numeroDias + 1;
		}		
		//error_log("dias".$numeroDias);
		
		if($numeroDias != ""){
			//Se validara el numero de dias para poder activar el concepto de tipo lavaderia
			//error_log("dias".$numeroDias);
			if($numeroDias < 7){
				//Se toma el concepto de tipo lavanderia
				//Se toma el id del concepto con su nombre, para ser agregado mas adelante al combo de conceptos.
				$queryConcepto = sprintf("SELECT dc_id, cp_concepto FROM cat_conceptosbmw WHERE cp_activo = 1 AND cp_concepto='Lavandería'");
				$rstConcepto = $cnn->consultar($queryConcepto);
				$i=0;
				while ($fila = mysql_fetch_assoc($rstConcepto)) {
					$_arreglo[]=array(
							'dc_id' =>$fila['dc_id'],
							'cp_concepto' =>$fila['cp_concepto']
					);
					$i++;
				}				
			}			
		}	
		
	echo json_encode($_arreglo);
}

if(isset($_POST['tramiteConcepto'])){
	$conexion = new conexion();
	$parametro = new Parametro();	
	$idTramite = $_POST['tramiteConcepto'];	
	
	$sql = "SELECT sv_viaje 
			FROM solicitud_viaje 
			WHERE sv_tramite = $idTramite";
	$res = $conexion->consultar($sql);
	$row = mysql_fetch_assoc($res);
	$svViaje = $row['sv_viaje'];
	
	if($svViaje == 'Sencillo' || $svViaje == 'Redondo')
		$datosViaje = $parametro->datosViajeSencilloRedondo($idTramite);		
	else
		$datosViaje = $parametro->datosViajeMultidestino($idTramite);	
	// 	"Dato1" (Tipo de viaje) = $datosConceptoViaje[0]
	// 	"Dato2" (Region) = $datosConceptoViaje[1]
	// 	"Dato3" (Dias de viaje) = $datosConceptoViaje[2]	
	echo json_encode($datosViaje);	
}

if(isset($_POST['mensajeAlimentos'])){
	$cnn = new conexion();
	$mensaje=$_POST['mensajeAlimentos'];
	$dif=$_POST['diferenciaExcepcionAHL'];
	
	$queryExcepciones=sprintf("insert into excepciones
                            (
                                ex_id,
								ex_mensaje,
                                ex_diferencia,
								ex_solicitud,
								ex_comprobacion,
								ex_comprobacion_detalle
                            )
                            VALUES(
                                default, 
                                '%s',
                                %s,
                                %s,
                                %s,
                                %s
                            )",
							$mensaje,
							$dif,
							'0',
							$comprobacionViaje,
							'0');
                            
}

if(isset($_POST['idTramiteExc'])){
	$idTramiteExcEdit = $_POST['idTramiteExc'];
	$_arreglo = array();
	$cnn = new conexion();
	$queryIdTramiteEdit="SELECT IFNULL(ex_mensaje,'') AS 'ex_mensaje',IFNULL(ex_diferencia,'') AS 'dif_exc' FROM comprobaciones 
	INNER JOIN detalle_comprobacion
	ON dc_comprobacion = co_id
	LEFT JOIN excepciones
	ON ex_comprobacion_detalle = dc_id
	WHERE co_tramite = (SELECT co_tramite FROM comprobaciones WHERE co_mi_tramite = {$idTramiteExcEdit})";
	//error_log($queryIdTramiteEdit);
	$rstEdit = $cnn->consultar($queryIdTramiteEdit);
	
	while ($fila = mysql_fetch_assoc($rstEdit)){
			$_arreglo[]=array(
			'ex_mensaje'=>utf8_encode($fila['ex_mensaje']),
			'dif_exc'=>utf8_encode($fila['dif_exc'])			
			);		
	}
	echo json_encode($_arreglo);
}

if(isset($_POST["proveedorRFC"])){
	$cnn = new conexion();	
	$proveedorNombre = scapeUTF8(UTF8_decode($_POST['proveedorNombre']));
	$sql = "SELECT COUNT(pro_id) AS result
			FROM proveedores 
			WHERE pro_rfc = '".$_POST['proveedorRFC']."' 
			AND pro_proveedor LIKE '$proveedorNombre'";
	$res = $cnn->consultar($sql);
	$row = mysql_fetch_assoc($res);
	echo $row["result"];
}

//funcion que nos permitira activar el campo de comentarios si el parametro ha sido excedido
if(isset($_POST["tramiteComentario"])){	
	$tramiteIdComentario=$_POST['tramiteComentario'];
	$conceptoIdComentario=$_POST['conceptoIdComentario'];
	$cnn = new conexion();
	$parameter = new Parametro();
	$conceptoC = new Concepto();
	$sv_viaje = "";
	$datosConcepto = array();
	$nombreParametro = "";
	$tramite = 0;
	$idConceptosNI=0;
	$numeroDias = 0;
	//obtenemos el id del tramite en cuestion
	$query_tramite="SELECT co_tramite FROM comprobaciones WHERE co_mi_tramite =  {$tramiteIdComentario}";
	$res = $cnn->consultar($query_tramite);
	$row = mysql_fetch_array($res);
	$tramite = $row['co_tramite'];
	if($tramite == ""){
		$tramite = $tramiteIdComentario;
	}	
	//seleccionamos el viaje (Sencillo,redondo,Multidestinos)
	$query_viaje="SELECT sv_viaje FROM solicitud_viaje WHERE sv_tramite = {$tramite} ";
	//error_log($query_viaje);
	$rst=$cnn->consultar($query_viaje);
	
	while ($fila = mysql_fetch_assoc($rst)) {
		$sv_viaje = $fila['sv_viaje'];
	}
	//se obtinen a continuacion 3 datos importantes ( Tipo de viaje , La region y  Los dias de viaje )
	if($sv_viaje == 'Sencillo' || $sv_viaje == 'Redondo'){
		$datosConcepto=$parameter->datosViajeSencilloRedondo($tramite);
	}else{
		//cuando sea multidestinos se tomara los datos del ultimo itinerario
		$datosConcepto=$parameter->datosViajeMultidestino($tramite);
	}
	
	//El orden de los datos obtenidos es el siguiente:
	// 	"Dato1" (Tipo de viaje) = $datosConcepto[0]
	// 	"Dato2" (Region) = $datosConcepto[1]
	// 	"Dato3" (Dias de viaje) = $datosConcepto[2]
	
	//obtiene el numero de dias de la solicitud en cuestion.
	if($sv_viaje == 'Redondo'){
		$numeroDias = $datosConcepto[2] + 1;
	}else{
		$numeroDias = $datosConcepto[2];
	}
	//dependiendo del id del concepto le daremos sus valores (Nacional e internacional)
	
	if($datosConcepto[0] == 'Nacional'){		
		if($conceptoIdComentario == $conceptoC->GetIdAlimentos()){ //Alimentos
			$nombreParametro="TasaAlimentosDiariaNacional";	
		}elseif($conceptoIdComentario == $conceptoC->GetIdHotel()){//Hospedaje
			$nombreParametro="TasaHospedajeNacional";	
		}elseif($conceptoIdComentario == $conceptoC->GetIdLavanderia()){//Lavanderia
			$nombreParametro="MinimoDiarioLavanderiaNacional";	
		}	
	}else if($datosConcepto[0]='Intercontinental' || $datosConcepto[0]='Continental'){	
	if($conceptoIdComentario == $conceptoC->GetIdAlimentos()){ //Alimentos
			$nombreParametro="TasaAlimentosDiariaInternacional";	
		}elseif($conceptoIdComentario == $conceptoC->GetIdHotel()){//Hospedaje
			$nombreParametro="TasaHospedajeInternacional";	
		}elseif($conceptoIdComentario == $conceptoC->GetIdLavanderia()){//Lavanderia
			$nombreParametro="MinimoDiarioLavanderiaInternacional";	
		}	
	}
	
	//se reliza el calculo del monto maximo de Tasa diaria de los conceptos (Nacional/Internacional)
	$idConceptosNI=$parameter->calculoTasaConceptos($conceptoIdComentario, $nombreParametro);
	
	//Se obtiene la cantidad y la divisa correspondiente
	$query=sprintf("SELECT pr_cantidad,div_id FROM parametro_regionbmw WHERE re_id='%s' AND p_id='%s'",$datosConcepto[1],$idConceptosNI);
	//error_log("query".$query);
	$var=$cnn->consultar($query);
	$i=0;
	while ($fila = mysql_fetch_assoc($var)) {
		$_arreglo[]=array(
				'pr_cantidad' =>$fila['pr_cantidad'],
				'div_id' =>$fila['div_id'],
				'dias_viaje' => $numeroDias
		);
		$i++;
	}
	//se regresa el monto calculado
	echo json_encode($_arreglo);
}

if(isset($_POST['detalle'])){
	$cnn = new conexion();
	
	$montoAprobado = $_POST['monto'];
	$ivaAprobado = $_POST['iva'];
	$propinaAprobada = $_POST['propina'];
	$totalAprobado = $_POST['total'];
	$conceptoReasignado = $_POST['concepto'];
	$detalleComprobacion = $_POST['detalle'];
	$tabla = "detalle_comprobacion_invitacion";
	$tabla2 = "comprobacion_invitacion";
	$Comprobacion = $_POST['comprobacion'];
	$hospedaje = "";
	
// 	error_log("montoAprobado antes de str_replace: ".$montoAprobado);
// 	error_log("ivaAprobado antes de str_replace: ".$ivaAprobado);
// 	error_log("propinaAprobada antes de str_replace: ".$propinaAprobada);
// 	error_log("totalAprobado antes de str_replace: ".$totalAprobado);
	
	$montoAprobado = str_replace(',', '', $montoAprobado);
	$ivaAprobado = str_replace(',', '', $ivaAprobado);
	$propinaAprobada = str_replace(',', '', $propinaAprobada);
	$totalAprobado = str_replace(',', '', $totalAprobado);
	
// 	error_log("montoAprobado despues de str_replace: ".$montoAprobado);
// 	error_log("ivaAprobado despues de str_replace: ".$ivaAprobado);
// 	error_log("propinaAprobada despues de str_replace: ".$propinaAprobada);
// 	error_log("totalAprobado despues de str_replace: ".$totalAprobado);
	
	if($Comprobacion == 1){
		$query = sprintf("SELECT dc_id FROM cat_conceptosbmw WHERE cp_concepto = '%s'", $conceptoReasignado);
		//error_log($query);
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$conceptoReasignado = $fila['dc_id'];
		
		$tabla = "detalle_comprobacion";
		$tabla2 = "comprobaciones";
		$hospedajeAprobado = $_POST['hospedaje'];
		$hospedaje = sprintf(" dc_imp_hospedaje = '%s',", $hospedajeAprobado);
	}
	
	$sql = sprintf("UPDATE %s SET dc_monto = '%s', dc_iva = '%s', dc_propinas = '%s', %s dc_total = '%s', dc_concepto = '%s' WHERE dc_id = '%s'",
			$tabla, $montoAprobado, $ivaAprobado, $propinaAprobada, $hospedaje, $totalAprobado, $conceptoReasignado, $detalleComprobacion);
	error_log($sql);
	$cnn->ejecutar($sql);
	
	$query = sprintf("SELECT co_id FROM %s JOIN %s ON dc_comprobacion = co_id WHERE dc_id = '%s'", $tabla2, $tabla, $detalleComprobacion);
	error_log($query);
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$co_id = $fila['co_id'];
	
	if($Comprobacion == 1){ 
		// Solo para Comprobación de Viaje se debe tomar el monto total de la comprobación después del recalculo del reembolso 
		$totalAprobado = $_POST['totalComprobado'];
		$totalAprobado = str_replace(',', '', $totalAprobado);
	}
	
	$sql = sprintf("UPDATE %s SET co_total = '%s' WHERE co_id = '%s'", $tabla2, $totalAprobado, $co_id);
	error_log($sql);
	$cnn->ejecutar($sql);
	
}

//Guardar la Tasa introducida por Finanzas
if(isset($_POST['tasaUSD']) && isset($_POST['tasaEUR'])){
	$tasaDolar = $_POST['tasaUSD'];
	$tasaEuro = $_POST['tasaEUR'];
	$tramiteComp = $_POST['tramite'];
	
	//error_log("tasaDolar antes de str_replace: ".$tasaDolar);
	//error_log("tasaEuro antes de str_replace: ".$tasaEuro);
	
	$tasaDolar = str_replace(',', '', $tasaDolar);
	$tasaEuro = str_replace(',', '', $tasaEuro);
	
	//error_log("tasaDolar despues de str_replace: ".$tasaDolar);
	//error_log("tasaEuro despues de str_replace: ".$tasaEuro);
	
	$cnn = new conexion();
	$sql = sprintf("UPDATE comprobaciones SET co_tasa_USD = '%s', co_tasa_EUR = '%s' WHERE co_mi_tramite = '%s';", $tasaDolar, $tasaEuro, $tramiteComp);
	//error_log($sql);
	$cnn->ejecutar($sql);
}

// Verificar ei el concepto ha sido recalculado
if(isset($_POST['conceptoComprobacionAMEX'])){
	$conceptoAMEX = $_POST['conceptoComprobacionAMEX'];
	$tramite = new Tramite();
	$encontrado = $tramite->verificarDetalleRecalculado($conceptoAMEX);
	//error_log("----->>>>>>>>>>>".$encontrado);
	echo $encontrado;
}

// Seleccionar el cargo AMEX según su Id
if(isset($_POST['idcargoAMEX'])){
	$id_cargoAmex = $_POST['idcargoAMEX'];
	$_arreglo = array();
	$cnn = new conexion();
	$query_cargostc = sprintf("SELECT idamex,DATE_FORMAT(fecha_cargo,'%%d/%%m/%%Y') AS fecha,concepto,CONVERT(monto,DECIMAL(10,2)) AS monto,moneda_local FROM amex WHERE idamex = '%s'", $id_cargoAmex);
	//error_log($query_cargostc);
	$rst2 = $cnn->consultar($query_cargostc);
	$i=0;
	$j=0;
	while ($filaa = mysql_fetch_assoc($rst2)){
		$data=sprintf("%s:%s - %s - %s %s ",
				$filaa['idamex'], 
				$filaa['fecha'], 
				utf8_encode($filaa['concepto']), 
				$filaa['monto'], 
				$filaa['moneda_local']);
		$_arreglo[$i] = $data;
		$i++;
	}
	echo json_encode($_arreglo);
}

// Actualizaremos a cero el estatus del cargo cuando el la suma de los gastos sea menor al cargo AMEX
if(isset($_POST['comprobacionTram'])){
	$comprobacionTram = $_POST['comprobacionTram'];
	$cnn = new conexion();
	$query = sprintf("SELECT dc_idamex_comprobado FROM detalle_comprobacion 
		JOIN comprobaciones ON co_id = dc_comprobacion
		WHERE co_mi_tramite = '%s'", $comprobacionTram);
	//error_log($query);
	$rst = $cnn->consultar($query);
	$aux = array();
	while($datos=mysql_fetch_assoc($rst)){
		array_push($aux,$datos);
	}
	
	foreach($aux as $datosAux){
		// Obtenemos el id del cargo AMEX
		$idAmex = $datosAux['dc_idamex_comprobado'];
		
		// Obtenemos el montoAmex asociado al cargo
		$querytotalAmex = sprintf("SELECT montoAmex FROM amex WHERE idamex = '%s'", $idAmex);
		//error_log($querytotalAmex);
		$rsttA = $cnn->consultar($querytotalAmex);
		$fila = mysql_fetch_assoc($rsttA);
		$totalAmex = $fila["montoAmex"];
		
		// Obtenemos la suma del total de los gastos asociados al cargo AMEX
		$querytotalGastosAmex = sprintf("SELECT SUM(dc_total) AS montoComprobado FROM detalle_comprobacion WHERE dc_idamex_comprobado = '%s'", $idAmex);
		//error_log($querytotalGastosAmex);
		$rstGA = $cnn->consultar($querytotalGastosAmex);
		$fila = mysql_fetch_assoc($rstGA);
		$totalGastosAmex = $fila["montoComprobado"];
		
		//Actualizamos el estado a 0 si la suma de los cargos es menor al cargo amex, para que el cargo amex pueda ser seleccionado nuevamente.
		if($totalGastosAmex < $totalAmex){
			$sql = sprintf("UPDATE amex SET estatus = '0' WHERE idamex = '%s'", $idAmex);
			//error_log($sql);
			$cnn->ejecutar($sql);
		}
	}
	echo "realizado";
}

// Seleccionar el cargo AMEX según su Id solo para Comprobaciones de Invitación 
if(isset($_POST['cargoAmexseleccionado'])){
	$id_cargoAmex = $_POST['cargoAmexseleccionado'];
	$_arreglo = array();
	$cnn = new conexion();
	$query_cargostc = sprintf("SELECT idamex,DATE_FORMAT(fecha_cargo,'%%d/%%m/%%Y') AS fecha,concepto,CONVERT(monto,DECIMAL(10,2)) AS monto,moneda_local FROM amex WHERE idamex = '%s'", $id_cargoAmex);
	//error_log($query_cargostc);
	$rst2 = $cnn->consultar($query_cargostc);
	$i=0;
	$j=0;
	while ($filaa = mysql_fetch_assoc($rst2)){
		$data=sprintf("%s:%s - %s - %s %s ",
				$filaa['idamex'],
				$filaa['fecha'],
				utf8_encode($filaa['concepto']),
				$filaa['monto'],
				$filaa['moneda_local']);
		$_arreglo[$i] = $data;
		$i++;
	}
	echo json_encode($_arreglo);
}
?>
