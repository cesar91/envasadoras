<?PHP 
	require_once("../../lib/php/constantes.php");
	require_once("$RUTA_A/flujos/comprobaciones/services/func_comprobacion.php");
	require_once("../../Connections/fwk_db.php");
	require_once("$RUTA_A/functions/utils.php");
	require_once("$RUTA_A/functions/Comprobacion.php");
	require_once("$RUTA_A/lib/php/mobile_device_detect.php");

	$tipoUsuario = $_SESSION["perfil"];
	
	
	if(isset($_REQUEST["modo"])){		

		$cnn = new conexion();
		
		$modo = $_REQUEST["modo"];
		$idTramite = $_POST["idT"];
		$delegado = $_POST['delegado'];
		$idusuario = $_POST['iu'];
		$perfil = $_POST['perfil'];
		$privilegios = $_POST['privilegios'];
		$etapa = $_POST['t_etapa_actual'];
		$rows = $_POST['total_rows'];
		$observaciones = $_POST['campo_observaciones'];
		$historialObservaciones = $_POST["campo_historial"];
		$cecoNuevo = $_POST['centro_de_costos_new'];		
		$cecoOriginal = $_POST['centro_de_costos_old'];		
		$divisaEuro = $_POST["valorDivisaEUR"];
		$divisaDolar = $_POST["valorDivisaUSD"];				
		
		if($observaciones != ""){
			$notificacion = new Notificacion();
			$observaciones = $notificacion->anotaObservacion($idusuario, $historialObservaciones, $observaciones, FLUJO_COMPROBACION, COMPROBACION_ETAPA_EN_APROBACION);			
			$sql = "UPDATE comprobaciones 
					SET co_observaciones = '$observaciones' 
					WHERE co_mi_tramite = '$idTramite'";
			$cnn->ejecutar($sql);			
		}
		
		$tramite = new Tramite();
		$tramite->Load_Tramite($idTramite);
		$t_ruta_autorizacion = $tramite->Get_dato("t_ruta_autorizacion");
		$t_delegado = $tramite->Get_dato("t_delegado");
		$t_dueno = $tramite->Get_dato("t_dueno");
		$t_iniciador = $tramite->Get_dato("t_iniciador");	
		
		$rutaAutorizacion  = new RutaAutorizacion();
		
		//print_r($_REQUEST);			
		if($modo == "autorizar"){	
			$finRuta = false;
			//$siguienteAprobador = $rutaAutorizacion->AutorizarFinanzas($idTramite, $cecoNuevo , 1);
			$siguienteAprobador = $rutaAutorizacion->getSiguienteAprobador($idTramite,$t_dueno);
			if($siguienteAprobador == ""){
				$finRuta = true;
				$siguienteAprobador = $t_iniciador;
			}
			$duenoActual01 = new Usuario();
			if($duenoActual01->Load_Usuario_By_ID($t_dueno))
				$dueno_act_nombre = $duenoActual01->Get_dato('nombre');
			else{
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
				$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
			}
			$remitente = $t_dueno;
			$destinatario = $tramite->Get_dato("t_iniciador");
			//$mensaje = sprintf("La Comprobaci&oacute;n de Viaje <strong>%05s</strong> ha sido <strong>MODIFICADA</strong> por <strong>%05s</strong>", $idTramite, $dueno_act_nombre);
			//$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, 1, ""); 
			
			if(!$finRuta){
				$mensaje = $tramite->crearMensaje($idTramite, COMPROBACION_ETAPA_EN_APROBACION, false, true, $t_delegado);				
				$tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_EN_APROBACION, FLUJO_COMPROBACION, $siguienteAprobador, "");
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, 1, "");
				$mensaje2 = sprintf("La Comprobaci&oacute;n de Viaje <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%05s</strong> y requiere de su autorizaci&oacute;n", $idTramite, $dueno_act_nombre);
				$tramite->EnviaNotificacion($idTramite, $mensaje2, $remitente, $siguienteAprobador, 1, ""); 				
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Viaje <strong>%05s</strong> ha sido <strong>APROBADA</strong> por completo.",$idTramite);
				$tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_APROBADA, FLUJO_COMPROBACION, $siguienteAprobador, "");
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, 1, ""); 
				$tramite->setCierreFecha($idTramite);
				$queryTramite = sprintf("SELECT * FROM tramites WHERE t_id = $idTramite");
				$res_Tramite = $cnn->consultar($queryTramite);
				while ($fila_Tramite = mysql_fetch_assoc($res_Tramite)){
					$fCierre = $fila_Tramite['t_fecha_cierre'];
				}
				$f = $fCierre = explode("-",$fCierre);
				$mes = $f[1];
				$queryCECO = sprintf("SELECT * FROM comprobaciones WHERE co_mi_tramite = $idTramite");
				$res_CECO = $cnn->consultar($queryCECO);
				while ($fila_CECO = mysql_fetch_assoc($res_CECO)){
					$idComp = $fila_CECO['co_id'];
					$ceco = $fila_CECO['co_cc_clave'];
				}
				$queryDetComp = sprintf("SELECT SUM(dc_total) AS descontar FROM detalle_comprobacion WHERE dc_comprobacion = $idComp AND dc_tipo_comprobacion = 'Comprobacion de Reembolso' OR dc_tipo_comprobacion = 'Comprobacion de AMEX'");
				$res_DetComp = $cnn->consultar($queryDetComp);
				while ($fila_DetComp = mysql_fetch_assoc($res_DetComp)){
					$descontar = $fila_DetComp['descontar'];
				}
				$queryPresupuestoE=sprintf("SELECT * FROM periodo_presupuestal WHERE cc_id = '%s' AND MONTH(pp_periodo_inicial) = '%s'",$ceco,$mes);
				$rstPresupuestoE=$cnn->consultar($queryPresupuestoE);
				while ($fila = mysql_fetch_array($rstPresupuestoE)){
					$idPP=($fila["pp_id"]);
					$DPPdisponible=($fila["pp_presupuesto_disponible"]-$descontar);
					$DPPutilizado=($fila["pp_presupuesto_utilizado"]+$descontar);
				}
				echo $queryActualizaE=sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible='%s',pp_presupuesto_utilizado='%s' WHERE pp_id='%s'",$DPPdisponible,$DPPutilizado,$idPP);
				$cnn->ejecutar($queryActualizaE);
			}		
			exit(header("Location: ./index.php?okAut"));		
		}
		
		
		if($modo == "enviarSupervisor"){	
			$montoToleranciaSupervisorFnanzas = ULT_APROBACION;			
			$anticipoComprobado = $_POST["anticipoComprobado"];
			$personalComprobado = $_POST["personalComprobado"];
			$amexComprobado = $_POST["amexComprobado"];
			$efectivoComprobado = $_POST["efectivoComprobado"];
			$amexExternoComprobado = $_POST["amexExternoComprobado"];
			$montoDescotar = $_POST["montoDescotar"];
			$montoReembolsar = $_POST["montoReembolsar"];
			$totalComprobado = $totalComprobado;
			$comprobacion = new Comprobacion();
			$comprobacion->Load_Comprobacion_By_co_mi_tramite($idTramite);
			$comprobacion->ActualizarResumenFinanzas($idTramite, $anticipoComprobado, $personalComprobado, $amexExternoComprobado, $efectivoComprobado, $montoDescotar, $montoReembolsar, $amexExternoComprobado, $totalComprobado);
			$idComprobacion = $comprobacion->Get_dato("co_id");			
			$usuario = new Usuario();
			$siguienteDueno  = ($totalComprobado <= $montoToleranciaSupervisorFnanzas) ? $usuario->getGerenteSFinanzas(SUPERVISOR_FINANZAS) : $usuario->getGerenteSFinanzas(GERENTE_FINANZAS);
			
			$sql = "DELETE 
					FROM detalle_comprobacion 
					WHERE dc_comprobacion = '$idComprobacion'";
			$cnn->ejecutar($sql);
			
			for($i = 1; $i <= $rows; $i++){
				$tipoComprobacion = $_POST['row_tipoComprobacion'.$i];
				$noTransaccion = ($_POST['row_noTransaccion'.$i] == "N/A") ? 0 : $_POST['row_noTransaccion'.$i] ;
				$cargoTarjeta = $_POST['row_cargoTarjeta'.$i];				
				$concepto = $_POST['row_concepto'.$i];
				$tipoComida = ($_POST['row_tipoComida'.$i] == "N/A") ? 0 : $_POST['row_tipoComida'.$i];
				$comentario = $_POST['row_comentario'.$i];
				$asistentes = $_POST['row_asistentes'.$i];
				$fecha = $_POST['row_fecha'.$i];
				$rfc = ($_POST['row_rfc'.$i] == "N/A") ? 0 : $_POST['row_rfc'.$i];
				$folio = $_POST['row_folio'.$i];
				$monto = $_POST['row_monto'.$i];
				$iva = $_POST['row_iva'.$i];
				$propina = $_POST['row_propina'.$i];
				$impuestoHospedaje = $_POST['row_impuestoHospedaje'.$i];
				$total = $_POST['row_total'.$i];
				$divisa = $_POST['row_divisa'.$i];
				$totalPartida = $_POST['row_totalPartida'.$i];
				$dc_origen_personal = $_POST["row_origenPersonal".$i];		
				$dc_id = $_POST['row_id'.$i];														
				$default = ($dc_id != "") ? $dc_id : "DEFAULT";
				
				$sql = "INSERT INTO detalle_comprobacion
                    (dc_id, dc_comprobacion, dc_tipo_comprobacion, dc_notransaccion, dc_idamex, dc_fecha_registro,
					dc_concepto, dc_tipo_comida, dc_comentario, dc_asistentes, dc_fecha_comprobante, dc_rfc, dc_proveedor,
					dc_folio_factura, dc_monto, dc_iva, dc_porcentaje_iva, dc_propina, dc_impuesto_hospedaje, dc_total,
					dc_divisa, dc_tasa_divisa, dc_total_partida, dc_estatus, dc_enviado_sap, dc_origen_personal,
					dc_total_aprobado, dc_total_aprobado_cxp, dc_total_dolares)
                    VALUES
					($default, '$idComprobacion', '$tipoComprobacion', '$noTransaccion', '$cargoTarjeta', NOW(), 
					'$concepto', '$tipoComida', '$comentario', '$asistentes', '$fecha', '$rfc', '0',
					'$folio', '$monto', '$iva', '0', '$propina', '$impuestoHospedaje', '$total',
					'$divisa', '0', '$totalPartida', '0', '0', '$dc_origen_personal',
					'0', '0', '0')";
				$cnn->insertar($sql);					
			}
			
			$dueno = $siguienteDueno;
			$etapa = COMPROBACION_ETAPA_EN_APROBACION_POR_SF;
			$remitente = $siguienteDueno;						
			$mail = 0;
			$redirect = "docs=docs&type=1&action=envia";
		}
		
		if($modo == "rechazar"){
			$tramite->limpiarAutorizaciones($idTramite);					
			$dueno = $t_dueno;
			$etapa = COMPROBACION_ETAPA_RECHAZADA;
			$remitente = $t_dueno;		
			$destinatario = $t_iniciador;
			$mail = 0;
			$ruta = "";			
			$redirect = "action=rechazar";				
		}
		$mensaje = $tramite->crearMensaje($idTramite, $etapa, false, true, $delegado);
		$tramite->Modifica_Etapa($idTramite, $etapa, FLUJO_COMPROBACION, $dueno, $ruta, $delegado);
		$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, $mail, ""); 		
		exit(header("location: index.php?$redirect"));
	}else{ 
	?>
	<script type="text/javascript" src="../../lib/js/formatNumber.js"></script>
	<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script> 
	<script type="text/javascript" src="../../lib/js/jquery/jquery.price_format.1.6.min.js"></script>		
	<script type="text/javascript" src="js/cargaDatos.js"></script>
	<script type="text/javascript" src="js/backspaceGeneral.js"></script>	
	<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>	
	<style>
		span{			
			font-weight: bold;
		}
		.div_flotante{
			background-color: #FAFAFA;
			position:absolute; 
			margin-left:0%; 
			margin-right:0%; 
			margin-top:0%; 
			border:1px solid #808080; 
			padding: 5px; 			
		}		
		.tablaDibujada{			
			border: 1px #CCCCCC solid;
			margin: auto;
			margin-top: 5px;
			text-align:left;
			width: 785px;
		}	
		.button_refresh{
			background:url(../../images/fwk/action_reorder.gif);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}		
		.button_autorizar{
			background:url(../../images/Arrow_Right.png);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.button_ok{
			background:url(../../images/ok.png);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.button_rechazar{
			background:url(../../images/reject.png);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}		
		.button_volver{
			background:url(../../images/back.png);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.button_imprimir{
			background:url(../../images/icon_Imprimir.gif);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}		
		.div_close{
			font-size:11px; 
			bordercolor:#333333:
			border: px1;
			background-color: #A9A9F5;
			color: #FFFFFF;
			width: 100%;
			font-weight: bold;
		}
		.montos_rojos{
			color: #DF0101;
		}
		.input_editable, .input_total{
			border-color: #FFFFFF; 
			text-align: right;
		}
		.button_eliminar{
			width: 27px; 
			height:27px; 
			background:url(../../images/action_cancel.gif); 
			background-position: center;  
			background-repeat: no-repeat;
			border-style: none; 
			cursor: pointer;	
			filter: url(filters.svg#grayscale); /* Firefox 3.5+ */
			filter: gray; /* IE6-9 */
			-webkit-filter: grayscale(1); /* Google Chrome & Safari 6+ */	
			opacity: 0.5;
			filter: alpha(opacity=50); /* For IE8 and earlier */				
		}
		.button_recalcular{
			width: 27px; 
			height: 27px; 
			background: url(../../images/fwk/action_reorder.gif); 
			background-position: center; 
			background-repeat: no-repeat; 
			border-style: none; 
			cursor:pointer;
			filter: url(filters.svg#grayscale); /* Firefox 3.5+ */
			filter: gray; /* IE6-9 */
			-webkit-filter: grayscale(1); /* Google Chrome & Safari 6+ */			
			opacity: 0.5;
			filter: alpha(opacity=50); /* For IE8 and earlier */	
		}
		.button_recalcular:hover , .button_eliminar:hover {			
			filter: none;
			-webkit-filter: grayscale(0);
			opacity: 1;
			filter: alpha(opacity=100); /* For IE8 and earlier */	
		}	
		#comprobacion_table{
			text-align: center;
		}
		#autorizar, #rechazar, #enviarSupervisor, #ventanaDivisa, #ventanaCeco, #table_divisas{
			display: none;
		}
	</style>	
	<script language="JavaScript" type="text/javascript">	
		/**
		 * Inicializa los Eventos una vez que se ha ter minado de Cargar el DOM (Document Object Model)
		 */
		var doc = $(document);
		doc.ready(init);
			
		function init(){				
			blockUI(true);
			
			/**
			 * Funcion Global, permite obtener el valor de un parametro enviado por URL 
			 *
			 * @param name string 	=> Nombre de la variable a obtener
			 * @return string 		=> El valor del parametro si existe este, de lo contrario devuelve 0
			 */
			$._GET = function(name){
				var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(top.window.location.href); 
				return (results !== null) ? results[1] : 0;
			}
			
			/**
			 * Funcion Global, permite centrar un div en pantalla		 
			 */
			jQuery.fn.center = function () {
				this.css("position","absolute");
				this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
				this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
				return this;
			}
			
			/**
			 * Configuración Default del Objeto AJAX de JQuery
			 *
			 * @attr url string			=> Indicamos la ruta al servidor que se encarga de atender las peticiones
			 * @attr timeout int		=> Indicamos el tiempo de espera de respuesta del servidor
			 * @attr async string		=> Indicamos el modo de peticion será sincrono o asincrono
			 * @attr cache boolean		=> Indicamos si guardaremos en la cache las respuestas del servidor
			 * @attr type string		=> Indicamos por que metodo se enviará la información al servidor
			 * @attr dataType string	=> Indicamos el tipo de codificación de los datos que esperamos como respuesta
			 * @attr contentType string => Indicamos el tipo de codificación de los datos enviados
			 */		 			
			var urlServices = "services/services.php";	
			$.ajaxSetup({
				'url':			urlServices,
				'timeout':		5000,
				'async':		false,
				'cache':		false,
				'type':			'post',
				'dataType':		'json',			
				'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
			});
			

			if($._GET("id") != 0){				
				var usuario = $("#iu").val();
				var tramite = $._GET("id");
				var json = obtenInformacionGeneral(tramite);				
				obtenCentrosCostosFI(usuario);
				obtenInformacionGasolina(tramite);				
				
				asignaVal("t_etapa_actual", json["t_etapa_actual"]);			
				asignaVal("centro_de_costos_new", json["co_cc_clave"]);	
				asignaVal("centro_de_costos_old", json["co_cc_clave"]);	
				asignaVal("cecoVentana", json["co_cc_clave"]);	
				
				asignaVal("idT", tramite);			
				
				var anticipoCeco = obtenAnticipoSol(tramite,0);	
				var anticipo = anticipoCeco["sv_total_anticipo"];
				anticipo = (!anticipoCeco ) ? "0.00" : anticipo;
				asignaVal("anticipo", anticipo);	
				asignaText("div_anticipo", anticipo, "number");

				var objetoPartidas = obtenDatosPartida(tramite);
				for(var prop in objetoPartidas){
					var partida = objetoPartidas[prop];
					for(var propPar in partida)
						partida[propPar] = (partida[propPar] == "" || partida[propPar] == null) ? "N/A" :  partida[propPar];
					
					var id = obtenTablaLength("comprobacion_table")+1;
					var	renglon = creaRenglonFI(objetoPartidas[prop], id)
					agregaRenglon(renglon, "comprobacion_table");
					obtenConceptosPartida(id)
				}						
				$(".button_eliminar").hide();
				
				calcularResumen();
				asignaVal("total_rows",obtenTablaLength("comprobacion_table"));
		var ant = $( "#anticipo").val();
		var rowCount = $('#comprobacion_table tr').length;
		var rowTotal = 0;
		var rowIVA = 0;
		var rowRetIVA = 0;
		var rowRetISR = 0;
		var rowDesc = 0;
		var rowIeps = 0;

		var rowTotalRbl = 0;
		var rowIVARbl = 0;
		var rowRetIVARbl = 0;
		var rowRetISRRbl = 0;
		var rowDescRbl = 0;
		var rowIepsRbl = 0;
		
		var rowTotalAnt = 0;
		var rowIVAAnt = 0;
		var rowRetIVAAnt = 0;
		var rowRetISRAnt = 0;
		var rowDescAnt = 0;
		var rowIepsAnt = 0;
		
		var rowTotalAMEX = 0;
		var rowIVAAMEX = 0;
		var rowRetIVAAMEX = 0;
		var rowRetISRAMEX = 0;
		var rowDescAMEX = 0;
		var rowIepsAMEX = 0;
		
		var rowConceptoAnt = '';
			for (i = 1; i < rowCount; i++) {
				rowTipoComp = $( "#div_row_tipoComprobacion"+i).html();
				rowConcepto = $( "#div_row_conceptoTexto"+i).html();				
					switch(rowConcepto) {
						case "Impuesto de IVA":
							rowIVA += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
							rowIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							break;
						case "Retenciones IVA":
							rowRetIVA += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
							rowRetIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowRetIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowRetIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							break;
						case "Retenciones ISR":
							rowRetISR += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
							rowRetISRRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowRetISRAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowRetISRAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							break;													
						case "Descuento":
							rowDesc += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
							rowDescRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowDescAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowDescAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							break;
						case "IEPS":
							rowIeps += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
							rowIepsRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowIepsAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowIepsAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							break;							
						default:
							rowTotal += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
							rowTotalRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowTotalAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
							rowTotalAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
					}
			}
			$( "#div_res_subtotal").html("$"+rowTotal.toFixed(2));
			$( "#div_res_desc").html("$"+rowDesc.toFixed(2));
			$( "#div_res_iva").html("$"+rowIVA.toFixed(2));
			$( "#div_res_ret_iva").html("$"+rowRetIVA.toFixed(2));
			$( "#div_res_ret_isr").html("$"+rowRetISR.toFixed(2));
			$( "#div_res_ieps").html("$"+rowIeps.toFixed(2));
			
			$( "#div_res_subtotal_ant").html("$"+rowTotalAnt.toFixed(2));
			$( "#div_res_desc_ant").html("$"+rowDescAnt.toFixed(2));
			$( "#div_res_iva_ant").html("$"+rowIVAAnt.toFixed(2));
			$( "#div_res_ret_iva_ant").html("$"+rowRetIVAAnt.toFixed(2));
			$( "#div_res_ret_isr_ant").html("$"+rowRetISRAnt.toFixed(2));
			$( "#div_res_ieps_ant").html("$"+rowIepsAnt.toFixed(2));
			
			$( "#div_res_subtotal_amex").html("$"+rowTotalAMEX.toFixed(2));
			$( "#div_res_desc_amex").html("$"+rowDescAMEX.toFixed(2));
			$( "#div_res_iva_amex").html("$"+rowIVAAMEX.toFixed(2));
			$( "#div_res_ret_iva_amex").html("$"+rowRetIVAAMEX.toFixed(2));
			$( "#div_res_ret_isr_amex").html("$"+rowRetISRAMEX.toFixed(2));
			$( "#div_res_ieps_amex").html("$"+rowIepsAMEX.toFixed(2));									
				var TotalReembolso = rowTotalRbl - rowDescRbl + rowIVARbl - rowRetIVARbl - rowRetISRRbl - rowIepsRbl;
				$( "#div_total_reembolso").html("$"+TotalReembolso.toFixed(2));
				var TotalAnticipo = rowTotalAnt - rowDescAnt + rowIVAAnt - rowRetIVAAnt - rowRetISRAnt - rowIepsAnt;
				$( "#div_comp_anticipo").html("$"+TotalAnticipo.toFixed(2));
				var TotalAMEX = rowTotalAMEX - rowDescAMEX + rowIVAAMEX - rowRetIVAAMEX - rowRetISRAMEX - rowIepsAMEX;
				$( "#div_comp_amex").html("$"+TotalAMEX.toFixed(2));
				var TotalPendiente = ant - TotalAnticipo;
				$( "#div_pendiente").html("$"+TotalPendiente.toFixed(2));
				var div_totalSuma = TotalReembolso + TotalAnticipo +TotalAMEX;
				$( "#div_totalSuma").html("$"+div_totalSuma.toFixed(2));					
			}
			
			$("#ventanaDivisa").draggable({containment : "#Interfaz", scroll: false}).center();		
			$("#ventanaCeco").draggable({containment : "#Interfaz", scroll: false}).center();
			
			var etapa = $("#t_etapa_actual").val();
			var perfil = $("#perfil").val();			
			var registrosOriginales = obtenTablaLength("comprobacion_table");
			var delegado = $("#delegado").val();
			var privilegios = $("#privilegios").val();
			var tramite = $._GET("id");		
			
			deshabilitaElemento();
			/////MUESTRO AUTORIZACION//////
			ocultarElemento("continuar");
			ocultarElemento("ventanaCeco");
			//mostrarElemento("ventanaCeco");
			validaDivisasInit(etapa, perfil);
			validaCecoInit(etapa, perfil);
			validaBotonesInit(etapa, perfil, delegado, privilegios);
			habilitaElemento();	
			habilitaElemento("Volver");
			
			validaRefrescarInit(perfil);
			validaHistorialInit(perfil);
			validaColumnasInit(perfil, etapa);
			validaLecturaColumnasInit(perfil);
			validaExcepcionesInit(perfil, tramite);
			
			obtenExcepciones($._GET("id"));
			obtenExcepcionesPresupuesto($._GET("id"));
			
			$("#continuar").click(function(){
				ocultarElemento("continuar");
				mostrarElemento("ventanaCeco");				
				habilitaElemento("continuar2");
				habilitaElemento("cecoVentana");
			});
			
			$("#cecoVentana").change(function(){
				var val = $(this).val();
				asignaVal("centro_de_costos_new", val);			
				deshabilitaElemento("centro_de_costos_new");					
				if(val != $("#centro_de_costos_old").val())
					asignaVal("continuar2","     Enviar a CECO");						
				else
					asignaVal("continuar2","     Continuar");	
			});
			
			$("#continuar2").click(function(){
				var val = $.trim($(this).val());
				if(val == "Continuar"){
					ocultarElemento("ventanaCeco");
					validaDivisasInit(etapa, perfil);
					validaCecoInit(etapa, perfil);
					validaBotonesInit(etapa, perfil, delegado, privilegios);
					habilitaElemento();
				}else if(val == "Enviar a CECO"){
					if(confirm("Esta seguro que desea reasignar el CECO?")){
						habilitaElemento();
						$("#comprobacion_form").attr("action", "comprobacion_travel_view.php?modo=autorizar");				
						$("#comprobacion_form").submit();		
					}					
				}				
			});
			
			$("#aceptarDivisa").click(function(){
				var requeridos = validaRequeridos($("#ventanaDivisa"));				
				if(requeridos){
					var usd = $("#tasaUSDeditable").val();
					var eur = $("#tasaEUReditable").val();				
					guardarTasas(tramite);
					asignaVal("valorDivisaEUR", usd);
					asignaVal("valorDivisaUSD", eur);
					asignaText("span_tasaDollar", usd, "number");
					asignaText("span_tasaEuro", eur, "number");			
					ocultarElemento("ventanaDivisa");
					habilitaElemento();
					deshabilitaElemento("centro_de_costos_new");
					recalculaTotalPartida();
				}				
			});
				
			$("#actualizaResumen").click(function(){
				calcularResumen();
				asignaVal("total_rows",obtenTablaLength("comprobacion_table"));
			});
			
			$("#autorizar").click(function(){
				var pendientes = validaPendientes();
				if(pendientes){
					ocultarElemento("botones_table");								
					validarPresupuesto();
					$("#comprobacion_form").attr("action", "comprobacion_travel_view.php?modo=autorizar");				
					$("#comprobacion_form").submit();				
				}
			});			
			
			$("#enviarSupervisor").click(function(){
				var pendientes = validaPendientes();
				if(pendientes){
					ocultarElemento("botones_table");
					$("#comprobacion_form").attr("action", "comprobacion_travel_view.php?modo=enviarSupervisor");				
					$("#comprobacion_form").submit();				
				}				
			});

			$("#Volver").click(function(){
				ocultarElemento("botones_table");
				location.href="index.php";
			});
			
			$("#imprimir").click(function(){
					var subtotal = $("#div_res_subtotal").html();
					var descuento = $("#div_res_desc").html();
					var iva = $("#div_res_iva").html();
					var retisr = $("#div_res_ret_isr").html();
					var retiva = $("#div_res_ret_iva").html();
					var ieps = $("#div_res_ieps").html();
					var anticipo = $("#div_comp_anticipo").html();
					var amex = $("#div_comp_amex").html();
					var reembolso = $("#div_total_reembolso").html();
					var pendiente = $("#div_pendiente").html();					
				window.open("generador_pdf.php?id="+$._GET("id")+"&subtotal="+subtotal+"&descuento="+descuento+"&iva="+iva+"&retisr="+retisr+"&retiva="+retiva+"&ieps="+ieps+"&anticipo="+anticipo+"&amex="+amex+"&reembolso="+reembolso+"&pendiente="+pendiente,"imprimir");
			});	

			$("#rechazar").click(function(){
				ocultarElemento("botones_table");
				$("#comprobacion_form").attr("action", "comprobacion_travel_view.php?modo=rechazar");				
				$("#comprobacion_form").submit();				
			});
		
			$(".button_eliminar").live('click', function(){
				if(confirm("Confirma que desea eliminar esta partida?")){
					var id = $(this).attr("id");
					var objetoRenglonPersonal = generaObjetoRenglon(id);
					var idOriginal = objetoRenglonPersonal["origenPersonal"];
					var objetoRenglonOrignal = generaObjetoRenglon(idOriginal);
					var objetoSumando = sumaRenglones(objetoRenglonOrignal, objetoRenglonPersonal);
					var renglonModificado = creaRenglonFI(objetoSumando, idOriginal);
					modificarRenglon(renglonModificado, idOriginal,"comprobacion_table");					
					$(".input_editable").priceFormat({
						prefix: '', centsSeparator: '.', thousandsSeparator: ','
					});
					$(".input_total").priceFormat({
						prefix: '', centsSeparator: '.', thousandsSeparator: ','
					});	
					validaColumnasRecalculo(idOriginal,[21]);
					obtenConceptosPartida(idOriginal);
					borrarRenglon(id,"comprobacion_table");
					validaColumnasInit(perfil, etapa);					
					calcularResumen();
				}						
			});
			
			$(".button_recalcular").live('click', function(){
				var tablaLength = obtenTablaLength("comprobacion_table");
				var id = $(this).attr("id");
				var idProximo = tablaLength+1;
				var valida = validarLimiteTotal(id);
				if(valida){
					var objetoRenglon = generaObjetoRenglon(id);	
					var detalleOrignal = obtenDetalleOriginal(objetoRenglon["dc_id"]);
					var comparacion = comparaValoresDetalle(objetoRenglon, detalleOrignal, id);
					var renglonPersonal = creaRenglonFI(comparacion, idProximo );
					agregaRenglon(renglonPersonal,"comprobacion_table");
					validaLecturaColumna("comprobacion_table", idProximo)					
					validaColumnasInit(perfil, etapa);
					validaColumnasRecalculo(idProximo,[19,20]);
					validaColumnasRecalculo(id,[20]);
					$("#div_row_monto"+idProximo).trigger("keydown");
					$("#div_row_iva"+idProximo).trigger("keydown");
					$("#div_row_propina"+idProximo).trigger("keydown");
					$("#div_row_impuestoHospedaje"+idProximo).trigger("keydown");
					$("#div_row_total"+idProximo).trigger("keydown");
					$("#div_row_total"+idProximo).trigger("keydown");
					asignaVal("row_pendiente"+id, 0);
					
					calcularResumen();												
				}				
			});
			
			$("#centro_de_costos_new").change(function(){
				if($(this).val() != $("#centro_de_costos_old").val()){
					ocultarElemento("enviarSupervisor");					
					asignaVal("autorizar", "       Autorizar");
				}		
			});
			
			/**
			 * Evento, inicializa <input monto>
			 *
			 * 1 Asgina una mascara al campo
			 */
			$('#tasaUSDeditable').priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			});
			
			
			/**
			 * Evento, inicializa <input monto>
			 *
			 * 1 Asgina una mascara al campo
			 */
			$('#tasaEUReditable').priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			});
			
			$('.input_editable').live('keydown', function(){
				$("#"+id).priceFormat({
					prefix: '', centsSeparator: '.', thousandsSeparator: ','
				});
			});
			
			
			$('.input_editable').live("click",function(){				
				$('.input_editable').blur(function(){								
					asignaPendiente($(this).attr("id"));
					calculaTotalesPartida(id);			
				});
			});
			
			$('.input_total').live('keydown', function(){				
				var id = $(this).attr("id");
				$("#"+id).priceFormat({
					prefix: '', centsSeparator: '.', thousandsSeparator: ','
				});
			});			
			
			/*$('input').live('keydown', function(){				
				var id = $(this).attr("id");					
				confirmaRegreso(id);				
			});*/
	
			blockUI(false);
		}		
	</script>
		<div align="center" id="Layer1">
			<form name="comprobacion_form" id="comprobacion_form" action="" method="post"><div align="center">
			<table id="comprobacion_informacion_table" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
				<tr>
					<td colspan="5">
						<div align="center" style="color:#003366"><strong>Informaci&oacute;n general</strong></div>
					</td>
				</tr>
				<tr><td colspan="5"></td></tr>
				<tr>
					<td width="6%" align="right">No. de Folio:</td>
					<td width="18%" align="left"><span id="span_noFolio"></td>
					<td width="2%" rowspan="4"></td>
					<td width="12%" align="right">Fecha de creaci&oacute;n:</td>
					<td width="14%"><span id="span_fechaRegistro"></span></td>
				</tr>
				<tr>
					<td align="right">Solicitante:</td>
					<td><span id="span_nombreEmpleado"></td>
					<td align="right">Centro de costos:</td>
					<td><span id="span_ceco"></span></td>
				</tr>						
				<tr>
					<td align="right">Motivo:</td>
					<td><span id="span_motivo"></span></td>				
					<td align="right">Destino:</td>
					<td><span id="span_destino"></span></td>				
				</tr>
				<tr>
					<td align="right">Fecha de Viaje:</td>
					<td><span id="span_fechaViaje"></span></td>				
					<td align="right">Etapa:</td>
					<td><span id="span_etapa"></span></td>
				</tr>       
				<tr>
					<td align="right">Autorizador(es):</td>
					<td colspan="5"><span id="span_autorizadores"></span></td>
				</tr>
				<tr><td colspan="5"></td></tr>							   
			</table>
			<br />
			<br />
			<table width="29%" align="center" id="table_divisas">
				<tr id="tr_divisas">
					<td><strong>Tasa USD:&nbsp;&nbsp;</strong><span id="span_tasaDollar"></span></td>
					<td><strong>Tasa EUR:&nbsp;&nbsp;</strong><span id="span_tasaEuro"></span></td>					
				</tr>
				<tr>
					<td colspan="2">
						Centro de costos:
						<select name='centro_de_costos_new' id="centro_de_costos_new"></select>
						<input type="hidden" id="centro_de_costos_old" name="centro_de_costos_old" />					
					</td>
				</tr>
			</table>
			<br />
			<br />
			<div align="center"><h1>Gastos comprobados</h1></div>
			<table id="comprobacion_table" class="tablesorter" cellspacing="1">
			  <thead>
				<tr>
					<th>No.</th>
					<th>Tipo</th>
					<th>No. Transacci&oacute;n</th>
					<th>Fecha</th>
					<th>Concepto</th>
					<th>Concepto AMEX</th>
					<th>Comentario</th>
					<th>No. Asistentes</th>
					<th>RFC</th>
					<th>Proveedor</th>
					<th>Factura</th>
					<th>Monto</th> 
					<th>IVA</th>
					<th>Impuesto <br/>Hospedaje</th>			
					<th>Total</th>
					<th>Total</th>
					<th>Divisa</th>
					<th>Excepci&oacute;n</th>		  
					<th>Reasignaci&oacute;n de concepto</th>
					<th>Recalcular</th>
					<th>Eliminar</th>	
					<th>--</th>	
					<th>XML</th>						
				</tr>
			  </thead>
			  <tbody></tbody>
			</table>
			<table style="display:none"			align="right">
				<tr>
					<td><span id="span_totalComprobacionText"></span></td>
					<td>
						<span id="span_totalComprobacion"></span>
						<input type="hidden" name="totalComprobacion" id="totalComprobacion" value=""/>
					</td>
					<td>MXN</td>				
				</tr>
			</table>
			<br />
			<div id="div_excepcion_table">
	    	<h1 align="center">Excepciones</h1>
				<table id="excepcion_table" class="tablesorter" style="text-align: left; width: 580px;">
					<thead>
						<tr>
							<th>Concepto</th>
							<th>Mensaje</th>
							<th>Excedente</th>
							<th>Tipo de Excepci&oacute;n</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>			
			</div>
			<br />
			<div id="div_refrescar">
				<input class="button_refresh" type="button" name="actualizaResumen" id="actualizaResumen" value="       Actualizar Resumen"  onclick="actualizarResumen();"/>
			</div>
			<br />
			<table>		
				<tr>
					<td>	
						<table>
							<tr>
								<td width="10px">Historial de observaciones: </td>
								<td>
									<textarea name="campo_historial" id="campo_historial" cols="90" rows="5" readonly="readonly" ></textarea>
								</td>
							</tr>
						</table>   
						<table id="table_historialObservaciones">
							<tr>
								<td width="10px">Observaciones: </td>
								<td><textarea name="campo_observaciones" id="campo_observaciones" rows="5" cols="90"></textarea></td>								
							</tr>
						</table>													
					</td>
					<td width="110px">&nbsp;</td>		
					<td>
						<div align="center">
							<table class="tablaDibujada" style="width:300px; text-align:right;" >
								<tr>
									<td colspan="2" style="text-align: center;"><h3>Resumen</h3></td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
                                <tr>
                                    <td>Subtotal: </td>
                                    <td>
										<div id="div_res_subtotal">$ 0.00</div>
										<div style="display:none;" id="div_res_subtotal_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_subtotal_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr>
                                    <td>Descuento: </td>
                                    <td>
										<div id="div_res_desc">$ 0.00</div>
										<div style="display:none;" id="div_res_desc_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_desc_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr>
                                    <td>IVA: </td>
                                    <td>
										<div id="div_res_iva">$ 0.00</div>
										<div style="display:none;" id="div_res_iva_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_iva_amex">$ 0.00</div>
									</td>
                                </tr>
								<tr>
                                    <td>Retencion de ISR: </td>
                                    <td>
										<div id="div_res_ret_isr">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_isr_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_isr_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr>
                                    <td>Retencion de IVA: </td>
                                    <td>
										<div id="div_res_ret_iva">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_iva_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_iva_amex">$ 0.00</div>
									</td>
                                </tr>
								 <tr>
                                    <td>IEPS: </td>
                                    <td>
										<div id="div_res_ieps">$ 0.00</div>
										<div style="display:none;" id="div_res_ieps_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_ieps_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr class="montos_rojos">
                                    <td>Total comprobacion Anticipo:</td>
                                    <td>
										<div id="div_comp_anticipo">$ 0.00</div>
										<input type="hidden" name="anticipo" id="anticipo" value="0" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Total comprobacion AMEX:</td>
                                    <td>
										<div id="div_comp_amex">$ 0.00</div>
										<input type="hidden" name="montoDescontar" id="montoDescontar" value="" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Total de Reembolso:</td>
                                    <td>
										<div id="div_total_reembolso">$ 0.00</div>
										<input type="hidden" name="montoDescontar" id="montoDescontar" value="" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Pendiente de comprobar:</td>
                                    <td>
										<div id="div_pendiente">$ 0.00</div>
										<input type="hidden" name="montoDescontar" id="montoDescontar" value="" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Total:</td>
                                    <td>
										<div id="div_totalSuma">$ 0.00</div>
										<input type="hidden" name="montoSuma" id="montoSuma" value="" />
									</td>
                                </tr>								
							</table>	
							
							<br/><br/>
							<table id="table_gasolina" class="tablaDibujada" style="width:300px; text-align:right;">
								<tr><td colspan="2" align="center"><h3>Detalle de Gasolina en M&eacute;xico</h3></td></tr>
								<tr><td colspan="2">&nbsp;</td></tr>								
								<tr>
									<td>Modelo Auto: </td>
									<td><span id="span_modeloAtuo"></span></td>
								</tr>
								<tr>
									<td>Factor Gasolina: </td>
									<td><span id="span_factorGasolina"></span></td>
								</tr>
								<tr>
									<td>Kilometraje:  </td>
									<td><span id="span_kilometraje"></span></td>
								</tr>
								<tr>
									<td>Monto por Factor:  </td>
									<td><span id="span_montoGasolina"></span></td>
								</tr>
								<tr style="color:blue">
									<td>Total Gasolina: </td>
									<td><span id="span_totalGasolina"></span></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr><td>Ruta detallada:  </td>
									<td><textarea name="rutaDetalladaTextArea" id="rutaDetalladaTextArea" readonly="readonly" onkeypress="confirmaRegreso(this.id);" rows="3" ></textarea></td>
								</tr>			
							</table>							
						</div>	
					</td>		
				</tr>
			</table>
		
		<div class="div_flotante" id="ventanaCeco" >
			<table class="div_close"><tr><td>Reasignaci&oacute;n de CECO</td></tr></table>		
			<div width="400" align="center">
				<p>S&iacute; se es reasignado el CECO, no se permitir&aacute; modificar los montos registrados</p>	
				Centro de costos:<select name='cecoVentana' id="cecoVentana"></select><br/><br/>				
				<input class="button_ok" type="button"  value="     Continuar"  name="continuar2" id="continuar2"/>				
			</div>
		</div>
		
		<div class="div_flotante" id="ventanaDivisa" >
			<table class="div_close"><tr><td>Captura de tasa</td></tr></table>
			<table width="400" align="center"  class="tablaDibujada" style="width:400px">
				<tr >
					<td rowspan="5">&nbsp;</td>
					<td align="right">Tasa USD:</td>
					<td rowspan="2">&nbsp;</td>
					<td align="left"><input type="text" name="tasaUSDeditable" id="tasaUSDeditable" value="<?PHP echo $divisaUSD;?>" size="10" maxlength="13" /></td>					
				</tr>
				<tr >
					<td align="right">Tasa EUR:</td>					
					<td align="left"><input type="text" name="tasaEUReditable" id="tasaEUReditable" value="<?PHP echo $divisaEUR;?>" size="10" maxlength="13" /></td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td colspan="3" align="center"><strong>Nota: Para desbloquear la pantalla, de click en 'Aceptar'.</strong></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
			</table>
			<br />
			<input class="button_ok" type="button" name="aceptarDivisa" id="aceptarDivisa" value="     Aceptar"/>
		</div>
		
		<center>
		<table id="botones_table">
			<tr>
				<td>									
					<input class="button_ok" type="button"  value=""  name="autorizar" id="autorizar"/>
					<!--input class="button_autorizar" type="button" value="     Enviar a Supervisor" id="enviarSupervisor" name="enviarSupervisor"/>-->
					<input class="button_ok" type="button" value="     Continuar" id="continuar" name="continuar"/>
					<input class="button_volver" type="button" value="     Volver"  name="Volver" id="Volver"/>					
					<input class="button_rechazar" type="button" value="     Rechazar" id="rechazar" name="rechazar"/>
					<input type="button" value="     Imprimir" id="imprimir"    name="imprimir"    class="button_imprimir" />
				</td>
			</tr>
		</table>
		</center>		
		
		<input type='hidden' id='valorDivisaMEX' name='valorDivisaMEX' value="1">
		<input type='hidden' id='valorDivisaEUR' name='valorDivisaEUR' value="0">
		<input type='hidden' id='valorDivisaUSD' name='valorDivisaUSD' value="0">		
		<input type="hidden" name="total_rows" id="total_rows" value="" readonly="readonly"/>
		<input type="hidden" name="idT" id="idT" value="0" />    
		<input type="hidden" name="iu" id="iu" value="<?PHP echo $_SESSION["idusuario"]; ?>" />
		<input type="hidden" name="perfil" id="perfil" value="<?PHP echo $tipoUsuario; ?>" />
		<input type="hidden" name="delegado" id="delegado" value="<?PHP echo $_SESSION['idrepresentante'];?>" />
		<input type="hidden" name="privilegios" id="privilegios" value="<?PHP echo $_SESSION['privilegios'];?>" />
		<input type="hidden" name="t_etapa_actual" id="t_etapa_actual" value="0"/>
		</form>
	</div>		
<?PHP
	}
?>