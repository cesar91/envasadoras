<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

class ComprobacionesGastos extends conexion {

	protected $co_id;
	protected $rst_compGastos;

	public function __construct() {
		parent::__construct();
		$this->co_id = "";
	}
	
	// Consultar todos los datos de la Solicitud de Gastos búsqueda por Id de Tramite
	public function cargaComprobacionGastosporTramite($idTramite) {
		$query = sprintf("SELECT * FROM comprobacion_gastos WHERE co_mi_tramite = '%s'", $idTramite);
		//error_log("--->>Consultar datos de la comprobacion: ".$query);
				
        $this->rst_compGastos = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $this->co_id = $this->Get_dato("co_id");
        }
	}
	
	public function Get_dato($nombre) {
		return(mysql_result($this->rst_compGastos, 0, $nombre));
	}

	// Funcion que registra una solicitud de gastos
	public function Crea_Gasto($idTramite, $solicitud_referenciada, $centroCosto, $observ, $observEd, $totalComprobaciones, $anticipoCompAutBMW, $personalDescontar, $amexCompAutBMW, $efectivoCompAutBMW, $montoDescontar, $montoReembolsar, $co_gasolina, $fecha_inicial, $fecha_final, $motivo_gasolina, $co_tipo_auto, $co_modelo_auto, $co_kilometraje, $co_monto_gasolina, $co_ruta, $amexExterno, $lugar_restaurante, $ciudad, $anticipo_solicitud){
		date_default_timezone_set("America/Mexico_City");
		
		if($fecha_inicial != "00000000")
			$fecha_inicial = fecha_to_mysql($fecha_inicial);
		
		if($fecha_final != "00000000")
			$fecha_final = fecha_to_mysql($fecha_final);
		
		$query = sprintf("INSERT INTO comprobacion_gastos(
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
							co_amex_externo,
	    					co_lugar,
	    					co_ciudad, 
							co_anticipo_gasto
	                    ) VALUES(default,
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
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
	    					'%s',
	    					'%s',
							'%s')",
	                    $anticipoCompAutBMW,
	                    $amexCompAutBMW,
	                    $montoReembolsar,
	                    $montoDescontar,
	                    $totalComprobaciones,
	    				$solicitud_referenciada, 
	                    $idTramite,
	                    $centroCosto,
	                    $observ,
	    				$observEd,
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
						$amexExterno,
			    		$lugar_restaurante,
			    		$ciudad,
						$anticipo_solicitud);
	    //error_log("--->>Inserción de comprobación de Gastos: ".$query);
		$this->co_id = parent::insertar($query);
		return($this->co_id);
	}

	// Funcion que edita una solicitud de gastos
	public function Edita_Comprobacion_Gasto($idTramite,$solicitud_referenciada,$centroCosto,$observ,$observEd,$totalComprobaciones,$anticipoCompAutBMW,$personalDescontar,$amexCompAutBMW,$efectivoCompAutBMW,$montoDescontar,$montoReembolsar,$co_gasolina,$fecha_inicial,$fecha_final,$motivo_gasolina,$co_tipo_auto,$co_modelo_auto,$co_kilomettraje,$co_monto_gasolina,$co_ruta,$amexExterno, $lugar_restaurante, $ciudad, $anticipo_solicitud){
		if($fecha_inicial != "00000000")
			$fecha_inicial = fecha_to_mysql($fecha_inicial);
		
		if($fecha_final != "00000000")
			$fecha_inicial = fecha_to_mysql($fecha_final);
		
		$query = sprintf("UPDATE comprobacion_gastos
					SET co_tramite = '%s', 
					  co_anticipo_comprobado = '%s',
					  co_amex_comprobado = '%s',
					  co_mnt_reembolso = '%s',
					  co_mnt_descuento = '%s',
					  co_total = '%s',
					  co_cc_clave = '%s',
					  co_observaciones = '%s',
    				  co_observaciones_edicion = '%s',
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
					  co_amex_externo = '%s', 
					  co_lugar = '%s', 
					  co_ciudad = '%s',
					  co_anticipo_gasto = '%s'
					WHERE co_mi_tramite = '%s'",
					$solicitud_referenciada,
                    $anticipoCompAutBMW,
                    $amexCompAutBMW,                    
                    $montoReembolsar,
                    $montoDescontar,
                    $totalComprobaciones,
                    $centroCosto,
                    $observ,
    				$observEd,
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
					$lugar_restaurante,
					$ciudad,
					$anticipo_solicitud,
    				$idTramite);
		//error_log("--->>Edición de comprobación de Gastos: ".$query);
		parent::ejecutar($query);
		
		$query = sprintf("SELECT co_id FROM comprobacion_gastos WHERE co_mi_tramite = '%s'", $idTramite);
		$rst = parent::consultar($query);
		return(mysql_result($rst, 0, "co_id"));
	}

	// Actualización del campo de observaciones de la tabla comprobacion_gastos
	public function actualizaObservaciones($observaciones, $observacionesEdicion, $tramite) {
		$query = sprintf("UPDATE comprobacion_gastos SET co_observaciones = '%s', co_observaciones_edicion = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $observacionesEdicion, $tramite);
		parent::ejecutar($query);
	}
	
	// Actualización del Centro de Costos de la Comprobacion de Gastos
	public function actualizarCECO($centroCosto, $idtramite) {
		$query = sprintf("UPDATE comprobacion_gastos SET co_cc_clave = '%s' WHERE co_mi_tramite = '%s'", $centroCosto, $idtramite);
		//error_log("--->>Actualizar Centro de Costos: ".$query);
		parent::ejecutar($query);
	}
	
	public function actualizarResumen($idTramite, $anticipoCABMW, $personalDescuento, $amexCABMW, $efectivoCXBMW, $montoDescontar, $montoReembolsar, $amexExterno, $totalComp){
		$queryResumen = sprintf("UPDATE comprobacion_gastos SET
					co_anticipo_comprobado = '%s',
					co_personal_descuento = '%s',
					co_amex_comprobado = '%s',
					co_efectivo_comprobado = '%s',
					co_mnt_descuento = '%s',
					co_mnt_reembolso = '%s',
					co_amex_externo = '%s',
					co_total = '%s' 
					WHERE co_mi_tramite = '%s'", 
					$anticipoCABMW, 
					$personalDescuento, 
					$amexCABMW, 
					$efectivoCXBMW, 
					$montoDescontar, 
					$montoReembolsar, 
					$amexExterno, 
					$totalComp, 
					$idTramite);
		//error_log("--->>Actualización de los montos modificados por Finanzas: ".$queryResumen);
		parent::ejecutar($queryResumen);
	}
	
	// Borrar detalles de la comprobación por Id de concepto
	public function borrarDetallesporIdConcepto($idComprobacion, $idConcepto){
		$query = sprintf("DELETE FROM detalle_comprobacion_gastos WHERE dc_comprobacion = '%s' AND dc_concepto = '%s'", $idComprobacion, $idConcepto);
		//error_log("--->>Borrar detalles de la comprobación por id de concepto: ".$query);
		parent::ejecutar($query);
	}
}
?>