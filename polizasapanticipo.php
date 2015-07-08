<?php
require_once("./lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
$ruta = $RUTA_POLIZA;
$rarchivos = $ruta;
$rbackup = $rarchivos."backup/";
$genera = false;
$conexion = new conexion();
$sqlViaje = "SELECT
						svc_detalle_tramite AS detID,
						DATE_FORMAT(t.t_fecha_cierre, '%d.%m.%Y') AS fecha,
						'DA' AS tipoDoc, 
						e.e_codigo sociedad, 
						'MXN' moneda,
						t.t_id as tramite,
						concat(u.u_usuario,' ','antviaj') as usuario,
						u.u_usuario as user,
						'09' as clave,
						'2' as CME,
						IF(e.e_codigo = '310' OR e.e_codigo = '315' OR e.e_codigo = '320', 'SERV', 'AGUA') AS division,
						t.t_etiqueta as motivo,
						sv_total_anticipo as total
						FROM sv_conceptos_detalle sv
						INNER JOIN tramites t ON sv.svc_detalle_tramite = t.t_id
						INNER JOIN usuario u ON t.t_iniciador = u.u_id
						INNER JOIN empresas e ON u.u_empresa = e.e_codigo
                        INNER JOIN solicitud_viaje svj ON t.t_id = svj.sv_tramite
						WHERE t_fecha_cierre > 0
                        AND svj.sv_anticipo = 1
                        AND sv.svc_enviado_sap = 0
						group by t_id";
$resViaje = $conexion->consultar($sqlViaje);
$sqlGasto = "SELECT
				sg_tramite  AS detID,
				DATE_FORMAT(t.t_fecha_cierre, '%d.%m.%Y') AS fecha,
				'DA' AS tipoDoc, 
				e.e_codigo sociedad, 
				'MXN' moneda,
				t.t_id as tramite,
				concat(u.u_usuario,' ','antgast') as usuario,
				u.u_usuario as user,
				'09' as clave,
				'2' as CME,
				IF(e.e_codigo = '310' OR e.e_codigo = '315' OR e.e_codigo = '320', 'SERV', 'AGUA') AS division,
				t.t_etiqueta as motivo,
				sg_monto as total
					FROM solicitud_gastos
					INNER JOIN tramites t ON t.t_id = sg_tramite
					INNER JOIN usuario u ON t.t_iniciador = u.u_id
					INNER JOIN empresas e ON u.u_empresa = e.e_codigo
					WHERE t_etapa_actual = 3
					AND sg_enviado_sap is null
	                OR sg_enviado_sap = 0
					group by t_id";
$resGasto = $conexion->consultar($sqlGasto);
if(mysql_num_rows($resViaje)){
	$genera = true;
}
if(mysql_num_rows($resGasto)){
	$genera = true;
}
$page_print = "";
while($rowViaje = mysql_fetch_assoc($resViaje)) {
$detalleViaje[] = $rowViaje['detID'];
	$page_print .= $rowViaje['fecha'].chr(9).$rowViaje['tipoDoc'].chr(9).$rowViaje['sociedad'].chr(9).$rowViaje['moneda'].chr(9).$rowViaje['tramite'].chr(9).$rowViaje['usuario'].chr(9).$rowViaje['clave'].chr(9).$rowViaje['user'].chr(9).$rowViaje['CME'].chr(9).$rowViaje['total'].chr(9).$rowViaje['division'].chr(9).''.chr(9).''.chr(9).$rowViaje['motivo']."\n";
	$page_print .= $rowViaje['fecha'].chr(9).$rowViaje['tipoDoc'].chr(9).$rowViaje['sociedad'].chr(9).$rowViaje['moneda'].chr(9).$rowViaje['tramite'].chr(9).$rowViaje['usuario'].chr(9).'19'.chr(9).$rowViaje['user'].chr(9).$rowViaje['CME'].chr(9).$rowViaje['total'].chr(9).$rowViaje['division'].chr(9).''.chr(9).''.chr(9).$rowViaje['motivo']."\n";
}
if($detalleViaje > 0){
	$idv = implode(",",$detalleViaje);
	$sql = "UPDATE sv_conceptos_detalle SET svc_enviado_sap = 1 WHERE svc_detalle_tramite in (".$idv.")";
	$conexion->ejecutar($sql);
}
while($rowGasto = mysql_fetch_assoc($resGasto)) {
	$detalleGasto[] = $rowGasto['detID'];
	$page_print .= $rowGasto['fecha'].chr(9).$rowGasto['tipoDoc'].chr(9).$rowGasto['sociedad'].chr(9).$rowGasto['moneda'].chr(9).$rowGasto['tramite'].chr(9).$rowGasto['usuario'].chr(9).$rowGasto['clave'].chr(9).$rowGasto['user'].chr(9).$rowGasto['CME'].chr(9).$rowGasto['total'].chr(9).$rowGasto['division'].chr(9).''.chr(9).''.chr(9).$rowGasto['motivo']."\n";
	$page_print .= $rowGasto['fecha'].chr(9).$rowGasto['tipoDoc'].chr(9).$rowGasto['sociedad'].chr(9).$rowGasto['moneda'].chr(9).$rowGasto['tramite'].chr(9).$rowGasto['usuario'].chr(9).'19'.chr(9).$rowGasto['user'].chr(9).$rowGasto['CME'].chr(9).$rowGasto['total'].chr(9).$rowGasto['division'].chr(9).''.chr(9).''.chr(9).$rowGasto['motivo']."\n";
}
if($detalleGasto > 0){
	$idg= implode(",",$detalleGasto);
	$sql1 = "UPDATE solicitud_gastos SET sg_enviado_sap = 1 WHERE sg_tramite in (".$idg.")";
	$conexion->ejecutar($sql1);
}
if($genera == true){
	$consecutivo = leer_archivos_y_directorios($rarchivos);
	$myFile = date("Ymd")."_".$consecutivo."_anticipo.txt";
	$fh = fopen($ruta.$myFile, 'w');
	$stringData = trim($page_print);
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