<?php
session_start();


require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once('../lib/php/utils.php');

$userar = $_SESSION["usuario"];
$var = "pero";
$I	= new Interfaz("Reportes",true);

$cnn = new conexion();
//Borrar Vista resultado
$borra_vista = sprintf("DROP VIEW IF EXISTS resultado");
$cnn->ejecutar($borra_vista);
//Borrar Vista resultado2
$borra_vista2 = sprintf("DROP VIEW IF EXISTS resultado2");
$cnn->ejecutar($borra_vista2);
//Borrar Tabla resultado
$borra_tabla = sprintf("DROP TABLE IF EXISTS resultado");
$cnn->ejecutar($borra_tabla);
//Borrar Tabla resultado2
$borra_tabla2 = sprintf("DROP TABLE IF EXISTS resultado2");
$cnn->ejecutar($borra_tabla2);

function insertarRuta($rutaNombre,$t_id){
	$cnn = new conexion();
	$sql=sprintf("INSERT INTO rutatransformacion(rut_id,rut_ruta) VALUES('%s','%s')",$t_id,$rutaNombre);
	$cnn->consultar($sql);
}


function get_nombre_de_agrupacion_usuarios_if_exist($id){
	$agrup_usu = new AgrupacionUsuarios();
	if($agrup_usu->Load_Grupo_de_Usuario_By_ID($id)){
		$agrup_nombre = $agrup_usu->Get_dato("au_nombre");
	}else{
		$agrup_nombre = "";
	}

	return $agrup_nombre;
}

//Funcion para generar la ruta con nombres para reportes
function generaRutaNombre($ruta){
	$autorizadores ="";
	//Traerá la ruta de autorización de la solicitud correspondiente
	$usuarioAprobador = new Usuario();
	//Obtener los nombres de los Autorizadores
	$token = strtok($ruta,"|");
	$autorizadores = "";
	if($token != false){
		$aux = get_nombre_de_agrupacion_usuarios_if_exist($token);
		if($aux == ""){
			$usuarioAprobador->Load_Usuario_By_ID($token);
			$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
		}else{
			$nombre_autorizadores = $aux;
		}
		$autorizadores .= $nombre_autorizadores;
		$token = strtok("|");
		while($token != false){
			$autorizadores .= ", ";
			$aux = get_nombre_de_agrupacion_usuarios_if_exist($token);
			if($aux == ""){
				$usuarioAprobador->Load_Usuario_By_ID($token);
				$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
			}else{
				$nombre_autorizadores = $aux;
			}
			$autorizadores .= $nombre_autorizadores;
			$token = strtok("|");
		}
	}
	
	return $autorizadores;
}

?>
<html>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/formatNumber.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/withoutReloading.js" type="text/javascript"></script>
<!-- Llamada para la carga de combos -->
<script  src="../reportes/js/reportes.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">
//Fecha
$(function() {
	//campo
	$("#fechaInicio").date_input();
	$("#fechaFin").date_input();
	});
	//obtiene formato de fecha YYYY/mm/dd
	$.extend(DateInput.DEFAULT_OPTS, {
	stringToDate: function(string) {
	var matches;
	if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
	return new Date(matches[1], matches[2] - 1, matches[3]);
	} else {
	return null;
	};
	},
	dateToString: function(date) {
	var month = (date.getMonth() + 1).toString();
	var dom = date.getDate().toString();
	if (month.length == 1) month = "0" + month;
	if (dom.length == 1) dom = "0" + dom;
	return dom + "/" + month + "/" + date.getFullYear();
	},
	rangeStart: function(date) {
     return this.changeDayTo(this.start_of_week, new Date(date.getFullYear(), date.getMonth()), -1);
    },

    rangeEnd: function(date) {
    return this.changeDayTo((this.start_of_week - 1) % 7, new Date(date.getFullYear(), date.getMonth() + 1, 0), 1);
    }

	})

	//Opciones de Idioma del Calendario
	jQuery.extend(DateInput.DEFAULT_OPTS, {
	month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});
//fin Fecha
</script>

<link rel="stylesheet" type="text/css" href="../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../css/table_style.css"/>
<link rel="stylesheet" type="text/css" href="../css/style_Table_Edit.css"/>
<style type="text/css">
.style1 {color: #FF0000}
</style>
<?
	if(isset ($_GET['reporte'])){
		$_SESSION['reporte'] = $_GET['reporte'];
	}
	echo "<center><h3>".$_SESSION['reporte']."</h3></center>";
?>
<FORM ACTION="reporte.php" METHOD="post" name="formReporte">
<table align='center', width='30%'>
<? if(($_SESSION['reporte']== "Reporte de Gastos")||($_SESSION['reporte']== "Reporte de Anticipos no comprobados")){?>
	<? if($_SESSION['reporte']== "Reporte de Gastos"){?>
		
		<!-- Tipo Empleado -->
			<tr>
				<td  colspan='1' align='right' >Tipo de Empleado:</td>
					<td colspan='2' align="left" style="top:100px;">
					<select name="tipoemp" id="tipoemp">
						<option value="1">BMW</option>
						<option value="0">EXTERNOS</option>						
					</select>
				</td>
			</tr>
	<?php }?>
			<!-- Empresa -->
			<tr>
				<td colspan='1' align='right' style="left:20px;">Empresa:</td>
					<td colspan='2' align="left" style="top:100px;">
					<?php $empresa = new Empresa(); ?>						
						<select name="EmpresaOpciones" id="EmpresaOpciones" onChange="obtener_cecos(this.value)">
							<option name="EmpresaOpciones" id="EmpresaOpciones" value="Seleccione una Empresa">
								Seleccione una Empresa
							</option>
						<?php foreach($empresa->Load_all() as $arrE){ ?>
						<option name="EmpresaOpciones" id="EmpresaOpciones" value="<?php echo $arrE['e_id'];?>">
									<?php echo $arrE['e_nombre'];?>
							</option>
							<?php } ?>		
						</select>
					</td>								
			</tr>
			<!-- CECOS -->
			<tr>
				<td  colspan='1' align='right' >CECO:</td>
				<td colspan="2">
				<?if($_SESSION['reporte']== "Reporte de Gastos"){?>
					<select name="CecoOpciones" id="CecoOpciones" onChange="">
				<?php }else if($_SESSION['reporte']== "Reporte de Anticipos no comprobados"){?>
					<select name="CecoOpciones" id="CecoOpciones" onChange="obtener_empleado(this.value)" >
				<?php }?>	
						<option name="CecoOpciones" id="CecoOpciones" value="-1">
							Sin Datos
						</option>
					</select>					
				</td>											
			</tr>
				
		<?if($_SESSION['reporte']== "Reporte de Anticipos no comprobados"){?>
			<!-- EMPLEADO -->
			<tr>
				<td  colspan='1' align='right' >Empleado:</td>
				<td colspan="2">
					<select name="EmpleadoOpciones" id="EmpleadoOpciones" onChange="" >
						<option name="EmpleadoOpciones" id="EmpleadoOpciones" value="-1">
							Sin Datos
						</option>
					</select>					
				</td>											
			</tr>
		<?php }?>
	<?php }?>
		</table>		
		<br>
		<!-- Calendario -->			
		<table align='center', width='30%'>
			<td  colspan='1' align='right' >Periodo:</td>			
			<tr>							
				<td><br><input name="fechaInicio" id="fechaInicio" value="" size="15" />
					<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> Fecha Inicial</td>
				<td><br><input name="fechaFin" id="fechaFin" value="" size="15" />
					<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> Fecha Final</td>
			</tr>
		</table>
		
	<table align='center', width='30%'>    
		<!-- Submit -->    
		<tr><td colspan='2' align='center'>&nbsp;</td></tr>    
		<tr>
			<td colspan='2' align='center'>
			   <INPUT TYPE="submit" name='buscar' id='buscar' value="buscar">
			</td>
		</tr>     
	</table>
</FORM>
<?php
if(isset ($_POST['buscar'])){
	
	$count = 0;
	
		//Variables necesarias  para la busqueda
		$tEmpleado=$_POST['tipoemp'];
		$empresaId=$_POST['EmpresaOpciones'];		
		$ceco=$_POST['CecoOpciones'];
		$nomEmpleado=$_POST['EmpleadoOpciones'];
		
		$fechaInicio=$_POST["fechaInicio"];
		$fechaFin=$_POST["fechaFin"];
		
		$parametros =" ";
		
		//====================== Validacion de busqueda (parametros)======================
		//Empresa
		if($empresaId !=null){
			 if($empresaId == "Seleccione una Empresa"){
				$parametros.="";
				$empresaId="";
			}else{
				if($_SESSION['reporte']== "Reporte de Gastos"){
					$empresa= new Empresa();
					$empresa->Load_Empresa($empresaId);
					$empresaNom=$empresa->Get_Dato("e_nombre");
					$parametros.="WHERE empresa LIKE '%{$empresaNom}%'";
				}else{
					$parametros.="";
				}
				
			}
		}else{
			$parametros.="";
		}
		
		
		//Centro de costos
		if($ceco != null){
			if($ceco == '-1'||$ceco == "Sin Datos"){
				$parametros.="";
				$ceco=" ";
			}else{
				if($_SESSION['reporte']== "Reporte de Gastos"){
					$ceco = new CentroCosto();
					$ceco->Busca_CeCo($_POST['CecoOpciones']);
					$Ceco=$ceco->Get_Dato('cc_centrocostos');
					$parametros.=" AND centro_de_costos ='{$Ceco}'";
				}else{			
					$parametros.=" AND cc_id='{$ceco}'";
				}
			}
				
			}else{
			$parametros.="";
		}
		
		//nombre de empleado
		if($_SESSION['reporte']== "Reporte de Anticipos no comprobados"){		
			if( $nomEmpleado != null){
				if($nomEmpleado == '-1'||$nomEmpleado == "Sin Datos"){
					$parametros.="";
					$nomEmpleado="";
				}else{
					$parametros.="AND nombre LIKE '%{$nomEmpleado}%'";
				}
			}
		}
		
		//validacion para fechas
				
		
		if(($_POST["fechaFin"] == "") && ($_POST["fechaInicio"] == "")){
			$parametros.="";
		}else{
		
			//Toma de valores  de fecha inicial y final
			if (isset($_POST["fechaInicio"])&& !empty($_POST["fechaInicio"])){
				$date=explode("/",$_POST["fechaInicio"]);
				 if(count($date)!=3){
				return "";
				}
				$date_db=$date[2]."-".$date[1]."-".$date[0];
				
				if($_SESSION['reporte'] == "Reporte de Gastos"){					
					if(($_POST['EmpresaOpciones'] == "Seleccione una Empresa") && ($_POST['CecoOpciones'] == '-1')){
						$parametros.=" WHERE fecha_de_comprobacion >= '".$date_db." 00:00:00'";
					}else{
						$parametros.= "AND fecha_de_comprobacion >= '".$date_db." 00:00:00'";
					}
				}elseif(($_SESSION['reporte']== "Reporte de Anticipos no comprobados") || ($_SESSION['reporte']== "Reporte de Anticipos generados y registrados en SAP")){
					$parametros.=" AND sv_fecha_registro >= '".$date_db." 00:00:00'";
				}elseif($_SESSION['reporte']== "Reporte de Gastos AMEX no comprobados"){
					$parametros.= "WHERE fecha >= '".$date_db."'";
				}elseif($_SESSION['reporte']== "Reporte de Viajes en Avion"){
					$parametros.=" AND svi_fecha_salida >= '".$date_db." 00:00:00'";
				}elseif($_SESSION['reporte']== "Reporte de Descuentos"){
					$parametros.=" AND t_fecha_registro >= '".$date_db." 00:00:00'";
				}elseif($_SESSION['reporte']== "Reporte de Solicitudes fuera de politica"){
					$parametros.=" WHERE sv_fecha_registro >= '".$date_db." 00:00:00'";
				}
				
			}
			
			if (isset($_POST["fechaFin"])&& !empty ($_POST["fechaFin"])){
				$date=explode("/",$_POST["fechaFin"]);
				 if(count($date)!=3){
				return "";
				}
				$date_db=$date[2]."-".$date[1]."-".$date[0];
				if($_SESSION['reporte']== "Reporte de Gastos"){
					$parametros.=" AND fecha_de_comprobacion <= '".$date_db." 23:59:59'";
				}elseif(($_SESSION['reporte']== "Reporte de Anticipos no comprobados") || ($_SESSION['reporte']== "Reporte de Anticipos generados y registrados en SAP")){
					$parametros.=" AND sv_fecha_registro <= '".$date_db." 23:59:59'";
				}elseif($_SESSION['reporte']== "Reporte de Gastos AMEX no comprobados"){
					$parametros.=" AND fecha <= '".$date_db."'";
				}elseif($_SESSION['reporte']== "Reporte de Viajes en Avion"){
					$parametros.=" AND svi_fecha_salida <= '".$date_db." 23:59:59'";
				}elseif($_SESSION['reporte']== "Reporte de Descuentos"){
					$parametros.=" AND t_fecha_registro <= '".$date_db." 23:59:59'";
				}elseif($_SESSION['reporte']== "Reporte de Solicitudes fuera de politica"){
					$parametros.=" AND sv_fecha_registro >= '".$date_db." 23:59:59'";
				}
			}
		}		
	
	 //Empieza la validacion del tipo de reporte
	 if($_SESSION['reporte']== "Reporte de Gastos"){
	 		 	
	 	//se realizara el drop de las vistas si es que existen
	 	$borra_vista_filtrado = sprintf("DROP VIEW IF EXISTS VistaFiltrado");
	 	$cnn->ejecutar($borra_vista_filtrado);
	 	$borra_vista_gastos1 = sprintf("DROP VIEW IF EXISTS  V_Gastos1");
	 	$cnn->ejecutar($borra_vista_gastos1);
	 	$borra_vista_gastos2 = sprintf("DROP VIEW IF EXISTS  V_Gastos2");
	 	$cnn->ejecutar($borra_vista_gastos2);
	 	$borra_vista_gastos_ruta1 = sprintf("DROP VIEW IF EXISTS VistaGastosRuta1 ");
	 	$cnn->ejecutar($borra_vista_gastos_ruta1);
	 	$borra_vista_gastos_ruta2 = sprintf("DROP VIEW IF EXISTS VistaGastosRuta2 ");
	 	$cnn->ejecutar($borra_vista_gastos_ruta2);

	 	//ejecutamos las vistas relacionadas con la ruta
	 	$vistaRuta1=sprintf("Call VistaGastosRuta1();");
	 	mysql_query($vistaRuta1);
	 	$vistaRuta2=sprintf("Call VistaGastosRuta2();");
	 	mysql_query($vistaRuta2);
	 	
	 	//===============================Construccion de la ruta en nombres
	 	
	 	//Crearemos la tabla que nos permitira alojar las rutas transformadas
	 	$rutaNombres="";
	 	$borra_tabla = sprintf("DROP TABLE IF EXISTS  rutatransformacion");
	 	$cnn->ejecutar($borra_tabla);
	 	
	 	$creaTabla="CREATE TABLE rutatransformacion(
	 	rut_id VARCHAR(45),
	 	rut_ruta VARCHAR(256)
	 	)";
	 	$cnn->consultar($creaTabla);
	 	 
	 	$arregloRuta="SELECT folio,ruta_de_aprobacion FROM VistaGastosRuta1 ".$parametros."
	 	UNION
	 	SELECT folio,ruta_de_aprobacion FROM VistaGastosRuta2 ".$parametros."";

	 	
	 	error_log($arregloRuta);
	 	$rst2 = $cnn->consultar($arregloRuta);
	 	 
	 	while ($filaa = mysql_fetch_assoc($rst2)) {
	 		$rutaNombres=generaRutaNombre($filaa['ruta_de_aprobacion']);
	 		error_log("rutas".$rutaNombres);
	 		insertarRuta($rutaNombres,$filaa['folio']);
	 	}	 	
	 	//=======================================Fin
	 	
	 	//ejecutamos las vistas relacionadas con los datos generados
	 	$Vista1=sprintf("Call VistaGastos1();");
	 	mysql_query($Vista1);
	 	error_log($Vista1);
	 	$Vista2=sprintf("Call VistaGastos2();");
	 	mysql_query($Vista2);

	 	//se creara una vista en la cual genere el filtrado por la fecha 
	 	$VistaFiltrado="CREATE VIEW VistaFiltrado AS
	 	SELECT * FROM V_Gastos1".$parametros."
	 	UNION
	 	SELECT * FROM V_Gastos2".$parametros."";
	 	$cnn->ejecutar($VistaFiltrado);
	 	
	 	//seleccionamos lo que hay en las vistas y realizamos una union.	 	
	 	$sql=" CREATE TABLE resultado as
	 	SELECT no_de_solicitud, tipo_de_comprobacion,DATE_FORMAT(fecha_de_comprobacion,'%d/%m/%Y') AS 'fecha_de_comprobacion',folio_de_solicitud,
	 	numero_de_empleado,nombre_del_empleado,empresa,documento,DATE_FORMAT(fecha_de_solicitud,'%d/%m/%Y') AS 'fecha_de_solicitud',fecha_de_autorizacion,motivo,
	 	anticipo,centro_de_costos,concepto,cuenta_contable,importe_sin_iva,propina,impuesto_sobre_hospedaje,iva,
	 	monto_total,moneda,rfc,transaccion,numero_de_tarjeta,ruta_de_aprobacion FROM VistaFiltrado";
	 	
	 	
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "reporteDeGastos";
		$_SESSION['head'] = "REPORTE DE GASTOS";
		
	}elseif($_SESSION['reporte']== "Reporte de Anticipos no comprobados"){
		//nombres.	 	
	 	$rutaNombres="";	 	
	 	//Primero crearemos la tabla que nos permitira alojar las rutas transformadas
	 	
	 	$borra_tabla = sprintf("DROP TABLE IF EXISTS  rutatransformacion");
	 	$cnn->ejecutar($borra_tabla);
	 	
	 	$creaTabla="CREATE TABLE rutatransformacion(
		rut_id VARCHAR(45),		
		rut_ruta VARCHAR(256)
		)";
	 	$cnn->consultar($creaTabla);
	 	
		$arregloRuta="SELECT
		t.t_id,		
		t.t_ruta_autorizacion		
		FROM tramites AS t
		INNER JOIN flujos ON flujos.f_id = t.t_flujo
		INNER JOIN comprobaciones cmp ON cmp.co_mi_tramite = t.t_id
		LEFT  JOIN solicitud_viaje sv ON cmp.co_tramite = sv.sv_tramite 
		INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
		INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
		INNER JOIN detalle_comprobacion dc ON dc.dc_comprobacion = cmp.co_id
		LEFT  JOIN cat_conceptosbmw ccp ON ccp.dc_id = dc.dc_concepto		
		WHERE dc.dc_id IS NOT NULL".$parametros."
	 	GROUP BY t.t_id;";
		
		error_log($arregloRuta);
		$rst2 = $cnn->consultar($arregloRuta);

		while ($filaa = mysql_fetch_assoc($rst2)) {
			$rutaNombres=generaRutaNombre($filaa['t_ruta_autorizacion']);
			error_log("rutas".$rutaNombres);
			insertarRuta($rutaNombres,$filaa['t_id']);
		}
		
		
	 	$sql="CREATE TABLE resultado as
	 	SELECT
		t.t_id AS 'NO. DE SOLICITUD',
		CONCAT(cc.cc_centrocostos, '-', cc.cc_nombre) AS 'CENTRO DE COSTOS',
		emp.nombre AS 'EMPLEADO',
		DATE_FORMAT(sv.sv_fecha_registro,'%d/%m/%Y') AS 'FECHA DE SOLICITUD',
		IFNULL(ccp.cp_concepto, '') AS 'CONCEPTO',
		sv.sv_total_anticipo AS 'ANTICIPO',
		rt.rut_ruta AS 'RUTA DE APROBACIÓN'		
		FROM tramites AS t
		INNER JOIN flujos ON flujos.f_id = t.t_flujo
		INNER JOIN comprobaciones cmp ON cmp.co_mi_tramite = t.t_id
		LEFT  JOIN solicitud_viaje sv ON cmp.co_tramite = sv.sv_tramite 
		INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
		INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
		INNER JOIN detalle_comprobacion dc ON dc.dc_comprobacion = cmp.co_id
		LEFT  JOIN cat_conceptosbmw ccp ON ccp.dc_id = dc.dc_concepto
		INNER JOIN rutatransformacion rt ON rt.rut_id = t.t_id
		WHERE dc.dc_id IS NOT NULL".$parametros."
		GROUP BY t.t_id;";
			 
	 		 	
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "ReportedeAnticiposnoComprobados";
		$_SESSION['head'] = "REPORTE DE ANTICIPOS NO COMPROBADOS";

	}elseif($_SESSION['reporte']== "Reporte de Viajes en Avion"){
		
	 	$sql="CREATE TABLE resultado as
	 	
	 	SELECT				
		sv.sv_tramite  AS 'No. DE SOLICITUD',
		emp.nombre AS 'NOMBRE',
		svi.svi_origen AS 'ORIGEN',
		svi.svi_destino AS 'DESTINO',
		DATE_FORMAT(sv.sv_fecha_registro,'%d/%m/%Y') AS 'FECHA SOLICITUD',
		IFNULL(t.t_fecha_cierre, '') AS 'FECHA APROBACIÓN',
		IFNULL(svi.svi_fecha_salida, '') AS 'FECHA SALIDA',
		IFNULL(svi.svi_fecha_llegada, '') AS 'FECHA REGRESO',
		IFNULL(svi.svi_monto_vuelo, '') AS 'SUBTOTAL',
		IFNULL(svi.svi_tua, '') AS 'TUA',
		IFNULL(svi.svi_iva, '') AS 'IVA',
		IFNULL((svi.svi_monto_vuelo + svi.svi_tua + svi.svi_iva), '') AS 'TOTAL',
		IFNULL(svi.svi_aerolinea,'') AS 'AEROLINEA',
		cc.cc_centrocostos AS 'CENTRO DE COSTOS',
		IF(svi.svi_tipo_aerolinea IS NOT NULL , 
		IF(svi.svi_tipo_aerolinea = 1 , 'Normal' ,'Bussines Class' ) ,'N/A') AS 'DESCRIPCIÓN DE CLASE DE TARIFA',
		CONCAT(svi.svi_fecha_salida, ' - ', t.t_fecha_cierre ) AS 'ANTICIPACIÓN DE COMPRA',
		IFNULL(svi.svi_monto_vuelo_cotizacion, '') AS 'MONTO SOLICITUD'		
		FROM solicitud_viaje AS sv
		INNER JOIN tramites t ON t.t_id = sv.sv_tramite
		INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
		INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
		INNER JOIN sv_itinerario svi ON svi.svi_solicitud = sv.sv_id
		WHERE svi.svi_tipo_transporte='Aéreo'
	 	AND t.t_etapa_actual='9'".$parametros."";			 		 	
	 	
	 	
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "reporteDeViajesEnAvion";
		$_SESSION['head'] = "REPORTE DE VIAJES EN AVION";
	 	
	 }elseif($_SESSION['reporte']== "Reporte de Anticipos generados y registrados en SAP"){
	 	
	 	$rutaNombres="";
	 	//Primero crearemos la tabla que nos permitira alojar las rutas transformadas	 	 
	 	$borra_tabla = sprintf("DROP TABLE IF EXISTS  rutatransformacion");
	 	$cnn->ejecutar($borra_tabla);
	 	
	 	$creaTabla="CREATE TABLE rutatransformacion(
	 	rut_id VARCHAR(45),
	 	rut_ruta VARCHAR(256)
	 	)";
	 	$cnn->consultar($creaTabla);
	 	
	 	$arregloRuta="SELECT DISTINCT
		t.t_id,
		t.t_ruta_autorizacion,
		DATE_FORMAT(sv.sv_fecha_registro,'%d/%m/%Y') 	
		FROM tramites AS t		
		INNER JOIN solicitud_viaje sv ON sv.sv_tramite=t.t_id
		INNER JOIN sv_itinerario svi ON svi.svi_solicitud = sv.sv_id
		INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
		INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
		INNER JOIN sv_conceptos_detalle svc ON svc.svc_detalle_tramite = t.t_id
		INNER JOIN cat_conceptosbmw cbmw ON cbmw.dc_id = svc.svc_detalle_concepto
	 	WHERE svc.`svc_enviado_sap` IS NOT NULL".$parametros."";
	 	
	 	$rst2 = $cnn->consultar($arregloRuta);
	 	
	 	while ($filaa = mysql_fetch_assoc($rst2)) {
	 		$rutaNombres=generaRutaNombre($filaa['t_ruta_autorizacion']);
	 		error_log("rutas".$rutaNombres);
	 		insertarRuta($rutaNombres,$filaa['t_id']);
	 	}
	 	
	 	$sql="CREATE TABLE resultado as
	 	
	 	SELECT 
		t.t_id AS 'NO. DE SOLICITUD',
		DATE_FORMAT(sv.sv_fecha_registro,'%d/%m/%Y') AS 'FECHA DE SOLICITUD',
		t.t_iniciador AS 'NÚMERO DE EMPLEADO',
		emp.nombre AS 'NOMBRE DEL EMPLEADO',
		CONCAT(cc.cc_centrocostos, '-', cc.cc_nombre) AS 'CENTRO DE COSTOS',
		svc.svc_conversion AS 'ANTICIPO',
		sv.sv_motivo AS 'MOTIVO DEL VIAJE',
		svi.svi_origen AS 'ORIGEN',
		svi.svi_destino AS 'DESTINO',
		cbmw.`cp_concepto` AS 'CONCEPTO',
		rt.rut_ruta AS 'RUTA DE APROBACIÓN'	
				
		FROM tramites AS t		
		INNER JOIN solicitud_viaje sv ON sv.sv_tramite=t.t_id
		INNER JOIN sv_itinerario svi ON svi.svi_solicitud = sv.sv_id
		INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
		INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
		INNER JOIN sv_conceptos_detalle svc ON svc.svc_detalle_tramite = t.t_id
		INNER JOIN cat_conceptosbmw cbmw ON cbmw.dc_id = svc.svc_detalle_concepto
		INNER JOIN rutatransformacion rt ON rt.rut_id = t.t_id
	 	WHERE svc.`svc_enviado_sap` IS NOT NULL".$parametros."";
	 	
			
	 	
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "reporteDeAnticiposGeneradosYRegistradosEnSAP";
		$_SESSION['head'] = "REPORTE DE ANTICIPOS GENERADOS Y REGISTRADOS EN SAP";	 	
	
	 }elseif($_SESSION['reporte']== "Reporte de Gastos AMEX no comprobados" ){
	 	
	 	$borra_vista_gastos_amex1 = sprintf("DROP VIEW IF EXISTS VistaGastosAmex1 ");
	 	$cnn->ejecutar($borra_vista_gastos_amex1);
	 	$borra_vista_gastos_amex2 = sprintf("DROP VIEW IF EXISTS VistaGastosAmex2 ");
	 	$cnn->ejecutar($borra_vista_gastos_amex2);
	 	
	 	//ejecutamos las vistas relacionadas con los datos generados
	 	$VistaAmex1=sprintf("Call VistaGastosAmex1();");
	 	mysql_query($VistaAmex1);
	 	error_log($Vista1);
	 	$VistaAmex2=sprintf("Call VistaGastosAmex2();");
	 	mysql_query($VistaAmex2);
	 	
	 	$sql="CREATE TABLE resultado as
	 	SELECT * FROM VistaGastosAmex1".$parametros."
	 	UNION
	 	SELECT * FROM VistaGastosAmex2".$parametros."";	 	
	 	
	 	
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "reporteGastosAMEXnoComprobados";
		$_SESSION['head'] = "REPORTE GASTOS AMEX NO COMPROBADOS";
	 	
	 }elseif($_SESSION['reporte']== "Reporte de Descuentos" ){	
		
	 	$sql="CREATE TABLE resultado as	 	
	 	SELECT
		emp.idempleado AS 'NÚMERO DE EMPLEADO',
		emp.nombre AS 'NOMBRE DEL EMPLEADO',
		CONCAT(cc.cc_centrocostos, '-', cc.cc_nombre) AS 'CENTRO DE COSTOS',
		IF(cmp.co_mnt_descuento <= 0, '', cmp.co_mnt_descuento ) AS 'MONTO A DESCONTAR',
		IFNULL(ex.ex_mensaje, '') AS 'OBSERVACIÓN',
		IFNULL(dc.dc_descripcion, '') AS 'COMENTARIO',
		t.t_id AS 'FOLIO DE COMPROBACIÓN',
		DATE_FORMAT(t.t_fecha_registro,'%d/%m/%Y') AS 'FECHA DE COMPROBACIÓN'
		FROM tramites AS t
		
		INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
		
		INNER JOIN comprobaciones cmp ON t.t_id = cmp.co_id
		INNER JOIN cat_cecos cc ON cc.cc_id = cmp.co_cc_clave
		INNER JOIN detalle_comprobacion dc ON dc.dc_comprobacion = cmp.co_id
		LEFT  JOIN excepciones ex ON dc.dc_id = ex.ex_comprobacion_detalle
		WHERE cmp.co_id IS NOT NULL".$parametros."
		GROUP BY t.t_id";
	 	
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "reporteDeDescuentos";
		$_SESSION['head'] = "REPORTE DE DESCUENTOS";
	 	
	 }elseif($_SESSION['reporte']== "Reporte de Solicitudes fuera de politica" ){
	 	
	 	$sql="CREATE TABLE resultado as
	 	SELECT
	 	sv.sv_tramite  AS 'FOLIO',
	 	DATE_FORMAT(sv.sv_fecha_registro,'%d/%m/%Y') AS 'FECHA DE CREACIÓN',
	 	t.t_iniciador AS 'NÚMERO DE EMPLEADO',
	 	emp.nombre AS 'NOMBRE DEL EMPLEADO',
	 	CONCAT (cc.cc_centrocostos, ' - ' , cc.cc_nombre) AS 'CENTRO DE COSTOS',
	 	sv.sv_total_anticipo AS 'ANTICIPO',
	 	t.t_etiqueta AS 'MOTIVO',
	 	IFNULL(DATE_FORMAT(svi.svi_fecha_salida,'%d/%m/%Y'), '' )AS 'FECHA DE VIAJE',
	 	svi.svi_origen AS 'ORIGEN',
	 	svi.svi_destino AS 'DESTINO',
	 	IFNULL(ccp.cp_concepto, '') AS 'CONCEPTO',
	 	IFNULL(ccp.cp_cuenta, '') AS 'CUENTA CONTABLE',
	 	IFNULL(svcd.svc_detalle_monto_concepto, '') AS 'MONTO CONCEPTO',
	 	cmp.co_total AS 'APROBADO',
	 	IFNULL(pbmw.p_nombre, '') AS 'PARÁMETRO',
	 	IFNULL(preg.pr_cantidad, '') AS 'POLITICA ASOCIADA',
	 	IFNULL((preg.pr_cantidad - svcd.svc_detalle_monto_concepto), '')AS 'VALOR QUE ROMPE LA REGLA',
	 	#AS 'POLÍTICA DIAS ANTICIPACION Y PRESUPUESTO',
	 	IFNULL(t.t_ruta_autorizacion, '') AS 'RUTA DE APROBACIÓN'	 	
	 	FROM solicitud_viaje AS sv	 	
	 	INNER JOIN tramites t ON t.t_id = sv.sv_id
	 	INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
	 	INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
	 	INNER JOIN sv_itinerario svi ON svi.svi_solicitud = sv.sv_id	 	
	 	INNER JOIN comprobaciones cmp ON cmp.co_mi_tramite = t.t_id
	 	LEFT  JOIN detalle_comprobacion dc ON dc.dc_comprobacion = cmp.co_id
	 	LEFT  JOIN cat_conceptosbmw ccp ON ccp.dc_id = dc.dc_concepto
	 	LEFT  JOIN sv_conceptos_detalle svcd ON svcd.svc_detalle_concepto = ccp.dc_id	 	
	 	LEFT  JOIN parametrosbmw pbmw ON pbmw.p_concepto = ccp.dc_id
	 	LEFT  JOIN parametro_regionbmw preg ON pbmw.p_id = preg.p_id".$parametros."	 	
	 	GROUP BY t.t_id";	 		 	
	 	
	 	echo $sql;
	 	$_SESSION['query'] = $sql;
		$_SESSION['nombre'] = "reporteDeSolicitudesFueraDePolitica";
		$_SESSION['head'] = "REPORTE DE SOLIITUDES FUERA DE POLITICA";
	 	
	 }
	
	$res=mysql_query($sql);
	$sql="select * from resultado;";
	$res=mysql_query($sql);
	//$fields = mysql_num_fields($res);
	/*$aux="";
	$aux.="<html><table  align='center' cellspacing='4' cellpadding='4' >";

	for ($i=0;$i<$fields;$i++){
		$aux.="<td style=\"font-size: 12px;\"><br><br><strong>".mysql_field_name( $res , $i )."</strong></td>";
	}
	while($row=mysql_fetch_row($res)){
		$line = '';
		$aux.="<tr>";
		foreach($row as $value){
			$aux.="<td style=\"font-size: 10px;\" align='left'>{$value}</td>";
		}
		$aux.="</tr>";
	}
	$aux.="</table></html>";*/
	//error_log("wuaaaa".$res);
	$count = mysql_num_rows($res);
	$sql="drop table if exists resultado;";
	mysql_query($sql);

	if($count > 0){
		if(($_POST["fechaFin"] == "") && ($_POST["fechaInicio"] == "")){
			if($count == 1){
				echo "<center><font color=blue> ".$count." registro fue encontrado.</font></center>";
			}else{
				echo "<center><font color=blue> ".$count." registros fueron encontrados.</font></center>";
			}
			
		}else{
			if($count == 1){
				echo "<center><font color=blue>Reporte entre ".$fechaInicio." y ".$fechaFin.", ".$count." registro encontrado.</font></center>";
			}else{
				echo "<center><font color=blue>Reporte entre ".$fechaInicio." y ".$fechaFin.", ".$count." registros encontrados.</font></center>";
			}
			
		}		
		
		/*echo $aux;*/
		?>
			<FORM ACTION="exportar.php" METHOD="get" name="rep1" target="_blank" id="rep1">
				<table align='center', width='30%'>    
					<!-- Submit -->    
					<tr><td colspan='2' align='center'>&nbsp;</td><tr>    
					<tr>
						<td colspan='2' align='center'>
							<INPUT TYPE="submit" name='exportar' id='exportar' value="Exportar a excel">
						</td>
					</tr>     
				</table>
			</FORM>
		<?
	}else{
		echo "<center><font color=red>Sus par&aacute;metros de b&uacute;squeda no generaron resultados.</font></center>";
	}
}
$I->footer();
?>
</html>
