<?php
class Empresa extends Conexion {

	protected $rst_empresa;

	public function Get_Dato($nombre){
		return(mysql_result($this->rst_empresa,0,$nombre));
	}

	/**
	 *  Buscamos una empresa
	 */
	public function Load_Empresa($id){
        $query=sprintf("SELECT * FROM empresas WHERE e_codigo='%s'",$id);
        $this->rst_empresa=parent::consultar($query);        
	}
	
	/**
	 *  Buscamos una empresa por su Id
	 */
	public function Load_EmpresaporID($id){
        $query=sprintf("SELECT * FROM empresas WHERE e_id='%s'",$id);
        $this->rst_empresa=parent::consultar($query);        
	}
	
	/**
	 *  Borra una empresa
	 */
	public function Delete_Empresa($id){
        $query=sprintf("UPDATE empresas SET e_estatus = 0 WHERE e_id='%s'",$id);
        $this->rst_empresa=parent::ejecutar($query);        
	}    
    	
	/**
     * Crea una empresa
	 */
	public function Nueva_Empresa($nombre,$codigo,$estatus){
		
		$query=sprintf("INSERT INTO empresas
					(
						e_id,
						e_nombre,
						e_codigo,
						e_estatus						
					)
					VALUES(
						default,
						'%s',
						'%s',
						'%s'
					)",
					$nombre,
					$codigo,
					$estatus
			);
			$id=parent::insertar($query);
			$this->Load_Empresa($id);
	}
		
	/**
     * Edita una empresa
	 */
	public function Edita_Empresa($empresa_id,$nombre,$codigo,$status){
		
		$query=sprintf("UPDATE empresas SET e_nombre = '%s', e_codigo = '%s', e_estatus=%s WHERE e_id = %s",
					$nombre, $codigo, $status,$empresa_id
			);
			parent::ejecutar($query);
			$this->Load_Empresa($empresa_id);
	}        
        
    /*
     *  Carga todas las empresas
     */
	public function Load_all(){
		$arr= array();
		$query=sprintf("SELECT * FROM empresas");
		$arrEmp=array();
		$var=parent::consultar($query);
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrEmp,$arr);
		}
		return $arrEmp;
	}
	
	public function Load_all_activas(){
		$arr= array();
		$query=sprintf("SELECT * FROM empresas where e_estatus=1");
		$arrEmp=array();
		$var=parent::consultar($query);
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrEmp,$arr);
		}
		return $arrEmp;
	}
}
?>
