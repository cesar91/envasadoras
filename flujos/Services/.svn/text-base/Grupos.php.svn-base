<?php
require_once("Connections/fwk_db.php");
require_once("Empresas.php");
require_once("ArrayCollection.php");

class Grupos{
	
	function Grupos(){
		
	}
	
	public function load_data_delete($id){
		$cnn	= new conexion();
		$aux	= array("id"=>0, "nombre"=>"", "empresa"=>"", "usuarios"=>0, "mensaje"=>"");
		$query	= sprintf(" select *, coalesce((select e_nombre from empresas where e_id=G.g_empresa),'UNIVERSAL') as empresa from grupos G where g_id=%s;",$id);
		$rst	= $cnn->consultar($query);
		if($cnn->get_rows()>0){
			$aux["id"]			= $fila["g_id"];
			$aux["nombre"]		= $fila["g_nombre"];
			$aux["empresa"]		= $fila["empresa"];
			$aux["usuarios"]	= $fila["usuarios"];
			$aux[""]			= $fila[""];
			
		}
		
		return($aux);
		
		
	}
	
	
	function Get_Data($id=0){
		$cnn	= new conexion();
		$aux	= array("id"=>"0","nombre"=>"","empresa"=>0,"datos"=>"");
		$query	= sprintf("select * from grupos where g_id=%s",$id);
		$rst	= $cnn->consultar($query);
		if($cnn->get_rows()>0){
			$fila			= mysql_fetch_assoc($rst);
			$aux["id"]		= $fila["g_id"];
			$aux["nombre"]	= $fila["g_nombre"];
			$aux["empresa"]	= $fila["g_empresa"];
			$aux["datos"]	= $this->Get_detalle($id);
			
		}
		return($aux);
		
	}
	private function Get_detalle($id){
		$cnn	= new conexion();
		$aux	= array();
		$query	= sprintf(" select U.u_id, concat(U.u_paterno,' ',U.u_materno,' ',U.u_nombre)as nombre from detalle_grupo DG inner join usuario U on (DG.dg_usuario=U.u_id) where DG.dg_grupo=%s  order by nombre;",$id);
		$rst	= $cnn->consultar($query);
		while($fila= mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["u_id"],"nombre"=>$fila["nombre"]));
			
		}
		return($aux);
		
	}
	
	
	
	
	function Load_Empresas(){
		$E= new Empresas();
		return($E->Load_empresas());
	}
	
	function Load_Usuarios(){
		$aux	= array();
		$cnn 	= new conexion();
		$query	="select * from usuario order by u_nombre, u_paterno, u_materno;";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			
			$nombre=$fila["u_nombre"] . " " . $fila["u_paterno"] . " " . $fila["u_materno"];
			array_push($aux,array("id"=>$fila["u_id"],"nombre"=>$nombre));
		}
		return($aux);
		
	}
	
	function Save_Data($id=0, $nombre, $empresa, $cadena){
		$empresa=($empresa=="")?"NULL":$empresa;
		$cnn	= new conexion();
		
		if($id>0){ //MOdificacion
			$query	=sprintf("update grupos set g_nombre='%s', g_empresa=%s where g_id=%s",$nombre,$empresa,$id);
			$cnn->insertar($query);
			$query	=sprintf("delete from detalle_grupo where dg_grupo=%s",$id);
			$cnn->insertar($query);
			$idgrupo=$id;
			
		}
		else{ //Nuevo Grupo
			$query	=sprintf("insert into grupos (g_id, g_nombre, g_empresa, g_fecha_creacion)
					  VALUES(default,'%s',%s,now())
			",$nombre,$empresa
			);
			$idgrupo= $cnn->insertar($query);
		}
		
		
		$usuarios=explode(",",$cadena);
		$aux="";
		foreach ($usuarios as $usuario){
			$query=sprintf("insert into detalle_grupo (dg_id, dg_usuario,dg_grupo) values(default,%s,%s);",$usuario,$idgrupo);
			$cnn->insertar($query);	
		}
		return(true);
		
	}
	
}
?>