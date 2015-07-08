<?php
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
			$this->SetTextColor(0,0,0);			
			$this->Ln(9);
			$this->SetFont('Helvetica', 'B', 12);
			$this->Cell(0,10,'SOLICITUD DE GASTOS',0,1,'C');
			$this->Ln(6);
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
	$reqAnticipo = "";
	
	//Se cargan los comensales de la comprobacion de invitacion
	$comensales = new Comensales();
	$invitados = $comensales->Load_comensales_solicitud_by_tramite($idTramite);
	 
	if($cnn = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		mysql_select_db($MYSQL_DATABASE);
	else
		die("Error de conexión.");
	
	//Cabecera del reporte de solicitude de gastos	
	$sql = "SELECT sg_id, t_iniciador, t_id, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y') AS fecha_registro, t_etiqueta, FORMAT(sg_monto, 2) AS sg_monto, 
			sg_divisa, FORMAT(sg_monto_pesos, 2) AS sg_monto_pesos, IF(sg_ciudad = '', 'N/A', sg_ciudad) AS sg_ciudad, sg_observaciones, 
			DATE_FORMAT(sg_fecha_gasto,'%d/%m/%Y') AS fecha_gasto, IF(sg_lugar = '', 'N/A', sg_lugar) AS sg_lugar, sg_requiere_anticipo, 
			sg_concepto, t_dueno,  CONCAT(cc_centrocostos,' - ',cc_nombre) as ceco, t_etapa_actual, t_flujo, cc_nombre, t_delegado, 
			et_etapa_nombre, nombre, div_nombre, cp_concepto 
			FROM solicitud_gastos as sg
			INNER JOIN tramites as t ON (t.t_id = sg.sg_tramite) 
			INNER JOIN empleado ON (idfwk_usuario = t_iniciador) 
			LEFT JOIN cat_cecos as cc ON (sg.sg_ceco = cc.cc_id) 
			INNER JOIN etapas ON (et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo) 
			INNER JOIN divisa ON (div_id = sg_divisa) 
			INNER JOIN cat_conceptos ON (cp_id = sg_concepto) 
			WHERE t_id = ".$idTramite;
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	$fecha_registro = $row["fecha_registro"];
	$fecha_gasto = $row["fecha_gasto"];
	$solicitante = $row["nombre"];
	$ceco = $row["ceco"];
	$motivo = $row["t_etiqueta"];
	$etapa = $row["et_etapa_nombre"];
	$lugar = $row["sg_lugar"];
	$ciudad = $row["sg_ciudad"];
	$conceptoGasto = $row["cp_concepto"];
	$observaciones = $row["sg_observaciones"];
	$montoSolicitado = $row["sg_monto"];
	$montoPesos = $row["sg_monto_pesos"];
	$divisa = $row["div_nombre"];
	$anticipo = $row["sg_requiere_anticipo"];
	$idconcepto = $row['sg_concepto'];
	
	$t_delegado = $row["t_delegado"];
	$rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = ($rutaAutorizacion->getNombreAutorizadores($idTramite));
    
    if($anticipo){
    	$reqAnticipo = "La Solicitud de Gastos requiere anticipo.";
    }
    
	// Construccion de cuadro principal (informacion general)
	$pdf->SetFont('consola', 'B', 12);
	$pdf->Ln(7);
	$pdf->SetX(50);
	$pdf->Multicell(190,8,"Información general",'LRT','C',0);
	
	$pdf->SetX(50);
	$pdf->Cell(45,3,"No. de Folio:",0,0,'R');	
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$idTramite,0,0,'L');	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Lugar/Restaurante:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$lugar,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Solicitante:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	if( $t_delegado != 0){		
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
	$pdf->Cell(45,3,"Fecha de creación:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->MultiCell(55,3,$fecha_registro,0,'L');
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
	$pdf->Cell(45,3,"Centro de Costos:",0,0,'R');	
	$pdf->SetFont('consola', '', 10);
	$pdf->MultiCell(45,3,$ceco,0,'L');
	$pdf->Ln(-3);
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Fecha de gasto:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->SetX(95);
	$pdf->Cell(45,3,$fecha_gasto,0,0,'L');
	$pdf->SetX(140);	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"Etapa de la solicitud:",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,$etapa,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->Cell(45,3,"Concepto: ",0,0,'R');	
	$pdf->Cell(45,3,$conceptoGasto,0,0,'L');	
	$pdf->SetFont('consola', 'B', 10);
	$pdf->Cell(45,3,"",0,0,'R');
	$pdf->SetFont('consola', '', 10);
	$pdf->Cell(45,3,"",0,0,'L');
	
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
	// Fin de datos de informacion general
	
	if($idconcepto == COMIDAS_REPRESENTACION){
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
		foreach($invitados as $datosAux){
			$cont++;
			$string = array($cont,$datosAux["c_nombre_invitado"], $datosAux["c_puesto_invitado"], $datosAux["c_empresa_invitado"], $datosAux["c_tipo_invitado"]);
			$heigthCell = 4;
			$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=55);
		}
		
		$pdf->Ln(2);
		$pdf->SetX(50);
		$pdf->SetFont('consola', '', 6);
		$pdf->Cell(180,3,"Total de invitados: ",0,0,'R',0);
		$pdf->SetFont('consola', 'B', 6);
		$pdf->Cell(190,3,$cont,0,1,'L',0);
	}
	
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Ln(10);
	$pdf->SetX(50);
	$pdf->Multicell(190,1,"",'LRT','C',0);
	
	$pdf->Ln(13);
	$pdf->SetX(50);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(45,3,"Ciudad: ",0,0,'R',0);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(55,3,$ciudad,0,1,'L',0);
	
	$pdf->Ln(-3);
	$pdf->SetX(155);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(30,3,"Total Monto Solicitado: ",0,0,'R',0);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(30,3,$montoSolicitado." ".$divisa,0,1,'L',0);
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(45,3,"Historial de observaciones:",0,1,'R',0);
	
	//$pdf->Ln();
	$pdf->SetX(95);
	$pdf->SetFont('consola', 'Z', 6);
	$pdf->MultiCell(130,3,$observaciones,1,'L');
	
	$pdf->Ln();
	$pdf->SetX(50);
	$pdf->Cell(190,-28,"",'LRT');
	
	$pdf->Output("Reporte_Solicitud_$idTramite.pdf",'I');		
	echo "<script>
			window.open('Reporte_Solicitud_$idTramite.pdf','_self','');
		</script>";
	mysql_close($cnn);
	exit;
?>