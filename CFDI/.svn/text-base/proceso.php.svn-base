<?php 
	/**
	 * ALTER TABLE factura 
	 * ADD COLUMN f_procesada INT NOT NULL DEFAULT 0  AFTER f_folioCfdiRegistroFiscal ,
	 * ADD COLUMN f_fecha_proceso DATETIME NULL  AFTER f_procesada ;
	 * 
	 * ALTER TABLE factura 
	 * ADD INDEX f_procesada (f_procesada ASC) ;		
	 * 
	 * #Proceso para crear facturas en una estructura 
	 *  0 1 * * * curl http://121.0.0.1/envasadroas/CFDI/proceso.php 
	 */	
	require_once("../lib/php/constantes.php");
	require_once("../Connections/fwk_db.php");
	require_once("../lib/php/configuracion.mailgun.php");
	
	$conexion = new Conexion();
	$directorioSalida = realpath(dirname(__FILE__)."/../../respaldoxml");
	$mensaje = "";
	
	ini_set('memory_limit', '1024M');
	set_time_limit(6000);
	
	$mensaje.= logger("INFO", "=======================");
	$mensaje.= logger("INFO", "Comienza Proceso");
	$mensaje.= logger("INFO", "Creando conexion a BD");
	$mensaje.= logger("INFO", "Consultando facturas por procesar");
	
	$sql = "SELECT DATE_FORMAT(t_fecha_cierre, '%Y%m%d') AS t_fecha_cierre, uuid, factura.id_factura, dc_comprobacion, urlMD5, c_folio, YEAR(t_fecha_cierre) as year, DATE_FORMAT(t_fecha_cierre, '%m') as month
			FROM factura 
			JOIN detalle_comprobacion ON detalle_comprobacion.id_factura = factura.id_factura
			JOIN comprobaciones ON dc_comprobacion = co_id
			JOIN tramites ON co_mi_tramite = t_id
			WHERE f_procesada = 0
			AND t_etapa_actual = 3
			
			UNION 
			
			SELECT DATE_FORMAT(t_fecha_cierre, '%Y%m%d') AS t_fecha_cierre, uuid, factura.id_factura, dc_comprobacion, urlMD5, c_folio, YEAR(t_fecha_cierre) as year, DATE_FORMAT(t_fecha_cierre, '%m') as month
			FROM factura 
			JOIN detalle_comprobacion_gastos ON detalle_comprobacion_gastos.id_factura = factura.id_factura
			JOIN comprobaciones ON dc_comprobacion = co_id
			JOIN tramites ON co_mi_tramite = t_id
			WHERE f_procesada = 0
			AND t_etapa_actual = 3
			
			GROUP BY id_factura
			";
	$res = $conexion->consultar($sql);		
	$row = $conexion->get_data_array($res);
	$tot = $conexion->get_rows($res);		
	$mensaje.= logger("INFO", "$tot Facturas encontradas");	
	
	
	foreach($row as $key => $val){
		try{
			$facturaString = file_get_contents(dirname(__FILE__)."/../flujos/comprobaciones/".$val["urlMD5"].".xml");
			$facturaXml = new SimpleXMLElement($facturaString);
			$receptor = $facturaXml->xpath("//cfdi:Receptor");
			$rfcReceptor = (string)$receptor[0]["rfc"];
			
			if(!validaDirectorio($directorioSalida, $rfcReceptor))
				creaDirectorio($directorioSalida, $rfcReceptor);
			if(!validaDirectorio($directorioSalida."/".$rfcReceptor, $val["year"]))
				creaDirectorio($directorioSalida."/".$rfcReceptor, $val["year"]);
			if(!validaDirectorio($directorioSalida."/".$rfcReceptor."/".$val["year"], $val["month"]))
				creaDirectorio($directorioSalida."/".$rfcReceptor."/".$val["year"], $val["month"]);		
			
			$nombre = creaNombre($val);
			$archivo = creaArchivo($directorioSalida."/".$rfcReceptor."/".$val["year"]."/".$val["month"]."/".$nombre, $facturaString);
			
			$mensaje.= logger("INFO", "Actualizando registro ".$val["id_factura"]);
			$sql = "UPDATE factura	
					SET 
						f_procesada = 1,
						f_fecha_proceso = NOW()
					WHERE id_factura = ".$val["id_factura"];
			$conexion->insertar($sql);
		}catch(Exception $e){
			$mensaje.= logger("ERROR", dirname(__FILE__)."/../flujos/comprobaciones/".$val["urlMD5"].".xml - ".$e->getMessage());		
		}
	}	
	
	$mensaje.= logger("INFO", "Finalizando proceso");			
	echo $mensaje.= logger("INFO", "Enviado Correo");		
	parametroMail("Log respaldo Facturas", "notificacionesmn@gmail.com", $mensaje);
	
	function validaDirectorio($base, $subDirectorio){
		logger("INFO", "Validando directorio $base/$subDirectorio");
		return (file_exists($base."/".$subDirectorio) && is_dir($base."/".$subDirectorio));		
	}
	
	function creaDirectorio($base, $subDirectorio){
		logger("INFO", "Creando directorio $base/$subDirectorio");
		return mkdir($base."/".$subDirectorio);		
	}
	
	function creaNombre($data){
		logger("INFO", "Creando nombre ".$data["dc_comprobacion"]."T&E".$data["f_folio"]."_".$data["t_fecha_cierre"].".xml");
		return $data["dc_comprobacion"]."_".$data["t_fecha_cierre"]."_".strtoupper($data["uuid"]).".xml";	
	}
		
	function creaArchivo($directorio, $string){
		logger("INFO", "Creando archivo $directorio");
		return file_put_contents($directorio, $string);
	}	

	function logger($type, $msg){		
		return date("Y-m-d H:i:s")." [$type] ".$msg." \n";				
	}
	
	
?>
