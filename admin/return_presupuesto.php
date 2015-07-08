<?php
require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

$cnn	= new conexion();

$querySelP = sprintf("SELECT * FROM periodo_presupuestal WHERE pp_presupuesto_disponible < 0");
$rsSelP = $cnn->consultar($querySelP);

while($rowsSelP = mysql_fetch_assoc($rsSelP)){
	$idPres          = $rowsSelP['pp_id'];
	$cecoPres        = $rowsSelP['pp_ceco'];
	$disponiblePres  = $rowsSelP['pp_presupuesto_disponible'];
	$inicialPres 	 = $rowsSelP['pp_presupuesto_inicial'];
	
	$queryUpP = sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible = pp_presupuesto_inicial WHERE pp_id = %s AND pp_presupuesto_disponible < 0",$idPres);
	$rsUpP	  = $cnn->insertar($queryUpP);
	
	$queryComp = sprintf("SELECT SUM(COM.co_total_aprobado) as AprobadoTot FROM comprobaciones COM, tramites TRA WHERE COM.co_mi_tramite = TRA.t_id AND TRA.t_etapa_actual = 4 AND COM.co_cc_clave = %s",$cecoPres);
	$rsComp    = $cnn->consultar($queryComp);
	while($rowsComp = mysql_fetch_assoc($rsComp)){
		$AprobadoTot = $rowsComp['AprobadoTot'];
		echo "CECO -> ".$cecoPres."    Mont -> ".$AprobadoTot."<br>";
		
		$querySelPresIni = sprintf("SELECT pp_presupuesto_disponible FROM periodo_presupuestal WHERE pp_id = %s", $idPres);
		$rsSelPresIni = $cnn->consultar($querySelPresIni);
		if($rowPresIni = mysql_fetch_assoc($rsSelPresIni)){
			$topPresIni = $rowPresIni['pp_presupuesto_disponible'];
		}
		$rsResta = $topPresIni - $AprobadoTot;
		
		echo $rsResta."<br>";
		
		$queryUpComp = sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible = %s WHERE pp_id = %s ",$rsResta, $idPres);
		$rsUpComp = $cnn->insertar($queryUpComp);
	}
	
	$querySolAmex = sprintf("SELECT SUM(SA.sa_anticipo) as AprobadoTot FROM solicitud_amex SA, tramites TRA WHERE SA.sa_tramite = TRA.t_id AND TRA.t_etapa_actual = 4 AND SA.sa_ceco_paga = %s",$cecoPres);
	$rsSolA    = $cnn->consultar($querySolAmex);
	while($rowSolA = mysql_fetch_assoc($rsSolA)){
		$AprobadoTotA = $rowSolA['AprobadoTot'];
		echo "CECO -> ".$cecoPres."    Mont2 -> ".$AprobadoTotA."<br>";
		
		$querySelPresIniA = sprintf("SELECT pp_presupuesto_disponible FROM periodo_presupuestal WHERE pp_id = %s", $idPres);
		$rsSelPresIniA = $cnn->consultar($querySelPresIniA);
		if($rowPresIniA = mysql_fetch_assoc($rsSelPresIniA)){
			$topPresIniA = $rowPresIniA['pp_presupuesto_disponible'];
		}
		$rsRestaA = $topPresIniA - $AprobadoTotA;
		echo $rsRestaA."<br>";
		
		$queryUpAmex = sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible = %s WHERE pp_id = %s ",$rsRestaA, $idPres);
		$rsUpAmex = $cnn->insertar($queryUpAmex);
	}
	
	$querySolViaje = sprintf("SELECT SUM(SV.sv_anticipo) as AprobadoTot FROM solicitud_viaje SV, tramites TRA WHERE SV.sv_tramite = TRA.t_id AND TRA.t_etapa_actual = 4 AND SV.sv_ceco_paga = %s",$cecoPres);
	$rsSolV    = $cnn->consultar($querySolViaje);
	while($rowSolV = mysql_fetch_assoc($rsSolV)){
		$AprobadoTotV = $rowSolV['AprobadoTot'];
		echo "CECO -> ".$cecoPres."    Mont3 -> ".$AprobadoTotV."<br>";
		
		$querySelPresIniV = sprintf("SELECT pp_presupuesto_disponible FROM periodo_presupuestal WHERE pp_id = %s", $idPres);
		$rsSelPresIniV = $cnn->consultar($querySelPresIniV);
		if($rowPresIniV = mysql_fetch_assoc($rsSelPresIniV)){
			$topPresIniV = $rowPresIniV['pp_presupuesto_disponible'];
		}
		$rsRestaV = $topPresIniV - $AprobadoTotV;
		echo $rsRestaV."<br>";
		
		$queryUpViaje = sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible = %s WHERE pp_id = %s ",$rsRestaV, $idPres);
		$rsUpViaje = $cnn->insertar($queryUpViaje);
	}
		
		
}
?>