<?
require_once("QRCode/qr_imgV2.php");
require_once("importeco.php");
function tempnam_face($path, $suffix){
	do{
		if(!file_exists($path))
			mkdir($path);
			
		$file = $path."/".mt_rand().$suffix;
		$fp = fopen($file, 'x');		
	}
	while(!$fp);

	fclose($fp);
	return $file;
}
 function leeXML($fileName){
	
    if($cdfi=@simplexml_load_file($fileName)){
			$cfdiDatosReceptor = $cdfi->xpath("//cfdi:Receptor");
			$receptorNombre 			= (string)$cfdiDatosReceptor[0]["nombre"];
			$receptorRFC 	    		= (string)$cfdiDatosReceptor[0]["rfc"];
			$cfdiDomicilioReceptor = $cdfi->xpath("//cfdi:Receptor/cfdi:Domicilio");
			$ReceptorCalle 				= (string)$cfdiDomicilioReceptor[0]["calle"];
			$ReceptorNoExterior 		= (string)$cfdiDomicilioReceptor[0]["noExterior"];
			$ReceptorColonia			= (string)$cfdiDomicilioReceptor[0]["colonia"];
			$ReceptorMunicipio 			= (string)$cfdiDomicilioReceptor[0]["municipio"];
			$ReceptorPais 				= (string)$cfdiDomicilioReceptor[0]["pais"];
			$ReceptorCP 				= (string)$cfdiDomicilioReceptor[0]["codigoPostal"];
			//////////////////////////////////////////////
			///////////////DATOS DEL EMISOR///////////////
			//////////////////////////////////////////////
			$cfdiDatosEmisor = $cdfi->xpath("//cfdi:Emisor");
			$emisorNombre 			= (string)$cfdiDatosEmisor[0]["nombre"];
			$emisorRFC 	    		= (string)$cfdiDatosEmisor[0]["rfc"];
			$cfdiDomicilioFiscalEmisor = $cdfi->xpath("//cfdi:Emisor/cfdi:DomicilioFiscal");
			$emisorCalle 			= (string)$cfdiDomicilioFiscalEmisor[0]["calle"];
			$emisorNoExt 			= (string)$cfdiDomicilioFiscalEmisor[0]["noExterior"];
			$emisorNoInt 			= (string)$cfdiDomicilioFiscalEmisor[0]["noInterior"];
			$emisorCol 				= (string)$cfdiDomicilioFiscalEmisor[0]["colonia"];
			$emisorLocalidad 		= (string)$cfdiDomicilioFiscalEmisor[0]["localidad"];
			$emisorMunicipio 		= (string)$cfdiDomicilioFiscalEmisor[0]["municipio"];
			$emisorEstado 			= (string)$cfdiDomicilioFiscalEmisor[0]["estado"];
			$emisorPais 			= (string)$cfdiDomicilioFiscalEmisor[0]["pais"];
			$emisorCodigoPostal 	= (string)$cfdiDomicilioFiscalEmisor[0]["codigoPostal"];
			//print_r($cfdiDomicilioFiscalEmisor);
			//////////////////////////////////////////////
			/////////////DATOS DEL COMPROBANTE////////////
			//////////////////////////////////////////////
			$cfdiComprobante = $cdfi->xpath("/cfdi:Comprobante");
			$comprobanteVersion 			= (string)$cfdiComprobante[0]["version"];
			$comprobanteSerie 				= (string)$cfdiComprobante[0]["serie"];
			$comprobanteFolio 				= (string)$cfdiComprobante[0]["folio"];
			$comprobanteFecha 				= (string)$cfdiComprobante[0]["fecha"];
			$comprobanteSello 				= (string)$cfdiComprobante[0]["sello"];
			$comprobanteFPago 				= (string)$cfdiComprobante[0]["formaDePago"];
			$comprobanteNoCer 				= (string)$cfdiComprobante[0]["noCertificado"];
			$comprobanteCertificado 		= (string)$cfdiComprobante[0]["certificado"];
			$comprobanteSubtotal 			= (float)(string)$cfdiComprobante[0]["subTotal"];
			$comprobanteDescuento 			= (float)(string)$cfdiComprobante[0]["descuento"];
			$comprobanteTipoCambio 			= (float)(string)$cfdiComprobante[0]["TipoCambio"];
			$comprobanteMoneda 				= (string)$cfdiComprobante[0]["Moneda"];
			$comprobanteTotal 				= (float)(string)$cfdiComprobante[0]["total"];
			$comprobanteTipoComp 			= (string)$cfdiComprobante[0]["tipoDeComprobante"];
			$comprobanteMetodoPago			= (string)$cfdiComprobante[0]["metodoDePago"];
			$comprobanteExpedicion 			= (string)$cfdiComprobante[0]["LugarExpedicion"];
			//print_r($cfdiComprobante);
			//////////////////////////////////////////////
			////////////////TIMBRE FISCAL/////////////////
			//////////////////////////////////////////////
			$namespaceTimbre = $cdfi->getNamespaces(true);
			$cdfi->registerXPathNamespace('prefix', $namespaceTimbre['tfd']);
			$tfdTimbreFiscalDigital = $cdfi->xpath("///prefix:TimbreFiscalDigital");
			$uuid = (string)$tfdTimbreFiscalDigital[0]["UUID"];
			$fechaTimbrado = (string)$tfdTimbreFiscalDigital[0]["FechaTimbrado"];
			$selloCFD = (string)$tfdTimbreFiscalDigital[0]["selloCFD"];
			$noCertificadoSat = (string)$tfdTimbreFiscalDigital[0]["noCertificadoSAT"];
			$selloSat = (string)$tfdTimbreFiscalDigital[0]["selloSAT"];
			//////////////////////////////////////////////
			////////////////////CONCEPTO//////////////////
			//////////////////////////////////////////////			
			$cfdiConcepto = $cdfi->xpath("///cfdi:Concepto");
			$conceptos = array();
			foreach($cfdiConcepto as $key => $val){		
				$concepto = array(
					"cantidad" 		=> (string)$val["cantidad"],
					"unidad" 		=> (string)$val["unidad"],
					"descripcion" 	=> (string)$val["descripcion"],
					"valorUnitario" => (string)$val["valorUnitario"],
					"importe"		=> (string)$val["importe"] 
				);
				array_push($conceptos, $concepto);
			}
			//////////////////////////////////////////////
			////////////////////RETENCION//////////////////
			//////////////////////////////////////////////
			$cfdiRetencion = $cdfi->xpath("////cfdi:Retencion");
			$retenciones = array();
			foreach($cfdiRetencion as $key => $val){
				$retencion = array(
					"impuesto" 		=> (string)$val["impuesto"],
					"importe" 		=> (string)$val["importe"]
				);
				array_push($retenciones, $retencion);
			}			
			//////////////////////////////////////////////
			////////////////////TRASLADO//////////////////
			//////////////////////////////////////////////
			$cfdiTraslado = $cdfi->xpath("////cfdi:Traslado");
			$traslados = array();
			foreach($cfdiTraslado as $key => $val){
				$traslado = array (
					"impuesto" 		=> (string)$val["impuesto"],
					"tasa" 		=> (string)$val["tasa"],
					"importe" 		=> (string)$val["importe"]
				);
				array_push($traslados, $traslado);
			}
			
			$cfdiImpuestos = $cdfi->xpath("///cfdi:Impuestos");
			foreach($cfdiImpuestos as $key => $val){
				$totalImpuestosRetenidos[] = (string)$val["totalImpuestosRetenidos"];
				$totalImpuestosTrasladados[]     = (string)$val["totalImpuestosTrasladados"];
			}
			
			
			$cadenaQR = "?re=".$emisorRFC."&rr=".$receptorRFC."&tt=".$comprobanteTotal."&id=".$uuid;
			$directory = dirname(__FILE__).DIRECTORY_SEPARATOR;	
			$imgFilename = qr_imgV2($cadenaQR, "M", 8, 8, "J", $directory); 	
			$imgFilename = str_replace($directory, "", $imgFilename);			

				$html = "";
				$html .= '<center>';
					$html .= '<div id="factura">';
						$html .= '<div id="body">';
							$html .= '<table width="800px">';
								$html .= '<tr>';
									$html .= '<td>';
										$html .= '<div id="header">';
											$html .= '<div>';
												$html .= '<img id="logo" src="logo_envasadoras.png" height="40%" width="40%" />';
											$html .= '</div>';
										$html .= '</div>';
									$html .= '</td>';
									$html .= '<td>';
										$html .= '<div>';
											$html .= '<p id="emisor" class="left">
														'.$emisorNombre.'<br/>
														'.$emisorRFC.'<br/>
														'.$emisorCalle." ".$emisorNoExt.'<br/>
														'.$emisorMunicipio.", ".$emisorEstado.'<br/>
														"CP"'.$emisorCodigoPostal." ".$emisorPais.'<br/>										
													<p>';
										$html .= '</div>';						
									$html .= '</td>';
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td>';
										$html .= '<table class="tableCliente rouded left" width="550px">';
											$html .= '<tr class="par"><td><b>Cliente</b></td></tr>';
											$html .= '<tr class="none"><td>'.$receptorNombre.'</td></tr>';
											$html .= '<tr class="par"><td>'.$receptorRFC.'</td></tr>';
											$html .= '<tr class="none"><td>'.$ReceptorCalle.'</td></tr>';
											$html .= '<tr class="par"><td>'.$ReceptorMunicipio.", ".$ReceptorMunicipio.'</td></tr>';
											$html .= '<tr class="none"><td>'.$ReceptorPais.'</td></tr>';
											$html .= '<tr class="par"><td>'.$ReceptorCP.'</td></tr>';
										$html .= '</table>';
									$html .= '</td>';
									$html .= '<td>';
										$html .= '<table class="tableEmisor rouded left" width="225px">';
											$html .= '<tr class="par"><td><b>Factura</b></td></tr>';
											$html .= '<tr class="none"><td>'.$comprobanteFolio.'</td></tr>';
											$html .= '<tr class="par"><td><b>Fecha de Emisión</b></td></tr>';
											$html .= '<tr class="none"><td>'.$comprobanteFecha.'</td></tr>';
											$html .= '<tr class="par"><td><b>Fecha de Certificación</b></td></tr>';
											$html .= '<tr class="none"><td>'.$fechaTimbrado.'</td></tr>';
											$html .= '<tr class="par"><td><b>No de Serie del Certificado Emisor</b></td></tr>';
											$html .= '<tr class="none"><td>'.$comprobanteNoCer.'</td></tr>';
											$html .= '<tr class="par"><td><b>No. de Serie del Certificado del SAT</b>	</td></tr>';
											$html .= '<tr class="none"><td>'.$noCertificadoSat.'</td></tr>';
										$html .= '</table>';
									$html .= '</td>';
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td colspan="2">';
										$html .= '<table class="tableFactura rouded left" width="790">';
											$html .= '<thead>';
												$html .= '<th width="80px">Cantidad</th>';
												$html .= '<th width="80px">Unidad</th>';
												$html .= '<th width="350px">Descripción</th>';
												$html .= '<th width="100px">Unitario</th>';
												$html .= '<th width="100px">Importe</th>';
											$html .= '</thead>';
											$html .= '<tbody>';
													$class = array("par", "none");
													$cont = 0;
													foreach($conceptos as $key => $val){
														$cont++;
														$aux = (is_int($cont/2)) ? 1 : 0;
														$html .= '<tr class="'.$class[$aux].'">';
															$html .= '<td>'.$val["cantidad"].'</td>';
															$html .= '<td>'.$val["unidad"].'</td>';
															$html .= '<td>'.$val["descripcion"].'</td>';
															$html .= '<td style="text-align: right;">'.number_format($val["valorUnitario"], "2", ".", ",").'</td>';
															$html .= '<td style="text-align: right;">'.number_format($val["importe"], "2", ".", ",").'</td>';
														$html .= '</tr>';
													}
												$html .= '<tr class="par">';
													$html .= '<td colspan="3">';
														$html .= '<p>
															<b>Total con letra:</b><br/>
															 '.covertirNumLetras($comprobanteTotal).'
														</p>';
													$html .= '</td>';
													$html .= '<td style="text-align: right;"><b>Subtotal</b></td>';
													$html .= '<td style="text-align: right;">'.number_format($comprobanteSubtotal,"2", ".", ",").'</td>';											
												$html .= '</tr>';
												foreach($traslados as $key => $val){
													$impuestoT = $val['impuesto'];
													$tasaT = $val['tasa'];
													$importeT = $val['importe'];
													if($impuestoT == 'IVA'){
														$html .= '<tr class="par">';
															$html .= '<td colspan="3"></td>';
															$html .= '<td style="text-align: right;"><b>IVA</b></td>';
															$html .= '<td style="text-align: right;">'.number_format($importeT,"2", ".", ",").'</td>';
														$html .= '</tr>';
													}
													if($impuestoT == 'IEPS'){
														$html .= '<tr class="par">';
															$html .= '<td colspan="3"></td>';
															$html .= '<td style="text-align: right;"><b>IEPS</b></td>';
															$html .= '<td style="text-align: right;">'.number_format($importeT,"2", ".", ",").'</td>';
														$html .= '</tr>';
													}													
												}
												foreach($retenciones as $key => $val){
													$impuestoR = $val['impuesto'];
													$importeR = $val['importe'];
													if($impuestoR == 'IVA'){
														$html .= '<tr class="par">';
															$html .= '<td colspan="3"></td>';
															$html .= '<td style="text-align: right;"><b>Retención IVA</b></td>';
															$html .= '<td style="text-align: right;">'.number_format($importeR,"2", ".", ",").'</td>';
														$html .= '</tr>';
													}
													if($impuestoR == 'ISR'){
														$html .= '<tr class="par">';
															$html .= '<td colspan="3"></td>';
															$html .= '<td style="text-align: right;"><b>Retención ISR</b></td>';
															$html .= '<td style="text-align: right;">'.number_format($importeR,"2", ".", ",").'</td>';
														$html .= '</tr>';
													}													
												}
												if(!empty($comprobanteDescuento)){
													$html .= '<tr class="par">';
														$html .= '<td colspan="3"></td>';
														$html .= '<td style="text-align: right;"><b>Descuento</b></td>';
														$html .= '<td style="text-align: right;">'.number_format($comprobanteDescuento,"2", ".", ",").'</td>';
													$html .= '</tr>';
												}
												$html .= '<tr class="par">';
													$html .= '<td colspan="3"></td>';
													$html .= '<td style="text-align: right;"><b>Total</b></td>';
													$html .= '<td style="text-align: right;">'.number_format($comprobanteTotal,"2", ".", ",").'</td>';
												$html .= '</tr>';
											$html .= '</tbody>';
										$html .= '</table>';
									$html .= '</td>';
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td style="border: 0px" class="left">';
										$html .= '<br />';
										$html .= '<div>';
											$html .= '<div style="border: 1px solid black; padding: 4px; width: 550px; word-wrap: break-word; ">';
												$html .= '<p>';
													$html .= '<b>Sello Digital del Emisor</b><br/>';
													$html .= $comprobanteSello;
												$html .= '</p>';								
												$html .= '<p>';
													$html .= '<b>Sello Digital del SAT</b><br/>';
													$html .= $selloSat;
												$html .= '</p>';
											$html .= '</div>';
											$html .= '<br/>';
											$html .= '<div style="border: 1px solid black;padding: 4px; width: 550px; word-wrap: break-word; ">';
												$html .= '<p>';
													$html .= '<b>Cadena original del complemento de certificacion digital del SAT</b><br/>';
													$html .= '||1.0|'.$uuid.'|'.$fechaTimbrado.'|'.$selloCFD.'|'.$noCertificadoSat.'||';
												$html .= '</p>';
												$html .= '<p>';
													$html .= '<b>Folio Fiscal</b><br />';
													$html .= $uuid;
												$html .= '</p>';
											$html .= '</div>';
										$html .= '</div>';							
									$html .= '</td>';
									$html .= '<td>';
										$html .= '<div>';
											$html .= '<center>';
												$html .= '<img src="'.$imgFilename.'" width="200px">';
											$html .= '</center>';
										$html .= '</div>';
									$html .= '</td>';
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td colspan="2"  style="border: 0;">';	
										$html .= '<br />';
										$html .= '<div id="footer">';
											$html .= '<p>
												EFECTOS FISCALES AL PAGO. ESTE DOCUMENTO ES UNA REPRESENTACION IMPRESA DE UN CFDI.<br />
												FORMA DE PAGO: '.$comprobanteFPago.' METODO DE PAGO: '.$comprobanteMetodoPago.'
											</p>';
										$html .= '</div>';
										$html .= '<br />';
									$html .= '</td>';
								$html .= '</tr>';
							$html .= '</table>';
						$html .= '</div>';							
					$html .= '</div>';		
				$html .= '</center>';
			echo utf8_decode($html);
			
    }else{
        echo '
            <tr>
                <td colspan="5" align="center">El archivo no existe.</td>
            </tr>
        ';
    }
}
?>
<html>	
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<head>
				<style>
					.left{
						text-align: left;
					}			
					.rouded{
						-webkit-border-radius: 6px;
						border-radius: 6px;
						-webkit-box-shadow: 0 1px 6px -2px rgba(0,0,0,0.2);
						box-shadow: 0 1px 6px -2px rgba(0,0,0,0.2);
					}
					body{
						font-family: arial, calibri;
						font-size: 12px;							
					}
					#footer{
						text-align: center;
						font-family: arial, calibri;
						font-size: 8px;			
					}
					#header{
						font-family: arial, calibri;
						font-size: 12px;			
					}
					#body table{
						font-family: arial, calibri;
						font-size: 11px;			
					}			
					.tableFactura, .tableEmisor, .tableCliente{
						border: 1px solid black;		
					}
					img{
						width: 200px;				
						padding: 0px;				
					}
					#logo{
						float:left;			
					}
					th{
						color: white;
						font-bold: weigth;
						background: black;
						color-backgroudn: black;
						-webkit-print-color-adjust:black;
					}
					.none{
						color: black;
						font-bold: weigth;
						background: white;
						color-backgroudn: white;
						-webkit-print-color-adjust:white;
					}
					.par{
						color: black;
						font-bold: weigth;
						background: #E6E6E6;
						color-backgroudn: #E6E6E6;
						-webkit-print-color-adjust:#E6E6E6;
					}
				</style>
			</head>
	<body>
<?php
//print_r($_REQUEST);
$archivo = $_GET["xml"];
$ruta = str_replace ('"' , '' , $archivo);
leeXML($ruta);
?>
	</body>
</html>	