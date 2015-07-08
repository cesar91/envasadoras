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
	//fechas
	$val7 = $_POST['val7'];	
	$val8 = $_POST['val8'];
	
	if($val7 != "(NULL)"|| $val8 != "(NULL)"){
		//tranformacion de formato de fecha
		$date=explode("/",$val7);
		$date2=explode("/",$val8);
		$val7=$date[2]."-".$date[1]."-".$date[0];
		$val8=$date2[2]."-".$date2[1]."-".$date2[0];
		
	}		
	
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
	$cnn = new conexion();
	$dato="";
	$query = sprintf("update sv_itinerario set
						   svi_empresa_auto='%s',
						   svi_tipo_auto='%s',
						   svi_dias_renta='%s',
						   svi_total_auto= '%s',
						   svi_total_pesos_auto='%s'
                           where svi_id='%s'",
						   $val1,
						   $val2,
						   $val3,
						   $val4,
						   $val5,
						   $idItinerario);
	error_log($query);
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
	$query_lugar = sprintf("SELECT sv_motivo,
								sv_viaje,
								sv_anticipo
								FROM solicitud_viaje WHERE sv_tramite='%s'", $idsolicitud);
	error_log($query_lugar);
	$rst = $cnn->consultar($query_lugar);
	while($arr=mysql_fetch_assoc($rst)){
		array_push($dato,$arr);
	}
	echo json_encode($dato);
}
//Obtener aviones
if (isset($_POST['datos_avion'])) {
	$idTramite = $_POST['datos_avion'];
	$cnn = new conexion();
	$dato=array();
	//$dato="";
	$query ="select svi_tipo_viaje, svi_aerolinea, DATE_FORMAT(svi_fecha_salida_avion,'%d/%m/%Y') AS 'svi_fecha_salida_avion',
						DATE_FORMAT(svi_fecha_regreso_avion,'%d/%m/%Y') AS 'svi_fecha_regreso_avion', svi_monto_vuelo, svi_iva,
						svi_tua, svi_monto_vuelo_cotizacion,svi_tipo_aerolinea 
						from sv_itinerario as svi
						where svi.svi_solicitud=".$idTramite." group by svi.svi_id;";
						error_log("www ".$query);
	$rst = $cnn->consultar($query);
	while($arr=mysql_fetch_assoc($rst)){
		array_push($dato,$arr);
	}
	echo json_encode($dato);
	/*while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'svi_tipo_viaje'				=>$fila['svi_tipo_viaje'],
		'svi_aerolinea'					=>$fila['svi_aerolinea'],
		'svi_fecha_salida_avion'		=>$fila['svi_fecha_salida_avion'],
		'svi_fecha_regreso_avion'		=>$fila['svi_fecha_regreso_avion'],
		'svi_monto_vuelo'				=>$fila['svi_monto_vuelo'],
		'svi_iva'						=>$fila['svi_iva'],
		'svi_tua'						=>$fila['svi_tua'],
		'svi_monto_vuelo_cotizacion'	=>$fila['svi_monto_vuelo_cotizacion']
		);
	}
	echo json_encode($_arreglo);*/
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
sum(if(h_total_pesos,h_total_pesos,0)) as h_total_pesos,
/*Datos de auto*/
svi_empresa_auto,
svi_costo_auto,
div_nombre as svi_divisa_auto,
svi_tipo_auto,
svi_total_auto,
svi_dias_renta,
svi_total_pesos_auto,
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
	error_log($query);
	$rst = $cnn->consultar($query);
	
/*	while($arr=mysql_fetch_assoc($rst)){
		array_push($dato,$arr);
	}
	echo json_encode($dato);*/
	
	while($fila=mysql_fetch_assoc($rst)){
		$_arreglo[]=array(
		'svi_id'				=>$fila['svi_id'],
		'svi_destino'			=>$fila['svi_destino'],
		'svi_origen'			=>$fila['svi_origen'],
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
		'svi_medio'				=>$fila['svi_medio']
		);
	}
	echo json_encode($_arreglo);
}

//Obtener los hoteles
if (isset($_POST['hoteles'])) {
	$idItinerario = $_POST['hoteles'];
	$cnn = new conexion();
	$dato=array();
	//$dato="";
	$query = sprintf("select h_hotel_id,
d.div_nombre as div_id,
h_total_pesos,
h_total,
h_subtotal,
h_nombre_hotel,
h_noches,
h_no_reservacion,
h_iva,
date_format(h_fecha_salida, '%s') as h_fecha_salida,
date_format(h_fecha_llegada, '%s') as h_fecha_llegada,
h_costo_noche,
h_comentarios,
h_ciudad

from hotel as h
inner join divisa as d
on h.div_id = d.div_id
where h.svi_id = '%s'
group by h.h_hotel_id;",
"%d/%m/%Y", "%d/%m/%Y", $idItinerario);
	error_log($query);
	$rst = $cnn->consultar($query);
	
/*	while($arr=mysql_fetch_assoc($rst)){
		array_push($dato,$arr);
	}
	echo json_encode($dato);*/
	
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
		'h_ciudad'			=>utf8_encode($fila['h_ciudad'])
		);
	}
	echo json_encode($_arreglo);
}

//Obtener los conceptos
if (isset($_POST['conceptos'])) {
	$idTramite = $_POST['conceptos'];
	$cnn = new conexion();
	$dato=array();
	//$dato="";
	$query = sprintf("select svc_detalle_id,
svc_detalle_concepto,
cp_concepto,
svc_detalle_tramite,
svc_itinerario,
svc_detalle_monto_concepto,
svc_divisa,
d.div_nombre as div_nombre,
svc_conversion,
svc_monto_divisa

from sv_conceptos_detalle as svcd
inner join divisa as d
on svcd.svc_divisa = d.div_id
inner join cat_conceptosbmw as ccbmw
on svcd.svc_detalle_concepto = ccbmw.dc_id
where svcd.svc_detalle_tramite = '%s'
group by svcd.svc_detalle_id;",
$idTramite);
	error_log($query);
	$rst = $cnn->consultar($query);
	
/*	while($arr=mysql_fetch_assoc($rst)){
		array_push($dato,$arr);
	}
	echo json_encode($dato);*/
	
	while($fila=mysql_fetch_assoc($rst)){
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
		'svc_monto_divisa'				=>$fila['svc_monto_divisa']
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
			$query = sprintf("select pbmw.p_id as id_parametro,
	crbmw.re_nombre,
	prbmw.re_id as region,
	prbmw.pr_cantidad as cantidad,
	prbmw.div_id as divisa,
	d.div_tasa,
	(prbmw.pr_cantidad * d.div_tasa) as cantidad_en_mxn
	from parametrosbmw as pbmw
	inner join parametro_regionbmw as prbmw
	on pbmw.p_id = prbmw.p_id
	inner join cat_regionesbmw as crbmw
	on prbmw.re_id = crbmw.re_id
	inner join divisa as d
	on prbmw.div_id = d.div_id
	where pbmw.p_concepto = '%s'
	and crbmw.re_nombre = '%s'", $conceptoId, $regionNombre);
			error_log("REGION!!!!".$query);
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
	//contadores que me permitiran saber si hay un itinerario mezclado  o del mismo tipo , aplicable para varios o para 1
	$nacional=0;
	$internacional=0;	
	
	error_log("aqui esta".$idTramite);
	$query=sprintf("SELECT DISTINCT svi_solicitud FROM sv_itinerario WHERE svi_solicitud=(SELECT sv_id FROM solicitud_viaje WHERE sv_tramite=%s )",$idTramite);
	$rst = $cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {			
			$svi_solicitud=$fila['svi_solicitud'];
	}
	error_log("SVISOL".$svi_solicitud);
	//tomara el tipo de vaje para el itinerario
	$queryViaje=sprintf("SELECT svi_tipo_viaje FROM sv_itinerario WHERE  svi_solicitud=%s",$svi_solicitud);
	error_log($queryViaje);
	$tipoViaje = $cnn->consultar($queryViaje);	
	
	
	//se tomara el valor de svi_monto_vuelo_cotizacion (Para los iinerarios este siemrpe sera el mismo)
	$queryMontoCotiza=sprintf("SELECT DISTINCT  svi_monto_vuelo_cotizacion FROM sv_itinerario WHERE  svi_solicitud=%s",$svi_solicitud);
	$montoVueloCotiza = $cnn->consultar($queryMontoCotiza);	
	while ($fila = mysql_fetch_assoc($montoVueloCotiza)) {			
				$svi_monto_vuelo_cotizacion=$fila['svi_monto_vuelo_cotizacion'];
		}
		
		error_log("valor nuevo". $svi_monto_vuelo_total);
		error_log("vuelo Cotizacion".$svi_monto_vuelo_cotizacion);
		$dif = $svi_monto_vuelo_total + $svi_monto_vuelo_cotizacion;
		error_log($dif);
	//primero comprobamos si es solo 1 itinerario o mas
	$row= mysql_num_rows($tipoViaje);
	error_log("numero de filas".$row);
	
		
	while ($fila = mysql_fetch_assoc($tipoViaje)) {			
			if($row == 1){
					//se tendra un solo itinerario ahora solo se validara si es nacional /internacional
				if($fila['svi_tipo_viaje'] == 'Nacional'){					
					$indicador=$parametro->montoMaxToleranciaNacional($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion );
				}else if($fila['svi_tipo_viaje'] == 'Continental' || $fila['svi_tipo_viaje'] == 'Intercontinental'){
					$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total, $svi_monto_vuelo_cotizacion);					
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
		$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion );
	}elseif($internacional>$nacional && $nacional==0){ //itinerarios internacionales unicamente
		$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}elseif($internacional > $nacional && $nacional != 0){//itinerarios mezclados
		$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}elseif($nacional>$internacional && $internacional==0){//itinerarios nacionales unicamente
		$indicador=$parametro->montoMaxToleranciaNacional($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}elseif($nacional>$internacional && $internacional!=0){//itinerarios mezclados
		$indicador=$parametro->montoMaxToleranciaInter($svi_monto_vuelo_total,$svi_monto_vuelo_cotizacion);
	}
	
	error_log("nacional".$nacional);
	error_log("interna".$internacional);
	error_log($indicador);
	echo json_encode($indicador);
}

if(isset($_POST['tramiteId'])){
	error_log("aqui esta la modificacion de etapa");
	$tramite=new Tramite();		
	$tramiteId=$_POST['tramiteId'];
	$tramite=new Tramite();
	$tramite->Load_Tramite($tramiteId);
	$iniciador=$tramite->Get_dato("t_iniciador");
	$tramite->Modifica_Etapa($tramiteId, SOLICITUD_ETAPA_COMPRADA, FLUJO_SOLICITUD,$iniciador, "");
	
}

if(isset($_POST['tramiteCId'])){
	$cnn = new conexion();
	$tramiteId=$_POST['tramiteCId'];
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
		
	
	//Notificacion al gerente de area	
	//obtenemos el id del usuario al que pertenece ese Director de area
	$query=sprintf("SELECT sv_ceco_paga FROM solicitud_viaje WHERE sv_tramite=%s",$tramiteId);
	$rst=$cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {			
			$sv_ceco_paga=$fila['sv_ceco_paga'];
	}
	
	$queryResp=sprintf("SELECT cc_responsable FROM cat_cecos WHERE cc_id=%s",$sv_ceco_paga);
	$rstResp=$cnn->consultar($queryResp);
	while ($fila = mysql_fetch_assoc($rstResp)) {			
			$cc_responsable=$fila['cc_responsable'];
	}		
	
	$GerenteArea=new Empleado();
	$GerenteArea->Load_id_empleado($cc_responsable);
	$destinatario=$GerenteArea->Get_dato('idempleado');
	
	//construimos la segunda ruta de autorizacion
	$rutaAutorizacion=new RutaAutorizacion();
	$aprobadornuevo=$rutaAutorizacion->generaRutaSegundaAutorizacion($destinatario, $tramiteId);
	
	$tramite->Modifica_Etapa($tramiteId,SOLICITUD_ETAPA_SEGUNDA_APROBACION, FLUJO_SOLICITUD,$aprobadornuevo, "");
	
	
	$usuarioCreador = new Usuario();
	$usuarioCreador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
	
	$mensaje=sprintf("El monto que la agencia de viajes ha registrado para la compra del boleto de avi&oacute;n de la solicitud de viaje <strong>%05s</strong> creada por:<strong>%s</strong> excede el l&iacute;mite de tolerancia  y requiere de tu autorizaci&oacute;n.",$tramiteId,$usuarioCreador->Get_dato('nombre'));
	$remitente = $tramite->Get_dato("t_dueno");		
	//se le enviara al Gerente de area		
	$tramite->EnviaNotificacion($tramiteId, $mensaje, $remitente,$destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	
	//queda pendiente las notificaciones de la segunda ruta.
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
	
	$presupuesto=new Presupuesto();
	$diferenciaPresupuesto=$presupuesto->calculoPresupuestoXCECO($totalSolicitud, $cecoId);
	error_log("Indicador de presupuesto".$diferenciaPresupuesto);
	
	echo json_encode($diferenciaPresupuesto);

}



?>
