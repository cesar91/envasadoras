<?php
	class Usuario extends conexion {
		
		protected $rst_usuario;
				
        /*
         *  Regresa un arreglo con todos los usuarios
         */
		public function Load_all(){
			$arr= array();
			$query=sprintf("select * from usuario order by u_nombre");
			$arrUsuarios=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrUsuarios,$arr);
			}
			return $arrUsuarios;
		}
	
        /*
         *  Regresa un arreglo con todos los tipos de usuarios
         */    
		public function Load_tipo_usuario(){
			$query=sprintf("select * from cat_tipo_usuario ");
			$arr=array();
			$arrDato=array();
			$dato=parent::consultar($query);
			while($arrDatos=mysql_fetch_assoc($dato)){
				array_push($arr,$arrDatos);
			}
			return $arr;
		}
		
		
		public function Load_tipo_usuario_id($id){
			$query=sprintf("SELECT ut_tipo,tu_nombre FROM usuario_tipo JOIN cat_tipo_usuario ON ut_tipo=tu_id WHERE ut_usuario='%s' ORDER BY ut_tipo",$id);
			$arr=array();
			$arrDato=array();
			$dato=parent::consultar($query);
			while($arrDatos=mysql_fetch_assoc($dato)){
				array_push($arr,$arrDatos);
				
			}
			return $arr;
		}
		
		public function Load_tipo_usuario_unico_id($id){
			$query=sprintf("SELECT ut_tipo FROM usuario_tipo WHERE ut_usuario='%s'",$id);
			$dato=parent::consultar($query);			
			while($arrDatos=mysql_fetch_assoc($dato)){
				$tipo=$arrDatos['ut_tipo'];
			}
			return $tipo;
		}
		
        /*
         *  Carga un usuario basado en su nombre de usuario
         */
		public function Load_Usuario($usuario){
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id inner join usuario_tipo on u_id = ut_usuario
				WHERE u_usuario='%s' group by idempleado",$usuario);
			$this->rst_usuario=parent::consultar($query);
			return parent::get_rows();
		}
		
		public function Load_Usuario_Edit($usuario){
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id 
				WHERE u_id='%s' group by idempleado",$usuario);
			$this->rst_usuario=parent::consultar($query);
			return parent::get_rows();
		}
		
		
        /*
         *  Carga un usuario basado en su ID
         */        
		public function Load_Usuario_By_ID($id){
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id inner join usuario_tipo on u_id = ut_usuario
                              WHERE u_id='%s'",$id);      
			$this->rst_usuario=parent::consultar($query);
			return parent::get_rows();
		}

		public function Load_Usuario_By_ID_Edit($id){
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id 
                              WHERE u_id='%s'",$id);      
            error_log($query);
			$this->rst_usuario=parent::consultar($query);
			return parent::get_rows();
		}
				
        /*
         *  Carga un usuario basado en su correo electronico
         */                   
		public function Load_Usuario_email($email){
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id 
                              WHERE  u_email='%s'",$email);              
			$this->rst_usuario_mail=parent::consultar($query);
			return parent::get_rows();
		}
		   
		/*
		 *  Regresa un arreglo con todos los usuarios apartir de un email
		*/
		public function Load_Usuarios_By_Email($email){
			$arr= array();
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id 
                              WHERE  u_email='%s'",$email);              
			$arrUsuarios=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrUsuarios,$arr);
			}
			return $arrUsuarios;
		}
		
		/*
         *  Carga los datos del Director general 
         */
		public function Load_Usuario_By_Clave($directorGeneral){
			$query=sprintf("SELECT * FROM empleado INNER JOIN usuario ON idfwk_usuario = u_id 
                              WHERE director_general='%s'",$directorGeneral);      
			$this->rst_usuario=parent::consultar($query);
			return parent::get_rows();
		}
		
		/**
		 * Regresamos un dato del Usuario
		 */
		public function Get_dato($nombre){
			return(mysql_result($this->rst_usuario,0,$nombre));
		}
    
    
        /*
         *  Regresa el nombre del usuario con el ID dado.
         */
        public function Get_Nombre($u_id){
            $query=sprintf("SELECT u_nombre FROM usuario WHERE u_id='%s'",$u_id);
			$rst=parent::consultar($query);
            return(mysql_result($rst,0,"u_nombre"));
        }
      
        /*
         *  Regresa el ID del primer usuario encontrado con el TIPO dado.
         */
        public function Get_Id_by_Tipo($ut_tipo){
            $query=sprintf("SELECT ut_usuario FROM usuario_tipo WHERE ut_tipo='%s'",$ut_tipo);
			$rst=parent::consultar($query);
            return(mysql_result($rst,0,"ut_usuario"));
        }
      
        /*
         *  Agrega un nuevo usuario
         */  		
		public function add_Usuario($u_empresa,$u_nom,$u_ap,$u_am,$u_usuario,$u_proveedor,$u_passwd,$u_email,$u_estatus,$u_cuentaporcobrar){
			$query = sprintf("INSERT INTO usuario (u_id, u_empresa, u_nombre,u_paterno,u_materno, u_usuario, u_proveedor, u_passwd, u_email,u_activo,u_producto) VALUES ( 
									NULL, '%s', '%s','%s','%s','%s', '%s', '%s', '%s','%s','%s')",
								  $u_empresa,
								  $u_nom,
								  $u_ap,
								  $u_am,								  
								  $u_usuario,
								  $u_proveedor,
								  $u_passwd,
								  $u_email,								  
								  $u_estatus,
								  $u_cuentaporcobrar
								  );
                    error_log($query);
					
			return ( parent::insertar($query));
		}
		
        /*
         *  Actualiza un usuario
         */          
		public function update_Usuario($u_empresa,$u_nom,$u_ap,$u_am,$u_usuario,$u_proveedor,$u_producto,$u_passwd,$u_email,$usuario,$u_estatus,$u_cuentaporcobrar){
		
			$query = sprintf("UPDATE usuario SET u_empresa=%s, u_nombre='%s',u_paterno='%s',u_materno='%s', u_usuario='%s', u_proveedor='%s', u_producto='%s', u_passwd='%s', u_email='%s', u_activo=%s, u_producto='%s' WHERE u_id=%s",
								  $u_empresa,
								  $u_nom,								  
								  $u_ap,
								  $u_am,
								  $u_usuario,
								  $u_proveedor,
								  $u_producto,
								  $u_passwd,
								  $u_email,								  
								  $u_estatus,
								  $u_cuentaporcobrar,								  
								  $usuario								  
								  );
                    error_log($query);					
			return ( parent::insertar($query));
		}
		
        /*
         *  Agrega un nuevo empleado
         */          
		public function add_Empleado($u_empresa,$u_nombre,$u_usuario,$iduser,$u_telefono,$u_tarjeta,$u_tarjeta_gas,$u_centrocosto,$u_puesto,$u_directorgeneral,$status){
			$query = sprintf("INSERT INTO empleado (idempleado,nombre, idfwk_usuario, telefono, notarjetacredito, notarjetacredito_gas, idcentrocosto, npuesto, fechaultimamod, fechacreacion, director_general, estatus) VALUES ( 
									default, '%s', %s, '%s', '%s', '%s', %s, '%s', now(), now(),%s,%s)",
								  //$idunidadnegocio,
								  $u_nombre,								  								  
								  $iduser,
								  $u_telefono,
								  $u_tarjeta,
								  $u_tarjeta_gas,
								  $u_centrocosto,								  
								  $u_puesto,					  
								  $u_directorgeneral,								  
								  $status
								  );
                                error_log($query);
					parent::insertar($query);
		}
		
		public function add_Usuario_Tipo($usuario,$id){
			$query = sprintf("INSERT INTO usuario_tipo (ut_id,ut_usuario,ut_tipo) VALUES ( 
									default,  '%s', '%s')",
								  //$idunidadnegocio,								  
								  $usuario,
								  $id
								  );
                                error_log($query);
					parent::insertar($query);
		}
		
		public function get_usuario_tipo_valores($usuario){
			$arr= array();
			$query=sprintf("SELECT ut_tipo FROM usuario_tipo WHERE ut_usuario=".$usuario);
			$arrUsuarios=array();
			$var=parent::consultar($query);
			while($arr=mysql_fetch_assoc($var)){
				array_push($arrUsuarios,$arr);
			}
			return $arrUsuarios;	
		}
		
		public function delete_Usuario_Tipo($usuario){
			$query = sprintf("delete from usuario_tipo where ut_usuario='%s'",
								  //$idunidadnegocio,								  
								  $usuario								  
								  );
                                error_log($query);
					parent::insertar($query);
		}
		
		public function  Load_dir_general(){
			$query = sprintf("SELECT MAX(`u_id`) as d1 FROM usuario u INNER JOIN empleado e ON u.u_id=e.idfwk_usuario WHERE director_general=1");
                    error_log($query);
  			$pp = mysql_fetch_assoc(parent::consultar($query));
            $pp_disponible = $pp['d1'];
            if ($pp_disponible>0)
            	return $pp_disponible;
            else
            	return 0;
		}
		
		public function add_Homog_dueno($usuario,$id){
			if ($id==4)
				$id=3000;
			if ($id==5)
				$id=1000;
			if ($id==6)
				$id=2000;
			$query = sprintf("INSERT INTO homologacion_dueno (hd_id,hd_au_id,hd_u_id) VALUES ( 
								default,  '%s', '%s')",								 							  
							  $id,
							  $usuario
							  );
                  error_log($query);
			parent::insertar($query);
		}
		
		public function delete_Homog_dueno($usuario){
			$query = sprintf("delete from homologacion_dueno where hd_u_id='%s'",
								  //$idunidadnegocio,								  
								  $usuario								  
								  );
                                error_log($query);
					parent::insertar($query);
		}
				
        /*
         *  Actualiza un empleado
         */        
		public function update_Empleado($nombre,$telefono,$notarjetacredito,$notarjetacreditogas,$idcentrocosto,$npuesto,$empleado,$u_directorgeneral,$status,$jefe){
			$query = sprintf("UPDATE empleado SET nombre='%s',telefono='%s', notarjetacredito='%s',notarjetacredito_gas='%s', 
                                                        idcentrocosto='%s',  npuesto='%s', fechaultimamod=now(), director_general=%s,  jefe=%s,  estatus=%s WHERE idempleado=%s",
								  //$idunidadnegocio,
								  $nombre,								  								  
								  $telefono,
								  $notarjetacredito,
								  $notarjetacreditogas,
								  $idcentrocosto,								  
								  $npuesto,								  
								  $u_directorgeneral,
								  $jefe,
								  $status,
								  $empleado								  
								  );								  
                                  error_log($query);
					parent::insertar($query);
		}
		
		/*
         *   Valida si las credenciales del usuario son correctas
         */
		public function Valida($user,$pass){		
			$query=sprintf("SELECT * FROM usuario WHERE u_usuario='%s' AND u_passwd='%s' AND u_activo = '1'",$user,$pass);
			$this->rst_usuario=parent::consultar($query);
			if(parent::get_rows()<=0){
				return (false);
			}
			$this->Load_Usuario(mysql_result($this->rst_usuario,0,8));
			return(true);	
		}
				
        /*
         *  Regresa el grupo de aprobadores para el usuario.
         */
        public function getGrupoAprobador(){
            $empleado_id = $this->Get_dato('idempleado');
            $query = sprintf("select * from grupos where g_id = (
                        select cc_grupo_id from cat_cecos where cc_id = (
                        select idcentrocosto from empleado where idempleado = %s
                        ))",$empleado_id);
            $grupo = mysql_fetch_assoc(parent::consultar($query));
            return $grupo;
        }
        
        /*
         *  Regresa el grupo de aprobadores para el ceco.
         */
        public function getGrupoAprobadorPorCeco($ceco){
            $query = sprintf("SELECT * FROM grupos WHERE g_id = ( SELECT cc_grupo_id FROM cat_cecos WHERE cc_id = '%s' )",$ceco);
            error_log($query);
            $grupo = mysql_fetch_assoc(parent::consultar($query));
            error_log(print_r($grupo, True));
            return $grupo;
        }        
        
        /*
         *  El algoritmo para aprobar una solicitud es el sig.
         * 
         *      1) Primero se checa si el prespuesto es suficiente, si no se 
         *         va a una excepcion de presupuesto, en la excepcion de presupuesto
         *         se checa que autorizador de excepcion lo tiene que hacer
         * 
         *      2) En base al grupo y la cantidad se determina el usuario
         *         que la debe aprobar
         */
        public function buscaAprobadorParaSolicitud($ceco, $total, $fecha){
            
            $ruta_autorizadores = array();
            
            // Busca grupo para el ceco
            $grupo = $this->getGrupoAprobadorPorCeco($ceco);
            
            // Buscar presupuesto para ceco y fecha
            $query = sprintf("SELECT * FROM periodo_presupuestal where pp_ceco = '%s' and '%s' >= pp_periodo_inicial and '%s' <= pp_periodo_final", $ceco, $fecha, $fecha);
            $pp = mysql_fetch_assoc(parent::consultar($query));
            $pp_disponible = $pp['pp_presupuesto_disponible'];
            
            
            if ($total > $pp_disponible){
                
                if ($total <= $grupo['usuario_excepcion_limite']){                    
                    array_push($ruta_autorizadores, $grupo['usuario_excepcion']);
                    return $ruta_autorizadores;           
                } else {                    
                    array_push($ruta_autorizadores, $grupo['usuario_excepcion']);
                    array_push($ruta_autorizadores, $grupo['usuario1_excepcion']);
                    return $ruta_autorizadores;                                             
                }             
            }

            if ($total <= $grupo['usuario1_limite']){
                
                if(isset($grupo['usuario1']) && $grupo['usuario1']!=NULL){
                    array_push($ruta_autorizadores, $grupo['usuario1']);
                }
                    
                 if(isset($grupo['usuario1a']) && $grupo['usuario1a']!=NULL){
                    array_push($ruta_autorizadores, $grupo['usuario1a']);    
                }  
                return $ruta_autorizadores;                              
            }

            if ($total <= $grupo['usuario2_limite']){
                
                
                if(isset($grupo['usuario1']) && $grupo['usuario1']!=NULL){
                    array_push($ruta_autorizadores, $grupo['usuario1']);
                }
                    
                 if(isset($grupo['usuario1a']) && $grupo['usuario1a']!=NULL){
                    array_push($ruta_autorizadores, $grupo['usuario1a']);    
                }    
                               
                if(isset($grupo['usuario2']) && $grupo['usuario2']!=NULL){
                    array_push($ruta_autorizadores, $grupo['usuario2']);
                }
                
                if(isset($grupo['usuario2a']) && $grupo['usuario2a']!=NULL){
                    array_push($ruta_autorizadores, $grupo['usuario2a']); 
                }

                return $ruta_autorizadores;               
            }
            
            if ($total <= $grupo['usuario3_limite']){
                
                if(isset($grupo['usuario1']) && $grupo['usuario1']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario1']);
                 if(isset($grupo['usuario1a']) && $grupo['usuario1a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario1a']);        
                               
                if(isset($grupo['usuario2']) && $grupo['usuario2']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario2']);
                 if(isset($grupo['usuario2a']) && $grupo['usuario2a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario2a']); 

                if(isset($grupo['usuario3']) && $grupo['usuario3']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario3']);
                 if(isset($grupo['usuario3a']) && $grupo['usuario3a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario3a']);

                return $ruta_autorizadores;               
            }     
            
            if ($total <= $grupo['usuario4_limite']){
                
                if(isset($grupo['usuario1']) && $grupo['usuario1']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario1']);
                 if(isset($grupo['usuario1a']) && $grupo['usuario1a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario1a']);        
                               
                if(isset($grupo['usuario2']) && $grupo['usuario2']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario2']);
                 if(isset($grupo['usuario2a']) && $grupo['usuario2a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario2a']); 
                    
                if(isset($grupo['usuario3']) && $grupo['usuario3']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario3']);
                 if(isset($grupo['usuario3a']) && $grupo['usuario3a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario3a']);                    

                if(isset($grupo['usuario4']) && $grupo['usuario4']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario4']);
                 if(isset($grupo['usuario4a']) && $grupo['usuario4a']!=NULL)
                    array_push($ruta_autorizadores, $grupo['usuario4a']);  

                return $ruta_autorizadores;               
            }                    
            
            
            if(isset($grupo['usuario1']) && $grupo['usuario1']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario1']);
             if(isset($grupo['usuario1a']) && $grupo['usuario1a']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario1a']);        
                           
            if(isset($grupo['usuario2']) && $grupo['usuario2']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario2']);
             if(isset($grupo['usuario2a']) && $grupo['usuario2a']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario2a']); 
                
            if(isset($grupo['usuario3']) && $grupo['usuario3']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario3']);
             if(isset($grupo['usuario3a']) && $grupo['usuario3a']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario3a']);                    

            if(isset($grupo['usuario4']) && $grupo['usuario4']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario4']);
             if(isset($grupo['usuario4a']) && $grupo['usuario4a']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario4a']);  
                
            if(isset($grupo['usuario5']) && $grupo['usuario5']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario5']);
             if(isset($grupo['usuario5a']) && $grupo['usuario5a']!=NULL)
                array_push($ruta_autorizadores, $grupo['usuario5a']);                 

            return $ruta_autorizadores;               
            
        }
        
        /*
         *  Regresa la agencia de viajes asignada al ceco
         */        
        public function buscaAgenciaViajesParaSolicitud($ceco){
            $grupo = $this->getGrupoAprobadorPorCeco($ceco);
            $autorizador = $grupo['usuario_agencia'];
            return $autorizador;                                  
        }        
 
        /*
        *  Regresa el cxp asignado al ceco
        */ 
        public function buscaAprobadorCxPParaComprobacion($ceco){
            $grupo = $this->getGrupoAprobadorPorCeco($ceco);
            $autorizador = $grupo['usuario_cxp'];
            return $autorizador;                                  
        }        
 
        /*
         *     Regresa el aprobador para la comprobacion
         */
        public function buscaAprobadorParaComprobacion($ceco, $total, $total_anticipo, $fecha){
                        
            if($total > $total_anticipo){
                
                $remanente = $total - $total_anticipo;

                // usamos la misma logica para autorizar el remanente que para la solicitud
                return $this->buscaAprobadorParaSolicitud($ceco, $remanente, $fecha);
                
            } else {
                
                // Busca grupo para el ceco
                $grupo = $this->getGrupoAprobadorPorCeco($ceco);
                array_push($ruta_autorizadores, $grupo['usuario_cxp']);
                return $ruta_autorizadores;              
            
            }                                    
        }          
		
		public function SetRechazaDelegado($id){
			
			$query=sprintf("select nt_remitente from notificaciones where nt_id='%s'",$id);
			$rst = parent::consultar($query);
			$iniciador= mysql_result($rst,0,0);
			
			$query=sprintf("select u_usuario from usuario where u_id=%s",$iniciador);
			$rst = parent::consultar($query);
			$iniciador2= mysql_result($rst,0,0);
			
			$query="update empleado set delegado='0' where idempleado='".$iniciador."'";
			$rst= parent::insertar($query);
			return $iniciador2;
		}
				
		public function SetAceptaDelegado($id){
						
			$query=sprintf("select nt_remitente from notificaciones where nt_id='%s'",$id);
			$rst = parent :: consultar($query);
			$iniciador= mysql_result($rst,0,0);
						
			$query=sprintf("select u_usuario from usuario where u_id=%s",$iniciador);
			$rst = parent ::consultar($query);
			$iniciador= mysql_result($rst,0,0);										
			
			$query="update notificaciones set nt_aceptado=1 where nt_id='".$id."'";
			$rst= parent ::insertar($query);
			return $iniciador;
		}
		
		public function add_delegacion($delegadoid,$delegadorid,$autorizador,$perfil){
			$query = sprintf("INSERT INTO usuarios_asignados
            (ua_id,
             id_asignador,
             id_delegado,
             privilegios,
			id_tipo	)
VALUES (default,
        '%s',
        '%s',
        '%s',
		'%s')",
					$delegadoid,
					$delegadorid,
					$autorizador,
					$perfil
			);
			error_log($query);
			parent::insertar($query);
		}
		
		public function find_delegaciones($uid){
			$query = sprintf("SELECT COUNT(ua_id) as cuenta FROM usuarios_asignados WHERE id_asignador='%s'", $uid);
			$rst2 = parent::consultar($query);
			$dato=mysql_result($rst2,0,"cuenta");
			if($dato>0)
				return true;
			else
				return false;
		}
		
		public function find_tipos($uid){
			$query = sprintf("SELECT COUNT(ut_usuario) as cuenta FROM usuario_tipo WHERE ut_usuario='%s'", $uid);
			$rst2 = parent::consultar($query);
 			$dato = mysql_result($rst2, 0, "cuenta");
 			if($dato>1)
				return true;
			else
				return false;
		}
		
		public function find_tipos_usuario($uid){
			$cnn = new conexion();
			$query = sprintf("SELECT ut_tipo, tu_nombre FROM usuario_tipo AS ut JOIN cat_tipo_usuario ON ut_tipo=tu_id WHERE ut_usuario='%s'", $uid);
			error_log($query);
			$rst2 = $cnn->consultar($query);
			$arr=array();
			while($dato=mysql_fetch_assoc($rst2)){
				array_push($arr,$dato);
			}
			return $arr;
		}
		
		public function find_esAgencia($uid){
			$cnn = new conexion();
			$query = sprintf("SELECT ut_tipo, tu_nombre FROM usuario_tipo AS ut JOIN cat_tipo_usuario ON ut_tipo=tu_id WHERE ut_usuario='%s'", $uid);
			$rst2 = $cnn->consultar($query);
			$arr=array();
			$esagencia=false;
			while($dato=mysql_fetch_assoc($rst2)){
				if($dato['tu_nombre']=="Agencia de viajes"){
					$esagencia=true;
				}
			}
			return $esagencia;
		}
		
		public function find_tipos_usuario_delegacion($uid,$delegado){
			$cnn = new conexion();
			$query = sprintf("SELECT id_tipo, tu_nombre FROM usuarios_asignados AS ut JOIN cat_tipo_usuario ON id_tipo=tu_id WHERE id_delegado='%s' AND id_asignador='%s'", $uid,$delegado);
			error_log($query);
			$rst2 = $cnn->consultar($query);
			$arr=array();
			while($dato=mysql_fetch_assoc($rst2)){
				array_push($arr,$dato);
			}
			return $arr;
		}
		
		public function getnombretipo($idtipo){
			$cnn = new conexion();
			$name="";
			$query = sprintf("SELECT tu_nombre FROM cat_tipo_usuario WHERE tu_id='%s'",$idtipo);
			error_log($query);
			$rst2 = $cnn->consultar($query);
			$name=mysql_result($rst2,0,'tu_nombre');
			error_log($name);
			return $name;
		}
		
		public function getidtipo($idtipo){
			$cnn = new conexion();
			$id="";
			$query = sprintf("SELECT tu_id FROM cat_tipo_usuario WHERE tu_id='%s'",$idtipo);			
			$rst2 = $cnn->consultar($query);
			while($dato=mysql_fetch_assoc($rst2)){
				$id=$dato['tu_id'];
			}			
			$id=(int)$id;
			return $id;
		}
		
		
		public function del_delegacion($delegadoid){
			$query = sprintf("DELETE FROM usuarios_asignados WHERE id_asignador='%s'",$delegadoid);
			error_log($query);
			parent::insertar($query);
		}
		
		public function getGerenteSFinanzas($tu_id){
			$gerenteSFinanzas="";
			$cnn = new conexion();
			$query=sprintf("SELECT ut_usuario FROM usuario_tipo WHERE ut_tipo='%s'",$tu_id);
			$rst2 = $cnn->consultar($query);
			while($dato=mysql_fetch_assoc($rst2)){
				$gerenteSFinanzas=$dato['ut_usuario'];
			}
			
			$gerenteSFinanzas=(int)$gerenteSFinanzas;
			return $gerenteSFinanzas;
		}		
		
		public function obtenerPrivilegios($idusuario, $iddelegado){
			$sql = "SELECT IF(privilegios=0,0,1) AS privilegios
					FROM usuarios_asignados 
					WHERE id_asignador = '$iddelegado' 
					AND id_delegado = '$idusuario'";
			$res = parent::consultar($sql);
			$row = mysql_fetch_assoc($res);
			return $row["privilegios"];
		}
}
?>