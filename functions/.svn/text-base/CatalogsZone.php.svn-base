<?php
	class CatalogsZone extends conexion {
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
			$query=sprintf("select * from cat_regiones_conceptos where reco_id=%s",$id);
			
			$this->rst_catalogo=parent::consultar($query);
			if(parent::get_rows()>0){
				$this->dc_id=$this->Get_dato("reco_id");
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
		public function Add($nombre_zona, $select_zona){
			
			$aux			="";
			
			$query=sprintf("INSERT INTO cat_regiones_conceptos
						(
							reco_id,							
							reco_nombre,
							reco_pertenece_region,
							reco_activo
						)
						VALUES(
							default,
							'%s',
							%s,
							1
						)
					",
					utf8_decode($nombre_zona),
					$select_zona
															
			);	
			$this->dc_id=parent::insertar($query);			
			return($this->dc_id);
		}
		/*
		*Modificamos concepto
		*/
		public function Modifica_Concepto($nombre_zona,$select_zona, $idCat){
			$query=sprintf("update cat_regiones_conceptos set reco_nombre='%s', reco_pertenece_region=%s where reco_id=%s",utf8_encode($nombre_zona),$select_zona, $idCat);
			parent::insertar($query);
			
		}
		
		/**
		 * Eliminamos un concepto	
		 */
		 public function Delete($id){
			$query=sprintf("update cat_regiones_conceptos set reco_activo=false where reco_id=%s",$id);
			parent::insertar($query);
			return(true);		
		}		
	}
?>
