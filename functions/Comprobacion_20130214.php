<?php
class Comprobacion extends conexion {
    
    protected $c_id;
    protected $rst_co;
    protected $aprob;
    protected $dc_id;
    public function __construct(){
        parent::__construct();
        $this->c_id="";
        $this->dc_id="";
    }
    
    /**
     * Cargamos los datos de un tramite
     *
     * @param integer $id
     */
    public function Load_Comprobacion($id){
        $query=sprintf("SELECT * FROM comprobaciones where co_id=%s",$id);
        $this->rst_co=parent::consultar($query);
        if(parent::get_rows()>0){
            $this->c_id=$this->Get_dato("co_id");
        }
    }
    /**
     * Cargamos una comprobacion de invitacion
     */
    public function Load_Comprobacion_Invitacion($id){
        $query=sprintf("SELECT * FROM comprobacion_invitacion where co_id=%s",$id);
        $this->rst_co=parent::consultar($query);
        if(parent::get_rows()>0){
            $this->c_id=$this->Get_dato("co_id");
        }
    }
    /**
     * Cargamos una comprobacion de invitacion
     */
    public function Load_Comprobacion_Invitacion_By_co_mi_tramite($id){
        $query=sprintf("SELECT * FROM comprobacion_invitacion where co_mi_tramite='%s'",$id);
        $this->rst_co=parent::consultar($query);
        if(parent::get_rows()>0){
            $this->c_id=$this->Get_dato("co_id");
        }
    }
    /**
     * Cargamos una comprobacion de viaje
     */
    public function Load_Comprobacion_By_co_mi_tramite($id){
        $query=sprintf("SELECT * FROM comprobaciones where co_mi_tramite='%s'",$id);
        $this->rst_co=parent::consultar($query);
        if(parent::get_rows()>0){
            $this->c_id=$this->Get_dato("co_id");
        }
    }
		public function Load_Tramite($id){
			$query=sprintf("select * from tramites where t_id=%s",$id);
			
			$this->rst_tramite=parent::consultar($query);
			if(parent::get_rows()>0){
				$this->t_id=$this->Get_dato("t_id");
			}
		}    
    /**
     * Regresamos El valor de un campo de la Tabla de un Tramite previamente cargado
     *
     * @param text $nombre
     * @return text
     */
    public function Get_dato($nombre){
        return(mysql_result($this->rst_co,0,$nombre));
    }

    public function Modifica_propietario($partida,$comprobacion,$responsable){		
        $query=sprintf("update detalle_comprobacion set dc_responsable=%s where dc_id=%s and dc_comprobacion=%s",$responsable,$partida,$comprobacion);
        parent::insertar($query);
    }
    
    public function Cancela_Partida($partida,$comprobacion){
        $query=sprintf("update detalle_comprobacion set dc_estatus=0 where dc_id=%s and dc_comprobacion=%s",$partida,$comprobacion);
        parent::insertar($query);
    }
    
    public function Aprueba_Partida($partida,$monto,$iva,$comprobacion,$estatus,$TipoComp){
        $query=sprintf("update detalle_comprobacion set dc_estatus=%s where dc_id=%s and dc_comprobacion=%s;",$estatus,$partida,$comprobacion);
        parent::insertar($query);
    }
    
    // Aprueba por el autorizador el concepto de la comprobacion
    public function Aprueba_Partida_Comprobacion($dc_id){
        $query=sprintf("UPDATE detalle_comprobacion SET dc_total_aprobado = dc_total WHERE dc_id=%s",$dc_id);
        parent::insertar($query);
    }   
    
    // Aprueba por CxP el concepto de la comprobacion
    public function Aprueba_Partida_Comprobacion_CxP($dc_id){
        $query=sprintf("UPDATE detalle_comprobacion SET dc_total_aprobado_cxp = dc_total, dc_estatus=".PARTIDA_COMPROBACION_APROBADA." WHERE dc_id=%s",$dc_id);
        parent::insertar($query);
    }   
    
    // Aprueba por CxP el concepto de la comprobacion
    public function Actualiza_Observaciones_Comprobacion($co_id, $observaciones){
        $query=sprintf("UPDATE comprobaciones SET co_observaciones = '".$observaciones."' WHERE co_id=%s",$co_id);
        parent::insertar($query);
    }                    
	
	public function Crea_Comprobacion_Invitacion($total, $sol_inv, $co_subtotal, $co_iva, $tramite, $centroCosto, $cMotive, $observ){
		$query = sprintf(
						"insert into comprobaciones (
							co_id, 
							co_total,
							co_cancelado,
							co_aceptada,
							co_tramite,
							co_fecha_registro,
							co_subtotal,
							co_iva,
							co_mi_tramite,
							co_cc_clave,
							co_motivo,
							co_tipo,
							co_observaciones
						) VALUES(
							default,
							%s,
							'0',            
							'0',
							%s,
							now(),
							%s,
							%s,
							%s,
							%s,
							'%s',
							'3',
							'%s'
						)",
						$total,
						$sol_inv,
						$co_subtotal,
						$co_iva,
						$tramite,
						$centroCosto,
						$cMotive,
						$observ
		);
		$this->t_id=parent::insertar($query);
		$this->Load_Comprobacion($this->t_id);
		return($this->c_id);
	}
	
	public function Crea_Comprobacion_Invitacion2($total, $sol_inv, $co_subtotal, $co_iva, $tramite, $centroCosto, $cMotive, $cTipo, $observ, $observEdicion, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad){
		$query = sprintf(
						"insert into comprobacion_invitacion (
							co_id, 
							co_total,
							co_tramite,
							co_fecha_registro,
							co_subtotal, 
							co_mi_tramite,
							co_cc_clave,
							co_motivo,
							co_tipo,
							co_observaciones,
							co_observaciones_edicion,
							co_num_invitado, 
							co_lugar,
							co_fecha_invitacion,
							co_hubo_exedente,
							co_ciudad
						) VALUES(
							default,
							%s,
							%s,
							now(),
							%s, 
							%s,
							%s,
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s'
						)",
						$total,
						$sol_inv,
						$co_subtotal, 
						$tramite,
						$centroCosto,
						$cMotive,
						$cTipo,
						$observ, 
						$observEdicion, 
						$tot_invitados, 
						$invitacionLug, 
						$fecha_inv, 
						$excedente,
						$co_ciudad
		);
		//echo (error_log($query));
		$this->t_id=parent::insertar($query);
		$this->Load_Comprobacion_Invitacion($this->t_id);
		return($this->c_id);
	}
	
	public function edita_Comprobacion_Invitacion2($total, $sol_inv, $co_subtotal, $co_iva, $centroCosto, $cMotive, $cTipo, $observ, $observEdicion, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad,$tramite){
		$query = sprintf(
							"update comprobacion_invitacion set 
								co_total='%s',
								co_tramite='%s',
								co_fecha_registro=now(),
								co_subtotal='%s', 
								co_cc_clave='%s',
								co_motivo='%s',
								co_tipo='%s',
								co_observaciones='%s',
								co_observaciones_edicion='%s',
								co_num_invitado='%s', 
								co_lugar='%s',
								co_fecha_invitacion='%s',
								co_hubo_exedente='%s',
								co_ciudad='%s'
								where co_mi_tramite='%s'",
		$total,
		$sol_inv,
		$co_subtotal,
		$centroCosto,
		$cMotive,
		$cTipo,
		$observ,
		$observEdicion, 
		$tot_invitados,
		$invitacionLug,
		$fecha_inv,
		$excedente,
		$co_ciudad,	
		$tramite
		);
		//echo (error_log($query));
		parent::ejecutar($query);
		$this->Load_Comprobacion_Invitacion_By_co_mi_tramite($tramite);
		return($this->c_id);
	}
	// Guarda los datos de fecha y lugar de la comprobación
	public function Actualiza_Comprobacion_invitacion($idSolicitud, $invitacionLug, $fecha_inv){
		$query=sprintf("UPDATE comprobacion_invitacion SET co_lugar = '".$invitacionLug."', co_fecha_invitacion='".$fecha_inv."' WHERE co_tramite=%s",$idSolicitud);
		parent::insertar($query);
	}
	
	//Obten id de comprobacion a partir del co_tramite
	public function Obten_id_comprobacion($idSolicitud){
		$query=sprintf("SELECT co_id FROM comprobacion_invitacion INNER JOIN solicitud_invitacion ON comprobacion_invitacion.co_tramite=solicitud_invitacion.si_tramite WHERE si_tramite=%s",$idSolicitud);			
		$this->rst_tramite=parent::consultar($query);
		if(parent::get_rows()>0){
			$this->t_id=$this->Get_dato("t_id");
		}
	}
	
	// Guarda el monto total en pesos de la comprobación en la tabla de detalle
	public function Actualiza_Detalle_Comprobacion_pesos($idSolicitud, $totalpesos){
		$query=sprintf("UPDATE detalle_comprobacion_invitacion SET dci_monto_total_pesos = '".$totalpesos."' WHERE co_tramite=%s",$idSolicitud);
		parent::insertar($query);
	}
	
	//funcion para actualizar el concepto seleccionado de la tabla detalle_comprobacion
	public function Actualiza_dc_concepto($idSolicitud, $concepto){
		$query=sprintf("UPDATE detalle_comprobacion SET dc_concepto = '%s' WHERE dc_comprobacion = %s",$concepto,$idSolicitud);
		parent::insertar($query);
	}
	
	public function Actualiza_dc_total_aprobado($idSolicitud, $dc_total_aprobado){
		$query=sprintf("UPDATE detalle_comprobacion_invitacion SET dc_total_aprobado = '%s' WHERE dc_comprobacion = %s",$dc_total_aprobado,$idSolicitud);
		parent::insertar($query);
	}
	
	public function Actualiza_co_cc_clave($idSolicitud, $centrocosto){
		$query=sprintf("UPDATE comprobaciones SET co_cc_clave = '%s' WHERE co_id = %s",$centrocosto,$idSolicitud);
		error_log($query);
		parent::insertar($query);
	}
	
	public function Agrega_Detalle_Comp_Invitacion($comprobacion_id,$concepto,$rfc,$monto,$porcentaje_iva,$iva,$total,$proveedor,$fecha,$divisa,$tasa,$no_asistentes,$propina,$comentario,$folio){
		//ajusta fecha para db
		$date = explode("/", $fecha);
		if (count($date) != 3) {
			return "";
		}
		$date_db = $date[2] . "-" . $date[1] . "-" . $date[0];

		$query = sprintf("
								insert into detalle_comprobacion
								(
									dc_id,
									dc_comprobacion,
									dc_concepto,
									dc_rfc,
									dc_monto,
									dc_porcentaje_iva,
									dc_iva,
									dc_total,
									dc_proveedor,
									dc_fecha,
									dc_factura,
									dc_divisa,
									dc_tipocambio,
									dc_comensales,
									dc_propinas,
									dc_info_extra,
									dc_folio_factura
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
									now(),
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s'
								)
								",
						$comprobacion_id,
						$concepto,
						$rfc,
						$monto,
						$porcentaje_iva,
						$iva,
						$total,
						$proveedor,
						$date_db,
						$divisa,
						$tasa,
						$no_asistentes,
						$propina,
						$comentario,
						$folio
		);
		parent::insertar($query);
	}
	
	public function Agrega_Detalle_Comp_Invitacion2($comprobacion_id,$concepto,$rfc,$monto,$porcentaje_iva,$iva,$total,$proveedor,$fecha,$divisa,$tasa,$no_asistentes,$propina,$folio, $totalpesos, $idAmex,$comentario){
		//ajusta fecha para db
		$date = explode("/", $fecha);
		if (count($date) != 3) {
			return "";
		}
		$date_db = $date[2] . "-" . $date[1] . "-" . $date[0];

		$query = sprintf("
								insert into detalle_comprobacion_invitacion
								(
									dc_id,
									dc_comprobacion,
									dc_concepto,
									dc_rfc,
									dc_monto,
									dc_porcentaje_iva,
									dc_iva,
									dc_total,
									dc_total_aprobado,
									dc_proveedor,
									dc_fecha,
									dc_factura,
									dc_divisa,
									dc_tipocambio,
									dc_comensales,
									dc_propinas,
									dc_folio_factura,
									dci_monto_total_pesos,
									dc_idamex_comprobado,
									dc_comentarios
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
									now(),
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
								",
						$comprobacion_id,
						$concepto,
						$rfc,
						$monto,
						$porcentaje_iva,
						$iva,
						$total,
						$total,
						$proveedor,
						$date_db,
						$divisa,
						$tasa,
						$no_asistentes,
						$propina,
						$folio,
						$totalpesos,
						$idAmex, 
						$comentario
		);
		//error_log($query);
		parent::insertar($query);
	}

	function AgregarConceptoFinanzas($idTramite,$valComprobacion,$dc_idamex_comprobado,$dc_notransaccion,$conceptoDes,$montoBD,$propinaBD,$divisaBD,$ivaBD,$imphospedajeBD,$total,$totalUSD,$comentario,$fecha){		
		//funcion que nos permitira agregar un concepto agregado en la BD (Finanzas)
		
		$dc_id=0;
		$co_id=0;
		$valCero=0;

		//obtener el id del concepto de tipo personal
		$queryPersonal=sprintf("SELECT dc_id FROM cat_conceptosbmw WHERE cp_concepto='%s'",$conceptoDes);
		$conceptoPersonal =parent::consultar($queryPersonal);
		while ($fila = mysql_fetch_assoc($conceptoPersonal)) {			
				$dc_id=$fila['dc_id'];
		}
		
		//inicia la inserccion de datos
		$queryco_id=sprintf("SELECT co_id FROM comprobaciones WHERE co_mi_tramite =%s",$idTramite);
		$conceptoTemp =parent::consultar($queryco_id);
		
		while ($fila = mysql_fetch_assoc($conceptoTemp)) {			
			$co_id=$fila['co_id'];
			}

		//comentario vacio
		if($comentario == ""){
			$comentario = "(NULL)";
		}	
		
		$dc_total_pesos=$montoBD+$propinaBD+$ivaBD+$imphospedajeBD;
				
				$queryInserta="INSERT INTO detalle_comprobacion(dc_comprobacion,
						dc_tipo,
						dc_idamex_comprobado,
						dc_notransaccion,
						dc_concepto,
						dc_tipo_comida,
						dc_monto,
						dc_divisa,
						dc_iva,
						dc_total,
						dc_total_dolares,
						dc_descripcion,
						dc_comensales,
						dc_fecha,
						dc_folio_factura,
						dc_proveedor,
						dc_rfc,
						dc_propinas,
						dc_imp_hospedaje,
						dc_tipocambio,
						dc_total_pesos) VALUES(".$co_id.",
						".$valComprobacion."
						,".$dc_idamex_comprobado.",
						".$dc_notransaccion.",
						".$dc_id.",
						".$valCero.",
						".$montoBD.",
						'".$divisaBD."',
						".$ivaBD.",
						".$total.",
						".$totalUSD.",
						'".$comentario."',
						".$valCero.",
						'".$fecha."',
						'".$valCero."',
						".$valCero.",
						".$valCero.",
						".$valCero.",
						".$imphospedajeBD.",
						".$valCero.",
						".$dc_total_pesos.")";
						error_log($queryInserta);
						parent::insertar($queryInserta);		
							
	
	}
	
	function AgregarConceptoFinanzasAmex($idTramite,$valComprobacion,$dc_id,$montoBD,$propinaBD,$divisaBD,$ivaBD,$imphospedajeBD,$total,$totalUSD,$comentario,$fecha,$dc_idamex_comprobado,$numero){
		//funcion que nos permitira agregar un concepto agregado en la BD (Finanzas)
		$valCero=0;
		$aux = array();		
		$co_id=0;
		//inicia la inserccion de datos
		$queryco_id=sprintf("SELECT co_id FROM comprobaciones WHERE co_mi_tramite =%s",$idTramite);
		$conceptoTemp =parent::consultar($queryco_id);
		
		while ($fila = mysql_fetch_assoc($conceptoTemp)) {			
			$co_id=$fila['co_id'];
			}
			
		//comentario vacio
		if($comentario == ""){
			$comentario = "(NULL)";
		}		
		$dc_total_pesos=$montoBD+$propinaBD+$ivaBD+$imphospedajeBD;
		
			$queryInserta="INSERT INTO detalle_comprobacion(dc_comprobacion,
						dc_tipo,
						dc_idamex_comprobado,
						dc_notransaccion,
						dc_concepto,
						dc_tipo_comida,
						dc_monto,
						dc_divisa,
						dc_iva,
						dc_total,
						dc_total_dolares,
						dc_descripcion,
						dc_comensales,
						dc_fecha,
						dc_folio_factura,
						dc_proveedor,
						dc_rfc,
						dc_propinas,
						dc_imp_hospedaje,
						dc_tipocambio,
						dc_total_pesos) VALUES(".$co_id.",
						".$valComprobacion.",
						".$dc_idamex_comprobado.",
						".$numero.",
						".$dc_id.",
						".$valCero.",
						".$montoBD.",
						'".$divisaBD."',
						".$ivaBD.",
						".$total.",
						".$totalUSD.",
						'".$comentario."',
						".$valCero.",
						'".$fecha."',
						'".$valCero."',
						".$valCero.",
						".$valCero.",
						".$valCero.",
						".$imphospedajeBD.",
						".$valCero.",
						".$dc_total_pesos.")";
						error_log($queryInserta);
						parent::insertar($queryInserta);		
	}
	
	function ActualizarResumenFinanzas($idTramite,$anticipoCABMW,$personalDescuento,$amexCABMW,$efectivoCXBMW,$montoDescontar,$montoReembolsar,$totalComp){
		$aux=array();
		$queryResumenFinanzas="UPDATE comprobaciones SET 
			co_anticipo_comprobado=".$anticipoCABMW.",
			co_personal_descuento=".$personalDescuento.",
			co_amex_comprobado=".$amexCABMW.",
			co_efectivo_comprobado=".$efectivoCXBMW.",
			co_mnt_descuento=".$montoDescontar.",
			co_mnt_reembolso=".$montoReembolsar.", 
			co_total=".$totalComp." WHERE co_mi_tramite=".$idTramite." ";
			error_log($queryResumenFinanzas);
			parent::insertar($queryResumenFinanzas);	
	
	}
	
	function ActualizarCamposAprobados($idtramitefin,$tipo,$notransaccion,$fecha,$comentario,$monto,$propina,$iva,$imphospedaje,$total,$divisa,$conceptoP,$detalleProcedente,$idcargoAmex){
		$co_id=0;
		$valCero=0;
		$divisa_USD=0;
		$tipoAnticipo=0;
		$aux = array();	

		//validacion del tipo de Anticipo
		if($tipo == "Anticipo"){
			$tipoAnticipo=1;
		}elseif($tipo == "Amex"){
			$tipoAnticipo=2;
		}elseif($tipo == "Reembolso" ){
			$tipoAnticipo=3;
		}elseif($tipo == "Amex externo" ){
			$tipoAnticipo=4;
		}		
		
		//realizamos la tranformacion de fecha de formato normal al de BD		
		$date = explode("/", $fecha);
		if (count($date) != 3) {
			return "";
		}
		$date_db = $date[2] . "-" . $date[1] . "-" . $date[0];
		
		
		//validacion del tipo de comprobacion
		if($notransaccion == "N/A"){//quiere decir que es diferente de amex
			$dc_idamex_comprobado=0;
			$dc_notransaccion=0;
			
		}else{
			$dc_idamex_comprobado=1;
			$dc_notransaccion=$notransaccion;
		}		
		
		//Realizamos la conversion de pesos a dolares para su total
		$queryUSD=sprintf("SELECT div_tasa FROM divisa WHERE div_nombre='USD'");
		$USD=parent::consultar($queryUSD);
		while ($fila = mysql_fetch_assoc($USD)) {
			$divisa_USD=$fila['div_tasa'];
		}
		
		$totalDolares=$total/$divisa_USD;
		error_log("USD".$totalDolares);
		//inicia la inserccion de datos
		$queryco_id=sprintf("SELECT co_id FROM comprobaciones WHERE co_mi_tramite =%s",$idtramitefin);
		$conceptoTemp =parent::consultar($queryco_id);
		
		while ($fila = mysql_fetch_assoc($conceptoTemp)) {			
			$co_id=$fila['co_id'];
			}		
						
					$queryInsert="INSERT INTO detalle_comprobacion(dc_comprobacion,
						dc_tipo,
						dc_idamex_comprobado,
						dc_notransaccion,
						dc_concepto,
						dc_tipo_comida,
						dc_monto,
						dc_divisa,
						dc_iva,
						dc_total,
						dc_total_dolares,
						dc_descripcion,
						dc_comensales,
						dc_fecha,
						dc_folio_factura,
						dc_proveedor,
						dc_rfc,
						dc_propinas,
						dc_imp_hospedaje,
						dc_tipocambio,
						dc_total_pesos,
						dc_origen_personal, 
						dc_factura ) VALUES(".$co_id.",
						".$tipoAnticipo.",
						".$idcargoAmex.",
						".$dc_notransaccion.",
						".$conceptoP.",
						'0',
						".$monto.",
						'".$divisa."',
						".$iva.",
						".$total.",
						".$totalDolares.",
						'".$comentario."',
						".$valCero.",
						'".$date_db."',
						'".$valCero."',
						".$valCero.",
						".$valCero.",
						".$propina.",
						".$imphospedaje.",
						".$valCero.",
						".$total.",
						'".$detalleProcedente."', 
						'".$date_db."')";
					//error_log($tipo);
					error_log($queryInsert);
					//error_log($comentario);
					parent::insertar($queryInsert);
					
	}
	
}
?>
