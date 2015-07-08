<?php

require_once("Connections/fwk_db.php");
require_once("Empresas.php");
require_once("ArrayCollection.php");


class Centros{
	function Get_centros($empresa){
		$aux	=array();
		$cnn	= new conexion();
		$query	= sprintf(" SELECT e_nombre from empresas where e_id=%s;",$empresa);
		$rst	= $cnn->consultar($query);
		$emp	=mysql_fetch_assoc($rst);
		
		$query	=sprintf("select *  from centro_costo where cc_empresa ='%s' and cc_activo=true order by cc_nombre",$empresa);
		$rst	=$cnn->consultar($query);
		
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["cc_id"], "nombre"=>$fila["cc_nombre"], "clave"=>$fila["cc_clave"],"dueno"=>$fila["cc_dueno"],"backup"=>$fila["cc_backup"],"profit"=>$fila["cc_profit"],"segmento"=>$fila["cc_segmento"],"division"=>$fila["cc_division"]));
		}
		
		return(array("nombre"=>$emp["e_nombre"],"centros"=>$aux));
	}
	
	function Add($empresa,$nombre,$clave,$dueno,$backup,$profit,$segmento,$division){
		$cnn	= new conexion();
		$query	=sprintf("insert into centro_costo
						(
							cc_id, 
							cc_empresa, 
							cc_nombre, 
							cc_clave,
							cc_dueno,
							cc_backup,
							cc_profit,
							cc_segmento,
							cc_division
						)
		
						VALUES(
							default,
							%s,
							'%s',
							'%s',
							'%s',
							'%s',
							%s,
							'%s',
							'%s'								
						)
				",
					$empresa,
					$nombre,
					$clave,
					$dueno,
					$backup,
					$profit,
					$segmento,
					$division
				
				);
		return($cnn->insertar($query));
	}
	
	function Delete($id){
		$cnn	= new conexion();
		$query	= sprintf("update centro_costo set cc_activo=false where cc_id=%s",$id);
		$cnn->insertar($query);
		return("");
	}
	
	function Modificar($id,$nombre,$clave,$dueno, $backup,$profit,$segmento,$division){
		$cnn	= new conexion();
		$query        =sprintf("update centro_costo set cc_nombre='%s', cc_clave='%s', cc_dueno='%s', cc_backup='%s', cc_profit=%s, cc_segmento='%s', cc_division='%s'  where cc_id=%s",$nombre,$clave,$dueno,$backup,$profit,$segmento,$division,$id);
		$cnn->insertar($query);
		return("");
	}
	
	function Busca_CC_Dpto(){
		session_start();
		$Usu=$_SESSION["idusuario"];
		
		$aux	= array();
		$cnn	= new conexion();
		$query	= "SELECT cc_nombre,cc_clave FROM usuario inner join centro_costo ON(usuario.u_centro=centro_costo.cc_id)where usuario.u_id=".$Usu;
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("cc_nombre"=>$fila["cc_nombre"],"cc_clave"=>$fila["cc_clave"]));			
		}
		return($aux);	
	}
	
	
	function Busca_CC_Dpto_Actual($idDptoActual){	
		$aux	= array();
		$cnn	= new conexion();
		$query	= "SELECT * from solicitud_viaje where sv_tramite=".$idDptoActual;
		$rst	= $cnn->consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("cc_clave"=>$fila["sv_cc_clave"]));			
		}
		return($aux);	
	}

	function Busca_CC_Dpto_Actual_Gastos($idDptoActual){        
               $aux        = array();
               $cnn        = new conexion();
               $query        = "SELECT * from solicitud_gastos where sg_tramite=".$idDptoActual;
               $rst        = $cnn->consultar($query);
               while($fila=mysql_fetch_assoc($rst)){
                       array_push($aux,array("cc_clave"=>$fila["sg_cc_clave"]));
               }
               return($aux);        
       }

	function Busca_CC_Dpto_Actual_Pago($idDptoActual){   
        $aux    = array();
        $cnn    = new conexion();
        $query    = "SELECT * from solicitud_pago where sp_tramite=".$idDptoActual;
        $rst    = $cnn->consultar($query);
        while($fila=mysql_fetch_assoc($rst)){
            array_push($aux,array("cc_clave"=>$fila["sp_cc_clave"]));           
        }
        return($aux);   
    }

function Busca_CC_Dpto_Actual_Anticipo($idDptoActual){  
        $aux    = array();
        $cnn    = new conexion();
        $query    = "SELECT * from solicitud_anticipo where sa_tramite=".$idDptoActual;
        $rst    = $cnn->consultar($query);
        while($fila=mysql_fetch_assoc($rst)){
            array_push($aux,array("cc_clave"=>$fila["sa_cc_clave"]));          
        }
        return($aux);  
    }

}
?>
