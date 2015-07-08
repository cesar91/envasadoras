<?php
	class CatalogsHotel extends conexion {
		protected $dc_id;
		protected $rst_catalogo;
		public function __construct(){
			parent::__construct();
			$this->dc_id="";
		}
		
		/**
		 * Cargamos los datos de un concepto
		 *
		 */
		public function Load_Catalogs($id){
			$query=sprintf("select * from cat_hoteles where hotel_id=%s",$id);
			
			$this->rst_catalogo=parent::consultar($query);
			if(parent::get_rows()>0){
				$this->dc_id=$this->Get_dato("hotel_id");
			}
		}
		
		
		/**
		 * Regresamos El valor de un campo de la Tabla de conceptos
		 *		
		 */
		public function Get_dato($nombre){
			return(mysql_result($this->rst_catalogo,0,$nombre));
		}
		
		/**
		 * Registramos un Nuevo concepto
		 *
		 */
		public function Add($nombre_hotel, $select_cd, $costo){
			
			$aux			="";
			
			$query=sprintf("INSERT INTO cat_hoteles
						(
							hotel_id,		
							hotel_nombre,
							hotel_ciudad,
							hotel_costo,
							hotel_activo						)
						VALUES(
							default,
							'%s',
							'%s',	
							 %s,							
							 1						)
					",
					$nombre_hotel,
					$select_cd,
					$costo
															
			);	
			$this->dc_id=parent::insertar($query);			
			return($this->dc_id);
		}
		/*
		*Modificamos concepto
		*/
		public function Modifica_Hotel($nombre_hotel,$cd, $costo, $idCat){
			$query=sprintf("update cat_hoteles set hotel_nombre='%s', hotel_ciudad='%s', hotel_costo='%s'  where hotel_id=%s", $nombre_hotel,$cd, $costo, $idCat);
			parent::insertar($query);
			
		}
		
		/**
		 * Eliminamos un concepto	
		 */
		 public function Delete($id){
			$query=sprintf("update cat_hoteles set hotel_activo=false where hotel_id=%s",$id);
			parent::insertar($query);
			return(true);		
		}		
	}
?>
