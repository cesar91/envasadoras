<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

class SolicitudesGastos extends conexion {

	protected $sg_id;
	protected $rst_solGastos;

	public function __construct() {
		parent::__construct();
		$this->sg_id = "";
	}
    
    // Consultar todos los datos de la Solicitud de Gastos búsqueda por Id de Tramite
    public function cargaGastoporTramite($idTramite) {
    	$query = sprintf("SELECT * FROM solicitud_gastos WHERE sg_tramite = '%s'", $idTramite);		
        $this->rst_solGastos = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $this->sg_id = $this->Get_dato("sg_id");
        }
    }
    
    public function Get_dato($nombre) {
    	return(mysql_result($this->rst_solGastos, 0, $nombre));
    }
    
    // Funcion que registra una solicitud de gastos
    public function Crea_Gasto($motivo, $monto, $total_pesos, $divisa, $cecos, $tramite, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechagasto, $gasto_lugar, $req_anticipo, $concepto) {
    	date_default_timezone_set("America/Mexico_City");
    	
    	$query = sprintf("INSERT INTO solicitud_gastos(
    			sg_id,
    			sg_motivo,
    			sg_monto,
    			sg_monto_pesos,
    			sg_fecha_registro,
    			sg_divisa,
    			sg_ceco,
    			sg_tramite,
    			sg_ciudad,
    			sg_observaciones,
    			sg_observaciones_edicion,
    			sg_fecha_gasto,
    			sg_lugar,
    			sg_requiere_anticipo,
    			sg_concepto)
    			VALUES(default,
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
    			'%s');
    			", $motivo, $monto, $total_pesos, $divisa, $cecos, $tramite, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechagasto, $gasto_lugar, $req_anticipo, $concepto);
    	//error_log("--->> Creacion del gasto: ".$query);
    	$this->sg_id = parent::insertar($query);
    	return($this->sg_id);
    }
    
    // Funcion que edita una solicitud de gastos
    public function Edita_Gasto($motivo, $monto, $total_pesos, $divisa, $cecos, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechagasto, $gasto_lugar, $req_anticipo, $concepto, $tramite) {
    	$query = sprintf("UPDATE solicitud_gastos SET 
    			sg_motivo = '%s',
    			sg_monto = '%s',
    			sg_monto_pesos = '%s',
    			sg_divisa = '%s',
    			sg_ceco = '%s',
    			sg_ciudad = '%s',
    			sg_observaciones = '%s',
    			sg_observaciones_edicion = '%s',
    			sg_fecha_gasto = '%s',
    			sg_lugar = '%s',
    			sg_requiere_anticipo = '%s',
    			sg_concepto = '%s' 
    			WHERE sg_tramite = '%s'", $motivo, $monto, $total_pesos, $divisa, $cecos, $ciudad_solicitud, $observaciones, $observacionesEdicion, $fechagasto, $gasto_lugar, $req_anticipo, $concepto, $tramite);
    	//error_log("--->> Edicion del gasto: ".$query);
    	parent::ejecutar($query);
    }
    
    // Actualización del campo de observaciones de la tabla solicitud_gastos
    public function actualizaObservaciones($observaciones, $observacionesEdicion, $tramite) {
    	$query = sprintf("UPDATE solicitud_gastos SET sg_observaciones = '%s', sg_observaciones_edicion = '%s' WHERE sg_tramite = '%s'", $observaciones, $observacionesEdicion, $tramite);
    	parent::ejecutar($query);
    }
}
?>