<?php
require_once "../Connections/fwk_db.php";

            class report {

      		function Load_mis_tramites($fecha,$fechallegada,$empresa,$usuario,$centro){
			$cnn	= new conexion();
			$aux	=array();
			$extra	="";
			if($empresa	>0){	$extra.=" and usuario.u_empresa in (select e_id from empresas where e_id={$empresa})";	}
			if($centro != ""){	$extra.=" and empleado.idcentrocosto='{$centro}'";			}
			if($usuario	>0){	$extra.=" and tramites.t_iniciador={$usuario}";		}
			
			$date=explode("/",$fecha);		
			$fecha=$date[2] ."-".$date[1]."-".$date[0];
			
			$date=explode("/",$fechallegada);		
			$fechallegada=$date[2] ."-".$date[1]."-".$date[0];
			
			$query	=sprintf("select date(t_fecha_registro)as fecha,case t_flujo when 1 then 'Sv' end,t_id as tramite,u_nombre as nombre,t_etiqueta as referencia,t_cancelado as cancelado, t_cerrado as activo,t_cerrado as cerrado  from tramites inner join usuario on (tramites.t_iniciador=usuario.u_id) inner join empleado on (empleado.nombre=usuario.u_nombre) where (t_fecha_registro >='%s 00:00:01' and t_fecha_registro<='%s 23:59:59') %s order by fecha;",$fecha,$fechallegada, $extra);
            $rst	=$cnn->consultar($query);
			$ruta	="../images/";
			while($fila=mysql_fetch_assoc($rst)){
				
				$fila["activo"]		=($fila["activo"]==false)?"{$ruta}ok.png":"{$ruta}action_cancel.gif";
				$fila["cerrado"]	=($fila["cerrado"]==true)?"{$ruta}ok.png":"{$ruta}action_cancel.gif";
				$fila["cancelado"]	=($fila["cancelado"]==true && $fila["activo"]==true )?"{$ruta}ok.png":"{$ruta}action_cancel.gif";
			
			
							
				/*switch($fila["t_flujo"]){
					case $fila=1:
						$fila="SV";
					break;
					case $fila=2:
						$fila="SA";
					break;
					case $fila=3:
						$fila="C";
					break;
				
				}*/
				array_push($aux,$fila);
				
			}
			return($aux);
					
		}
		

		
  function Reporte_Gastos($fechaInicio,$fechaFin,$empresa,$usuario,$centro,$concepto){
			$cnn	= new conexion();
			$aux	=array();
			            
            $extra	="";
			if($empresa	>0 && $empresa!='Todas'){
                $extra.=" and U.u_empresa in (select e_id from empresas where e_id='{$empresa}')";
            }
            
      		if($centro != "Todas"){
                $extra.=" and EP.idcentrocosto='{$centro}'";
            }
            
			if($usuario	!= "Todas"){
                $extra.=" and T.t_iniciador='{$usuario}'";	
            }
            
			if($concepto	!= "Todas"){
                $extra.=" and CC.dc_id='{$concepto}'";	
            }            

            // Convierte fechas a formato de mysql
			$date=explode("/",$fechaInicio);
			$fechaInicio=$date[2] ."-".$date[1]."-".$date[0];
			$date=explode("/",$fechaFin);
			$fechaFin=$date[2] ."-".$date[1]."-".$date[0];
            
            $query=sprintf("SELECT 
e_codigo,cc_centrocostos,u_nombre,cp_concepto, SUM(dc_total_aprobado) as total
FROM comprobaciones C 
INNER JOIN tramites T ON (C.co_mi_tramite=T.t_id)
INNER JOIN detalle_comprobacion DC on (C.co_id=DC.dc_comprobacion) 
INNER JOIN usuario U on(T.t_iniciador=U.u_id) 
INNER JOIN empleado EP on (U.u_id=EP.idfwk_usuario)
INNER JOIN cat_cecos CECO ON (EP.idcentrocosto = CECO.cc_id)
INNER JOIN empresas E on (U.u_empresa=E.e_id) 
INNER JOIN cat_conceptos CC on (DC.dc_concepto=CC.dc_id)
WHERE DC.dc_id=DC.dc_id 
  AND(C.co_fecha_registro >='%s 00:00:01' and C.co_fecha_registro<='%s 23:59:59') %s 
GROUP BY e_codigo,cc_centrocostos,u_nombre,cp_concepto", $fechaInicio, $fechaFin, $extra);
            //error_log($query);

			$rst=$cnn->consultar($query);
			while($fila=mysql_fetch_assoc($rst)){
                  	array_push($aux,$fila);
			}
			return($aux);
		}
		
	}//FIN DE CLASE
?>
