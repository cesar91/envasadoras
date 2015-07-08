<?php

/* * ******************************************************
 *            T&E Catálogo de Proveedores                *
 * Creado por:	  Jorge Usigli Huerta 11-Ene-2010	 *
 * Modificado por: Jorge Usigli Huerta 04-Feb-2010       *
 * Modificado por: Uziel Castillo 06/OCT/2011   *
 * PHP, jQuery, JavaScript, CSS                          *
 * ******************************************************* */
//require_once("$RUTA_A/lib/php/constantes.php");
//require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

function Add_excepcion($mensaje, $diferencia, $solicitud_id, $comprobacion_id, $detalleCom_id, $concepto, $detalleSol_id, $sv_itinerario, $excedePresupuesto = 0){
    $cnn = new conexion();
    $query = sprintf("
                            insert into excepciones
                            (
                                ex_id,
								ex_mensaje,
                                ex_diferencia,
								ex_solicitud,
								ex_comprobacion,
								ex_comprobacion_detalle,
								ex_concepto,
								ex_solicitud_detalle,
								ex_solicitud_itinerario,
								ex_presupuesto
                            )
                            VALUES(
                                default, 
                                '%s',
                                %s,
                                %s,
                                %s,
								%s,
                                %s,
								%s,
								%s,
								%s
                            )
                            ",
                    $mensaje,
                    $diferencia,
                    $solicitud_id,
                    $comprobacion_id,
                    $detalleCom_id,
					$concepto,
					$detalleSol_id,
					$sv_itinerario,
					$excedePresupuesto
    );
    return($cnn->insertar($query));
}
       
function Add_detalle($comprobacion, $tipoComprobacion, $noTransaccion, $cargoTarjeta, $concepto, $tipoComida, $comentario, $asistentes, $fecha, $rfc, $proveedor, $folio, $monto, $iva, $propina, $impuestoHospedaje, $total, $divisa, $totalPartida){
    $date = explode("/", $fecha);
    if(count($date) != 3)
        return "";    
    $fecha = $date[2]."".$date[1]."".$date[0];
	$prov = ($proveedor == "") ? "0" : $proveedor;
    $cnn = new conexion();
	$query = "INSERT INTO detalle_comprobacion
                    (dc_id, dc_comprobacion, dc_tipo_comprobacion, dc_notransaccion, dc_idamex, dc_fecha_registro,
					dc_concepto, dc_tipo_comida, dc_comentario, dc_asistentes, dc_fecha_comprobante, dc_rfc, dc_proveedor,
					dc_folio_factura, dc_monto, dc_iva, dc_porcentaje_iva, dc_propina, dc_impuesto_hospedaje, dc_total,
					dc_divisa, dc_tasa_divisa, dc_total_partida, dc_estatus, dc_enviado_sap, dc_origen_personal,
					dc_total_aprobado, dc_total_aprobado_cxp, dc_total_dolares)
                    VALUES
					(DEFAULT, '$comprobacion', '$tipoComprobacion', '$noTransaccion', '$cargoTarjeta', NOW(), 
					'$concepto', '$tipoComida', '$comentario', '$asistentes', '$fecha', '$rfc', '$prov',
					'$folio', '$monto', '$iva', '0', '$propina', '$impuestoHospedaje', '$total',
					'$divisa', '0', '$totalPartida', '0', '0', '0',
					'0', '0', '0')";
	return($cnn->insertar($query));	
}

function add_tramite($tramite, $excedente, $refencia, $usuario, $jefe, $flujo) {
    
    if(!(isset($refencia))){
        $refencia="";
    }
    
    $cnn = new conexion();

    $query = sprintf("insert into tramites
				(
				t_id,
				t_fecha_registro,
				t_cancelado,
				t_cerrado,
				t_fecha_ultima_modificacion,
				t_iniciador,				
				t_owner,
				t_etapa_actual,
				t_aceptado,
				t_flujo,
				t_comprobado,
				t_etiqueta
				)
		
			VALUES(
				default,
				now(),
				false,
				false,
				now(),
				%s,				
				%s,
				3,
				false,
				%s,
				false,
				'%s'
			)
		", $usuario, $jefe, $flujo, $refencia
    );

    //error_log($query);
    return($cnn->insertar($query));
}

function Add_new($tramite, $anticipo_comprobado, $amex_comprobado, $mnt_reembolso, $mnt_descuento, $total, $Dpto, $refacturar, $observ) {

    $anticipo_comprobado = str_replace(",", "", $anticipo_comprobado);
    $amex_comprobado = str_replace(",", "", $amex_comprobado);
    $mnt_reembolso = str_replace(",", "", $mnt_reembolso);
    $mnt_descuento = str_replace(",", "", $mnt_descuento);
    $total = str_replace(",", "", $total);
 
    $cnn = new conexion();
    $query = sprintf(
                    "insert into comprobaciones (
                        co_id, 
                        co_anticipo_comprobado,
                        co_amex_comprobado,
                        co_mnt_reembolso,
                        co_mnt_descuento,
                        co_total,
                        co_tramite,
                        co_fecha_registro,
                        co_cc_clave,
                        co_refacturar,
                        co_status_erp,
                        co_observaciones
                    ) VALUES(
                        default,
                        %s,
                        %s,            
                        %s,
                        %s,
                        %s,
                        %s,
                        now(),
                        %s,
                        %s,
                        '0',
                        '%s'
                    )
        ",
                    $anticipo_comprobado,
                    $amex_comprobado,                    
                    $mnt_reembolso,
                    $mnt_descuento,
                    $total,
                    $tramite,
                    $Dpto,
                    $refacturar,
                    $observ
    );
    $id_comprobacion = $cnn->insertar($query);
    return ($id_comprobacion);
}



function Add_new_comprobacion($idTramite2, $idTramite,$centroCosto,$observ,$observEd,$totalComprobaciones,$total_anticipo,$anticipoCompAutBMW,$personalDescontar,$amexCompAutBMW,$efectivoCompAutBMW,$montoDescontar,$montoReembolsar,$co_gasolina,$fecha_inicial,$fecha_final,$motivo_gasolina,$co_tipo_auto,$co_modelo_auto,$co_kilometraje,$co_monto_gasolina,$co_ruta,$amexExterno) {
	
    $totalComprobaciones = floatval(str_replace(",", "", $totalComprobaciones));
    $anticipoCompAutBMW = floatval(str_replace(",", "", $anticipoCompAutBMW));
    $personalDescontar = floatval(str_replace(",", "", $personalDescontar));
    $amexCompAutBMW = floatval(str_replace(",", "", $amexCompAutBMW));
    $efectivoCompAutBMW = floatval(str_replace(",", "", $efectivoCompAutBMW));
    $montoDescontar = floatval(str_replace(",", "", $montoDescontar));
    $montoReembolsar = floatval(str_replace(",", "", $montoReembolsar));
    $total_anticipo = floatval(str_replace(",", "", $total_anticipo));
	$amexExterno = floatval(str_replace(",", "", $amexExterno));     
		
	if($fecha_inicial!= '00000000'){	
		$date = explode("/", $fecha_inicial);
		if (count($date) != 3) {
			return "";
		}
		$fecha_inicial = $date[2] . "" . $date[1] . "" . $date[0];
	}
	
	if($fecha_final!='00000000'){	
		   $date = explode("/", $fecha_final);
		if (count($date) != 3) {
			return "";
		}
		$fecha_final = $date[2] . "" . $date[1] . "" . $date[0];
	}
	
    $cnn = new conexion();
    $query = sprintf(
                    "insert into comprobaciones (
                        co_id, 
                        co_anticipo_comprobado,
                        co_amex_comprobado,
                        co_mnt_reembolso,
                        co_mnt_descuento,
                        co_total,
                        co_tramite,
						co_mi_tramite,
                        co_fecha_registro,
                        co_cc_clave,                       
                        co_observaciones,
    					co_observaciones_edicion,
						co_anticipo_viaje,
						co_personal_descuento,
						co_efectivo_comprobado,
						co_gasolina,
						co_fecha_inicial_gasolina,
						co_fecha_final_gasolina,
						co_motivo_gasolina,
						co_tipo_auto, 
						co_modelo_auto, 
						co_kilometraje, 
						co_monto_gasolina, 
						co_ruta,
						co_amex_externo
                    ) VALUES(
                        default,
                        '%s',
                        '%s',            
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        now(),
                        '%s',                       
                        '%s',
    					'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						%s,
						%s,
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s'
                    )
        ",
                    $anticipoCompAutBMW,
                    $amexCompAutBMW,                    
                    $montoReembolsar,
                    $montoDescontar,
                    $totalComprobaciones,
                    $idTramite2,
                    $idTramite,
                    $centroCosto,
                    $observ,
    				$observEd,
					$total_anticipo,
					$personalDescontar,
					$efectivoCompAutBMW,
					$co_gasolina,
					$fecha_inicial,
					$fecha_final,
					$motivo_gasolina,
					$co_tipo_auto,
					$co_modelo_auto, 
					$co_kilometraje, 
					$co_monto_gasolina, 
					$co_ruta,
					$amexExterno
    );
	
	//error_log($query);
	$id_comprobacion = $cnn->insertar($query);
    return ($id_comprobacion);
}

function Add_factura($uuid,$fechaTimbrado,$selloCFD,$noCertificadoSat,$selloSat,$emisorNombre,$emisorRFC,$emisorDomicilio,$emisorEstado,$emisorPais,$comprobanteVersion,$comprobanteSerie,$comprobanteFolio,$comprobanteFecha,$comprobanteSello,$comprobanteFPago,$comprobanteNoCer,$comprobanteCertificado,$comprobanteSubtotal,$comprobanteTipoCambio,$comprobanteMoneda,$comprobanteTotal,$comprobanteTipoComp,$comprobanteMetodoPago,$comprobanteExpedicion){
    $cnn = new conexion();
	$query = "INSERT INTO factura 
				(uuid,
					f_timbrado,
					noCertSat, 
					selloSat, 
					e_nombre, 
					e_rfc, 
					e_domicilio, 
					e_estado, 
					e_pais, 
					c_version, 
					c_serie, 
					c_folio,
					c_fecha,
					c_sello, 
					c_fpago, 
					c_nocer, 
					c_tipocambio, 
					c_moneda, 
					c_subtotal, 
					c_total, 
					c_tipocomprobante, 
					c_lugarexpedicion) 
					VALUES (
							'".mysql_real_escape_string($uuid)."', 
							'".mysql_real_escape_string($fechaTimbrado)."', 
							'".mysql_real_escape_string($noCertificadoSat)."', 
							'".mysql_real_escape_string($selloSat)."',
							'".mysql_real_escape_string($emisorNombre)."', 
							'".mysql_real_escape_string($emisorRFC)."', 
							'".mysql_real_escape_string($emisorDomicilio)."', 
							'".mysql_real_escape_string($emisorEstado)."', 
							'".mysql_real_escape_string($emisorPais)."',
							'".mysql_real_escape_string($comprobanteVersion)."', 
							'".mysql_real_escape_string($comprobanteSerie)."', 
							'".mysql_real_escape_string($comprobanteFolio)."', 
							'".mysql_real_escape_string($comprobanteFecha)."', 
							'".mysql_real_escape_string($comprobanteSello)."', 
							'".mysql_real_escape_string($comprobanteFPago)."', 
							'".mysql_real_escape_string($comprobanteNoCer)."', 
							'".mysql_real_escape_string($comprobanteTipoCambio)."', 
							'".mysql_real_escape_string($comprobanteMoneda)."', 
							'".mysql_real_escape_string($comprobanteSubtotal)."', 
							'".mysql_real_escape_string($comprobanteTotal)."', 
							'".mysql_real_escape_string($comprobanteTipoComp)."', 
							'".mysql_real_escape_string($comprobanteExpedicion)."');";
							//exit($query);
	return($cnn->insertar($query));	
}
/*
function Add_new_comprobacion($mi_tramite, $tramite, $anticipo_comprobado, $amex_comprobado, $mnt_reembolso, $mnt_descuento, $total, $Dpto, $refacturar, $observ) {

    $anticipo_comprobado = str_replace(",", "", $anticipo_comprobado);
    $amex_comprobado = str_replace(",", "", $amex_comprobado);
    $mnt_reembolso = str_replace(",", "", $mnt_reembolso);
    $mnt_descuento = str_replace(",", "", $mnt_descuento);
    $total = str_replace(",", "", $total);
 
    $cnn = new conexion();
    $query = sprintf(
                    "insert into comprobaciones (
                        co_id, 
                        co_anticipo_comprobado,
                        co_amex_comprobado,
                        co_mnt_reembolso,
                        co_mnt_descuento,
                        co_total,
                        co_tramite,
						co_mi_tramite,
                        co_fecha_registro,
                        co_cc_clave,
                        co_refacturar,
                        co_status_erp,
                        co_observaciones
                    ) VALUES(
                        default,
                        %s,
                        %s,            
                        %s,
                        %s,
                        %s,
                        %s,
                        %s,
                        now(),
                        %s,
                        %s,
                        '0',
                        '%s'
                    )
        ",
                    $anticipo_comprobado,
                    $amex_comprobado,                    
                    $mnt_reembolso,
                    $mnt_descuento,
                    $total,
                    $tramite,
                    $mi_tramite,
                    $Dpto,
                    $refacturar,
                    $observ
    );
    $id_comprobacion = $cnn->insertar($query);
    return ($id_comprobacion);
}*/

function Get_Dato($nombre, $tipo) {
    switch ($tipo) {
        case 1:
            $tabla = "solicitud_anticipo";
            break;
        case 2:
            $tabla = "comprobacionesamex";
            break;
        case 3:
            $tabla = "solicitud_viaje";
            break;
        case 4:
            $tabla = "comprobaciones";
            break;
        case 5:
            $tabla = "solicitud_invitacion";
            break;
    }

    $cnn = new conexion();
    $query = sprintf("select * from %s", $tabla);
    $rst = $cnn->consultar($query);
    return(mysql_result($rst, 0, $nombre));
}

function actualizaComprobacion($tramite, $subtotal, $iva, $total, $excedente, $referencia, $Dpto, $tipo, $tarjeta) {

    $subtotal = str_replace(",", "", $subtotal);
    $iva = str_replace(",", "", $iva);
    $excedente = str_replace(",", "", $excedente);
    $total = str_replace(",", "", $total);

    if ($total != 0 || $subtotal != 0 || $iva != 0 || $total != "NaN" || $subtotal != "NaN" || $iva != "NaN") {
        $campos = "";
        $parametros = "";
        if (!empty($subtotal)) {

            $co_subtotal = "co_subtotal=" . $subtotal;
            $campos.=$co_subtotal;
        }
        if (!empty($iva)) {

            $co_iva = "co_iva=" . $iva;
            $campos.=$co_iva;
        }
        if (!empty($total)) {

            $co_total = "co_total=" . $total;
            $campos.=$co_total;
        }
        if (!empty($referencia)) {

            $co_internal_order = "co_internal_order" . $referencia;
            $co_referencia = ", co_motivo" . $referencia;
            $campos.=$co_internal_order . $co_referencia;
        }

        //Parametros
        $parametros = " co_mi_tramite=" . $tramite;

        if ($tipo != 2) {
            $tabla = "comprobaciones";
        } else {
            $tabla = "comprobacionesamex";
        }

        if (!empty($campos)) {
            $query = sprintf("update %s set %s where %s", $tabla, $campos, $parametros);
        }
    }
}

function updateComprobacion($idTramite,$centroCosto,$observ,$observEd,$totalComprobaciones,$total_anticipo,$anticipoCompAutBMW,$personalDescontar,$amexCompAutBMW,$efectivoCompAutBMW,$montoDescontar,$montoReembolsar,$co_gasolina,$fecha_inicial,$fecha_final,$motivo_gasolina,$co_tipo_auto,$co_modelo_auto,$co_kilomettraje,$co_monto_gasolina,$co_ruta,$amexExterno) {
	$totalComprobaciones = floatval(str_replace(",", "", $totalComprobaciones));
    $anticipoCompAutBMW = floatval(str_replace(",", "", $anticipoCompAutBMW));
    $personalDescontar = floatval(str_replace(",", "", $personalDescontar));
    $amexCompAutBMW = floatval(str_replace(",", "", $amexCompAutBMW));
    $efectivoCompAutBMW = floatval(str_replace(",", "", $efectivoCompAutBMW));
    $montoDescontar = floatval(str_replace(",", "", $montoDescontar));
    $montoReembolsar = floatval(str_replace(",", "", $montoReembolsar));
    $total_anticipo = floatval(str_replace(",", "", $total_anticipo));     
	$amexExterno = floatval(str_replace(",", "", $amexExterno));     
	
	if($fecha_inicial!= '00000000'){	
		$date = explode("/", $fecha_inicial);
		if (count($date) != 3) {
			return "";
		}
		$fecha_inicial = $date[2] . "" . $date[1] . "" . $date[0];
	}
	
	if($fecha_final!='00000000'){	
		   $date = explode("/", $fecha_final);
		if (count($date) != 3) {
			return "";
		}
		$fecha_final = $date[2] . "" . $date[1] . "" . $date[0];
	}
		
    $cnn = new conexion();
    $query = sprintf("UPDATE comprobaciones
					SET co_anticipo_comprobado = '%s',
					  co_amex_comprobado = '%s',
					  co_mnt_reembolso = '%s',
					  co_mnt_descuento = '%s',
					  co_total = '%s',
					  co_cc_clave = '%s',
					  co_observaciones = '%s',
    				  co_observaciones_edicion = '%s',
					  co_anticipo_viaje = '%s',
					  co_personal_descuento = '%s',
					  co_efectivo_comprobado = '%s',
					  co_gasolina = '%s',
					  co_fecha_inicial_gasolina = '%s',
					  co_fecha_final_gasolina = '%s',
					  co_motivo_gasolina = '%s',
					  co_tipo_auto = '%s',
					  co_modelo_auto = '%s',
					  co_kilometraje = '%s',
					  co_monto_gasolina = '%s',
					  co_ruta = '%s',
					  co_amex_externo = '%s'
					WHERE co_mi_tramite = '%s'",
                    $anticipoCompAutBMW,
                    $amexCompAutBMW,                    
                    $montoReembolsar,
                    $montoDescontar,
                    $totalComprobaciones,
                    $centroCosto,
                    $observ,
    				$observEd,
					$total_anticipo,
                    $personalDescontar,
					$efectivoCompAutBMW,
					$co_gasolina,
					$fecha_inicial,
					$fecha_final,
					$motivo_gasolina,
					$co_tipo_auto,
					$co_modelo_auto,
					$co_kilomettraje,
					$co_monto_gasolina,
					$co_ruta,
					$amexExterno,
    				$idTramite);		
	$cnn->ejecutar($query);	
	$query = sprintf("select co_id from comprobaciones where co_mi_tramite='%s'", $idTramite);
	$rst = $cnn->consultar($query);
	return(mysql_result($rst, 0, "co_id"));
}

function comprobacionSolicitud($idsolicitud, $monto) {

    $cnn = new conexion();
    $query = sprintf("select s.* from solicitud_anticipo as s inner join tramites as t on (t.t_id=s.sa_tramite) where s.sa_tramite=%s and t.t_comprobado=0", $idsolicitud);
    $rst = $cnn->consultar($query);
    $fila = mysql_fetch_assoc($rst);
    $rows = mysql_num_rows($rst);

    $anticipo = $fila['sa_anticipo'];

    if ($rows > 0) {
        $cnn = new conexion();
        $query = sprintf("update tramites set t_cerrado=true,  t_fecha_cierre =now(), t_comprobado=1 where t_id=%s", $idsolicitud);
        $cnn->insertar($query);
    }
}

function comprobacionParcial($idsolicitud, $monto) {

    $cnn = new conexion();
    $query = sprintf("select s.sa_anticipo,s.sa_descuento from solicitud_anticipo as s inner join tramites as t on (t.t_id=s.sa_tramite) where s.sa_tramite=%s and t.t_comprobado=0", $idsolicitud);
    $rst = $cnn->consultar($query);
    $fila = mysql_fetch_assoc($rst);
    $descuento = $fila['sa_descuento']; //monto restante a comprobar
    $anticipo = $fila['sa_anticipo']; // monto solicitado

    if ($descuento != 0) {
        $cnn = new conexion();
        $query = sprintf("update solicitud_anticipo set sa_descuento=(%s-%s) where sa_tramite=%s", $anticipo, $monto, $idsolicitud);
        $cnn->insertar($query);
    } else {
        $cnn = new conexion();
        $query = sprintf("update tramites set t_cerrado=true,  t_fecha_cierre =now(), t_comprobado=true where t_id=%s", $idsolicitud);
        $cnn->insertar($query);
    }
}

function existe_substr($cadena,$substr,$separador){
	$aux2 = false;
	$token = strtok($cadena,$separador);
	while($token != false){
		if($token == $substr){
			$aux2 = true;
		}
		$token = strtok($separador);
	}
	return $aux2;
}

function obtener_ruta_de_autorizacion_de_comprobacion_invitacion($idComprobacion){
	$t_ruta_autorizacion = "";
	
	$id_director_de_area = "";
	$id_director_general = "";
	$id_controlling = "";
	$id_finanzas = "";
	
	$dir_area = true;
	$dir_gral = true;
	$controlling = true;
	$finanzas = true;
	
	//Obtenemos el id del tramite de la comprobacion de invitacion que se esta comprobando, no el id del tramite de la comprobacion.
	$compInv = new Comprobacion();
	$compInv->Load_Comprobacion_Invitacion($idComprobacion);
	$id_tramite = $compInv->Get_dato("co_mi_tramite");
	
	//El primer autorizador es el "Gerente de area",
	//osea el responsable del centro de costos de la comprobacion de invitacion.
	$cc_id = $compInv->Get_dato("co_cc_clave");
	
	$cc = new CentroCosto();
	$cc->Load_CeCo($cc_id);
	$id_gerente_de_area = $cc->Get_Dato("cc_responsable");

/*	
	//Se checa si el usuario es de "BMW Financial Services".
	$tramite = new Tramite();
	$tramite->Load_Tramite($id_tramite);
	$id_iniciador = $tramite->Get_dato("t_iniciador");
	
	$usuario = new Usuario();
	$usuario->Load_Usuario_By_ID($id_iniciador);
	$usu_empresa = $usuario->Get_dato("u_empresa");
	if($usu_empresa == "2"){
		$dir_gral = true;
	}else{
		//Se checa si existe un invitado de tipo "Gobierno".
		$comensales = new Comensales();
		$comensales_array = $comensales->Load_comensales_by_tramite($id_tramite);
		$no_invitados = count($comensales_array);
		for($i=0;$i<$no_invitados;$i++){
			if($comensales_array[$i]['dci_tipo_invitado'] == "Gobierno"){
				$dir_gral = true;
				break;
			}
		}
		if($dir_gral == true){
		}else{
			//Se checa si el monto solicitado por persona es mayor a 50 EUR.
			$si_monto_pesos = $sol_inv->Get_dato("si_monto_pesos");
			
			$divisa = new Divisa();
			$divisa->Load_data("3"); //div_id de EUR = 3
			$tasa_eur = $divisa->Get_dato("div_tasa");
			
			$monto_x_persona = $si_monto_pesos/$tasa_eur/$no_invitados;
			if($monto_x_persona > 50){
				$dir_gral = true;
			}
		}
	}
*/
	$usuario = new Usuario();
	
	//El segundo autorizador es el "Director de area".
	if($dir_area == true){
		$id_director_de_area = $cc->Get_Dato("cc_director_de_area");
	}
	
	$agrup_usu = new AgrupacionUsuarios();
	//El cuarto autorizador es el "Controlling".
	if($controlling == true){
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre('Controlling');
		$id_controlling = $agrup_usu->Get_dato("au_id");
	}
	//El quinto autorizador es el "Finanzas".
	if($finanzas == true){
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre('Finanzas');
		$id_finanzas = $agrup_usu->Get_dato("au_id");
	}
	
	//Se arma la ruta de autorizacion
	$t_ruta_autorizacion = $id_gerente_de_area;
	if($dir_area == true && $id_director_de_area != ""){
		if(existe_substr($t_ruta_autorizacion,$id_director_de_area,"|") == false){
			$t_ruta_autorizacion .= "|".$id_director_de_area;
		}
	}
	if($dir_gral == true && $id_director_general != ""){
		if(existe_substr($t_ruta_autorizacion,$id_director_general,"|") == false){
			$t_ruta_autorizacion .= "|".$id_director_general;
		}
	}
	if($controlling == true && $id_controlling != ""){
		if(existe_substr($t_ruta_autorizacion,$id_controlling,"|") == false){
			$t_ruta_autorizacion .= "|".$id_controlling;
		}
	}
	if($finanzas == true && $id_finanzas != ""){
		if(existe_substr($t_ruta_autorizacion,$id_finanzas,"|") == false){
			$t_ruta_autorizacion .= "|".$id_finanzas;
		}
	}
	
	return $t_ruta_autorizacion;
}

function get_nombre_de_agrupacion_usuarios_if_exist($id){
	$agrup_usu = new AgrupacionUsuarios();
	if($agrup_usu->Load_Grupo_de_Usuario_By_ID($id)){
		$agrup_nombre = $agrup_usu->Get_dato("au_nombre");
	}else{
		$agrup_nombre = "";
	}
	
	return $agrup_nombre;
}

/**
 * Funciones para ingresar excepciones a nivel viaje
 */
function addExcepcionAlimento($mensajeA,$diferenciaA,$comprobacion_id){
	if(($mensajeA != "") && ($diferenciaA != 0)){
		Add_excepcion($mensajeA, $diferenciaA, '0', $comprobacion_id, '0');
	}
	
}
function addExcepcionHospedaje($mensajeH,$diferenciaH,$comprobacion_id){
	if(($mensajeH != "") && ($diferenciaH != 0)){
		Add_excepcion($mensajeH, $diferenciaH, '0', $comprobacion_id, '0');
	}
	
}
function addExcepcionLavanderia($mensajeL,$diferenciaL,$comprobacion_id){
	if(($mensajeL != "") && ($diferenciaL != 0)){
		Add_excepcion($mensajeL, $diferenciaL, '0', $comprobacion_id, '0');
	}
	
}
/**
 * Funcion para ingresar la excepcion a nivel detalle
 */
function ingresarExcepcionDetalle($conceptoId,$excepcion,$diferencia,$detalleComprobacionId,$comprobacionId){
	$concepto = new Concepto();
	if($excepcion != ""){
		if($conceptoId == $concepto->GetIdAlimentos()){
			Add_excepcion($excepcion, $diferencia, '0', $comprobacionId, $detalleComprobacionId);
		}else if($conceptoId == $concepto->GetIdHotel()){
			Add_excepcion($excepcion, $diferencia, '0', $comprobacionId, $detalleComprobacionId);
		}else if($conceptoId == $concepto->GetIdLavanderia()){
			Add_excepcion($excepcion, $diferencia, '0', $comprobacionId, $detalleComprobacionId);
		}			
	}	
}

/*
 *  Función para actoaluzar el campo de Comprobación en la tabla amex
 */
function setComprobacionid($idamex, $idComprobacion){
	$cnn = new conexion();
	$query = sprintf("UPDATE amex SET comprobacion_id = '%s' WHERE idamex = '%s'", $idComprobacion, $idamex);
	error_log($query);
	$cnn->ejecutar($query);
}
?>
