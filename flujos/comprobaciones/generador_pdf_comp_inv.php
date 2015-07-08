<?php
require("../../Connections/fwk_db.php");
require_once("../../functions/Comprobacion.php");
require_once("../solicitudes/services/C_SV.php");
require_once("services/func_comprobacion.php");
	ob_end_clean();
	require_once('../../lib/fpdf17/fpdf.php');

	class PDF extends FPDF{

		function Header(){
			$this->Image("../../images/logo-grupobmw2.jpg",10,10,49,10);
			$this->Image("../../images/LogosBMW.JPG",170,10,23,26);
			$this->SetFont('Helvetica', 'B', 15);
			$this->SetTextColor(30,150,230);
			$this->Text(15,22,"Mexico");
			$this->SetTextColor(0,0,0);			
			$this->Ln(9);
			$this->SetFont('Helvetica', 'B', 12);
			$this->Cell(0,10,'REPORTE DE INVITACIÓN',0,1,'C');
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
	
	$pdf = new PDF('P','mm','Letter');
	
	$pdf->AddFont('consola','','consola.php');
	$pdf->AddFont('consola','B','consolab.php');
	$pdf->AddFont('consola','Z','consolaz.php');
	$pdf->AddFont('consola','I','consolai.php');
	
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$cnn = new conexion();
	$idTramite = $_GET['id'];
	
	//Se cargan los comensales de la comprobacion de invitacion
	$comensales = new Comensales();
	$invitados = $comensales->Load_comensales_by_tramite($idTramite);
	
	// Carga datos de autorizadores
	$rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = ($rutaAutorizacion->getNombreAutorizadores($idTramite));		
	
	//Se carga el tramite de la comprobacion
	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	
	//Se carga la comprobacion
	$comp_inv = new Comprobacion();
	$comp_inv->Load_Comprobacion_Invitacion_By_co_mi_tramite($idTramite);
	
	//Se obtiene el id del tramite de la solicitud de invitacion
	$id_tramite_sol_inv = $comp_inv->Get_dato("co_tramite");

	//Se carga la solicitud de invitacion a la que hace referencia la comprobacion
	$sol_inv = new C_SV();
	$sol_inv->Load_Solicitud_Invitacion_Tramite($id_tramite_sol_inv);
	
	//Datos de la comprobacion
	$idComprobacion    = $comp_inv->Get_dato("co_id");
    $co_fecha_registro = cambiarFormatoFecha($comp_inv->Get_dato("co_fecha_registro"));
    $co_tipo    = $comp_inv->Get_dato("co_tipo");
    $motivo     = $comp_inv->Get_dato("co_motivo");
    $ciudad     = $comp_inv->Get_dato("co_ciudad");
    $co_tramite = $comp_inv->Get_dato("co_tramite");
    $co_cc_clave = $comp_inv->Get_dato("co_cc_clave");
    $subtotal_comp	= number_format($comp_inv->Get_dato("co_subtotal"),2,".",",");
    $iva_comp	= number_format($comp_inv->Get_dato("co_iva"),2,".",",");
    $total_comp	= number_format($comp_inv->Get_dato("co_total"),2,".",",");
    $co_total_aprobado = number_format($comp_inv->Get_dato("co_total_aprobado"),2,".",",");
    $co_pendiente = number_format($comp_inv->Get_dato("co_total") - $comp_inv->Get_dato("co_total_aprobado"),2,".",",");
    $observaciones = $comp_inv->Get_dato("co_observaciones");
    $co_num_invitados = $comp_inv->Get_dato("co_num_invitado");
    $co_lugar = $comp_inv->Get_dato("co_lugar");
    $co_fecha_invitacion = cambiarFormatoFecha($comp_inv->Get_dato("co_fecha_invitacion"));
    $co_hubo_excedente = $comp_inv->Get_dato("co_hubo_exedente");	
    $referencia = $tramite->Get_dato("t_etiqueta");
    $t_owner    = $tramite->Get_dato("t_dueno");
    $t_iniciador    = $tramite->Get_dato("t_iniciador");
	$comprobacion_etapa = $tramite->Get_dato("t_etapa_actual");
	$comprobacion_flujo = $tramite->Get_dato("t_flujo");
	$t_delegado = $tramite->Get_dato("t_delegado");
	
	$sql = "SELECT nombre FROM empleado WHERE idfwk_usuario = ".$t_delegado;
	$rst = $cnn->consultar($sql);
	$fila = mysql_fetch_assoc($rst);
	$t_delegado = $fila['nombre'];

    $usu = new Usuario();
	$usu->Load_Usuario_By_ID($t_iniciador);
	$nombreEmpleado=$usu->Get_dato("nombre");

	$query = "SELECT FORMAT(dc_monto,2) AS dc_monto, FORMAT(dc_iva,2) AS dc_iva, FORMAt(dc_propinas,2) AS dc_propinas, dc_divisa, dc_proveedor, pro_proveedor, pro_rfc,
			FORMAT(dci_monto_total_pesos,2) AS dci_monto_total_pesos, dc_concepto
			FROM detalle_comprobacion_invitacion AS dci 
			LEFT JOIN proveedores AS p on p.pro_id = dci.dc_proveedor 
			WHERE dc_comprobacion = $idComprobacion";	
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$arrayAux = $fila;
	$dci_monto = $fila['dc_monto'];
	$rfc = $fila['pro_rfc'];
	$dci_iva = $fila['dc_iva'];
	$dci_divisa = $fila['dc_divisa'];
	$dci_propina = $fila['dc_propinas'];
	$dci_proveedor = $fila['dc_proveedor'];
	$p_proveedor = $fila['pro_proveedor'];
	$dci_concepto = $fila['dc_concepto'];
	$co_total = $total_comp;
	$dci_monto_total_pesos = $fila['dci_monto_total_pesos'];	
	
	$query = "select cp_concepto from cat_conceptosbmw where dc_id = $dci_concepto";
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$dci_concepto_nombre = $fila['cp_concepto'];
	
	//Carga el nombre de la etapa en que esta la comprobacion de invitacion
	$etapa = new Etapa();
	$etapa->Load_Etapa_by_etapa_y_flujo($comprobacion_etapa,$comprobacion_flujo);
	$comprobacion_etapa_nombre = $etapa->Get_dato("et_etapa_nombre");

    $divisa	= $sol_inv->Get_dato("si_divisa");
    $total_solicitado	= number_format($sol_inv->Get_dato("si_monto"),2,".",",");   
    $monto_pesos	= number_format($sol_inv->Get_dato("si_monto_pesos"),2,".",",");   

	
	
    // datos del centro de costos
	$cc = new CentroCosto();
	$cc->Load_CeCo($co_cc_clave);
    $cc_centrocostos=$cc->Get_Dato("cc_centrocostos");
    $cc_nombre=$cc->Get_Dato("cc_nombre");

    // Datos de la Divisa
    $Divisa = new Divisa();
    $Divisa->Load_data($dci_divisa);
    $div_nombre = $Divisa->Get_dato("div_nombre");
	
   

	$pdf->SetFont('consola', 'B', 6.5);
	$pdf->Ln(7);
	$pdf->SetX(15);
	$pdf->Multicell(180,8,"Información de la comprobación de solicitud de invitación",'LRT','C');
	
	$pdf->SetX(15);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(45,3,"Trámite:",0,0,'R');
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(45,3,$idTramite,0,0,'L');
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(45,3,"Fecha de creación:",0,0,'R');
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(45,3,$co_fecha_registro,0,0,'L');
	
	$pdf->Ln();
	$pdf->SetX(15);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(45,3,"Solicitante:",0,0,'R');
	$pdf->SetFont('consola', '', 6);
	$pdf->SetX(60);
	if( $t_delegado != ''){		
		$pdf->Cell(45,3,$t_delegado,0,0,'L');	
		$pdf->Ln(2.5);
		$pdf->SetX(60);
		$pdf->Cell(45,3,"EN NOMBRE DE: ".$nombreEmpleado,0,0,'L');			
	}else{	
		$pdf->Cell(45,3,$nombreEmpleado,0,0,'L');			
	}	
	$pdf->SetFont('consola', 'B', 6);
	$pdf->SetX(105);
	$pdf->Cell(45,3,"Centro de costos:",0,0,'R');
	$pdf->SetFont('consola', '', 6);
	$pdf->MultiCell(45,3,$cc_centrocostos.' - '.$cc_nombre,0,'L');
	$pdf->Ln(-3);
	
	$pdf->Ln(3);			
	$pdf->SetX(15);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(45,3,"Lugar de invitación/Restaurante:",0,0,'R');
	$pdf->SetFont('consola', '', 6);
	$pdf->SetX(60);
	$pdf->Cell(45,3,$co_lugar,0,0,'L');
	$pdf->SetX(105);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(45,3,"Etapa de la comprobación:",0,0,'R');
	$pdf->SetFont('consola', '', 6);
	$pdf->MultiCell(45,3,$comprobacion_etapa_nombre,0,'L');
	$pdf->Ln(-3);
	$pdf->SetFont('consola', 'B', 6);
	
	$pdf->Ln();
	$pdf->SetFont('consola', 'B', 6);
	$pdf->SetX(15);
	$pdf->MultiCell(45,3,"Fecha de invitación:",0,'R');
	$pdf->Ln(-3);
	$pdf->SetFont('consola', '', 6);
	$pdf->SetX(60);
	$pdf->MultiCell(45,3,$co_fecha_invitacion,0,'L');
	$pdf->Ln(-3);
	$pdf->SetX(105);
	$pdf->Cell(90,3,"",0,0,'R');

	$pdf->Ln();
	$pdf->SetFont('consola', 'B', 6);
	$pdf->SetX(15);
	$pdf->MultiCell(45,3,"Autorizador(es):",0,'R');
	$pdf->Ln(-3);
	$pdf->SetFont('consola', '', 5.7);
	$pdf->SetX(60);
	$pdf->MultiCell(135,3,strip_tags($autorizadores),0,'L');

	$pdf->Ln();
	$pdf->SetX(15);
	$pdf->Cell(180,-26,"",'LRT');	
	
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Ln(1);
	$pdf->Cell(0,10,'Conceptos comprobados',0,1,'C');

	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(30,150,230);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->SetX(15);
	$string           = array("No.","Tipo","Fecha","Concepto","Comentario","Asis.","RFC","Proveedores","Monto","Divisa","IVA","Propina","Total");
	$widthLimitString = array(4,12,10,14,29,8,13,14,10,6 ,10,10,10);		
	$pixelLength 	  = array(6,16,15,20,36,8,17,20,15,9,15,15,15);
	$factor           = array(1,1 ,1 ,1 ,1 ,1,1,1 ,1 ,1 ,1 ,1 ,1);	
	$heigthCell = 4;
	$pdf->createHead($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=4);
	
	$pdf->SetFont('consola', '', 6);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);	
	$cont = 0;
	$sql = "SELECT CASE co_tipo
				WHEN 1 THEN 'Amex'
				WHEN 2 THEN 'Anticipos'
				WHEN 3 THEN 'Reembolso'
				WHEN 4 THEN 'Amex Externo'
				END AS co_tipo, DATE_FORMAT(dc_factura, '%d/%m/%Y') as dc_factura, cp_concepto, co_num_invitado, IF(dc_rfc='','N/A',dc_rfc) as dc_rfc, IF(pro_proveedor IS NULL,'N/A',pro_proveedor) as pro_proveedor , FORMAT(dc_monto,2) as dc_monto, div_nombre,
				FORMAT(dc_propinas,2) as dc_propinas, FORMAT(dc_iva,2) as dc_iva, FORMAT(dc_total,2) as dc_total, dc_comentarios
			FROM tramites
			JOIN comprobacion_invitacion ON co_mi_tramite = t_id
			JOIN detalle_comprobacion_invitacion dci ON dci.dc_comprobacion = co_id
			JOIN cat_conceptosbmw con ON con.dc_id = dci.dc_concepto
			JOIN divisa ON div_id = dc_divisa
			LEFT JOIN proveedores ON pro_id = dc_proveedor
			WHERE co_mi_tramite = $idTramite";
	$res = mysql_query($sql);
	while($row = mysql_fetch_assoc($res)){
		$cont++;
		$string = array($cont,$row["co_tipo"],$row['dc_factura'],$row["cp_concepto"],$row["dc_comentarios"],$row["co_num_invitado"],$row["dc_rfc"],$row["pro_proveedor"],$row["dc_monto"],$row["div_nombre"],
						$row["dc_iva"],$row["dc_propinas"],$row["dc_total"]);	
		$heigthCell = 5;
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=35);
	}	

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
	$pdf->createHead($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=15);
	$cont=0;
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('consola', '', 6);
	foreach($invitados as $datosAux){
		$cont++;	
		$string = array($cont,$datosAux["dci_nombre_invitado"],$datosAux["dci_puesto_invitado"],$datosAux["dci_empresa_invitado"],$datosAux["dci_tipo_invitado"]);
		$heigthCell = 4;
		$pdf->createTable($string,$widthLimitString,$factor,$pixelLength,$heigthCell,$setX=15);	
	}

	$pdf->Ln(2);
	$pdf->SetX(15);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(25,3,"Total de invitados: ",0,0,'R',0);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(10,3,$co_num_invitados,0,1,'L',0);

	$pdf->Ln(-3);
	$pdf->SetX(135);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(30,3,"Total monto solicitado: ",0,0,'R',0);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(30,3,$total_solicitado." ".$divisa,0,1,'L',0);

	$pdf->Ln(1);
	$pdf->SetX(15);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(25,3,"Ciudad: ",0,0,'R',0);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(20,3,$ciudad,0,1,'L',0);

	$pdf->Ln(-3);
	$pdf->SetX(135);
	$pdf->SetFont('consola', '', 6);
	$pdf->Cell(30,3,"Total comprobado: ",0,0,'R',0);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(30,3,$dci_monto_total_pesos." MXN",0,1,'L',0);

	$pdf->Ln(2);
	$pdf->SetX(15);
	$pdf->SetFont('consola', '', 6);
	if($co_hubo_excedente != 1){
		$pdf->Cell(25,3,"Observaciones: ",0,1,'R',0);
	}else{
		$pdf->Cell(45,3,"Justificación de motivo del excedente: ",0,1,'R',0);
	}
	$pdf->Ln(1);
	$pdf->SetX(40);
	$pdf->SetFont('consola', 'Z', 6);
	$pdf->MultiCell(130,3,$observaciones,1,'L');

	$sql = "SELECT FORMAT(co_amex_comprobado,2) as co_amex_comprobado, FORMAT(co_efectivo_comprobado,2) as co_efectivo_comprobado, 
			FORMAT(co_personal_descuento,2) as co_personal_descuento, FORMAT(co_mnt_descuento,2) as co_mnt_descuento, FORMAT(co_mnt_reembolso,2) as co_mnt_reembolso
			FROM comprobacion_invitacion 
			WHERE co_mi_tramite = ".$_GET['id'];
	$res = @mysql_query($sql);
	$row = @mysql_fetch_assoc($res);
	//resumen
	$pdf->Ln(7);
	$pdf->SetX(135);
	$pdf->SetFont('consola', 'Z', 7);
	$pdf->MultiCell(60,35,"",1,'L');
	
	$pdf->Ln(-33);	
	$pdf->SetX(110);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(65	,3,"Resumen",'',0,'R');
	
	$pdf->Ln(7);	
	$pdf->SetX(115);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Amex comprobado:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(25,3,$row['co_amex_comprobado']." MXN",0,0,"R");
		
	$pdf->Ln(5);		
	$pdf->SetX(115);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Efectivo comprobado:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(25,3,$row['co_efectivo_comprobado']." MXN",0,0,"R");
	
	$pdf->Ln(5);		
	$pdf->SetX(115);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Personal a descontar:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(25,3,$row['co_personal_descuento']." MXN",0,0,"R");

	$pdf->SetTextColor('223','1','1');
	$pdf->Ln(5);		
	$pdf->SetX(115);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Descuento:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(25,3,$row['co_mnt_descuento']." MXN",0,0,"R");
	
	$pdf->Ln(5);		
	$pdf->SetX(115);
	$pdf->SetFont('consola', 'B', 7);
	$pdf->Cell(55,3,"Reembolso:",'',0,'R');
	$pdf->SetFont('consola', '', 7);
	$pdf->Cell(25,3,$row['co_mnt_reembolso']." MXN",0,0,"R");
	
	$pdf->SetTextColor('0','0','0');
	$pdf->Ln(-1);
	$pdf->SetX(30);
	$pdf->SetFont('consola', 'U', 6);
	$pdf->Cell(45,3,"                               ",0,0,'L',0);
	$pdf->Cell(45,3,"                               ",0,1,'R',0);
	
	$pdf->Ln(1);
	$pdf->SetX(40);
	$pdf->SetFont('consola', 'B', 6);
	$pdf->Cell(45,3," Fecha de entrega ",0,0,'L',0);
	$pdf->Cell(24,3," Firma solicitante ",0,1,'R',0);

	$pdf->Ln(10);
	$pdf->SetX(70);
	$pdf->SetFont('consola', 'U', 8);
	$pdf->Cell(130,3,"Nota: La documentación soporte se anexa en la parte posterior de esta hoja.",0,1,'C',0);
		
	$pdf->Output("prueba.pdf",'I');
	
	echo "<script language='javascript'>window.open('prueba.pdf','_self','');</script>";//para ver el archivo pdf generado
	exit;
?>