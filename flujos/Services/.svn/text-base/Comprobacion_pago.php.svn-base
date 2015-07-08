<?php
require_once("Connections/fwk_db.php");

class Comprobacion_pago{
	
	public function Load_bancos(){
		$aux	= array();
		$cnn	= new conexion();
		$query	= "select * from bancos order by b_nombre";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["b_id"],"nombre"=>$fila["b_nombre"]));	
		}
		return($aux);
		
	}
	
	public function Carga_Datos($fecha,$fecha2){
		$aux	=array();
		$cnn	= new conexion();
		$query	=sprintf(" select  T.t_id as id,T.t_cancelado as cancelado, T.t_cerrado as seleccionado, concat(U.u_paterno,' ',U.u_materno,' ',U.u_nombre) as nombre, SG.sg_anticipo as monto,date(T.t_fecha_registro) as fecha  from solicitud_gastos SG inner join tramites T on (T.t_id=SG.sg_tramite) inner join usuario U on (U.u_id=T.t_iniciador) where SG.sg_referencia_comprobacion is not null and (T.t_fecha_registro >='%s 00:00:01' and T.t_fecha_registro <='%s 23:59:59');",$fecha, $fecha2);
		$rst	=$cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			$fila["seleccionado"]	=($fila["seleccionado"]	==true)?true:false;
			$fila["cancelado"]		=($fila["cancelado"]	==true)?true:false;
			$fila["editable"]		=($fila["seleccionado"]	==true)?false:true;
			array_push($aux,$fila);
		}
		return($aux);
	}
	
	public function Guardar($id, $aprobado=false,$cancelado=false){
		if($id>0){
			if($aprobado!=1)
			$aprobado=0;
			
			if($cancelado!=1)
			$cancelado=0;
			$aprobado=($cancelado==1)?0:$aprobado;
			$cnn	= new conexion();
			$query	=sprintf("UPDATE tramites set t_cerrado=%s, t_cancelado=%s, t_fecha_ultima_modificacion=now() where t_id=%s",$aprobado,$cancelado,$id);
			
			$cnn->insertar($query);
		}
		else{
		}
		return($id);
	}
}

?>