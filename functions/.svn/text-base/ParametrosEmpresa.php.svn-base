<?php
class ParametrosEmpresa extends conexion {
	protected $pe_rst;
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Cargamos los datos de la tabla parametro_empresa
	 *
	 * @param integer $parametro
	 * @param integer $flujoempresa
	 */
	
	function Load_Data($parametro, $flujoempresa){
		
		$query=sprintf("select * from  parametro_empresa where pe_parametro=%s and pe_flujo_empresa=%s",$parametro,$flujoempresa);
		
		$this->pe_rst=parent::consultar($query);
	}
	
	/**
	 * Obtenemos el valor un campo de la tabla 
	 *
	 * @param text $nombre
	 * @return valor
	 */
	
	function Get_dato($nombre){
		return(mysql_result($this->pe_rst,0,$nombre));
		
	}
}
?>