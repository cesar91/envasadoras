<?php
	class ModeloAuto extends conexion {
		
		protected $rst_modelo;
				
       /*
         *  Regresa un arreglo con todos los datos de un modelo auto
         */    
		public function Load_modeloAuto(){
			$query=sprintf("select * from modelo_auto");
			$arr=array();
			$arrDato=array();
			$dato=parent::consultar($query);
			while($arrDatos=mysql_fetch_assoc($dato)){
				array_push($arr,$arrDatos);
			}
			return $arr;
		}
	
       	/**
		 * Regresamos un dato del Usuario
		 */
		public function Get_dato($nombre){
			return(mysql_result($this->rst_modelo,0,$nombre));
		}
    
    
        /*
         *  Regresa el nombre del usuario con el ID dado.
         */
        public function Get_Nombre($ma_id){
            $query=sprintf("SELECT ma_nombre FROM modelo_auto WHERE ma_id='%s'",$ma_id);
			$rst=parent::consultar($query);
            return(mysql_result($rst,0,"ma_nombre"));
        }
      
        /*
         *  Regresa el nombre del usuario con el ID dado.
         */
        public function Get_Factor($ma_id){
            $query=sprintf("SELECT ma_factor FROM modelo_auto WHERE ma_id='%s'",$ma_id);
			$rst=parent::consultar($query);
            return(mysql_result($rst,0,"ma_factor"));
        }
      
        /*
         *  Agrega un nuevo Modelo Auto
         */          
		public function add_ModeloAuto($nombre, $factor, $estatus){
			$query = sprintf("INSERT INTO modelo_auto (ma_nombre, ma_factor, ma_estatus) VALUES (
								  '%s', '%s', '%s')",
								  $nombre,
								  $factor,
								  $estatus
								  );
                                error_log($query);
					parent::ejecutar($query);
		}
		
	    /*
         *  Actualiza un Modelo de Auto
         */        
		public function update_Modelo_auto($ma_id,$nombre,$factor,$estatus){
			$query = sprintf("UPDATE modelo_auto SET ma_nombre='%s', ma_factor='%s', ma_estatus='%s' WHERE ma_id=%s",
								  $nombre,
								  $factor,
								  $estatus, 							  
								  $ma_id
								  );
                                  error_log($query);
					parent::ejecutar($query);
		}
      
}
?>
