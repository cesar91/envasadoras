<?php
$root  = $_SERVER['DOCUMENT_ROOT'];
$dir  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$uri = explode("/", $dir);
$base = $root . "/" . $uri[1] . "/";

require_once($base."lib/php/constantes.php");
require_once("$RUTA_A/Connections/fwk_db.php");
require_once("$RUTA_A/functions/Empleado.php");

class PoliticasGastos{
	protected $rst_PoliticasGastos;
		
	private $idUsuario;
	private $region;
	private $idConcepto;
	private $flujo;
	private $solicitud;
	
	public function __construct($_arreglo){
		//error_log("--->> 1.- Arreglo obtenido: ".print_r($_arreglo, true));
		if(!empty($_arreglo)){
			$_arreglo = json_decode($_arreglo);
			//error_log("--->> 2.- Arreglo obtenido: ".print_r($_arreglo, true));
			foreach ($_arreglo as $key => $value){
				$this->$key = $value;
			}
			$this->obtenerPoliticadeGasto();
		}
	}
	
	public function identificarBando($idUsuario){
		$empleado = new Empleado();
		$empleado->cargaEmpleadoporIdusuario($idUsuario);
		return $empleado->Get_dato("b_id");
	}
	
	public function Get_dato($nombre){
		return($this->rst_PoliticasGastos[$nombre]);
	}
	
	public function obtenerPoliticadeGasto(){
		$cnn = new conexion();
		// Obtener valores del Arreglo Asociativo
		$idUsuario = $this->idUsuario;
		$region = $this->region;
		$concepto = $this->idConcepto;
		$flujo = $this->flujo;
		$solicitud = $this->solicitud;
		$_arreglo = array();
		
// 		error_log("-->>IdUsuario: ".$idUsuario);
// 		error_log("-->>Region: ".$region);
// 		error_log("-->>Concepto: ".$concepto);
// 		error_log("-->>Flujo: ".$flujo);
		
		// Obtener Bando del Empleado
		$bandoEmpleado = $this->identificarBando($idUsuario);
		if(isset($solicitud) > 0 && $flujo = 3){
			if($concepto == 3 || $concepto == 4 || $concepto == 5 || $concepto == 29 || $concepto == 30 || $concepto == 31){
				$concepto;
			}else{
				$concepto = 0;
			}
			$queryPoliticas = sprintf("SELECT pd.pd_monto, pd.div_id, CONVERT((pd.pd_monto * dv.div_tasa), DECIMAL(10,2)) AS montolimitePolitica
					FROM politicas_divisa AS pd 
					INNER JOIN politicas_flujos AS pf ON (pf.pf_id = pd.pf_id) 
					INNER JOIN divisa AS dv ON (dv.div_id = pd.div_id) 
					WHERE pf.b_id = '%s' 
					AND pf.re_id = '%s' 
					AND pf.c_id = '%s'
					AND pf.f_id = '%s'", $bandoEmpleado, $region, $concepto, $flujo);
		}else{
			$queryPoliticas = sprintf("SELECT pd.pd_monto, pd.div_id, CONVERT((pd.pd_monto * dv.div_tasa), DECIMAL(10,2)) AS montolimitePolitica
					FROM politicas_divisa AS pd 
					INNER JOIN politicas_flujos AS pf ON (pf.pf_id = pd.pf_id) 
					INNER JOIN divisa AS dv ON (dv.div_id = pd.div_id) 
					WHERE pf.b_id = '%s' 
					AND pf.re_id = '%s' 
					AND pf.c_id = '%s'
					AND pf.f_id = '%s'", $bandoEmpleado, $region, $concepto, $flujo);
		}
		
		$rst = $cnn->consultar($queryPoliticas);
		//error_log("--->>Monto limite de Politica: ".mysql_result($rst, 0, 'montolimitePolitica'));
		$filasRetornadas = mysql_num_rows($rst);
		
		if($filasRetornadas != 0){
			while($fila = mysql_fetch_assoc($rst))
				$_arreglo[] = array('montoPolitica' => $fila['montolimitePolitica']);
		}else
			$_arreglo[] = array('montoPolitica' => 0);
		
        echo json_encode($_arreglo);
	}	
}

$politicasGastos = new PoliticasGastos($_REQUEST['jObject']);

?>
