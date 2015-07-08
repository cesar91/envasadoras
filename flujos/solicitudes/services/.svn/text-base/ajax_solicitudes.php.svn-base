<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/functions/Tramite.php");

if (isset($_POST['no_region'])) {
    $no_region = $_POST['no_region'];

    $cnn = new conexion();
    
   if($no_region=="2"){
       $query = sprintf("SELECT re_id, re_nombre FROM cat_regiones WHERE re_id='2' OR re_id='4'");
   }
   if($no_region=="3"){
       $query = sprintf("SELECT re_id, re_nombre FROM cat_regiones WHERE re_id='3'");
   }
    
    $rst2 = $cnn->consultar($query);
	$opciones = '<option value="-1">Seleccione ...</option>';
	
	while($fila = mysql_fetch_assoc($rst2)){
		$opciones .= '<option value="'.$fila["re_id"].'">'.utf8_encode($fila["re_nombre"]).'</option>';
	}
	//error_log("--->>Opciones: ".$opciones);
	echo json_encode($opciones);
}

// seleccionar la divisa de cierta region
if (isset($_POST['idRegion'])){
    $idRegion = $_POST['idRegion'];

    $cnn = new conexion();
	$query_cargostc = sprintf("select div_nombre from divisa inner join cat_regionesbmw on divisa.div_id = cat_regionesbmw.re_div_id where re_id = '%s' ", $idRegion);
    $rst2 = $cnn->consultar($query_cargostc);
	$i=0;
    while ($filaa = mysql_fetch_assoc($rst2)){
        $data=sprintf($filaa['div_nombre']);
        $_arreglo[$i]=$data;
        $i++;
    }
	echo json_encode($_arreglo);
}

//Selecciona el monto de politica y la divisa del concepto de comidas
if (isset($_POST['re_id'])) {
	$re_id = $_POST['re_id'];

	$dc_id = 6; //El 6 es el ID del concepto de comidas
	
	$cnn = new conexion();
	$ids = array("6","30","31","32"); //6 - Concepto comidas : 30 - Alimentacion desayuno : 31 - Alimentacion comida : 32 - Alimentacion cena
	
	for($i=0;$i<4;$i++){
		$query_card_gastos = sprintf("SELECT PRBMW.PR_CANTIDAD,
D.DIV_NOMBRE,
D.DIV_TASA
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
AND CC.DC_ID = '%s'", $re_id, $ids[$i]);
		$rst2 = $cnn->consultar($query_card_gastos);
		$filaa = mysql_fetch_assoc($rst2);

		$_arreglo[3*$i]=$filaa['PR_CANTIDAD'];
		$_arreglo[3*$i+1]=$filaa['DIV_NOMBRE'];
		$_arreglo[3*$i+2]=$filaa['DIV_TASA'];
	}
	
	echo json_encode($_arreglo);
}

//Selecciona el monto de politica y la divisa del concepto de comidas
if (isset($_POST['dc_id']) && isset($_POST['region_id'])) {
	$dc_id = $_POST['dc_id'];
	$re_id = $_POST['region_id'];
    //Amex corporativa Gastos
	$cnn = new conexion();
	$query_card_gastos = sprintf("SELECT PRBMW.PR_CANTIDAD,
D.DIV_NOMBRE,
D.DIV_TASA
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
	$rst2 = $cnn->consultar($query_card_gastos);
	$filaa = mysql_fetch_assoc($rst2);

	$_arreglo[0]=$filaa['PR_CANTIDAD'];
	$_arreglo[1]=$filaa['DIV_NOMBRE'];
	$_arreglo[2]=$filaa['DIV_TASA'];

	echo json_encode($_arreglo);
}

// seleccionar la tasa de la divisa
if (isset($_POST['divisa'])) {
    $divisa = $_POST['divisa'];

    $cnn = new conexion();
    $query_cargostc = sprintf("SELECT DIV_TASA FROM divisa WHERE DIV_ID = '%s' or DIV_NOMBRE = '%s' ", $divisa, $divisa);
    $rst2 = $cnn->consultar($query_cargostc);
	$i=0;
    while ($filaa = mysql_fetch_assoc($rst2)){
        $data=sprintf($filaa['DIV_TASA']);
        $_arreglo[$i]=$data;
        $i++;
    }
	
	echo json_encode($_arreglo);
}


//monto solicitado
if (isset($_POST['mntsolic'])) {
	$idsolicitud = $_POST['mntsolic'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT si_monto FROM solicitud_invitacion WHERE si_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['si_monto'];
	}
	echo $dato;
}
//monto en pesos
if (isset($_POST['mntsolmn'])) {
	$idsolicitud = $_POST['mntsolmn'];
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
//divisa
if (isset($_POST['divsol'])) {
	$idsolicitud = $_POST['divsol'];
	$cnn = new conexion();
	$dato="";
	$dato1=0;
	$query_lugar = sprintf("SELECT si_divisa FROM solicitud_invitacion WHERE si_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = $fila['si_divisa'];
	}
	switch($dato){
		case "MXN":$dato1=1;break;
		case "USD":$dato1=2;break;
		case "EUR":$dato1=3;break;
	}
	echo $dato1;
}
//observaciones
if (isset($_POST['observ'])) {
	$idsolicitud = $_POST['observ'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT si_observaciones FROM solicitud_invitacion WHERE si_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['si_observaciones']);
	}
	echo $dato;
}
if (isset($_POST['motivo'])) {
	$idsolicitud = $_POST['motivo'];
	$cnn = new conexion();
	$dato="";
	$query_lugar = sprintf("SELECT si_motivo FROM solicitud_invitacion WHERE si_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['si_motivo']);
	}
	echo $dato;
}
//Elimnar Hotel
if(isset($_POST['hotel'])){
	$id_hotel = $_POST['hotel'];
	$idItinerarioHotel = $_POST['itinerarioHotel'];
	$cnn = new conexion();
	$dato="";
	$query = sprintf("DELETE FROM hotel WHERE h_hotel_id ='%s' AND svi_id='%s'",$id_hotel,$idItinerarioHotel);
    //error_log($query);
	$cnn->ejecutar($query);
	echo true;	
}
//Actualizar los datos de avion de los itinerarios de una solicitud
if (isset($_POST['id_sol_viaje'])) {
	$costoAvionOriginal=0;
	$etapaAvion=0;
	$idTramiteSolAvion=0;
	$tramite = new Tramite();
	$idsolicitud = $_POST['id_sol_viaje'];
	$idTramiteAvion = $_POST['idTramiteAvion'];
	
	$val1 = $_POST['val1'];
	$val2 = $_POST['val2'];
	$val3 = $_POST['val3'];
	$val4 = $_POST['val4'];
	$val5 = $_POST['val5'];
	$val6 = $_POST['val6'];
	//fechas
	$val7 = $_POST['val7'];	
	$val8 = $_POST['val8'];
	
	// Tranformacion de formato de fecha
	if($val7 != "(NULL)"){
		$date=explode("/",$val7);
		$val7=$date[2]."-".$date[1]."-".$date[0];
	}
	
	if($val8 != "(NULL)"){
		$date2=explode("/",$val8);
		$val8=$date2[2]."-".$date2[1]."-".$date2[0];
	}
	
	$val9 = $_POST['val9'];		
	$cnn = new conexion();
	
	//-----Obtener monto originar de avion 		
	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramiteAvion);
	$etapaAvion=$tramite->Get_dato('t_etapa_actual');
	
	$tramite->CalculoTotalAvion($idsolicitud,$val4);
	
	$dato="";		
	$query = sprintf("UPDATE sv_itinerario SET
		svi_monto_vuelo='%s',
		svi_iva='%s',
		svi_tua='%s',
		svi_monto_vuelo_cotizacion='%s',
		svi_aerolinea='%s',
		svi_tipo_aerolinea='%s',
		svi_fecha_salida_avion='%s',
		svi_fecha_regreso_avion='%s'						   
		WHERE svi_solicitud='%s'",
		$val1,
		$val2,
		$val3,
		$val4,
		utf8_decode($val5),
		$val6,
		$val7,
		$val8,
		$idsolicitud);
	//error_log($query);
	$cnn->ejecutar($query);
	echo true;
}
//Actualizar los datos de auto de los itinerarios de una solicitud
if (isset($_POST['id_auto'])) {
	$idItinerario = $_POST['id_auto'];
	$val1 = $_POST['val1'];
	$val2 = $_POST['val2'];
	$val3 = $_POST['val3'];
	$val4 = $_POST['val4'];
	$val5 = $_POST['val5'];
	$cnn = new conexion();
	$dato="";
	$query = sprintf("UPDATE sv_itinerario SET 
			svi_empresa_auto='%s', 
			svi_tipo_auto='%s', 
			svi_dias_renta='%s', 
			svi_total_auto= '%s', 
			svi_total_pesos_auto='%s' 
			WHERE svi_id='%s'", 
			$val1, 
			$val2, 
			$val3, 
			$val4, 
			$val5, 
			$idItinerario);
	//error_log($query);
	$cnn->ejecutar($query);
	echo true;
}

//AJAX PARA SOLICITUDES DE VIAJE

//Obtener el motivo
if (isset($_POST['motivo22'])) {
	$idsolicitud = $_POST['motivo22'];
	$cnn = new conexion();
	$dato=array();
	//$dato="";
	$query_lugar = sprintf("SELECT sv_motivo, sv_viaje, sv_anticipo FROM solicitud_viaje WHERE sv_tramite='%s'", $idsolicitud);
	//error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
			'sv_motivo' =>utf8_encode($fila['sv_motivo']),
			'sv_viaje' =>$fila['sv_viaje'],
			'sv_anticipo' =>$fila['sv_anticipo']
		);
	}
	echo json_encode($_arreglo);
}
//Obtener aviones
if (isset($_POST['datos_avion'])) {
	$idTramite = $_POST['datos_avion'];
	$cnn = new conexion();
	$dato=array();
	$query ="SELECT svi_solicitud,svi_tipo_viaje, sv_viaje, svi_aerolinea, 
				DATE_FORMAT(svi_fecha_salida_avion,'%d/%m/%Y') AS 'svi_fecha_salida_avion', 
				IF(sv_viaje = 'Multidestinos',(SELECT DATE_FORMAT(MAX(svi_fecha_salida),'%d/%m/%Y') FROM sv_itinerario WHERE svi_solicitud='".$idTramite."'),DATE_FORMAT(svi_fecha_regreso_avion,'%d/%m/%Y')) AS 'svi_fecha_regreso_avion', 
				svi_monto_vuelo, svi_iva, 
				svi_tua, svi_monto_vuelo_cotizacion,svi_tipo_aerolinea, se_nombre
				FROM sv_itinerario AS svi
				INNER JOIN solicitud_viaje AS sv ON (sv.sv_id = svi.svi_solicitud) 
				INNER JOIN servicios AS se ON (se.se_id = svi.svi_tipo_aerolinea)
				WHERE svi.svi_solicitud = ".$idTramite." GROUP BY svi.svi_id;";
	//error_log("--->>Datos de avión: ".$query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'svi_solicitud'					=>$fila['svi_solicitud'],
		'svi_tipo_viaje'				=>$fila['svi_tipo_viaje'],
		'sv_viaje'						=>$fila['sv_viaje'],		
		'svi_aerolinea'					=>utf8_encode($fila['svi_aerolinea']),
		'svi_fecha_salida_avion'		=>$fila['svi_fecha_salida_avion'],
		'svi_fecha_regreso_avion'		=>$fila['svi_fecha_regreso_avion'],
		'svi_monto_vuelo'				=>$fila['svi_monto_vuelo'],
		'svi_iva'						=>$fila['svi_iva'],
		'svi_tua'						=>$fila['svi_tua'],
		'svi_monto_vuelo_cotizacion'	=>$fila['svi_monto_vuelo_cotizacion'],
		'svi_tipo_aerolinea'			=>$fila['svi_tipo_aerolinea'],
		'se_nombre'						=>utf8_encode($fila['se_nombre']));
	}
	//error_log("--->> ".print_r($_arreglo, true));
	echo json_encode($_arreglo);
}
//Obtener los itinerarios
if (isset($_POST['itinerarios'])) {
	$idTramite = $_POST['itinerarios'];
	$cnn = new conexion();
	$dato=array();
	$_arreglo=array();
	//$dato="";
	$query = sprintf("select
		svi.svi_id,
		svi_destino,
		svi_origen,
		svi_tipo_transporte,
		svi_tipo_viaje,
		svi_region,
		re_nombre,
		date_format(svi_fecha_salida, '%s') as svi_fecha_salida,
		date_format(svi_fecha_llegada, '%s') as svi_fecha_llegada,
		svi_horario_salida,
		svi_horario_llegada,
		svi_kilometraje,
		if(svi_hotel_agencia=1,'true','false') as svi_hotel_agencia,
		if(svi_renta_auto_agencia=1,'true','false') as svi_renta_auto_agencia,
		if(svi_hotel=1,'true','false') as svi_hotel,
		if(svi_renta_auto=1,'true','false') as svi_renta_auto,
		sum(if(h_noches,h_noches,0)) as h_noches,
		FORMAT(sum(if(h_total_pesos,h_total_pesos,0)),2) as h_total_pesos,
		/*Datos de auto*/
		svi_empresa_auto,
		FORMAT(svi_costo_auto,2) as svi_costo_auto,
		div_nombre as svi_divisa_auto,
		svi_tipo_auto,
		FORMAT(svi_total_auto,2) as  svi_total_auto,
		svi_dias_renta,
		FORMAT(svi_total_pesos_auto,2) AS svi_total_pesos_auto,
		svi_medio

		from sv_itinerario as svi
		inner join solicitud_viaje as sv
		on svi.svi_solicitud = sv.sv_id
		inner join tramites as t
		on sv.sv_tramite = t.t_id
		inner join cat_regionesbmw as crbmw
		on svi.svi_region = crbmw.re_id
		left join divisa as d
		on d.div_id = svi.svi_divisa_auto
		left join hotel as h
		on svi.svi_id = h.svi_id
		where t.t_id = '%s'
		group by svi.svi_id;",
		"%d/%m/%Y", "%d/%m/%Y", $idTramite);
	//error_log($query);
	$rst = $cnn->consultar($query);
	
/*	while($arr=mysql_fetch_assoc($rst)){
		array_push($dato,$arr);
	}
	echo json_encode($dato);*/
	
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'svi_id'				=>$fila['svi_id'],
		'svi_destino'			=>utf8_encode($fila['svi_destino']),
		'svi_origen'			=>utf8_encode($fila['svi_origen']),
		'svi_tipo_transporte'	=>utf8_encode($fila['svi_tipo_transporte']),
		'svi_tipo_viaje'		=>$fila['svi_tipo_viaje'],
		'svi_region'			=>$fila['svi_region'],
		're_nombre'				=>$fila['re_nombre'],
		'svi_fecha_salida'		=>$fila['svi_fecha_salida'],
		'svi_fecha_llegada'		=>$fila['svi_fecha_llegada'],
		'svi_horario_salida'	=>utf8_encode($fila['svi_horario_salida']),
		'svi_horario_llegada'	=>utf8_encode($fila['svi_horario_llegada']),
		'svi_kilometraje'		=>$fila['svi_kilometraje'],
		'svi_hotel_agencia'		=>$fila['svi_hotel_agencia'],
		'svi_renta_auto_agencia'=>$fila['svi_renta_auto_agencia'],
		'svi_hotel'				=>$fila['svi_hotel'],
		'svi_renta_auto'		=>$fila['svi_renta_auto'],
		'h_noches'				=>$fila['h_noches'],
		'h_total_pesos'			=>$fila['h_total_pesos'],
		//Datos de auto
		'svi_empresa_auto'		=>utf8_encode($fila['svi_empresa_auto']),
		'svi_costo_auto'		=>$fila['svi_costo_auto'],
		'svi_divisa_auto'		=>$fila['svi_divisa_auto'],
		'svi_tipo_auto'			=>utf8_encode($fila['svi_tipo_auto']),
		'svi_total_auto'		=>$fila['svi_total_auto'],
		'svi_dias_renta'		=>$fila['svi_dias_renta'],
		'svi_total_pesos_auto'	=>$fila['svi_total_pesos_auto'],
		'svi_medio'				=>utf8_encode($fila['svi_medio'])
		);
	}
	echo json_encode($_arreglo);
}

//Obtener los hoteles
if (isset($_POST['hoteles'])) {
	$idItinerario = $_POST['hoteles'];
	$cnn = new conexion();
	$dato=array();
	$_arreglo = array();
	//$dato="";
	$query = sprintf("SELECT h_hotel_id,
		h.div_id as div_id,
		FORMAT(h_total_pesos,2) as h_total_pesos,
		FORMAT(h_total,2) as h_total,
		FORMAT(h_subtotal,2) as h_subtotal,
		h_nombre_hotel,
		h_noches,
		h_no_reservacion,
		FORMAT(h_iva,2) as h_iva,
		DATE_FORMAT(h_fecha_salida, '%s') as h_fecha_salida,
		DATE_FORMAT(h_fecha_llegada, '%s') as h_fecha_llegada,
		FORMAT(h_costo_noche,2) as h_costo_noche,
		h_comentarios, 
		h_ciudad, 
		h_tipo_hotel, 
		se_nombre, 
		div_nombre 
		FROM hotel AS h 
		INNER JOIN divisa AS d ON (h.div_id = d.div_id) 
		INNER JOIN servicios AS se ON(se.se_id = h.h_tipo_hotel) 
		WHERE h.svi_id = '%s' 
		GROUP BY h.h_hotel_id;",
		"%d/%m/%Y", "%d/%m/%Y", $idItinerario);
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'h_hotel_id'		=>$fila['h_hotel_id'],
		'div_id'			=>$fila['div_id'],
		'h_total_pesos'		=>$fila['h_total_pesos'],
		'h_total'			=>$fila['h_total'],
		'h_subtotal'		=>$fila['h_subtotal'],
		'h_nombre_hotel'	=>utf8_encode($fila['h_nombre_hotel']),
		'h_noches'			=>$fila['h_noches'],
		'h_no_reservacion'	=>$fila['h_no_reservacion'],
		'h_iva'				=>$fila['h_iva'],
		'h_fecha_salida'	=>$fila['h_fecha_salida'],
		'h_fecha_llegada'	=>$fila['h_fecha_llegada'],
		'h_costo_noche'		=>$fila['h_costo_noche'],
		'h_comentarios'		=>utf8_encode($fila['h_comentarios']),
		'h_ciudad'			=>utf8_encode($fila['h_ciudad']),
		'h_tipoHotel_id'	=>$fila['h_tipo_hotel'],
		'h_tipo_hotel_txt'	=>utf8_encode($fila['se_nombre']),
		'h_divisa_hotel_txt'=>$fila['div_nombre']
		);
	}
	echo json_encode($_arreglo);
}

//Obtener los conceptos
if (isset($_POST['conceptos'])) {
	$idTramite = $_POST['conceptos'];
	$cnn = new conexion();
	$dato = array();
	$query = "SELECT COUNT(*) AS total
				FROM sv_itinerario
				JOIN solicitud_viaje ON sv_id = svi_solicitud
				WHERE sv_tramite = $idTramite";
	//error_log($query);
	$res = $cnn->consultar($query);
	$row = mysql_fetch_assoc($res);
	$tot = $row['total'];	
	
	/*$query = "(SELECT svc_detalle_id, svc_detalle_concepto, cp_concepto, svc_detalle_tramite, svc_itinerario, FORMAT(svc_detalle_monto_concepto,2) AS svc_detalle_monto_concepto,
				 svc_divisa, div_nombre, svc_conversion, svc_monto_divisa, FORMAT(svc_dias_itinerario, 0) AS svc_dias_itinerario, 0 AS recurrencia, svi_dias_viaje 
			FROM sv_conceptos_detalle
			JOIN sv_itinerario ON svi_id = svc_itinerario
			JOIN solicitud_viaje ON sv_id = svi_solicitud
			JOIN cat_conceptos ON svc_detalle_concepto = cp_id
			JOIN divisa ON div_id = svc_divisa
			WHERE sv_tramite = $idTramite
			AND (SELECT COUNT(*) 
					FROM sv_conceptos_detalle  scd
					JOIN sv_itinerario svi ON svi.svi_id = svc_itinerario 
					WHERE scd.svc_detalle_concepto = cp_id
					AND svi.svi_solicitud = sv_id) != $tot
			ORDER BY cp_concepto)
			
			UNION
			 
			(SELECT svc_detalle_id, svc_detalle_concepto, cp_concepto, svc_detalle_tramite, svc_itinerario, FORMAT(SUM(svc_detalle_monto_concepto),2) AS svc_detalle_monto_concepto,
				 svc_divisa, div_nombre, FORMAT(SUM(svc_conversion),2) AS svc_conversion, FORMAT(SUM(svc_monto_divisa),2) AS svc_monto_divisa, FORMAT(SUM(svc_dias_itinerario), 0) AS svc_dias_itinerario, 1 AS recurrencia, SUM(svi_dias_viaje) AS svi_dias_viaje
			FROM sv_conceptos_detalle
			JOIN sv_itinerario ON svi_id = svc_itinerario
			JOIN solicitud_viaje ON sv_id = svi_solicitud
			JOIN cat_conceptos ON svc_detalle_concepto = cp_id
			JOIN divisa ON div_id = svc_divisa
			WHERE sv_tramite = $idTramite
			AND (SELECT COUNT(*) 
					FROM sv_conceptos_detalle  scd
					JOIN sv_itinerario svi ON svi.svi_id = svc_itinerario 
					WHERE scd.svc_detalle_concepto = cp_id
					AND svi.svi_solicitud = sv_id) = $tot
			#AND (svc_detalle_concepto = 11 OR svc_detalle_concepto = 12 OR svc_detalle_concepto = 13)
			GROUP BY cp_id)
			
			UNION
			
			(SELECT svc_detalle_id, svc_detalle_concepto, cp_concepto, svc_detalle_tramite, svc_itinerario, FORMAT(svc_detalle_monto_concepto,2) AS svc_detalle_monto_concepto,
				 svc_divisa, div_nombre, svc_conversion, svc_monto_divisa, FORMAT(svc_dias_itinerario, 0) AS svc_dias_itinerario, 0 AS recurrencia, svi_dias_viaje 
			FROM sv_conceptos_detalle
			JOIN sv_itinerario ON svi_id = svc_itinerario
			JOIN solicitud_viaje ON sv_id = svi_solicitud
			JOIN cat_conceptos ON svc_detalle_concepto = cp_id
			JOIN divisa ON div_id = svc_divisa
			WHERE sv_tramite = $idTramite
			AND (SELECT COUNT(*) 
					FROM sv_conceptos_detalle  scd
					JOIN sv_itinerario svi ON svi.svi_id = svc_itinerario 
					WHERE scd.svc_detalle_concepto = cp_id
					AND svi.svi_solicitud = sv_id) = $tot
			#AND (svc_detalle_concepto = 5 OR svc_detalle_concepto = 6 OR svc_detalle_concepto = 10)
			ORDER BY cp_concepto)
			ORDER BY cp_concepto";*/
	$query = "(SELECT svc_detalle_id, svc_detalle_concepto, cp_concepto, svc_detalle_tramite, svc_itinerario, FORMAT(svc_detalle_monto_concepto,2) AS svc_detalle_monto_concepto,
				 svc_divisa, div_nombre, svc_conversion, svc_monto_divisa, FORMAT(svc_dias_itinerario, 0) AS svc_dias_itinerario, 0 AS recurrencia, svi_dias_viaje 
			FROM sv_conceptos_detalle
			JOIN sv_itinerario ON svi_id = svc_itinerario
			JOIN solicitud_viaje ON sv_id = svi_solicitud
			JOIN cat_conceptos ON svc_detalle_concepto = cp_id
			JOIN divisa ON div_id = svc_divisa
			WHERE sv_tramite = $idTramite
			AND (SELECT COUNT(*) 
					FROM sv_conceptos_detalle  scd
					JOIN sv_itinerario svi ON svi.svi_id = svc_itinerario 
					WHERE scd.svc_detalle_concepto = cp_id
					AND svi.svi_solicitud = sv_id) != $tot
			ORDER BY cp_concepto)
			
			UNION
			 
			(SELECT svc_detalle_id, svc_detalle_concepto, cp_concepto, svc_detalle_tramite, svc_itinerario, FORMAT(SUM(svc_detalle_monto_concepto),2) AS svc_detalle_monto_concepto,
				 svc_divisa, div_nombre, FORMAT(SUM(svc_conversion),2) AS svc_conversion, FORMAT(SUM(svc_monto_divisa),2) AS svc_monto_divisa, FORMAT(SUM(svc_dias_itinerario), 0) AS svc_dias_itinerario, 1 AS recurrencia, SUM(svi_dias_viaje) AS svi_dias_viaje
			FROM sv_conceptos_detalle
			JOIN sv_itinerario ON svi_id = svc_itinerario
			JOIN solicitud_viaje ON sv_id = svi_solicitud
			JOIN cat_conceptos ON svc_detalle_concepto = cp_id
			JOIN divisa ON div_id = svc_divisa
			WHERE sv_tramite = $idTramite
			AND (SELECT COUNT(*) 
					FROM sv_conceptos_detalle  scd
					JOIN sv_itinerario svi ON svi.svi_id = svc_itinerario 
					WHERE scd.svc_detalle_concepto = cp_id
					AND svi.svi_solicitud = sv_id) = $tot
			GROUP BY cp_id)
			ORDER BY cp_concepto";
	//error_log("conceptos: ".$query);
	$rst = $cnn->consultar($query);
	while($fila = mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'svc_detalle_id'				=>$fila['svc_detalle_id'],
		'svc_detalle_concepto'			=>$fila['svc_detalle_concepto'],
		'cp_concepto'					=>$fila['cp_concepto'],
		'svc_detalle_tramite'			=>$fila['svc_detalle_tramite'],
		'svc_itinerario'				=>$fila['svc_itinerario'],
		'svc_detalle_monto_concepto'	=>$fila['svc_detalle_monto_concepto'],
		'svc_divisa'					=>$fila['svc_divisa'],
		'div_nombre'					=>$fila['div_nombre'],
		'svc_conversion'				=>$fila['svc_conversion'],
		'svc_monto_divisa'				=>$fila['svc_monto_divisa'],
		'svc_dias_itinerario'			=>$fila['svc_dias_itinerario'],
		'recurrencia'    				=>$fila['recurrencia'],
		'svi_dias_viaje'				=>$fila['svi_dias_viaje']
		);
	}
	echo json_encode($_arreglo);
}

//AJAX PARA PARAMETROS DE CONCEPTOS

//Obtener el motivo
if (isset($_POST['conceptoId']) && isset($_POST['regionNombre'])) {
	$regionNombreArray = explode(",",$_POST['regionNombre']);
	$idConceptoArray = explode(",",$_POST['conceptoId']);
	
	$cnn = new conexion();
	$dato=array();

	foreach($regionNombreArray as $regionNombre){
		foreach($idConceptoArray as $conceptoId){
			$query = sprintf("SELECT pbmw.p_id AS id_parametro,
					crbmw.re_nombre,
					prbmw.re_id AS region,
					prbmw.pr_cantidad AS cantidad,
					prbmw.div_id AS divisa,
					d.div_tasa,
					(prbmw.pr_cantidad * d.div_tasa) AS cantidad_en_mxn
					FROM parametrosbmw AS pbmw
					INNER JOIN parametro_regionbmw AS prbmw
					ON pbmw.p_id = prbmw.p_id
					INNER JOIN cat_regionesbmw AS crbmw
					ON prbmw.re_id = crbmw.re_id
					INNER JOIN divisa AS d
					ON prbmw.div_id = d.div_id
					WHERE pbmw.p_concepto = '%s'
					AND crbmw.re_nombre = '%s'", $conceptoId, $regionNombre);
			//error_log("REGION!!!!".$query);
			$rst = $cnn->consultar($query);
			while($arr=mysql_fetch_assoc($rst)){
				$dato[$regionNombre.$conceptoId]=array(
				'id_parametro'		=>$arr['id_parametro'],
				're_nombre'			=>$arr['re_nombre'],
				'cantidad'			=>$arr['cantidad'],
				'divisa'			=>$arr['divisa'],
				'div_tasa'			=>$arr['div_tasa'],
				'cantidad_en_mxn'	=>$arr['cantidad_en_mxn']
				);
			}
		}
	}
	echo json_encode($dato);
}

if(isset($_POST['tramiteAvion'])){
	$aux=array();
	$cnn = new conexion();
	
	$idTramite=$_POST['tramiteAvion'];
	$svi_monto_vuelo_total=$_POST['monto_vuelo'];
	
	$svi_solicitud=0;
	$svi_monto_vuelo_cotizacion=0;
	$parametro=new Parametro();
	$indicador=0;
	// Contadores que me permitiran saber si hay un itinerario mezclado ó del mismo tipo, aplicable para varios o para 1
	$nacional=0;
	$internacional=0;	
	
	//error_log("aqui esta".$idTramite);
	$query=sprintf("SELECT DISTINCT svi_solicitud FROM sv_itinerario WHERE svi_solicitud=(SELECT sv_id FROM solicitud_viaje WHERE sv_tramite=%s )",$idTramite);
	$rst = $cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {			
		$svi_solicitud=$fila['svi_solicitud'];
	}
	//error_log("SVISOL".$svi_solicitud);
	/* Tomará el tipo de vaje para el itinerario
	 *  Seria el nuevo query - Se tomara el tipo de viaje de las solicitudes de tipo Aereo
	 *  Checar la validacion de Nacional e internacional
	 */
	$queryViaje=sprintf("SELECT svi_tipo_viaje FROM sv_itinerario WHERE  svi_solicitud = '%s' AND svi_tipo_transporte = 'Aéreo'",$svi_solicitud);
	//error_log($queryViaje);
	$tipoViaje = $cnn->consultar($queryViaje);	
	
	
	// Se tomará el valor de la primera cotizacion de avion realizada por agencia:svi_monto_vuelo_cotizacion (Para los iinerarios este siempre sera el mismo)
	$queryMontoCotiza=sprintf("SELECT DISTINCT  svi_monto_vuelo_cotizacion FROM sv_itinerario WHERE  svi_solicitud=%s",$svi_solicitud);
	$montoVueloCotiza = $cnn->consultar($queryMontoCotiza);	
	while ($fila = mysql_fetch_assoc($montoVueloCotiza)) {			
		$svi_monto_vuelo_cotizacion=$fila['svi_monto_vuelo_cotizacion'];
	}
		
	//error_log("valor nuevo". $svi_monto_vuelo_total);
	//error_log("vuelo Cotizacion".$svi_monto_vuelo_cotizacion);
	$dif = $svi_monto_vuelo_total + $svi_monto_vuelo_cotizacion;
	//primero comprobamos si es solo 1 itinerario o mas
	$row= mysql_num_rows($tipoViaje);
	
	while ($fila = mysql_fetch_assoc($tipoViaje)) {			
		if($row == 1){
			// Se tendra un solo itinerario ahora solo se validara si es nacional /internacional
			if($fila['svi_tipo_viaje'] == 'Nacional'){	
				$indicador = $parametro->montoMaxTolerancia(0, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
				//$indicador=$parametro->montoMaxToleranciaNacional($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion );
			}else if($fila['svi_tipo_viaje'] == 'Continental' || $fila['svi_tipo_viaje'] == 'Intercontinental'){
				$indicador = $parametro->montoMaxTolerancia(1, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
				//$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
			}
		}else{
			if($fila['svi_tipo_viaje']== 'Nacional'){
				$nacional++;
			}elseif($fila['svi_tipo_viaje'] == 'Continental' || $fila['svi_tipo_viaje']== 'Intercontinental'){
				$internacional++;
			}				
		}
	}

	//empieza la validacion				
	if(($nacional == $internacional) && ($nacional != 0)){
		$indicador = $parametro->montoMaxTolerancia(1, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
		//$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion );
	}elseif($internacional>$nacional && $nacional==0){ //itinerarios internacionales unicamente
		$indicador = $parametro->montoMaxTolerancia(1, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
		//$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}elseif($internacional > $nacional && $nacional != 0){//itinerarios mezclados
		$indicador = $parametro->montoMaxTolerancia(1, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
		//$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}elseif($nacional>$internacional && $internacional==0){//itinerarios nacionales unicamente
		$indicador = $parametro->montoMaxTolerancia(0, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
		//$indicador=$parametro->montoMaxToleranciaNacional($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}elseif($nacional>$internacional && $internacional!=0){//itinerarios mezclados
		$indicador = $parametro->montoMaxTolerancia(1, $svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);
		//$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}
	
// 	error_log("nacional".$nacional);
// 	error_log("internacional".$internacional);
//  error_log("--->>Indicador de excedente de parametro: ".$indicador);
	$diferencia = 0;
	$diferencia = $svi_monto_vuelo_total - $svi_monto_vuelo_cotizacion;
	$arreglo[] = array('indicador' => $indicador, 'diferenciaMontos' => $diferencia, 'monto_limite' => MONTO_EXCEDENTE_VUELO);
	//error_log("--->>Parametros monto avion: ".print_r($arreglo, true));
	echo json_encode($arreglo);
}

if(isset($_POST['tramiteId'])){
	//error_log("aqui esta la modificacion de etapa");
	$cnn = new conexion();
	$sv_ceco=0;
	$conceptoAvion=0;
	$DPPdisponible=0;
	$DPPutilizado=0;
	$tramite=new Tramite();		
	$tramiteId=$_POST['tramiteId'];
	$montoAvionAceptado=$_POST['montoAvion'];
	
	//Se realizara la resta del presupuesto del ceco al cual esta asociado para el concepto avion.
	
	//obtenemos el ceco que esta asociado a la solicitud de viaje.
	$ceco = $tramite->cecoAsignado($tramiteId);
	$sv_ceco = $ceco[0]['sv_ceco_paga'];
	$sv_total = $ceco[0]['sv_total'];
	//Obtengo la fecha de Cierre
	$queryCierre=sprintf("SELECT * FROM tramites WHERE t_id='%s'",$tramiteId);
	$rstCierre=$cnn->consultar($queryCierre);
	while ($filaCierre = mysql_fetch_array($rstCierre)){
		$fCierre=$filaCierre["t_fecha_cierre"];
	}	
	$f = explode("-",$fCierre);
	$mes = $f[1];
	//inicia la resta del presupuesto.
	$queryPresupuestoE=sprintf("SELECT * FROM periodo_presupuestal WHERE cc_id = '%s' AND MONTH(pp_periodo_inicial) = '%s'",$sv_ceco,$mes);
	$rstPresupuestoE=$cnn->consultar($queryPresupuestoE);
	while ($fila = mysql_fetch_array($rstPresupuestoE)){
		$idPP=($fila["pp_id"]);
		$DPPdisponible=($fila["pp_presupuesto_disponible"]-$sv_total);
		$DPPutilizado=($fila["pp_presupuesto_utilizado"]+$sv_total);
	}
	$queryActualizaE=sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible='%s',pp_presupuesto_utilizado='%s' WHERE pp_id='%s'",$DPPdisponible,$DPPutilizado,$idPP);
	$cnn->ejecutar($queryActualizaE);
}

if(isset($_POST['tramiteCId'])){
	$cnn = new conexion();
	$tramiteId = $_POST['tramiteCId'];
	$diferenciaMontos = $_POST['diferencia'];
	$tramite=new Tramite();
	$tramite->Load_Tramite($tramiteId);
	
	$sv_ceco_paga=0;
	$cc_responsable=0;
	$Gerente_area=0;
	
	//Notificacion para empleado
	$mensaje=sprintf("El monto que la agencia de viajes ha registrado para la compra del boleto de avi&oacute;n de la solicitud de viaje <strong>%05s</strong> excede el l&iacute;mite de tolerancia, se enviar&aacute; nuevamente a aprobaci&oacute;n",$tramiteId);
	$remitente = $tramite->Get_dato("t_dueno");
	
	//enviar la notificacion al empleado ----> t_iniciador
	
	$destinatario = $tramite->Get_dato("t_iniciador");	
	$tramite->EnviaNotificacion($tramiteId, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	
	/*$query=sprintf("select * from EXPENVKIOQA1.usuario where u_id = %s",CONTROLINTERNO);
	$rst=$cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {
		$sv_ceco_paga=$fila['u_id'];
	}
	*/
	//Notificacion al gerente de area		
	//construimos la segunda ruta de autorizacion
	$rutaAutorizacion=new RutaAutorizacion();
	$aprobadornuevo=$rutaAutorizacion->generaRutaSegundaAutorizacion($destinatario, $tramiteId, $diferenciaMontos);
	
	$tramite->Modifica_Etapa($tramiteId,SOLICITUD_ETAPA_SEGUNDA_APROBACION, FLUJO_SOLICITUD,$aprobadornuevo, "");	
	
	$usuarioCreador = new Usuario();
	$usuarioCreador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
	
	$mensaje=sprintf("El monto que la agencia de viajes ha registrado para la compra del boleto de avi&oacute;n de la solicitud de viaje <strong>%05s</strong> creada por:<strong>%s</strong> excede el l&iacute;mite de tolerancia  y requiere de su autorizaci&oacute;n.",$tramiteId,$usuarioCreador->Get_dato('nombre'));
	$remitente = $tramite->Get_dato("t_dueno");		
	//se le enviara al Gerente de area		
	$tramite->EnviaNotificacion($tramiteId, $mensaje, $remitente,$aprobadornuevo, "1", ""); //"0" para no enviar email y "1" para enviarlo
	
}

//funcion que nos regresara el numero de CECO al cual esta asignado el usuario ( creacion de una solicitud viaje)
if(isset($_POST['idEmpleado'])){
	$idEmpleado=(int)$_POST['idEmpleado'];
	$cecoEmpleado=0;
	$cnn = new conexion();
	$query=sprintf("SELECT cc_centrocostos from cat_cecos where cc_id = (select idcentrocosto from empleado where idempleado = %s)",$idEmpleado);
	$rst=$cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$cecoEmpleado=$fila['cc_centrocostos'];		
	}	
	echo json_encode($cecoEmpleado);
}

//funcion qe nos regresa el numero de CECO cuando se trata de una edicion
if(isset($_POST['idTramiteEdicion'])){
	$idTramiteEdicion=(int)$_POST['idTramiteEdicion'];
	$cnn = new conexion();
	$cecoEmpleadoEdita=0;
	$query=sprintf("SELECT cc_centrocostos FROM cat_cecos WHERE cc_id=(SELECT sv_ceco_paga FROM solicitud_viaje WHERE sv_tramite=%s)",$idTramiteEdicion);
	$rst=$cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$cecoEmpleadoEdita=$fila['cc_centrocostos'];		
	}	
	echo json_encode($cecoEmpleadoEdita);
}

//Validacion dependiendo el presupuesto del CECO seleccionado
if(isset($_POST['totalPresupuesto'])){
	$totalSolicitud = $_POST['totalPresupuesto'];
	$cecoId =  $_POST['cecoSel'];	
	//error_log("total".$totalSolicitud);
	//error_log("ceco id".$cecoId);
	$presupuesto=new Presupuesto();
	$diferenciaPresupuesto=$presupuesto->calculoPresupuestoXCECO($totalSolicitud, $cecoId);
	//error_log("Indicador de presupuesto".$diferenciaPresupuesto);
	
	echo json_encode($diferenciaPresupuesto);

}


if(isset($_POST['Multidestinos'])){
	$id_Solicitud=$_POST['Multidestinos'];
	//error_log("id de la solicitud.................".$id_Solicitud);
	$cnn = new conexion();
	$fechaUltimoTramite="";
	$queryMultidestinos="SELECT DATE_FORMAT(MAX(svi_fecha_salida),'%d/%m/%Y') AS 'svi_fecha_salida' FROM sv_itinerario WHERE svi_solicitud='{$id_Solicitud}'";
	$rstMulti = $cnn->consultar($queryMultidestinos);
	
	while ($fila = mysql_fetch_assoc($rstMulti)) {
	$fechaUltimoTramite=$fila['svi_fecha_salida'];
	}
	
	echo json_encode($fechaUltimoTramite);

}

// Selecciona el monto límite de Hospedaje para la cotización de Hotel por el empleado
if (isset($_POST['regionHospedaje']) || isset($_POST['zonaGeografica'])) {
	$region = $_POST['regionHospedaje'];
	$zonaGeografica = $_POST['zonaGeografica'];
	$concepto = "5"; // Concepto de Hotel
	$parametros = "";
	$cnn = new conexion();
	$_arreglo=array();
	
	if($region != "0"){
		$parametros = "AND prbmw.re_id = (SELECT re_id FROM cat_regionesbmw WHERE re_nombre = '{$region}')";
	}
	
	$query = "SELECT prbmw.pr_cantidad, moneda.div_nombre FROM parametro_regionbmw AS prbmw
			INNER JOIN parametrosbmw AS pbmw ON (pbmw.p_id = prbmw.p_id)
			INNER JOIN divisa AS moneda ON (moneda.div_id = prbmw.div_id)
			INNER JOIN cat_conceptos AS ccbmw ON (ccbmw.dc_id = pbmw.p_concepto)
			INNER JOIN cat_regiones AS crbmw ON (crbmw.re_id = prbmw.re_id)
			WHERE ccbmw.dc_id = '{$concepto}'
			AND pbmw.p_id = '{$zonaGeografica}' ".$parametros;
	//error_log($query);
	$rst = $cnn->consultar($query);
	$i=0;
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'montoLim' =>$fila['pr_cantidad'],
		'moneda' =>$fila['div_nombre']);
		$i++;
	}
	echo json_encode($_arreglo);
}

//funcion que nos regresara el numero de CECO al cual esta asignado el usuario ( creacion de una solicitud viaje)
if(isset($_POST['obtenerConceptos'])){
	$_arreglo=array();
	$cnn = new conexion();
	$query = sprintf("SELECT c.cp_id AS cp_id, cp_concepto, cp_cuenta FROM cat_conceptos AS c 
			INNER JOIN conceptos_flujos AS cf ON (c.cp_id = cf.cp_id) 
			WHERE cf.f_id = %s AND cp_activo = 1 ORDER BY cp_concepto", FLUJO_SOLICITUD);
	//error_log("--->>Consulta Conceptos según el flujo: ".$query);
	$rst=$cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[] = array(
				'dc_id'			=>	$fila['cp_id'],
				'cp_concepto'	=>	utf8_encode($fila['cp_concepto']));
	}
	echo json_encode($_arreglo);
}

//funcion que permitira enviar la notificacion al empleado de que la compra ha sido realizada
if(isset($_POST['observacionesCompra'])){
	$cnn = new conexion();
	$observacionesCompra=$_POST['observacionesCompra'];	
	$tramiteCompra=$_POST['tramiteCompra'];
	$usuarioCompra=$_POST['usuarioCompra'];
	$excede = $_POST['excede'];
	
	$observacionesCompra=utf8_decode($observacionesCompra);
	//Se inserta la observacion que la agencia ha realizado para confirmar la compra
	$queryCompra = sprintf("INSERT INTO observaciones(
			ob_id,
			ob_texto,
			ob_fecha,
			ob_tramite,
			ob_usuario
	)VALUES(
			default,
			'%s',
			now(),
			%s,
			%s
	)",
			$observacionesCompra,
			$tramiteCompra,
			$usuarioCompra
	);
	
	$ob_id = $cnn->insertar($queryCompra);
	
	if($excede != 1){
		// Se enviará la notificacion al empleado para avisar que la compra de la  compra, asi como tambien la fecha en la cual esta solicitud ha sido comprada
		$tramite = new Tramite();
		$tramite->Load_Tramite($tramiteCompra);
		$iniciador=$tramite->Get_dato("t_iniciador");	
		$mensaje=sprintf("La solicitud de viaje <strong>%05s</strong> ha sido <strong>COMPRADA</strong> de manera exitosa por </strong>Agencia.",$tramiteCompra);
		$tramite->EnviaNotificacion($tramiteCompra, $mensaje, $usuarioCompra,$iniciador, "1", "", 0); //"0" para no enviar email y "1" para enviarlo, el último argumento indicará si se coloca el link de ingreso a la aplicación (1) y (0) si no es requerido.
		$tramite->Modifica_Etapa($tramiteCompra, SOLICITUD_ETAPA_COMPRADA, FLUJO_SOLICITUD,$iniciador, "");
		
		// Se inserta la fecha de termino
		$tramite->setCierreFecha($tramiteCompra);
	}
}

// Seleccionar las observaciones introducidas por el usuario
if(isset($_POST['observTramite'])){
	$id_Solicitud = $_POST['observTramite'];
	$observaciones = "";
	//error_log("Id de la solicitud: ".$id_Solicitud);
	$cnn = new conexion();
	$tramite = new Tramite();
	$tramite->Load_Tramite($id_Solicitud);
	$t_dueno = $tramite->Get_dato("t_dueno");
	$_arreglo=array();
	
	if($t_dueno == 3000){
		$query = sprintf("SELECT ob_texto AS observacion, t_etapa_actual AS etapa FROM tramites 
		LEFT JOIN observaciones ON t_id = ob_tramite WHERE t_id = '%s'",$id_Solicitud);
	}else{
		$query = sprintf("SELECT sv_observaciones AS observacion, t_etapa_actual AS etapa FROM solicitud_viaje 
			JOIN tramites ON t_id = sv_tramite WHERE sv_tramite = '%s'",$id_Solicitud);
	}
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[] = array(
			'observacion'=>utf8_encode($fila['observacion']),
			'etapa'=>$fila['etapa']);
	}
	echo json_encode($_arreglo);
}

//obtener la etapa actual de la solicitud cuando este en edicion
if(isset($_POST['etapaRechazo'])){
	
	$idTramiteR=$_POST['etapaRechazo'];
	$etapaR = 0;
	$tramiteR = new Tramite();
	$tramiteR->Load_Tramite($idTramiteR);
	$etapaR = $tramiteR->Get_dato('t_etapa_actual');
	//error_log("etapa :/".$etapaR);
	echo $etapaR;
}

//Total de la solicitud Caso: Rechazada / Guardado previo.
if(isset($_POST['totalBD'])){
	$idTramiteEdit = $_POST['totalBD'];
	$totSolicitudEdit=0;
	$cnn = new conexion();
	$queryTotalSol=sprintf("SELECT sv_total FROM solicitud_viaje WHERE sv_tramite='%s'",$idTramiteEdit);
	$rstTotal = $cnn->consultar($queryTotalSol);
	while($fila=mysql_fetch_array($rstTotal)){				
		$totSolicitudEdit=$fila['sv_total'];
	}	
	echo $totSolicitudEdit;
}

//GetId-Renta de auto
if(isset($_POST['idRentaAuto'])){
	$idRentaAuto=$_POST['idRentaAuto'];
	$concepto=new Concepto();
	$idRentaAuto=$concepto->GetRentaAuto();
	echo $idRentaAuto;
}

//GetId-Hotel
if(isset($_POST['idHotel'])){
	$idHotel=0;
	$concepto=new Concepto();
	$idHotel = $concepto->GetIdHotel();
	echo $idHotel;
}

//GetTotalAvion
if(isset($_POST['totalAvion'])){
	$tramiteAvion=$_POST['totalAvion'];
	$montoAvionSol=0;
	$cnn = new conexion();
	$queryTotalAvion="SELECT IFNULL(svi_monto_vuelo_cotizacion,'') AS svi_monto_vuelo_cotizacion  FROM sv_itinerario 
	INNER JOIN solicitud_viaje
	ON svi_solicitud=sv_id
	WHERE sv_tramite={$tramiteAvion}";
	$rstAvion = $cnn->consultar($queryTotalAvion);
	while($fila=mysql_fetch_array($rstAvion)){				
		$montoAvionSol=$fila['svi_monto_vuelo_cotizacion'];
	}
	if($montoAvionSol == ""){
		$montoAvionSol = 0;
	}	
	echo $montoAvionSol;
}

//Obtener monto máximo de la politíca de Comidas de Invitación
if (isset($_POST['montoMaximo'])){
	$cnn = new conexion();
	$query = sprintf("SELECT pr.pr_cantidad AS pr_cantidad, d.div_nombre AS div_nombre FROM parametro_regionbmw AS pr
			INNER JOIN parametrosbmw AS pb ON pr.p_id = pb.p_id AND pb.p_id = '22'
			INNER JOIN divisa AS d ON d.div_id = pr.div_id");
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'montoCantidad' =>utf8_encode($fila['pr_cantidad']),
		'divisaMonto' =>$fila['div_nombre']);
	}
	
	echo json_encode($_arreglo);
}

// Obtener datos de Solicitud de Información
if (isset($_POST['mntsolicinv'])) {
	$idTramite = $_POST['mntsolicinv'];
	$cnn = new conexion();
	
	$query = "SELECT si_motivo, si_num_invitados, FORMAT(si_monto, 2) AS si_monto, FORMAT(si_monto_pesos, 2) AS montoPesos, si_divisa, si_tramite, 
		si_ciudad, si_observaciones, si_observaciones_edicion, DATE_FORMAT(si_fecha_invitacion, '%d/%m/%Y') AS si_fecha_invitacion, si_lugar, si_hubo_exedente, div_id, si_monto_pesos, 
		t_etapa_actual, t_autorizaciones_historial
		FROM solicitud_invitacion 
		INNER JOIN tramites ON t_id = si_tramite
		INNER JOIN divisa ON div_nombre = si_divisa 
		WHERE si_tramite ='{$idTramite}'";
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
			'si_motivo' =>utf8_encode($fila['si_motivo']), 
			'si_num_invitados' =>$fila['si_num_invitados'], 
			'si_monto' =>$fila['si_monto'],
			'montoPesos' =>$fila['montoPesos'],
			'si_monto_pesos' =>utf8_encode($fila['si_monto_pesos']),
			'si_divisa' =>$fila['div_id'],
			'si_tramite' =>$fila['si_tramite'],
			'si_ciudad' =>utf8_encode($fila['si_ciudad']),
			'si_observaciones' =>utf8_encode($fila['si_observaciones']),
			'si_observaciones_edicion' =>utf8_encode($fila['si_observaciones_edicion']),
			'si_fecha_invitacion' =>$fila['si_fecha_invitacion'],
			'si_lugar' =>utf8_encode($fila['si_lugar']),
			'si_hubo_exedente' =>$fila['si_hubo_exedente'],
			't_etapa' =>$fila['t_etapa_actual'],
			'si_autorizaciones' =>$fila['t_autorizaciones_historial']
		);
	}
	echo json_encode($_arreglo);
}

if(isset($_POST['svc_detalle_tramite_comp'])){
	$id_tramiteComp = $_POST['svc_detalle_tramite_comp'];
	
	$cnn = new conexion();
	$query = sprintf("UPDATE sv_conceptos_detalle SET svc_divisa = '1' WHERE svc_divisa = '0' AND svc_detalle_tramite = '%s'", $id_tramiteComp);
	//error_log($query);
	$cnn->ejecutar($query);
	echo true;
}


	if(isset($_REQUEST["excepciones"])){	
		$conexion = new conexion();
		$idTramite = $_REQUEST["tramite"];
		$idUsuario = $_REQUEST["usuario"];
		$sql = "SELECT 
					(1 + svc_itinerario - (SELECT MIN(svi_id) FROM sv_itinerario JOIN solicitud_viaje ON svi_solicitud = sv_id WHERE sv_tramite = $idTramite)) AS itinerario,
					cp_concepto AS concepto,	
					svcd.svc_detalle_concepto,
					FORMAT(svcd.svc_conversion,2) AS total,
					ex_mensaje AS mensaje, 
					FORMAT(ex_diferencia,2) AS excedente,
					IF(pd_monto>0 , FORMAT(pd_monto * div_tasa,2), '0') AS politica,
					(CASE ex_presupuesto
						WHEN 1 THEN 'Excepci&oacute;n de Presupuesto'
						WHEN 0 THEN 'Excepci&oacute;n de Pol&iacute;tica' 
						ELSE ''
					END) AS tipoExcepcion
				FROM sv_conceptos_detalle AS svcd
				LEFT JOIN solicitud_viaje AS sv ON sv.sv_tramite = svcd.svc_detalle_tramite
				LEFT JOIN tramites AS t ON sv.sv_tramite = t.t_id
				LEFT JOIN cat_conceptos ON svcd.svc_detalle_concepto = cp_id
				LEFT JOIN excepciones ON ex_solicitud_detalle = svcd.svc_detalle_id
				LEFT JOIN politicas_flujos ON (politicas_flujos.c_id = cp_id AND politicas_flujos.re_id = (SELECT svi_region FROM sv_itinerario WHERE svi_id = svc_itinerario) AND politicas_flujos.b_id = (SELECT b_id FROM empleado WHERE idfwk_usuario = $idUsuario AND politicas_flujos.f_id = 1) ) 
				LEFT JOIN politicas_divisa ON politicas_flujos.pf_id = politicas_divisa.pf_id 
				LEFT JOIN divisa ON divisa.div_id = politicas_divisa.div_id
				WHERE t.t_id = $idTramite
				ORDER BY cp_concepto";
		$res = $conexion->consultar($sql);
		while($row = mysql_fetch_assoc($res)){
			$cont++;
			$response->rows[$cont] = array("itinerario"	=> $row["itinerario"],
											"concepto"	=> utf8_encode($row["concepto"]),
											"total"		=> $row["total"],											
											"mensaje"	=> $row["mensaje"],
											"politica"	=> $row["politica"],
											"excedente" => $row["excedente"],
											"tipoExcepcion" => $row["tipoExcepcion"]);
		}
		echo json_encode($response);		
	}
	
	if(isset($_REQUEST["excepcionesPresupuesto"])){	
		$conexion = new conexion();
		$idTramite = $_REQUEST["tramite"];
		$sql = sprintf("SELECT ex_mensaje, FORMAT(ex_diferencia,2) AS excedente, IF(ex_presupuesto = 1, 'Excepci&oacute;n de Presupuesto', '') AS tipoExcepcion
				FROM excepciones 
				JOIN solicitud_viaje ON sv_id = ex_solicitud
				LEFT JOIN tramites ON sv_tramite = t_id
				WHERE t_id = %s
				AND ex_presupuesto = 1", $idTramite);
		$res = $conexion->consultar($sql);
		while($row = mysql_fetch_assoc($res)){
			$response = array("mensaje" 	=> $row["ex_mensaje"],
						"excedente"			=> $row["excedente"],
						"tipoExcepcion"		=> $row["tipoExcepcion"]);						
		}
		echo json_encode($response);		
	}
?>
