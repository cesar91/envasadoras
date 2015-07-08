<?php
	$root  = $_SERVER['DOCUMENT_ROOT'];
	$dir  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$uri = explode("/", $dir);
	$base = $root . "/" . $uri[1] . "/";

	require_once($base."lib/php/constantes.php");
	require_once("$RUTA_A/Connections/fwk_db.php");

	class Excepcion{
		
		private $id;
		private $mensaje;
		private $diferencia;
		private $solicitud;
		private $comprobaicon;
		private $comprobacionDetalle;
		private $concepto;
		
		/**
		 * Constructor de la Clase, recibe una cadena JSON que mapea para llenar el objeto
		 *
		 * @args string => Cadena JSON con la siguiente estructura {"comprobacion": ?, "comprobacionDetalle": ?, "solicitud": ?, "mensaje": ?, "concepto": ?, "diferencia": ?}		 
		 */
		public function __construct($args){
			if(!empty($args)){
				$args = json_decode($args);				
				foreach ($args as $key => $value)
					$this->$key = $value;								
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
		public function obtenerExcepcion(){
			$cnn = new conexion();
			
			$solicitud = $this->solicitud;
			$comprobaicon  = $this->comprobacion;
			$comprobacionDetalle = $this->comprobacionDetalle;
			
			$sql = "SELECT * 
					FROM excepciones
					WHERE ex_comprobacion = '$comprobaicon'
					OR ex_comprobacion_detalle = '$comprobacionDetalle'
					OR ex_solicitud = '$solicitud'";
			$res = $cnn->consultar($sql);
			$row = mysql_fetch_assoc($res);
			
			$array = array("id" 					=> $row["ex_id"],
						   "mensaje" 				=> $row["ex_mensaje"],
						   "diferencia" 			=> $row["ex_diferencia"],
						   "solicitud" 				=> $row["ex_solicitud"],
						   "comprobaicon" 			=> $row["ex_comprobacion"],
						   "comprobacionDetalle"	=> $row["ex_comprobacion_detalle"],
						   "concepto" 				=> $row["ex_concepto"]);
			foreach ($array as $key => $value)
					$this->$key = $value;
					
			return json_encode($array);
		}	
		
		/**
		 * La funcion realiza el trabajo de registrar excepciones en la BD
		 */		
		public function insertaExcepcion(){
			$cnn = new conexion();
			
			$mensaje = $this->mensjae;
			$diferencia = $this->diferencia;
			$solicitud = $this->solicitud;
			$comprobaicon  = $this->comprobacion;
			$comprobacionDetalle = $this->comprobacionDetalle;
			$concepto = $this->concepto;
			
			$sql = "INSERT INTO excepciones
						(ex_id, ex_mensaje, ex_diferencia, ex_solicitud, ex_comprobacion, ex_comprobacion_detalle, ex_concepto)
                    VALUES 
						(default, '$mensaje', '$diferencia', '$solicitud', '$comprobaicon', '$comprobacionDetalle', '$concepto')";
			$this->id = $cnn->insertar($sql);			
		}	
		
		/**
		 * La funcion realiza el trabajo de eliminar excepciones
		 */		
		public function eliminarExcepcion(){
			$cnn = new conexion();
			
			$solicitud = $this->solicitud;
			$comprobaicon  = $this->comprobacion;
			
			$sql = "DELETE excepciones
					FROM excepciones 					
					WHERE ex_comprobacion = $comprobaicon
					OR ex_solicitud = $solicitud";						
			$cnn->ejecutar($sql);	
		}	
	}
?>