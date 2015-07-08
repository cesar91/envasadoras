<?php

require_once("Connections/fwk_db.php");
require_once("Empresas.php");
require_once("ArrayCollection.php");


class Cancelaciones{
	
	function Rechazos(){	
		$aux	= array();
		$cnn	= new conexion();
		$query	= "SELECT * from descuento ";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("nombre"=>$fila["descuento"]));			
		}
		return($aux);	
	}
	
	function Descuentos(){	
		$aux	= array();
		$cnn	= new conexion();
		$query	= "SELECT * from rechazos ";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("nombre"=>$fila["re_motivo"]));			
		}
		return($aux);	
	}
}
?>