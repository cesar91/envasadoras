<?php
class Flujo extends conexion {
	
	protected $flujo_rst;
	protected $f_id;
	function __construct(){
		parent::__construct();
	}
	
	public function Load($flujo){
		$query=sprintf("select * from flujos where f_id=%s",$flujo);
		$this->flujo_rst=parent::consultar($query);
		
	}
	
	/**
	 * Retornamos un datos
	 *
	 * @param text $nombre
	 * @return valor
	 */
	
	public function Get_dato($nombre){
		return(mysql_result($this->flujo_rst,0,$nombre));
	}
	
}
?>