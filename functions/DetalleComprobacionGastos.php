<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

class DetalleComprobacionGastos extends conexion {

	protected $dc_id;
	protected $rst_detalleCompGastos;

	public function __construct() {
		parent::__construct();
		$this->dc_id = "";
	}

	// Consultar los detalles de la Comprobación de Gastos por ID de Comprobación
	public function cargaDetalleComprobacion($idComprobacion) {
		$query = sprintf("SELECT * FROM detalle_comprobacion_gastos WHERE dc_comprobacion = '%s'", $idComprobacion);
		$this->rst_detalleCompGastos = parent::consultar($query);
		if (parent::get_rows() > 0) {
			$this->dc_id = $this->Get_dato("dc_id");
		}
	}

	public function Get_dato($nombre) {
		return(mysql_result($this->rst_detalleCompGastos, 0, $nombre));
	}
	
	function agregaDetalle($idComprobacion, $cargo_asociado, $idAmex, $no_transaccion, $conceptoBMW, $tipoComida, $monto, $tipoDivisa, $iva, $total, $totalDolares, $comentario, $no_asistentes, $fecha, $folio_factura, $idProv, $rfc, $propina, $impuesto_hospedaje, $totalMxn){
		$fecha = fecha_to_mysql($fecha);
		
		$query = sprintf("INSERT INTO detalle_comprobacion_gastos(
				dc_id,
				dc_comprobacion,
				dc_tipo,
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
				dc_tipo_cambio,
				dc_comensales,
				dc_propinas,
				dc_imp_hospedaje,
				dc_comentarios,
				dc_folio_factura,
				dc_idamex_comprobado,
				dc_estatus,
				dc_enviado_sap,
				dc_tipo_comida,
				dc_total_aprobado,
				dc_total_aprobado_cxp,
				dc_total_pesos,
				dc_notransaccion,
				dc_total_dolares)
				VALUES(
				default,
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'0',
				'%s',
				'%s',
				'%s',
				NOW(),
				'%s',
				'%s',
				0,
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				0,
				0,
				'%s',
				0,
				0,
				'%s',
				'%s',
				'%s')",
				$idComprobacion,
				$cargo_asociado,
				$conceptoBMW,
				$rfc,
				$monto,
				$iva,
				$total,
				$idProv,
				$fecha,
				$tipoDivisa,
				$no_asistentes,
				$propina,
				$impuesto_hospedaje,
				$comentario,
				$folio_factura,
				$idAmex,
				$tipoComida,
				$totalMxn,
				$no_transaccion,
				$totalDolares
		);
		//error_log($query);
		$dc = $this->dc_id = parent::insertar($query);
		return($dc);
	}
	
	// Limpiar los detalles de comprobación
	public function limpiar_detalles($id_tramite){
    	$query = sprintf("DELETE detalle_comprobacion_gastos FROM detalle_comprobacion_gastos, comprobacion_gastos, tramites
				WHERE t_id = co_mi_tramite AND co_id = dc_comprobacion AND t_id = '%s'", $id_tramite);
    	//error_log($query);
    	parent::ejecutar($query);		
    }
}