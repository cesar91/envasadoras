<?php

class C_SV extends conexion {

    protected $id_viaje;
    protected $id_itinerario;
    protected $id_invitacion;
    protected $rst_viaje;
	protected $rst_itinerario;

    function __construct() {
        parent::__construct();
        $this->id_viaje = "";
        $this->id_itinerario = "";
        $this->id_invitacion = "";
    }
	
    public function Load_Itinerario($id) {
        $query = sprintf("select * from sv_itinerario where svi_id=%s", $id);
        $this->rst_itinerario = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $iiii = $this->Get_dato("svi_id");
        }
    }
	function Load_region_by_nombre($nombre){
		$query=sprintf("select * from cat_regionesbmw where re_nombre='%s'",$nombre);
		$this->rstd=mysql_fetch_assoc(parent::consultar($query));
	}
	public function Get_data($nombre){
		return($this->rstd[$nombre]);
	}
    /**
     * Obtiene el dato de un itinerario
     *
     * @param text $nombre
     * @return text
     */
    public function Get_dato_itinerario($nombre) {
        return(mysql_result($this->rst_itinerario, 0, $nombre));
    }
	
    /**
     * FunciÃ³n que registra una Solicitud de Viaje
     */
    public function Add($sTipo_viaje, $sMotivo, $sObservaciones, $idTramite, $FechaMySQL, $sCat_cecos, $total_solicitud,$check,$total_anticipo) {
        $query = sprintf("INSERT INTO solicitud_viaje
					  (
					  	sv_id,
						sv_viaje,
						sv_motivo,
						sv_observaciones,
						sv_tramite,
					  	sv_fecha_registro,
						sv_ceco_paga,
						sv_total,
						sv_anticipo,
						sv_total_anticipo
					  )
					  VALUES(
					  	default,
					  	'%s',
					  	'%s',
						'%s',
						'%s',
						'%s',
						'%s',
                        '%s',
						'%s',
						'%s'
					  )	
                        ", $sTipo_viaje, $sMotivo, $sObservaciones, $idTramite, $FechaMySQL, $sCat_cecos, $total_solicitud,$check,$total_anticipo
        );
		//error_log($query);
		
        $queryEtiqueta = sprintf("UPDATE tramites SET t_etiqueta='%s' WHERE t_id ='%s'",$sMotivo, $idTramite);
        //error_log($queryEtiqueta);
        parent::ejecutar($queryEtiqueta);
        
        $this->id_viaje = parent::insertar($query);
		//error_log("------ ".$this->id_viaje);
        return($this->id_viaje);
    }

    // Funcion que registra una solicitud de amex
    public function Add_amex($total_dias, $sObser, $motivo, $cecos_cargado, $cecos_beneficiado, $fecha_viaje, $id_tramite) {
        $query = sprintf("INSERT INTO solicitud_amex
					  (
					  	sa_id,
                        sa_dias_viaje,
					  	sa_observaciones,
                        sa_motivo,
					  	sa_fecha_registro,
                        sa_ceco_paga,
                        sa_ceco_benef,
                        sa_fecha_viaje,
					  	sa_tramite
 					  )
					  VALUES
                      (
					  	default,
					  	'%s',
                        '%s',
                        '%s',
        			  	now(),
                        '%s',
                        '%s',                        
                        '%s',    
					  	'%s'
					  )
                    ", $total_dias, $sObser, $motivo, $cecos_cargado, $cecos_beneficiado, $fecha_viaje, $id_tramite
        );
        $this->id_viaje = parent::insertar($query);
        return($this->id_viaje);
    }

    // Funcion que registra una solicitud de invitaciÃ³n
    public function Add_invitacion($motivo, $numInvitados, $monto, $total_pesos, $divisa, $cecos_invitacion, $tramite, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechainvit, $inv_lugar, $inv_hubo_exedente) {
        $query = sprintf("INSERT INTO solicitud_invitacion(
					  							si_id,
                                                si_motivo,
					  							si_num_invitados,
					  							si_monto,
                                                si_monto_pesos,
                                                si_fecha_registro,
                                                si_divisa,
                                                si_ceco,
                                                si_tramite,
												si_ciudad,
					  							si_observaciones,
        										si_observaciones_edicion,
					  							si_fecha_invitacion,
					  							si_lugar,
												si_hubo_exedente)
					  	VALUES(default,
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
                                                '%s',
                                                '%s');
                    ", $motivo, $numInvitados, $monto, $total_pesos, $divisa, $cecos_invitacion, $tramite, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechainvit, $inv_lugar, $inv_hubo_exedente
        );
        //error_log($query);
        $this->id_viaje = parent::insertar($query);
        return($this->id_viaje);
    }

    
    public function Edit_invitacion($motivo, $numInvitados, $monto, $total_pesos, $divisa, $cecos_invitacion, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechainvit, $inv_lugar,$inv_hubo_exedente,$tramite) {
    	$query = sprintf("UPDATE solicitud_invitacion SET
                                                    si_motivo='%s',
    					  							si_num_invitados='%s',
    					  							si_monto='%s',
                                                    si_monto_pesos='%s',
                                                    si_fecha_registro=now(),
                                                    si_divisa='%s',
                                                    si_ceco='%s',
    												si_ciudad='%s',
    					  							si_observaciones='%s',
    												si_observaciones_edicion='%s',
    					  							si_fecha_invitacion='%s',
    					  							si_lugar='%s',
    					  							si_hubo_exedente='%s'
													where si_tramite='%s';
                        ",$motivo, $numInvitados, $monto, $total_pesos, $divisa, $cecos_invitacion, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechainvit, $inv_lugar,$inv_hubo_exedente,$tramite
    	);
    	//error_log($query);
    	parent::ejecutar($query);
    	
    	$queryEtiqueta = sprintf("UPDATE tramites SET t_etiqueta='%s' WHERE t_id ='%s'",$motivo, $tramite);
    	//error_log($query);
    	parent::ejecutar($queryEtiqueta);
    	
    	$query="select si_id from solicitud_invitacion where si_tramite='".$tramite."'";
    	$this->rst_viaje = parent::consultar($query);
    	if (parent::get_rows() > 0) {
    		$this->id_viaje = $this->Get_dato("si_id");
    	}
    	return($this->id_viaje);
    }
    
	//===================================================================
							      //($sDestino, $sAgencia_hotel, $sAgencia_auto, $sFecha_regreso, $sFecha_salida, $sHora_regreso, $sHora_salida, $sRenta_hotel, $sKilometraje, $sOrigen, $sRegion, $sRenta_auto, $sMedio_transporte, $idSolViaje, $sCosto_dia, $sDias_renta, $sTipo_divisa, $sEmpresa_auto, $sTipo_auto, $sMontoA_pesos, $sTotal_auto );
	public function Add_Itinerario($sDestino, $sAgencia_hotel, $sAgencia_auto, $sFecha_regreso, $sFecha_salida, $sHora_regreso, $sHora_salida, $sRenta_hotel, $sKilometraje, $sOrigen, $sRegion, $sRenta_auto, $sMedio_transporte,$sTipo_transporte, $idSolViaje, $sCosto_dia, $sDias_renta, $sTipo_divisa, $sEmpresa_auto, $sTipo_auto, $sMontoA_pesos, $sTotal_auto, $sZona_geografica,$noDiasViaje) {
		//error_log("sAgencia_autodentro-----------".$sAgencia_auto);
		//error_log("TIPO_VIAJE".$sTipo_transporte);
		$date = explode("/", $sFecha_regreso);
        if (count($date) != 3) {
            return "";
        }
        $sFecha_regreso = $date[2] . "-" . $date[1] . "-" . $date[0];
        $date = explode("/", $sFecha_salida);
        if (count($date) != 3) {
            return "";
        }
        $sFecha_salida = $date[2] . "-" . $date[1] . "-" . $date[0];

        $query = sprintf("
								INSERT INTO sv_itinerario
								(
									
									svi_id,
									svi_destino,
									svi_hotel_agencia,
									svi_renta_auto_agencia,
									svi_fecha_llegada,
									svi_fecha_salida,
									svi_horario_llegada,
									svi_horario_salida,
									svi_hotel,
									svi_kilometraje,
									svi_origen,
									svi_region,
									svi_renta_auto,
									svi_tipo_transporte,
									svi_solicitud,
									svi_costo_auto,
									svi_dias_renta,
									svi_divisa_auto,
									svi_empresa_auto,
									svi_tipo_auto,
									svi_total_pesos_auto,
									svi_total_auto,
									svi_tipo_viaje,
									svi_medio,
        							svi_dias_viaje

								)
								VALUES(
									default,
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
        							'%s'
								)
							
									",$sDestino, $sAgencia_hotel, $sAgencia_auto, $sFecha_regreso, $sFecha_salida, $sHora_regreso, $sHora_salida, $sRenta_hotel, $sKilometraje, $sOrigen, $sRegion, $sRenta_auto, $sMedio_transporte, $idSolViaje, $sCosto_dia, $sDias_renta, $sTipo_divisa, $sEmpresa_auto, $sTipo_auto, $sMontoA_pesos, $sTotal_auto, $sZona_geografica,$sTipo_transporte,$noDiasViaje
        );
		//error_log($query);
        //RETORNAMOS EL ID DEL ITINERARIO
        return(parent::insertar($query));
	}	
    //=================== HOTELES =======================================
    //Funcion que agregará hoteles en la creación de una solicitud de viaje
	public function Add_Hoteles($sDivisa, $idItinerario, $sComentario, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal, $sBandera ) {
		$date = explode("/", $sLlegada);
        if (count($date) != 3) {
            return "";
        }
        $sLlegada = $date[2] . "-" . $date[1] . "-" . $date[0];
        $date = explode("/", $sSalida);
        if (count($date) != 3) {
            return "";
        }
        $sSalida = $date[2] . "-" . $date[1] . "-" . $date[0];
		$cnn = new conexion();
		$query_divisa = sprintf("SELECT div_id FROM divisa WHERE div_nombre = '%s'", $sDivisa);
		//error_log($query_divisa);
		$rst2 = $cnn->consultar($query_divisa);
		$i=0;
		while ($filaa = mysql_fetch_array($rst2)){
			$data=$filaa['div_id'];
		}
        $query = sprintf("
								INSERT INTO hotel
								(
									svi_id,
									h_hotel_id,
									div_id,
									h_comentarios,
									h_costo_noche,
									h_nombre_hotel,
									h_iva,
									h_fecha_llegada,
									h_total_pesos,
									h_ciudad,
									h_noches,
									h_no_reservacion,
									h_fecha_salida,
									h_subtotal,
									h_total,
        							h_cotizacion_inicial
								)
								VALUES(
									'%s',
									default,
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
        							 %s
								)
							
								",$idItinerario, $data, $sComentario, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal,$sBandera 
        );
		//error_log($query);
        //RETORNAMOS EL ID DEL ITINERARIO
        return(parent::insertar($query));
	}
	
	//Función que agregará los hoteles en la agencia / Escenarios: Primera cotizacion - pin-pong Agencia Empleado
	public function addHotelesAE($svi_id,$divisaHotel, $sComentario, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal,$sBandera){
		$TotalMXN=(float)$sMontoH_pesos;
		if($sNo_reservacion == ""){
			$NReservacion = 0;
		}else{
			$NReservacion=$sNo_reservacion;
		}
		$idHotel=0;
		$query=sprintf("SELECT h_hotel_id FROM hotel WHERE svi_id=%s AND h_total_pesos=%s AND h_no_reservacion=%s",$svi_id,$TotalMXN,$NReservacion);
		//error_log($query);
		$rstAE= parent::consultar($query);
		while ($aux = mysql_fetch_assoc($rstAE)) {
			$idHotel=$aux['h_hotel_id'];
		}			
		if($idHotel == ""){
			$idHotel=0;
		}
		//error_log(".......".$idHotel);
		if($idHotel == 0){
			$this->addHoteles($svi_id, $divisaHotel, $sComentario, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal,$sBandera);			
		}
	}
	
	//Función que agregará los hoteles en la agencia / Escenarios: Primera cotizacion - pin-pong Agencia Empleado
	public function addHoteles($svi_id,$divisaHotel, $sComentario, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal,$sBandera){
		$query = sprintf("
				INSERT INTO hotel
				(
				svi_id,
				h_hotel_id,
				div_id,
				h_comentarios,
				h_costo_noche,
				h_nombre_hotel,
				h_iva,
				h_fecha_llegada,
				h_total_pesos,
				h_ciudad,
				h_noches,
				h_no_reservacion,
				h_fecha_salida,
				h_subtotal,
				h_total,
				h_cotizacion_inicial
		)
				VALUES(
				'%s',
				default,
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				 %s
		)
				",$svi_id,$divisaHotel, $sComentario, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal,$sBandera
		);
		//error_log($query);
		return(parent::insertar($query));
	}
	//===================================================================
	
	//=================== Autos =======================================
	//funcion que ingresará un auto en la etapa de agencias, Escenarios: Primera cotización / pin-pong Agencia
	public function addAutoAE($empresaAuto,$tipoAuto,$diasRenta,$costoDia,$totalAuto,$montoPesos,$divisaAuto,$svi_id){
		$query = sprintf("update sv_itinerario set
				svi_empresa_auto='%s',
				svi_tipo_auto='%s',
				svi_costo_auto='%s',
				svi_dias_renta='%s',
				svi_total_auto='%s',
				svi_total_pesos_auto='%s',
				svi_divisa_auto='%s'
				where svi_id='%s'",
				$empresaAuto,
				$tipoAuto,
				$costoDia,
				$diasRenta,
				$totalAuto,
				$montoPesos,
				$divisaAuto,
				$svi_id);
		//error_log($query);
		return(parent::insertar($query));
	}

	/**
     * Registramos un itinerario a partir de un viaje previamente registrado
     * 	
     */
    /*public function Add_Itinerario($sOrigen, $sDestino, $sFecha, $sSelect_medio_transporte, $sSelect_hora_salida, $nKilometraje, $sHospedaje, $svi_renta_auto, $svi_hotel_agencia, $sNoches, $sDias, $sTipoViaje, $nNameHotel, $sFechaLLegada, $sHorarioLlegada) {

        $date = explode("/", $sFecha);

        if (count($date) != 3) {
            return "";
        }

        $sFecha = $date[2] . "-" . $date[1] . "-" . $date[0];

        $date = explode("/", $sFechaLLegada);

        if (count($date) != 3) {
            return "";
        }

        $sFechaLLegada = $date[2] . "-" . $date[1] . "-" . $date[0];

        $query = sprintf("
								INSERT INTO sv_itinerario
								(
									svi_id,
									svi_origen,
									svi_destino,
									svi_fecha_salida,
									svi_medio,
									svi_horario,
									svi_kilometraje,
									svi_hotel,
									svi_renta_auto,
									svi_hotel_agencia,
									svi_noches_hospedaje,
									svi_tipo_viaje,								
									svi_solicitud,
									svi_dias_viaje,
									svi_nombre_hotel,
									svi_fecha_llegada,
									svi_horario_llegada
								)
								VALUES(
									default,
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									 %s,
									 %s,
									 %s,
									 %s,
									 %s,
									'%s',
									 %s,
									 %s,
									'%s',
									'%s',
									'%s'
								)
							
									", trim($sOrigen), trim($sDestino), trim($sFecha), trim($sSelect_medio_transporte), trim($sSelect_hora_salida), trim($nKilometraje), trim($sHospedaje), trim($svi_renta_auto), trim($svi_hotel_agencia), trim($sNoches), trim($sTipoViaje), $this->id_viaje, trim($sDias), trim($nNameHotel), trim($sFechaLLegada), trim($sHorarioLlegada)
        );
        //error_log($query);
        //RETORNAMOS EL ID DEL ITINERARIO
        return(parent::insertar($query));
    }*/

    //
    //  Guarda el itinerario de solicitudes de amex.
    //
	public function Add_Itinerario_sa($sOrigen, $sDestino, $sFecha_salida, $sFecha_llegada, $sSelect_hora_salida, $tipo_viaje) {

        $sFecha_salida_MySQL = fecha_to_mysql($sFecha_salida);
        $sFecha_llegada_MySQL = fecha_to_mysql($sFecha_llegada);

        $query = sprintf("
                            INSERT INTO sa_itinerario
                            (
                                sai_id,
                                sai_origen,
                                sai_destino,
                                sai_fecha_salida,
                                sai_fecha_llegada,
                                sai_horario,
                                sai_tipo_viaje,
                                sai_solicitud
                            )
                            VALUES(
                                default,
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s'
                            )

                                ", $sOrigen, $sDestino, $sFecha_salida_MySQL, "NULL", // Para el caso de Marti solo se maneja una fecha. AP
                $sSelect_hora_salida, $tipo_viaje, $this->id_viaje
        );

        //error_log($query);
        //RETORNAMOS EL ID DEL ITINERARIO
        return(parent::insertar($query));
    }

    /* Modificacion de Itinerarios
     * *
     */

    public function Edit_Itinerario_amex($nId_itinerario, $sTipoViaje, $nNumvuelo, $sAerolinea, $nCostoViaje, $nIva, $nTua, $nOtroImp, $sSelect_hora_salida, $HoraSalida, $apartadoBoleto) {
        $query = sprintf("update sa_itinerario set
                           sai_tipo_viaje='%s',
                           sai_numero_transporte=%s,
                           sai_aerolinea='%s',
                           sai_monto_vuelo=%s,
                           sai_iva=%s,
                           sai_tua=%s,
                           sai_otrocargo=%s,
                           sai_horario='%s',
                           sai_hora_salida='%s',
                           sai_apartado_boleto='%s'
                           where sai_id=%s ", $sTipoViaje, $nNumvuelo, $sAerolinea, $nCostoViaje, $nIva, $nTua, $nOtroImp, $sSelect_hora_salida, $HoraSalida, $apartadoBoleto, $nId_itinerario);
        //error_log($query);
        return(parent::insertar($query));
    }

    /* Modificacion de Itinerarios Viaje ------------ AVION
     * *
     */

	 public function Edit_Itinerario_Viaje($nId_itinerario,$sTipoViaje,$sAerolinea,$nCostoViaje,$nCostoViajeTotal,$nCostoViajeTotalCotizacion,$nIva,$nTua,$sfechaSalida,$sfechaLlegada,$tipo_aerolinea,$cotizado) {
        $query = sprintf("update sv_itinerario set
                           svi_tipo_viaje='%s',
                           svi_aerolinea='%s',
                           svi_monto_vuelo=%s,
                           svi_monto_vuelo_total=%s,
                           svi_monto_vuelo_total_cotizacion=%s,
                           svi_iva=%s,
                           svi_tua=%s,
						   svi_fecha_salida='%s',
						   svi_fecha_llegada='%s',
						   svi_tipo_aerolinea='%s',
						   svi_cotizado=%s
                           where svi_id=%s ", $sTipoViaje, $sAerolinea, $nCostoViaje, $nCostoViajeTotal, $nCostoViajeTotalCotizacion, $nIva, $nTua, $sfechaSalida, $sfechaLlegada, $tipo_aerolinea, $cotizado, $nId_itinerario);
        //error_log($query);
        return(parent::insertar($query));
    }

    /* Modificacion de Itinerarios Viaje ----------- HOTEL
     * *
     */

	 public function Edit_Itinerario_Viaje_Hotel($nId_itinerario, $ciudad_hotel, $nombre_hotel, $noches_hotel, $caract_hab_hotel, $subtotal_hotel, $otros_cargos_hotel, $no_reservacion_hotel, $hora_llegada_hotel, $hora_salida_hotel, $costo_x_noche) {
        $query = sprintf("update sv_itinerario set
                           svi_ciudad='%s',
                           svi_nombre_hotel='%s',
                           svi_noches_hospedaje=%s,
						   svi_caracteristicas_habitacion='%s',
						   svi_costo_hotel='%s',
						   svi_otrocargo='%s',
						   svi_hotel_reservacion='%s',
						   svi_hotel_llegada='%s',
						   svi_hotel_salida='%s',
						   svi_costo_noche='%s'
                           where svi_id=%s ", $ciudad_hotel, $nombre_hotel, $noches_hotel, $caract_hab_hotel, $subtotal_hotel, $otros_cargos_hotel, $no_reservacion_hotel, $hora_llegada_hotel, $hora_salida_hotel, $costo_x_noche, $nId_itinerario);
        //error_log($query);
        return(parent::insertar($query));
    }

    /* Modificacion de Itinerarios Viaje ----------- AUTO
     * *
     */

	 public function Edit_Itinerario_Viaje_Auto($nId_itinerario, $empresa_auto, $tipo_de_auto, $dias_de_renta_auto, $costo_x_dia_auto, $subtotal_auto) {
        $query = sprintf("update sv_itinerario set
                           svi_empresa_auto='%s',
                           svi_tipo_auto='%s',
                           svi_dias_renta='%s',
                           svi_costo_auto='%s',
                           svi_total_auto='%s'
                           where svi_id=%s ", $empresa_auto, $tipo_de_auto, $dias_de_renta_auto, $costo_x_dia_auto, $subtotal_auto, $nId_itinerario);
        //error_log($query);
        return(parent::insertar($query));
    }

    /*
     *  Modifica el campo de observaciones
     */

    public function Modifica_Observaciones($tramite_id, $observaciones, $flujo) {
/*
        if ($flujo == FLUJO_ANTICIPO) {
            $query = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = %s", $observaciones, $tramite_id);
        }

        if ($flujo == FLUJO_AMEX) {
            $query = sprintf("UPDATE solicitud_amex SET sa_observaciones = '%s' WHERE sa_tramite = %s", $observaciones, $tramite_id);
        }
*/
        if ($flujo == FLUJO_SOLICITUD_INVITACION) {
            $query = sprintf("UPDATE solicitud_invitacion SET si_observaciones = '%s' WHERE si_tramite = %s", $observaciones, $tramite_id);
        }
/*
        if ($flujo == FLUJO_COMPROBACION) {
            $query = sprintf("UPDATE comprobaciones SET co_observaciones = '%s' WHERE co_tramite = %s", $observaciones, $tramite_id);
        }
*/


        //error_log($flujo."---".$query);
        return(parent::insertar($query));
    }

    /**
     * Carga los datos de una solicitud de viaje
     *
     * @param integer $id
     */
    public function Load_Solicitud_tramite($id) {
		$query = sprintf("SELECT * FROM solicitud_viaje WHERE sv_tramite=%s", $id);
		//error_log($query);
        $this->rst_viaje = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $this->id_viaje = $this->Get_dato("sv_id");
        }
    }

    public function Load_Solicitud_Amex_Tramite($id) {
        $query = sprintf("SELECT * FROM solicitud_amex WHERE sa_tramite=%s", $id);
        $this->rst_viaje = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $this->id_viaje = $this->Get_dato("sa_id");
        }
    }

    public function Load_Solicitud_Invitacion_Tramite($id) {
        $query = sprintf("SELECT * FROM solicitud_invitacion WHERE si_tramite=%s", $id);
        $this->rst_viaje = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $this->id_invitacion = $this->Get_dato("si_id");
        }
    }

    public function add_conceptos_detalle($IDitinerario,$idTramite,$MontoU,$DivisaU,$MontoTotalU,$MontoTotalMxnU,$DiasConceptoU,$IDconcepto){
		$id_sv = 0;
		
		switch ($DivisaU){
			case "MXN":
				$id_sv = 1;
				break;
			case "USD":
				$id_sv = 2;
				break;
			case "EUR":
				$id_sv = 3;
				break;
			default:
				$id_sv = $DivisaU;
		}
		//error_log("ID divisa: ".$id_sv);
		
        $query = sprintf("
								INSERT INTO sv_conceptos_detalle
								(
									svc_detalle_id,
									svc_detalle_concepto,
									svc_detalle_tramite,
									svc_itinerario,
									svc_detalle_monto_concepto,
									svc_divisa,
									svc_conversion,
									svc_aprobado,
									svc_monto_divisa, 
        							svc_dias_itinerario
								)
								VALUES(
									default,
									'%s',
									'%s',
				                    '%s',
									'%s',
				                    '%s',
				                    '%s',
									'0',
									'%s', 
        							'%s'
								)
			", $IDconcepto, $idTramite, $IDitinerario, $MontoU, $id_sv, $MontoTotalMxnU, $MontoTotalU, $DiasConceptoU
        );
        //error_log($query);
        return(parent::insertar($query));
    }
	//Agrega concepto itinerario
	public function add_concepto_itinerario($IDconcepto,$idConcepto_detalle,$IDitinerario) {

        $query = sprintf("
								INSERT INTO concepto_itinerario
								(
									id_concepto_itinerario,
									svc_detalle_id,
									svi_id
								)
								VALUES(
									default,
									'%s',
				                    '%s'
								)

									", $idConcepto_detalle,$IDitinerario
        );
        return(parent::insertar($query));
    }
    /**
     * Obtiene el dato de una solicitud de viaje
     *
     * @param text $nombre
     * @return text
     */
    public function Get_dato($nombre) {
        return(mysql_result($this->rst_viaje, 0, $nombre));
    }

    public function InsertObservaciones($texto,$t_id,$u_id){
    	$query = sprintf("insert into observaciones(
    			ob_id,
    			ob_texto,
    			ob_fecha,
    			ob_tramite,
    			ob_usuario
    	)values(
    			default,
    			'%s',
    			now(),
    			%s,
    			%s
    	)",
    			$texto,
    			$t_id,
    			$u_id
    	);
    	parent::insertar($query);
    }   

}



function add_detalle_solicitud_invitacion($solicitud_invitacion_origen, $nombre_invitado_solicitud, $puesto_invitado_solicitud, $empresa_invitado_solicitud, $tipo_invitado_solicitud, $tramite_solicitud_invitacion) {
    $cnn = new conexion();
    $query = sprintf("
                            insert into comensales_sol_inv
                            (
                                dci_id,
                                dci_solicitud_inv,
                                dci_nombre_invitado,
                                dci_puesto_invitado,
                                dci_empresa_invitado,
                                dci_tipo_invitado,
                                dci_solicitud
                            )
                            VALUES(
                                default, 
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s'
                            )
                            ", $solicitud_invitacion_origen, $nombre_invitado_solicitud, $puesto_invitado_solicitud, $empresa_invitado_solicitud, $tipo_invitado_solicitud, $tramite_solicitud_invitacion
    );
    //error_log($query);
    return($cnn->insertar($query));
}



?>
