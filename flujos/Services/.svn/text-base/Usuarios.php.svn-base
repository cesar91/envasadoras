<?php
require_once("Connections/fwk_db.php");
class Usuarios{
	
	
	public function delete_usuario($id){
		$cnn	= new conexion();
		$query	= sprintf("update usuario set u_activo=false where u_id=%s",$id);
		$cnn->insertar($query);
		return("");
	}
	
	public function load_data_delete($id){
			$cnn	= new conexion();
			$aux	= array("id"=>0, "nombre"=>"", "empresa"=>"", "rol"=>"", "mensaje"=>"");
			$query	= sprintf("select u_id,concat(u_paterno,' ',u_materno,' ',u_nombre) as nombre, (select e_nombre from empresas where e_id=U.u_empresa) as empresa, (select r_nombre from  roles where r_id=U.u_rol) as rol from usuario U where u_id=%s",$id);
			$rst	= $cnn->consultar($query);
			if($cnn->get_rows()>0){
				$fila	= mysql_fetch_assoc($rst);
				$aux["id"]			= $fila["u_id"];
				$aux["nombre"]		= $fila["nombre"];
				$aux["empresa"]		= $fila["empresa"];
				$aux["rol"]			= $fila["rol"];
				$aux["mensaje"]		= "";
			}
			return($aux);
		}
	
	
	function Carga_tipos(){
		$aux	= array();
		$cnn	= new conexion();
		$query	= "select * from tipo_usuario";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["tu_id"],"nombre"=>$fila["tu_nombre"]));
			
		}
		return($aux);
		
	}
	
	function Carga_centros($empresa){
		$aux	= array();
		$cnn	= new conexion();
		$query	= sprintf("select cc_id,cc_nombre from centro_costo where cc_empresa=%s and cc_activo=true order by cc_nombre;",$empresa);
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["cc_id"],"nombre"=>$fila["cc_nombre"]));
			
		}
		return($aux);
		
		
	}
	
	function Carga_empresas(){
		$aux	= array();
		$cnn	= new conexion();
		$query	= "select * from empresas order  by e_nombre";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["e_id"],"nombre"=>$fila["e_nombre"]));
			
		}
		return($aux);
	}
	function Carga_Roles($empresa){
		$aux	= array();
		$cnn	= new conexion();
		$query	= "select * from roles where r_empresa =$empresa and r_activo=true order by r_nombre";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["r_id"],"nombre"=>$fila["r_nombre"]));
			
		}
		return($aux);
		
	}
	
	function Load_Bancos(){
		$aux	= array();
		$cnn	= new conexion();
		$query	= "select * from bancos order by b_nombre";
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["b_id"],"nombre"=>$fila["b_nombre"]));	
		}
		return($aux);
	}
	
	function Busca_Datos($usuario){
		
		if($usuario=="")
		$usuario=0;
		$cnn	=new conexion();
		$aux	=array("nombre"=>"","paterno"=>"","materno"=>"","email"=>"", "usuario"=>"", "passwd"=>"","rol"=>"0","empresa"=>"0","id"=>"","tipo"=>"1","centro"=>"", "banco"=>"0", "cuenta"=>"","cuentabancaria"=>"","bancotarjeta"=>"","cuentabanco"=>"","sap"=>"","asociado"=>"");
		$query	="select * from usuario where u_id=$usuario";
		
		$rst	=$cnn->consultar($query);
		if($cnn->get_rows()>0){
			
			$fila=mysql_fetch_assoc($rst);
			$aux["nombre"]			= $fila["u_nombre"];
			$aux["paterno"]			= $fila["u_paterno"];
			$aux["materno"]			= $fila["u_materno"];
			$aux["email"]			= $fila["u_email"];
			$aux["usuario"]			= $fila["u_usuario"];
			$aux["passwd"]			= $fila["u_passwd"];
			$aux["rol"]				= $fila["u_rol"];
			$aux["id"]				= $fila["u_id"];
			$aux["empresa"]			= $fila["u_empresa"];
			$aux["tipo"]			= $fila["u_tipo"];
			$aux["centro"]			= $fila["u_centro"];
			$aux["banco"]			= $fila["u_banco"];
			$aux["cuenta"]			= $fila["u_cuenta"];
			$aux["cuentabancaria"]	= $fila["u_cuenta_bancaria"];
			$aux["bancotarjeta"]	= $fila["u_banco_tarjeta"];
			$aux["cuentatarjeta"]	= $fila["u_cuenta_tarjeta"];
			$aux["sap"]    = $fila["u_sap"];
			$aux["asociado"]    = $fila["u_asociado"];
			
		}
		return($aux);
		
	}
	
	public function Add($id,$nombre,$paterno,$materno,$email,$usuario,$passwd,$rol,$empresa, $interno=true,$tipo=1,$centro,$banco,$cuenta,$cuentabancaria,$bancotarjeta, $cuentatarjeta,$sap,$asociado){
			$estado=0;
			$cnn	= new conexion();
			if($id!=""){ //UPDATE
				
					$query=sprintf(" select * from usuario where u_usuario ='%s' and u_passwd ='%s' and u_id!=%s",$usuario,$passwd,$id);
					$cnn->consultar($query);
					if($cnn->get_rows()>0){
						
						return("Ya existe un usuario con el mismo nombre de usuario y password. Por favor verifica los datos e intenta de nuevo");
					}		
				
				
					$estado=1;
					$query=sprintf("update usuario set 
							u_nombre='%s',
							u_paterno='%s',
							u_materno='%s',
							u_email='%s',
							u_usuario='%s',
							u_passwd='%s',
							u_rol=%s,
							u_empresa=%s,
							u_interno=%s,
							u_tipo=%s,
							u_centro=%s,
							u_banco=%s,
							u_cuenta='%s',
							u_cuenta_bancaria='%s',
							u_banco_tarjeta='%s',
							u_cuenta_tarjeta='%s',
							u_sap='%s',
							u_asociado='%s'
							
					where u_id=%s",
							$nombre,
							$paterno,
							$materno,
							$email,
							$usuario,
							$passwd,
							$rol,
							$empresa,
							$interno,
							$tipo,
							$centro,
							$banco,
							$cuenta,
							$cuentabancaria,
							$bancotarjeta,
							$cuentatarjeta,
							$sap,
							$asociado,
							$id
					
					);
			}
			else{ //ES NUEVO USUARIO
				
					$query=sprintf("insert into usuario
						(
							u_id,
							u_nombre,
							u_paterno,
							u_materno,
							u_email,
							u_usuario,
							u_passwd,
							u_rol,
							u_empresa,
							u_interno,
							u_activo,
							u_tipo,
							u_centro,
							u_banco,
							u_cuenta,
							u_cuenta_bancaria,
							u_banco_tarjeta,
							u_cuenta_tarjeta,
							u_sap,
							u_asociado
							
							
						)
						VALUES(
							default,
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							%s,
							%s,
							%s,
							true,
							%s,
							%s,
							%s,
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s'							
							
							
						)",
							$nombre,
							$paterno,
							$materno,
							$email,
							$usuario,
							$passwd,
							$rol,
							$empresa,
							$interno,
							$tipo,
							$centro,
							$banco,
							$cuenta,
							$cuentabancaria,
							$bancotarjeta,
							$cuentatarjeta,
							$sap,
							$asociado
					);
				
				
			}
			$id="";
			$id=$cnn->insertar($query);
			if($id="" && $estado=0){
				return("Error al Registrar el Usuario por favor verifique los datos");
			}
			return("");
			
	}
	
	
	function busca_CentroCosto_Usuarios($idUsu){
               $aux        = array();
               $cnn        = new conexion();
               $query        = "SELECT * FROM usuario where u_id=".$idUsu;
               $rst        = $cnn->consultar($query);
               while($fila=mysql_fetch_assoc($rst)){
                       array_push($aux,array("nombre"=>$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"],"id"=>$fila["u_id"]));
               }
               return($aux);        
       }
 
 	function busca_CentroCosto_UsuariosRet($idUsu){
               $aux        = array();
               $cnn        = new conexion();
               $query        = "SELECT * FROM usuario where u_id=".$idUsu;
               $rst        = $cnn->consultar($query);
               while($fila=mysql_fetch_assoc($rst)){
                       array_push($aux,array("nombre"=>$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"],"id"=>$fila["u_id"]));
               }
               return($aux);        
       }
 

	function busca_CentroCosto_Usuarios_Todos(){
               $aux        = array();
               $cnn        = new conexion();
               $query        = "SELECT * FROM usuario";
               $rst        = $cnn->consultar($query);
               while($fila=mysql_fetch_assoc($rst)){
                       array_push($aux,array("nombre"=>$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"],"id"=>$fila["u_id"]));
               }
               return($aux);        
       } 

	
	function busca_Jefe(){
               session_start();
               $Usu=$_SESSION["idusuario"];
               
               $aux        = array();
               $cnn        = new conexion();
               $query        = "select * from usuario where u_id in (select u_jefe from usuario where u_id =".$Usu.")";
               $rst        = $cnn->consultar($query);
               while($fila=mysql_fetch_assoc($rst)){
                       array_push($aux,array("id"=>$fila["u_id"],"nombre"=>$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"]));
               }
               return($aux);
       
       }
 
		
	function busca_Autorizador($autorizador){
		
		$aux	= array();
		$cnn	= new conexion();
		$query	= "select * from usuario where u_id in (select u_jefe from usuario where u_id =".$autorizador.")";
		$rst	= $cnn->consultar($query);

		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["u_id"],"nombre"=>$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"]));			
		}
		return($aux);	
	
	}
}
?>
