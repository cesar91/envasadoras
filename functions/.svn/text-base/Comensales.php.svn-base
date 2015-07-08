<?php
    require_once("$RUTA_A/lib/php/class.phpmailer.php");

	class Comensales extends conexion {
		protected $id_comensal;
		protected $rst_comensal;
		public function __construct(){
			parent::__construct();
			$this->id_comensal="";
		}
		
		/**
		 * Cargamos los datos de un comensal
		 *
		 * @param integer $id
		 */
		public function Load_comensal(){
			$query=sprintf("SELECT * FROM comensales");
			
			$this->rst_comensal=parent::consultar($query);
			if(parent::get_rows()>0){
				$this->id_comensal=$this->Get_dato("id_comensal");
			}
		}
		
		public function Load_comensales_solicitud_by_tramite($id){
			$arr= array();
			$query = sprintf("SELECT * FROM comensales 
				INNER JOIN solicitud_gastos ON (sg_id = c_solicitud) 
				INNER JOIN tramites ON (t_id = sg_tramite) 
				WHERE t_id = %s ORDER BY id_comensal",$id);
			$arrComensales=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrComensales,$arr);
			}
			return $arrComensales;
		}
		
		public function Load_comensales_comprobacion_by_tramite($id){
			$arr= array();
			$query = sprintf("SELECT * FROM comensales
					INNER JOIN comprobacion_gastos ON (co_id = c_comprobacion)
					INNER JOIN tramites ON (t_id = co_mi_tramite)
					WHERE t_id = %s ORDER BY id_comensal",$id);
			//error_log("--->>Query consulta invitados de comprobacion: ".$query);
			$arrComensales=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrComensales,$arr);
			}
			return $arrComensales;
		}
		
		// Cargar datos de los comensales de una solicitud
		public function Load_comensales_by_solicitud($idsolicitud){
			$arr= array();
			$query = sprintf("SELECT * FROM comensales WHERE c_solicitud = %s ORDER BY id_comensal", $idsolicitud);
			$arrComensales=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrComensales,$arr);
			}
			return $arrComensales;
		}
		
		// Cargar comensales de una comprobación
		public function Load_comensales_by_comprobacion($idcomprobacion){
			$arr= array();
			$query = sprintf("SELECT * FROM comensales WHERE c_comprobacion = %s ORDER BY id_comensal", $idcomprobacion);
			$arrComensales=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrComensales,$arr);
			}
			return $arrComensales;
		}
				
		/**
		 * Regresamos El valor de un campo de la Tabla de un Comensal previamente cargado
		 *
		 * @param text $nombre
		 * @return text
		 */
		public function Get_dato($nombre){
			return(mysql_result($this->rst_comensal,0,$nombre));
		}
		
      
		/**
		 *      Crea un nuevo comensal
		 */
		public function Crea_Comensal($solicitud, $nombreInvitado, $puestoInvitado, $empresaInvitado, $tipoInvitado, $comprobacion){
			$query=sprintf("INSERT INTO comensales ( 
				id_comensal, 
				c_solicitud, 
				c_nombre_invitado,
				c_puesto_invitado,
				c_empresa_invitado,
				c_tipo_invitado,
				c_comprobacion)
			VALUES( 
				default, 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s')",
				$solicitud,
				$nombreInvitado,
				$puestoInvitado,
				$empresaInvitado,
				$tipoInvitado,
				$comprobacion);	
            //error_log("--->>Insercion de comensales: ".$query);
			$this->id_comensal=parent::insertar($query);
			return($this->id_comensal);
		}
		
		public function Eliminar_Comensal_de_Solicitud($IdSolicitud){
			$query=sprintf("DELETE FROM comensales WHERE c_solicitud = %s", $IdSolicitud);
			parent::ejecutar($query);
		}
		
		public function Eliminar_Comensal_de_Comprobacion($IdComprobacion){
			$query=sprintf("DELETE FROM comensales WHERE c_comprobacion = %s", $IdComprobacion);
			parent::ejecutar($query);
		}
	}
?>
