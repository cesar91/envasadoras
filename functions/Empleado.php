<?php
	class Empleado extends conexion {
		
		protected $rst_empleado;
		function __construct(){
			parent::__construct();
			
		}
		
		function busca_profundidad($e_id,$profundidad,$contador=0){
			
			$query=sprintf("select u_jefe,u_id from usuario where u_id=%s",$e_id);
			
			$aux=parent::consultar($query);
			if(parent::get_rows()>0){
				$e_id=mysql_result($aux,0,0);
				if($contador==$profundidad){
					return(mysql_result($aux,0,"u_id"));
				}
				else{
					
					if(trim($e_id)==""){
						return(mysql_result($aux,0,"u_id"));
					}
					$contador++;
					return($this->busca_profundidad($e_id,$profundidad,$contador));
				}
			}
			else{
				//NO tiene Jefe en tal profundidad
				return(-1);
			}	
		}	
		
		public function Load_datos_por_usuario($id_usuario){
			$query=sprintf("select * from empleado where nuempleado=%s",$id_usuario);
			$this->rst_empleado=parent::consultar($query);
		}
		
		public function Load_datos_por_usuario2($id_usuario){
			$query=sprintf("select * from empleado where idempleado=%s",$id_usuario);
			$this->rst_empleado=parent::consultar($query);
		}
		
		// Buscar datos del usuario en la tabla de empleado a partir de id de usuario
		public function cargaEmpleadoporIdusuario($id_usuario){
			$query = sprintf("SELECT * FROM empleado WHERE idfwk_usuario = '%s'",$id_usuario);
			$this->rst_empleado=parent::consultar($query);
		}
		
		// Busacar los datos del empleado uniendolos con los datos del usuario
		public function cargaDatosEmpleadoUsuario($id_usuario){
			$query = sprintf("SELECT * FROM empleado INNER JOIN usuario ON (u_id = idfwk_usuario) WHERE idfwk_usuario = '%s'",$id_usuario);
			$this->rst_empleado=parent::consultar($query);
		}
		
		public function  Get_dato($nombre){
			return(mysql_result($this->rst_empleado,0,$nombre));
		}
		
		/*
		 * Funcion que nos permitira tomar los datos de un empleado responsable de algun CC.
		 */
		public function Load_id_empleado($cc_responsable){
			$query=sprintf("SELECT idempleado FROM empleado WHERE idfwk_usuario=%s",$cc_responsable);
			$this->rst_empleado=parent::consultar($query);	
		}
		
	}
?>