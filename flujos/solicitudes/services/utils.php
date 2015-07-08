<?php

//
//  Funciones comunes
//

// Regresa el codigo HTML para pintar el select de conceptos de viaje
function get_select_conceptos_viaje($tipoDivisa){
    
    $ret = "";
    $idusuario=$_SESSION["idusuario"];
    $nivelUser=$_SESSION["nivelUser"];
    $cnn    = new conexion();
    $query=sprintf("SELECT cp_concepto, cp_retencion, dc_id, p_id, cp_deducible as deducible, coalesce(p_cantidad, 0) AS p_cantidad, cp_recurrente FROM cat_conceptos LEFT JOIN parametros ON (dc_id=p_concepto and p_nivel_usuario=%s and p_divisa='$tipoDivisa') WHERE cp_activo=true AND cp_en_anticipo = 1 AND dc_catalogo=1 and cp_empresa_id = " . $_SESSION["empresa"] . " ORDER BY cp_concepto",$nivelUser);
    //error_log($query);
    $rst    = $cnn->consultar($query);
    while($fila=mysql_fetch_assoc($rst)){          
        $ret = $ret . "<option id=".$fila["cp_concepto"]." value=".$fila["dc_id"]."&".$fila["p_cantidad"]."&".$fila["cp_recurrente"].">".$fila["cp_concepto"]."</option>";
    }
    
    return $ret;
}

//FUNCIONES PARA OBTENER RUTA DE AUTORIZACION DE SOLICITUD DE VIAJE

function obtener_ruta_de_autorizacion_de_solicitud_viaje($id_tramite,$zonaGeografica){
	$t_ruta_autorizacion = "";
	
	$id_agencia = "";
	$id_director_de_area = "";
	$id_director_general = "";
	$id_controlling = "";
	$id_finanzas = "";
	
	$agencia = true;
	$dir_area = true;
	$dir_gral = true;
	$controlling = true;
	$finanzas = true;
	
	//Cargamos la soliciytu de viaje en base al tramite
	$compInv = new C_SV();
	$compInv->Load_Solicitud_tramite($id_tramite);
	
	//Se obtiene el ID del usuario de agencia
	$usuario_aux = new Usuario();
	$id_agencia = $usuario_aux->Get_Id_by_Tipo("4");//El 4 es el ID del tipo agencia actualmente 16/abr/2012
	
	//El primer autorizador es el "Gerente de area",
	//osea el responsable del centro de costos de la comprobacion de invitacion.
	$cc_id = $compInv->Get_dato("sv_ceco_paga");
	
	$cc = new CentroCosto();
	$cc->Load_CeCo($cc_id);
	$id_gerente_de_area = $cc->Get_Dato("cc_responsable");

/*	
	//Se checa si el usuario es de "BMW Financial Services".
	$tramite = new Tramite();
	$tramite->Load_Tramite($id_tramite);
	$id_iniciador = $tramite->Get_dato("t_iniciador");
	
	$usuario = new Usuario();
	$usuario->Load_Usuario_By_ID($id_iniciador);
	$usu_empresa = $usuario->Get_dato("u_empresa");
	if($usu_empresa == "2"){
		$dir_gral = true;
	}else{
		//Se checa si existe un invitado de tipo "Gobierno".
		$comensales = new Comensales();
		$comensales_array = $comensales->Load_comensales_by_tramite($id_tramite);
		$no_invitados = count($comensales_array);
		for($i=0;$i<$no_invitados;$i++){
			if($comensales_array[$i]['dci_tipo_invitado'] == "Gobierno"){
				$dir_gral = true;
				break;
			}
		}
		if($dir_gral == true){
		}else{
			//Se checa si el monto solicitado por persona es mayor a 50 EUR.
			$si_monto_pesos = $sol_inv->Get_dato("si_monto_pesos");
			
			$divisa = new Divisa();
			$divisa->Load_data("3"); //div_id de EUR = 3
			$tasa_eur = $divisa->Get_dato("div_tasa");
			
			$monto_x_persona = $si_monto_pesos/$tasa_eur/$no_invitados;
			if($monto_x_persona > 50){
				$dir_gral = true;
			}
		}
	}
*/
	$usuario = new Usuario();
	
	//El segundo autorizador es el "Director de area".
	if($dir_area == true){
		$id_director_de_area = $cc->Get_Dato("cc_director_de_area");
	}
	//El tercer autorizador es el "Director general".
	if($dir_gral == true){
		$usuario->Load_Usuario_By_Clave(1); // por el campo director_general obtenemos su idfwk_usuario
		$id_director_general = $usuario->Get_dato("idfwk_usuario");
	}
	$agrup_usu = new AgrupacionUsuarios();
	//El cuarto autorizador es el "Controlling".
	if($controlling == true){
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre('Controlling');
		$id_controlling = $agrup_usu->Get_dato("au_id");
	}
	//El quinto autorizador es el "Finanzas".
	if($finanzas == true){
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre('Finanzas');
		$id_finanzas = $agrup_usu->Get_dato("au_id");
	}
	
	//Se arma la ruta de autorizacion
	//La nueva ruta consiste en tomar el tipo de region que se ha seleccionado en el itinerario creado
	$t_ruta_autorizacion = $id_agencia."|".$id_gerente_de_area;
	
	//Si es de tipo Nacional
	if($zonaGeografica == "Nacional"){
		
		//La nueva ruta de autorizacion sera: Gerente Area->Director de Area->Controlling->Finanzas
		if($dir_area == true && $id_director_de_area != ""){
			if(existe_substr($t_ruta_autorizacion,$id_director_de_area,"|") == false){
				$t_ruta_autorizacion .= "|".$id_director_de_area;
			}
		}
		if($controlling == true && $id_controlling != ""){
			if(existe_substr($t_ruta_autorizacion,$id_controlling,"|") == false){
				$t_ruta_autorizacion .= "|".$id_controlling;
			}
		}
		if($finanzas == true && $id_finanzas != ""){
			if(existe_substr($t_ruta_autorizacion,$id_finanzas,"|") == false){
				$t_ruta_autorizacion .= "|".$id_finanzas;
			}
		}		
	//si es de tipo Continental y/o Intercontinental	
	}else if($zonaGeografica == "Continental" || $zonaGeografica == "Intercontinental" ){
		
		//La Ruta de autorizacion sera la ruta base Gerente Area->Director de Area->Director General->Controlling->Finanzas
		if($dir_area == true && $id_director_de_area != ""){
			if(existe_substr($t_ruta_autorizacion,$id_director_de_area,"|") == false){
				$t_ruta_autorizacion .= "|".$id_director_de_area;
			}
		}
		if($dir_gral == true && $id_director_general != ""){
			if(existe_substr($t_ruta_autorizacion,$id_director_general,"|") == false){
				$t_ruta_autorizacion .= "|".$id_director_general;
			}
		}
		if($controlling == true && $id_controlling != ""){
			if(existe_substr($t_ruta_autorizacion,$id_controlling,"|") == false){
				$t_ruta_autorizacion .= "|".$id_controlling;
			}
		}
		if($finanzas == true && $id_finanzas != ""){
			if(existe_substr($t_ruta_autorizacion,$id_finanzas,"|") == false){
				$t_ruta_autorizacion .= "|".$id_finanzas;
			}
		}
	}
	//error_log($t_ruta_autorizacion.$zonaGeografica);
	return $t_ruta_autorizacion;
}

function eliminar_autorizador_iniciador($ruta_de_autorizacion,$id_user){
	$separador = "|";
	$aux3 = "";
	$token = strtok($ruta_de_autorizacion,$separador);
	while($token != false){
		if($token != $id_user){
			if($aux3 == ""){
				$aux3 .= $token;
			}else{
				$aux3 .= "|".$token;
			}
		}
		$token = strtok($separador);
	}
	return $aux3;
}

//FUNCIONES PARA OBTENER RUTA DE AUTORIZACION DE COMPROBACION DE VIAJE

function obtener_ruta_de_autorizacion_de_comprobacion_viaje($id_tramite){
	$t_ruta_autorizacion = "";
	
	$id_agencia = "";
	$id_director_de_area = "";
	$id_director_general = "";
	$id_controlling = "";
	$id_finanzas = "";
	
	$agencia = true;
	$dir_area = true;
	$dir_gral = true;
	$controlling = true;
	$finanzas = true;
	
	//Cargamos la comprobacion de viaje en base al tramite
	$compViaje = new Comprobacion();
	$compViaje->Load_Comprobacion_By_co_mi_tramite($id_tramite);
	
	//Se obtiene el ID del usuario de agencia
	$usuario_aux = new Usuario();
	$id_agencia = $usuario_aux->Get_Id_by_Tipo("4");//El 4 es el ID del tipo agencia actualmente 16/abr/2012
	
	//El primer autorizador es el "Gerente de area",
	//osea el responsable del centro de costos de la comprobacion de invitacion.
	$cc_id = $compViaje->Get_dato("co_cc_clave");
	
	$cc = new CentroCosto();
	$cc->Load_CeCo($cc_id);
	$id_gerente_de_area = $cc->Get_Dato("cc_responsable");

/*	
	//Se checa si el usuario es de "BMW Financial Services".
	$tramite = new Tramite();
	$tramite->Load_Tramite($id_tramite);
	$id_iniciador = $tramite->Get_dato("t_iniciador");
	
	$usuario = new Usuario();
	$usuario->Load_Usuario_By_ID($id_iniciador);
	$usu_empresa = $usuario->Get_dato("u_empresa");
	if($usu_empresa == "2"){
		$dir_gral = true;
	}else{
		//Se checa si existe un invitado de tipo "Gobierno".
		$comensales = new Comensales();
		$comensales_array = $comensales->Load_comensales_by_tramite($id_tramite);
		$no_invitados = count($comensales_array);
		for($i=0;$i<$no_invitados;$i++){
			if($comensales_array[$i]['dci_tipo_invitado'] == "Gobierno"){
				$dir_gral = true;
				break;
			}
		}
		if($dir_gral == true){
		}else{
			//Se checa si el monto solicitado por persona es mayor a 50 EUR.
			$si_monto_pesos = $sol_inv->Get_dato("si_monto_pesos");
			
			$divisa = new Divisa();
			$divisa->Load_data("3"); //div_id de EUR = 3
			$tasa_eur = $divisa->Get_dato("div_tasa");
			
			$monto_x_persona = $si_monto_pesos/$tasa_eur/$no_invitados;
			if($monto_x_persona > 50){
				$dir_gral = true;
			}
		}
	}
*/
	$usuario = new Usuario();
	
	//El segundo autorizador es el "Director de area".
	if($dir_area == true){
		$id_director_de_area = $cc->Get_Dato("cc_director_de_area");
	}
	
	$agrup_usu = new AgrupacionUsuarios();
	//El cuarto autorizador es el "Controlling".
	if($controlling == true){
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre('Controlling');
		$id_controlling = $agrup_usu->Get_dato("au_id");
	}
	//El quinto autorizador es el "Finanzas".
	if($finanzas == true){
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre('Finanzas');
		$id_finanzas = $agrup_usu->Get_dato("au_id");
	}
	
	//Se arma la ruta de autorizacion
	$t_ruta_autorizacion = $id_agencia."|".$id_gerente_de_area;
	if($dir_area == true && $id_director_de_area != ""){
		if(existe_substr($t_ruta_autorizacion,$id_director_de_area,"|") == false){
			$t_ruta_autorizacion .= "|".$id_director_de_area;
		}
	}
	if($dir_gral == true && $id_director_general != ""){
		if(existe_substr($t_ruta_autorizacion,$id_director_general,"|") == false){
			$t_ruta_autorizacion .= "|".$id_director_general;
		}
	}
	if($controlling == true && $id_controlling != ""){
		if(existe_substr($t_ruta_autorizacion,$id_controlling,"|") == false){
			$t_ruta_autorizacion .= "|".$id_controlling;
		}
	}
	if($finanzas == true && $id_finanzas != ""){
		if(existe_substr($t_ruta_autorizacion,$id_finanzas,"|") == false){
			$t_ruta_autorizacion .= "|".$id_finanzas;
		}
	}
	
	return $t_ruta_autorizacion;
}

function getNoItinerario($auxItinerarios, $svi_id){
	$i=0;
	foreach($auxItinerarios as $item){
		$i++;
		if($svi_id == $item["svi_id"]){
			return $i;
		}
	}
	return "error";
}
?>
