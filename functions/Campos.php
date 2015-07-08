<?php
 class Campos extends conexion {
 	public function __construct(){
 		parent::__construct();
 	}
 	
 	public function New_Field($name,$label,$flujo){
 		$query=sprintf("insert into campos_flujo
 					(
 						ca_id,
 						ca_nombre_campo,
 						ca_etiqueta_campo,
 						ca_flujo
 					)
 					VALUES(
 						default,
 						'%s',
 						'%s',
 						%s
 					)",
 					$name,
 					$label,
 					$flujo
 				);
 		parent::insertar($query);
 		
 		
 	}
 	
 }
?>