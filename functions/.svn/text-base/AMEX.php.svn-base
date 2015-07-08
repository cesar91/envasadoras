<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

class AMEX extends conexion {

	protected $idamex;
	protected $rst_rstAMEX;

	public function __construct() {
		parent::__construct();
		$this->idamex = "";
	}

	// Consultar los cargos de AMEX búsqueda por Id de cargo
	public function obtenerCargosporID($idamex) {
		$query = sprintf("SELECT * FROM amex WHERE idamex = '%s'", $idamex);
		$this->rst_rstAMEX = parent::consultar($query);
		if (parent::get_rows() > 0) {
			$this->idamex = $this->Get_dato("idamex");
		}
	}

	public function Get_dato($nombre) {
		return(mysql_result($this->rst_rstAMEX, 0, $nombre));
	}
	
	public function restablecerCargos($idComprobacion){
		$query = sprintf("UPDATE amex a
			LEFT JOIN empleado e1 ON tarjeta = e1.notarjetacredito
			LEFT JOIN empleado e2 ON tarjeta = e2.notarjetacredito_gas
			LEFT JOIN usuario u1 ON u1.u_id = e1.idfwk_usuario
			LEFT JOIN usuario u2 ON u2.u_id = e2.idfwk_usuario
			SET comprobacion_id = '0' WHERE a.comprobacion_id = '%s'", $idComprobacion);
		parent::ejecutar($query);
	}
	
	public function modificaEstatusUtilizado($idUsuario){
		$query_estatus_dev = sprintf("UPDATE amex a
		LEFT JOIN empleado e1 ON tarjeta = e1.notarjetacredito
		LEFT JOIN empleado e2 ON tarjeta = e2.notarjetacredito_gas
		LEFT JOIN usuario u1 ON u1.u_id = e1.idfwk_usuario
		LEFT JOIN usuario u2 ON u2.u_id = e2.idfwk_usuario
		SET a.estatus = 1
		WHERE a.estatus = 3 AND u1.u_id = '%s'
		OR a.estatus = 3 AND u2.u_id = '%s'", $idUsuario, $idUsuario);
		parent::ejecutar($query_estatus_dev);
	}
	
	// Asocioamos la Comprobación al cargo AMEX seleccionado
	public function setComprobacionid($idamex, $idComprobacion){
		$query = sprintf("UPDATE amex SET comprobacion_id = '%s' WHERE idamex = '%s'", $idComprobacion, $idamex);
		parent::ejecutar($query);
	}
	
}
?>