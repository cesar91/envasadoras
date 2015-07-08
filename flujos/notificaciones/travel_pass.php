<?php
/********************************************************
*         T&E Módulo de Edición de Agencia de Viajes    *
* Creado por:	Luis Daniel Barojas Cortes				*
* PHP, jQuery, JavaScript, CSS                      frm.rowCount.value    *
*********************************************************/

require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

// Convierte una fecha del formato "18/05/2011 08:46:05" al formato "2011-02-27"
function fecha_to_mysql($fecha){
    $pieces1 = explode(" ", $fecha);
    $pieces = explode("/", $pieces1[0]);
    return $pieces[2]."-".$pieces[1]."-".$pieces[0];
}

// Se rechazo la solicitud
if(isset($_POST["rechazar"]) && isset($_POST["tramite"])){
    require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");

    $idTramite = $_POST["tramite"];
    $observaciones = $_POST["observ"];

    // Actualiza el campo de observaciones
    $Csv=new C_SV();    
    $Csv->Load_Solicitud_Amex_Tramite($idTramite);
    $Csv->Modifica_Observaciones($idTramite, $observaciones, FLUJO_AMEX);    

    // Envia el tramite a cancelacion
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);    
    $iniciador = $tramite->Get_dato("t_iniciador");
    $aprobador = $tramite->Get_dato("t_dueno");
    $usuarioAgencia = new Usuario();
    $usuarioAgencia->Load_Usuario_By_ID($aprobador);   
    $mensaje = sprintf("La solicitud <strong>%05s</strong> ha sido <strong>CANCELADA</strong> por <strong>%s</strong>.",
                            $idTramite, $usuarioAgencia->Get_dato('nombre'));
    $tramite->EnviaMensajeSinMail($idTramite, $mensaje); // Notese que el mensaje se envia antes que se cambia la etapa                            
    $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_RECHAZADA, FLUJO_AMEX, $iniciador);
    
    header("Location: ./index.php?action=cancelada");
    
// Envia a autorizacion
} else if(isset($_POST["devolver"])) {

    if(isset($_POST['rowCount']) && $_POST['rowCount']!=0 && isset($_POST['iniciador']) && $_POST['iniciador']!="")
    {
        require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
                
        $CViaje= new C_SV();
        $cnn = new conexion();
        
        // Carga los datos del tramite
        $observaciones=$_POST['observ'];
        $idTramite=$_POST['tramite'];
        $nIniciador=$_POST['iniciador'];
        $Anticipo = $_POST['Total'];
        
        // Quita las comas
        $Anticipo = str_replace(',', '', $Anticipo);

        // Fecha de viaje (salida)
        $sFecha=$_POST['fecha_tramite'];        
        $FechaMySQL = fecha_to_mysql($sFecha);
        
        // Carga los datos del centro de costos
        $query_user = "SELECT * FROM usuario inner join empleado on (u_usuario = numempleado) where u_id = '$nIniciador'";
        $resUser = $cnn->consultar($query_user);
        $datos = mysql_fetch_assoc($resUser);        
        $centroCostos = $datos["idcentrocosto"];
        $cc = "SELECT * FROM cat_cecos WHERE cc_id = '$centroCostos'";
        $resCC = $cnn->consultar($cc);
        $idcentrocostos = mysql_result($resCC,0,"cc_id");
    
        // Buscamos quien debe aprobar esta solicitud
        $Us=new Usuario();
        $Us->Load_Usuario_By_No_Empleado($nIniciador);
        $ruta_autorizadores = $Us->buscaAprobadorParaSolicitud($centroCostos, $Anticipo, $FechaMySQL);
        $aprobador           = $ruta_autorizadores[0];
                                    
        // Carga los datos del itinerario
        for($i=1;$i<=$_POST['rowCount'];$i++)
        {
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario'.$i])){
                $nId_itinerario=$_POST['id_itinerario'.$i];
                $sTipoViaje=$_POST['tipo'.$i];
                $nNumvuelo=$_POST['numvuelo'.$i];
                $sAerolinea=$_POST['aerolinea'.$i];
                $nCostoViaje=$_POST['costoViaje'.$i];
                $nIva=$_POST['iva'.$i];
                $nTua=$_POST['tua'.$i];
                $nOtroImp=$_POST['otros_imp'.$i];
                $sSelect_hora_salida=$_POST['hora'.$i];
                $HoraSalida =$_POST['horaSalida'.$i];
                $apartadoBoleto = $_POST['apartado_boleto'.$i];
				
                $CViaje->Edit_Itinerario_amex($nId_itinerario,$sTipoViaje,$nNumvuelo,$sAerolinea,$nCostoViaje,$nIva,$nTua,$nOtroImp,$sSelect_hora_salida,$HoraSalida,$apartadoBoleto);
            }
        }//for	
        
        // Actualiza el campo de observaciones
        $Csv=new C_SV();    
        $Csv->Load_Solicitud_Amex_Tramite($idTramite);
        $Csv->Modifica_Observaciones($idTramite, $observaciones, FLUJO_AMEX);    

        // Actualiza el valor del anticipo en la tabla de amex
        $amexID = $Csv->Get_dato("sa_id");
        $queryMonto = "SELECT (SUM(sai_monto_vuelo) + SUM(sai_iva) + SUM(sai_tua) + SUM(sai_otrocargo)) AS monto FROM sa_itinerario s WHERE sai_solicitud = $amexID";
        //error_log($queryMonto);
        $rstMonto =$cnn->consultar($queryMonto);
        $monto = mysql_result($rstMonto,0,"monto");          
        $queryMontoActualizar = "UPDATE solicitud_amex SET sa_anticipo ='$monto' WHERE sa_id = $amexID";
        //error_log($queryMontoActualizar);
        $cnn->insertar($queryMontoActualizar);  

        // Envia el tramite a aprobacion
        $tramite = new Tramite();
        $tramite->Load_Tramite($idTramite);    
        $iniciador = $tramite->Get_dato("t_iniciador");
        $agencia = $tramite->Get_dato("t_dueno");
        $usuarioAgencia = new Usuario();
        $usuarioAgencia->Load_Usuario_By_ID($agencia);   
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador);   
        $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_APROBACION, FLUJO_AMEX, $aprobador, $ruta_autorizadores);
                
        // Se envia el mensaje a las 3 partes de la transaccion
        $mensaje = sprintf("La solicitud <strong>%05s</strong> ha sido <strong>COMPLETADA</strong> por <strong>%s</strong> y enviada a <strong>%s</strong> para su aprobaci&oacute;n.",
                                $idTramite, $usuarioAgencia->Get_dato('nombre'), $usuarioAprobador->Get_dato('nombre'));
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $agencia); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $aprobador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $iniciador); 
                     
        // Envia a aprobacion
        header("Location: ./index.php?action=devolver");
    }
    
    else
        header("Location: ./index.php?action=error");
        
// Muestra la pantalla de captura de datos de agencia de viajes
} else if(isset($_POST['devolver_to_emple'])){
	$texto = $_POST['observ_to_emple'];
	$t_id = $_POST["tramite"];
	$u_id = $_POST["iu"];

	$cnn = new conexion();
	$query = sprintf("insert into observaciones(
				ob_id,
				ob_texto,
				ob_fecha,
				ob_tramite,
				ob_usuario
			)values(
				default,
				'%s',
				now(),
				%s,
				%s
			)", 
				$texto,
				$t_id,
				$u_id
	);
	$ob_id = $cnn->insertar($query);
		
	$tramite = new Tramite();
	//Se modifica la etapa
	$aprobador = $u_id;
	$ruta_autorizadores = "";
	$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_APROBACION, FLUJO_SOLICITUD, $aprobador, $ruta_autorizadores);
	//Se envia la notificacion al empleado
	$mensaje = sprintf("La agencia de viajes ha realizado <strong>OBSERVACIONES</strong> sobre la solicitud <strong>%s</strong>.",$t_id);
	$destinatario = $_POST['id_empleado'];
	if($ob_id != ""){
		$tramite->EnviaNotificacion($t_id, $mensaje, $u_id, $destinatario); 
	}
    if( ((isset($_POST['rowCount']) && $_POST['rowCount']!=0)||(isset($_POST['rowCountHotel']) && $_POST['rowCountHotel']!=0)||(isset($_POST['rowCountAuto']) && $_POST['rowCountAuto']!=0)) && isset($_POST['iniciador']) && $_POST['iniciador']!="")
    {
		$mensaje2 = sprintf("La agencia de viajes ha realizado <strong>COTIZACIONES</strong> sobre la solicitud <strong>%s</strong>.",$t_id);
		if($ob_id != ""){
			$tramite->EnviaNotificacion($t_id, $mensaje2, $u_id, $destinatario); 
		}

		require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
                
        $CViaje= new C_SV();
        $cnn = new conexion();
        
        // Carga los datos del tramite
        $observaciones=$_POST['observ'];
        $idTramite=$_POST['tramite'];
        $nIniciador=$_POST['iniciador'];
        $Anticipo = $_POST['Total'];
        $Anticipo_hotel = $_POST['Total_hotel'];
        $Anticipo_auto = $_POST['Total_auto'];
        
        // Quita las comas
        $Anticipo = str_replace(',', '', $Anticipo);
        $Anticipo_hotel = str_replace(',', '', $Anticipo_hotel);
        $Anticipo_auto = str_replace(',', '', $Anticipo_auto);

        // Fecha de viaje (salida)
        $sFecha=$_POST['fecha_tramite'];        
        $FechaMySQL = fecha_to_mysql($sFecha);
        
        // Carga los datos del centro de costos
/*        $query_user = "SELECT * FROM usuario inner join empleado on (u_usuario = numempleado) where u_id = '$nIniciador'";
        $resUser = $cnn->consultar($query_user);
        $datos = mysql_fetch_assoc($resUser);        
        $centroCostos = $datos["idcentrocosto"];
        $cc = "SELECT * FROM cat_cecos WHERE cc_id = '$centroCostos'";
        $resCC = $cnn->consultar($cc);
        $idcentrocostos = mysql_result($resCC,0,"cc_id");*/
    
        // Buscamos quien debe aprobar esta solicitud
/*        $Us=new Usuario();
        $Us->Load_Usuario_By_No_Empleado($nIniciador);
        $ruta_autorizadores = $Us->buscaAprobadorParaSolicitud($centroCostos, $Anticipo, $FechaMySQL);
        $aprobador           = $ruta_autorizadores[0];*/
		$aprobador = $u_id;
                                    
        // Carga los datos del itinerario - avion
        for($i=1;$i<=$_POST['rowCount2']+$_POST['rowCount'];$i++){
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario'.$i])){
                $nId_itinerario=$_POST['id_itinerario'.$i];

                $sTipoViaje=$_POST['tipo'.$i];
                $sAerolinea=$_POST['aerolinea'.$i];
				$separar = explode("/",$_POST['fecha'.$i]);
					$sfechaSalida = $separar[2]."-".$separar[1]."-".$separar[0];
				if($_POST['fecha_llegada_input'.$i] != ""){
					$separar = explode("/",$_POST['fecha_llegada_input'.$i]);
						$sfechaLlegada = $separar[2]."-".$separar[1]."-".$separar[0];
				}else{
					$sfechaLlegada = $_POST['fecha_llegada_input'.$i];
				}
                $nCostoViaje=$_POST['costoViaje'.$i];
                $nIva=$_POST['iva'.$i];
                $nTua=$_POST['tua'.$i];
				$nCostoViajeTotal = $nCostoViaje + $nIva + $nTua;
                $tipo_aerolinea=$_POST['tipo_aerolinea'.$i];

				$CViaje->Load_Itinerario($nId_itinerario);    
				$nCostoViajeTotalCotizacion = $CViaje->Get_dato_itinerario("svi_monto_vuelo_total");
				if($nCostoViajeTotalCotizacion == ""){
					$nCostoViajeTotalCotizacion = $nCostoViajeTotal;
				}

				$itinerarioCotizado = $CViaje->Get_dato_itinerario("svi_cotizado");
				if($itinerarioCotizado == "0" || $itinerarioCotizado == 0){
				}else{
				}

				$CViaje->Edit_Itinerario_Viaje($nId_itinerario,$sTipoViaje,$sAerolinea,$nCostoViaje,$nCostoViajeTotal,$nCostoViajeTotalCotizacion,$nIva,$nTua,$sfechaSalida,$sfechaLlegada,$tipo_aerolinea,1);
            }
        }//for	
        // Carga los datos del itinerario - hotel
        for($i=1;$i<=$_POST['rowCountHotel2']+$_POST['rowCountHotel'];$i++){
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario_hotel'.$i])){
                $nId_itinerario=$_POST['id_itinerario_hotel'.$i];

                $ciudad_hotel=$_POST['ciudad_hotel'.$i];
                $nombre_hotel=$_POST['nombre_hotel'.$i];
                $noches_hotel=$_POST['noches_de_hospedaje'.$i];
                $caract_hab_hotel=$_POST['caracteristicas_habitacion'.$i];
                $subtotal_hotel=$_POST['subtotal_hotel'.$i];
                $otros_cargos_hotel=$_POST['otros_cargos'.$i];
                $no_reservacion_hotel=$_POST['no_de_reservacion'.$i];
                $hora_llegada_hotel=$_POST['hora_llegada'.$i];
                $hora_salida_hotel=$_POST['hora_salida'.$i];
                $costo_x_noche=$_POST['costo_x_noche'.$i];
				
				$CViaje->Edit_Itinerario_Viaje_Hotel($nId_itinerario, $ciudad_hotel, $nombre_hotel, $noches_hotel, $caract_hab_hotel, $subtotal_hotel, $otros_cargos_hotel, $no_reservacion_hotel, $hora_llegada_hotel, $hora_salida_hotel, $costo_x_noche);
            }
        }//for	
        // Carga los datos del itinerario - AUTO
        for($i=1;$i<=$_POST['rowCountAuto2']+$_POST['rowCountAuto'];$i++){
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario_auto'.$i])){
                $nId_itinerario=$_POST['id_itinerario_auto'.$i];

                $empresa_auto=$_POST['empresa_auto'.$i];
                $tipo_de_auto=$_POST['tipo_de_auto'.$i];
                $dias_de_renta_auto=$_POST['dias_de_renta'.$i];
                $costo_x_dia_auto=$_POST['costo_x_dia'.$i];
                $subtotal_auto=$_POST['subtotal_auto'.$i];
				
				$CViaje->Edit_Itinerario_Viaje_Auto($nId_itinerario, $empresa_auto, $tipo_de_auto, $dias_de_renta_auto, $costo_x_dia_auto, $subtotal_auto);
            }
        }//for	
        
        // Actualiza el campo de observaciones
        $Csv=new C_SV();    
/*        $Csv->Load_Solicitud_Amex_Tramite($idTramite);
        $Csv->Modifica_Observaciones($idTramite, $observaciones, FLUJO_AMEX);    */

        $Csv->Load_Solicitud_tramite($idTramite);
        // Actualiza el valor del anticipo en la tabla de amex
/*        $viajeID = $Csv->Get_dato("sv_id");
        $queryMonto = "SELECT (SUM(svi_monto_vuelo) + SUM(svi_iva) + SUM(svi_tua) + SUM(svi_otrocargo)) AS monto FROM sv_itinerario WHERE svi_solicitud = $viajeID";
		//error_log($queryMonto);
        $rstMonto =$cnn->consultar($queryMonto);
        $monto = mysql_result($rstMonto,0,"monto");
        $queryMontoActualizar = "UPDATE solicitud_viaje SET sv_anticipo ='$monto' WHERE sv_id = $viajeID";
        //error_log($queryMontoActualizar);
        $cnn->insertar($queryMontoActualizar);  
*/
        // Envia el tramite a aprobacion
/*        $tramite = new Tramite();
        $tramite->Load_Tramite($idTramite);    
        $iniciador = $tramite->Get_dato("t_iniciador");
        $agencia = $tramite->Get_dato("t_dueno");
        $usuarioAgencia = new Usuario();
        $usuarioAgencia->Load_Usuario_By_ID($agencia);   
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador);   
        $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_APROBACION, FLUJO_AMEX, $aprobador, $ruta_autorizadores);
                
        // Se envia el mensaje a las 3 partes de la transaccion
        $mensaje = sprintf("La solicitud <strong>%05s</strong> ha sido <strong>COMPLETADA</strong> por <strong>%s</strong> y enviada a <strong>%s</strong> para su aprobación.",
                                $idTramite, $usuarioAgencia->Get_dato('nombre'), $usuarioAprobador->Get_dato('nombre'));
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $agencia); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $aprobador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $iniciador); 
                     
        // Envia a aprobacion
        header("Location: ./index.php?action=devolver");*/

				//Se modifica la etapa
				$aprobador = $nIniciador;
				$ruta_autorizadores = "";
				$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_APROBACION, FLUJO_SOLICITUD, $aprobador, $ruta_autorizadores);
	}
	
	header("Location: ./index.php?id=".$t_id);
	
} else if(isset($_POST['comprar'])){
	$texto = $_POST['observ_to_emple'];
	$t_id = $_POST["tramite"];
	$u_id = $_POST["iu"];

	$cnn = new conexion();
	$query = sprintf("insert into observaciones(
				ob_id,
				ob_texto,
				ob_fecha,
				ob_tramite,
				ob_usuario
			)values(
				default,
				'%s',
				now(),
				%s,
				%s
			)", 
				$texto,
				$t_id,
				$u_id
	);
	$ob_id = $cnn->insertar($query);
		
	$tramite = new Tramite();
	//Se modifica la etapa
	$aprobador = $u_id;
	$ruta_autorizadores = "";
	$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_APROBACION, FLUJO_SOLICITUD, $aprobador, $ruta_autorizadores);
	//Se envia la notificacion al empleado
	$mensaje = sprintf("La agencia de viajes ha realizado <strong>OBSERVACIONES</strong> sobre la solicitud <strong>%s</strong>.",$t_id);
	$destinatario = $_POST['id_empleado'];
	if($ob_id != ""){
		$tramite->EnviaNotificacion($t_id, $mensaje, $u_id, $destinatario); 
	}
    if( ((isset($_POST['rowCount']) && $_POST['rowCount']!=0)||(isset($_POST['rowCountHotel']) && $_POST['rowCountHotel']!=0)||(isset($_POST['rowCountAuto']) && $_POST['rowCountAuto']!=0)) && isset($_POST['iniciador']) && $_POST['iniciador']!="")
    {
		$mensaje2 = sprintf("La agencia de viajes ha realizado <strong>COTIZACIONES</strong> sobre la solicitud <strong>%s</strong>.",$t_id);
		if($ob_id != ""){
			$tramite->EnviaNotificacion($t_id, $mensaje2, $u_id, $destinatario); 
		}

		require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
                
        $CViaje= new C_SV();
        $cnn = new conexion();
        
        // Carga los datos del tramite
        $observaciones=$_POST['observ'];
        $idTramite=$_POST['tramite'];
        $nIniciador=$_POST['iniciador'];
        $Etapa_del_tramite=$_POST['etapa_del_tramite'];
		
        $Anticipo = $_POST['Total'];
        $Anticipo_hotel = $_POST['Total_hotel'];
        $Anticipo_auto = $_POST['Total_auto'];
        
        // Quita las comas
        $Anticipo = str_replace(',', '', $Anticipo);
        $Anticipo_hotel = str_replace(',', '', $Anticipo_hotel);
        $Anticipo_auto = str_replace(',', '', $Anticipo_auto);

        // Fecha de viaje (salida)
        $sFecha=$_POST['fecha_tramite'];        
        $FechaMySQL = fecha_to_mysql($sFecha);
        
		$aprobador = $u_id;
		
		$exedeParametrosDeTolerancia = false;

        // Carga los datos del itinerario - avion
        for($i=1;$i<=$_POST['rowCount2']+$_POST['rowCount'];$i++){
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario'.$i])){
                $nId_itinerario=$_POST['id_itinerario'.$i];

                $sTipoViaje=$_POST['tipo'.$i];
                $sAerolinea=$_POST['aerolinea'.$i];
				$separar = explode("/",$_POST['fecha'.$i]);
					$sfechaSalida = $separar[2]."-".$separar[1]."-".$separar[0];
				if($_POST['fecha_llegada_input'.$i] != ""){
					$separar = explode("/",$_POST['fecha_llegada_input'.$i]);
						$sfechaLlegada = $separar[2]."-".$separar[1]."-".$separar[0];
				}else{
					$sfechaLlegada = $_POST['fecha_llegada_input'.$i];
				}
                $nCostoViaje=$_POST['costoViaje'.$i];
                $nIva=$_POST['iva'.$i];
                $nTua=$_POST['tua'.$i];
				$nCostoViajeTotal = $nCostoViaje + $nIva + $nTua;
                $tipo_aerolinea=$_POST['tipo_aerolinea'.$i];

				$CViaje->Load_Itinerario($nId_itinerario);    
				$nCostoViajeTotalCotizacion = $CViaje->Get_dato_itinerario("svi_monto_vuelo_total");
				if($nCostoViajeTotalCotizacion == ""){
					$nCostoViajeTotalCotizacion = $nCostoViajeTotal;
				}

				$itinerarioCotizado = $CViaje->Get_dato_itinerario("svi_cotizado");
				if($itinerarioCotizado == "0" || $itinerarioCotizado == 0){
				}else{
				}
				
				//Se checa si excede los parametros de tolerancia para el copsto del boleto de avion
				if($Etapa_del_tramite == 3 || $Etapa_del_tramite == '3'){
					if($sTipoViaje == "Nacional"){
						$parametroTolerancia = 500;
					}else if($sTipoViaje == "Continental" || $sTipoViaje == "Intercontinental"){
						$parametroTolerancia = 2000;
					}
					if(($nCostoViajeTotal-$nCostoViajeTotalCotizacion) > $parametroTolerancia){
						$exedeParametrosDeTolerancia = true;
					}
				}

				$CViaje->Edit_Itinerario_Viaje($nId_itinerario,$sTipoViaje,$sAerolinea,$nCostoViaje,$nCostoViajeTotal,$nCostoViajeTotalCotizacion,$nIva,$nTua,$sfechaSalida,$sfechaLlegada,$tipo_aerolinea,1);
            }
        }//for	
        // Carga los datos del itinerario - hotel
        for($i=1;$i<=$_POST['rowCountHotel2']+$_POST['rowCountHotel'];$i++){
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario_hotel'.$i])){
                $nId_itinerario=$_POST['id_itinerario_hotel'.$i];

                $ciudad_hotel=$_POST['ciudad_hotel'.$i];
                $nombre_hotel=$_POST['nombre_hotel'.$i];
                $noches_hotel=$_POST['noches_de_hospedaje'.$i];
                $caract_hab_hotel=$_POST['caracteristicas_habitacion'.$i];
                $subtotal_hotel=$_POST['subtotal_hotel'.$i];
                $otros_cargos_hotel=$_POST['otros_cargos'.$i];
                $no_reservacion_hotel=$_POST['no_de_reservacion'.$i];
                $hora_llegada_hotel=$_POST['hora_llegada'.$i];
                $hora_salida_hotel=$_POST['hora_salida'.$i];
                $costo_x_noche=$_POST['costo_x_noche'.$i];
				
				$CViaje->Edit_Itinerario_Viaje_Hotel($nId_itinerario, $ciudad_hotel, $nombre_hotel, $noches_hotel, $caract_hab_hotel, $subtotal_hotel, $otros_cargos_hotel, $no_reservacion_hotel, $hora_llegada_hotel, $hora_salida_hotel, $costo_x_noche);
            }
        }//for	
        // Carga los datos del itinerario - AUTO
        for($i=1;$i<=$_POST['rowCountAuto2']+$_POST['rowCountAuto'];$i++){
            //Partidas de Itinerario
            if(isset($_POST['id_itinerario_auto'.$i])){
                $nId_itinerario=$_POST['id_itinerario_auto'.$i];

                $empresa_auto=$_POST['empresa_auto'.$i];
                $tipo_de_auto=$_POST['tipo_de_auto'.$i];
                $dias_de_renta_auto=$_POST['dias_de_renta'.$i];
                $costo_x_dia_auto=$_POST['costo_x_dia'.$i];
                $subtotal_auto=$_POST['subtotal_auto'.$i];
				
				$CViaje->Edit_Itinerario_Viaje_Auto($nId_itinerario, $empresa_auto, $tipo_de_auto, $dias_de_renta_auto, $costo_x_dia_auto, $subtotal_auto);
            }
        }//for	
        
        // Actualiza el campo de observaciones
        $Csv=new C_SV();    

        $Csv->Load_Solicitud_tramite($idTramite);

        $tramite->Load_Tramite($idTramite);    
        $iniciador = $tramite->Get_dato("t_iniciador");
        $agencia = $tramite->Get_dato("t_dueno");
                
		$usuarioAgencia = new Usuario();
		$usuarioAgencia->Load_Usuario_By_ID($agencia);   

        // Se envia el mensaje a las 3 partes de la transaccion
        $mensaje = sprintf("La solicitud <strong>%05s</strong> ha sido <strong>COMPRADA</strong> por <strong>(AGENCIA) %s</strong>.",
                                $idTramite, $usuarioAgencia->Get_dato('nombre'));
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $agencia); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $agencia, $iniciador); 

	}
		$empleado= new Empleado();
		$empleado->Load_datos_por_usuario2($iniciador);
		$iniciador = $empleado->Get_dato("jefe");
				//Se modifica la etapa
				$aprobador = $_POST['iniciador'];
				$ruta_autorizadores = "";
				if($exedeParametrosDeTolerancia == true){
					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_APROBACION, FLUJO_SOLICITUD, $iniciador, $ruta_autorizadores);
				}else{
					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_COMPRADA, FLUJO_SOLICITUD, $aprobador, $ruta_autorizadores);
				}
	
	//header("Location: ./index.php?id=".$t_id);
    if($mobile){
        header("Location: ".$RUTA_R."inicial.php?action=comprar");   
    } else {
        // Regresa a la pagina de solicitudes    
        header("Location: ./index.php?action=comprar");  
    }    
} else if(isset($_GET['id']) && $_GET['id']!="") {

    require_once "../../Connections/fwk_db.php";
    $empleado = $_SESSION["idusuario"];
    $cnn = new conexion();
    $aux = array();
    $query = "select sv_id,
t_iniciador,
t_id,
date_format(t_fecha_registro,'%d/%m/%y %h:%i:%s'),
t_etiqueta,
sv_anticipo,
sv_dias_viaje,
sv_observaciones,
cc_centrocostos,
cc_nombre,
u.u_email,
t_etapa_actual
from solicitud_viaje as sv
inner join tramites as t
on (t.t_id=sv.sv_tramite)
left join cat_cecos as cc
on (sv.sv_ceco_paga = cc.cc_id)
inner join usuario as u
on t.t_iniciador = u.u_id
where t.t_id = {$_GET['id']}";
    //error_log($query);
    $rst = $cnn->consultar($query);
    while($datos=mysql_fetch_assoc($rst)){
        array_push($aux,$datos);
    }
    echo'
    <script language="javascript" type="text/javascript">
        //construyeTable();
    </script>';
    
    // Datos de la solicitud
    $id_Solicitud=mysql_result($rst,0,0);
    $iniciador=mysql_result($rst,0,1);
    $iniciadorT=mysql_result($rst,0,1);
    $tramite=mysql_result($rst,0,2);
    $fecha_tramite=mysql_result($rst,0,3);
    $motivo=mysql_result($rst,0,4);
    $anticipo=mysql_result($rst,0,5);
    $totaldias=mysql_result($rst,0,6);
    $observaciones=mysql_result($rst,0,7);
    $cc_centrocostos=mysql_result($rst,0,"cc_centrocostos");
    $cc_nombre=mysql_result($rst,0,"cc_nombre");
    $etapa_solicitud=mysql_result($rst,0,"t_etapa_actual");


    // Datos del usuario 
    $query=" SELECT * FROM usuario where u_id={$iniciador}";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $iniciador=$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"];
	$u_email=$fila["u_email"];

    // Datos del empleado
//    $query=" SELECT * FROM empleado INNER JOIN cat_cecos ON (idcentrocosto = cc_id) where numempleado='{$fila['u_usuario']}'";
    $query=" select * from empleado inner join cat_cecos on (idcentrocosto = cc_id) where idempleado='{$fila['u_id']}'";
    $rst=$cnn->consultar($query);    
    $fila=mysql_fetch_assoc($rst);
    $puesto=$fila["npuesto"];
    $telefono=$fila["telefono"];
    $centrocosto=$fila["idcentrocosto"];
    $centrocosto_nombre=$fila["cc_centrocostos"];
    $departamento=$fila["departamento"];
	$num_empleado=$fila["idempleado"];
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html>
    <head>
    <script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/dom-drag.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
    <script language="javascript" type="text/javascript">

		function centrar(elementoid){
			xpos = (screen.availWidth/2);
			ypos = (screen.availHeight/2);
			
			document.getElementById(elementoid).style.top = (document.documentElement.scrollTop+ypos) + "px";
			document.getElementById(elementoid).style.left = (document.documentElement.scrollLeft+xpos) + "px";
			document.getElementById(elementoid).style.visibility="visible";
		}
		$(window).resize();
		
		//validación campos numericos
		function validaNum(valor){
			cTecla=(document.all)?valor.keyCode:valor.which;
			if(cTecla==8) return true;
			patron=/^([0-9.]{1,2})?$/;
			cTecla= String.fromCharCode(cTecla);
			return patron.test(cTecla);
		}

        var doc;

        doc = $(document);
        doc.ready(inicializarEventos);//cuando el documento esté listo

        function inicializarEventos(){

        //ajusta tabla
        $("#table_datos").tablesorter({
                //cabeceras deshabilitadas del ordenamiento
                headers: { 
                    4: {sorter: false }, 
                    7: {sorter: false },
                    9: {sorter: false },
                    11:{sorter: false }  
                } //headers
            }); //tabla
            
            if(navigator.appName!='Netscape'){
                Drag.init(document.getElementById("ventanita"));
                document.getElementById('ventanita').style.cursor="move";
                //document.getElementById('ventanita').style.width="50%";
            }								
            
            $("#selectCd").change(function(){			
                $.post("../solicitudes/services/ingreso_sin_recargar_proceso.php",{ idCd:$(this).val() },
                function(data){
                    //$("#capselectHotel2").html(data);						
                    onloadHotels();
                    
                })
            });
                        
            
            $("#selectCd").autocomplete("../solicitudes/services/ingreso_sin_recargar_proceso.php", {
                minChars:1,
                matchSubset:1,
                matchContains:1,
                cacheLength:10,
                onItemSelect:seleccionaItem,
                //onFindValue:buscaRFC,
                formatItem:arreglaItem,
                autoFill:false,
                extraParams:{varaux:1}
            });//fin autocomplete
                    
        }//fin ready ó inicializarEventos
        
        function seleccionaItem(li) {
                onloadHotels();
        }//fin seleccionaItem
        
        function arreglaItem(row) {
            //da el formato a la lista
            return row[0];
        }//fin arreglaItem

		function verificar_tipo_boleto(tipo_boleto){
			if(tipo_boleto=="Sencillo" || tipo_boleto=="Varias escalas"){
				$("#fecha_llegada_label").css("display", "none");
				$("#fecha_llegada_input").css("display", "none");
			}else if(tipo_boleto=="Redondo"){
				$("#fecha_llegada_label").css("display", "block");
				$("#fecha_llegada_input").css("display", "block");
			}
		}
        
		function getTotal(){
			var frm=document.edit_itinerarios;
			if(frm.costo_x_noche.value=="" || frm.costo_x_noche.value==0){
				costo_x_noche_ = 0;
			}else{
				costo_x_noche_ = frm.costo_x_noche.value;
			}
			if(frm.noches_de_hospedaje.value=="" || frm.noches_de_hospedaje.value==0){
				noches_de_hospedaje_ = 0;
			}else{
				noches_de_hospedaje_ = frm.noches_de_hospedaje.value;
			}
			if(frm.otros_cargos.value=="" || frm.otros_cargos.value==0){
				otros_cargos_ = 0;
			}else{
				otros_cargos_ = frm.otros_cargos.value;
			}
			frm.total_hotel.value=parseFloat(costo_x_noche_)*parseFloat(noches_de_hospedaje_)+parseFloat(otros_cargos_);
		}
		function getTotal2(){
			var frm=document.edit_itinerarios;
			if(frm.costo_x_dia.value=="" || frm.costo_x_dia.value==0){
				costo_x_dia_ = 0;
			}else{
				costo_x_dia_ = frm.costo_x_dia.value;
			}
			if(frm.dias_de_renta.value=="" || frm.dias_de_renta.value==0){
				dias_de_renta_ = 0;
			}else{
				dias_de_renta_ = frm.dias_de_renta.value;
			}
			frm.total_auto.value=parseFloat(costo_x_dia_)*parseFloat(dias_de_renta_);
		}

        function flotanteActive(valor,id,salida,destino,hotel,auto,no_de_fila){
            var frm=document.edit_itinerarios;
            var dir="";
            id_table=parseInt($("#table_aprobado").find("tr:last").find("td").eq(0).html());
			
			var tipo_de_pasajero = "";
			var tipo_de_viaje = "";
			var tipo_de_vuelo = "";
			var svi_fecha_salida = "";
			var svi_fecha_llegada = "";
			var svi_noches_hospedaje = "";
			
			//HOTEL
			var svi_nombre_hotel = "";
			var svi_nombre_ciudad_hotel = "";
			var svi_caract_hotel = "";
			var svi_llegada_hotel = "";
			var svi_salida_hotel = "";
			var svi_otros_cargos_hotel = "";
			var svi_total_hotel = "";
			var svi_no_reservacion_hotel = "";
			var svi_costo_noche_hotel = "";
			//AUTO
			var svi_empresa_auto = "";
			var svi_tipo_auto = "";
			var svi_dias_renta_auto = "";
			var svi_costo_por_dia_auto = "";
			var svi_total_auto = "";
			
			var aux = 0;
            /*
            if(!isNaN(id_table)){
                document.getElementById('ventanita').style.visibility = 'hidden';
                document.getElementById('datosgrales').disabled = false;
                document.getElementById('datosgrales').style.opacity = 1;

                //Habilita botones
                document.getElementById('devolver').disabled = false;
                document.getElementById('rechazar').disabled = false;
                LimpiaCampos();
                }else */
            if (valor==1){
                //Checar si ya se habia agregado la cotizacion al itinerario
				cotizado_avion = 0; // 1 significa que ya se habia agregado la cotizacion, y 0 que no se habia agregado antes la cotizacion para este itinerario
				tab = document.getElementById('table_aprobado');
				for (i=1; fila = tab.getElementsByTagName('tr')[i]; i++){
					celda = fila.getElementsByTagName('td')[1].innerHTML.split("<");
					if(celda[0] == no_de_fila){
						cotizado_avion = 1;
						aux_avion = tab.getElementsByTagName('tr')[i];
					}
				}
                //Checar si ya se habia agregado la cotizacion al itinerario
				cotizado_hotel = 0; // 1 significa que ya se habia agregado la cotizacion, y 0 que no se habia agregado antes la cotizacion para este itinerario
				tab = document.getElementById('table_aprobado_hotel');
				for (i=1; fila = tab.getElementsByTagName('tr')[i]; i++){
					celda = fila.getElementsByTagName('td')[1].innerHTML.split("<");
					if(celda[0] == no_de_fila){
						cotizado_hotel = 1;
						aux_hotel = tab.getElementsByTagName('tr')[i];
					}
				}
                //Checar si ya se habia agregado la cotizacion al itinerario
				cotizado_auto = 0; // 1 significa que ya se habia agregado la cotizacion, y 0 que no se habia agregado antes la cotizacion para este itinerario
				tab = document.getElementById('table_aprobado_auto');
				for (i=1; fila = tab.getElementsByTagName('tr')[i]; i++){
					celda = fila.getElementsByTagName('td')[1].innerHTML.split("<");
					if(celda[0] == no_de_fila){
						cotizado_auto = 1;
						aux_auto = tab.getElementsByTagName('tr')[i];
					}
				}
				
                LimpiaCampos();
                document.getElementById('ventanita').style.visibility = 'visible';
                document.getElementById('datosgrales').disabled = true;
                document.getElementById('datosgrales').style.opacity = .3;
                
                //Deshabilita botones					
                document.getElementById('devolver').disabled = true;
                document.getElementById('rechazar').disabled = true;					

                //carga combo ciudades con el de itinerario por default
                //$("#selectCd").val(destino);

				if(cotizado_avion == 1){
					alert("Ya se había cotizado antes este itinerario.\nSe editara la cotización ingresada anteriormente.");
					  //for (j=0; celda = aux_avion.getElementsByTagName('td')[j]; j++)
						//alert (celda.innerHTML);
					
					//alert("aqui");
					
					celda = aux_avion.getElementsByTagName('td')[2].innerHTML.split(">");
					tipo_de_viaje = celda[1];

					//tipo_de_vuelo = "";
					//alert(tipo_de_vuelo);

					celda = aux_avion.getElementsByTagName('td')[4].innerHTML.split("<");
					svi_fecha_salida = celda[0];

					celda = aux_avion.getElementsByTagName('td')[5].innerHTML.split("<");
					svi_fecha_llegada = celda[0];

					celda = aux_avion.getElementsByTagName('td')[3].innerHTML.split(">");
					$('#aerolinea').val(celda[1]);

					celda = aux_avion.getElementsByTagName('td')[6].innerHTML.split(";");
					do{
						celda[1] = celda[1].replace(',','');
					}while(celda[1].indexOf(',') >= 0);
					$('#costoViaje').val(celda[1]);
					
					celda = aux_avion.getElementsByTagName('td')[7].innerHTML.split(">");
					do{
						celda[1] = celda[1].replace(',','');
					}while(celda[1].indexOf(',') >= 0);
					$('#iva').val(celda[1]);
					
					celda = aux_avion.getElementsByTagName('td')[8].innerHTML.split(">");
					do{
						celda[1] = celda[1].replace(',','');
					}while(celda[1].indexOf(',') >= 0);
					$('#tua').val(celda[1]);

					//Se guarda el numero de fila que se eliminara antes de guardar una nueva fila.
					celda = aux_avion.getElementsByTagName('td')[0].innerHTML.split("<");
					$('#fila_avion').val(celda[0]);
					
					celda = $('#tipo_aerolinea'+$('#fila_avion').val()).val();
					tipo_de_pasajero = celda;
					
					tipo_de_vuelo = $("#tipo_de_vuelo"+no_de_fila).val();
				}else{
					tipo_de_pasajero = $("#tipo_aerolinea"+no_de_fila).val();
					tipo_de_viaje = $("#svi_tipo_viaje"+no_de_fila).val();
					tipo_de_vuelo = $("#tipo_de_vuelo"+no_de_fila).val();
					svi_fecha_salida = $("#svi_fecha_salida"+no_de_fila).val();
					svi_fecha_llegada = $("#svi_fecha_llegada"+no_de_fila).val();
				}
				if(cotizado_hotel == 1){
					alert("Ya se había cotizado antes el hotel.\nSe editara la cotización ingresada anteriormente.");
					  //for (j=0; celda = aux_hotel.getElementsByTagName('td')[j]; j++)
						//alert (celda.innerHTML);

					celda = aux_hotel.getElementsByTagName('td')[2].innerHTML.split(">");
					svi_nombre_hotel = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[3].innerHTML.split(">");
					svi_nombre_ciudad_hotel = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[4].innerHTML.split(">");
					svi_noches_hospedaje = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[5].innerHTML.split(">");
					svi_caract_hotel = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[7].innerHTML.split(">");
					svi_llegada_hotel = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[9].innerHTML.split(">");
					svi_salida_hotel = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[12].innerHTML.split(">");
					svi_otros_cargos_hotel = celda[1];
					
					celda = aux_hotel.getElementsByTagName('td')[13].innerHTML;
					svi_total_hotel = celda;
					
					celda = aux_hotel.getElementsByTagName('td')[14].innerHTML.split(">");
					svi_no_reservacion_hotel = celda[1];
					
					//Se guarda el numero de fila que se eliminara antes de guardar una nueva fila.
					celda = aux_hotel.getElementsByTagName('td')[0].innerHTML.split("<");
					$('#fila_hotel').val(celda[0]);
					
					celda = $('#costo_x_noche'+$('#fila_hotel').val()).val();
					svi_costo_noche_hotel = celda;
				}else{
					svi_noches_hospedaje = $("#svi_noches_hospedaje"+no_de_fila).val();
				}
				if(cotizado_auto == 1){
					alert("Ya se había cotizado antes el auto.\nSe editara la cotización ingresada anteriormente.");
					  //for (j=0; celda = aux_auto.getElementsByTagName('td')[j]; j++)
						//alert (celda.innerHTML);
					
					celda = aux_auto.getElementsByTagName('td')[2].innerHTML.split(">");
					svi_empresa_auto = celda[1];
					celda = aux_auto.getElementsByTagName('td')[3].innerHTML.split(">");
					svi_tipo_auto = celda[1];
					celda = aux_auto.getElementsByTagName('td')[4].innerHTML.split(">");
					svi_dias_renta_auto = celda[1];
					celda = aux_auto.getElementsByTagName('td')[6].innerHTML.split(">");
					svi_costo_por_dia_auto = celda[1];
					celda = aux_auto.getElementsByTagName('td')[9].innerHTML;
					svi_total_auto = celda;

					//Se guarda el numero de fila que se eliminara antes de guardar una nueva fila.
					celda = aux_auto.getElementsByTagName('td')[0].innerHTML.split("<");
					$('#fila_auto').val(celda[0]);
				}else{
				}
				
				frm.id_itinerario.value=id;
				frm.fechasalida.value=salida;
				frm.consecutivo_de_itinerario.value=no_de_fila;

				frm.cotizado_avion.value=cotizado_avion;
				frm.cotizado_hotel.value=cotizado_hotel;
				frm.cotizado_auto.value=cotizado_auto;
				
				//Se selecciona el tipo de pasajero
				$('#tipo').children('option[value='+tipo_de_pasajero+']').attr('selected','selected').parent('select');
				//Se selecciona el tipo de viaje
				$('#select_tipo_viaje').children('option[value='+tipo_de_viaje+']').attr('selected','selected').parent('select');
				//Se selecciona el tipo de vuelo
				$('#select_tipo_viaje_pasaje').children('option[value='+tipo_de_vuelo+']').attr('selected','selected').parent('select');
				verificar_tipo_boleto(tipo_de_vuelo);
				//Se establecen las fechas del itinerario
				$('#fecha').val(svi_fecha_salida);
				$('#fecha_llegada_input').val(svi_fecha_llegada);
				//Setea las noches de hospedaje
				$('#noches_de_hospedaje').val(svi_noches_hospedaje);
				
				/* SE JALA LA COTIZACION DEL HOTEL A LA VENTANITA FLOTANTE */
				//Se establece el nombre de la ciudad del hotel
				$('#nombre_hotel').val(svi_nombre_hotel);
				//Se establece el nombre del hotel
				$('#ciudad_hotel').val(svi_nombre_ciudad_hotel);
				//Se establece las caracteristicas del hotel
				$('#caracteristicas_habitacion').val(svi_caract_hotel);
				//Se establece la llegada y salida del hotel
				$('#hora_llegada').val(svi_llegada_hotel);
				$('#hora_salida').val(svi_salida_hotel);
				//Se establece los otros cargos
				$('#otros_cargos').val(svi_otros_cargos_hotel);
				//Se establece el total
				$('#total_hotel').val(svi_total_hotel);
				//Se establece el numero de reservacion
				$('#no_de_reservacion').val(svi_no_reservacion_hotel);
				//Se establece el costo por noche
				$('#costo_x_noche').val(svi_costo_noche_hotel);
				
				/* SE JALA LA COTIZACION DEL AUTO A LA VENTANITA FLOTANTE */
				//Se establece la empresa del auto
				$('#empresa_auto').val(svi_empresa_auto);
				//Se establece el tipo de auto
				$('#tipo_de_auto').val(svi_tipo_auto);
				//Se estable el numero de dias de renta de auto
				$('#dias_de_renta').val(svi_dias_renta_auto);
				//Se establece el costo por dia de auto renta
				$('#costo_x_dia').val(svi_costo_por_dia_auto);
				//Se establece el total
				$('#total_auto').val(svi_total_auto);
				
				//Se hacen visibles las tablas de avion, hotel y auto segun corresponda
				$("#datos_avion").css("display", "block");
				document.getElementById('datos_avion').style.visibility = 'visible';
				if(hotel == 1){
					frm.hotel_chexk.value = "1";
					$("#datos_hotel").css("display", "block");
					document.getElementById('datos_hotel').style.visibility = 'visible';
				}else{
					frm.hotel_chexk.value = "0";
					$("#datos_hotel").css("display", "none");
					document.getElementById('datos_hotel').style.visibility = 'hidden';
				}
				if(auto == 1){
					frm.auto_chexk.value = "1";
					$("#datos_auto").css("display", "block");
					document.getElementById('datos_auto').style.visibility = 'visible';
				}else{
					frm.auto_chexk.value = "0";
					$("#datos_auto").css("display", "none");
					document.getElementById('datos_auto').style.visibility = 'hidden';
				}
				centrar('ventanita');
            } else {
                document.getElementById('ventanita').style.visibility = 'hidden';
                document.getElementById('datosgrales').disabled = false;
                document.getElementById('datosgrales').style.opacity = 1;
                                    
                //Habilita botones					
                document.getElementById('devolver').disabled = false;
                document.getElementById('rechazar').disabled = false;
                LimpiaCampos();

				document.getElementById('datos_avion').style.visibility = 'hidden';
				document.getElementById('datos_hotel').style.visibility = 'hidden';
				document.getElementById('datos_auto').style.visibility = 'hidden';
				$("#datos_avion").css("display", "none");
				$("#datos_hotel").css("display", "none");
				$("#datos_auto").css("display", "none");
            }
            
            //$("#aceptarnueva").attr("disabled", "disabled");
        }

        function construyePartidas(){
			var frm=document.edit_itinerarios;
			var hotel_ = frm.hotel_chexk.value;
			var auto_ = frm.auto_chexk.value;
			
			if(frm.aerolinea.value == ""){
				alert("Ingrese el nombre de la aerolínea.");
			}else if(parseFloat(frm.costoViaje.value) == 0 || frm.costoViaje.value == ""){
				alert("Ingrese el costo del vuelo.");
			}else if(frm.tua.value == ""){
				alert("Ingrese el TUA.");
			}else if(frm.fecha.value == ""){
				alert("Ingrese la fecha de salida.");
			}else if(frm.fecha_llegada_input.value == "" && frm.select_tipo_viaje_pasaje.value != "Sencillo" && frm.select_tipo_viaje_pasaje.value != "Varias escalas"){
				alert("Ingrese la fecha de llegada.");
			}else if(frm.ciudad_hotel.value == "" && hotel_ == "1"){
				alert("Ingrese la ciudad del hotel.");
			}else if(frm.nombre_hotel.value == "" && hotel_ == "1"){
				alert("Ingrese el nombre del hotel.");
			}else if((frm.noches_de_hospedaje.value == "" || parseFloat(frm.noches_de_hospedaje.value) == 0) && hotel_ == "1"){
				alert("Ingrese el número de noches.");
			}else if(frm.caracteristicas_habitacion.value == "" && hotel_ == "1"){
				alert("Ingrese las características de la habitación.");
			}else if(frm.hora_llegada.value == "" && hotel_ == "1"){
				alert("Ingrese la hora de llegada.");
			}else if(frm.hora_salida.value == "" && hotel_ == "1"){
				alert("Ingrese la hora de salida");
			}else if(frm.no_de_reservacion.value == "" && hotel_ == "1"){
				alert("Ingrese el número de reservación.");
			}else if((frm.costo_x_noche.value == "" || parseFloat(frm.costo_x_noche.value) == 0) && hotel_ == "1"){
				alert("Ingrese el costo por noche de hotel.");
			}else if(frm.otros_cargos.value == "" && hotel_ == "1"){
				alert("Ingrese otros cargos.");
			}else if(frm.empresa_auto.value == "" && auto_ == "1"){
				alert("Ingrese la empresa del auto.");
			}else if(frm.dias_de_renta.value == "" && auto_ == "1"){
				alert("Ingrese los días de renta del auto.");
			}else if(frm.costo_x_dia.value == "" && auto_ == "1"){
				alert("Ingrese el costo por día.");
			}else{
				if(frm.cotizado_avion.value == "1" || frm.cotizado_avion.value == 1){
					borrarPartida(frm.fila_avion.value);
				}
				construyePartida();
				if(hotel_ == "1"){
					if(frm.cotizado_hotel.value == "1" || frm.cotizado_hotel.value == 1){
						borrarPartidaHotel(frm.fila_hotel.value);
					}
					construyePartidaHotel();
				}
				if(auto_ == "1"){
					if(frm.cotizado_auto.value == "1" || frm.cotizado_auto.value == 1){
						borrarPartidaAuto(frm.fila_auto.value);
					}
					construyePartidaAuto();
				}
				flotanteActive(0);
			}
		}
		
        function construyePartida(){
            var frm=document.edit_itinerarios;
            //conteo de la partida
            filasTabla = frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
            frm.rowCount2.value=parseInt(frm.rowCount2.value)+parseInt(1);

            //Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
            id=parseInt($("#table_aprobado").find("tr:last").find("td").eq(0).html());
            if(isNaN(id)){
                id=1;
            }else{
                id+=parseInt(1);
            }

			if(frm.etapa_del_tramite.value == 3 || frm.etapa_del_tramite.value == '3'){
				//alert(frm.etapa_del_tramite.value);
				if(frm.select_tipo_viaje.value == "Nacional"){
					//alert(frm.select_tipo_viaje.value);
				}else if(frm.select_tipo_viaje.value == "Continental" || frm.select_tipo_viaje.value == "Intercontinental"){
					//alert(frm.select_tipo_viaje.value);
				}
			}

            //Creamos la nueva fila y sus respectivas columnas
    
            var row=frm.rowCount.value;
            var nuevaFila='<tr>';
            nuevaFila+="<td>"+id+"<input type='hidden' name='row"+id+"' id=row'"+id+"' value='"+id+"' readonly='readonly' /></td>";
            nuevaFila+="<td>"+frm.consecutivo_de_itinerario.value+"<input type='hidden' name='id_itinerario"+id+"' id=id_itinerario'"+id+"' value='"+frm.id_itinerario.value+"' readonly='readonly' /></td>";
            nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+frm.select_tipo_viaje.value+"'  readonly='readonly' />"+frm.select_tipo_viaje.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='aerolinea"+id+"' id='aerolinea"+id+"' value='"+frm.aerolinea.value+"' readonly='readonly' />"+frm.aerolinea.value+"</td>";
            nuevaFila+="<td>"+frm.fecha.value+"<input type='hidden' name='fecha"+id+"' id='fecha"+id+"' value='"+frm.fecha.value+"' readonly='readonly' /></td>";
            if(frm.fecha_llegada_input.value == ""){
				nuevaFila+="<td>N/A<input type='hidden' name='fecha_llegada_input"+id+"' id='fecha_llegada_input"+id+"' value='"+frm.fecha_llegada_input.value+"' readonly='readonly' /></td>";
			}else{
				nuevaFila+="<td>"+frm.fecha_llegada_input.value+"<input type='hidden' name='fecha_llegada_input"+id+"' id='fecha_llegada_input"+id+"' value='"+frm.fecha_llegada_input.value+"' readonly='readonly' /></td>";
			}
			
            //nuevaFila+="<td><input type='hidden' name='numvuelo"+id+"' id='numvuelo"+id+"' value='"+frm.numvuelo.value+"' readonly='readonly' />"+frm.numvuelo.value+"</td>";
            //nuevaFila+="<td><input type='hidden' name='horaSalida"+id+"' id='horaSalida"+id+"' value='"+frm.horaSalida.value+"'  readonly='readonly' />"+frm.horaSalida.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='costoViaje"+id+"' id='costoViaje"+id+"' value='"+frm.costoViaje.value+"' readonly='readonly' />$&nbsp;"+number_format(frm.costoViaje.value,2,".",",")+"</td>";
            //Ricardo: agrego los impuestos adicionales desglosados.
            //var monto_iva = frm.costoViaje.value * (frm.iva.value / 100);
            var monto_iva = frm.iva.value;
            nuevaFila+="<td><input type='hidden' name='iva"+id+"' id='iva"+id+"' value='"+monto_iva+"' readonly='readonly' />"+number_format(monto_iva,2,".",",")+"</td>";
            nuevaFila+="<td><input type='hidden' name='tua"+id+"' id='tua"+id+"' value='"+frm.tua.value+"' readonly='readonly' />"+number_format(frm.tua.value,2,".",",")+"</td>";
            nuevaFila+="<td>"+(parseFloat(frm.costoViaje.value)+parseFloat(monto_iva)+parseFloat(frm.tua.value))+"</td>";
            //nuevaFila+="<td><input type='hidden' name='apartado_boleto"+id+"' id='apartado_boleto"+id+"' value='"+frm.apartado_boleto.value+"' readonly='readonly' />"+frm.apartado_boleto.value+"</td>";
            nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='"+id+"'  onclick='borrarPartida(this.id);' style='cursor:pointer'/></div></td>";
            nuevaFila+= '</tr>';
			
            nuevaFila+="<input type='hidden' name='tipo_aerolinea"+id+"' id='tipo_aerolinea"+id+"' value='"+frm.tipo.value+"' readonly='readonly' />";
            
			//Ricardo: los impuestos ya no se le suman al subtotal, los removí.
            frm.anticipo.value=number_format(parseFloat(frm.anticipo.value.replace(",",""))+parseFloat(frm.costoViaje.value.replace(",","")),2,".",",");
            frm.ivaT.value=number_format((parseFloat(frm.ivaT.value.replace(",",""))+parseFloat(monto_iva)),2,".",",");
            //Ricardo: desglosado de impuestos.
            frm.tuaT.value=number_format(parseFloat(frm.tuaT.value.replace(",",""))+parseFloat(frm.tua.value.replace(",","")),2,".",",");
            frm.Total.value=number_format(parseFloat(frm.anticipo.value.replace(",",""))+parseFloat(frm.ivaT.value.replace(",",""))+parseFloat(frm.tuaT.value.replace(",","")),2,".",",");
            
            $("#table_aprobado").append(nuevaFila);
            
        }//construye partida
        
        function construyePartidaHotel(){
            var frm=document.edit_itinerarios;
            //conteo de la partida
            filasTabla = frm.rowCountHotel.value=parseInt(frm.rowCountHotel.value)+parseInt(1);
			frm.rowCountHotel2.value=parseInt(frm.rowCountHotel2.value)+parseInt(1);

            //Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
            id=parseInt($("#table_aprobado_hotel").find("tr:last").find("td").eq(0).html());
            if(isNaN(id)){	
                id=1;
            }else{
                id+=parseInt(1);
            }
            

            //Creamos la nueva fila y sus respectivas columnas
    
            var row=frm.rowCountHotel.value;
            var nuevaFila='<tr>';
            nuevaFila+="<td>"+id+"<input type='hidden' name='row"+id+"' id=row'"+id+"' value='"+id+"' readonly='readonly' /></td>";
            nuevaFila+="<td>"+frm.consecutivo_de_itinerario.value+"<input type='hidden' name='id_itinerario_hotel"+id+"' id=id_itinerario_hotel'"+id+"' value='"+frm.id_itinerario.value+"' readonly='readonly' /></td>";
            nuevaFila+="<td><input type='hidden' name='nombre_hotel"+id+"' id='nombre_hotel"+id+"' value='"+frm.nombre_hotel.value+"'  readonly='readonly' />"+frm.nombre_hotel.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='ciudad_hotel"+id+"' id='ciudad_hotel"+id+"' value='"+frm.ciudad_hotel.value+"' readonly='readonly' />"+frm.ciudad_hotel.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='noches_de_hospedaje"+id+"' id='noches_de_hospedaje"+id+"' value='"+frm.noches_de_hospedaje.value+"' readonly='readonly' />"+frm.noches_de_hospedaje.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='caracteristicas_habitacion"+id+"' id='caracteristicas_habitacion"+id+"' value='"+frm.caracteristicas_habitacion.value+"' readonly='readonly' />"+frm.caracteristicas_habitacion.value+"</td>";
            nuevaFila+="<td>"+frm.fecha.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='hora_llegada"+id+"' id='hora_llegada"+id+"' value='"+frm.hora_llegada.value+"' readonly='readonly' />"+frm.hora_llegada.value+"</td>";
            nuevaFila+="<td>"+frm.fecha_llegada_input.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='hora_salida"+id+"' id='hora_salida"+id+"' value='"+frm.hora_salida.value+"' readonly='readonly' />"+frm.hora_salida.value+"</td>";
            nuevaFila+="<td></td>";
			var subtotal_de_hotel = parseFloat(frm.noches_de_hospedaje.value)*parseFloat(frm.costo_x_noche.value);
            nuevaFila+="<td><input type='hidden' name='subtotal_hotel"+id+"' id='subtotal_hotel"+id+"' value='"+subtotal_de_hotel+"' readonly='readonly' />"+subtotal_de_hotel+"</td>";
            nuevaFila+="<td><input type='hidden' name='otros_cargos"+id+"' id='otros_cargos"+id+"' value='"+frm.otros_cargos.value+"' readonly='readonly' />"+frm.otros_cargos.value+"</td>";
            nuevaFila+="<td>"+(parseFloat(subtotal_de_hotel)+parseFloat(frm.otros_cargos.value))+"</td>";
            nuevaFila+="<td><input type='hidden' name='no_de_reservacion"+id+"' id='no_de_reservacion"+id+"' value='"+frm.no_de_reservacion.value+"' readonly='readonly' />"+frm.no_de_reservacion.value+"</td>";
            nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='"+id+"'  onclick='borrarPartidaHotel(this.id);' style='cursor:pointer'/></div></td>";
            nuevaFila+= '</tr>';

            nuevaFila+="<input type='hidden' name='costo_x_noche"+id+"' id='costo_x_noche"+id+"' value='"+frm.costo_x_noche.value+"' readonly='readonly' />";
            
            frm.anticipo_hotel.value=number_format(parseFloat(frm.anticipo_hotel.value.replace(",",""))+parseFloat(parseFloat(frm.noches_de_hospedaje.value)*parseFloat(frm.costo_x_noche.value)),2,".",",");
            frm.otrosT_hotel.value=number_format(parseFloat(frm.otrosT_hotel.value.replace(",",""))+parseFloat(frm.otros_cargos.value.replace(",","")),2,".",",");
            frm.Total_hotel.value=number_format(parseFloat(frm.anticipo_hotel.value.replace(",",""))+parseFloat(frm.otrosT_hotel.value.replace(",","")),2,".",",");
            
            $("#table_aprobado_hotel").append(nuevaFila);
            
        }//construye partida
        
        function construyePartidaAuto(){
            var frm=document.edit_itinerarios;
            //conteo de la partida
            filasTabla = frm.rowCountAuto.value=parseInt(frm.rowCountAuto.value)+parseInt(1);
			frm.rowCountAuto2.value=parseInt(frm.rowCountAuto2.value)+parseInt(1);

            //Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
            id=parseInt($("#table_aprobado_auto").find("tr:last").find("td").eq(0).html());
            if(isNaN(id)){	
                id=1;
            }else{
                id+=parseInt(1);
            }
            

            //Creamos la nueva fila y sus respectivas columnas
    
            var row=frm.rowCountAuto.value;
            var nuevaFila='<tr>';
            nuevaFila+="<td>"+id+"<input type='hidden' name='row"+id+"' id=row'"+id+"' value='"+id+"' readonly='readonly' /></td>";
            nuevaFila+="<td>"+frm.consecutivo_de_itinerario.value+"<input type='hidden' name='id_itinerario_auto"+id+"' id=id_itinerario_auto'"+id+"' value='"+frm.id_itinerario.value+"' readonly='readonly' /></td>";
            nuevaFila+="<td><input type='hidden' name='empresa_auto"+id+"' id='empresa_auto"+id+"' value='"+frm.empresa_auto.value+"' readonly='readonly' />"+frm.empresa_auto.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='tipo_de_auto"+id+"' id='tipo_de_auto"+id+"' value='"+frm.tipo_de_auto.value+"' readonly='readonly' />"+frm.tipo_de_auto.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='dias_de_renta"+id+"' id='dias_de_renta"+id+"' value='"+frm.dias_de_renta.value+"' readonly='readonly' />"+frm.dias_de_renta.value+"</td>";
            nuevaFila+="<td></td>";
            nuevaFila+="<td><input type='hidden' name='costo_x_dia"+id+"' id='costo_x_dia"+id+"' value='"+frm.costo_x_dia.value+"' readonly='readonly' />"+frm.costo_x_dia.value+"</td>";
            var subtotal_de_auto = parseFloat(frm.dias_de_renta.value)*parseFloat(frm.costo_x_dia.value);
			nuevaFila+="<td><input type='hidden' name='subtotal_auto"+id+"' id='subtotal_auto"+id+"' value='"+subtotal_de_auto+"' readonly='readonly' />"+subtotal_de_auto+"</td>";
            nuevaFila+="<td><input type='hidden' name='otros_cargos_auto"+id+"' id='otros_cargos_auto"+id+"' value='0.00' readonly='readonly' />0.00</td>";
            nuevaFila+="<td>"+(parseFloat(subtotal_de_auto)+parseFloat("0.00"))+"</td>";
            nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='"+id+"'  onclick='borrarPartidaAuto(this.id);' style='cursor:pointer'/></div></td>";
            nuevaFila+= '</tr>';
            
            frm.anticipo_auto.value=number_format(parseFloat(frm.anticipo_auto.value.replace(",",""))+parseFloat(parseFloat(frm.dias_de_renta.value)*parseFloat(frm.costo_x_dia.value)),2,".",",");
            //frm.otrosT_auto.value=number_format(parseFloat(frm.otrosT_auto.value)+parseFloat(frm.otros_cargos_auto.value.replace(",","")),2,".",",");
			frm.otrosT_auto.value = "0";
            frm.Total_auto.value=number_format(parseFloat(frm.anticipo_auto.value.replace(",",""))+parseFloat(frm.otrosT_auto.value.replace(",","")),2,".",",");
            
            $("#table_aprobado_auto").append(nuevaFila);
            
        }//construye partida
        
        function LimpiaCampos(){
            $("#numvuelo").val("");
            $("#horaSalida").val("");            
            $("#otros_imp").val("0.00");            
            $("#selectHotel").val("");
            $("#noches").val("");
            $("#otrocargo").val("0.00");           
            $('#select_medio_transporte').find('option:first').attr('selected', 'selected').parent('select');
            //$('#aerolinea').find('option:first').attr('selected', 'selected').parent('select');
            $('#select_hora_salida').find('option:first').attr('selected', 'selected').parent('select');
            $('#tipoHab').find('option:first').attr('selected', 'selected').parent('select');

            $('#select_tipo_viaje').find('option:first').attr('selected', 'selected').parent('select');
            $("#aerolinea").val("");
            $("#costoViaje").val("0.00");
			$('#tipo').find('option:first').attr('selected', 'selected').parent('select');
			$('#iva').val("0");
			$('#select_tipo_viaje_pasaje').find('option:first').attr('selected', 'selected').parent('select');
            $("#tua").val("0");
			$("#ciudad_hotel").val("");
            $("#noches_de_hospedaje").val("0");
            $("#nombre_hotel").val("");
            $("#caracteristicas_habitacion").val("");
            $("#costo_x_noche").val("0");
            $("#hora_llegada").val("");
            $("#otros_cargos").val("0");
            $("#hora_salida").val("");
            $("#total_hotel").val("0");
            $("#no_de_reservacion").val("");
            $("#empresa_auto").val("");
            $("#dias_de_renta").val("0");
			$('#tipo_de_auto').find('option:first').attr('selected', 'selected').parent('select');
            $("#costo_x_dia").val("0");
            $("#total_auto").val("0");

        }
        
        function borrarPartida(id){
            var firstRow=parseInt($("#table_aprobado").find("tr:first").find("td").eq(0).html());
            var lastRow=parseInt($("#table_aprobado").find("tr:last").find("td").eq(0).html());
            var frm=document.edit_itinerarios;
            frm.rowCount.value = lastRow - 1;
            var row=frm.rowCount.value;

            // Obtiene los valores de la partida a eliminar
            var costoViaje = parseFloat($("#costoViaje"+id).val());
            var iva        = parseFloat($("#iva"+id).val());
            var tua        = parseFloat($("#tua"+id).val());
            var otrocargo  = parseFloat($("#otros_imp"+id).val());
            
            // Calcula el valor nuevo de cada campo
            var costoViajeTotal = parseFloat(frm.anticipo.value.replace(",","")) - costoViaje;
            var ivaTotal        = parseFloat(frm.ivaT.value.replace(",","")) - iva;
            var tuaTotal        = parseFloat(frm.tuaT.value.replace(",","")) - tua;
            var Total = costoViajeTotal + ivaTotal + tuaTotal;
            
            // Pone los valores afectados
            frm.anticipo.value = number_format(costoViajeTotal,2,".",",");
            frm.ivaT.value     = number_format(ivaTotal,2,".",",");
            frm.tuaT.value     = number_format(tuaTotal,2,".",",");
            frm.Total.value    = number_format(Total,2,".",",");

            $("#costoViaje"+id).parent().parent().fadeOut("normal", function () { 
                $(this).remove();                                    
            });

            return false;
    
        }//borrar partida

        function borrarPartidaHotel(id){
            var firstRow=parseInt($("#table_aprobado_hotel").find("tr:first").find("td").eq(0).html());
            var lastRow=parseInt($("#table_aprobado_hotel").find("tr:last").find("td").eq(0).html());
            var frm=document.edit_itinerarios;
            frm.rowCountHotel.value = lastRow - 1;
            var row=frm.rowCountHotel.value;

            // Obtiene los valores de la partida a eliminar
            var subtotal_hotel = parseFloat($("#subtotal_hotel"+id).val());
            var otros_cargos  = parseFloat($("#otros_cargos"+id).val());
            
            // Calcula el valor nuevo de cada campo
            var anticipo_hotel = parseFloat(frm.anticipo_hotel.value.replace(",","")) - subtotal_hotel;
            var otrosT_hotel  = parseFloat(frm.otrosT_hotel.value.replace(",","")) - otros_cargos;
            var Total_hotel = anticipo_hotel + otrosT_hotel;
            
            // Pone los valores afectados
            frm.anticipo_hotel.value = number_format(anticipo_hotel,2,".",",");
            frm.otrosT_hotel.value   = number_format(otrosT_hotel,2,".",",");                    
            frm.Total_hotel.value    = number_format(Total_hotel,2,".",",");

            $("#subtotal_hotel"+id).parent().parent().fadeOut("normal", function () { 
                $(this).remove();                                    
            });

            return false;
    
        }//borrar partida

        function borrarPartidaAuto(id){
            var firstRow=parseInt($("#table_aprobado_auto").find("tr:first").find("td").eq(0).html());
            var lastRow=parseInt($("#table_aprobado_auto").find("tr:last").find("td").eq(0).html());
            var frm=document.edit_itinerarios;
            frm.rowCountAuto.value = lastRow - 1;
            var row=frm.rowCountAuto.value;

            // Obtiene los valores de la partida a eliminar
            var subtotal_auto = parseFloat($("#subtotal_auto"+id).val());
            var otros_cargos_auto  = parseFloat($("#otros_cargos_auto"+id).val());
            
            // Calcula el valor nuevo de cada campo
            var anticipo_auto = parseFloat(frm.anticipo_auto.value.replace(",","")) - subtotal_auto;
            var otrosT_auto  = parseFloat(frm.otrosT_auto.value.replace(",","")) - otros_cargos_auto;
            var Total_auto = anticipo_auto + otrosT_auto;
            
            // Pone los valores afectados
            frm.anticipo_auto.value = number_format(anticipo_auto,2,".",",");
            frm.otrosT_auto.value   = number_format(otrosT_auto,2,".",",");                    
            frm.Total_auto.value    = number_format(Total_auto,2,".",",");

            $("#subtotal_auto"+id).parent().parent().fadeOut("normal", function () { 
                $(this).remove();                                    
            });

            return false;
    
        }//borrar partida

        function activaSave(){
            var frm=document.edit_itinerarios;
    
            if(frm.numvuelo.value=="" || frm.costoViaje.value=="" || frm.iva.value=="")
                $("#aceptarnueva").attr("disabled", "disabled");

            else
                $("#aceptarnueva").removeAttr("disabled"); 
        }
		

                
    </script>


    <link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
    <style>
        .style1 {color: #FF0000}
        .alignRight{
            text-align:right
        }
        .alignLeft{
            text-align:left
        }
    </style>

    </head>
  <body>
  <br /> 	
  <div>
  <center> 
  <form name="edit_itinerarios" id="edit_itinerarios" action="../notificaciones/travel_pass.php" method="post">
    <table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
        <tr>
            <td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n del Empleado</strong></div></td>
          <!--  <td width="49%" ></td>-->
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        
        <tr>
            <td width="6%"><div align="right">Tr&aacute;mite:</div></td>
            <td width="18%"><strong><?php echo $tramite ?></strong></td>
            <td width="2%">&nbsp;</td>
            <td width="10%"><div align="right">Fecha de creaci&oacute;n:</div></td>
            <td width="14%"><div align="left"><strong><?php echo $fecha_tramite ?></strong></div></td>
        </tr>
        
        <tr>
            <td ><div align="right">Solicitante:</div></td>
            <td ><div align="left"><strong><?php echo $iniciador;?></strong></div></td>
            <td >&nbsp;</td>
            <td ><div align="right">Centro de costos:</div></td>
            <td ><div align="left"><strong><?php echo $cc_centrocostos ?> - <?php echo $cc_nombre ?></strong></div></td>
        </tr>
        
        <tr>
            <td ><div align="right">No. empleado:</div></td>
            <td ><div align="left"><strong><?php echo $num_empleado?><input type="hidden" name="id_empleado" value ="<?php echo $num_empleado?>" /></strong></div></td>
            <td >&nbsp;</td>				
            <td ><div align="right">e-mail:</div></td>
            <td ><div align="left"><strong><?php echo $u_email ?></strong></div></td>
        </tr>
                    
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
                    
    </table>
    <br />
	<table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
            <td width="6%"><div align="right">Motivo:</div></td>
            <td width="18%"><strong><?php echo $motivo ?></strong></td>
            <td width="2%">&nbsp;</td>
            <td width="10%"><div align="right">D&iacute;as de viaje:</div></td>
            <td width="14%"><div align="left"><strong><?php echo $totaldias ?></strong></div></td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	</table>
    <br />
    <br />
    <br />
    <div align="center" style="color:#003366"><strong>Itinerarios de viaje</strong></div>
    <table id="solicitud_table" class="tablesorter" cellspacing="1">
        <thead> 
            <tr> 				   
                <th width="7%" align="center">No.</th>
                <th width="7%" align="center">Tipo de viaje</th>
                <th width="7%" align="center">Viaje</th>
                <th width="7%" align="center">Origen</th>
                <th width="7%" align="center">Destino</th>
                <th width="7%" align="center">Fecha de salida</th>
                <th width="7%" align="center">Horario de salida</th>
                <th width="7%" align="center">Fecha de regreso</th>
                <th width="7%" align="center">Horario de regreso</th>
                <th width="7%" align="center">Transporte</th>
                <th width="7%" align="center">Hospedaje</th>
                <th width="7%" align="center">Noches</th>
                <th width="7%" align="center">Renta de auto</th>
                <th width="7%" align="center">Agregar cotizaci&oacute;n</th>
            </tr> 

            <?php 
				$aux = array();
				$query = "select svi_id,
svi_tipo_viaje,
sv.sv_viaje,
svi_origen,
svi_destino,
svi_horario,
DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') as fecha_salida,
svi_horario_llegada,
DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') as fecha_llegada,
svi.svi_medio,
svi_hotel_agencia,
svi_noches_hospedaje,
svi_renta_auto
FROM sv_itinerario as svi
inner join solicitud_viaje as sv
on sv.sv_id = svi.svi_solicitud
where svi.svi_solicitud = {$id_Solicitud}";
				$rst = $cnn->consultar($query);
				while($datos=mysql_fetch_assoc($rst)){
					array_push($aux,$datos);
				}
				$i=1;
                foreach($aux as $datosAux){
            ?>
                <tr>
                    <td align="center"><?php echo $i ?></td>
                    <td align="center"><?php echo $datosAux["svi_tipo_viaje"] ?><input type="hidden" id="svi_tipo_viaje<?php echo $i ?>" value="<?php echo $datosAux["svi_tipo_viaje"] ?>"/></td>
                    <td align="center"><?php echo $datosAux["sv_viaje"] ?><input type="hidden" id="tipo_de_vuelo<?php echo $i ?>" value="<?php echo $datosAux["sv_viaje"] ?>"/></td>
                    <td align="center"><?php echo $datosAux["svi_origen"] ?></td>
                    <td align="center"><?php echo $datosAux["svi_destino"] ?></td>
                    <td align="center"><?php echo $datosAux["fecha_salida"] ?><input type="hidden" id="svi_fecha_salida<?php echo $i ?>" value="<?php echo $datosAux["fecha_salida"] ?>"/></td>
                    <td align="center"><?php echo $datosAux["svi_horario"] ?></td>
                    <td align="center"><?php if($datosAux["sv_viaje"]!='Sencillo' && $datosAux["sv_viaje"]!='Varias escalas') echo $datosAux["fecha_llegada"]; else echo "N/A"; ?><input type="hidden" id="svi_fecha_llegada<?php echo $i ?>" value="<?php if($datosAux["sv_viaje"]!='Sencillo' && $datosAux["sv_viaje"]!='Varias escalas') echo $datosAux["fecha_llegada"]; else echo ""; ?>"/></td>
                    <td align="center"><?php echo $datosAux["svi_horario_llegada"] ?></td>
                    <td align="center"><?php echo $datosAux["svi_medio"] ?></td>
                    <td align="center"><?php if($datosAux["svi_hotel_agencia"]==1) echo "SI"; else echo "NO"; ?></td>
                    <td align="center"><?php echo $datosAux["svi_noches_hospedaje"] ?><input type="hidden" id="svi_noches_hospedaje<?php echo $i ?>" value="<?php echo $datosAux["svi_noches_hospedaje"] ?>"/></td>
                    <td align="center"><?php if($datosAux["svi_renta_auto"]==1) echo "SI"; else echo "NO"; ?></td>
                    <td>
                       <div align="center">
                          <img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Itinerario'  onclick="flotanteActive(1,<?php echo $datosAux['svi_id']?>,'<?php echo $datosAux["fecha_salida"] ?>','<?php echo $datosAux["svi_destino"]?>','<?php echo $datosAux["svi_hotel_agencia"]?>','<?php echo $datosAux["svi_renta_auto"]?>','<?php echo $i ?>');" style="cursor:pointer"/>
                       </div>
                    </td>
                </tr>
            <?php
					$i++;
                }
            ?>						
        </thead> 
        <tbody> <div align="center" style="color:#0000FF; font-size:9.5px">Nota: Para editar itinerario de clic en el icono de edici&oacute;n (&nbsp;<img src='../../images/addedit.png'/>&nbsp;).</div>
            <!-- cuerpo tabla-->
        </tbody>

    </table>		
    <br/>

    <div align="center" style="color:#003366"><strong>Resumen de observaciones</strong></div>
    <table id="resumen_de_observaciones" class="tablesorter" cellspacing="1" style="width:60%">
        <thead> 
            <tr> 				   
                <th align="center">Fecha/Hora</th>
                <th align="center">Comentario</th>
                <th align="center">Autor</th>
            </tr> 

            <?php 
				$aux = array();
				$query = "select DATE_FORMAT(ob_fecha,'%d/%m/%Y') as fecha,
					ob_texto as comentario,
					e.nombre as autor
					from observaciones
					inner join empleado as e
					on ob_usuario = e.idempleado
					where ob_tramite = {$tramite}
					order by ob_fecha asc;";
				$rst = $cnn->consultar($query);
				while($datos=mysql_fetch_assoc($rst)){
					array_push($aux,$datos);
				}
                foreach($aux as $datosAux){
            ?>
                <tr>
                    <td width="20%" align="center"><?php echo $datosAux["fecha"] ?></td>
                    <td width="">
						<textarea style="width:100%; border:0" rows="8"><?php echo $datosAux["comentario"] ?></textarea>
					</td>
                    <td width="20%" align="center"><?php echo $datosAux["autor"] ?></td>
                </tr>
            <?php
                }
            ?>						
        </thead> 
        <tbody>
            <!-- cuerpo tabla-->
        </tbody>  
    </table>
	<br/>

    <!-----------------------------------------------------
						Datos de avion
	------------------------------------------------------>
	<div align="left" style="color:#003366; margin-left:3em"><strong>Datos de Avi&oacute;n</strong></div>
    <table id="table_aprobado" class="tablesorter" cellspacing="1" style="">
        <thead>
            <tr> 
                <th width="2%">No.</th> 
                <th width="4%">No. Itinerario</th>
                <th width="4%">Tipo de viaje</th>
                <th width="4%">Aerol&iacute_;nea</th>
                <th width="4%">Fecha de salida</th>
                <th width="4%">Fecha de regreso</th>
                <th width="5%">Subtotal</th>
                <th width="5%">IVA</th>
                <th width="5%">TUA</th>
                <th width="5%">Total</th>
                <th width="7%">Eliminar</th>
            </tr> 
            <?php 
				$aux = array();
				$query = "select svi_id,
					svi_tipo_viaje,
					sv.sv_viaje,
					svi_aerolinea,
					svi_monto_vuelo,
					svi_iva,
					svi_tua,
					DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') as fecha_salida,
					DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') as fecha_llegada,
					svi_tipo_aerolinea,
					svi_cotizado
					FROM sv_itinerario as svi
					inner join solicitud_viaje as sv
					on sv.sv_id = svi.svi_solicitud
					where svi.svi_solicitud = {$id_Solicitud}";
				$rst = $cnn->consultar($query);
				while($datos=mysql_fetch_assoc($rst)){
					array_push($aux,$datos);
				}
				$subtotal_avion=0;
				$iva_avion=0;
				$tua_avion=0;
				$i=1;
				$id=1;
                foreach($aux as $datosAux){
				if($datosAux["svi_cotizado"] == "1" || $datosAux["svi_cotizado"] == 1){
            ?>
                <tr>
                    <td><?php echo $id ?><input type="hidden" name="row<?php echo $id ?>" id="row<?php echo $id ?>" value="<?php echo $id ?>" readonly="readonly" /></td>
                    <td><?php echo $i ?><input type="hidden" name="id_itinerario<?php echo $id ?>" id="id_itinerario<?php echo $id ?>" value="<?php echo $datosAux["svi_id"] ?>" readonly="readonly" /></td>
                    <td><input type="hidden" name="tipo<?php echo $id ?>" id="tipo<?php echo $id ?>" value="<?php echo $datosAux["svi_tipo_viaje"] ?>" readonly="readonly" /><?php echo $datosAux["svi_tipo_viaje"] ?></td>
                    <td><input type="hidden" name="aerolinea<?php echo $id ?>" id="aerolinea<?php echo $id ?>" value="<?php echo $datosAux["svi_aerolinea"] ?>" readonly="readonly" /><?php echo $datosAux["svi_aerolinea"] ?></td>
                    <td><?php echo $datosAux["fecha_salida"] ?><input type="hidden" name="fecha<?php echo $id ?>" id="fecha<?php echo $id ?>" value="<?php echo $datosAux["fecha_salida"] ?>" readonly="readonly" /></td>
                    <td><?php if($datosAux["sv_viaje"]!='Sencillo' && $datosAux["sv_viaje"]!='Varias escalas') echo $datosAux["fecha_llegada"]; else echo "N/A"; ?><input type="hidden" name="fecha_llegada_input<?php echo $id ?>" id="fecha_llegada_input<?php echo $id ?>" value="<?php if($datosAux["sv_viaje"]!='Sencillo' && $datosAux["sv_viaje"]!='Varias escalas') echo $datosAux["fecha_llegada"]; else echo ""; ?>" readonly="readonly" /></td>
                    <td><input type="hidden" name="costoViaje<?php echo $id ?>" id="costoViaje<?php echo $id ?>" value="<?php echo $datosAux["svi_monto_vuelo"] ?>" readonly="readonly" />$&nbsp;<?php echo $datosAux["svi_monto_vuelo"] ?></td>
                    <td><input type="hidden" name="iva<?php echo $id ?>" id="iva<?php echo $id ?>" value="<?php echo $datosAux["svi_iva"] ?>" readonly="readonly" /><?php echo $datosAux["svi_iva"] ?></td>
                    <td><input type="hidden" name="tua<?php echo $id ?>" id="tua<?php echo $id ?>" value="<?php echo $datosAux["svi_tua"] ?>" readonly="readonly" /><?php echo $datosAux["svi_tua"] ?></td>
                    <td><?php echo (floatval($datosAux["svi_monto_vuelo"])+floatval($datosAux["svi_iva"])+floatval($datosAux["svi_tua"]))?></td>
					<td><div align="center"><img class="elimina" src="../../images/delete.gif" alt="Click aqu&iacute; para Eliminar" name="del" id="<?php echo $id ?>" onclick="borrarPartida(this.id);" style="cursor:pointer"/></div></td>
                </tr>
                    <input type="hidden" name="tipo_aerolinea<?php echo $id ?>" id="tipo_aerolinea<?php echo $id ?>" value="<?php echo $datosAux["svi_tipo_aerolinea"] ?>" readonly="readonly" />
            <?php
					$subtotal_avion = floatVal($subtotal_avion)+floatVal($datosAux["svi_monto_vuelo"]);
					$iva_avion = floatVal($iva_avion)+floatVal($datosAux["svi_iva"]);
					$tua_avion = floatVal($tua_avion)+floatVal($datosAux["svi_tua"]);
					$id++;
				}
					$i++;
                }
            ?>						
        </thead> 
        <tbody> 
            <!-- cuerpo tabla-->
        </tbody> 
    </table>
	<table id="datosgrales" style="" width="300px" border="0" align="left">
		<tr>
		  <td width="10%" class="alignRight">Subtotal:</td>
		  <td width="10%">$
			<input type="text" name="anticipo" id="anticipo" value="<?php echo $subtotal_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/>
		  </td>
		</tr>
		<tr>
		  <td class="alignRight">IVA:</td>
		  <td>$
			<input type="text" name="ivaT" id="ivaT" value="<?php echo $iva_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<!---Ricardo: añadí los elementos y controles para mostrar el TUA y Otros Impuestos--->
		<tr>
		  <td class="alignRight">TUA:</td>
		  <td>$
			<input type="text" name="tuaT" id="tuaT" value="<?php echo $tua_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<tr>
		  <td class="alignRight">Total:</td>
		  <td>$
			<input type="text" name="Total" id="Total" value="<?php echo floatVal($subtotal_avion)+floatVal($iva_avion)+floatVal($tua_avion) ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<tr>
		  <td colspan="2"></td>
		</tr>
	</table>
	<br clear="all">
	<br>

    <!-----------------------------------------------------
						Datos de Hotel
	------------------------------------------------------>
	<div align="left" style="color:#003366; margin-left:3em"><strong>Datos de Hotel</strong></div>
    <table id="table_aprobado_hotel" class="tablesorter" cellspacing="1" style="">
        <thead>
            <tr> 
                <th width="2%">No.</th> 
                <th width="4%">No. Itinerario</th>
                <th width="4%">Nombre hotel</th>
                <th width="4%">Ciudad</th>
                <th width="4%">Noches</th>
                <th width="4%">Tipo de habitaci&oacute;n</th>
                <th width="4%">Fecha de llegada</th>
                <th width="4%">Hora de llegada</th>
                <th width="4%">Fecha de salida</th>
                <th width="4%">Hora de salida</th>
                <th width="5%">$ Costo estimado</th>
                <th width="5%">Subtotal</th>
                <th width="5%">Otros cargos</th>
                <th width="5%">Total</th>
                <th width="5%">No. de reservaci&oacute;n</th>                
                <th width="7%">Eliminar</th>
            </tr> 
            <?php 
				$aux = array();
				$query = "select svi_id,
						svi_hotel_agencia,
						sv_viaje,
						svi_ciudad,
						svi_nombre_hotel,
						svi_noches_hospedaje,
						svi_caracteristicas_habitacion,
						svi_costo_hotel,
						svi_otrocargo,
						svi_hotel_reservacion,
						DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') as fecha_salida,
						DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') as fecha_llegada,
						svi_hotel_llegada,
						svi_hotel_salida,
						svi_costo_noche,
						svi_cotizado
						FROM sv_itinerario as svi
						inner join solicitud_viaje as sv
						on sv.sv_id = svi.svi_solicitud
						where svi.svi_solicitud = {$id_Solicitud}";
				$rst = $cnn->consultar($query);
				while($datos=mysql_fetch_assoc($rst)){
					array_push($aux,$datos);
				}
				$subtotal_de_hotel=0;
				$otros_carg=0;
				$i=1;
				$id=1;
                foreach($aux as $datosAux){
				if($datosAux["svi_cotizado"] == "1" || $datosAux["svi_cotizado"] == 1){
				if($datosAux["svi_hotel_agencia"] == "1" || $datosAux["svi_hotel_agencia"] == 1){
            ?>
                <tr>
                    <td><?php echo $id ?><input type="hidden" name="row<?php echo $id ?>" id="row<?php echo $id ?>" value="<?php echo $id ?>" readonly="readonly" /></td>
                    <td><?php echo $i ?><input type="hidden" name="id_itinerario_hotel<?php echo $id ?>" id="id_itinerario_hotel<?php echo $id ?>" value="<?php echo $datosAux["svi_id"] ?>" readonly="readonly" /></td>
					<td><input type="hidden" name="nombre_hotel<?php echo $id ?>" id="nombre_hotel<?php echo $id ?>" value="<?php echo $datosAux["svi_nombre_hotel"] ?>"  readonly="readonly" /><?php echo $datosAux["svi_nombre_hotel"] ?></td>
					<td><input type="hidden" name="ciudad_hotel<?php echo $id ?>" id="ciudad_hotel<?php echo $id ?>" value="<?php echo $datosAux["svi_ciudad"] ?>" readonly="readonly" /><?php echo $datosAux["svi_ciudad"] ?></td>
					<td><input type="hidden" name="noches_de_hospedaje<?php echo $id ?>" id="noches_de_hospedaje<?php echo $id ?>" value="<?php echo $datosAux["svi_noches_hospedaje"] ?>" readonly="readonly" /><?php echo $datosAux["svi_noches_hospedaje"] ?></td>
					<td><input type="hidden" name="caracteristicas_habitacion<?php echo $id ?>" id="caracteristicas_habitacion<?php echo $id ?>" value="<?php echo $datosAux["svi_caracteristicas_habitacion"] ?>" readonly="readonly" /><?php echo $datosAux["svi_caracteristicas_habitacion"] ?></td>
					<td><?php echo $datosAux["fecha_salida"] ?></td>
					<td><input type="hidden" name="hora_llegada<?php echo $id ?>" id="hora_llegada<?php echo $id ?>" value="<?php echo $datosAux["svi_hotel_llegada"] ?>" readonly="readonly" /><?php echo $datosAux["svi_hotel_llegada"] ?></td>
					<td><?php if($datosAux["sv_viaje"]!='Sencillo') echo $datosAux["fecha_llegada"]; else echo ""; ?></td>
					<td><input type="hidden" name="hora_salida<?php echo $id ?>" id="hora_salida<?php echo $id ?>" value="<?php echo $datosAux["svi_hotel_salida"] ?>" readonly="readonly" /><?php echo $datosAux["svi_hotel_salida"] ?></td>
					<td></td>
					<td><input type="hidden" name="subtotal_hotel<?php echo $id ?>" id="subtotal_hotel<?php echo $id ?>" value="<?php echo $datosAux["svi_costo_hotel"] ?>" readonly="readonly" /><?php echo $datosAux["svi_costo_hotel"] ?></td>
					<td><input type="hidden" name="otros_cargos<?php echo $id ?>" id="otros_cargos<?php echo $id ?>" value="<?php echo $datosAux["svi_otrocargo"] ?>" readonly="readonly" /><?php echo $datosAux["svi_otrocargo"] ?></td>
					<td><?php echo (floatval($datosAux["svi_costo_hotel"])+floatval($datosAux["svi_otrocargo"]))?></td>
					<td><input type="hidden" name="no_de_reservacion<?php echo $id ?>" id="no_de_reservacion<?php echo $id ?>" value="<?php echo $datosAux["svi_hotel_reservacion"] ?>" readonly="readonly" /><?php echo $datosAux["svi_hotel_reservacion"] ?></td>
					<td><div align="center"><img class="elimina" src="../../images/delete.gif" alt="Click aqu&iacute; para Eliminar" name="del" id="<?php echo $id ?>"  onclick="borrarPartidaHotel(this.id);" style="cursor:pointer"/></div></td>
                </tr>
					<input type="hidden" name="costo_x_noche<?php echo $id ?>" id="costo_x_noche<?php echo $id ?>" value="<?php echo $datosAux["svi_costo_noche"] ?>" readonly="readonly" />
            <?php
					$subtotal_de_hotel = floatVal($subtotal_de_hotel)+floatVal($datosAux["svi_costo_hotel"]);
					$otros_carg = floatVal($otros_carg)+floatVal($datosAux["svi_otrocargo"]);
					$id++;
				}
				}
					$i++;
                }
            ?>						
        </thead> 
        <tbody> 
            <!-- cuerpo tabla-->
        </tbody> 
    </table>
	<table id="datosgrales_hotel" style="" width="300px" border="0" align="left">
		<tr>
		  <td width="10%" class="alignRight">Subtotal:</td>
		  <td width="10%">$
			<input type="text" name="anticipo_hotel" id="anticipo_hotel" value="<?php echo $subtotal_de_hotel ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/>
		  </td>
		</tr>
		<!---Ricardo: añadí los elementos y controles para mostrar el TUA y Otros Impuestos--->
		<tr>
		  <td class="alignRight">Otros cargos:</td>
		  <td>$
			<input type="text" name="otrosT_hotel" id="otrosT_hotel" value="<?php echo $otros_carg ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<tr>
		  <td class="alignRight">Total:</td>
		  <td>$
			<input type="text" name="Total_hotel" id="Total_hotel" value="<?php echo floatVal($subtotal_de_hotel)+floatVal($otros_carg) ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<tr>
		  <td colspan="2"></td>
		</tr>
	</table>
	<br clear="all">
	<br>

    <!-----------------------------------------------------
						Datos de Auto
	------------------------------------------------------>
	<div align="left" style="color:#003366; margin-left:3em"><strong>Datos de Auto</strong></div>
    <table id="table_aprobado_auto" class="tablesorter" cellspacing="1" style="">
        <thead>
            <tr> 
                <th width="2%">No.</th> 
                <th width="4%">No. Itinerario</th>
                <th width="4%">Empresa</th>
                <th width="4%">Tipo de auto</th>
                <th width="4%">D&iacute;as de renta</th>
                <th width="5%">$ Costo estimado</th>
                <th width="5%">Costo x d&iacute;a</th>
                <th width="5%">Subtotal</th>
                <th width="5%">Otros cargos</th>
                <th width="5%">Total</th>
                <th width="7%">Eliminar</th>
            </tr> 
            <?php 
				$aux = array();
				$query = "select svi_id,
					svi_renta_auto,
					svi_empresa_auto,
					svi_tipo_auto,
					svi_dias_renta,
					svi_costo_auto,
					svi_total_auto,
					svi_cotizado
					FROM sv_itinerario as svi
					inner join solicitud_viaje as sv
					on sv.sv_id = svi.svi_solicitud
					where svi.svi_solicitud = {$id_Solicitud}";
				$rst = $cnn->consultar($query);
				while($datos=mysql_fetch_assoc($rst)){
					array_push($aux,$datos);
				}
				$subtotal_de_auto=0;
				$otros_cargo=0;
				$i=1;
				$id=1;
                foreach($aux as $datosAux){
				if($datosAux["svi_cotizado"] == "1" || $datosAux["svi_cotizado"] == 1){
				if($datosAux["svi_renta_auto"] == "1" || $datosAux["svi_renta_auto"] == 1){
            ?>
                <tr>
                    <td><?php echo $id ?><input type="hidden" name="row<?php echo $id ?>" id="row<?php echo $id ?>" value="<?php echo $id ?>" readonly="readonly" /></td>
                    <td><?php echo $i ?><input type="hidden" name="id_itinerario_auto<?php echo $id ?>" id="id_itinerario_auto<?php echo $id ?>" value="<?php echo $datosAux["svi_id"] ?>" readonly="readonly" /></td>
                    <td><input type="hidden" name="empresa_auto<?php echo $id ?>" id="empresa_auto<?php echo $id ?>" value="<?php echo $datosAux["svi_empresa_auto"] ?>" readonly="readonly" /><?php echo $datosAux["svi_empresa_auto"] ?></td>
                    <td><input type="hidden" name="tipo_de_auto<?php echo $id ?>" id="tipo_de_auto<?php echo $id ?>" value="<?php echo $datosAux["svi_tipo_auto"] ?>" readonly="readonly" /><?php echo $datosAux["svi_tipo_auto"] ?></td>
                    <td><input type="hidden" name="dias_de_renta<?php echo $id ?>" id="dias_de_renta<?php echo $id ?>" value="<?php echo $datosAux["svi_dias_renta"] ?>" readonly="readonly" /><?php echo $datosAux["svi_dias_renta"] ?></td>
                    <td></td>
					<td><input type="hidden" name="costo_x_dia<?php echo $id ?>" id="costo_x_dia<?php echo $id ?>" value="<?php echo $datosAux["svi_costo_auto"] ?>" readonly="readonly" /><?php echo $datosAux["svi_costo_auto"] ?></td>
                    <td><input type="hidden" name="subtotal_auto<?php echo $id ?>" id="subtotal_auto<?php echo $id ?>" value="<?php echo $datosAux["svi_total_auto"] ?>" readonly="readonly" /><?php echo $datosAux["svi_total_auto"] ?></td>
                    <td><input type="hidden" name="otros_cargos_auto<?php echo $id ?>" id="otros_cargos_auto<?php echo $id ?>" value="0.00" readonly="readonly" />0.00</td>
                    <td><?php echo (floatval($datosAux["svi_total_auto"])+floatval("0.00"))?></td>
					<td><div align="center"><img class="elimina" src="../../images/delete.gif" alt="Click aqu&iacute; para Eliminar" name="del" id="<?php echo $id ?>"  onclick="borrarPartidaAuto(this.id);" style="cursor:pointer"/></div></td>
                </tr>
            <?php
					$subtotal_de_auto = floatVal($subtotal_de_auto)+floatVal($datosAux["svi_total_auto"]);
					$otros_cargo = floatVal($otros_cargo)+floatVal("0.00");
					$id++;
				}
				}
					$i++;
                }
            ?>						
        </thead> 
        <tbody> 
            <!-- cuerpo tabla-->
        </tbody> 
    </table>
	<table id="datosgrales_auto" style="" width="300px" border="0" align="left">
		<tr>
		  <td width="10%" class="alignRight">Subtotal:</td>
		  <td width="10%">$
			<input type="text" name="anticipo_auto" id="anticipo_auto" value="<?php echo $subtotal_de_auto ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/>
		  </td>
		</tr>
		<!---Ricardo: añadí los elementos y controles para mostrar el TUA y Otros Impuestos--->
		<tr>
		  <td class="alignRight">Otros cargos:</td>
		  <td>$
			<input type="text" name="otrosT_auto" id="otrosT_auto" value="<?php echo $otros_cargo ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<tr>
		  <td class="alignRight">Total:</td>
		  <td>$
			<input type="text" name="Total_auto" id="Total_auto" value="<?php echo floatVal($subtotal_de_auto)+floatVal($otros_cargo) ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
		</tr>
		<tr>
		  <td colspan="2"></td>
		</tr>
	</table>
	<br clear="all">
	<br>

    <!-----------------------------------------------------
		Cuadro donde se ingresan las observaciones
	------------------------------------------------------>
	<div id="observaciones_para_empleado">
		<table border="0" width="62%">
			<tr>
				<td width="5px">&nbsp;</td>
				<td colspan="3">Observaciones: </td>
				<td width="5px">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">
					<textarea name="observ_to_emple" id="observ_to_emple" style="width:100%" rows="12" cols="130"></textarea>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3" align="center">
					<input type="submit" name="devolver_to_emple" id="devolver_to_emple" value="Devolver a empleado" onclick="">
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

		<tr>
		  <td colspan="5"><br />
			<div align="center" style="display:none">
			  <input type="submit" value="Enviar Autorizaci&oacute;n" id="devolver" name="devolver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
			  <input type="submit" value="Rechazar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
			  <input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
			  <input type="hidden" name="ne" id="ne" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
			  <input type="hidden" name="iniciador" id="iniciador" readonly="readonly" value="<? echo $iniciadorT;  ?>" />
			  <input type="hidden" name="tramite" id="tramite" readonly="readonly" value="<? echo $tramite;?>" />
			  <input type="hidden" name="anticipo_amex" id="anticipo_amex" readonly="readonly" value="<? echo $anticipo;?>" />
			  <input type="hidden" name="fecha_tramite" id="fecha_tramite" readonly="readonly" value="<? echo $fecha_tramite;?>" />
			  <input type="hidden" name="etapa_del_tramite" id="etapa_del_tramite" readonly="readonly" value="<? echo $etapa_solicitud;?>" />
					<!--Estos se incrementan pero no disminuyen-->
                    <input type="hidden" id="rowCount2" name="rowCount2" value="0" readonly="readonly"/>
                    <input type="hidden" id="rowCountHotel2" name="rowCountHotel2" value="0" readonly="readonly"/>
                    <input type="hidden" id="rowCountAuto2" name="rowCountAuto2" value="0" readonly="readonly"/>
			</div>
		  </td>
		</tr>
		<tr>
		  <td colspan="5"><br />
			<div align="center" style="">
			  <input type="submit" value="    Comprar y devolver a empleado" id="comprar" name="comprar"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
			</div>
		  </td>
		</tr>

		</table>
	</div>
	
    <!--<<<<TABLE QUE FUNCIONA COMO FLOTANTE>>>>>>>>>>-->
		
<!--	<div id="ventanita" style="visibility:hidden; position:absolute; top:300; left:200; width:33%; background-color:#F0F0FF">-->
	<div id="ventanita" style="visibility:hidden; position:absolute; top:50%; left:50%; width:450px; margin-left:-225px; height:; margin-top:; border:1px solid #808080; padding:5px; background-color:#F0F0FF">
<table bordercolor="#333333" style="font-size:11px" border="0" width="100%">
	<tr bgcolor="#8C8CFF">
		<td colspan="6">
			<font color="#ffffff"><strong>&nbsp;&nbsp;&nbsp;Datos de agencia</strong></font>
		</td>
		<td width="16px">
			<img src="../../images/close2.ico" alt="Cerrar" align="right" onclick="flotanteActive(0);" style="cursor:pointer"/>
		</td>
	</tr>
</table>
        <div style="display:none">
		<table bordercolor="#333333" style="font-size:11px" border="1">
			<tr>
                <td class="alignRight">
              </td>
                <td class="alignLeft">
              </td>
              <td><div align="right">Medio de Transporte:</div>
            </td>
                <td class="alignLeft"><b>A&eacute;reo</b></td>
            </tr>
            <tr>
                <td>
                    <div align="right" id="labelPasaje">No. de Vuelo<span class="style1">*</span>:</div>
                </td>
                <td>
                    <input name="numvuelo" type="text" id="numvuelo" onkeypress="return validaNum (event);" onkeyup="" size="10"/>
                </td>
                <td></td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td class="alignRight">Hora de Salida:</td>
                <td>
                    <input type="text" name="horaSalida" id="horaSalida" size="10">
                </td>
                <td class="alignRight"></td>
                <td class="alignLeft">
                </td>
            </tr>
            <tr>
                <td class="alignRight"></td>
                <td class="alignLeft">
                </td>
                <td class="alignRight"></td>
                <!---ricardo: modifico el campo de impuesto tua para que no se capture un porcentaje sino un --->
                <td class="alignLeft">
                </td>
            </tr>
            <tr>
            <!---Ricardo: agrego un campo para otros impuestos --->
				<td class="alignRight">Otros Impuestos:</td>
                <td class="alignLeft"><input type="text" name="otros_imp" id="otros_imp" value="0" style="width:45px" onkeypress="return validaNum (event);" onkeyup="" />
                </td>
                <td align="center">Apartado de boleto:</td>
                <td colspan="2">
                    <input type="text" name="apartado_boleto" id="apartado_boleto" style="width:45px" value="0" onkeypress="return validaNum(event);" maxlength="2">&nbsp;Horas
                </td>
            </tr>
            <tr>
            </tr>

            <tr>
                <td colspan="6">
                    <input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
                    <input type="hidden" id="rowCountHotel" name="rowCountHotel" value="0" readonly="readonly"/>
                    <input type="hidden" id="rowCountAuto" name="rowCountAuto" value="0" readonly="readonly"/>
                </td>
            </tr>
		</table>
		</div>

<table id="datos_avion" border="0" cellspacing="3" width="450px" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
  <tr>
    <td>&nbsp;</td>
    <td colspan="5" align="center"><strong>Avi&oacute;n</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
		<input  type="hidden" id="hotel_chexk" name="hotel_chexk" value="0"/>
		<input  type="hidden" id="auto_chexk" name="auto_chexk" value="0"/>
		<input  type="hidden" id="id_itinerario" name="id_itinerario" value=""/>
		<input  type="hidden" id="consecutivo_de_itinerario" name="consecutivo_de_itinerario" value=""/>
		
		<input  type="hidden" id="cotizado_avion" name="cotizado_avion" value="0"/>
			<input  type="hidden" id="fila_avion" name="fila_avion" value=""/><!--Aqui se guarda el indice que se va a eliminar del resumen para simular la actualizacion-->
		<input  type="hidden" id="cotizado_hotel" name="cotizado_hotel" value="0"/>
			<input  type="hidden" id="fila_hotel" name="fila_hotel" value=""/>
		<input  type="hidden" id="cotizado_auto" name="cotizado_auto" value="0"/>
			<input  type="hidden" id="fila_auto" name="fila_auto" value=""/>
		
		<input type="hidden" id="fechasalida" name="fechasalida" readonly="readonly" value="<?echo $datosAux["fecha_salida"];?>"/>
		Tipo de viaje:
	</td>
    <td>
		<select name="select_tipo_viaje" id="select_tipo_viaje" onchange="" style="width:75px" disabled="true">
			<?php
				$tipoViaje=array("Nacional","Continental","Intercontinental");
				for($i=0;$i<count($tipoViaje);$i++){
					echo "<option id=".$i." value=".$tipoViaje[$i].">".$tipoViaje[$i]."</option>";
				}
			?>
		</select>
	</td>
    <td>&nbsp;</td>
    <td><div id="eAerolinea">Aerol&iacute;nea:</div></td>
    <td><input type="text" name="aerolinea" id="aerolinea" size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Costo<span class="style1">*</span>:</td>
    <td>$&nbsp;<input type="text" name="costoViaje" id="costoViaje" value="0.00" style="width:55px; text-align:right;" onkeypress="return validaNum (event);" onkeyup="" />&nbsp;MXP</td>
    <td>&nbsp;</td>
    <td>Tipo:</td>
    <td>
		<select name="tipo" id="tipo">
			<option name="1" id="1" value="Turista">Turista</option>
			<option name="2" id="2" value="Business class">Business class</option>
		</select> 
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>IVA<span class="style1">*</span>:</td>
    <td>$&nbsp;<input type="text" name="iva" id="iva" value="0" style="width:55px; text-align:right;" onkeypress="return validaNum (event);" onkeyup="" />&nbsp;MXP</td>
    <td>&nbsp;</td>
    <td>Tipo de vuelo:</td>
    <td>
		<select name="select_tipo_viaje_pasaje" id="select_tipo_viaje_pasaje" onchange="verificar_tipo_boleto(this.value)" disabled="true">
			<!--    <option value="-1">Seleccione...</option>         -->
			<option value="Sencillo">Sencillo</option>
			<option value="Redondo">Redondo</option>
			<option value="Varias escalas">Varias escalas</option>
		</select>
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>TUA<span class="style1">*</span>:</td>
    <td>$&nbsp;<input type="text" name="tua" id="tua" value="0" style="width:55px; text-align:right;" onkeypress="return validaNum (event);" onkeyup="" />&nbsp;MXP</td>
    <td>&nbsp;</td>
    <td>Fecha salida:</td>
    <td>
		<input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="12" onchange="">
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><div id="fecha_llegada_label">Fecha regreso:</div></td>
    <td>
		<input name="fecha_llegada_input" id="fecha_llegada_input" value="<?php echo date('d/m/Y'); ?>" size="12" onchange=""/>
	</td>
    <td></td>
  </tr>
</table>
<table id="datos_hotel" border="0" cellspacing="3" width="450px" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
  <tr>
    <td width="">&nbsp;</td>
    <td colspan="5" align="center"><strong>Hotel</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Ciudad:</td>
    <td><input id="ciudad_hotel" type="text" size="20"/></td>
    <td>&nbsp;</td>
    <td>Noches:</td>
    <td>
		<input name="noches_de_hospedaje" type="text" id="noches_de_hospedaje" value="0" size="2" maxlength="2" onkeypress="return validaNum (event);" onkeyup="getTotal();"/>
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Hotel:</td>
    <td><input id="nombre_hotel" type="text"  size="20"/></td>
    <td>&nbsp;</td>
    <td>Caracter&iacute;sticas de la habitaci&oacute;n:</td>
    <td><input id="caracteristicas_habitacion" type="text" size="10"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Costo x noche:</td>
    <td>
		<input id="costo_x_noche" type="text" size="10" value="0" onkeypress="return validaNum (event);" onkeyup="getTotal();"/>
	</td>
    <td>&nbsp;</td>
    <td>Llegada:</td>
    <td><input id="hora_llegada" type="text" size="6"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Otros cargos:</td>
    <td><input id="otros_cargos" type="text" size="10" value="0" onkeypress="return validaNum (event);" onkeyup="getTotal();"/></td>
    <td>&nbsp;</td>
    <td>Salida:</td>
    <td><input id="hora_salida" type="text" size="6"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Total:</td>
    <td>
		<input id="total_hotel" type="text" size="10" value="0" readonly="readonly"/>
	</td>
    <td>&nbsp;</td>
    <td>No. de reservaci&oacute;n:</td>
    <td><input id="no_de_reservacion" type="text" size="10"/></td>
    <td>&nbsp;</td>
  </tr>
</table>
<table id="datos_auto" border="0" cellspacing="3" width="450px" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
  <tr>
    <td>&nbsp;</td>
    <td colspan="5" align="center"><strong>Renta de Auto</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Empresa:</td>
    <td><input id="empresa_auto" type="text" size="20"/></td>
    <td>&nbsp;</td>
    <td>D&iacute;as de renta:</td>
    <td>
		<input name="dias_de_renta" type="text" id="dias_de_renta" value="0" size="2" maxlength="2" onkeypress="return validaNum (event);" onkeyup="getTotal2();"/>
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Tipo de auto:</td>
    <td>
		<select name="tipo_de_auto" id="tipo_de_auto">
			<option name="1" id="1" value="Compacto">Compacto</option>
			<option name="2" id="2" value="Mediano">Mediano</option>
		</select> 
	</td>
    <td>&nbsp;</td>
    <td>Costo x d&iacute;a:</td>
    <td>
		<input id="costo_x_dia" type="text" size="10" value="0" onkeypress="return validaNum (event);" onkeyup="getTotal2();"/>	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Total:</td>
    <td>
		<input id="total_auto" type="text" size="10" value="0" readonly="readonly"/>
	</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="450px" border="0">
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">
		<input name="aceptarnueva" id="aceptarnueva" type="button" value="Aceptar" onclick="construyePartidas();"/>
	</td>
    <td>&nbsp;</td>
    <td colspan="2" align="left">
		<input name="aceptarnueva" type="button" value="Cancelar" onclick="flotanteActive(0);"/>
	</td>
    <td>&nbsp;</td>
  </tr>
</table>
		
	</div>
	
    </form>

    </center>
      </div>
      <br/>
      <?php //tabs(array("grid_travel.php"));
      ?>
   </body>
</html>
<?php

    //require_once("travel_pass.php");

}
else{
    echo "<font color='#FF0000'><b>Se econtr&oacute; error en el tr&iacute;mite. Verifique e intente de nuevo.</b></font>";
}

   if(isset( $_GET['buy'])){
        echo "<script language='javascript'>
                document.getElementById('comprar').style.visibility = 'visible';
                document.getElementById('devolver_to_emple').style.visibility = 'hidden';
              </script>";
    }
?>
