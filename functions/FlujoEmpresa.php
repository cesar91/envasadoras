<?php
class FlujoEmpresa extends conexion {
	protected $rst_flujoempresa;
	function __construct(){
		parent::__construct();
	}
	
	public function Load($id){
		$query=sprintf("select * from flujos_empresas where fe_id=%s",$id);
		$this->rst_flujoempresa=parent::consultar($query);
	}
	
	public function Get_dato($nombre){
		return(mysql_result($this->rst_flujoempresa,0,$nombre));
	}
	
	public function Load_por_empresa($empresa,$flujo=2){
		$query=sprintf("select * from flujos_empresas where e_id=%s and fe_flujo=%s",$empresa,$flujo);
		$this->rst_flujoempresa=parent::consultar($query);
	}
} 
?>