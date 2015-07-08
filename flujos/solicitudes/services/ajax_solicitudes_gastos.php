<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/functions/Tramite.php");
require_once("$RUTA_A/functions/RutaAutorizacion.php");

function codifica($arg){
	foreach($arg as $key => $val)
		//$arg[$key] = utf8_decode(htmlentities($val));
		$arg[$key] = utf8_encode($val);
	return $arg;
}

// Solicitudes de Gastos
// Obtener tasas
if (isset($_POST['cargaTasas'])) {
	$cnn = new conexion();

	// Obtener tasa USD
	$query=sprintf("SELECT div_tasa AS tasaDollar FROM  divisa WHERE div_nombre = 'USD'");
	$rst = $cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {
		$tasaDollar = $fila['tasaDollar'];
	}

	// Obtener tasa USD
	$query=sprintf("SELECT div_tasa AS tasaEuro FROM  divisa WHERE div_nombre = 'EUR'");
	$rst = $cnn->consultar($query);
	while ($fila = mysql_fetch_assoc($rst)) {
		$tasaEuro = $fila['tasaEuro'];
	}
	
	$_arreglo[] = array('tasaUSD' => $tasaDollar, 'tasaEUR' => $tasaEuro);
	echo json_encode($_arreglo);
}

// Obtener información del previo de la Solicitud de Gastos
if (isset($_POST['noSolicitud'])){
	$cnn = new conexion();
	$tramite = $_POST['noSolicitud'];
	
	$query = sprintf("SELECT sg_motivo, sg_monto, sg_divisa, sg_ceco, sg_ciudad, sg_monto_pesos, 
					%s AS sg_fecha_gasto, sg_lugar, cc_centrocostos, FORMAT(sg_monto_pesos, 2) AS montoPesos, 
					IF(t_etapa_actual = %s, sg_observaciones_edicion, sg_observaciones) AS observaciones, 
					sg_observaciones, sg_observaciones_edicion, sg_requiere_anticipo, sg_concepto, t_etapa_actual 
					FROM solicitud_gastos 
					JOIN tramites ON sg_tramite = t_id 
					JOIN cat_cecos ON sg_ceco = cc_id 
					WHERE sg_tramite = '%s'", "DATE_FORMAT(sg_fecha_gasto,'%d/%m/%Y')", SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR, $tramite);
	//error_log("--->> Carga del gasto: ".$query);
	$rst = $cnn->consultar($query);
	while($fila = mysql_fetch_assoc($rst)){
		$_arreglo[] = array(
			'motivo' => utf8_encode($fila['sg_motivo']),
			'monto' => $fila['sg_monto'],
			'divisa' => $fila['sg_divisa'],
			'ceco' => $fila['cc_centrocostos'],
			'ciudad' => utf8_encode($fila['sg_ciudad']),
			'observaciones' => utf8_encode($fila['sg_observaciones']),
			'observaciones_edicion' => utf8_encode($fila['sg_observaciones_edicion']),
			'fecha_gasto' => $fila['sg_fecha_gasto'],
			'lugar' => utf8_encode($fila['sg_lugar']),
			'etapaActual' => $fila['t_etapa_actual'],
			'requiere_anticipo' => $fila['sg_requiere_anticipo'],
			'concepto' => $fila['sg_concepto'], 
			'montoPesos' => $fila['montoPesos']
		);
	}
	echo json_encode($_arreglo);
}

// Verificar si el usuario es un director
if(isset($_POST['idUsuario'])){
	$cnn = new conexion();
	$idUsuario = $_POST['idUsuario'];
	$esDirector = 0;
	
	$rutaAutorizacion = new RutaAutorizacion();
	$nivel = $rutaAutorizacion->nivelEmpleado($idUsuario);
	
	if($nivel == DIRECTOR_GENERAL){
		$esDirector = 1;
	}
	//error_log("--->>Es director: ".$esDirector." nivel del usuario: ".$nivel);
	echo $esDirector;
}

// Carga de datos del Usuario
if (isset($_POST['idusuario'])) {
	$cnn = new conexion();
	$idUsuario = $_POST['idusuario'];
	$_arreglo = array();
	$i=0;

	$query = sprintf("SELECT DISTINCT nombre, npuesto, e_nombre, IF(u_interno = 1, 'Interno', 'Externo') as tipo
			FROM empleado
			INNER JOIN usuario ON (u_id = idfwk_usuario)
			INNER JOIN empresas ON (u_empresa = e_id)
			WHERE u_id = '%s'", $idUsuario);
	//error_log("--->>Consulta Usario: ".$query);
	$rst2 = $cnn->consultar($query);
	$i=0;
	$j=0;
	while ($filaa = mysql_fetch_assoc($rst2)){
		$data=sprintf("%s:%s:%s:%s", utf8_encode($filaa['nombre']), utf8_encode($filaa['npuesto']), utf8_encode($filaa['e_nombre']), $filaa['tipo']);
		$mayus=array("Á","É","Í","Ó","Ú","á","é","í","ó","ú");
		$mayus2=array("A","E","I","O","U","a","e","i","o","u");
		$data=str_replace($mayus,$mayus2,$data);
		$_arreglo[$i]=$data;
		$i++;
		//error_log("--->>CHECA!!! --->> ".$data);
	}
	echo json_encode($_arreglo);
}

// Carga de invitados
if (isset($_POST['idTramite'])) {
	$cnn = new conexion();
	$t_id = $_POST['idTramite'];
	$_arreglo = array();
	$i=0;
	
	$query = sprintf("SELECT c_nombre_invitado, c_puesto_invitado, c_empresa_invitado, c_tipo_invitado 
		FROM comensales 
		INNER JOIN solicitud_gastos ON (sg_id = c_solicitud) 
		INNER JOIN tramites ON (t_id = sg_tramite) 
		WHERE t_id = '%s'", $t_id);
	//error_log("--->>Consulta Invitados: ".$query);
	$rst2 = $cnn->consultar($query);
	$i=0;
	$j=0;
    while ($filaa = mysql_fetch_assoc($rst2)){
        $data=sprintf("%s:%s:%s:%s",utf8_encode($filaa['c_nombre_invitado']), utf8_encode($filaa['c_puesto_invitado']), utf8_encode($filaa['c_empresa_invitado']), $filaa['c_tipo_invitado']);
		$mayus=array("Á","É","Í","Ó","Ú","á","é","í","ó","ú");
        $mayus2=array("A","E","I","O","U","a","e","i","o","u");
        $data=str_replace($mayus,$mayus2,$data);        
        $_arreglo[$i]=$data;
        $i++;
        //error_log("--->>CHECA!!! --->> ".$data);
    }
	echo json_encode($_arreglo);
}

if(isset($_POST['cargarExcepciones'])){
	$cnn = new conexion();
	$tramiteaConsultar = $_POST['tramite'];
	
	$query = sprintf("SELECT cp_concepto, ex_mensaje, ex_diferencia, 
				(CASE ex_presupuesto
					WHEN 1 THEN 'Excepci&oacute;n de Presupuesto'
					WHEN 0 THEN 'Excepci&oacute;n de Pol&iacute;tica' 
					ELSE ''
				END) AS tipoExcepcion
				FROM excepciones, solicitud_gastos, cat_conceptos
				WHERE ex_solicitud = sg_id
				AND cp_id = ex_concepto
				AND sg_tramite = '%s' ", $tramiteaConsultar);
	//error_log("--->>Consulta Excepciones: ".$query);
	$res = $cnn->consultar($query);
	while($row = codifica(mysql_fetch_assoc($res))){
		$cont++;
		$response->rows[$cont] = array("concepto" 	=> $row["cp_concepto"],
										"mensaje"	=> $row["ex_mensaje"],
										"excedente" => $row["ex_diferencia"],
										"tipoExcepcion" => $row["tipoExcepcion"]);
	}		
	echo json_encode($response);
}

if(isset($_POST['excepcionesPresupuesto'])){
	$cnn = new conexion();
	$tramiteaConsultar = $_POST['tramite'];
	
	$query = sprintf("SELECT ex_mensaje, FORMAT(ex_diferencia,2) AS excedente, IF(ex_presupuesto = 1, 'Excepci&oacute;n de Presupuesto', '') AS tipoExcepcion
				FROM excepciones 
				JOIN solicitud_gastos ON sg_id = ex_solicitud
				LEFT JOIN tramites ON sg_tramite = t_id
				WHERE t_id = %s
				AND ex_presupuesto = 1", $tramiteaConsultar);
	//error_log("--->>Consulta Excepciones Presupuesto: ".$query);
	$res = $cnn->consultar($query);
	while($row = codifica(mysql_fetch_assoc($res))){
		$response = array("concepto" 	=> $row["cp_concepto"],
										"mensaje"	=> $row["ex_mensaje"],
										"excedente" => $row["excedente"],
										"tipoExcepcion" => $row["tipoExcepcion"]);
	}		
	echo json_encode($response);
}

/**
 * Obtiene el nombre de la empresa del usuario
 *
 * @param usuario int POST 		=> Id del usuario en sesion
 * @return json					=> Devuelve la Empresa del empleado
 */

if(isset($_POST["empresaUsuario"])){
	$conexion = new conexion();
	$usuario = $_POST['usuario'];
	$sql = "SELECT
		e_nombre
		FROM empresas
		INNER JOIN usuario ON (e_id = u_empresa)
		WHERE u_id = $usuario";
	//error_log("--->> ".$sql);
	$res = $conexion->consultar($sql);
	echo json_encode(codifica(mysql_fetch_assoc($res)));
}
?>