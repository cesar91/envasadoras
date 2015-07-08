<?php
	/**
	 * Registro & Edicion de Comprobaciones de Viaje => Servicios 
	 * Creado por: IHV 2013-06-13
	 */	 	 
	require_once("../../../lib/php/constantes.php");
	require_once("$RUTA_A/Connections/fwk_db.php");
	require_once("$RUTA_A/functions/utils.php");
	
	$conexion = new conexion();
	$cont = 0;
	
	function codifica($arg){
		foreach($arg as $key => $val)
			//$arg[$key] = utf8_decode(htmlentities($val));
			$arg[$key] = utf8_encode($val);
		return $arg;
	}
	
	//================================================================================================================	
	/**
	 * Resetea los cargos de un usuario
	 *
	 * @param inicializaAmex string POST	=> Peticion a la que atiende
	 * @param usuario string				=> id del usuario del que se resetearan los cargos
	 */
	if (isset($_POST['inicializaAmex'])) {
		$usuario = $_POST['usuario'];
		$sql = "UPDATE amex a
				LEFT JOIN empleado e1 ON tarjeta = e1.notarjetacredito
				LEFT JOIN empleado e2 ON tarjeta = e2.notarjetacredito_gas
				LEFT JOIN usuario u1 ON u1.u_id = e1.idfwk_usuario
				LEFT JOIN usuario u2 ON u2.u_id = e2.idfwk_usuario
				SET a.estatus = 0				
				WHERE a.estatus = 3 AND u1.u_id = '$usuario'
				OR a.estatus = 3 AND u2.u_id = '$usuario'";
		$res = $conexion->consultar($sql);
		echo json_encode(array(true));
	}
	
	//================================================================================================================	
	/**
	 * Obtiene las solicitudes pendientes de comprobar 
	 *
	 * @param consultaSolicitudes string POST	=> Peticion a la que atiende
	 * @param idusuario string					=> id del usuario del que se buscan las  solicitudes
	 * @param tramite string					=> id del tramite cuando sea una edicion 
	 * @return json								=> las solicitudes a comprobar en formato json
	 */
	if(isset($_REQUEST["consultaSolicitudes"])){
		$idUsuario = $_REQUEST["idusuario"];
		$idTramite = (isset($_REQUEST["tramite"])) ?  $_REQUEST["tramite"] : "";		
		
		$sql = "SELECT 
					g.sg_id, 
					g.sg_motivo,
					g.sg_observaciones, 
					g.sg_tramite, 
					g.sg_fecha_registro, 
					t.t_iniciador, 
					t.t_etiqueta,
					co_id,
					(SELECT co_gasolina 
						FROM comprobacion_gastos 
						WHERE co_mi_tramite = co_tramite 
						LIMIT 1) AS co_gasolina, 
					t_comprobado, 
					t_etapa_actual
				FROM solicitud_gastos AS g 
				JOIN tramites AS t ON (g.sg_tramite = t.t_id) 
				LEFT JOIN comprobacion_gastos ON co_tramite = sg_tramite
				WHERE t_comprobado = 0 
				AND t.t_iniciador = $idUsuario ";
		
		if($idTramite != ""){
			$sql.= "AND co_mi_tramite = $idTramite";			
		}else{
			$sql.= "AND (t.t_etapa_actual = ".COMPROBACION_GASTOS_ETAPA_APROBADA.") 
				AND co_id is NULL";			
		}							
		$res = $conexion->consultar($sql);		
		$tot = mysql_num_rows($res);
		
		if(!empty($tot)){
			while($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array("sv_tramite" => $row["sg_tramite"],
									"sv_motivo" => $row["sg_motivo"]);			
			}
		}else{
			$sql = "SELECT co_motivo_gasolina, DATE_FORMAT(co_fecha_inicial_gasolina, '%d/%m/%Y') as co_fecha_inicial_gasolina, DATE_FORMAT(co_fecha_final_gasolina, '%d/%m/%Y') as co_fecha_final_gasolina, co_cc_clave
						FROM comprobacion_gastos
						JOIN tramites on t_id = co_mi_tramite 
						WHERE co_gasolina = 1
						AND t_comprobado = 0
						AND co_mi_tramite = '$idTramite'
						AND t_iniciador= '$idUsuario'";	
			$res = $conexion->consultar($sql);			
			while($row = codifica(mysql_fetch_array($res))){
				$cont++;
				$response->rows[$cont] = array("co_tramite" => $idTramite,
												"motivoGasolina" => $row["co_motivo_gasolina"],
												"fechaInicial" => $row["co_fecha_inicial_gasolina"],
												"fechaFinal" => $row["co_fecha_final_gasolina"],
												"ceco" => $row["co_cc_clave"]
												);							
			}
		
		}
		
		echo json_encode($response);
	}
	
	/**
	 * Obtiene los tipos de comprobacion de pendiendo del perfil y/o los anticipos 
	 *
	 * @param tiposComprobacion string POST	=> Peticion a la que atiende
	 * @param anticipo int					=> El monto del anticipo de la solicitud a comprobar
	 * @param perfil string					=> El perfil del usuario, (Perfil 3 es un Usuario Externo) 
	 * @param solicitud string				=> El tipo de solicitud a comprobar
	 * @return json							=> Los tipos de comprobacion en formato json
	 */
	if(isset($_REQUEST["tiposComprobacion"])){
		$anticipo = $_REQUEST["anticipo"];
		$perfil = $_REQUEST["perfil"];
		$solicitud = $_REQUEST["solicitud"];
		
		if($perfil != 3 && ($solicitud == "-1"  || $anticipo == 0)  )
			$tiposComprobacion = array(1 => "Comprobacion de AMEX", 2 => " Comprobacion de Reembolso");
		elseif($solicitud > 0 && $perfil != 3 && $anticipo > 0)
			$tiposComprobacion = array(1 => "Comprobacion de Anticipo", 2 => "Comprobacion de AMEX", 3 => "Comprobacion de Reembolso");
		else
			$tiposComprobacion = array(1 => "Comprobacion de Reembolso", 2 => "Comprobacion de AMEX Externo");
		
		for($cont = 1; $cont <= count($tiposComprobacion); $cont++)
			$response->rows[$cont] = array("tipo" => $tiposComprobacion[$cont]);
		
		echo json_encode($response);
	}
	
	/**
	 * Obtiene los CECOS activos de la empresa del usuario
	 *
	 * @param centrosCostos string POST	=> Peticion a la que atiende
	 * @param idusuario int				=> El id del usuario
	 * @return json						=> Los CECOs activos en formato json
	 */
	if(isset($_REQUEST["centrosCostos"])){
		$idUsuario = $_REQUEST["idusuario"];
		$sql = "SELECT c.cc_id, c.cc_centrocostos, c.cc_nombre from cat_cecos c 
							INNER JOIN empleado e ON c.cc_id = e.idcentrocosto 
							WHERE c.cc_empresa_id = (SELECT cc_empresa_id 
										FROM empleado
										JOIN cat_cecos ON idcentrocosto = cc_id
										WHERE idfwk_usuario = $idUsuario)
							AND c.cc_estatus = 1 
							AND e.idempleado = " . $idUsuario . " 
							order by c.cc_centrocostos";
		$res = $conexion->consultar($sql);
		while($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array("cc_id" => $row["cc_id"],
											"cc_centrocostos" => $row["cc_centrocostos"],
											"cc_nombre" => $row["cc_nombre"]);
		}
		echo json_encode($response);
	}
	
	/**
	 * Obtiene el CECO activo del usuario
	 *
	 * @param centrosCostosUsuario string POST	=> Peticion a la que atiende
	 * @param idusuario int				=> El id del usuario
	 * @return json						=> Los CECOs activos en formato json
	 */
	if(isset($_REQUEST["centrosCostosUsuario"])){
		$idUsuario = $_REQUEST["idusuario"];
		$sql = "SELECT cc_id, cc_centrocostos, cc_nombre
				FROM cat_cecos
				INNER JOIN empleado ON (idcentrocosto = cc_id)
				WHERE idfwk_usuario = $idUsuario 
				AND cc_estatus = 1";
			$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	
	/**
	 * Obtiene los Conceptos de Gastos activos dependiendo del tipo de comprobacion y la solicitud
	 *
	 * @param conceptosGastos string POST	=> Peticion a la que atiende
	 * @param solicitud int					=> El id de la solicitud a comprobar
	 * @param tipoComprobacion int			=> El tipo de comprobacion que se esta seleccionando
	 * @return json							=> Los Conceptos de Gastos activos en formato json
	 */
	if(isset($_REQUEST["conceptosGastos"])){
		$solicitud = $_REQUEST["solicitud"];
		$tipoComprobacion = $_REQUEST["tipoComprobacion"];
		
		$sql = "SELECT cc.cp_id, cp_concepto
				FROM cat_conceptos  cc
				JOIN conceptos_flujos  cf ON cc.cp_id = cf.cp_id
				WHERE cp_activo = 1";
		if($tipoComprobacion == "Comprobacion de AMEX")	{
			$sql .= " AND f_id = 5";
		}else{
			$sql .= " AND f_id = ".FLUJO_COMPROBACION_GASTOS;
		}
		/*if($solicitud == "-1"){
			$sql.= " AND (
						cp_concepto = 'Gasolina'
						OR cp_concepto = 'Casetas'
						OR cp_concepto = 'Personal'
						OR cp_concepto = 'Otros gastos'
					)";
		}else*/
		if($tipoComprobacion == "Reembolso")
			$sql.= " AND cp_concepto != 'Personal'";
		
		$sql .= " ORDER BY cp_id";
		$res = $conexion->consultar($sql);
		
		while($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array("cp_id" => $row["cp_id"],
											"cp_concepto" => $row["cp_concepto"]);
		}
		echo json_encode($response);
	}
	
	/**
	 * Obtiene las Divisas del catalogo
	 *
	 * @param divisas string POST	=> Peticion a la que atiende	 
	 * @return json					=> Las Divisas en formato json
	 */
	if(isset($_REQUEST["divisas"])){
		$cont = 0;
		$sql = "SELECT div_id, div_nombre, div_tasa
				FROM divisa";
		$res = $conexion->consultar($sql);
		while($row = mysql_fetch_assoc($res)){
			$cont++;
			$response->rows[$cont] = array("div_id" => $row["div_id"],
											"div_tasa" => $row["div_tasa"],
											"div_nombre" => $row["div_nombre"]);
		}
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene el ceco de la solicitud, y el anticipo de esta
	 *
	 * @param anticipoCeco string POST	=> Peticion a la que atiende	 
	 * @param tramite string 			=> el tramite del cual requerimos la informacion 
	 * @return json						=> En una cadena json el ceco y el anticipo de la solicitud
	 */
	if(isset($_REQUEST["anticipoCeco"])){
		$idTramite = $_REQUEST["tramite"];
		$tramiteEdicion = $_REQUEST["tramiteEdit"];
		$sql = "SELECT IF(sg_requiere_anticipo = 1, sg_monto_pesos, 0) AS sv_total_anticipo, sg_ceco AS sv_ceco_paga
				FROM solicitud_gastos
				WHERE sg_tramite =  $idTramite";
		$res = $conexion->consultar($sql);
		$response = mysql_fetch_assoc($res);
		
		if(empty($response)){
			$sql = "SELECT co_anticipo_gasto AS sv_total_anticipo , co_cc_clave AS sv_ceco_paga
					FROM comprobacion_gastos
					WHERE co_mi_tramite =$tramiteEdicion";
			$res = $conexion->consultar($sql);
			$response = mysql_fetch_assoc($res);
		}
		echo json_encode($response);
	}
	
	/**
	 * Obtiene el la region, el tipo de viaje y los dias de viaje de una solicitud
	 *
	 * @param datosSolicitud string POST	=> Peticion a la que atiende	 
	 * @param tramite string 				=> el tramite del cual requerimos la informacion 
	 * @return json							=> En una cadena json la region, el tipo de viaje y los dias de viaje
	 */
	if(isset($_POST['datosSolicitud'])){
		$idTramite = $_POST['tramite'];
		$sql = "SELECT sv_viaje 
				FROM solicitud_viaje 
				WHERE sv_tramite = $idTramite";
		$res = $conexion->consultar($sql);
		$row = mysql_fetch_assoc($res);
		$svViaje = $row['sv_viaje'];
		
		$parametro = new Parametro();
		if($svViaje == 'Sencillo' || $svViaje == 'Redondo')
			$datosViaje = $parametro->datosViajeSencilloRedondo($idTramite);
		else
			$datosViaje = $parametro->datosViajeMultidestino($idTramite);
		echo json_encode($datosViaje);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene el la region, el tipo de viaje y los dias de viaje de una solicitud
	 *
	 * @param tipoTarjeta string POST	=> Peticion a la que atiende	 	 
	 * @return json						=> Los Tipos de Tarjeta en formato json
	 */
	if(isset($_REQUEST["tipoTarjeta"])){
		$tipoTarjeta = array(1 => "AMEX corporativa Gastos");
		for($cont = 1; $cont <= count($tipoTarjeta); $cont++){
			$response->rows[$cont] = array("tipo" => $tipoTarjeta[$cont]);
		}
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene los cargos segun el usuario y la tarjeta 
	 *
	 * @param cargosTarjeta string POST	=> Peticion a la que atiende	 	 
	 * @param tipoTarjeta string 		=> El tipo de tarjeta del cual se quieren obtener los cargos
	 * @param idUsuario string 			=> El usuario dueÃ±o de la tarjeta del cual se quieren obtener los cargos
	 * @return json						=> Los cargos de la Tarjeta en formato json
	 */
	if(isset($_REQUEST["cargosTarjeta"])){
		$tipoTarjeta = $_REQUEST["tipoTarjetaAmex"];
		$idUsuario = $_REQUEST["idusuario"];
		$tramite = $_REQUEST["tramite"];
		
		if($tipoTarjeta == "AMEX corporativa Gastos"){
			$sql = "SELECT DISTINCT tarjeta, idamex, DATE(fecha_cargo) AS fecha , concepto, monto, moneda_local
					FROM amex 
					JOIN empleado e ON tarjeta = notarjetacredito
					WHERE idfwk_usuario = $idUsuario
					AND e.estatus = 1
					AND amex.estatus = 0
					AND comprobacion_id = 0 ";
		}elseif($tipoTarjeta == "AMEX corporativa Gasolina"){
			$sql = "SELECT DISTINCT tarjeta, idamex, DATE(fecha_cargo) AS fecha , concepto, monto, moneda_local
					FROM amex 
					JOIN empleado e ON tarjeta = notarjetacredito_gas
					WHERE idfwk_usuario = $idUsuario
					AND e.estatus = 1
					AND amex.estatus = 0
					AND comprobacion_id = 0 ";
		}
		
		if($tramite != "")
			$sql.= "OR comprobacion_id = (SELECT co_id FROM comprobacion_gastos WHERE co_mi_tramite = $tramite)";
		$res = $conexion->consultar($sql);
		while($row = mysql_fetch_assoc($res)){
			$cont++;
			$response->rows[$cont] = array("tarjeta" => $row["tarjeta"],
											"idamex" => $row["idamex"],
											"fecha" => $row["fecha"],
											"concepto" => $row["concepto"],
											"monto" => $row["monto"],
											"moneda_local" => $row["moneda_local"]);
		}
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene el detalle del cargo seleccionado
	 *
	 * @param datelleCargo string POST	=> Peticion a la que atiende	 	 
	 * @param cargo string 				=> El cargo del cual se requiere el detalle
	 * @return json						=> El detalle del cargo de la Tarjeta en formato json
	 */
	if(isset($_REQUEST["datelleCargo"])){
		$cargo = $_REQUEST["cargo"];		
		$sql = "SELECT concepto, DATE(fecha_cargo) AS fecha_cargo, rfc_establecimiento, monto, moneda_local, montoAmex, monedaAmex, notransaccion, div_tasa*montoAmex AS conversion_pesos 
				FROM amex 
				JOIN divisa ON div_nombre = monedaAmex
				WHERE idamex = $cargo";		
		$res = $conexion->consultar($sql);
		$response = mysql_fetch_assoc($res);
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene los tipos de comida
	 *
	 * @param tipoComida string POST	=> Peticion a la que atiende	 	 
	 * @return json						=> Los tipos de comida en formato json
	 */
	if(isset($_REQUEST["tipoComida"])){
		$tiposComida = array("1" => "Desayuno", 2 =>"Comida", 3 =>"Cena");
		for($cont = 1; $cont <= count($tiposComida); $cont++){
			$response->rows[$cont] = array("tipo" => $tiposComida[$cont]);
		}
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Verifica si un proveedor existe en la BD
	 *
	 * @param validaProveedor string POST	=> Peticion a la que atiende	 	 
	 * @param proveedor string				=> Razon socia del proveedor a buscar	 	 
	 * @param rfc string 					=> RFC del proveedor a buscar
	 * @return json							=> La cantidad de proveedores encontrados
	 */	
	if(isset($_POST["validaProveedor"])){
		$proveedor = mysql_real_escape_string($_POST['proveedor']);
		$rfc = mysql_real_escape_string($_POST['rfc']);
		$sql = "SELECT COUNT(pro_id) AS result
				FROM proveedores 
				WHERE pro_rfc = '$rfc' 
				AND pro_proveedor = '$proveedor'";
		$res = $conexion->consultar($sql);
		$row = codifica(mysql_fetch_assoc($res));
		if($row["result"] == 0){
			$insert = "INSERT INTO proveedores VALUES (default, '$proveedor', '$rfc', null, '$proveedor', null, null ,1);";
			$insert1 = $conexion->consultar($insert);
			if($insert1){
				$res1 = $conexion->consultar($sql);
				$row = codifica(mysql_fetch_assoc($res1));
			}
		}
		echo json_encode(array($row["result"]));
	}
	
	/**
	 * Actualiza el estado de un cargo amex
	 *
	 * @param actualizaIdamex string POST	=> Peticion a la que atiende	 	 
	 * @param cargoTarjeta string			=> Cargo de la tarjeta que se actualizara
	 * @param estado string 				=> Estatus que se asignara
	 * @return json							=> Devuelve true
	 */	
	if(isset($_POST['actualizaIdamex'])){
		$idamex = $_POST['cargoTarjeta'];
		$estado = $_POST['estado'];
		$sql = "UPDATE amex 
				SET estatus ='$estado',
					comprobacion_id = '0' 
				WHERE idamex = '$idamex'";
		$res = $conexion->consultar($sql);
		echo json_encode(array(true));
	}
	
	/**
	 * Verifica el valor de un monto amex contra un valor enviado de la suma de las aprtidas de un cargo
	 *
	 * @param validaCargoAmex string POST	=> Peticion a la que atiende	 	 
	 * @param sumaCargo string				=> La suma de las partidas de un cargo
	 * @param cargoTarjeta string 			=> El cargo de la tarjeta contra el cual se va acomparar
	 * @return json							=> Devuelve true si son iguales, de lo contrario los ultimos 8 numeros del notransaccion 
	 */	
	if(isset($_POST['validaCargoAmex'])){
			$sumaCargo = $_POST["sumaCargo"];
			$cargoTarjeta = $_POST["cargoTarjeta"];
			$sql = "SELECT 
					IF(
						CONVERT(montoAmex * (SELECT div_tasa 
										 FROM divisa 
										 WHERE div_nombre = monedaAmex),
						DECIMAL(10,2)) = $sumaCargo 
						,TRUE
						,SUBSTRING(notransaccion,29,35)
					) AS result					
					FROM amex 
					WHERE idamex = '$cargoTarjeta'";
			$res = $conexion->consultar($sql);
			$row = mysql_fetch_assoc($res);
			echo json_encode($row);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene los tipos de Auto
	 *
	 * @param int tipoAuto POST 	 => Peticion a la que atiende	 
	 * @return json					 => Los tipos de auto en formato json
	 */
	if(isset($_REQUEST["tipoAuto"])){
		$tiposAuto = array(1 => "Pool/Rentado",2 => "Particular",3 => "Asignado");
		for($cont = 1; $cont <= count($tiposAuto); $cont++){
			$response->rows[$cont] = array("tipo" => $tiposAuto[$cont]);
		}
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene los Modelos de auto
	 *
	 * @param int modeloAuto POST 	 => Peticion a la que atiende
	 * @param string tipoAutos  	 => El tipo de auto seleccionado
	 * @return json					 => Los modelos de auto en formato json
	 */
	if(isset($_REQUEST["modeloAuto"])){
		$tipoAuto = $_REQUEST["tipoAutos"];
		$sql = "SELECT ma_id, ma_nombre, ma_factor
				FROM modelo_auto
				WHERE ma_estatus = 1 ";				
		if($tipoAuto == "Particular")
			$sql.= " AND ma_nombre != 'Auto particular'";
		$res = $conexion->consultar($sql);
		while($row = mysql_fetch_assoc($res)){
			$cont++;
			$response->rows[$cont] = array("ma_id" => $row["ma_id"],
											"ma_factor" => $row["ma_factor"],
											"ma_nombre" => $row["ma_nombre"]);						
		}
		echo json_encode($response);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene la informacion de las partidas de una compribacion
	 *
	 * @param int tramiteEdicion POST 	 => El id del tramite del cual se obtendran las partidas; Peticion a la que atiende
	 * @return json						 => Las partidas en formato json
	 */
	if(isset($_REQUEST["tramiteEdicion"])){
		$tramiteEdicion = $_REQUEST["tramiteEdicion"];
		$sql = "SELECT 
					dc.dc_id, 
					dc_tipo, 
					(SELECT dc_notransaccion FROM amex WHERE idamex = dc_idamex_comprobado) AS notransaccion,
					IFNULL((SELECT(IF(notarjetacredito = tarjeta, 'AMEX corporativa Gastos', IF(notarjetacredito_gas = tarjeta, 'AMEX corporativa Gasolina','')))		
							FROM empleado, amex
							WHERE (tarjeta = notarjetacredito 
								OR tarjeta = notarjetacredito_gas)
							AND idamex = dc_idamex_comprobado LIMIT 1), false) AS 'tipoTarjeta', 
					dc_idamex_comprobado,
					cp_concepto AS cp_concepto,
					ccb.cp_id AS concepto_id,		
					dc_tipo_comida,										
					DATE_FORMAT(dc_factura,'%d/%m/%Y') AS dc_factura,					
					(SELECT pro_proveedor FROM proveedores WHERE dc_proveedor = pro_id) AS dc_proveedor, 
					IF(dc_rfc IS NOT NULL AND dc_rfc != '', true, false) AS proveedorNacional,
					dc_rfc,
					dc_folio_factura,
					FORMAT(dc_monto,2) AS dc_monto,
					dc_divisa,
					(SELECT div_nombre FROM divisa WHERE dc_divisa = div_id) AS div_nombre,
					FORMAT(dc_iva,2) AS dc_iva,
					FORMAT(dc_total,2) AS dc_total,				
					FORMAT(dc_total,2) AS dc_total_partida,
					dc_comentarios,
					FORMAT(dc_propinas,2) AS dc_propinas,
					dc_comensales,					
					FORMAT(dc_imp_hospedaje,2) AS dc_imp_hospedaje,
					concepto,
					ex_mensaje,
					IF (dc_origen_personal= 0,0,1) AS dc_origen_personal,
					dc.dc_id AS dc_id,
					IF(factura.id_factura > 0,(CONCAT('<p align=center><a href=muestraXML.php?xml=\"',factura.urlMD5,'.xml\" target=\"_blank\">VER</a></p>')),'NO') AS factura,
					IF(factura.id_factura > 0, factura.id_factura, 0) AS fid
				FROM detalle_comprobacion_gastos AS dc
				JOIN comprobacion_gastos ON co_id = dc_comprobacion
				JOIN tramites ON t_id = co_mi_tramite
				JOIN cat_conceptos AS ccb ON dc_concepto = cp_id
				JOIN divisa ON  div_id = dc_divisa  
				LEFT JOIN amex ON idamex = dc.dc_idamex_comprobado
				LEFT JOIN excepciones ON dc.dc_id = ex_comprobacion_detalle
				LEFT JOIN factura on dc.id_factura = factura.id_factura
				WHERE t_id = $tramiteEdicion
				ORDER BY dc.dc_id";
		$res = $conexion->consultar($sql);
		while ($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array('tipoComprobacion'	=> $row['dc_tipo'],
											'noTransaccionTexto'=> $row['notransaccion'],
											'tipoTarjeta'		=> $row['tipoTarjeta'],
											'cargoTarjeta'		=> $row['dc_idamex_comprobado'],
											'conceptoAmex'		=> $row['concepto'],
											'conceptoTexto'		=> $row['cp_concepto'],
											'concepto'			=> $row['concepto_id'],
											'tipoComida'		=> $row['dc_tipo_comida'],	
											'fecha'				=> $row['dc_factura'],
											'proveedor'			=> $row['dc_proveedor'],
											'rfc'				=> $row['dc_rfc'],
											'folio'				=> $row['dc_folio_factura'],
											'comentario'		=> $row['dc_comentarios'],
											'asistentes'		=> $row['dc_comensales'],
											'monto'				=> $row['dc_monto'],
											'proveedorNacional'	=> $row['proveedorNacional'],
											'divisa'			=> $row['dc_divisa'],
											'divisaTexto'		=> $row['div_nombre'],											
											'iva'				=> $row['dc_iva'],
											'total'				=> $row['dc_total'],																																															
											'totalPartida'		=> $row['dc_total_partida'],	
											'excepcion'			=> $row["ex_mensaje"],
											'propina'			=> $row['dc_propinas'],											
											'impuestoHospedaje'	=> $row['dc_imp_hospedaje'],
											'origenPersonal'	=> $row['dc_origen_personal'],
											'dc_id'				=> $row['dc_id'],
											'factura'			=> $row['factura'],
											'fid'				=> $row['fid']												
										);
		}
		echo json_encode($response);
	}
	
	
	//================================================================================================================	
	//================================================================================================================	
	//================================================================================================================	
	
	/**
	 * Obtiene la informacion de las partidas de una comprobacion
	 *
	 * @param int tramiteEdicion POST 	 => El id del tramite del cual se obtendran las partidas; Peticion a la que atiende
	 * @return json						 => Las partidas en formato json
	 */
	 
	if(isset($_REQUEST["comprobacionInformacion"])){
		$tramite = $_REQUEST["tramite"];
		$sql = "SELECT 
					t_id, 
					t_dueno,		
					t_etapa_actual,
					co_cc_clave, 					
					IF(t_delegado = 0, nombre, CONCAT((SELECT nombre FROM empleado WHERE t_delegado = idfwk_usuario),' en nombre de ',nombre)) AS nombre,
					IF(co_gasolina = 1 ,co_motivo_gasolina, t_etiqueta) AS t_etiqueta, 
					DATE_FORMAT(t_fecha_registro,'%d/%m/%Y') AS t_fecha_registro,
					CONCAT(cc_centrocostos,' - ', cc_nombre) AS ceco,
					et_etapa_nombre, 
					#IF(co_gasolina != 1 ,CONCAT((SELECT svi_origen FROM sv_itinerario WHERE svi_solicitud = sv_id ORDER BY svi_fecha_salida ASC LIMIT 1), ' - ', 					
					#						(SELECT svi_destino FROM sv_itinerario WHERE svi_solicitud = sv_id ORDER BY svi_fecha_salida ASC LIMIT 1)
					#						), co_ruta) AS destino,
					/*IF(co_gasolina = 1 , CONCAT(DATE_FORMAT(co_fecha_inicial_gasolina,'%d/%m/%Y'),' - ',DATE_FORMAT(co_fecha_final_gasolina,'%d/%m/%Y')),
										DATE_FORMAT(sg_fecha_registro,'%d/%m/%Y')) */
					IF(co_gasolina = 1 , CONCAT((SELECT DATE_FORMAT(MIN(dc_factura),'%d/%m/%Y') FROM detalle_comprobacion_gastos WHERE dc_comprobacion = co_id),' - ',(SELECT DATE_FORMAT(MAX(dc_factura),'%d/%m/%Y') FROM detalle_comprobacion_gastos WHERE dc_comprobacion = co_id)),
										DATE_FORMAT(sg_fecha_registro,'%d/%m/%Y')) AS fechaViaje				
				FROM tramites
				JOIN empleado ON t_iniciador = idfwk_usuario
				JOIN comprobacion_gastos ON co_mi_tramite = t_id				
				LEFT JOIN cat_cecos ON co_cc_clave = cc_id				
				JOIN etapas ON et_etapa_id = t_etapa_actual AND t_flujo = et_flujo_id	
				LEFT JOIN solicitud_gastos ON co_tramite = sg_tramite	
				#JOIN usuario ON t_iniciador = u_id 								
				#LEFT JOIN solicitud_viaje ON comprobaciones.co_tramite=solicitud_viaje.sv_tramite
				#LEFT JOIN sv_itinerario ON sv_itinerario.svi_solicitud=solicitud_viaje.sv_id				
				WHERE t_id =  $tramite";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	
	if(isset($_REQUEST["nombreAutorizadores"])){
		$tramite = $_REQUEST["tramite"];
		$rutaAutorizacion = new RutaAutorizacion();
		$autorizadores = $rutaAutorizacion->getNombreAutorizadores($tramite);
		echo json_encode(array("autorizadores" => $autorizadores));
	}
	
	
	if(isset($_REQUEST["historialObservaciones"])){
		$tramite = $_REQUEST["tramite"];
		$sql = "SELECT co_observaciones FROM comprobacion_gastos WHERE co_mi_tramite = $tramite";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	
	//================================================================================================================	
	//================================================================================================================	
	//================================================================================================================	
	
	if(isset($_REQUEST["comprobacionGasolina"])){
		$tramite = $_REQUEST["tramite"];
		$sql = "SELECT ma_nombre, ma_factor, co_kilometraje, co_monto_gasolina, co_ruta, co_gasolina, 
				(SELECT IFNULL(SUM(dc_total), 0)
						FROM detalle_comprobacion_gastos
						WHERE dc_comprobacion = co_id                                                
						AND dc_concepto = 12
						AND (
								(SELECT idamex 
								FROM amex 
								WHERE idamex = dc_idamex_comprobado 
								AND moneda_local = 'MXN' 
								LIMIT 1) = dc_idamex_comprobado
							OR 
								(dc_idamex_comprobado = 0  
								AND dc_divisa = 1)
							)							
						) AS totalGasolina
				FROM comprobacion_gastos
				JOIN modelo_auto ON co_modelo_auto = ma_id
				JOIN tramites ON co_mi_tramite = t_id
				LEFT JOIN excepciones ON ex_comprobacion = co_id
				WHERE t_id = $tramite";
		$res = $conexion->consultar($sql);
		echo json_encode(mysql_fetch_assoc($res));
	}
	
	if(isset($_POST['guardarTasas'])){
		$tasaDolar = $_POST['tasaUSD'];
		$tasaEuro = $_POST['tasaEur'];
		$tramiteComp = $_POST['tramite'];
		$sql = "UPDATE comprobacion_gastos
				SET co_tasa_USD = '$tasaDolar', 
					co_tasa_EUR = '$tasaEuro' 
				WHERE co_mi_tramite = '$tramiteComp'";
		$conexion->ejecutar($sql);
		echo json_encode(array(true));
	}
	
	if(isset($_POST['comparaDetalles'])){
		$dc_id = $_POST['dc_id'];
		$sql = "SELECT * FROM detalle_comprobacion_gastos WHERE dc_id = $dc_id";
		$res = $conexion->consultar($sql);
		echo json_encode(mysql_fetch_assoc($res));
	}
	
	if(isset($_POST['presupuesto'])){
		$ceco = $_POST["ceco"];
		$sql = "SELECT SUM(pp_presupuesto_disponible) AS 'presupuesto' 
				FROM periodo_presupuestal 
				WHERE pp_ceco= '$ceco'";
		$res = $conexion->consultar($res);
		echo json_encode(mysql_fetch_assoc($res));
	}
	
	/**
	 * Carga los invitados de una Solicitud
	 * 
	 * @param cargaInvitados int POST 	=> Id del tramite asociado(Solicitud)
	 *  @return json 					=> Invitados de la Solicitud
	 */
	if(isset($_POST["cargaInvitados"])){
		$tramite = $_POST["tramiteSolicitud"];
		$sql = "SELECT c_nombre_invitado, 
				c_puesto_invitado, 
				c_empresa_invitado, 
				c_tipo_invitado
				FROM comensales
				INNER JOIN solicitud_gastos ON (sg_id = c_solicitud)
				WHERE sg_tramite = $tramite 
				ORDER BY id_comensal";
		$res = $conexion->consultar($sql);
		while ($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array('nombreInvitado'		=> $row['c_nombre_invitado'],
											'puestoInvitado'	=> $row['c_puesto_invitado'],
											'empresaInvitado'	=> $row['c_empresa_invitado'],
											'tipoInvitado'		=> $row['c_tipo_invitado']);
		}
		echo json_encode($response);
	}
	
	/**
	 * Carga los invitados de una comprobacion
	 * 
	 * @param tramiteEdicioncargaInvitados int POST => Id del tramite asociado a la Comprobacion
	 *  @return json								=> Invitados de la comprobacion 
	 */
	if(isset($_POST["tramiteEdicioncargaInvitados"])){
		$tramite = $_POST["tramiteComprobacion"];
		$sql = "SELECT c_nombre_invitado, 
				c_puesto_invitado, 
				c_empresa_invitado, 
				c_tipo_invitado
				FROM comensales
				INNER JOIN comprobacion_gastos ON (co_id = c_comprobacion)
				INNER JOIN tramites ON (t_id = co_mi_tramite)
				WHERE t_id = $tramite ORDER BY id_comensal";
		$res = $conexion->consultar($sql);
		while ($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array('nombreInvitado'		=> $row['c_nombre_invitado'],
											'puestoInvitado'	=> $row['c_puesto_invitado'],
											'empresaInvitado'	=> $row['c_empresa_invitado'],
											'tipoInvitado'		=> $row['c_tipo_invitado']
					
										);
		}
		echo json_encode($response);
	}
	
	/**
	 * Carga la información donde se llevo a cabo la Comida de Representacion
	 * 
	 * @param tramite int POST	=> Id del tramite asociado a la comprobacion
	 * @return json				=> La informacion del Lugar, Ciudad y Total de Invitados a la comida de Representacion
	 */
	
	if(isset($_POST["comidasRepresentacion"])){
		$tramite = $_POST["tramite"];
		$infoComprobacion = '';
		$sql = "SELECT co_lugar, 
				co_ciudad, 
				COUNT(*) AS totalInvitados 
				FROM comensales
				INNER JOIN comprobacion_gastos ON (co_id = c_comprobacion)
				INNER JOIN tramites ON (t_id = co_mi_tramite)
				WHERE t_id = $tramite";
		$res = $conexion->consultar($sql);
		$infoComprobacion = codifica(mysql_fetch_assoc($res));
		
		if(empty($infoComprobacion['co_lugar']) && empty($infoComprobacion['co_ciudad'])){
			$sql = "SELECT sg_lugar AS co_lugar, 
				sg_ciudad AS co_ciudad, 
				COUNT(*) AS totalInvitados 
				FROM comensales
				INNER JOIN solicitud_gastos ON (sg_id = c_solicitud)
				WHERE sg_tramite = $tramite";
			$res = $conexion->consultar($sql);
			$infoComprobacion = codifica(mysql_fetch_assoc($res));
		}
		echo json_encode($infoComprobacion);
	}
	
	/**
	 * Carga la información del usuario que realizo la Comida de representacion
	 *
	 * @param usuario int POST 		=> Id del usuario en sesion
	 * @return json					=> Informacion de los invitados Nombre, Empresa, Puesto, Tipo
	 */
	
	if(isset($_POST["usuarioInvitado"])){
		$usuario = $_POST['usuario'];
		$sql = "SELECT 
				nombre, 
				IF(u_interno = 1, 'Interno', 'Externo') AS tipo, 
				npuesto, 
				e_nombre
				FROM empleado
				INNER JOIN usuario ON (idfwk_usuario = u_id)
				INNER JOIN empresas ON (e_codigo = u_empresa)
				WHERE u_id = $usuario";
		$res = $conexion->consultar($sql);
		while ($row = codifica(mysql_fetch_assoc($res))){
			$cont++;
			$response->rows[$cont] = array('nombreInvitado'		=> $row['nombre'],
											'puestoInvitado'	=> $row['npuesto'],
											'empresaInvitado'	=> $row['e_nombre'],
											'tipoInvitado'		=> $row['tipo']
										);
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
		$usuario = $_POST['usuario'];
		$sql = "SELECT
			e_nombre
			FROM empresas
			INNER JOIN usuario ON (e_codigo = u_empresa)
			WHERE u_id = $usuario";
		//error_log("--->> ".$sql);
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	
	if(isset($_REQUEST["excepciones"])){
		$idTramite = $_REQUEST["tramite"];
		$sql = "SELECT 
					(SELECT cp_concepto FROM cat_conceptos WHERE cp_id = ex_concepto) AS cp_concepto, 
					ex_mensaje, 
					ex_diferencia,
						(CASE ex_presupuesto
							WHEN 1 THEN 'Excepci&oacute;n de Presupuesto'
							WHEN 0 THEN 'Excepci&oacute;n de Pol&iacute;tica' 
							ELSE ''
						END) AS tipoExcepcion
				 FROM excepciones e 
				INNER JOIN comprobacion_gastos c ON e.ex_comprobacion = c.co_id
				WHERE c.co_mi_tramite = $idTramite";
		$res = $conexion->consultar($sql);
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
				JOIN comprobacion_gastos ON co_id = ex_comprobacion
				LEFT JOIN tramites ON co_mi_tramite = t_id
				WHERE t_id = %s
				AND ex_presupuesto = 1", $tramiteaConsultar);
	$res = $cnn->consultar($query);
	while($row = codifica(mysql_fetch_assoc($res))){
		$response = array("concepto" 	=> $row["cp_concepto"],
						"mensaje"	=> $row["ex_mensaje"],
						"excedente" => $row["excedente"],
						"tipoExcepcion" => $row["tipoExcepcion"]);
	}		
	echo json_encode($response);
}
	if(isset($_POST['conceptoXML'])){
		$concepto = $_POST["concepto"];		
		$sql = "SELECT * FROM cat_conceptos WHERE  cp_id = $concepto";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	if(isset($_POST['ivaTrasladado'])){
		$concepto = $_POST["concepto"];		
		$sql = "SELECT * FROM cat_conceptos WHERE cp_cuenta = 1160013";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	if(isset($_POST['retIVA'])){
		$concepto = $_POST["concepto"];		
		$sql = "SELECT * FROM cat_conceptos WHERE cp_cuenta = 2150005";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	if(isset($_POST['retISR'])){
		$concepto = $_POST["concepto"];		
		$sql = "SELECT * FROM cat_conceptos WHERE cp_cuenta = 2150103";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	if(isset($_POST['descuento'])){
		$concepto = $_POST["concepto"];		
		$sql = "SELECT * FROM cat_conceptos WHERE cp_concepto = 'Descuento'";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	if(isset($_POST['IEPS'])){
		$concepto = $_POST["concepto"];		
		$sql = "SELECT * FROM cat_conceptos WHERE cp_concepto = 'IEPS'";
		$res = $conexion->consultar($sql);
		echo json_encode(codifica(mysql_fetch_assoc($res)));
	}
	if(isset($_POST['borraFactura'])){
		$idFactura = $_POST['idF'];
		$sql = "DELETE FROM factura 
				WHERE id_factura = '$idFactura'";
		$conexion->ejecutar($sql);
		echo json_encode(array(true));
	}	
?>