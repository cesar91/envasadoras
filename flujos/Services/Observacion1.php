<?php

require_once("Connections/fwk_db.php");

class Observacion{

       public function GetObservaciones($id){
error_log( "hola");
$file= 'km.txt';

	 	$cnn	= new conexion();
		$aux	= array();
		$asignado="";
			
		if($id>0){
				$query=sprintf("select * from notificaciones  where  nt_tramite=%s and nt_comentarios!='Ninguno'",$id);			
file_put_contents($file,$query);	
				$rst	= $cnn->consultar($query);
				
				while($fila=mysql_fetch_assoc($rst)){					
					array_push($aux,array("observacion"=>$fila["nt_comentarios"]." \n".$fila["nt_fecha"]));
				}
			}
    	  	return($aux);   
	          
	}
}
?>
