<?php
require_once("../../lib/php/constantes.php");
require("../../Connections/fwk_db.php");
require_once("../../functions/Comprobacion.php");
require_once("../solicitudes/services/C_SV.php");
require_once("services/func_comprobacion.php");
require_once('../../lib/fpdf17/fpdf.php');

	class PDF extends FPDF{

	function Header(){
		$this->Image("../../images/logo2.png",10,10,50,30);
		$this->Image("../../images/logo2.png",210,10,50,30);
		$this->SetFont('Helvetica', 'B', 15);
		$this->SetTextColor(30,150,230);
		$this->SetTextColor(0,0,0);			
		$this->Ln(14);
		$this->SetFont('Helvetica', 'B', 12);
		$this->Cell(0,0,'REPORTE DE GASTOS',0,1,'C');
		$this->Ln(10);
	}

	function Footer(){
			$this->SetY(-15);
			$this->SetFont('consola','I',8);
			$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		}
		
		function createHead($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX){
			$heigthCell = $this->heigth($widthLimitString,$string,$heigthCell);
			for($i=0; $i<count($string); $i++){				
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
		
		function createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX){
			$heigthCell = $this->heigth($widthLimitString,$string,$heigthCell);	
			for($i=0; $i<count($string); $i++){
				$string[$i] = str_replace('..','..',$this->stringLength($widthLimitString[$i],$string[$i],$pixelLength[$i]));
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
		
		function stringLength($widthCell, $string,$pixelLength){
			if($pixelLength == 36){ //comentarios
				$stringLength = (strlen($string)>36) ? substr($string,0,33)." .." : $string ;
			}else if($pixelLength == 20){ // proveedores
				$stringLength = (strlen($string)>20) ? substr($string,0,$widthCell*1-4)." .." : $string ;
			}else{
				$stringLength = (strlen($string)>23) ? substr($string,0,$widthCell*1-4)." .." : $string ;
			}			
														
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
	$pdf->SetFont('consola', 'B', 12);
	$pdf->SetTextColor(0,0,0);
	$cnn = new conexion();
	$idTramite = $_GET['id'];
	$comidasRepresentacionencontrado = false;
	
	// Carga datos de autorizadores
	$rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = ($rutaAutorizacion->getNombreAutorizadores($idTramite));		
	
	//Se carga el tramite de la comprobacion
	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	
	//Se carga la comprobacion
	$query = sprintf("SELECT t_id, %s as t_fecha_registro, t_etiqueta, t_iniciador, t_dueno, t_ruta_autorizacion, t_etapa_actual, %s as co_fecha_inicial_gasolina, t_etapa_actual,
		u_nombre,u_paterno,u_materno,nombre,idfwk_usuario,idcentrocosto,cc_nombre,cc_centrocostos, co_motivo_gasolina, et_etapa_nombre, %s as  co_fecha_final_gasolina,
		co_total, co_cc_clave, t_delegado, co_observaciones, co_lugar, co_ciudad 
		FROM tramites
		INNER JOIN usuario ON tramites.t_iniciador=usuario.u_id
		INNER JOIN empleado ON tramites.t_iniciador=empleado.idfwk_usuario
		INNER JOIN comprobacion_gastos ON comprobacion_gastos.co_mi_tramite = tramites.t_id
		INNER JOIN cat_cecos ON co_cc_clave=cat_cecos.cc_id
		INNER JOIN etapas ON etapas.et_etapa_id = tramites.t_etapa_actual AND tramites.t_flujo = etapas.et_flujo_id
		WHERE t_id = '%s'
		AND tramites.t_flujo = '%s'", "DATE_FORMAT(t_fecha_registro,'%d/%m/%Y')", "DATE_FORMAT(co_fecha_inicial_gasolina,'%d/%m/%Y')", "DATE_FORMAT(co_fecha_final_gasolina,'%d/%m/%Y')", $idTramite, FLUJO_COMPROBACION_GASTOS);
	//error_log($query);
	$rst = $cnn->consultar($query);
	while($row = mysql_fetch_assoc($rst)){
		$t_etapa = $row["t_etapa_actual"];
		$t_id = $row["t_id"];
		$t_fechaRegistro = $row["t_fecha_registro"];
		$t_etiqueta = $row["t_etiqueta"];
		$t_etiqueta = $row["co_motivo_gasolina"];
		$fecha_inicial = $row["co_fecha_inicial_gasolina"];
		$fecha_final = $row["co_fecha_final_gasolina"];
		$t_iniciador = $row["t_iniciador"];
		$t_dueno = $row["t_dueno"];
		$t_rutaAutorizacion = $row["t_ruta_autorizacion"];
		$comprobacion_etapa= $row["t_etapa_actual"];
		$usuarioNombre = $row["u_nombre"];
		$usuarioApp = $row["u_paterno"];
		$usuarioApm = $row["u_materno"];
		$nombreEmpleado = $row["nombre"];
		$idfwk_usuario = $row["idfwk_usuario"];
		$idcentrocosto = $row["idcentrocosto"];
		$cc_nombre = $row["cc_nombre"];
		$cc_centrocostos = $row["cc_centrocostos"];
		$co_total= $row["co_total"];
		$et_etapa_nombre = $row["et_etapa_nombre"];
		$co_ceco = $row["co_cc_clave"];
		$t_delegado = $row['t_delegado'];
		$co_observaciones = $row['co_observaciones'];
		$co_lugar = $row['co_lugar'];
		$co_ciudad = $row['co_ciudad'];
	}
	
	// Cargar los invitados de la comprobacion
	$comensales = new Comensales();
	$invitadosComprobacion = $comensales->Load_comensales_comprobacion_by_tramite($t_id);
	
	// Construccion de cuadro principal (informacion general)
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Ln(7);
	$pdf->SetX(50);
	$pdf->Multicell(190,8,"Información General",'LRT','C', 0);
	
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"No. de Folio:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$idTramite,0,0,'L');
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Fecha de Creación:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$t_fechaRegistro,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Solicitante:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$nombreEmpleado,0,0,'L');
	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Centro de costos:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->MultiCell(55,3,$cc_centrocostos.' - '.$cc_nombre,0,'L');
	
	$pdf->ln(-3);
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Motivo:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->SetX(95);
	$pdf->MultiCell(65,3,$t_etiqueta,0,'L');
	
	$pdf->ln(-3);
	$pdf->SetX(140);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Etapa:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3, $et_etapa_nombre ,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetFont('consola', 'B', 10);
	$pdf->SetX(50);
	$pdf->MultiCell(45,3,"Autorizador(es):",0,'R');
	$pdf->Ln(-3);
	$pdf->SetFont('consola', '', 10);
	$pdf->SetX(95);
	$pdf->MultiCell(135,3,strip_tags($autorizadores),0,'L');
	
	$pdf->ln();
	$pdf->SetX(140);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3, "" ,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->Cell(190,-25,"",'LRT'); // El -20 Define la altura de la tabla
	// Fin de construccion de cuadro principal (informacion general)
	
	$pdf->SetFont('consola', 'B', 12);
	$pdf->Ln(1);
	$pdf->Cell(0,10,'Gastos Comprobados',0,1,'C');

	// Tipo de letra y estilo
	$pdf->SetFont('consola', 'B', 6);		
	// Cabeceras de la tabla de gastos generada
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(30,150,230);	
	$pdf->SetFont('consola', 'B', 6);
	$string           = array("No","Tipo","No. Transacción","Fecha","Concepto","Comentarios","No. As.","RFC","Proveedor","Factura","Monto","Total","Divisa","Total MXN");
	$widthLimitString = array(3,15,11,11,59,29,6,13,14,7,11,11,11,11,11);		
	$pixelLength 	  = array(5,15,15,14,63,25,9,18,20,11,18,18,9,15,18);
	$factor           = array(1,1 ,1 ,1 ,1 ,1 ,1 ,1 ,1 ,1 ,1 ,1 ,1 ,1);
	
	$heigthCell = 3;
	$pdf->createHead($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=4);
	
	// Despúes de quye Finanzas modifica la Tasa, esta se debe mostrar en adelante
	if($t_etapa == COMPROBACION_ETAPA_APROBADA || $t_etapa == COMPROBACION_ETAPA_EN_APROBACION_POR_SF || $t_etapa == COMPROBACION_ETAPA_APROBADA_POR_SF || $t_etapa == COMPROBACION_ETAPA_RECHAZADA_POR_SF){
		$montoPesos = " CASE dc_divisa
		WHEN '2' THEN (FORMAT((co_tasa_USD * (dc_monto + dc_iva + dc_imp_hospedaje)),2))
		WHEN '3' THEN (FORMAT((co_tasa_EUR * (dc_monto + dc_iva + dc_imp_hospedaje)),2))
		ELSE (FORMAT(dc_monto + dc_iva + dc_imp_hospedaje,2))
		END";
	}else{
		$montoPesos = " SELECT FORMAT((div_tasa * (dc_monto + dc_imp_hospedaje)),2) FROM divisa WHERE div_id = dc_divisa ";
	}
    // Datos del concepto
	$aux_concepto = Array();
	 $query2 = "SELECT 
					dc.dc_id, 
					dc_tipo, 
					dc_concepto,
					IFNULL((SELECT dc_notransaccion FROM amex WHERE idamex = dc_idamex_comprobado), 'N/A') AS notransaccion,
					IFNULL((SELECT(IF(notarjetacredito = tarjeta, 'AMEX corporativa Gastos', IF(notarjetacredito_gas = tarjeta, 'AMEX corporativa Gasolina','')))		
							FROM empleado, amex
							WHERE (tarjeta = notarjetacredito 
								OR tarjeta = notarjetacredito_gas)
							AND idamex = dc_idamex_comprobado LIMIT 1), false) AS 'tipoTarjeta', 
					dc_idamex_comprobado,
					cp_concepto AS cp_concepto,
					ccb.cp_id AS concepto_id,		
					dc_tipo_comida,										
					DATE_FORMAT(dc_fecha,'%d/%m/%Y') AS dc_fecha,					
					IFNULL((SELECT pro_proveedor FROM proveedores WHERE dc_rfc = pro_rfc LIMIT 1), 'N/A') AS dc_proveedor, 
					IF(dc_rfc IS NOT NULL AND dc_rfc != '', true, false) AS proveedorNacional,
					IF(dc_rfc != '', dc_rfc, 'N/A') AS dc_rfc,
					IF(dc_folio_factura != '', dc_folio_factura, 'N/A') AS dc_folio_factura,
					FORMAT(dc_monto,2) AS dc_monto,
					dc_divisa,
					(SELECT div_nombre FROM divisa WHERE dc_divisa = div_id) AS div_nombre,
					FORMAT(dc_iva,2) AS dc_iva,
					FORMAT(dc_total,2) AS dc_total,				
					(".$montoPesos.") AS dc_total_partida,
					dc_comentarios,
					FORMAT(dc_propinas,2) AS dc_propinas,
					dc_comensales,
					concepto,
					ex_mensaje,
					IF (dc_origen_personal= 0,0,1) AS dc_origen_personal,
					dc.dc_id AS dc_id
				FROM detalle_comprobacion_gastos AS dc
				JOIN comprobacion_gastos ON co_id = dc_comprobacion
				JOIN tramites ON t_id = co_mi_tramite
				JOIN cat_conceptos AS ccb ON dc_concepto = cp_id
				JOIN divisa ON  div_id = dc_divisa  
				LEFT JOIN amex ON idamex = dc.dc_idamex_comprobado
				LEFT JOIN excepciones ON dc.dc_id = ex_comprobacion_detalle
				WHERE t_id = $idTramite
				ORDER BY dc.dc_id";
	$rst2 = $cnn->consultar($query2);
	
	while($datos2=mysql_fetch_assoc($rst2)){
		array_push($aux_concepto,$datos2);
	}
	$pdf->SetFont('consola', '', 6);
	$pdf->SetTextColor(0,0,0);  
	$pdf->SetFillColor(255,255,255);		
	$cont=1;
	$total_gastos=0;
	foreach($aux_concepto as $row){
				
		$string = array($cont, substr($row["dc_tipo"], 16), $row["notransaccion"], $row["dc_fecha"], $row["cp_concepto"], $row["dc_comentarios"], $row["dc_comensales"], $row["dc_rfc"],
						$row["dc_proveedor"], $row["dc_folio_factura"], $row["dc_monto"], $row["dc_total"], $row["div_nombre"], $row["dc_total_partida"]);
		$heigthCell = 4;
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=4);    
		$total_gastos+= floatVal(preg_replace("/,/",'',$row['dc_total_partida']));
		$cont++;
		
		if($row['dc_concepto'] == COMIDAS_REPRESENTACION){
			$comidasRepresentacionencontrado = true;
		}
	}

	$pdf->Ln(4);
	$pdf->Cell(235,3,"Total comprobado: ",0,0,'R',0);
	$pdf->Cell(30,3,number_format((floatVal($co_total)),2,".",",")." MXN",0,1,'L',0);
	
	if($comidasRepresentacionencontrado){
		$pdf->SetFont('consola', 'B', 6);
		$pdf->Ln(2);
		$pdf->Cell(0,10,'Lista de invitados',0,1,'C');
		
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFillColor(30,150,230);
		$pdf->SetFont('consola', 'B', 5.5);
		$pdf->SetX(15);
		$string           = array("No.","Nombre","Puesto","Empresa","Tipo");
		$widthLimitString = array(4,49,32,43,14);
		$pixelLength 	  = array(7,60,40,53,20);
		$factor           = array(1,1,1,1,1);
		$heigthCell = 4;
		$pdf->createHead($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=55);
		$cont=0;
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('consola', '', 6);
		foreach($invitadosComprobacion as $datosAux){
			$cont++;
			$string = array($cont,$datosAux["c_nombre_invitado"], $datosAux["c_puesto_invitado"], $datosAux["c_empresa_invitado"], $datosAux["c_tipo_invitado"]);
			$heigthCell = 4;
			$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=55);
		}
		
		$pdf->Ln(2);
		$pdf->SetX(30);
		$pdf->SetFont('consola', 'B', 6);
		$pdf->Cell(45,3,"Ciudad: ",0,0,'R');
		$pdf->SetFont('consola', '', 7);
		$pdf->Cell(50,3,$co_ciudad,0,0,'L');
		
		$pdf->SetX(70);
		$pdf->SetFont('consola', 'B', 6);
		$pdf->Cell(75,3,"Lugar/Restaurante: ",0,0,'R');
		$pdf->SetFont('consola', '', 7);
		$pdf->Cell(80,3,$co_lugar,0,0,'L');
		
		$pdf->SetX(50);
		$pdf->SetFont('consola', '', 6);
		$pdf->Cell(180,3,"Total de invitados: ",0,0,'R',0);
		$pdf->SetFont('consola', 'B', 6);
		$pdf->Cell(190,3,$cont,0,1,'L',0);
	}
	
	$sql = "SELECT cp_concepto AS 'concepto', ex_mensaje AS 'mensaje', ex_diferencia AS excedente,
				(CASE ex_presupuesto
					WHEN 1 THEN 'Excepción de Presupuesto'
					WHEN 0 THEN 'Excepción de Política' 
					ELSE ''
				END) AS tipoExcepcion
				FROM excepciones, comprobacion_gastos, cat_conceptos
				WHERE  ex_comprobacion  = co_id
				AND ex_comprobacion_detalle = 0
				AND cp_id = ex_concepto
				AND co_mi_tramite = $idTramite
				
			UNION 
				
				SELECT 'Presupuesto' AS 'concepto', ex_mensaje AS 'mensaje', FORMAT(ex_diferencia,2) AS excedente, 
					IF(ex_presupuesto = 1, 'Excepción de Presupuesto', '') AS tipoExcepcion
				FROM excepciones 
				JOIN comprobacion_gastos ON co_id = ex_comprobacion
				LEFT JOIN tramites ON co_mi_tramite = t_id
				WHERE t_id = $idTramite
				AND ex_presupuesto = 1";
	$res = mysql_query($sql);
	$tot = mysql_num_rows($res);
	$cont = 0;
	$totalConcepto = 0;
	if($tot > 0){
		$pdf->Ln(2);
		$pdf->SetFont('consola', 'B', 7);
		$pdf->Ln(2);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetX(95);
		$pdf->Cell(83,5,'Excepciones',0,1,'C');		
		
		$pdf->SetFont('consola', 'B', 6);				
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFillColor(30,150,230);
		
		
		$string           = array("No.", "Concepto", "Mensaje", "Excedente", "Tipo de Excepción");
		$widthLimitString = array(6, 50, 70, 15, 35);		
		$pixelLength      = array(10, 65, 85, 25, 45);
		$factor           = array(1, 1, 1, 1, 1);
		
		$heigthCell = 4;	
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=25);
				
		$pdf->SetTextColor(0,0,0);  
		$pdf->SetFillColor(255,255,255);		
		while ($row = mysql_fetch_assoc($res)){
			$cont++;
			$string = array($cont, $row["concepto"], strip_tags($row["mensaje"]), $row["excedente"], $row["tipoExcepcion"]);
			$heigthCell = 4;	
			$pdf->createTable($string, $widthLimitString, $factor, $pixelLength, $heigthCell, $setX=25);
		}
		$pdf->Ln(2);
	}
	
	$pdf->Ln(10);
	$pdf->SetX(22);
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(25,3,"Historial de observaciones: ",0,1,'R',0);
	//cuadro de observaciones
	$pdf->Ln(-5);
	$pdf->SetX(45);
	$pdf->SetFont('consola', 'Z', 7);
	$pdf->MultiCell(130,6,$co_observaciones,1,'L');
	
	$sql = "SELECT (IFNULL(FORMAT(sg_monto_pesos,2), 0.00)) as sg_monto_pesos, FORMAT(co_anticipo_comprobado,2) as co_anticipo_comprobado,
	FORMAT(co_personal_descuento,2) as co_personal_descuento, FORMAT(co_amex_comprobado,2) as co_amex_comprobado,
	FORMAT(co_efectivo_comprobado,2) as co_efectivo_comprobado, FORMAT(co_mnt_descuento,2) as co_mnt_descuento,
	FORMAT(co_mnt_reembolso,2) as co_mnt_reembolso 
	FROM comprobacion_gastos
	JOIN tramites ON co_mi_tramite = t_id
	LEFT JOIN solicitud_gastos ON sg_tramite = co_tramite
	WHERE t_id=".$_GET['id'];
	//error_log("--->>Consulta de Montos: ".$sql);
	$res = @mysql_query($sql);
	$row = @mysql_fetch_array($res);
	
	//resumen
	$pdf->Ln(3);
	$pdf->SetX(190);
	$pdf->SetFont('consola', 'Z', 7);
	$pdf->MultiCell(75,60,"",1,'L');
	
	$pdf->Ln(-60);	
	$pdf->SetX(191);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(50,3,"Resumen",'',0,'R');
	
	$pdf->Ln(7);	
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Subtotal:",'',0,'R');	
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['subtotal'],0,0,"L");
		
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Descuento:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['descuento'],0,0,"L");
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"IVA:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['iva'],0,0,"L");
	
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Retencion de ISR:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['retisr'],0,0,"L");
	
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Retencion de IVA:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['retiva'],0,0,"L");

	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"IEPS:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['ieps'],0,0,"L");
	
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->SetTextColor('223','1','1');
	$pdf->Cell(55,3,"Total comprobacion Anticipo:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['anticipo'],0,0,"L");
	
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Total comprobacion AMEX:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['amex'],0,0,"L");
	
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Total de Reembolso:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['reembolso'],0,0,"L");
	
	$pdf->Ln(5);		
	$pdf->SetX(180);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Pendiente de comprobar:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(0,3,$_GET['pendiente'],0,0,"L");	
	
	$pdf->SetTextColor('0','0','0');
	$pdf->Ln(-4);
	$pdf->SetX(55);
	$pdf->SetFont('consola', 'U', 9);
	$pdf->Cell(45,3,"                   ",0,0,'L',0);
	$pdf->Cell(45,3,"                   ",0,1,'R',0);
	$pdf->Ln(2);
	$pdf->SetX(55);
	$pdf->SetFont('consola', 'B', 9);
	$pdf->Cell(45,3," Fecha de entrega ",0,0,'L',0);
	$pdf->Cell(45,3," Firma solicitante ",0,1,'R',0);
	
	$pdf->Ln(10);
	$pdf->SetX(60);
	$pdf->SetFont('consola', 'U', 8);
	$pdf->Cell(298,3,"NOTA: La documentación soporte se anexa en la parte posterior de esta hoja.",0,1,'C',0);
		
	$pdf->Output("prueba.pdf",'I');
	
	echo "<script language='javascript'>window.open('prueba.pdf','_self','');</script>";//para ver el archivo pdf generado
	exit;
?>