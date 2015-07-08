<html>
<head></head>
<body>
<?php
require_once("../../lib/php/constantes.php");
require_once("$RUTA_A/Connections/fwk_db.php");	
    //cargamos el archivo por medio de la funcion simplexml_load_file
$output_dir = "";
if(isset($_FILES["myfile"])){
	$ret = array();
	
	$error =$_FILES["myfile"]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData()

	$valido = true;
	$file 	= $_FILES["myfile"];
	validaArchivo($file);
	
	$contenidoXml = file_get_contents($file["tmp_name"]);
	$arraycontenidoXML = array('contenidoXml' => base64_encode($contenidoXml));
	$requestValidadorService = RequestValidadorService::getInstance();
	$response = base64_decode($requestValidadorService->request(base64_encode($contenidoXml)));
	$xmlAux = $response;
	$response = json_decode($response);
	if(is_object($response)){
		if($response->response != "true"){
			$mensaje = "Errores encontrados :\n";
			foreach($response->errores as $key => $val){
				$mensaje.=  "\t\t\t\t *  ".$val->mensaje."\n";
				echo mensaje ($mensaje);
				exit();
			}
		}else{
			$conexion = new conexion();
			$uuid = $response->receptor->uuid;
			$sql = "SELECT id_factura
					FROM factura
					WHERE uuid = '".$uuid."'";
			$conexion->consultar($sql);
			$conexion->get_rows();
			if($conexion->get_rows() > 0){
				echo mensaje("Esta factura ya ha sido registrada anteriormente.");
				exit();
			}
			$fechaLimite = strtotime(date("Y-m-d",strtotime('+345 day', strtotime($response->receptor->fecha))));
			$fechaActual = strtotime(date("Y-m-d"));
			if($fechaActual > $fechaLimite){
				echo mensaje("Esta factura ha excedido el límite permitido de 45 dias para ser comprobada. ");
				exit();
			}	
			if(!is_array($_FILES["myfile"]["name"])) //single file
			{
				$fileName = $_FILES["myfile"]["name"];
				move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
				$ret[]= $fileName;
				leeXML($uuid,$arraycontenidoXML);
			}
		}
	}
	else  //Multiple files, file[]
	{
	  $fileCount = count($_FILES["myfile"]["name"]);
	  for($i=0; $i < $fileCount; $i++)
	  {
	  	$fileName = $_FILES["myfile"]["name"][$i];
		move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);
	  	$ret[]= $fileName;
	  }
	
	}
     json_encode($ret);
 }
 function mensaje($msj,$contenidoXml){
	$msj = array ("msj" => "$msj");
	return json_encode($msj);
 }
 function leeXML($uuid,$arraycontenidoXML){
 $conexion = new conexion();
 $fileName = $_FILES["myfile"]["name"];
	
    if($cdfi=@simplexml_load_file($fileName)){
	unlink($fileName);
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
			$comprobanteTipoCambio 			= (float)(string)$cfdiComprobante[0]["TipoCambio"];
			$comprobanteMoneda 				= (string)$cfdiComprobante[0]["Moneda"];
			$comprobanteTotal 				= (float)(string)$cfdiComprobante[0]["total"];
			$comprobanteTipoComp 			= (string)$cfdiComprobante[0]["tipoDeComprobante"];
			$comprobanteMetodoPago			= (string)$cfdiComprobante[0]["metodoDePago"];
			$comprobanteExpedicion 			= (string)$cfdiComprobante[0]["LugarExpedicion"];
			$comprobanteDescuento 			= (string)$cfdiComprobante[0]["descuento"];
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
			////////////////////TRASLADO//////////////////
			//////////////////////////////////////////////
			$cfdiTraslado = $cdfi->xpath("////cfdi:Traslado");
			//print_r($cfdiTraslado);
			foreach($cfdiTraslado as $key => $val){
				$trasladoImpuesto[] = (string)$val["impuesto"];
				$trasladoTasa[]     = (string)$val["tasa"];
				$trasladoImporte[]  = number_format((string)$val["importe"],2);
			}
			//////////////////////////////////////////////
			////////////////////RETENCIONES//////////////////
			//////////////////////////////////////////////
			$cfdiRetencion = $cdfi->xpath("////cfdi:Retencion");
			//print_r($cfdiTraslado);
			foreach($cfdiRetencion as $key => $val1){
				$retencionImpuesto[] = (string)$val1["impuesto"];
				$retencionImporte[]  = number_format((string)$val1["importe"],2);
			}			
			$arrayEmisor['Emisor'] = array('emisorNombre' => $emisorNombre,
										   'emisorRFC' => $emisorRFC,
										   'emisorCalle' => $emisorCalle,
										   'emisorNoExt' => $emisorNoExt,
										   'emisorNoInt' => $emisorNoInt,
										   'emisorCol' => $emisorCol,
										   'emisorLocalidad' => $emisorLocalidad,
										   'emisorMunicipio' => $emisorMunicipio,
										   'emisorEstado' => $emisorEstado,
										   'emisorPais' => $emisorPais,
										   'emisorCodigoPostal' => $emisorCodigoPostal);
			$arrayComprobante['Comprobante'] = array('comprobanteVersion' => $comprobanteVersion,
													 'comprobanteSerie' => $comprobanteSerie,
													 'comprobanteFolio' => $comprobanteFolio,
													 'comprobanteFecha' => $comprobanteFecha,
													 'comprobanteSello' => $comprobanteSello,
													 'comprobanteFPago' => $comprobanteFPago,
													 'comprobanteNoCer' => $comprobanteNoCer,
													 'comprobanteCertificado' => $comprobanteCertificado,
													 'comprobanteSubtotal' => number_format((string)$comprobanteSubtotal,2),
													 'comprobanteTipoCambio' => $comprobanteTipoCambio,
													 'comprobanteMoneda' => $comprobanteMoneda,
													 'comprobanteTotal' => number_format((string)$comprobanteTotal,2),
													 'comprobanteTipoComp' => $comprobanteTipoComp,
													 'comprobanteMetodoPago' => $comprobanteMetodoPago,
													 'comprobanteExpedicion' => $comprobanteExpedicion,
													 'comprobanteDescuento' => number_format((string)$comprobanteDescuento,2));
			$arrayimpuestos['ImpuestosTraslado'] = array ('impuesto' => $trasladoImpuesto,
												  'tasa' => $trasladoTasa,
												  'importe' => $trasladoImporte);
			$arrayretenciones['ImpuestosRetencion'] = array ('impuesto' => $retencionImpuesto,
												  'importe' => $retencionImporte);
			$arrayTimbre['Timbre'] = array ('uuid' => $uuid,
											'fechaTimbrado' => $fechaTimbrado,
											'selloCFD' => $selloCFD,
											'noCertificadoSat' => $noCertificadoSat,
											'selloSat' => $selloSat);
			//////////////////////////////////////////////
			///////////VALIDA PROVEEDOR///////////////////
			//////////////////////////////////////////////
			$sql = "SELECT pro_rfc
					FROM proveedores
					WHERE pro_rfc = '".$emisorRFC."'";
			$conexion->consultar($sql);
			$conexion->get_rows();
			if($conexion->get_rows() == 0){
				//print_r($result);
				$sql = "INSERT INTO proveedores (pro_id, pro_proveedor, pro_rfc, pro_dir_fiscal, pro_activo)
										VALUES (default, '".utf8_decode($emisorNombre)."', '".utf8_decode($emisorRFC)."','".utf8_decode($emisorCalle)." ".utf8_decode($emisorNoExt)." ".utf8_decode($emisorNoInt)." ".utf8_decode($emisorCol)." ".utf8_decode($emisorLocalidad)." ".utf8_decode($emisorMunicipio)." ".utf8_decode($emisorEstado)." ".utf8_decode($emisorPais)." ".utf8_decode($emisorCodigoPostal)."','1')";
				$insertar = $conexion->insertar($sql);
			}
			$msj = array('msj' => 'Exito');
			$result = array_merge($msj, $arraycontenidoXML, $arrayEmisor, $arrayComprobante,$arrayimpuestos,$arrayretenciones,$arrayTimbre);
			echo json_encode($result);
    }else{
        echo '
            <tr>
                <td colspan="5" align="center">Error al leer el archivo.</td>
            </tr>
        ';
    }
}
	function validaArchivo($file){
		$mensaje = "";
		$type 	 = "text/xml";		
		if(!empty($file["error"])){
			switch($file['error']){
				case '1':
					$mensaje = "Archivo excede del maximo permitido: ".ini_get("upload_max_filesize"); break;
				case '2':
					$mensaje = "Archivo excede del maximo permitido por el formulario: ".ini_get("MAX_FILE_SIZE"); break;
				case '3':
					$mensaje = "El archivo fue subido parcialmente"; break;
				case '4':
					$mensaje = "El archivo no fue subido"; break;
				case '6':
					$mensaje = "Problemas al generar el archivo temporal"; break;
				case '7':
					$mensaje = "Problemas al escribir el archivo"; break;
				case '8':
					$mensaje = "Se detuvo la subida por la extension del archivo"; break;
				case '999':
				default:
					$mensaje = "Ha ocurrido un error desconocido al subir el archivo"; break;
			}
			echo mensaje($mensaje);
			exit();
		}elseif(empty($file['tmp_name']) || $file['tmp_name'] == 'none'){
			$mensaje = "El archivo no fue subido";
			echo mensaje($mensaje);
			exit();
		}
		if($type != $file['type']){
			$mensaje = "El archivo no fue el esperado, favor de subir un archivo válido (.XML)";
			echo mensaje($mensaje);
			exit();
		}
	}
	class RequestValidadorService{

		private $requestValidador;
		private static $_instance;
		
		public static function getInstance(){
			if(!(self::$_instance instanceof self))
				self::$_instance = new self();
			return self::$_instance;
		}

		private function __clone(){ }

		private function __construct(){
			$this->requestValidador = "http://200.53.151.71:8080/ValidadorCFDI/?wsdl";		
			ini_set("oap.wsdl_cache_ttl", "1");			
		}

		public function request($xml){
			try{
				$client = new \SoapClient($this->requestValidador, array('trace' => 1, 'connection_timeout' => 15) );
				$params = array("cfdi" => $xml);
				$response = $client->__soapCall('Validacion', array('cfdi' => $params));
				return $response->detalle;				
			}catch(\Exception $e){
				return $e->getMessage();
			}
		}
	}
?>
</body>
</html>