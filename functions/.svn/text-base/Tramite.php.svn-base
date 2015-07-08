<?php
require_once "$RUTA_A/Connections/fwk_db.php";
require_once("$RUTA_A/lib/php/configuracion.mailgun.php");
require_once("$RUTA_A/lib/php/constantes.php");

class Tramite extends conexion {

    protected $t_id;
    protected $rst_tramite;

    public function __construct() {
        parent::__construct();
        $this->t_id = "";
    }

    /**
     * Cargamos los datos de un tramite
     *
     * @param integer $id
     */
    public function Load_Tramite($id) {
        $query = sprintf("select * from tramites where t_id=%s", $id);		
        $this->rst_tramite = parent::consultar($query);
        if (parent::get_rows() > 0) {
            $this->t_id = $this->Get_dato("t_id");
        }
    }

    /**
     * Regresamos El valor de un campo de la Tabla de un Tramite previamente cargado
     *
     * @param text $nombre
     * @return text
     */
    public function Get_dato($nombre) {
        return(mysql_result($this->rst_tramite, 0, $nombre));
    }

	/**
	 * Detenrmina la etapa del flujo para aquellos tramites que cuenten con un delegado/representante
	 *
	 * @param flujo int => f_id  de la tabla de flujos
	 * @return int 		=> et_etapa_id de la tabla de etapas	 
	 */	
	public function obtenerEtapaDelegados($flujo){
		switch ($flujo){
			case FLUJO_SOLICITUD:
				$etapa = SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
			case FLUJO_SOLICITUD_GASTOS:
				$etapa = SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
			case FLUJO_COMPROBACION:
				$etapa = COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
			case FLUJO_COMPROBACION_GASTOS:
				$etapa = COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
		}			
		return $etapa;
	}

	
    /**
     *      Crea un nuevo tramite
     */    
    public function Crea_Tramite($iniciador, $empresa, $etapa_actual, $flujo, $motivo, $delegado = 0) {
		$delegado = (empty($delegado)) ? 0 : $delegado;
		
        $query = sprintf("INSERT INTO tramites ( t_id, 
                                                 t_fecha_registro, 
                                                 t_fecha_ultima_modificacion, 
                                                 t_iniciador, 
                                                 t_dueno, 
                                                 t_etapa_actual, 
                                                 t_flujo, 
                                                 t_etiqueta, 
        										 t_delegado, 
        										 t_autorizaciones_historial, 
        										 t_autorizaciones,
        										 t_segunda_autorizacion )
					VALUES(                 
                                                default, 
                                                now(), 
                                                now(), 
                                                '%s', 
                                                '%s', 
                                                '%s', 
                                                '%s', 
                                                '%s',
        										'%s',
        										'',
        										'',
        										'0' )", 
                                                $iniciador, 
                                                $iniciador, 
                                                $etapa_actual, 
                                                $flujo, 
                                                $motivo,
        										$delegado
                                                );
        $this->t_id = parent::insertar($query);        
        $this->Load_Tramite($this->t_id);
        return($this->t_id);
    }

    /**
     *      Actualiza t_segunda_autorizacion
     */
    
    public function set_t_segunda_autorizacion($t_id,$valor) {

        $query = sprintf("update tramites set t_segunda_autorizacion = '%s' where t_id = '%s'",$valor,$t_id);
        
        parent::ejecutar($query);
        //error_log($query);
        $this->Load_Tramite($t_id);
        return($this->t_id);
    }

    /**
     *      Actualiza t_ruta_autorizacion
     */
    
    public function set_t_ruta_autorizacion($t_id,$valor) {

        $query = sprintf("update tramites set t_ruta_autorizacion = '%s' where t_id = '%s'",$valor,$t_id);
        
        parent::ejecutar($query);
        //error_log($query);
        $this->Load_Tramite($t_id);
        return($this->t_id);
    }

    /**
     *      Actualiza t_comprobado
     */
    
	public function set_t_comprobado($t_id,$valor){
		// Seleccionar el tramite de la Solicitud asociado a la Comprobacion
		$query = sprintf("SELECT co_tramite FROM comprobaciones WHERE co_mi_tramite = '%s'", $t_id);
		$rst = parent::consultar($query);
		$tramiteSolicitud = mysql_result($rst, 0, 'co_tramite');
		
		// Actualizar el campo de t_comprobado de la Solicitud asociado a la Comprobacion
		$query = sprintf("UPDATE tramites SET t_comprobado = '%s' WHERE t_id = '%s'", $valor, $tramiteSolicitud);
		parent::ejecutar($query);
		
		// Actualizar el campo de t_comprobado del tramite de la Comprobacion
		$query = sprintf("UPDATE tramites SET t_comprobado = '%s' WHERE t_id = '%s'", $valor, $t_id);      
		parent::ejecutar($query);
		
		//error_log($query);
		$this->Load_Tramite($t_id);
		return($this->t_id);
	}
    
    /**
     * Funcion que  nos permitira actualizar el campo de cierre de fecha(t_fecha_cierre), cuando una solicitud o comprobacion ya ha finalizado
     * su aprobacion
     */
    public function setCierreFecha($t_id){
    	$queryCierreFecha = sprintf("UPDATE tramites set t_fecha_cierre = now() WHERE t_id = '%s'",$t_id);    	
    	parent::ejecutar($queryCierreFecha);
    }
    
    /**
     * Actualizar la etiqueta del tramite en una edición de una Solicitud 
     */
    public function actualizarInfoTramite($t_id, $etiqueta, $representante){
    	$representante = (empty($representante)) ? 0 : $representante;
    	
		// Actualizar la etiqueta y el delegado en el tramite
		$queryEtiqueta = sprintf("UPDATE tramites SET t_etiqueta = '%s', t_delegado = '%s' WHERE t_id ='%s'",$etiqueta, $representante, $t_id);
		parent::ejecutar($queryEtiqueta);
    }
	

    /*
     *      Cambia el tramite a otra etapa
     */

    public function Modifica_Etapa($t_id, $etapa, $flujo, $dueno, $ruta_autorizadores, $delegado = 0){
	
		if(!empty($delegado))
			$etapa = $this->obtenerEtapaDelegados($flujo);
		
        if ($ruta_autorizadores == "")
            $query = sprintf("UPDATE tramites SET t_etapa_actual='%s', t_dueno = '%s', t_segunda_autorizacion = '0' WHERE t_id='%s'", $etapa, $dueno, $t_id);
        else
            $query = sprintf("UPDATE tramites SET t_etapa_actual='%s', t_dueno = '%s', t_segunda_autorizacion = '0', t_ruta_autorizacion = '%s' WHERE t_id='%s'", $etapa, $dueno, $ruta_autorizadores, $t_id);
        
        parent::ejecutar($query);
    }

    /*
     *      Cambia el tramite a otro dueno para que ahora este lo autorize
     */

    public function Modifica_Dueno($t_id, $etapa, $flujo, $aprobador_actual, $aprobador_siguiente) {
        $query = sprintf("SELECT t_autorizaciones FROM tramites WHERE t_id = '%s'", $t_id);
        $rst = parent::consultar($query);
        $aux = mysql_result($rst, 0, 't_autorizaciones');
		//SOLICITUD_INVITACION_ETAPA_RECHAZADA
		if($etapa == COMPROBACION_ETAPA_RECHAZADA ){
			if($aux == ""){
				$query = sprintf("UPDATE tramites SET t_etapa_actual='%s', t_flujo = '%s', t_dueno = '%s', t_autorizaciones_historial = %s, t_autorizaciones = '%s' WHERE t_id='%s'", $etapa, $flujo, $aprobador_siguiente, "CONCAT(t_autorizaciones_historial, $aprobador_actual)", "", $t_id);
			}else{
				$query = sprintf("UPDATE tramites SET t_etapa_actual='%s', t_flujo = '%s', t_dueno = '%s', t_autorizaciones_historial = %s, t_autorizaciones = '%s' WHERE t_id='%s'", $etapa, $flujo, $aprobador_siguiente, "CONCAT(t_autorizaciones_historial, '|', $aprobador_actual)", "", $t_id);
			}	
		}else{
			if($aux == ""){
				$query = sprintf("UPDATE tramites SET t_etapa_actual='%s', t_flujo = '%s', t_dueno = '%s', t_autorizaciones_historial = %s, t_autorizaciones = %s WHERE t_id='%s'", $etapa, $flujo, $aprobador_siguiente, "CONCAT(t_autorizaciones_historial, $aprobador_actual)", "CONCAT(t_autorizaciones, $aprobador_actual)", $t_id);
			}else{
				$query = sprintf("UPDATE tramites SET t_etapa_actual='%s', t_flujo = '%s', t_dueno = '%s', t_autorizaciones_historial = %s, t_autorizaciones = %s WHERE t_id='%s'", $etapa, $flujo, $aprobador_siguiente, "CONCAT(t_autorizaciones_historial, '|', $aprobador_actual)", "CONCAT(t_autorizaciones, '|', $aprobador_actual)", $t_id);
			}
		}

		//error_log($query);
		parent::insertar($query);
    }
    
    /*
     *      Cambia dueño del Tramite para que ahora este lo autorize
    */
    
    public function Modifica_Dueno_Etapa($t_id, $etapa, $flujo, $aprobador_siguiente) {
    	$query = sprintf("UPDATE tramites SET t_etapa_actual = '%s', t_dueno = '%s' WHERE t_id = '%s'", $etapa, $aprobador_siguiente, $t_id);
    	error_log($query);
    	parent::insertar($query);
    }

    /*
     *      Obtiene el nombre de la etapa
     */

    public function Get_EtapaNombre($etapa, $flujo) {
        $query = "SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = " . $etapa . " AND et_flujo_id = " . $flujo;
        //error_log($query);
        $rst = parent::consultar($query);
        return(mysql_result($rst, 0, 'et_etapa_nombre'));
    }

    /*
     *       Esta funcion determina el siguiente autorizador para el tramite. Funciona de
     *    la siguiente manera:
     * 
     *      1) Extrae la lista de t_autorizaciones
     *      2) Busca el dueno actual en la lista de t_autorizaciones
     *      3) Si hay un autorizador despues del dueno lo regresa, si no regresa -1
     * 
     */

    public function Get_Siguiente_Autorizador($t_id) {
        $query = "SELECT t_dueno, t_ruta_autorizacion FROM tramites WHERE t_id = " . $t_id;
        //error_log($query);
        $rst = parent::consultar($query);
        $t_dueno = mysql_result($rst, 0, 't_dueno');
        $t_autorizaciones = mysql_result($rst, 0, 't_ruta_autorizacion');

        //error_log("t_dueno=" . $t_dueno);
        //error_log("t_autorizaciones=" . $t_autorizaciones);

        $t_autorizaciones_array = explode('|', $t_autorizaciones);

        for ($i = 0; $i < count($t_autorizaciones_array); $i++) {
            if ($t_autorizaciones_array[$i] == $t_dueno) {
                if ($i < count($t_autorizaciones_array) - 1) {
                    return $t_autorizaciones_array[$i + 1];
                } else {
                    return "-1";
                }
            }
        }
    }

    /*
     *  Anade un nuevo id a la lista de autorizadores que ya dieron su vo.bo a una solicitud
     */

    public function AgregaSiguienteAutorizador($t_id, $t_autorizador_id) {

        $query = "SELECT t_autorizaciones FROM tramites WHERE t_id = " . $t_id;
        //error_log($query);
        $rst = parent::consultar($query);
        $t_autorizaciones = mysql_result($rst, 0, 't_autorizaciones');
        $t_autorizaciones_array = explode('|', $t_autorizaciones);
        array_push($t_autorizaciones_array, $t_autorizador_id);

        $query = "UPDATE tramites SET t_autorizaciones = '" . join("|", $t_autorizaciones_array) . "' WHERE t_id = " . $t_id;
        //error_log($query);
        parent::insertar($query);
    }

    /*
     *  Regresa la lista de ids de los usuarios que tienen que autorizar la solicitud
     */

    public function GetRutaAutorizacion($t_id) {

        $query = "SELECT t_ruta_autorizacion FROM tramites WHERE t_id = " . $t_id;
        //error_log($query);
        $rst = parent::consultar($query);
        $t_autorizaciones = mysql_result($rst, 0, 't_ruta_autorizacion');
        $t_autorizaciones_array = explode('|', $t_autorizaciones);
        return $t_autorizaciones_array;
    }

    /*
     *  Regresa la lista de ids de los usuarios que autorizaron la solicitud
     */

    public function GetAutorizaciones($t_id) {

        $query = "SELECT t_autorizaciones FROM tramites WHERE t_id = " . $t_id;
        //error_log($query);
        $rst = parent::consultar($query);
        $t_autorizaciones = mysql_result($rst, 0, 't_autorizaciones');
        $t_autorizaciones_array = explode('|', $t_autorizaciones);
        return $t_autorizaciones_array;
    }

    /*
     *      Envia el mensaje al dueño, asi como al iniciador de la solicitud sin enviar e-mail
     */

    public function EnviaMensajeSinMail($t_id, $mensaje) {

        $query = "SELECT t_dueno, t_iniciador FROM tramites WHERE t_id = " . $t_id;
        ////error_log($query);
        $rst = parent::consultar($query);
        $t_dueno = mysql_result($rst, 0, 't_dueno');
        $t_iniciador = mysql_result($rst, 0, 't_iniciador');

        $this->EnviaNotificacionSinMail($t_id, $mensaje, $t_dueno, $t_dueno);
        $this->EnviaNotificacionSinMail($t_id, $mensaje, $t_dueno, $t_iniciador);
    }

    /*
     *      Envia una notificacion sin enviar e-mail
     */

    public function EnviaNotificacionSinMail($t_id, $mensaje, $remitente, $destinatario) {

        $query = sprintf("INSERT INTO notificaciones ( nt_id, nt_descripcion, nt_tramite, nt_remitente, nt_asignado_a, nt_fecha )
						                      VALUES( default, '%s', %s, %s, %s, now() )", $mensaje, $t_id, $remitente, $destinatario
        );
        //error_log($query);
        $n_id = parent::insertar($query);

        //$this->EnviaNotificacionEmail($t_id, $mensaje, $remitente, $destinatario);

        return($n_id);
    }

    /*
     *      Envia el mensaje al dueño, asi como al iniciador de la solicitud
     */

    public function EnviaMensaje($t_id, $mensaje) {

        $query = "SELECT t_dueno, t_iniciador FROM tramites WHERE t_id = " . $t_id;
        //error_log($query);
        $rst = parent::consultar($query);
        $t_dueno = mysql_result($rst, 0, 't_dueno');
        $t_iniciador = mysql_result($rst, 0, 't_iniciador');

        $this->EnviaNotificacion($t_id, $mensaje, $t_dueno, $t_dueno, "0", "");
        $this->EnviaNotificacion($t_id, $mensaje, $t_dueno, $t_iniciador, "0", "");
    }
    
    /*
     * Verificar etapa del tramite para desactivar notificación
     */
    public function verificaEtapa($tramiteID){
   		$inactiva = 0;
    	$tramite =  $this->Load_Tramite($tramiteID);
    	$t_flujo = $this->Get_dato('t_flujo');
    	$t_etapa_actual = $this->Get_dato('t_etapa_actual');
    	
    	if($t_flujo == FLUJO_SOLICITUD && $t_etapa_actual == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR){
    		$inactiva = 1;
    	}else if($t_flujo == FLUJO_SOLICITUD_GASTOS && $t_etapa_actual == SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR){
    		$inactiva = 1;
    	}else if($t_flujo == FLUJO_COMPROBACION && $t_etapa_actual == COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR){
    		$activar = 1;
    	}else if($t_flujo == FLUJO_COMPROBACION_GASTOS && $t_etapa_actual == COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR){
    		$inactiva = 1;
    	}
    	//error_log("----->>>>Inactiva: ".$inactiva.", Flujo: ".$t_flujo.", Etapa: ".$t_etapa_actual." y Tramite:".$tramiteID);
    	return $inactiva;
    }
    
    /**
     * Determina el mensaje que se construirá si existe un representante o si simplemente es un usuario normal.
     *
     * @param t_id int				=> Consecutivo del tramite de la tabla de tramites
     * @param etapa int 			=> et_etapa_id de la tabla de etapas
     * @param creacion boolean 		=> boolean
     * @param autorizacion boolean	=> boolean
     * @param representante int		=> u_id de la tabla de usuario
     * @return str 					=> Mensaje con el nombre del(os) usuario(s) involucrado(s)
     */
	public function crearMensaje($t_id, $etapa, $creacion = false, $autorizacion = false, $representante){
		
		$rechazadas = array(array(FLUJO_SOLICITUD, 				SOLICITUD_ETAPA_RECHAZADA),
							array(FLUJO_SOLICITUD, 				SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR),
							array(FLUJO_SOLICITUD_GASTOS,		SOLICITUD_GASTOS_ETAPA_RECHAZADA),
							array(FLUJO_SOLICITUD_GASTOS, 		SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR),
							array(FLUJO_COMPROBACION, 			COMPROBACION_ETAPA_RECHAZADA),
							array(FLUJO_COMPROBACION, 			COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR),							
							array(FLUJO_COMPROBACION_GASTOS, 	COMPROBACION_GASTOS_ETAPA_RECHAZADA),
							array(FLUJO_COMPROBACION_GASTOS, 	COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR)
							);
			
		$this->Load_Tramite($t_id);
		$flujoId = $this->Get_dato('t_flujo');
		$jefe = $this->Get_dato('t_iniciador');
		$t_dueno = $this->Get_dato('t_dueno');
		
		$mensaje = "";
		$autor = "";
		$usuario = new Usuario();
		
		if(!empty($representante)){
			$usuario->Load_Usuario_By_ID($representante);
			$nombreRepresentante = $usuario->Get_dato('nombre');
			$autor = sprintf("<strong>%s</strong>", $nombreRepresentante);
			$autor = ($creacion && !$autorizacion) ? $autor." en su nombre" : $autor." en nombre de: ";
		}
		
		if(!$creacion && $autorizacion){
			if($usuario->Load_Usuario_By_ID($t_dueno)){
				$nombreJefe = $usuario->Get_dato('nombre');
			}else{
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
				$nombreJefe = $agrup_usu->Get_dato("au_nombre");
			}
		}else{
			$usuario->Load_Usuario_By_ID($jefe);
			$nombreJefe = $usuario->Get_dato('nombre');
		}
		
		
		if(empty($representante) || $autorizacion)
			$autor.= sprintf("<strong>%s</strong>", $nombreJefe);

		$flujo = new Flujo();
		$flujo->Load($flujoId);
		$flujoNombre = ucwords(strtolower($flujo->Get_dato("f_nombre")));
		
		$evento = ($creacion) ? "CREADA" : "AUTORIZADA";
		foreach($rechazadas as $key => $val){
            if($val[0] == $flujoId && $etapa == $val[1] ){
				$evento = "RECHAZADA";
                break; 
            }
		}
		
		$mensaje = sprintf("La %s <strong>%05s</strong> ha sido <strong>%s</strong> por: %s y requiere de su autorizaci&oacute;n.", $flujoNombre, $t_id, $evento, $autor);
        
		
		if(!$creacion && $autorizacion){
			$mensaje = str_replace(" y requiere de su autorizaci&oacute;n", "", $mensaje);
		}
		
		return $mensaje;
	}

    /*
     *      Envia una notificacion
     */
    public function EnviaNotificacion($t_id, $mensaje, $remitente, $destinatario, $mail, $mensaje_email, $link=1) {
    	
         $query = sprintf("INSERT INTO notificaciones (nt_id, nt_descripcion, nt_tramite, nt_remitente, nt_asignado_a, nt_fecha, nt_comentarios)
			VALUES( default, '%s', %s, %s, %s, now(), '')", $mensaje, $t_id, $remitente, $destinatario);
        $n_id = parent::insertar($query);
        $desactivar = $this->verificaEtapa($t_id);
        if($desactivar){
        	$queryInactivaNotificacion = sprintf("UPDATE notificaciones SET nt_activo = '0' WHERE nt_tramite = '%s' AND nt_id = '%s'", $t_id , $n_id);
        	parent::ejecutar($queryInactivaNotificacion);
        }
		if($mail == "1"){
			$this->EnviaNotificacionEmail($t_id, $mensaje, $remitente, $destinatario, $link);
		}
		
        return($n_id);
    }

    /*
     *      Envia una por correo
     */
    
    public function validaTramiteActivo($queryTr) {               
        $rst = parent::consultar($queryTr);
        return mysql_result($rst, 0, 't_id');               
    }

    public function EnviaNotificacionEmail($t_id, $mensaje, $remitente, $destinatario, $link) {

        /*global $SMTP_HOST;
        global $SMTP_PORT;
        global $SMTP_AUTH;
        global $SMTP_USERNAME;
        global $SMTP_PASSWORD;
        global $SMTP_FROM;
        global $SMTP_FROMNAME;*/
        
        global $SMTP_ACTIVAR_CORREO;
        
        global $SERVER;
        global $RUTA_R;

        /*// Configuracion del servidor de correo
        $mail = new PHPMailer();
        $mail->IsSMTP(); // send via SMTP
        $mail->Host = $SMTP_HOST;
        $mail->Port = $SMTP_PORT;
        $mail->SMTPAuth = $SMTP_AUTH;
        $mail->Username = $SMTP_USERNAME;
        $mail->Password = $SMTP_PASSWORD;
        $mail->From = $SMTP_FROM;
        $mail->FromName = $SMTP_FROMNAME;

        $mail->WordWrap = 50;  // set word wrap
        $mail->IsHTML(TRUE);*/

        $query = "SELECT u_email FROM usuario u WHERE u_id = " . $remitente;
        $rst = parent::consultar($query);
        $remitente_email = mysql_result($rst, 0, 'u_email');
        
        $query = "SELECT u_usuario FROM usuario u WHERE u_id = " . $destinatario;
        $rst = parent::consultar($query);
        $usuario = mysql_result($rst, 0, 'u_usuario');
        
        $query = "SELECT u_passwd FROM usuario u WHERE u_id = " . $destinatario;
        $rst = parent::consultar($query);
        $usuariopsw = mysql_result($rst, 0, 'u_passwd');

        $query = "SELECT u_email FROM usuario u WHERE u_id = " . $destinatario;
        $rst = parent::consultar($query);
        $destinatario_email = mysql_result($rst, 0, 'u_email');

        $query = "select t_flujo from tramites where t_id = " . $t_id;
        $rst = parent::consultar($query);
        $flujo_tramite = mysql_result($rst, 0, 't_flujo');
		
		$acceder_a = "";
		$url = "";
		if($flujo_tramite==FLUJO_SOLICITUD_GASTOS || $flujo_tramite==FLUJO_SOLICITUD){
			$acceder_a = "Acceder a la solicitud";
		}else if($flujo_tramite==FLUJO_COMPROBACION_GASTOS || $flujo_tramite==FLUJO_COMPROBACION){
			$acceder_a = "Acceder a la comprobaci&oacute;n";
		}
		
		if($link != 0 || $link != "0"){
			$url = sprintf("<br /><br />Para poder revisar el tr&aacute;mite debe ingresar al sistema en: <a href='http://%s%sindex.php?id=%s'>%s.</a>", $SERVER, $RUTA_R, $t_id, $acceder_a);
		}
		//error_log("--->urlBMW: ".$urlBMW);

        //$addr = "andres.portillo@gmail.com"; // de prueba
        //$mail->AddAddress($addr, $addr);
        
        if ($SMTP_ACTIVAR_CORREO) {
			$dest = $destinatario_email;
        }
        //error_log("destinatario_email=" . $destinatario_email);

        $subject = "Solicitud-Comprobantes por Autorizar";
        
        $body = sprintf("<font style='font-size:11.0pt; font-family: Arial, Helvetica, sans-serif'>Estimado Asociado:<br /><br />
				%s
				%s<br /><br />
				Atentamente, <br />Sistema de Gastos de Viaje<br /><br />
				Bonafont HOD<br /></font>
				<br />
				<font style='font-size:10.0pt; font-family: Arial, Helvetica, sans-serif'><strong>Este correo es informativo, favor de no responder a esta direcci&oacute;n, ya que no se encuentra habilitada para recibir mensajes</strong></font> 
				<br /><br />", $mensaje, $url);

        $body = sprintf("<font style='font-size:11.0pt; font-family: Arial, Helvetica, sans-serif'>Estimado Empleado:<br /><br />
				%s
				%s<br /><br />
				Atentamente, <br />Sistema de Gastos de Viaje<br /><br />
				Bonafont HOD<br /></font>
				<br />
				<font style='font-size:10.0pt; font-family: Arial, Helvetica, sans-serif'><strong>Este correo es informativo, favor de no responder a esta direcci&oacute;n, ya que no se encuentra habilitada para recibir mensajes</strong></font> 
				<br /><br />", $mensaje, $url);
		//error_log($mail->Body);
		parametroMail ($subject, $dest, $body);
		//parametroMail("Solicitud-Comprobantes por Autorizar",$destinatario_email,$mail->Body);
		//$mail->Send();
    }

	//consulta que nos regresara el calculo de los dias
	public function calculoDias($idTramite){
		//error_log("ENTRA A CALCULO DE DIAS");
		$totalDias=0;
		$tipoViaje="";
		$FechaSalida=0;
		$FechaLlegada=0;

		//checamos si el tipo de viaje es Redondo para poder sumarle un dia a su calculo de fechas.
		$query="SELECT sv_viaje FROM solicitud_viaje WHERE sv_tramite = $idTramite";
		$rst = parent::consultar($query);
		while ($fila = mysql_fetch_assoc($rst)) {
			$tipoViaje=$fila['sv_viaje'];
		}
		
		if($tipoViaje == "Multidestinos"){
			$query="SELECT svi_fecha_salida AS fechaLlegada,MAX(svi_fecha_salida) AS fechaSalida
			FROM sv_itinerario INNER JOIN solicitud_viaje ON (sv_itinerario.svi_solicitud=solicitud_viaje.sv_id) 
			INNER JOIN tramites ON (tramites.t_id=solicitud_viaje.sv_tramite) 
			INNER JOIN etapas ON etapas.et_etapa_id = tramites.t_etapa_actual AND tramites.t_flujo = etapas.et_flujo_id
			LEFT JOIN cat_cecos ON (sv_ceco_paga = cc_id)
			WHERE t_id={$idTramite} 
			ORDER BY svi_fecha_salida ASC
			LIMIT 0, 1";
		}else{
			$query="SELECT sum(svi_dias_viaje) as svi_dias_viaje,svi_fecha_salida AS fechaSalida ,svi_fecha_llegada AS fechaLlegada
			FROM sv_itinerario INNER JOIN solicitud_viaje ON (sv_itinerario.svi_solicitud=solicitud_viaje.sv_id)
			INNER JOIN tramites ON (tramites.t_id=solicitud_viaje.sv_tramite)
			INNER JOIN etapas ON etapas.et_etapa_id = tramites.t_etapa_actual AND tramites.t_flujo = etapas.et_flujo_id
			LEFT JOIN cat_cecos ON (sv_ceco_paga = cc_id)
			WHERE t_id={$idTramite}";
		}				
		$rst = parent::consultar($query);
		
		while ($fila = mysql_fetch_assoc($rst)) {
			if($tipoViaje == "Sencillo"){
				$totalDias=$fila['svi_dias_viaje'];
			}			
			$FechaLlegada=$fila['fechaLlegada'];
			$FechaSalida=$fila['fechaSalida'];
		}		
		
		if($tipoViaje == "Multidestinos"){			
			$queryDias="SELECT DATEDIFF('{$FechaSalida}','{$FechaLlegada}') AS svi_dias_viaje;";
			$rst = parent::consultar($queryDias);
			while ($fila = mysql_fetch_assoc($rst)) {
				$totalDias=$fila['svi_dias_viaje'];
				$totalDias = $totalDias + 1;
			}
		}else if($tipoViaje == "Redondo"){
			$queryDias="SELECT DATEDIFF('{$FechaLlegada}','{$FechaSalida}') AS svi_dias_viaje;";
			//error_log($queryDias);
			$rst = parent::consultar($queryDias);
			while ($fila = mysql_fetch_assoc($rst)) {
				$totalDias=$fila['svi_dias_viaje'];				
				$totalDias = $totalDias + 1;
			}	
		}		
		 //error_log("Dias de Viaje redondo".$totalDias);
		 return $totalDias;
	}

	function calculoTotalSolicitud($idTramite,$totHotel,$totAuto,$totAvion,$rowAuto,$rowHotel,$rowAvion,$cotizaAutoEmpleado,$cotizaHotelEmpleado){
		$totHotel=floatval($totHotel);
		$totAuto=floatval($totAuto);
		$totAvion=floatval($totAvion);
		//quiere decir que se ha ingresado alguna cotizacion de hotel y/o auto && $totAuto != 0 && $totHotel != 0 
		if(($rowHotel >= 1 && $rowAuto >= 1 && $rowAvion=1 ) || ($rowHotel >= 1 && $rowAuto == 0 && $rowAvion=1) || ($rowHotel == 0 && $rowAuto >= 1 && $rowAvion=1) || ($rowHotel == 0 && $rowAuto == 0 && $rowAvion=1)){
			//obtener el total de la solicitud y el total de anticipos ( Esto es por si existe un cambio de cotizacion de auto y/o Hotel)
			//se añadio la cotizacion de avion
			$cotizacionEmpleadoHotel=0;
			$cotizacionEmpleadoAuto=0;
			$TotAnticipo=0;
			
			//Total de anticipos  solo se tomara el total de los anticipos a escepcion de Renta de auto y hotel
			$Anticipos = "SELECT ccbmw.cp_concepto,
			svcd.svc_detalle_monto_concepto AS svc_detalle_monto_concepto,
			SUM(svcd.svc_conversion) AS svc_conversion
			FROM sv_conceptos_detalle AS svcd
			INNER JOIN solicitud_viaje AS sv
			ON sv.sv_tramite = svcd.svc_detalle_tramite
			INNER JOIN tramites AS t
			ON sv.sv_tramite = t.t_id
			INNER JOIN cat_conceptosbmw AS ccbmw
			ON svcd.svc_detalle_concepto = ccbmw.dc_id
			WHERE t.t_id = '{$idTramite}'
			GROUP BY ccbmw.cp_concepto";
				
			$rstAnticipos = parent::consultar($Anticipos);
				
			while($fila=mysql_fetch_array($rstAnticipos)){
				if($fila["cp_concepto"] != "Hotel" && $fila["cp_concepto"] != "Renta de auto" ){
					$TotAnticipo = $TotAnticipo + $fila["svc_conversion"];
				}
			}
				
			error_log("TotAnticipo".$TotAnticipo);
			
			//Cotizaciones hechas a Agencia o cotizadas por el usuario
			$querycotizaciones="SELECT svi_id FROM sv_itinerario svi							
								INNER JOIN solicitud_viaje sv
								ON sv.sv_id = svi.svi_solicitud
								INNER JOIN tramites tr
								ON tr.t_id = sv.sv_tramite
								WHERE t_id ={$idTramite}";
			//error_log($querycotizaciones);
			$rstCotizaciones = parent::consultar($querycotizaciones);
			//obtenemos la cotizacion incial del usuario
			while($fila=mysql_fetch_array($rstCotizaciones)){				
				$cotizacionEmpleadoHotel+=$this->obtenerCotizacionInicialHotel($fila['svi_id']);
				$cotizacionEmpleadoAuto+=$this->obtenerCotizacionInicialAuto($fila['svi_id']);
			}

			error_log("Total cotizado H por empleado".$cotizacionEmpleadoHotel);
			error_log("Total cotizado A por empleado".$cotizacionEmpleadoAuto);
			
			//$CalculoHA=$TotSolicitud-$TotAnticipo;			
			$CalculoNewHNewA=$totHotel+$totAuto+$totAvion;
			error_log("Total de lo nuevo".$CalculoNewHNewA);			
			//Se actualizara el total de la solicitud 			
			$TS=$cotizacionEmpleadoAuto+$cotizacionEmpleadoHotel+$CalculoNewHNewA+$TotAnticipo;
			$queryATot=sprintf("UPDATE solicitud_viaje SET sv_total=%s  WHERE sv_tramite=%s",$TS,$idTramite);
			parent::insertar($queryATot);
						
		}
	}
	
	public function obtenerCotizacionInicialHotel($svi_id){
		$h_total_pesos=0;
		$hotelAgencia = 0;
		
		$queryHotelInicial=sprintf("SELECT h_total_pesos FROM hotel WHERE svi_id=%s AND h_cotizacion_inicial=1",$svi_id);
		$rstHI= parent::consultar($queryHotelInicial);
		while ($aux = mysql_fetch_assoc($rstHI)) {
			$h_total_pesos += $aux['h_total_pesos'];
		}
		
		$queryHotelAgencia=sprintf("SELECT svi_hotel_agencia FROM sv_itinerario WHERE svi_id =%s",$svi_id);
		$rstHA= parent::consultar($queryHotelAgencia);
		while ($aux = mysql_fetch_assoc($rstHA)) {
			$hotelAgencia=$aux['svi_hotel_agencia'];
		}		
		
		if($h_total_pesos == ""){
			$h_total_pesos = 0;
		}else if($h_total_pesos != "" && $hotelAgencia != 0 ){
			$h_total_pesos = 0;
		}
		
		return (float)$h_total_pesos;
	}
	
	public function obtenerCotizacionInicialAuto($svi_id){
		$svi_total_pesos_auto=0;
		$autoAgencia = 0;
		
		$queryAutoIicial=sprintf("SELECT svi_total_pesos_auto FROM sv_itinerario svi WHERE svi_id = %s AND  svi_renta_auto_agencia=0",$svi_id);
		$rstAI= parent::consultar($queryAutoIicial);
		while ($aux = mysql_fetch_assoc($rstAI)) {
			$svi_total_pesos_auto=$aux['svi_total_pesos_auto'];
		}
		
		$queryAutoAgencia=sprintf("SELECT svi_renta_auto_agencia FROM sv_itinerario WHERE svi_id =%s",$svi_id);
		$rstAA= parent::consultar($queryAutoAgencia);
		while ($aux = mysql_fetch_assoc($rstAA)) {
			$autoAgencia=$aux['svi_renta_auto_agencia'];
		}
		
		
		if($svi_total_pesos_auto == ""){
			$svi_total_pesos_auto = 0;
		}else if($svi_total_pesos_auto != "" && $autoAgencia != 0){
			$svi_total_pesos_auto = 0;
		}
		
		return (float)$svi_total_pesos_auto;
	}
	
	public function hotelesAgencia($svi_id){
		$hotelAgencia = 0;
		
		$query_hotel=sprintf("SELECT svi_hotel_agencia FROM sv_itinerario WHERE svi_id = %s",$svi_id);
		$rst_hotel= parent::consultar($query_hotel);
		while ($aux = mysql_fetch_assoc($rst_hotel)) {
			$hotelAgencia=$aux['svi_hotel_agencia'];
		}
		return $hotelAgencia;
	}
	
	public function limpiarAutorizaciones($tramiteId){
		$queryLimpia=sprintf("UPDATE tramites set t_autorizaciones='' where t_id='%s'",$tramiteId);
		parent::insertar($queryLimpia);
	}
	public function cecoAsignado($tramiteID){
		$queryCECO="SELECT * FROM solicitud_viaje WHERE sv_tramite=$tramiteID";
		$rst_CECO= parent::consultar($queryCECO);
			while ($aux = mysql_fetch_assoc($rst_CECO)) {
			$arreglo[] = array('sv_ceco_paga' => $aux['sv_ceco_paga'], 'sv_total' => $aux['sv_total']);
			}
		return $arreglo;
	}	

	/**
	 * Funcion para diferenciar una  Sol. de viaje de tipo Terrestre, Aereo y Mezclado ( Aereo/Terrestre)	 *  
	 */
	public function solViajeTransporte($idTramite){
		$Aereo=0;
		$Terrestre=0;		
		$banderaTTransporte=0;
		
		$queryTransporte="SELECT  svi_tipo_transporte FROM sv_itinerario svi
		INNER JOIN solicitud_viaje sv
		ON svi.svi_solicitud=sv.sv_id 
		INNER JOIN tramites tr
		ON sv.sv_tramite = tr.t_id
		WHERE tr.t_id ='{$idTramite}'";
		
		$rstTransporte=parent::consultar($queryTransporte);
		while ($fila = mysql_fetch_assoc($rstTransporte)) {
			if($fila['svi_tipo_transporte'] == 'Aéreo'){
				$Aereo++;
			}elseif($fila['svi_tipo_transporte'] == 'Terrestre'){
				$Terrestre++;
			}
		}
		//error_log("aereo".$Aereo);
		//error_log("terrestre".$Terrestre);
		// $banderaTTransporte = 1 : si hay un transporte Mezclado (Pase a Agencia para la compra de boleto de avion) o si es solamente Aereo.
		// $banderaTTransporte = 0 : si solo el tipo de transporte es Terrestre.	
		if(($Aereo > $Terrestre) && ($Terrestre != 0)){
			$banderaTTransporte = 1;			
		}elseif(($Aereo == $Terrestre)){
			$banderaTTransporte = 1;
		}elseif(($Aereo < $Terrestre) && ($Aereo != 0)){			
			$banderaTTransporte = 1;
		}elseif(($Aereo > $Terrestre) && ($Terrestre == 0)){
			$banderaTTransporte = 1;
		}elseif(($Terrestre > $Aereo) && ($Aereo == 0)){
			$banderaTTransporte = 0;
		}
		
		return $banderaTTransporte;
		
	}
	
	public function CalculoTotalAvion($idsolicitud,$montoVueloNuevo){
		$totalSolicitud = 0;
		$totalSolicitudAvion = 0;
		$totalSolicitudNuevo = 0;
		
		$queryAvionCO="SELECT svi_monto_vuelo_cotizacion FROM sv_itinerario WHERE svi_solicitud={$idsolicitud}";
		
		$rstAvionCO = parent::consultar($queryAvionCO);
			
		while($fila = mysql_fetch_assoc($rstAvionCO)) {
			$costoAvionOriginal = $fila['svi_monto_vuelo_cotizacion'];
		}		
		
		$queryTotalSolicitud=sprintf("SELECT sv_total FROM solicitud_viaje WHERE sv_id = %s",$idsolicitud);
		$rstTotalS=parent::consultar($queryTotalSolicitud);
		while ($fila = mysql_fetch_assoc($rstTotalS)) {
			$totalSolicitud = $fila['sv_total'];
		}

		$montoVueloNuevo = str_replace(',', '',$montoVueloNuevo);
		
		$totalSolicitudAvion = $totalSolicitud - $costoAvionOriginal;
		
		$totalSolicitudNuevo = $totalSolicitudAvion + $montoVueloNuevo;
		
		
		$queryAvionTot=sprintf("UPDATE solicitud_viaje SET sv_total=%s  WHERE sv_id=%s",$totalSolicitudNuevo,$idsolicitud);
		parent::insertar($queryAvionTot);
	}

	public function verificarDetalleRecalculado($idDetalle){
		$encontrado = 0;	
		$query = sprintf("SELECT COUNT(dc_id) AS encontrado FROM detalle_comprobacion WHERE dc_origen_personal = '%s'", $idDetalle);
		$rst = parent::consultar($query);
		while ($aux = mysql_fetch_assoc($rst)) {
			$encontrado = $aux['encontrado'];
		}
		return (int)$encontrado;
	}
	
	public function actualizaDelegado($t_id, $idUsuario){
		$query = sprintf("UPDATE tramites SET t_delegado = '%s' WHERE t_id = '%s'", $idUsuario, $t_id);
		parent::ejecutar($query);
	}
	
	public function truncate ($num, $digits) {
		$shift = pow(10, $digits);
		return ((floor($num * $shift)) / $shift);
	}
	
	// Enviar e-mail informativo de las Interfaces AMEX y SAP
	public function EnviaNotificacionEmailInterfaces($tipo, $mensaje, $nombreArchivo = ""){
		global $SMTP_HOST;
        global $SMTP_PORT;
        global $SMTP_AUTH;
        global $SMTP_USERNAME;
        global $SMTP_PASSWORD;
        global $SMTP_FROM;
        global $SMTP_FROMNAME;
        
        global $SMTP_ACTIVAR_CORREO;
        
        global $SERVER;
        global $RUTA_R;
        
        // Configuracion del servidor de correo
        $mail = new PHPMailer();
        $mail->IsSMTP(); // send via SMTP
        $mail->Host = $SMTP_HOST;
        $mail->Port = $SMTP_PORT;
        $mail->SMTPAuth = $SMTP_AUTH;
        $mail->Username = $SMTP_USERNAME;
        $mail->Password = $SMTP_PASSWORD;
        $mail->From = $SMTP_FROM;
        $mail->FromName = $SMTP_FROMNAME;
        
        $mail->WordWrap = 50;  // set word wrap
        $mail->IsHTML(TRUE);
        
        // Verificar la interfaz puede ser de AMEX ó SAP
        switch($tipo){
        	case COMPROBACIONES :
        		$asunto = "SAP-Comprobaciones";
        		break;
        	case ANTICIPOS :
        		$asunto = "SAP-Anticipos";
        		break;
        	case VUELOS :
        		$asunto = "SAP-Vuelos";
        		break;
        	case AMEX :
        		$asunto = " AMEX";
        		break;
        }
        
        if($SMTP_ACTIVAR_CORREO){
        	if($tipo == AMEX){
        		/* 
        		 * Si el tipo de Interfaz que se recibe es AMEX, solo se enviará notificación por mail al equipo 
        		 * de Soporte con copia oculta al equipo de tencología de Mas Negocio
        		 */ 
        		$mail->AddAddress(EMAIL_SOPORTE_MN, EMAIL_SOPORTE_MN);
        		$mail->AddBCC(EMAIL_TECNOLOGIA_MN, EMAIL_TECNOLOGIA_MN);
        	}else{
        		/*
        		 * Para el caso de Interfaz SAP se requiere enviar un e-mail para la gente de SAP de BMW además de
        		 * copiar al equipo de Soporte y Tecnología de Mas Negocio
        		 */
        		$mail->AddAddress(EMAIL_RESPONSABLE_SAP_BMW, EMAIL_RESPONSABLE_SAP_BMW);
        		$mail->AddBCC(EMAIL_SOPORTE_MN, EMAIL_SOPORTE_MN);
        		$mail->AddBCC(EMAIL_TECNOLOGIA_MN, EMAIL_TECNOLOGIA_MN);
        	}
        }
        
        $cuerpoMensaje = "";
        $cuerpoMensajeAlt = "";
                
        switch($tipo){
			case AMEX:
        		$destinatario_email = EMAIL_SOPORTE_MN;
        		// Extraer nombre del reporte generado
        		$archivoGenerado = substr($asunto, 1, strlen($asunto));
        		$cuerpoMensaje = sprintf("<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
        				Reporte de carga de datos %s.</font><br />
        				<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
        				<ul>
        					<li>Fecha: %s</li>
        					<li>Ejecución: %s</li>
        				</ul>
        				</font><br /><br />
        				", $archivoGenerado, date("d/m/Y h:i:s"), $mensaje);
        		$cuerpoMensajeAlt = sprintf("<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
        				Reporte de carga de datos %s.</font><br />
        				<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
        				<ul>
        					<li>Fecha: %s</li>
        					<li>Ejecución: %s</li>
        				</ul>
        				</font><br /><br />
        				", $archivoGenerado, date("d/m/Y h:i:s"), $mensaje);
        		break;
			default:
				$destinatario_email = EMAIL_RESPONSABLE_SAP_BMW;
				// Extraer nombre del reporte generado
				$archivoGenerado = substr($asunto, 4, strlen($asunto));
				// Para separar el texto que corresponde a la Ejecución, buscamos el primer punto.
				$posicionPunto = strpos($mensaje, ".");
				// Apartir del primer punto, será nuestro mensaje que anotaremos en la línea de Ejecución.
				$mensajeEjecucion = substr($mensaje, 0, ($posicionPunto + 1));
				// El texto después del punto será el registro de nuestra bitácora. 
				$mensajeBitacora = substr($mensaje, ($posicionPunto + 2), strlen($mensaje));
				
				// Si no hay datos, informamos en la bitácora que no hay datos.
				if(strstr($mensaje, "Sin datos.")){
					$mensajeBitacora = substr($mensaje, strlen($mensaje) - 14, strlen($mensaje));
				}else if($tipo == COMPROBACIONES && !(strstr($mensaje, "Sin datos."))){
					$mensajeBitacora = substr($mensaje, 48, strlen($mensaje));
				}
				
				$cuerpoMensaje = sprintf("<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
						Reporte generación de archivo: %s(%s).</font><br />
						<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
        				<ul>
        					<li>Fecha: %s</li>
        					<li>%s</li>
							<li>Bitácora Mas Negocio: %s</li>
        				</ul>
        				</font><br /><br />
						<font style='font-size:10.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
		        		<strong>“Este correo es informativo, favor de no responder a esta dirección, ya que no se encuentra habilitada para recibir mensajes”</strong>
		        		</font><br /><br />
        				", $archivoGenerado, $nombreArchivo, date("d/m/Y h:i:s"), $mensajeEjecucion, $mensajeBitacora);
				$cuerpoMensajeAlt = sprintf("<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
						Reporte generación de archivo: %s(%s).</font><br />
						<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
        				<ul>
        					<li>Fecha: %s</li>
        					<li>%s</li>
							<li>Bitácora Mas Negocio: %s</li>
        				</ul>
        				</font><br /><br />
						<font style='font-size:10.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'>
		        		<strong>“Este correo es informativo, favor de no responder a esta dirección, ya que no se encuentra habilitada para recibir mensajes”</strong>
		        		</font><br /><br />
        				", $archivoGenerado, $nombreArchivo, date("d/m/Y h:i:s"), $mensajeEjecucion, $mensajeBitacora);
				break;
        }
        
        //error_log("destinatario_email = ".$destinatario_email);
        
        $mail->Subject = "Interfaz".$asunto;
        
        $mail->Body = $cuerpoMensaje;
        
        $mail->AltBody = $cuerpoMensajeAlt;
        
        //error_log($mail->Body);
        
        $mail->Send();
	}

	// Funcion para poder persistir el monto original del avion.
	public function valorAvionOrignal($tramite){
		$costoAvionOriginal = 0;
		$svi_solicitud = 0;
	
		$queryAvionCO="SELECT DISTINCT IFNULL(svi_monto_vuelo_cotizacion,'0') AS svi_monto_vuelo_cotizacion,svi_solicitud FROM tramites
		INNER JOIN solicitud_viaje ON sv_tramite = t_id
		INNER JOIN sv_itinerario ON svi_solicitud = sv_id
		WHERE t_id ={$tramite}";
		
		$rstAvionCO = parent::consultar($queryAvionCO);
		while($fila = mysql_fetch_assoc($rstAvionCO)) {
		$costoAvionOriginal = $fila['svi_monto_vuelo_cotizacion'];
		$svi_solicitud = $fila['svi_solicitud'];
		}
	
		$queryInsertaAvionO ="UPDATE sv_itinerario SET svi_monto_vuelo_total = {$costoAvionOriginal} WHERE svi_solicitud = {$svi_solicitud}";
		parent::ejecutar($queryInsertaAvionO);
	}
}

?>
