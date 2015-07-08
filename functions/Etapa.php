<?php
class Etapa extends conexion {
	protected $rst_etapa;
	
	public function __construct(){
		parent::__construct();
		$this->rst_etapa="";
	}
	
	public function Load_Etapa($id){
		$query				= sprintf("select * from etapas where et_id=%s",$id);
		$this->rst_etapa	= parent::consultar($query);
	}
	
	public function Load_Etapa_by_etapa_y_flujo($id_e,$id_f){
		$query				= sprintf("SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id=%s AND et_flujo_id=%s",$id_e,$id_f);
		$this->rst_etapa	= parent::consultar($query);
	}
	
	public function Get_dato($nombre){
		return(mysql_result($this->rst_etapa,0,$nombre));
	}
	
	
	public function Next_Set($et_id,$owner,$actual){
		$Empleado	= new Empleado();
		$resultado	=array();
		$query		= sprintf("select * from etapas where et_id=%s",$et_id);

		$aux		= parent::consultar($query);
				
		$etapa		= mysql_fetch_assoc($aux);
		
		if($etapa["et_finaliza"]==false){
		
			$query	= sprintf("select * from etapas where et_id='%s'",$etapa["et_siguiente_etapa"]);
					
			$aux	= parent::consultar($query);
					
					
			$etapa	= mysql_fetch_assoc($aux);
					
			$resultado["finaliza"]	= false;
			$resultado["etapa"]		= $etapa["et_id"];
			$resultado["empleado"]	= $Empleado->busca_profundidad($owner,$etapa["et_profundidad_responsable"]);
					
			return($resultado);
		}
		else{
			$resultado["empleado"]	=$actual;
			$resultado["etapa"]		=$et_id;
			$resultado["finaliza"]	=true;
					
		}
		return($resultado);
	}
	
	public function Add($nombre, $finaliza="false",$profundidad=0, $inicial="true", $id){
		$query=sprintf("
			insert into etapas (
				et_id,
				et_etapa,
				et_finaliza,
				et_profundidad_responsable,
				fe_inicial,
				fe_id
		
			)
			
			VALUES(
				default,
				'%s',
				%s,
				%s,
				%s,
				%s
			)",
			$nombre,
			$finaliza,
			$profundidad,
			$inicial,
			$id
		);
		
		
		$this->Load_Etapa(parent::insertar($query));
		
	}
	
	public function Modifica_siguiente_etapa($id,$id_siguiente){
		$query=sprintf("update etapas set et_id_siguiente_etapa=%s where et_id=%s",$id_siguiente,$id);
		parent::insertar($query);
	}
	
	public function Datos_Etapa($etapa){
		$rst=parent::consultar(sprintf("select * from etapas where et_id=%s",$etapa));
		return(mysql_fetch_assoc($rst));
		
		
	}
	public function Siguiente_Etapa($etapa){
		$query	=sprintf("select et_siguiente_etapa from etapas where et_id=%s",$etapa);
		$rst	=parent::consultar($query);
		$etapa	=mysql_result($rst,0,0);
		return($etapa);
	}
}
?>