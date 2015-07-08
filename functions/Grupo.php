<?php
class Grupo extends conexion {
	protected $rstg;
	
	function __construct(){
		parent::__construct();
	}
	public function Load_data($id){
		$query=sprintf("select * from grupos where g_id=%s",$id);
		$this->rstg=mysql_fetch_assoc(parent::consultar($query));
	}
	
	public function Get_dato($nombre){
		return($this->rstg[$nombre]);
	}
	
	public function deleteAprobadores($id){
		$query=sprintf("update grupo_aprobadores set ga_activo=0 where ga_grupo=%s",$id);
		$var=parent::insertar($query);
	}
	
	public function addAprobador($usuario,$monto,$grupo,$nivel,$excepto,$flujo){
		$query = sprintf("insert into grupo_aprobadores set 
								ga_id=default,
								ga_usuario='%s',
								ga_monto=%s,
								ga_grupo=%s,
								ga_activo=true,
								ga_nivel=%s,
								ga_usuario_excepcion=%s,
								ga_flujo=%s
									
							  ",
								 $usuario,
								 $monto,
								 $grupo,
								 $nivel,
								 $excepto,
								 $flujo					
								  );
					parent::insertar($query);
	}
	//Para Aprobadores
	public function Load_data_Aprobadores($id){
		$query=sprintf("select * from grupo_aprobadores where ga_grupo=%s order by ga_nivel asc",$id);
		$var=parent::consultar($query);
		$arr= array();
		$arrGruposA=array();
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrGruposA,$arr);
		}
		return $arrGruposA;		
	}
	
	public function Get_dato_Aprobadores($nombre){
		return($this->rstga[$nombre]);
	}


	public function Load_all(){
		$arr= array();
		$query=sprintf("select * from grupos ");
		$arrGrupos=array();
		$var=parent::consultar($query);
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrGrupos,$arr);
		}
		return $arrGrupos;
	}
	
	
	public function add_Grupo($g_nombre){
			$query = sprintf("insert into grupos set
									g_id=default,
									g_nombre='%s',
									g_fecha_creacion=now(),
									g_activo=true
								  ",
								 $g_nombre
								  );
			return ( parent::insertar($query));
		}
}
?>
