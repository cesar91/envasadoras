<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

if (isset($_POST['no_region'])) {
    $no_region = $_POST['no_region'];

    $cnn = new conexion();
    
   if($no_region=="2"){
       $query = sprintf("select re_id,re_nombre from cat_regionesbmw where re_id='4' or re_id='5'");
   }
   if($no_region=="3"){
       $query = sprintf("select re_id,re_nombre from cat_regionesbmw where re_id='3' or re_id='6'");
   }
    
    $rst2 = $cnn->consultar($query);
$i=0;
    while ($filaa = mysql_fetch_assoc($rst2)) {
        $data=sprintf("%s:%s",$filaa['re_id'], $filaa['re_nombre']);
        $_arreglo[$i]=$data;
        $i++;
    }
echo json_encode($_arreglo);
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
    $query_cargostc = sprintf("SELECT DIV_TASA FROM divisa WHERE DIV_ID = '%s' ", $divisa);
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
	error_log($query_lugar);
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
	error_log($query_lugar);
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
	error_log($query_lugar);
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
	error_log($query_lugar);
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
	error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while ($fila = mysql_fetch_assoc($rst)) {
		$dato = utf8_encode($fila['si_motivo']);
	}
	echo $dato;
}
//Elimnar Hotel
if(isset($_POST['hotel'])){
	$id_hotel = $_POST['hotel'];
	$cnn = new conexion();
	$dato="";
	$query = sprintf("DELETE FROM hotel WHERE h_hotel_id =".$id_hotel);
    error_log($query);
	$cnn->ejecutar($query);
	echo true;	
}
//Actualizar los datos de avion de los itinerarios de una solicitud
if (isset($_POST['id_sol_viaje'])) {
	$idsolicitud = $_POST['id_sol_viaje'];
	$val1 = $_POST['val1'];
	$val2 = $_POST['val2'];
	$val3 = $_POST['val3'];
	$val4 = $_POST['val4'];
	$val5 = $_POST['val5'];
	$val6 = $_POST['val6'];
	$val7 = $_POST['val7'];
	$val8 = $_POST['val8'];
	$val9 = $_POST['val9'];
	$cnn = new conexion();
	$dato="";
	$query = sprintf("update sv_itinerario set
                           svi_monto_vuelo='%s',
						   svi_iva='%s',
						   svi_tua='%s',
						   svi_monto_vuelo_cotizacion='%s',
						   svi_aerolinea='%s',
						   svi_tipo_aerolinea='%s',
						   svi_fecha_salida_avion='%s',
						   svi_fecha_regreso_avion='%s',
						   svi_monto_vuelo_total='%s'
                           where svi_solicitud='%s'",
						   $val1,
						   $val2,
						   $val3,
						   $val4,
						   $val5,
						   $val6,
						   $val7,
						   $val8,
						   $val9,
						   $idsolicitud);
	error_log($query);
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
	$val6 = $_POST['val6'];
	$cnn = new conexion();
	$dato="";
	$query = sprintf("update sv_itinerario set
						   svi_empresa_auto='%s',
						   svi_tipo_auto='%s',
						   svi_dias_renta='%s',
						   svi_total_auto=%s,
						   svi_dias_renta='%s',
						   svi_total_pesos_auto='%s'
                           where svi_id='%s'",
						   $val1,
						   $val2,
						   $val3,
						   $val4,
						   $val5,
						   $val6,
						   $idItinerario);
	error_log($query);
	$cnn->ejecutar($query);
	echo true;
}


?>
