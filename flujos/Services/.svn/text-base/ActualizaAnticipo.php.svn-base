<?php

require_once("Connections/fwk_db.php");

class ActualizaAnticipo{

	   public function Modidicar($id,$tabla,$campo,$dato,$idTabla){		   
			$cnn        = new conexion();			                    			
			$cnn = new conexion();
			$dato=str_replace("$","",$dato);
			$query=sprintf("update %s set %s='%s'  where %s=%s ",$tabla,$campo,$dato,$idTabla,$id);
			$cnn->insertar($query);							
	  }     
}
?>
