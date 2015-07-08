<?php
	class CatalogsGral extends conexion {
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
			$query=sprintf("select * from cat_conceptos where dc_id=%s",$id);
			
			$this->rst_catalogo=parent::consultar($query);
			if(parent::get_rows()>0){
				$this->dc_id=$this->Get_dato("dc_id");
			}
		}
		
		
		
		public function Load_all(){
			$arr= array();
			$query=sprintf("select * from cat_conceptos order by cc_centrocostos ");
			$arrConceptos=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrConceptos,$arr);
			}
			return $arrConceptos;
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
		public function Add($concepto, $cuenta, $clasificacion,$catalogo){
			
			$aux			="";
			
			$query=sprintf("INSERT INTO cat_conceptos
						(
							dc_id,							
							cp_clasificacion,
							cp_concepto,
							cp_cuenta,
							cp_activo,
							dc_catalogo
						)
						VALUES(
							default,
							'%s',
							'%s',	
							'%s',
							1,
							%s				
						)
					",
					$clasificacion,
					$concepto,
					$cuenta,
					$catalogo
															
			);	
			$this->dc_id=parent::insertar($query);			
			return($this->dc_id);
		}
		/*
		*Modificamos concepto
		*/
		public function Modifica_Concepto($concepto,$cuenta, $clasificacion, $id){
			$query=sprintf("update cat_conceptos set cp_concepto='%s', cp_cuenta='%s', cp_clasificacion='%s' where dc_id=%s", $concepto, $cuenta, $clasificacion,$id);
			parent::insertar($query);
			
		}
		
		/**
		 * Eliminamos un concepto	
		 */
		 public function Delete($id){
			$query=sprintf("update cat_conceptos set cp_activo=false where dc_id=%s",$id);
			parent::insertar($query);
			return(true);		
		}		
	}
?>
