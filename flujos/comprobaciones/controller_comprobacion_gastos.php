 <?PHP
	require_once("../../lib/php/constantes.php");
    require_once("../../Connections/fwk_db.php");
    require_once("../../functions/Usuario.php");
    require_once("../../functions/Tramite.php");
	require_once("../../functions/Delegados.php");
	require_once("../../functions/Presupuesto.php");
	require_once("../../functions/ComprobacionesGastos.php");
	require_once("../../functions/DetalleComprobacionGastos.php");
	require_once("services/func_comprobacion.php");
	require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
	require_once("$RUTA_A/flujos/solicitudes/services/utils.php");

	class ControllerComprobacionGastos{
		
		private $comprobacion;
		private $solicitud;
		private $usuario;
		private $factura;
		private $gasolina;
		private $partidas;
		private $invitados;
		private $totalPartidas;
		private $totalInvitados;
		private $excepciones;
		private $totalExcepciones;
		private $tramite;
		private $notificacion;
		private $mensajes;
		private $conexion;
		private $user;
		private $rutaAutorizacion;
		private $delegado;
		private $amex;
		private $comprobacionGastos;
		private $detalleComprobacionGastos;
		private $invitadoClass;
		
		public function __construct($args){	
			foreach($args as $key => $val)
				$args[$key] = ($val == "N/A" || $val == "NaN") ? "" : $val;								
		
			for($i = 1; $i <= $args["totalPartidas"]; $i++)
				$totalComprobado+= $args["row_totalPartida".$i];
				
			$this->comprobacion = array("modo" 						=> $args["modo"],
										"comprobacion" 				=> $args["comprobacion"],
										"tramite"	 				=> $args["tramiteEdit"],
										"centroCostos"				=> $args["centroCostos"],
										"comentario"				=> $args["comentario"],
										"historialObservaciones"	=> $args["historialObservaciones"],
										"observaciones"				=> $args["observaciones"],
										"anticipo"					=> $args["anticipo"],
										"anticipoComprobado"		=> $args["anticipoComprobado"],
										"personalComprobado"		=> $args["personalComprobado"],
										"amexComprobado"			=> $args["amexComprobado"],
										"efectivoComprobado"		=> $args["efectivoComprobado"],
										"amexExternoComprobado"		=> $args["amexExternoComprobado"],
										"montoDescontar" 			=> $args["montoDescontar"],
										"montoReembolsar" 			=> $args["montoReembolsar"],
										"totalComprobado"			=> $totalComprobado,
										"lugarComida"				=> $args["lugarComida"],
										"ciudadComida"				=> $args["ciudadComida"],
										"noInvitados"				=> $args["noInvitados"]
										);
										
			$this->solicitud = array("solicitud" 	=> $args["solicitud"],
									"motivo"	 	=> ($args["solicitud"] == "-1") ? $args["motivo"] : $args["motivoSolicitud"],
									"tipoViaje" 	=> $args["tipoViaje"],
									"diasViaje"		=> $args["diasViaje"],
									"region"		=> $args["region"]
									);
			
			$this->usuario = array("idusuario" 		=> $args["idusuario"],
									"perfil" 		=> $args["perfil"],
									"delegado"		=> $args["delegado"],
									"empresa"		=> $args["empresa"],
									);
			
			$this->gasolina = array("gasolina" 			=> ($args["solicitud"] == "-1") ? 1 : 0,
									"motivo" 			=> ($args["solicitud"] == "-1") ? $args["motivo"] : "",
									"fechaInicial" 		=> "00000000",
									"fechaFinal"		=> "00000000",
									"modeloAuto"		=> ($args["solicitud"] == "-1") ? $args["modeloAuto"] : 0,
									"kilometraje"		=> ($args["solicitud"] == "-1") ? $args["kilometraje"] : 0,
									"monto_gasolina"	=> ($args["solicitud"] == "-1") ? $args["monto_gasolina"] : 0,
									"ruta_detallada"	=> ($args["solicitud"] == "-1") ? $args["ruta_detallada"] : ""
									);									
									
			$this->totalPartidas = $args["totalPartidas"];
			
			for($i = 1; $i <= $this->totalPartidas; $i++)
				$this->partidas[$i] = $this->setPartida($args, $i);
				
			for($i = 1; $i <= $this->totalPartidas; $i++)
				$this->factura[$i] = $this->setFactura($args, $i);				
			
			$excepciones = explode(",",$args["totalExcepciones"]);			
			$this->totalExcepciones = count($excepciones);
			for($i = 1; $i <= count($excepciones); $i++)
				$this->excepciones[$i] = $this->setExcepcion($args, $i, $excepciones[$i-1]);
			
			$this->totalInvitados = $args["noInvitados"];
			for($i = 1; $i <= $this->totalInvitados; $i++)
				$this->invitados[$i] = $this->setInvitado($args, $i);

			$this->tramite = new Tramite();
			$this->notificacion = new Notificacion;
			$this->conexion = new conexion;
			$this->user = new Usuario;
			$this->rutaautorizacion = new RutaAutorizacion();
			$this->amex = new AMEX();
			
			$this->comprobacionGastos = new ComprobacionesGastos();
			$this->detalleComprobacionGastos = new DetalleComprobacionGastos();
			$this->invitadoClass = new Comensales();
		}
		
		public function getProveedor($index){			
			$sql = "SELECT pro_id FROM proveedores WHERE pro_rfc = '".$this->partidas[$index]["row_rfc".$index]."'";
			$res = $this->conexion->consultar($sql);		
			$row = mysql_fetch_assoc($res);			
			$this->partidas[$index]["row_proveedor".$index] = $row["pro_id"];			
		}
		
		public function setPartida($args, $index){
			return array("row_".$index 					=> $args["row_".$index],
						"row_tipoComprobacion".$index	=> $args["row_tipoComprobacion".$index],
						"row_noTransaccion".$index		=> $args["row_noTransaccion".$index],
						"row_cargoTarjeta".$index		=> ($args["row_cargoTarjeta".$index] == "") ? "0" : $args["row_cargoTarjeta".$index],
						"row_tipoTarjeta".$index		=> $args["row_tipoTarjeta".$index],						
						"row_concepto".$index			=> $args["row_concepto".$index],
						"row_tipoComida".$index			=> "0".$args["row_tipoComida".$index],
						"row_comentario".$index			=> $args["row_comentario".$index],
						"row_asistentes".$index			=> "0".$args["row_asistentes".$index],
						"row_fecha".$index				=> $args["row_fecha".$index],
						"row_rfc".$index				=> $args["row_rfc".$index],
						"row_proveedorNacional".$index	=> $args["row_proveedorNacional".$index],
						"row_proveedor".$index			=> "0".$args["row_proveedor".$index],
						"row_folio".$index				=> $args["row_folio".$index],
						"row_monto".$index				=> $args["row_monto".$index],
						"row_iva".$index				=> $args["row_iva".$index],
						"row_propina".$index			=> "0".$args["row_propina".$index],
						"row_impuestoHospedaje".$index	=> "0".$args["row_impuestoHospedaje".$index],
						"row_total".$index				=> $args["row_total".$index],
						"row_divisa".$index				=> $args["row_divisa".$index],						
						"row_totalPartida".$index		=> $args["row_totalPartida".$index]
						);		
		}
		
		public function setFactura($args, $index){
			return array("uuid".$index 						=> $args["uuid".$index],
						 "fechaTimbrado".$index 			=> $args["fechaTimbrado".$index],
						 "selloCFD".$index 					=> $args["selloCFD".$index],
						 "noCertificadoSat".$index 			=> $args["noCertificadoSat".$index],
						 "selloSat".$index 					=> $args["selloSat".$index],
						 "emisorNombre".$index 				=> $args["emisorNombre".$index],
						 "emisorRFC".$index 				=> $args["emisorRFC".$index],
						 "emisorDomicilio".$index 			=> $args["emisorDomicilio".$index],
						 "emisorEstado".$index 				=> $args["emisorEstado".$index],
						 "emisorPais".$index 				=> $args["emisorPais".$index],
						 "comprobanteVersion".$index 		=> $args["comprobanteVersion".$index],
						 "comprobanteSerie".$index 			=> $args["comprobanteSerie".$index],
						 "comprobanteFolio".$index 			=> $args["comprobanteFolio".$index],
						 "comprobanteFecha".$index 			=> $args["comprobanteFecha".$index],
						 "comprobanteSello".$index 			=> $args["comprobanteSello".$index],
						 "comprobanteFPago".$index 			=> $args["comprobanteFPago".$index],
						 "comprobanteNoCer".$index 			=> $args["comprobanteNoCer".$index],
						 "comprobanteCertificado".$index 	=> $args["comprobanteCertificado".$index],
						 "comprobanteSubtotal".$index 		=> $args["comprobanteSubtotal".$index],
						 "comprobanteTipoCambio".$index 	=> $args["comprobanteTipoCambio".$index],
						 "comprobanteMoneda".$index 		=> $args["comprobanteMoneda".$index],
						 "comprobanteTotal".$index 			=> $args["comprobanteTotal".$index],
						 "comprobanteTipoComp".$index 		=> $args["comprobanteTipoComp".$index],
						 "comprobanteMetodoPago".$index 	=> $args["comprobanteMetodoPago".$index],
						 "comprobanteExpedicion".$index 	=> $args["comprobanteExpedicion".$index],
						);
		}		
		
		public function setExcepcion($args, $index, $index2){			
			return array("e_row".$index 			=> $args["e_row".$index2],
						"e_row_concepto".$index		=> $args["e_row_concepto".$index2],
						"e_row_mensaje".$index		=> $args["e_row_mensaje".$index2],
						"e_row_fecha".$index		=> $args["e_row_fecha".$index2],
						"e_row_referencia".$index	=> $args["e_row_referencia".$index2],
						"e_row_diferencia".$index	=> $args["e_row_diferencia".$index2]
						);		
		}
		
		public function setInvitado($args, $index){
			return array("row_invitado".$index 		=> $args["row_invitado".$index],
					"row_nombreInvitado".$index 	=> $args["row_nombreInvitado".$index],
					"row_puestoInvitado".$index 	=> $args["row_puestoInvitado".$index],
					"row_empresaInvitado".$index 	=> $args["row_empresaInvitado".$index],
					"row_tipoInvitado".$index 		=> $args["row_tipoInvitado".$index]
			);
		}
		
		public function resetDetallesComprobacion(){
			$this->detalleComprobacionGastos->limpiar_detalles($this->comprobacion["tramite"]);
		}
		
		public function resetExcepciones(){
			$sql = "DELETE FROM excepciones 
					WHERE ex_comprobacion = ".$this->comprobacion["comprobacion"];
			$this->conexion->ejecutar($sql);		
		}
		
		public function resetInvitados(){
			$this->invitadoClass->Eliminar_Comensal_de_Comprobacion($this->comprobacion["comprobacion"]);
		}
		
		public function get($attr){
			return $this->$attr;
		}
		
		public function creaTramite(){
			$tramite = $this->tramite->Crea_Tramite($this->usuario["idusuario"], 
													$this->usuario["empresa"], 
													COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR, 
													FLUJO_COMPROBACION_GASTOS,
													$this->solicitud["motivo"], 
													$this->usuario["delegado"]
													);
			$this->comprobacion["tramite"] = $tramite;
		}
		
		public function creaComprobacion(){			
			$comprobacion = $this->comprobacionGastos->Crea_Gasto(
									$this->comprobacion["tramite"], 
									$this->solicitud["solicitud"], 
									$this->comprobacion["centroCostos"], 
									"", 
									$this->comprobacion["observaciones"], 
									"0".$this->comprobacion["totalComprobado"], 
									"0".$this->comprobacion["anticipoComprobado"], 
									"0".$this->comprobacion["personalComprobado"], 
									"0".$this->comprobacion["amexComprobado"], 
									"0".$this->comprobacion["efectivoComprobado"], 
									"0".$this->comprobacion["montoDescontar"], 
									"0".$this->comprobacion["montoReembolsar"], 
									$this->gasolina["gasolina"], 
									$this->gasolina["fechaInicial"], 
									$this->gasolina["fechaFinal"], 
									$this->gasolina["motivo"], 
									$this->gasolina["tipoAuto"], 
									$this->gasolina["modeloAuto"], 
									"0".$this->gasolina["kilometraje"], 
									"0".$this->gasolina["monto_gasolina"], 
									$this->gasolina["ruta_detallada"], 
									"0".$this->comprobacion["amexExternoComprobado"], 
									$this->comprobacion["lugarComida"], 
									$this->comprobacion["ciudadComida"],
									"0".$this->comprobacion["anticipo"]
				);	
			$this->comprobacion["comprobacion"] = $comprobacion;
		}
		
		public function actualizaTramiteEtiqueta($tramite){
			$this->tramite->actualizarInfoTramite($tramite, $this->solicitud["motivo"], $this->usuario["delegado"]);
		}
		
		public function actualizaComprobacion (){
			$comprobacion = $this->comprobacionGastos->Edita_Comprobacion_Gasto($this->comprobacion["tramite"], 
									$this->solicitud["solicitud"], 
									$this->comprobacion["centroCostos"], 
									"", 
									$this->comprobacion["observaciones"], 
									"0".$this->comprobacion["totalComprobado"], 
									"0".$this->comprobacion["anticipoComprobado"], 
									"0".$this->comprobacion["personalComprobado"], 
									"0".$this->comprobacion["amexComprobado"], 
									"0".$this->comprobacion["efectivoComprobado"], 
									"0".$this->comprobacion["montoDescontar"], 
									"0".$this->comprobacion["montoReembolsar"], 
									$this->gasolina["gasolina"], 
									$this->gasolina["fechaInicial"], 
									$this->gasolina["fechaFinal"], 
									$this->gasolina["motivo"], 
									$this->gasolina["tipoAuto"], 
									$this->gasolina["modeloAuto"], 
									"0".$this->gasolina["kilometraje"], 
									"0".$this->gasolina["monto_gasolina"], 
									$this->gasolina["ruta_detallada"], 
									"0".$this->comprobacion["amexExternoComprobado"], 
									$this->comprobacion["lugarComida"], 
									$this->comprobacion["ciudadComida"],
									"0".$this->comprobacion["anticipo"]
							);
			return $this->comprobacion["comprobacion"] = $comprobacion;
		}
		
		public function	enviaNotificacion($destinatario, $mensaje){			
			return $this->tramite->EnviaNotificacion($this->comprobacion["tramite"],
													$mensaje,
													$this->usuario["idusuario"], 
													$destinatario, 
													1);
		}
		
		public function obtenMensaje($etapa){
			return $this->tramite->crearMensaje($this->comprobacion["tramite"], $etapa, true, false, $this->usuario["delegado"]);
		}
		
		public function	obtenOservaciones(){
			$observacion  = $this->notificacion->anotaObservacion($this->usuario["idusuario"], 
															$this->comprobacion["historialObservaciones"],
															$this->comprobacion["observaciones"],
															FLUJO_COMPROBACION_GASTOS, 
															COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR
														);
			$observaciones = ($this->comprobacion["observaciones"] != "" ) ? $observacion : $this->comprobacion["historialObservaciones"];
			$this->comprobacionGastos->actualizaObservaciones($observaciones, "", $this->comprobacion["tramite"]);
		}
		
		public function modificaEtapa($etapa, $dueno, $ruta = ""){
			$this->tramite->Modifica_Etapa($this->comprobacion["tramite"], 
											$etapa, 
											FLUJO_COMPROBACION_GASTOS, 
											$dueno, 
											$ruta,
											$this->usuario["delegado"]);
		}
		
		public function resetAmex(){
			$this->amex->restablecerCargos($this->comprobacion["comprobacion"]);
			$this->amex->modificaEstatusUtilizado($this->usuario["idusuario"]);
		}
		
		public function agregaPartida($index){
			return $this->detalleComprobacionGastos->agregaDetalle($this->comprobacion["comprobacion"], 
									$this->partidas[$index]["row_tipoComprobacion".$index], 
									$this->partidas[$index]["row_cargoTarjeta".$index], // id del cargo AMEX
									$this->partidas[$index]["row_noTransaccion".$index], 
									$this->partidas[$index]["row_concepto".$index], 
									$this->partidas[$index]["row_tipoComida".$index], 
									$this->partidas[$index]["row_monto".$index], 
									$this->partidas[$index]["row_divisa".$index], 
									$this->partidas[$index]["row_iva".$index], 
									$this->partidas[$index]["row_total".$index], 
									$this->partidas[$index]["row_totalPartida".$index], 
									$this->partidas[$index]["row_comentario".$index], 
									$this->partidas[$index]["row_asistentes".$index], 
									$this->partidas[$index]["row_fecha".$index], 									
									$this->partidas[$index]["row_folio".$index],
									$this->partidas[$index]["row_proveedor".$index], 
									$this->partidas[$index]["row_rfc".$index], 
									$this->partidas[$index]["row_propina".$index], 
									$this->partidas[$index]["row_impuestoHospedaje".$index], 
									$this->partidas[$index]["row_totalPartida".$index]
				);
		}
		
		public function agregaFactura($index){			
			return Add_factura($this->factura[$index]["uuid".$index],
								$this->factura[$index]["fechaTimbrado".$index],
								$this->factura[$index]["selloCFD".$index],
								$this->factura[$index]["noCertificadoSat".$index],
								$this->factura[$index]["selloSat".$index],
								$this->factura[$index]["emisorNombre".$index],
								$this->factura[$index]["emisorRFC".$index],
								$this->factura[$index]["emisorDomicilio".$index],
								$this->factura[$index]["emisorEstado".$index],
								$this->factura[$index]["emisorPais".$index],
								$this->factura[$index]["comprobanteVersion".$index],
								$this->factura[$index]["comprobanteSerie".$index],
								$this->factura[$index]["comprobanteFolio".$index],
								$this->factura[$index]["comprobanteFecha".$index],
								$this->factura[$index]["comprobanteSello".$index],
								$this->factura[$index]["comprobanteFPago".$index],
								$this->factura[$index]["comprobanteNoCer".$index],
								$this->factura[$index]["comprobanteCertificado".$index],
								$this->factura[$index]["comprobanteSubtotal".$index],
								$this->factura[$index]["comprobanteTipoCambio".$index],
								$this->factura[$index]["comprobanteMoneda".$index],
								$this->factura[$index]["comprobanteTotal".$index],
								$this->factura[$index]["comprobanteTipoComp".$index],
								$this->factura[$index]["comprobanteMetodoPago".$index],
								$this->factura[$index]["comprobanteExpedicion".$index]
								);				
		}
		
		public function agregaExcepcion($index){
			$detComp = explode(",", $this->excepciones[$index]["e_row_referencia".$index]);
			$detallesComprobacion = array_pop($detComp);
			
			for($i = 0; $i < count($detallesComprobacion); $i++){
				if($detallesComprobacion[$i]  == "") $detallesComprobacion[$i] = "0";
				Add_excepcion($this->excepciones[$index]["e_row_mensaje".$index],
							$this->excepciones[$index]["e_row_diferencia".$index],
							0,
							$this->comprobacion["comprobacion"],
							$detallesComprobacion[$i],
							$this->excepciones[$index]["e_row_concepto".$index], 
							0,
							0
				);					
			}
		}
		
		public function asginaPartidaDetalle($index, $indexPartida){
			for($i = 1; $i <= $this->totalExcepciones; $i++){
				$referencia = explode(",", $this->excepciones[$i]["e_row_referencia".$i]);
				
				$arr = "";
				for($j = 0; $j < count($referencia); $j++){
					if($referencia[$j] == $index){
						$referencia[$j] = $indexPartida;
					}
					$arr.= $referencia[$j].",";
				}
				$arr = substr($arr, 0, -1);
				$this->excepciones[$i]["e_row_referencia".$i] = $arr;
			}
		}
		
		public function asginaComprobacionAmex($index){
			if($this->partidas[$index]["row_cargoTarjeta".$index] != 0){
				$sql = "UPDATE amex 
						SET comprobacion_id = ".$this->comprobacion["comprobacion"]."
						WHERE idamex = ".$this->partidas[$index]["row_cargoTarjeta".$index] ;
				$this->conexion->ejecutar($sql);				
			}
		}
		
		public function asginaEstatusAmex($index){
			if($this->partidas[$index]["row_cargoTarjeta".$index] != 0){
				$sql = "UPDATE amex 
						SET comprobacion_id = ".$this->comprobacion["comprobacion"].",
						estatus = 3
						WHERE idamex = ".$this->partidas[$index]["row_cargoTarjeta".$index] ;
				$this->conexion->ejecutar($sql);
			}
		}
		
		function updateFactura($idFactura,$urlMD5,$url) {
			$query = sprintf("UPDATE factura
							SET urlMD5 = '%s',
							  url = '%s'
							WHERE id_factura = '%s'",
							$urlMD5,                    
							$url,
							$idFactura);		
			$this->conexion->ejecutar($query);
		}
		
		function comprobacionFactura($idFactura,$comprobacion) {
			$query = sprintf("UPDATE detalle_comprobacion_gastos
							SET id_factura = '%s'
							WHERE dc_id = '%s'",
							$idFactura,
							$comprobacion);
			$this->conexion->ejecutar($query);
		}				
		
		public function agregaInvitado($index){
			$this->invitadoClass->Crea_Comensal("0", 
								$this->invitados[$index]["row_nombreInvitado".$index], 
								$this->invitados[$index]["row_puestoInvitado".$index], 
								$this->invitados[$index]["row_empresaInvitado".$index], 
								$this->invitados[$index]["row_tipoInvitado".$index], 
								$this->comprobacion["comprobacion"]
					);
		}
		
		public function guardarPrevio(){
			$dueno = $this->usuario["idusuario"];
			$this->modificaEtapa(COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR, $dueno);
		}
		
		public function enviarComprobacion(){
			$rutaautorizacion = new RutaAutorizacion();
			
			$rutaautorizacion->generarRutaAutorizacion($this->comprobacion["tramite"], $this->usuario["delegado"]);
			$excepciones = $rutaautorizacion->get_Excepciones($this->comprobacion["tramite"]);
			$rutaautorizacion->agregaAutorizadoresExcedentes($this->comprobacion["tramite"], $excepciones);
			$aprobador = $rutaautorizacion->getSiguienteAprobador($this->comprobacion["tramite"], $this->usuario["idusuario"]);
			$etapa = COMPROBACION_GASTOS_ETAPA_APROBACION;
			
			$this->tramite->Load_Tramite($this->comprobacion["tramite"]);
			$etapaActual = $this->tramite->Get_dato("t_etapa_actual");
			
			if($etapaActual == COMPROBACION_GASTOS_ETAPA_DEVUELTO_CON_OBSERVACIONES)
				$aprobador = 2000;		
			
			$this->modificaEtapa($etapa, $aprobador);			
			$this->obtenOservaciones();		
			$this->enviaNotificacion($aprobador, $this->obtenMensaje($etapa));
		}
	}
	
	if(!empty($_REQUEST)){
		$controllerComprobacion = new ControllerComprobacionGastos($_REQUEST);
		$comprobacion = $controllerComprobacion->get("comprobacion");
		$tramite	= $comprobacion["tramite"];
		$modo		= $comprobacion["modo"];
		$partidas 	= $controllerComprobacion->get("totalPartidas");
		$excepciones= $controllerComprobacion->get("totalExcepciones");
		$invitados  = $controllerComprobacion->get("totalInvitados");
		
		if($tramite == 0 || $tramite == ""){
			$tramite = $controllerComprobacion->creaTramite();
			$controllerComprobacion->creaComprobacion();
		}else{
			$controllerComprobacion->actualizaTramiteEtiqueta($tramite);
			$controllerComprobacion->actualizaComprobacion();
		}
		$controllerComprobacion->resetAmex();
		$controllerComprobacion->resetDetallesComprobacion();
		$controllerComprobacion->resetExcepciones();
		$controllerComprobacion->resetInvitados();
		
		for($i = 1; $i <= $partidas; $i++){
			$controllerComprobacion->getProveedor($i);
			$idPartida = $controllerComprobacion->agregaPartida($i);
			if($idPartida > 0){
				if($_REQUEST["fid".$i] > 0 ){
					$controllerComprobacion->comprobacionFactura($_REQUEST["fid".$i],$idPartida);
				}			
				if($_REQUEST["contenidoXML".$i] != "undefined"){
					$anio = date("Y");
					$mes = date("M");
					$ruta1 = $anio;
					$ruta2 = $ruta1."/".$mes;
						if(!file_exists($ruta1))
						{
						mkdir ($ruta1);
						chmod($ruta1, 0777);
						}
						if(!file_exists($ruta2))
						{
						mkdir ($ruta2);
						chmod($ruta2, 0777);
						}
					$content = base64_decode($_REQUEST["contenidoXML".$i]);
					$archivoMD5= md5("Archivo".$idPartida);
					$archivo = "Archivo".$idPartida.".xml";
					$rutaArchivo = $ruta2."/".$archivoMD5.".xml";
					file_put_contents($rutaArchivo,$content);
					$idfactura = $factura = $controllerComprobacion->agregaFactura($i,$idPartida);
					$controllerComprobacion->updateFactura($idfactura,$ruta2."/".$archivoMD5,$archivo);
				}
			}
			$controllerComprobacion->asginaPartidaDetalle($i, $idPartida);
			$controllerComprobacion->asginaComprobacionAmex($i);
			if($modo != "previo")
				$controllerComprobacion->asginaEstatusAmex($i);
			if($idfactura){	
				if($_REQUEST['row_proveedorNacional'.$i] != "N/A"){
					$controllerComprobacion->comprobacionFactura($idfactura,$idPartida);		
				}		
			}
		}
		
		for($i = 1; $i <= $excepciones; $i++){
			$controllerComprobacion->agregaExcepcion($i);
		}
		
		for($i = 1; $i <= $invitados; $i++){
			$controllerComprobacion->agregaInvitado($i);
		}
		if($modo == "previo"){
			$controllerComprobacion->guardarPrevio();
			exit(header("Location: ./index.php?oksaveprev&docs=docs&type=4"));
		}else{
			$controllerComprobacion->enviarComprobacion();
			exit(header("Location: ./index.php?oksave&docs=docs&type=4"));
		}
	}
?>