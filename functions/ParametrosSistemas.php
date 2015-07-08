<?php
$root  = $_SERVER['DOCUMENT_ROOT'];
$dir  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$uri = explode("/", $dir);
$base = $root . "/" . $uri[1] . "/";

require_once($base."lib/php/constantes.php");
	require_once("$RUTA_A/Connections/fwk_db.php");

	class ParametrosSistema{
	
		private $id;
		private $codigo;
		private $descripcion;
		private $flujo;
		private $valor;
		
		/**
		 * Constructor de la Clase, recibe una cadena JSON que mapea para llenar el objeto
		 *
		 * @args string => Cadena JSON con la siguiente estructura{"id": ?, "codigo": ?}		 
		 */
		public function __construct($args){
			if(!empty($args)){
				$args = json_decode($args);				
				foreach ($args as $key => $value)
					$this->$key = $value;				
				//$this->obtenerParametro();
			}
		}
		
		/**
		 * Permite Obtener algun atributo de la clase
		 * 
		 * @atributo string => nombre del atributo de la clase que se quiere obtener
		 * @return string => valor del atributo deseado
		 */
		public function get($atributo){
			return $this->$atributo;
		}
		
		/**
		 * Permite asignar algun atributo de la clase
		 * 
		 * @atributo string => nombre del atributo de la clase que se quiere asignar
		 * @valor string => valor asignado el atributo especificado
		 */
		public function set($atributo, $valor){		
			$this->$atributo = $valor;
		}
		
		/**
		 * La funcion permite obtener el objeto de la BD deseado
		 */		
		public function obtenerParametro(){
			$cnn = new conexion();
			
			$codigo = $this->codigo;
			$id = $this->id;			
			
			$sql = "SELECT * 
					FROM parametros_sistema
					WHERE ps_id = '$id'
					OR ps_codigo = '$codigo'";
			$res = $cnn->consultar($sql);
			$row = mysql_fetch_assoc($res);
			
			$array = array("id" 			=> $row["ps_id"],
						   "descripcion" 	=> $row["ps_descripcion"],
						   "codigo" 		=> $row["ps_codigo"],
						   "valor" 			=> $row["ps_valor"],
						   "flujo" 			=> $row["f_id"]);
			foreach ($array as $key => $value)
					$this->$key = $value;
					
			return json_encode($array);
		}	
	}
	
	/*if(isset($_REQUEST) && !empty($_REQUEST)){
		$param = new ParametrosSistema($_REQUEST["param"]);
		echo $param->obtenerParametro();		
	}/*else{
		$p = new ParametrosSistema();
		$p->set("id", "1");			
		$p->obtenerParametro();
		$p->get("valor");		
		//$parametro = json_decode($p->obtenerParametro());		
		//$parametro->valor;
	}*/
?>