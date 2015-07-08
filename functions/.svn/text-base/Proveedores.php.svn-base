<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

class Proveedores extends conexion {

	protected $pro_id;
	protected $rst_proveedores;

	public function __construct() {
		parent::__construct();
		$this->pro_id = "";
	}

	// Consultar proveedor por RFC
	public function obtenerProveedorporRFC($rfc) {
		$query = sprintf("SELECT * FROM proveedores WHERE pro_rfc = '%s'", $rfc);
		$this->rst_proveedores = parent::consultar($query);
		if (parent::get_rows() > 0) {
			$this->pro_id = $this->Get_dato("pro_id");
		}
	}

	public function Get_dato($nombre) {
		return(mysql_result($this->rst_proveedores, 0, $nombre));
	}
	
}
?>