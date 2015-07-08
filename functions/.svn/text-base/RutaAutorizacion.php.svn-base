<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once("$RUTA_A/functions/utils.php");
require_once("$RUTA_A/flujos/comprobaciones/services/func_comprobacion.php");

class RutaAutorizacion extends conexion
{
	function __construct(){
		parent::__construct();
	}
	
	public function getFinanzas(){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Finanzas");
		$finanzas = $agrup_usu->Get_dato("au_id");
		return $finanzas;
	}
	
	public function getAgencia(){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Agencia");
		$agencia = $agrup_usu->Get_dato("au_id");
		return $agencia;
	}
	
	public function getDueno($tramite){
		$dueno = "";
		$tramite_local = new Tramite();
		$tramite_local->Load_Tramite($tramite);
		$dueno = $tramite_local->Get_dato("t_dueno");
		return $dueno;
	}
	
	public function getIniciador($tramite){
		$dueno = "";
		$tramite_local = new Tramite();
		$tramite_local->Load_Tramite($tramite);
		$dueno = $tramite_local->Get_dato("t_iniciador");
		return $dueno;
	}
	
	function get_jefe($id){
		$query = sprintf("SELECT jefe FROM empleado WHERE idempleado=%s",$id);
		$rst = parent::consultar($query);
		
		while($fila = mysql_fetch_array($rst)){
			$jefe = $fila['jefe'];
		}
		$jefeInt = (int)$jefe;
		return $jefeInt;
	}
	
	function get_director($idEmpresa){
		$query = sprintf("SELECT * FROM usuario u INNER JOIN empleado e ON u.u_id = e.idempleado WHERE e.director_general =1",$id);
		$rst = parent::consultar($query);
		
		while($fila = mysql_fetch_array($rst)){
			$director = $fila['u_id'];
		}
		$directorInt = (int)$director;
		return $directorInt;
	}
	
	function get_administrador(){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("AUTORIZADOR_GENERAL");
		$autorizadorGeneral = (int)$agrup_usu->Get_dato("au_id");
		return $autorizadorGeneral;
	}
	
	function get_controlInterno(){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("CONTROL_INTERNO");
		$controlInterno = (int)$agrup_usu->Get_dato("au_id");
		return $controlInterno;
	}
	
	function get_controller(){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("CONTROLLER");
		$controller = (int)$agrup_usu->Get_dato("au_id");
		return $controller;
	}
	
	function generaExcepcion($idTramite, $presupuesto){
		$presupuesto = json_decode($presupuesto);
		
		if($presupuesto->excedePresupuesto){
			$solicitud = 0;
			$comprobacion = 0;
			$total = 0;
			$tramites = new Tramite();
			$tramites->Load_Tramite($idTramite);
			$flujoTramite = $tramites->Get_dato('t_flujo');
			
			switch($flujoTramite){
				case FLUJO_SOLICITUD:
					$tipoTramite = "Solicitud de Viaje";
					$query = sprintf("SELECT sv_id AS idSolicitud FROM solicitud_viaje WHERE sv_tramite = %s", $idTramite);
					$rst = parent::consultar($query);
					$solicitud = mysql_result($rst, 0, "idSolicitud");
					break;
				case FLUJO_SOLICITUD_GASTOS:
					$tipoTramite = "Solicitud de Gastos";
					$query = sprintf("SELECT sg_id AS idSolicitud FROM solicitud_gastos WHERE sg_tramite = %s", $idTramite);
					$rst = parent::consultar($query);
					$solicitud = mysql_result($rst, 0, "idSolicitud");
					break;
				case FLUJO_COMPROBACION:
					$tipoTramite = "Comprobaci&oacute;n de Viaje";
					$query = sprintf("SELECT co_id AS idComprobacion FROM comprobaciones WHERE co_mi_tramite = %s", $idTramite);
					$rst = parent::consultar($query);
					$comprobacion = mysql_result($rst, 0, "idComprobacion");
					break;
				case FLUJO_COMPROBACION_GASTOS:
					$tipoTramite = "Comprobaci&oacute;n de Gastos";
					$query = sprintf("SELECT co_id AS idComprobacion FROM comprobacion_gastos WHERE co_mi_tramite = %s", $idTramite);
					$rst = parent::consultar($query);
					$comprobacion = mysql_result($rst, 0, "idComprobacion");
					break;
			}
			$diferencia = abs($presupuesto->diferenciaPresupuesto);
			$mensaje = sprintf("La %s <strong>%05s</strong> ha excedido el presupuesto.", $tipoTramite, $idTramite);
			Add_excepcion($mensaje, $diferencia, $solicitud, $comprobacion, 0, 0, 0, 0, 1);
		}
	}
	
	function get_Excepciones($idTramite){
		$tramites = new Tramite();
		$tramites->Load_Tramite($idTramite);
		$flujoTramite = $tramites->Get_dato('t_flujo');
		
		switch($flujoTramite){
			case FLUJO_SOLICITUD:
				$condicionTipoExcepcion = "ex_solicitud = sv_id";
				$inner = "solicitud_viaje ON sv_id = ex_solicitud";
				$condicionGral = sprintf("sv_tramite = %s", $idTramite);
				break;
			case FLUJO_SOLICITUD_GASTOS:
				$condicionTipoExcepcion = "ex_solicitud = sg_id";
				$inner = "solicitud_gastos ON sg_id = ex_solicitud";
				$condicionGral = sprintf("sg_tramite = %s", $idTramite);
				break;
			case FLUJO_COMPROBACION:
				$condicionTipoExcepcion = "ex_comprobacion = co_id";
				$inner = "comprobaciones ON co_id = ex_comprobacion";
				$condicionGral = sprintf("co_mi_tramite = %s", $idTramite);
				break;
			case FLUJO_COMPROBACION_GASTOS:
				$condicionTipoExcepcion = "ex_comprobacion = co_id";
				$inner = "comprobacion_gastos ON co_id = ex_comprobacion";
				$condicionGral = sprintf("co_mi_tramite = %s", $idTramite);
				break;
		}
		
		$queryExcepciones = sprintf("SELECT COUNT(*) AS noExcepciones, 
				(SELECT COUNT(ex_presupuesto) FROM excepciones WHERE %s AND ex_presupuesto = 0) AS noExcepcionesPoliticas, 
				(SELECT COUNT(ex_presupuesto) FROM excepciones WHERE %s AND ex_presupuesto = 1) AS noExcepcionesPresupuesto 
				FROM excepciones 
				INNER JOIN %s 
				WHERE %s", $condicionTipoExcepcion, $condicionTipoExcepcion, $inner, $condicionGral);
		//error_log($queryExcepciones);
		$result = parent::consultar($queryExcepciones);
		$row = mysql_fetch_assoc($result);
		$excepciones = array('totalExcepciones' 		=> $row["noExcepciones"],
							'noExcepcionesPoliticas' 	=> $row["noExcepcionesPoliticas"],
							'noExcepcionesPresupuesto' 	=> $row["noExcepcionesPresupuesto"]);
		return json_encode($excepciones);
	}
	
	function rutaTipoIntNacional($tramite){
		// Construir el tipo de ruta dependiendo el tipo de region que se tenga en los itinerarios.
		$svi_solicitud = 0;
		$nacional = 0;
		$internacional = 0;
		$tipoRuta = "";
		
		$query = "SELECT svi_solicitud FROM sv_itinerario
				INNER JOIN solicitud_viaje ON svi_solicitud = sv_id
				INNER JOIN tramites ON sv_tramite = t_id
				WHERE t_id=$tramite";
		//error_log($query);
		$rst = parent::consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$svi_solicitud = $fila['svi_solicitud'];
		//error_log($svi_solicitud);
		
		// Tomara el tipo de vaje para el itinerario
		$queryViaje = sprintf("SELECT svi_tipo_viaje FROM sv_itinerario WHERE svi_solicitud=%s", $svi_solicitud);
		//error_log($queryViaje);
		$tipoViaje = parent::consultar($queryViaje);		
		// Primero comprobamos si es solo 1 itinerario o mas
		$row = mysql_num_rows($tipoViaje);
		//error_log("numero de filas".$row);
		
		while ($fila = mysql_fetch_assoc($tipoViaje)){
			if($row == 1){
				// Se tendra un solo itinerario ahora solo se validara si es nacional /internacional
				if($fila['svi_tipo_viaje'] == 'Nacional'){
					//error_log("Aqui nacional");
					$tipoRuta = "Nacional";
				}else if($fila['svi_tipo_viaje'] == 'Continental' || $fila['svi_tipo_viaje'] == 'Intercontinental'){
					//error_log("Aqui internacional");
					$tipoRuta="Internacional";
				}
			}else{
				//error_log("Aqui mas itinerario");
				if($fila['svi_tipo_viaje']== 'Nacional'){
					$nacional++;
				}elseif($fila['svi_tipo_viaje'] == 'Continental' || $fila['svi_tipo_viaje']== 'Intercontinental'){
					$internacional++;
				}
			}
		}
		
		if(($nacional == $internacional) && ($nacional != 0) && ($internacional !=0)){
			$tipoRuta="Internacional";
		}elseif($internacional>$nacional && $nacional==0){ //itinerarios internacionales unicamente
			$tipoRuta="Internacional";
		}elseif($internacional > $nacional && $nacional != 0){//itinerarios mezclados
			$tipoRuta="Internacional";
		}elseif($nacional>$internacional && $internacional==0){//itinerarios nacionales unicamente
			$tipoRuta="Nacional";
		}elseif($nacional>$internacional && $internacional !=0){//itinerarios mezclados
			$tipoRuta="Nacional";
		}
		
		return $tipoRuta;
	}
	
	public function asignaraAgencia($tramite){
		$debug = 0;
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Agencia");
		$agencia = $agrup_usu->Get_dato("au_id");
		$query_aa = sprintf("UPDATE tramites SET t_dueno = '%s' WHERE t_id = '%s'", $agencia, $tramite);
		parent::ejecutar($query_aa);
	}
	
	public function asignaraEmpleado($tramite){
		$debug = 1;
		$empleado = "";
		$query_emp = "";
		$query_emp = sprintf("SELECT t_iniciador FROM tramites WHERE t_id='%s'",$tramite);
		$rstempleado = parent::consultar($query_emp);
		$empleado = mysql_result($rstempleado,0,'t_iniciador');
		$query_aa=sprintf("UPDATE tramites SET t_dueno = '%s' WHERE t_id = '%s'",$empleado,$tramite);
		parent::ejecutar($query_aa);
	}
	
	public function requiereAgencia($idTramite){
		$query_tt = sprintf("SELECT IF(svi_tipo_transporte='Aéreo', 1, 0) AS svi_tipo_transporte 
						FROM sv_itinerario AS svi 
						JOIN solicitud_viaje AS sv ON svi.svi_solicitud = sv.sv_id 
						WHERE sv.sv_tramite = '%s'", $idTramite);
		$arr = parent::consultar($query_tt);
		$bandera_avion = false;
		
		while($dato=mysql_fetch_assoc($arr)){
			if($dato['svi_tipo_transporte'] == '1') $bandera_avion = true;
		}
		
		$query_ha = sprintf("SELECT svi_hotel_agencia 
						FROM sv_itinerario AS svi 
						JOIN solicitud_viaje AS sv ON svi.svi_solicitud = sv.sv_id 
						WHERE sv.sv_tramite = '%s'", $idTramite);
		$arr = parent::consultar($query_ha);
		$bandera_ha = false;
		
		while($dato=mysql_fetch_assoc($arr)){
			if($dato['svi_hotel_agencia'] == '1') $bandera_ha = true;
		}
		
		$query_ra = sprintf("SELECT svi_renta_auto_agencia 
						FROM sv_itinerario AS svi 
						JOIN solicitud_viaje AS sv ON svi.svi_solicitud = sv.sv_id 
						WHERE sv.sv_tramite = '%s'", $idTramite);
		$arr = parent::consultar($query_ra);
		$bandera_ra = false;
		
		while($dato=mysql_fetch_assoc($arr)){
			if($dato['svi_renta_auto_agencia'] == '1') $bandera_ra = true;
		}
		
		$bandera_agencia = ($bandera_avion == true || $bandera_ha==true) ? true : false;
		return $bandera_agencia;
	}

	public function requiereAnticipo($idTramite){
		$query_Ant = sprintf("SELECT * FROM solicitud_viaje WHERE sv_tramite = '%s'", $idTramite);
		$arr = parent::consultar($query_Ant);
		$bandera_Anticipo = false;
		
		while($dato=mysql_fetch_assoc($arr)){
			if($dato['sv_anticipo'] == '1') $bandera_Anticipo = true;
		}
		return $bandera_Anticipo;
	}
	
	function generaRutaAutorizacionSolicitudViaje($tramite, $idEmpleado, $autorizacion=false){
		$bandera_agencia = $this->requiereAgencia($tramite);
		
		// Se generar la ruta de Solicitud de viaje
		if($bandera_agencia == false || ($autorizacion == true)){
			$usuario = new Usuario();
			$usuario->Load_Usuario_By_ID($idEmpleado);
			//-------------------------------------- Empieza la toma de datos para la costruccion de la ruta-------------------------
			$ruta_autorizacion = "";
			$jefe = $this->get_jefe($idEmpleado);
			$empresa = $usuario->Get_dato("u_empresa");
			$director = $this->get_director($empresa);
			// Obtener tipo de Viaje
			$tipoRutaAutorizacion = $this->rutaTipoIntNacional($tramite);
			
			if($tipoRutaAutorizacion == "Nacional")
				$ruta_autorizacion = $jefe;
			else if($tipoRutaAutorizacion == "Internacional")
				$ruta_autorizacion = $jefe."|".$director;
			
			//error_log("ruta...........".$ruta_autorizacion);
			$queryRA = sprintf("UPDATE tramites SET t_iniciador = '%s', t_ruta_autorizacion = '%s' WHERE t_id = '%s'", $idEmpleado, $ruta_autorizacion, $tramite);
			parent::ejecutar($queryRA);
			return $ruta_autorizacion;
		}else{
			$this->asignaraAgencia($tramite);
			return "Agencia";
		}
	}
	
	function generaRutaAutorizacionSolicitudGastos($tramite,$iniciador){
		$ruta_autorizacion = "";
		$jefe = $this->get_jefe($iniciador);
		$ruta_autorizacion = $jefe."|".$this->get_administrador();
		return $ruta_autorizacion;
	}
	
	function generaRutaAutorizacionComprobacionViaje($tramite,$iniciador){
		$ruta_autorizacion = "";
		$jefe = $this->get_jefe($iniciador);
		$ruta_autorizacion = $jefe;
		return $ruta_autorizacion;
	}
	
	function generaRutaAutorizacionComprobacionGastos($tramite, $iniciador){
		$ruta_autorizacion = "";
		$jefe = $this->get_jefe($iniciador);
		$ruta_autorizacion = $jefe;
		return $ruta_autorizacion;
	}
	
	public function generarRutaAutorizacion($idTramite, $representante, $autorizacion = false){
		$rutaAutorizacion = "";
		$tramites = new Tramite();
		$tramites->Load_Tramite($idTramite);
		$t_flujo = $tramites->Get_dato('t_flujo');
		$t_iniciador = $tramites->Get_dato('t_iniciador');
		
		switch ($t_flujo){
			case FLUJO_SOLICITUD:
				$rutaAutorizacion = $this->generaRutaAutorizacionSolicitudViaje($idTramite, $t_iniciador, $autorizacion);
				break;
			case FLUJO_SOLICITUD_GASTOS:
				$rutaAutorizacion = $this->generaRutaAutorizacionSolicitudGastos($idTramite, $t_iniciador);
				break;
			case FLUJO_COMPROBACION:
				$rutaAutorizacion = $this->generaRutaAutorizacionComprobacionViaje($idTramite, $t_iniciador);
				break;
			case FLUJO_COMPROBACION_GASTOS:
				$rutaAutorizacion = $this->generaRutaAutorizacionComprobacionGastos($idTramite, $t_iniciador);
				break;
		}
		
		if($representante != 0)	$rutaAutorizacion = $t_iniciador."|";
		//error_log("--->>Ruta de Autorizacion: ".$rutaAutorizacion);
		$query_ra = sprintf("UPDATE tramites SET t_iniciador = '%s',t_ruta_autorizacion = '%s' WHERE t_id = '%s'", $t_iniciador, $rutaAutorizacion, $idTramite);
		//error_log("--->>Actualización ruta de autorización: ".$query_ra);
		parent::ejecutar($query_ra);
	}
	
	public function agregaAutorizadoresExcedentes($idTramite, $excepciones){
		$autorizadoresExcedentes = '';
		$excepciones = json_decode($excepciones);
		//error_log("Excepciones por politica: ".$excepciones->noExcepcionesPoliticas);
		//error_log("Excepciones por presupuesto: ".$excepciones->noExcepcionesPresupuesto);
		$tramites = new Tramite();
		$tramites->Load_Tramite($idTramite);
		$rutaAutorizacion = $tramites->Get_dato('t_ruta_autorizacion');
		$flujoTramite = $tramites->Get_dato('t_flujo');
		$autorizador = $this->get_administrador();
		$bandera_Anticipo = $this->requiereAnticipo($idTramite);
		
		switch ($flujoTramite){
			case FLUJO_SOLICITUD:
				$autorizadoresExcedentes = ($excepciones->noExcepcionesPresupuesto != 0) ? "|".$this->get_controller() : '';
				$autorizadoresExcedentes .= (!$this->requiereAgencia($idTramite)) ? "|".$this->get_administrador() : '';
				$autorizadoresExcedentes .= ($this->requiereAgencia($idTramite) && $bandera_Anticipo == true) ? "|".$this->get_administrador() : '';
				break;
			case FLUJO_SOLICITUD_GASTOS:
				$autorizadoresExcedentes = ($excepciones->noExcepcionesPoliticas != 0) ? "|".$this->get_controlInterno() : '';
				$autorizadoresExcedentes .= "|".$this->get_administrador();
				break;
			case FLUJO_COMPROBACION:
				$comprobaciones = new Comprobacion();
				$comprobaciones->Load_Comprobacion_By_co_mi_tramite($idTramite);
				$tramiteSolicitud = $comprobaciones->Get_dato('co_tramite');
				
				$autorizadoresExcedentes = ($excepciones->noExcepcionesPoliticas != 0) ? "|".$this->get_controlInterno() : '';
				$autorizadoresExcedentes .= (($tramiteSolicitud == -1) && ($excepciones->noExcepcionesPresupuesto != 0)) ? "|".$this->get_controller() : '';
				$autorizadoresExcedentes .= "|".$this->get_administrador();
				break;
			case FLUJO_COMPROBACION_GASTOS:
				$autorizadoresExcedentes = ($excepciones->noExcepcionesPoliticas != 0) ? "|".$this->get_controlInterno() : '';
				$autorizadoresExcedentes .= "|".$this->get_administrador();
				break;
		}
		
		$query = sprintf("UPDATE tramites SET t_ruta_autorizacion = CONCAT(t_ruta_autorizacion, '%s') WHERE t_id = '%s'", $autorizadoresExcedentes, $idTramite);
		parent::ejecutar($query);
	}
	
	public function generaRutaSegundaAutorizacion($iniciador, $tramite, $diferenciaMontos){
		//error_log("ya entro segunda ruta autorizacion........");
		$autorizador = 0;
		
		$tramiteSeg = new Tramite();
		$tramiteSeg->Load_Tramite($tramite);
		$ruta = $tramiteSeg->Get_dato("t_ruta_autorizacion");
		
		if($diferenciaMontos >= MONTO_EXCEDENTE_VUELO){
			$autorizador = (int)$this->get_controller();
		}
		
		$segundaRuta = "";
		$segundaRuta .= $ruta."|".$autorizador."|";
		error_log($segundaRuta);
		$query_seg = sprintf("UPDATE tramites SET t_ruta_autorizacion = '%s' WHERE t_id = '%s'", $segundaRuta, $tramite);
		parent::ejecutar($query_seg);
		return $autorizador;
	}
	
	public function getSiguienteAprobador($tramite, $dueno){
		$aprobador = "";
		
		$tramites = new Tramite();
		$tramites->Load_Tramite($tramite);
		$iniciador = $tramites->Get_dato('t_iniciador');
		$flujo = $tramites->Get_dato('t_flujo');
		$etapa = $tramites->Get_dato('t_etapa_actual');
		$iniciador = (int)$iniciador;
		$flujo = (int)$flujo;
		$etapa = (int)$etapa;
		
		if($iniciador == $dueno){
			//error_log("--->>Entro a primer aprobador");
			$ruta_tramite = array();
			$tramite_local = new Tramite();
			$tramite_local->Load_Tramite($tramite);
			$ruta_tramite = explode("|",$tramite_local->Get_dato("t_ruta_autorizacion"));
			$aprobador = $ruta_tramite[0];
			//error_log("-->>Aprobador: ".$aprobador);
		}else{
			//error_log("--->>Entro a sig aprobador");
			$ruta_tramite = array();
			$autorizaciones = array();
			$tramite_local = new Tramite();
			$tramite_local->Load_Tramite($tramite);
			$banderaauth = false;
			$ruta_tramite = explode("|", $tramite_local->Get_dato("t_ruta_autorizacion"));
			$no_autorizaciones = 0;
			
			if($tramite_local->Get_dato("t_autorizaciones") != ""){
				$autorizaciones = explode('|',$tramite_local->Get_dato("t_autorizaciones"));
				$no_autorizaciones = count($autorizaciones);
			}
			
			for($i=(int)$no_autorizaciones-2; $i < count($ruta_tramite); $i++){
				//error_log($ruta_tramite[$i]);
				if($ruta_tramite[$i] == $dueno){
					$aprobador = $ruta_tramite[$i+1];
					//error_log("aprobador".$aprobador);
					break;
				}
			}
		}
		return $aprobador;
	}
	
	public function getNombreAutorizadores($tramiteId){
		$ruta_del = array();
		$usuarioAprobador = new Usuario();
		$tramite = new Tramite();
		$tramite->Load_Tramite($tramiteId);
		$tramite_ruta = $tramite->Get_dato("t_ruta_autorizacion");
		//error_log("--->>> Ruta de Autorizacion: ".$tramite_ruta." <<<---");
		
		//Obtener los nombres de los Autorizadores
		$token = strtok($tramite_ruta,"|");
		$autorizadores = "";
		$encontrado = false;
		if($token != false){
			if($usuarioAprobador->Load_Usuario_By_ID($token)){
				$usuarioAprobador->Load_Usuario_By_ID($token);
				$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
			}else{
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_ID($token);
				$nombre_autorizadores = $agrup_usu->Get_dato("au_nombre");
			}
		
			$encontrado = strpos($token, "*");
			if($encontrado){
				$ruta_del = explode("*", $token);
				$usuarioAprobador->Load_Usuario_By_ID($ruta_del[0]);
				$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
				$autorizadores .= "<font color='#0000CA'>".$nombre_autorizadores."</font> AUTORIZO EN NOMBRE DE: ";
				$usuarioAprobador->Load_Usuario_By_ID($ruta_del[1]);
				$nombre_autorizadores1 = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
				$autorizadores .= $nombre_autorizadores1;
			}else{
				$autorizadores .= $nombre_autorizadores;
			}
			$token = strtok("|");
			while($token != false){
				$autorizadores .= ", ";
				if($usuarioAprobador->Load_Usuario_By_ID($token)){
					$usuarioAprobador->Load_Usuario_By_ID($token);
					$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
				}else{
					$agrup_usu = new AgrupacionUsuarios();
					$agrup_usu->Load_Grupo_de_Usuario_By_ID($token);
					$nombre_autorizadores = $agrup_usu->Get_dato("au_nombre");
				}
				$encontrado = strpos($token, "*");
				if($encontrado){
					$ruta_del = explode("*", $token);
					$usuarioAprobador->Load_Usuario_By_ID($ruta_del[0]);
					$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
					$autorizadores .= "<font color='#0000CA'>".$nombre_autorizadores."</font> AUTORIZO EN NOMBRE DE: ";
					$usuarioAprobador->Load_Usuario_By_ID($ruta_del[1]);
					$nombre_autorizadores1 = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
					$autorizadores .= $nombre_autorizadores1;
				}else{
					$autorizadores .= $nombre_autorizadores;
				}
				$token = strtok("|");
			}
		}
		
		return $autorizadores;
	}
} ?>