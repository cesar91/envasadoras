<?php

class Divisa extends conexion {

	protected $rstd;

	function Load_data($id){
		$query=sprintf("select * from divisa where div_id=%s",$id);
		$this->rstd=mysql_fetch_assoc(parent::consultar($query));
	}
	
	function Load_divisa_by_nombre($nombre){
		$query=sprintf("select * from divisa where div_nombre='%s'",$nombre);
		$this->rstd=mysql_fetch_assoc(parent::consultar($query));
	}
	
	public function Load_all(){
		$arr= array();
		$query="select div_nombre as nombre, div_tasa as tasa FROM divisa  WHERE div_id>0 order by div_id ";
        $arrDivisa=array();
		$var=parent::consultar($query);
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrDivisa,$arr);
		}
		return $arrDivisa;
	}
	
	public function Get_dato($nombre){
		return($this->rstd[$nombre]);
	}
	
	public function Insertar_Divisa($nombre, $tasa){
		$query=sprintf("INSERT INTO divisa VALUES ( NULL, '%s', %s, now() )	", $nombre, $tasa );
        parent::insertar($query);
	}
    
	public function Update_Divisa($nombre, $tasa){
		$query=sprintf("UPDATE divisa SET div_tasa = %s, div_ultima_fecha_modificacion = NOW() WHERE div_nombre = '%s'", $tasa, $nombre );
        parent::insertar($query);
		switch ($nombre){
			case "MXN":
				$idDiv = 1;
				break;
			case "USD":
				$idDiv = 2;
				break;
			case "EUR":
				$idDiv = 3;
				break;
		}                 
        $query=sprintf("INSERT INTO historial_divisa (hd_tasa,hd_fecha_modificacion,div_id)VALUES ('%s',now(),'%s')", $tasa, $idDiv );      
        parent::insertar($query);        
	}    
    
	public function Get_Divisa($nombre){
		$query=sprintf("SELECT div_tasa FROM divisa WHERE div_nombre = '%s'", $nombre );
        $resultset = mysql_fetch_assoc(parent::consultar($query));
	}        
}
?>
