<?php
	//ob_end_clean();	
	require_once("../../lib/php/constantes.php");
	require_once("../../Connections/fwk_db.php");	
	require_once("../../functions/utils.php");
	require_once('../../lib/fpdf17/fpdf.php');
		
	class PDF extends FPDF{
	
		function Header(){
			$this->Image("../../images/logo2.png",10,10,45,25);
			$this->Image("../../images/logo2.png",230,10,45,25);
			$this->SetFont('Helvetica', 'B', 15);
			$this->SetTextColor(30,150,230);
			//$this->Text(15,22,"Mexico");
			$this->SetTextColor(0,0,0);			
			$this->Ln(9);
			$this->SetFont('Helvetica', 'B', 12);
			$this->Cell(0,10,'SOLICITUD DE VIAJE',0,1,'C');
			$this->Ln(6);
		}
		
		function Footer(){
			$this->SetY(-15);
			$this->SetFont('consola','I',8);
			$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		}	

		function createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX){
			$heigthCell = $this->heigth($widthLimitString,$string,$heigthCell);	
			for($i=0; $i<count($string); $i++){
				$string[$i] = str_replace('...','',$this->stringLength($widthLimitString[$i],$string[$i]));
				$factor[$i] = $this->factorLength($widthLimitString[$i],$string[$i],$heigthCell);		
			}
			$setX = $setX;
			for($i=0; $i<count($string); $i++){
				$this->SetX($setX);
				$setX+= $pixelLength[$i];			
				$this->MultiCell($pixelLength[$i],$heigthCell/$factor[$i],$string[$i],1,'C',1);
				if(!($i==(count($string)-1))){
					$this->Ln(-$heigthCell);	
				}
			}				
		}
		
		function createHotel($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX){
			$heigthCell = $this->heigth($widthLimitString,$string,$heigthCell);
			for($i=0; $i<count($string); $i++){
				$string[$i] = str_replace('...','...',$this->stringLengthHotel($widthLimitString[$i],$string[$i]));
				$factor[$i] = $this->factorLength($widthLimitString[$i],$string[$i],$heigthCell);
			}
			$setX = $setX;
			for($i=0; $i<count($string); $i++){
				$this->SetX($setX);
				$setX+= $pixelLength[$i];
				$this->MultiCell($pixelLength[$i],$heigthCell/$factor[$i],$string[$i],1,'C',1);
				if(!($i==(count($string)-1))){
					$this->Ln(-$heigthCell);
				}
			}
		}
		
		function heigth($widthCell, $stringLength, $heightCell){
			$height = $heightCell;
			for($i = 0; $i < count($stringLength); $i++){
				if(strlen($stringLength[$i])>$widthCell[$i]) $height = $heightCell*2;
			}			
			return $height;
		}
		
		function stringLengthHotel($widthCell, $string){
			$stringLength = (strlen($string)>$widthCell) ? substr($string,0,$widthCell*1-4)." ..." : $string ;
			return $stringLength;
		}
		
		function stringLength($widthCell, $string){
			$stringLength = (strlen($string)>$widthCell) ? substr($string,0,$widthCell*2-4)." ..." : $string ;			
			return $stringLength;			
		}
		
		function factorLength($widthCell, $string, $heightCell){
			$factor = (strlen($string)>$widthCell) ? 2 : 1 ;			
			return $factor;			
		}		
	}		
	
	$pdf = new PDF('L','mm','Letter');
	
	$pdf->AddFont('consola','','consola.php');
	$pdf->AddFont('consola','B','consolab.php');
	$pdf->AddFont('consola','Z','consolaz.php');
	$pdf->AddFont('consola','I','consolai.php');
	
	$pdf->AliasNbPages();
	$pdf->AddPage();	
	$pdf->SetTextColor(0,0,0);	
	$idTramite = $_GET['id'];
	
	if($cnn = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		mysql_select_db($MYSQL_DATABASE);
	else
		die("Error de conexión.");
	
	//Cabecera del reporte de solicitude de viaje	
	$sql = "SELECT DATE_FORMAT(t_fecha_registro,'%d/%m/%Y') as fecha_registro, nombre, CONCAT(cc_centrocostos,' - ',cc_nombre) as ceco, t_etiqueta, et_etapa_nombre,
			(SELECT nombre FROM empleado WHERE idfwk_usuario = t_delegado) as t_delegado,
			(SELECT MIN(svi_fecha_salida) FROM sv_itinerario WHERE svi_solicitud = sv_id) AS svi_fecha_salida,
			(SELECT MAX(svi_fecha_llegada) FROM sv_itinerario WHERE svi_solicitud = sv_id) AS svi_fecha_llegada,
			IF(sv_viaje = 'Multidestinos','Multidestinos',
				IF(sv_viaje = 'Redondo',CONCAT((SELECT svi_origen FROM sv_itinerario WHERE svi_solicitud = sv_id ORDER BY svi_fecha_salida LIMIT 1),'|',(SELECT svi_destino FROM sv_itinerario WHERE svi_solicitud = sv_id ORDER BY svi_fecha_llegada DESC LIMIT 1)),(SELECT svi_destino FROM sv_itinerario WHERE svi_solicitud = sv_id ORDER BY svi_fecha_llegada DESC LIMIT 1))) AS destino,
			(SELECT SUM(svi_dias_viaje) FROM sv_itinerario WHERE svi_solicitud = sv_id) AS dias_viaje, t_iniciador
			FROM tramites
			JOIN usuario ON u_id = t_iniciador
			JOIN empleado ON idfwk_usuario = u_id
			JOIN solicitud_viaje ON sv_tramite = t_id
			JOIN cat_cecos ON cc_id = sv_ceco_paga
			JOIN etapas ON et_flujo_id = t_flujo AND et_etapa_id = t_etapa_actual
			WHERE t_id = ".$idTramite;
	//error_log("Query".$sql);			
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	//TIPO DE VIAJE EN LA SOLICITUD.
	$sql ="SELECT sv_viaje FROM solicitud_viaje WHERE sv_tramite = $idTramite";
	$res = mysql_query($sql);
	$tipoViajeSol=mysql_result($res,0,"sv_viaje");	
	
	$fecha_registro = $row["fecha_registro"];
	$solicitante = $row["nombre"];
	$ceco = $row["ceco"];
	$motivo = $row["t_etiqueta"];
	$etapa = $row["et_etapa_nombre"];
	$iniciador = $row["t_iniciador"];
	$co_fecha_salida = cambiarFormatoFecha($row["svi_fecha_salida"]);
	$co_fecha_llegada= cambiarFormatoFecha($row["svi_fecha_llegada"]);
	//$fecha_viaje = $row["fecha_viaje"];
	$destino = $row["destino"];
	//se obtiene la suma de los dias de cada uno de los itinerarios que poseea
	$tramiteDias=new Tramite();
	$dias_viaje = $tramiteDias->calculoDias($idTramite);
	
	$t_delegado = $row["t_delegado"];
	$rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = ($rutaAutorizacion->getNombreAutorizadores($idTramite));		
    
	//construccion de cuadro principal (informacion general)
	$pdf->SetFont('consola', 'B', 12);
	$pdf->Ln(7);
	$pdf->SetX(50);
	$pdf->Multicell(190,8,"Información general",'LRT','C',0);
	
	$pdf->SetX(50);
	$pdf->Cell(45,3,"No. de Folio:",0,0,'R');	
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$idTramite,0,0,'L');	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Fecha de creación:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$fecha_registro,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Solicitante:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	if( $t_delegado != ''){		
		$pdf->Cell(45,3,$t_delegado,0,0,'L');	
		$pdf->Ln(2.5);
		$pdf->SetX(95);
		$pdf->Cell(45,3,"EN NOMBRE DE: ".$solicitante,0,0,'L');	
		$pdf->Ln(-.5);
		$pdf->SetX(140);
	}else{	
		$pdf->Cell(45,3,$solicitante,0,0,'L');	
	}
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Centro de costos:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->MultiCell(55,3,$ceco,0,'L');
	$pdf->Ln(-3);
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Motivo:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->MultiCell(60,3,$motivo,0,'L');
	$pdf->Ln(-3);	
	$pdf->SetX(140);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Etapa:",0,0,'R');	
	$pdf->SetFont('consola', '', 10);
	$pdf->MultiCell(45,3,$etapa,0,'L');
	$pdf->Ln(-3);
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Fecha de viaje:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->SetX(95);
	//validacion de la fecha de viaje dependiendo el tipo de viaje
	if( $tipoViajeSol == "Redondo" ){
		$pdf->Cell(45,3,$co_fecha_salida. "|" .$co_fecha_llegada ,0,0,'L');
	}elseif( $tipoViajeSol == "Multidestinos" ){
		$pdf->Cell(45,3,$co_fecha_salida."|".$co_fecha_llegada,0,0,'L');
	}elseif( $tipoViajeSol == "Sencillo" ){
		$pdf->Cell(45,3,$co_fecha_salida,0,0,'L');
	}
	$pdf->SetX(140);
	//$pdf->Cell(45,3,$fecha_viaje,0,0,'L');	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Destino:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$destino,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->Cell(45,3,"",0,0,'R');	
	$pdf->Cell(45,3,"",0,0,'L');	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Días de viaje:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$dias_viaje,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->MultiCell(45,3,"Autorizador(es):",0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Ln(-3);
	$pdf->SetX(95);
	$pdf->MultiCell(135,3,strip_tags($autorizadores),0,'L');	
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->Cell(190,-28,"",'LRT');
	//fin de datos de informacion general		
		
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Ln(2);
	$pdf->Cell(0,10,'Itinerarios de viaje',0,1,'C');
	
	//Tipo de letra y estilo
	$pdf->SetFont('consola', 'B', 6);				
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(30,150,230);	
	
	//CABECERA TABLA ITINERARIOS
	if($tipoViajeSol == "Redondo"){
		$string = array("No.","Zona Geográfica","Región","Origen","Destino","Fecha de Salida","Horario de salida","Transporte","Hospedaje","Hospedaje MXN","Noches",
				"Renta auto","Renta de auto MXN","Fecha de Regreso","Horario de Regreso");
		$widthLimitString = array(4,18,18,18,18,18,14,12,9,14,10,9,12,12,17);
		$pixelLength 	  = array(8,21,23,25,25,20,20,16,13,18,12,11,18,18,20);
		$factor 		  = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$setX			  = 3;
	}else{
		$string = array("No.","Zona Geográfica","Región","Origen","Destino","Fecha de Salida","Horario de salida","Transporte","Hospedaje","Hospedaje MXN","Noches",
				"Renta auto","Renta de auto MXN");
		$widthLimitString = array(4,18,18,18,18,18,14,12,12,14,12,12,12);		
		$pixelLength 	  = array(8,25,25,25,25,20,20,18,18,18,18,18,18);
		$factor 		  = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		$setX             = 10;
	}
		
	$heigthCell = 4;	
	$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX);
	

	//CUERPO TABLA ITINERARIOS
	$pdf->SetFont('consola', '', 6);
	$pdf->SetTextColor(0,0,0);  
	$pdf->SetFillColor(255,255,255);
	$sql = "SELECT  svi_tipo_viaje, svi_origen, svi_destino, DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') AS fecha_salida, re_nombre,
				svi_horario_salida, svi_tipo_transporte, IF(svi_hotel=1,'SI','NO') AS hotel, 
				IF(h_total_pesos IS NULL,'0.00',FORMAT(SUM(h_total_pesos),2)) AS h_total_pesos,
				IF(SUM(h_noches),SUM(h_noches),0) AS noches,
				IF(svi_renta_auto=1,'SI','NO') AS auto,
				FORMAT(svi_total_pesos_auto,2) AS total_auto,
				IF(sv_viaje = 'Redondo',DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y'),'') AS fecha_regreso,
				IF(sv_viaje = 'Redondo',svi_horario_llegada,'') AS svi_horario_llegada	
			FROM sv_itinerario svi
			LEFT JOIN solicitud_viaje sv ON sv_id = svi_solicitud
			LEFT JOIN cat_regionesbmw ON re_id = svi_region
			LEFT JOIN hotel h ON h.svi_id = svi.svi_id  
			WHERE sv_tramite = $idTramite
			GROUP BY svi.svi_id";
	$res = mysql_query($sql);
	$cont = 0;
	while ($row = mysql_fetch_assoc($res)){
		$cont++;
		
		if($tipoViajeSol == "Redondo"){
			$string = array($cont,$row["svi_tipo_viaje"],$row["re_nombre"],$row["svi_origen"],$row["svi_destino"],$row["fecha_salida"],$row["svi_horario_salida"],$row["svi_tipo_transporte"],
					$row["hotel"],$row["h_total_pesos"],$row["noches"],$row["auto"],$row["total_auto"],$row["fecha_regreso"],$row["svi_horario_llegada"]);
			$setX   = 3;
		}else{
			$string = array($cont,$row["svi_tipo_viaje"],$row["re_nombre"],$row["svi_origen"],$row["svi_destino"],$row["fecha_salida"],$row["svi_horario_salida"],$row["svi_tipo_transporte"],
					$row["hotel"],$row["h_total_pesos"],$row["noches"],$row["auto"],$row["total_auto"]);
			$setX   = 10;
		}
						
		$heigthCell = 4;		
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX);	
	}	
	
	//COTIZACION AVION
	$pdf->Ln(1);
	$pdf->SetFont('consola', 'B', 7);	
	$pdf->Cell(0,7,'Cotizaciones',0,1,'C');	
	$pdf->Cell(0,5,'Datos del avión',0,1,'L');

	$pdf->SetFont('consola', 'B', 6);				
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(30,150,230);	
	//CABECERA AVIONES
	if($tipoViajeSol == "Redondo" || $tipoViajeSol == "Multidestinos" ){
		$string           = array("No.","Tipo de viaje","Aerolinea","Clase","Fecha de Salida","Fecha de Regreso","Subtotal","IVA","TUA","Total");
		$widthLimitString = array(4,19,22,22,22,22,22,22,22,22);
		$pixelLength      = array(8,26,30,30,30,30,30,21,21,30);
		$factor           = array(1,1,1,1,1,1,1,1,1,1);
	}else{
		$string           = array("No.","Tipo de viaje","Aerolinea","Clase","Fecha de Salida","Subtotal","IVA","TUA","Total");
		$widthLimitString = array(14,19,22,22,22,22,22,22,22);
		$pixelLength      = array(20,26,30,30,30,30,30,30,30);
		$factor           = array(1,1,1,1,1,1,1,1,1);
	}	
	
	$heigthCell = 4;
	$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);
	
	//CUERPO TABLA AVIONES
	$pdf->SetFont('consola', '', 6);
	$pdf->SetTextColor(0,0,0);  
	$pdf->SetFillColor(255,255,255);
	$sql = "SELECT svi_tipo_viaje, svi_aerolinea, se_nombre, DATE_FORMAT(svi_fecha_salida_avion,'%d/%m/%Y') AS fecha_salida,
				IF(sv_viaje = 'Multidestinos',(
				SELECT DATE_FORMAT(MAX(svi_fecha_salida),'%d/%m/%Y') AS 'svi_fecha_salida' FROM sv_itinerario 
				INNER JOIN solicitud_viaje ON svi_solicitud= sv_id
				INNER JOIN tramites ON sv_tramite = t_id
				WHERE t_id = $idTramite),DATE_FORMAT(svi_fecha_regreso_avion,'%d/%m/%Y')) AS fecha_regreso,
				svi_tipo_aerolinea,
				FORMAT(svi_monto_vuelo,2) AS svi_monto_vuelo, FORMAT(svi_iva,2) AS svi_iva, FORMAT(svi_tua,2) AS svi_tua, FORMAT(svi_monto_vuelo_cotizacion,2) AS svi_monto_vuelo_total
			FROM sv_itinerario 
			JOIN solicitud_viaje ON sv_id = svi_solicitud
			JOIN servicios AS se ON (svi_tipo_aerolinea = se.se_id)
			WHERE sv_tramite = $idTramite
			AND svi_aerolinea IS NOT NULL
			GROUP BY svi_solicitud";
	$res = mysql_query($sql);
	$cont = 0;
	$totalAvion = 0;
	while ($row = mysql_fetch_assoc($res)){
		$cont++;
		if($tipoViajeSol == "Redondo" || $tipoViajeSol == "Multidestinos"){
			$string = array($cont,$row["svi_tipo_viaje"],$row["svi_aerolinea"],$row["se_nombre"],$row["fecha_salida"],$row["fecha_regreso"],$row["svi_monto_vuelo"],$row["svi_iva"],
					$row["svi_tua"],$row["svi_monto_vuelo_total"]);
			$totalAvion+= floatval(preg_replace("/,/","",$string[9]));
		}else{
			$string = array($cont,$row["svi_tipo_viaje"],$row["svi_aerolinea"],$row["svi_tipo_aerolinea"],$row["fecha_salida"],$row["svi_monto_vuelo"],$row["svi_iva"],
					$row["svi_tua"],$row["svi_monto_vuelo_total"]);
			$totalAvion+= floatval(preg_replace("/,/","",$string[8]));
		}			
		
		$heigthCell = 4;
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);		
	}
		
	$pdf->SetFont('consola', 'B', 7);		
	$pdf->SetTextColor(0,0,0);			
	$pdf->SetX(235);
	$pdf->Cell(18,4,"Total: ","" ,'L');			
	$pdf->SetX(249);
	$pdf->Cell(18,4,number_format($totalAvion,2,'.',',')." MXN",0,0,'R');	
	
	//COTIZACION HOTEL	
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Ln(1);		
	$pdf->Cell(0,5,'Datos del hotel',0,1,'L');
	
	
	$pdf->SetFont('consola', 'B', 6);				
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(30,150,230);

	//CABECERA HOTEL
	$string           = array("No.","No. itinerario","Nombre hotel","Ciudad","Comentarios","Noches","Monto por noche","Divisa","Fecha de llegada","Fecha de salida","Total",
							  "No. de reservación");
	$widthLimitString = array(4,18,18,18,20,14,18,14,16,16,16,16);		
	$pixelLength      = array(8,24,25,25,26,20,20,20,22,22,22,22);
	$factor           = array(1,1,1,1,1,1,1,1,1,1,1,1);
	
	$heigthCell = 4;	
	$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);
	
	//CUERPO TABLA HOTEL
	$pdf->SetFont('consola', '', 6);
	$pdf->SetTextColor(0,0,0);  
	$pdf->SetFillColor(255,255,255);
	$sql = "SELECT (svi.svi_id-IFNULL((SELECT (MIN(svi_id)-1) FROM sv_itinerario WHERE svi_solicitud = sv_id),0)) AS svi_id, h_nombre_hotel, h_ciudad, h_comentarios, h_noches, FORMAT(h_costo_noche,2) as h_costo_noche, div_nombre,
				DATE_FORMAT(h_fecha_llegada,'%d/%m/%Y') AS fecha_llegada, DATE_FORMAT(h_fecha_salida,'%d/%m/%Y') AS fecha_salida,
				FORMAT(h_total_pesos,2) as h_total_pesos, h_no_reservacion
			FROM sv_itinerario AS svi
			JOIN hotel AS ho ON ho.svi_id = svi.svi_id
			JOIN solicitud_viaje ON sv_id  = svi_solicitud
			JOIN divisa d ON d.div_id = ho.div_id
			WHERE sv_tramite = $idTramite
			ORDER BY svi_id";
	$res = mysql_query($sql);
	$cont = 0;
	$totalHotel = 0;
	
	while($row = mysql_fetch_assoc($res)){
		$cont++;		
		$string = array($cont,$row["svi_id"],$row["h_nombre_hotel"],$row["h_ciudad"],$row["h_comentarios"],$row["h_noches"],$row["h_costo_noche"],
					$row["div_nombre"],$row["fecha_llegada"],$row["fecha_salida"],$row["h_total_pesos"],$row["h_no_reservacion"]);
		$heigthCell = 4;
		$pdf->createHotel($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);
		$totalHotel+= floatval(preg_replace("/,/","",$string[10]));
	}		
	$pdf->SetFont('consola', 'B', 7);				
	$pdf->SetX(235);
	$pdf->Cell(18,4,"Total: ","" ,'L');			
	$pdf->SetX(249);
	$pdf->Cell(18,4,number_format($totalHotel,2,'.',',')." MXN",0,0,'R');	
	
	//COTIZACION AUTO
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Ln(1);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(0,5,'Datos del auto',0,1,'L');
	
	$pdf->SetFont('consola', 'B', 6);				
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(30,150,230);
	
	//CABECERA AUTO
	$string           = array("No.","No. itinerario","Empresa","Tipo de auto","Días de renta","Costo por día","Divisa","Total");
	$widthLimitString = array(14,21,25,25,25,22,22,22);		
	$pixelLength      = array(21,28,38,38,38,31,31,31);
	$factor           = array(1,1,1,1,1,1,1,1);	
	$heigthCell = 4;	
	$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);
    
	//CUERPO AUTO
	$pdf->SetFont('consola', '', 6);
	$pdf->SetTextColor(0,0,0);  
	$pdf->SetFillColor(255,255,255);
	$sql = "SELECT (svi_id-IFNULL((SELECT (MIN(svi_id)-1) FROM sv_itinerario WHERE svi_solicitud = sv_id),0)) AS svi_id, svi_empresa_auto,
			IF(svi_tipo_auto = 1,'Compacto','Mediano') AS svi_tipo_auto, svi_dias_renta, FORMAT(svi_costo_auto,2) AS svi_costo_auto , div_nombre, FORMAT(svi_total_pesos_auto,2) AS svi_total_pesos_auto
			FROM sv_itinerario 
			LEFT JOIN solicitud_viaje ON sv_id = svi_solicitud 
			LEFT JOIN divisa ON div_id = svi_divisa_auto			
			WHERE sv_tramite = $idTramite
			AND svi_renta_auto = 1";
	$res = mysql_query($sql);
	$cont = 0;
	$totalAuto = 0;
	while($row = mysql_fetch_assoc($res)){
		$cont++;
		$string = array($cont,$row["svi_id"],$row["svi_empresa_auto"],$row["svi_tipo_auto"],$row["svi_dias_renta"],$row["svi_costo_auto"],$row["div_nombre"],$row["svi_total_pesos_auto"]);
		$heigthCell = 4;
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);
		$totalAuto+= floatval(preg_replace("/,/","",$string[7]));
	}
	$pdf->SetFont('consola', 'B', 7);		
	$pdf->SetTextColor(0,0,0);			
	$pdf->SetX(235);
	$pdf->Cell(18,4,"Total: ","" ,'L');			
	$pdf->SetX(249);
	$pdf->Cell(18,4,number_format($totalAuto,2,'.',',')." MXN",0,0,'R');
	
	// Conceptos
	$sql = "SELECT 
					(1 + svc_itinerario - (SELECT MIN(svi_id) FROM sv_itinerario JOIN solicitud_viaje ON svi_solicitud = sv_id WHERE sv_tramite = $idTramite)) AS itinerario,
					cp_concepto AS concepto,
					FORMAT(svcd.svc_conversion,2) AS total,
					IFNULL(ex_mensaje, 'N/A') AS mensaje, 
					IFNULL(FORMAT(ex_diferencia,2), 'N/A') AS excedente,
					IFNULL(FORMAT(pd_monto * div_tasa,2), 'N/A') AS politica,
					(CASE ex_presupuesto
						WHEN 1 THEN 'Excepción de Presupuesto'
						WHEN 0 THEN 'Excepción de Política' 
						ELSE 'N/A'
					END) AS tipoExcepcion
				FROM sv_conceptos_detalle AS svcd
				LEFT JOIN solicitud_viaje AS sv ON sv.sv_tramite = svcd.svc_detalle_tramite
				LEFT JOIN tramites AS t ON sv.sv_tramite = t.t_id
				LEFT JOIN cat_conceptos ON svcd.svc_detalle_concepto = cp_id
				LEFT JOIN excepciones ON ex_solicitud_detalle = svcd.svc_detalle_id
				LEFT JOIN politicas_flujos ON (politicas_flujos.c_id = cp_id AND politicas_flujos.re_id = (SELECT svi_region FROM sv_itinerario WHERE svi_id = svc_itinerario) AND politicas_flujos.b_id = (SELECT b_id FROM empleado WHERE idfwk_usuario = $iniciador AND politicas_flujos.f_id = 1) ) 
				LEFT JOIN politicas_divisa ON politicas_flujos.pf_id = politicas_divisa.pf_id 
				LEFT JOIN divisa ON divisa.div_id = politicas_divisa.div_id
				WHERE t.t_id = $idTramite
				
			UNION 
				
				SELECT 'N/A' AS itinerario, 'Presupuesto' AS concepto, 'N/A' AS total, ex_mensaje AS mensaje, FORMAT(ex_diferencia,2) AS excedente, 
						'N/A' AS politica, IF(ex_presupuesto = 1, 'Excepción de Presupuesto', '') AS tipoExcepcion
				FROM excepciones 
				JOIN solicitud_viaje ON sv_id = ex_solicitud
				LEFT JOIN tramites ON sv_tramite = t_id
				WHERE t_id = $idTramite
				AND ex_presupuesto = 1
			ORDER BY itinerario";
	$res = mysql_query($sql);
	$tot = mysql_num_rows($res);
	$cont = 0;
	$totalConcepto = 0;
	if($tot > 0){
		$pdf->Ln(5);
		$pdf->SetFont('consola', 'B', 7);
		$pdf->Ln(2);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetX(95);
		$pdf->Cell(0,5,'El empleado ha solicitado los siguientes anticipos',0,1,'L');		
		
		$pdf->SetFont('consola', 'B', 6);				
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFillColor(30,150,230);
		
		
		$string           = array("No.", "No. Itinerario", "Concepto", "Anticipo", "Excepción", "Política", "Excedente", "Tipo de Excepción");
		$widthLimitString = array(6, 18, 65, 13, 35, 13, 15, 25);		
		$pixelLength      = array(10, 20, 65, 20, 40, 20, 40, 40);
		$factor           = array(1, 1, 1, 1, 1, 1, 1, 1);
		
		$heigthCell = 4;	
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=10);
				
		$pdf->SetTextColor(0,0,0);  
		$pdf->SetFillColor(255,255,255);		
		while ($row = mysql_fetch_assoc($res)){
			$cont++;
			$string = array($cont, $row["itinerario"], $row["concepto"], $row["total"], strip_tags($row["mensaje"]), $row["excedente"], $row["politica"], $row["tipoExcepcion"]);
			$heigthCell = 4;	
			$pdf->createTable($string, $widthLimitString, $factor, $pixelLength, $heigthCell, $setX=10);
			$totalConcepto += floatval(preg_replace("/,/","", $string[3]));	
		}
		
		$pdf->SetFont('consola', 'B', 7);		
		$pdf->SetTextColor(0,0,0);			
		$pdf->SetX(220);
		$pdf->Cell(18,4,"Total Anticipo(s): ","" ,'L');			
		$pdf->SetX(248);
		$pdf->Cell(18,4,number_format($totalConcepto,2,'.',',')." MXN",0,0,'R');
		$pdf->ln(15);
	}else{
		$pdf->Ln(5);
		$pdf->SetFont('consola', 'B', 7);		
		$pdf->SetTextColor(0,0,0);		
		$pdf->Cell(0,5,'El empleado no solicitó ningún anticipo',0,1,'C');	
	}
	
	//TOTAL SOLICITUD
	$sql="SELECT sv_total FROM solicitud_viaje WHERE sv_tramite = $idTramite";
	$res = mysql_query($sql);
	$totalSolicitud=mysql_result($res,0,"sv_total");
	$pdf->SetFont('consola', 'B', 7);		
	$pdf->SetTextColor(0,0,0);		
	$pdf->SetX(222);
	$pdf->Cell(18,4,"Total solicitud: ","" ,'R');			
	$pdf->SetX(249);
	$pdf->Cell(18,4,number_format($totalSolicitud,2,'.',',')." MXN",0,0,"R");			
	
	//HISTORIAL DE OBSERVACIONES
	$sql = "SELECT sv_observaciones 
			FROM solicitud_viaje 
			WHERE sv_tramite = $idTramite";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	$pdf->Ln();		
	$pdf->SetX(10);	
	$pdf->Multicell(50,5,"Historial de observaciones:",'','L');		
	$pdf->SetX(30);
	
	$pdf->Multicell(220,7,$row["sv_observaciones"],'LRT','L');		
	$pdf->SetX(30);
	$pdf->Cell(220,-3,"",'LRT');		
	
	
	$pdf->Output("Reporte_Solicitud_$idTramite.pdf",'I');		
	echo "<script>
			window.open('Reporte_Solicitud_$idTramite.pdf','_self','');
		</script>";
	mysql_close($cnn);
	exit;
?>