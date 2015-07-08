<?php
require_once("Connections/fwk_db.php");
require_once("Empresas.php");
class Tipo_Empresas{
	
	
	
	function carga_tipos(){
		$cnn	= new conexion();
		$aux	=array();
			
		$query=" select te_id, te_nombre from tipo_empresas order by te_nombre;";
		$rst=$cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,$fila);			
		}
		return($aux);
	}

	
	function load_data_delete($id){
		$cnn	= new conexion();
		$aux	= array("nombre"=>"", "banco"=>"", "cuenta"=>"", "procesos"=>0, "mensaje"=>"", "id"=>0);
		$query	=sprintf(" select *,(select b_nombre from bancos where b_id=TE.te_banco) as banco, (select count(*) from empresas where e_tipo=TE.te_id) as procesos from tipo_empresas TE where te_id=%s",$id);
		$rst	=$cnn->consultar($query);
		if($cnn->get_rows()>0){
			
			$fila	= mysql_fetch_assoc($rst);
			$aux["id"]			= $fila["te_id"];
			$aux["nombre"]		= $fila["te_nombre"];
			$aux["banco"]		= $fila["banco"];
			$aux["cuenta"]		= $fila["te_cuenta"];
			$aux["procesos"]	= $fila["procesos"];
			$aux["mensaje"]		= (intval($fila["procesos"])>0)?"Esta empresa no puede ser Eliminada, Hay aun procesos con esta empresa asignada":"";
		}
		return($aux);
		
	}
	
	function delete_tipo($id){
		$cnn	= new conexion();
		$query=sprintf("update tipo_empresas set te_activo =false where te_id=%s",$id);
		$cnn->insertar($query);
		return("");
		
	}
	
	function Carga_cuentas($tipo){
		$cnn	= new conexion();
		$aux	=array();
		$query	= sprintf("select B.b_id as id,B.b_nombre as banco,coalesce(EB.eb_cuenta,' ') as cuenta,coalesce(EB.eb_cuenta2,' ') as cuenta2, case when EB.eb_id is null then false else true end as activo from bancos B left outer join tipo_empresa_bancos EB on (EB.eb_banco=B.b_id and EB.eb_tipo=%s);",$tipo);
		$rst	=$cnn->consultar($query);
		while($fila = mysql_fetch_assoc($rst)){
			$fila["activo"]= ($fila["activo"]==1)?1:0;
			array_push($aux,$fila);
		}
		return($aux);
	}
	
	
	
	function Add_cuenta($tipo, $banco, $cuenta,$cuenta2){
		if(strval($cuenta)!="" && strval($cuenta2)!=""){
			if(intval($banco)==-1){
					$cnn	= new conexion;
					$query=sprintf("delete from tipo_empresa_bancos where eb_tipo=%s",$tipo);
					$cnn->insertar($query);
			}
			elseif(intval($tipo)>0){
				$cnn	= new conexion;
				$query=sprintf("insert into tipo_empresa_bancos (eb_id, eb_tipo, eb_banco, eb_cuenta, eb_cuenta2)
						values(default, %s,%s,'%s','%s')
				",
						$tipo,$banco,trim($cuenta),trim($cuenta2)
				);
				$cnn->insertar($query);
			}
		}
		
		return($tipo);			
	}
	
	function Load_Bancos(){
		$cnn 	= new conexion();
		$aux	= array();
		$query	=" select * from bancos;";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila['b_id'],"nombre"=>$fila["b_nombre"]));
		}
		return($aux);
	}
	
	
	
	function Carga_datos($id=0){
		$aux	= array("id"=>0,"nombre"=>"","banco"=>"","cuenta"=>"","usuario"=>"","activo"=>"", "clave"=>"", "cuenta_contable"=>"");
		$cnn	= new conexion();
		$query	= sprintf("select * from tipo_empresas where te_id=%s",$id);
		$rst	= $cnn->consultar($query);
		if($cnn->get_rows()>0){
			$fila	=mysql_fetch_assoc($rst);
			$aux["nombre"]			= $fila["te_nombre"];
			$aux["banco"]			= $fila["te_banco"];
			$aux["cuenta"]			= $fila["te_cuenta"];
			$aux["usuario"]			= $fila["te_usuario"];
			$aux["activo"]			= $fila["te_activo"];
			$aux["id"]				= $fila["te_id"];
			$aux["clave"]			= $fila["te_clave_empleador"];
			$aux["cuenta_contable"]	= $fila["te_cuenta_contable"];
		}
		return($aux);
	}
	
	
	
	function Add($id,$nombre,$banco,$cuenta,$clave,$cuenta_contable){
		$cnn	= new conexion();
		
		if($id>0){
			$query=sprintf("update tipo_empresas set te_nombre='%s', te_banco=%s, te_cuenta='%s', te_clave_empleador='%s', te_cuenta_contable='%s' where te_id=%s",
							$nombre,$banco,$cuenta,$clave,$cuenta_contable,$id
			);
			$cnn->insertar($query);
			return($id);
		}
		else{
			
		
			$query=sprintf("insert into tipo_empresas
						(
							te_id,
							te_nombre,
							te_banco,
							te_cuenta,
							te_clave_empleador,
							te_cuenta_contable
						)
						VALUES(
							default,
							'%s',
							%s,
							'%s',
							'%s',
							'%s'
						)
					",
						$nombre,
						$banco,
						$cuenta,
						$clave,
						$cuenta_contable
					);
				
					
			return($cnn->insertar($query));
		}
		
		
	}
	
}
?>