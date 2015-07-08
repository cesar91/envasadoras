<?php
require_once("Connections/fwk_db.php");

class Itinerario{
		
	public function Modificar_datos($id, $km,$dias){
		
		if($id>0){
			$cnn	= new conexion();
			$query=sprintf("update sv_itinerario set svi_kilometraje='%s' where svi_id=%s",$km,$id);
							
			$cnn->insertar($query);
			//file_put_contents("km.txt",$query);


			$query=sprintf("select svi_solicitud from sv_itinerario where svi_id=%s",$id);
                        $rst2                        = $cnn->consultar($query);
                        $idSv          = mysql_result($rst2,0,0);        
                        
                        $query=sprintf("update solicitud_viaje set sv_anticipo='%s', sv_dias_viaje='%s' where sv_id=%s",str_replace(",","",$anticipo),$dias,$idSv);
                        $cnn->insertar($query);
			
		}
		//$datos=file_get_contents("/var/www/e_expenses/salida.txt");
		//file_put_contents("/var/www/e_expenses/salida.txt",$datos . "\n***" . $id);
		return($id);	
	}
}
/*$I=new Itinerario();
$I->Modificar_datos(214,4);*/
?>
