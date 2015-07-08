<?PHP 
/********************************************************
*         T&E Módulo de Edición y vista de 			*
*				Solicitud de Viajes      				*
* Creado por:	Luis Daniel Barojas Cortes				*
* PHP, jQuery, JavaScript, CSS                          *
*********************************************************/
require_once("$RUTA_A/flujos/solicitudes/services/utils.php");
require_once("$RUTA_A/flujos/comprobaciones/services/func_comprobacion.php");
require_once("$RUTA_A/functions/Divisa.php");
require_once("$RUTA_A/functions/utils.php");
				
    require_once "../../Connections/fwk_db.php";
    $empleado		= $_SESSION["idusuario"];
    $idusuario      = $_SESSION["idusuario"];
    $tipoUsuario    = $_SESSION["perfil"];
    $cnn			= new conexion();
    $aux= array();    
    $query="SELECT sv_id, t_iniciador,t_id, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y') as fecha_registro, t_etiqueta, 
				svi_id, svi_tipo_viaje,  svi_destino,  
				svi_medio, svi_kilometraje,  svi_horario, DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') as fecha_salida, 
				DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') as fecha_llegada,
				svi_iva, svi_tua,
				svi_hotel, sv_viaje, sv_observaciones, et_etapa_id,et_etapa_nombre, t_dueno,  cc_centrocostos, cc_nombre, 
				cc_id,  t_flujo, (select sum(svc_detalle_monto_concepto)
				FROM sv_conceptos_detalle WHERE svc_detalle_tramite = t_id) as monto, sv_total, t_etapa_actual, t_delegado  
                FROM sv_itinerario INNER JOIN solicitud_viaje ON (sv_itinerario.svi_solicitud=solicitud_viaje.sv_id) 
				INNER JOIN tramites ON (tramites.t_id=solicitud_viaje.sv_tramite) 
				INNER JOIN etapas on etapas.et_etapa_id = tramites.t_etapa_actual and tramites.t_flujo = etapas.et_flujo_id
				LEFT JOIN cat_cecos on (sv_ceco_paga = cc_id)
				WHERE t_id={$_GET['id']} ";
    
    $rst=$cnn->consultar($query);
    while($datos=mysql_fetch_assoc($rst)){
        array_push($aux,$datos);
    }
	//////////////////////////////////////
	
	
    // Datos de la solicitud
    $tramiteDias=new Tramite();
	$dias_de_viaje = $tramiteDias->calculoDias($_GET['id']);
    $id_Solicitud=mysql_result($rst,0,"sv_id");
    $iniciador=mysql_result($rst,0,"t_iniciador");
    $idItinerario=mysql_result($rst,0,"svi_id");
    $t_id=mysql_result($rst,0,"t_id");
    $fecha_tramite=mysql_result($rst,0,"fecha_registro");
    $motivo=mysql_result($rst,0,"t_etiqueta");
    $anticipo="";//mysql_result($rst,0,"sv_anticipo");
    $totaldias="";//mysql_result($rst,0,"sv_dias_viaje");
    $observaciones=mysql_result($rst,0,"sv_observaciones");
    $flujo=mysql_result($rst,0,"t_flujo");    
    $etapa=mysql_result($rst,0,"et_etapa_nombre");
    $dueno=mysql_result($rst,0,"t_dueno");
    $divisa="";//mysql_result($rst,0,"sv_divisa");
    $tasa="";//mysql_result($rst,0,"sv_tasa");
    $cc_centrocostos=mysql_result($rst,0,"cc_centrocostos");
    $cc_nombre=mysql_result($rst,0,"cc_nombre");    
    $CentroCostoId=mysql_result($rst,0,"cc_id");
    $sv_fecha_viaje="aaa";//mysql_result($rst,0,"sv_fecha_viaje");
    //$monto=mysql_result($rst,0,"sv_anticipo");
	$monto="hola";
	$tipo_viaje = mysql_result($rst,0,"sv_viaje");
	$id_etapa = mysql_result($rst,0,"et_etapa_id");
	$sv_total_viaje = mysql_result($rst,0,"sv_total");
	$t_etapa_actual = mysql_result($rst,0,"t_etapa_actual");
	$t_delegado = mysql_result($rst,0,"t_delegado");
	
	// Carga presupuesto
    $Ceco=new CentroCosto();
    $presupuesto_disponible = "";//$Ceco->get_presupuesto_disponible($CentroCostoId, $sv_fecha_viaje);
            
    // Carga datos del usuario
    $query=" SELECT * FROM usuario where u_id={$iniciador}";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $iniciador=$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"];
	$num_empleado=$fila["u_id"];
	$u_email=$fila["u_email"];

    // Dueño de la solitud
    $queryJefe="SELECT * from usuario where u_id='$dueno'";        
    $rst=$cnn->consultar($queryJefe);
    $aprobador = mysql_result($rst,0,"u_nombre");
    
	$aux = array();
	$query = "SELECT svi_id, 
			svi_tipo_viaje, 
			sv.sv_viaje, 
			svi_origen, 
			svi_destino, 
			svi_dias_viaje, 
			svi_horario, 
			svi_monto_vuelo_cotizacion, 
			DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') AS fecha_salida, 
			svi_horario_llegada, 
			DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') AS fecha_llegada, 
			svi.svi_medio, 
			svi_hotel_agencia, 
			svi_renta_auto 
			FROM sv_itinerario AS svi
			INNER JOIN solicitud_viaje AS sv
			ON sv.sv_id = svi.svi_solicitud
			WHERE svi.svi_solicitud = {$id_Solicitud}";
	$rst = $cnn->consultar($query);
	
	while($datos=mysql_fetch_assoc($rst)){
		array_push($aux,$datos);
	}

	if($tipo_viaje == "Redondo"){
		$fecha_total = mysql_result($rst,0,"fecha_salida")."|".mysql_result($rst,0,"fecha_llegada");
		$destino = mysql_result($rst,0,"svi_origen")."|".mysql_result($rst,0,"svi_destino");
	}else if($tipo_viaje == "Sencillo"){
		$fecha_total = mysql_result($rst,0,"fecha_salida");
		$destino = mysql_result($rst,0,"svi_destino");
	}else if($tipo_viaje == "Multidestinos"){
		$fecha_total = $aux[0]["fecha_salida"]." | ".$aux[sizeof($aux)-1]["fecha_salida"];
		$destino = "Multidestinos";
	}
    $dias_viaje = mysql_result($rst,0,"svi_dias_viaje");
    $tipoViaje = mysql_result($rst,0,"svi_tipo_viaje");
    $montoVueloTotalCotizado = mysql_result($rst,0,"svi_monto_vuelo_cotizacion");
	
    // Carga datos de autorizadores
    $tramite = new Tramite();
	$tramite->Load_Tramite($t_id);
    $ruta_autorizadores = $tramite->Get_dato("t_ruta_autorizacion");
    $autorizaciones     = $tramite->Get_dato("t_autorizaciones");

    //Traerá la ruta de autorización de la solicitud correspondiente
    $rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = $rutaAutorizacion->getNombreAutorizadores($t_id);

    //Validacion correspondiente del monto del hotel
    $div = new Divisa();
    $div->Load_data(2);
    $div_USD = $div->Get_dato('div_tasa');
    
    $div->Load_data(3);
    $div_EUR=$div->Get_dato('div_tasa');

    // Verificamos si la solicitud fue realizada por un delegado; de ser así imprimiremos el nombre de los involucrados 
    if($t_delegado != 0){
    	$duenoActual = new Usuario();
    	$duenoActual->Load_Usuario_By_ID($t_delegado);
    	$nombredelegado = $duenoActual->Get_dato('nombre');
    	$iniciador = "<font color='#0000CA'>".$nombredelegado."</font>".strtoupper(" en nombre de: ").$iniciador;
    }
    
    function setValorVacio($valor){
    	$valorInicial="";
    	if($valor == 0.00 ||$valor == 0){
    		$valorInicial="";
    	}else{
    		$valorInicial=$valor;
    	}
    	return $valorInicial;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Pragma" content="no-cache">

		<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>		
		<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/dom-drag.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
		<script language="JavaScript" src="js/functionsCalcularConceptos.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>
		<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
		<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery.datepick.js"></script>
		<script language="JavaScript" src="js/solicitud_viaje.js" type="text/javascript"></script>
		<script language="JavaScript" src="js/validarPoliticas.js" type="text/javascript"></script>
		<script language="JavaScript" src="../comprobaciones/js/backspaceGeneral.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../css/jquery.datepick.css" type="text/css" media="screen" />
		
		<link rel="stylesheet" href="../../lib/js/jquery-ui-1.10.4/development-bundle/themes/base/jquery.ui.all.css">
		<script language="JavaScript" src="../../lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js" type="text/javascript"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.core.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.widget.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.position.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.menu.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.autocomplete.js"></script>
	
		<script language="javascript" type="text/javascript">
		var j = jQuery.noConflict();
		var fechaMulti;
		var doc;
		var cc_centrocostos;
		var bandera_hotel;
		var bandera_auto;
		var banderaElimina=0;
		var banderaCancelaHotel=0;
		
			doc = $(document);
			doc.ready(inicializarEventos);
			function inicializarEventos(){

				$._GET = function(name){
					var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(top.window.location.href); 
					return (results !== null) ? results[1] : 0;
				}
				
				$(document).bind("contextmenu", function(e){ e.preventDefault(); });
				$("#ventanita_auto_hotel").draggable({containment : "#Interfaz", scroll: false});
				$("#ventanita_avion").draggable({containment : "#Interfaz", scroll: false});

				//calendario para ventanita_avion (Cotizacion)
				$("#llegada").datepick({ minDate:"<?PHP echo date("d/m/Y"); ?>"});

				$("#salida").datepick({	minDate:"<?PHP echo date("d/m/Y"); ?>"});

				//calendario para tabla_hotel
				$("#llegada2").datepick({
				minDate:"<?PHP echo date("d/m/Y"); ?>",
				onSelect:function(dates) { verificarFechas(); }
				});
				
				$("#salida2").datepick({
				minDate:"<?PHP echo date("d/m/Y"); ?>",
				onSelect:function(dates) { verificarFechas(); }
				});
				
				$("#rowCountHotelAprovado").val(parseInt($("#table_aprobado_hotel>tbody>tr").length));
				$("#rowCountAutoAprovado").val(parseInt($("#table_aprobado_auto>tbody>tr").length));
				$("#rowCount").val(parseInt($("#solicitud_table>tbody>tr").length));
				calculaTotal("0","table_aprobado_hotel","h_total_pesos","total_pesos_hotel");
				calculaTotal("0","table_aprobado_auto","svi_total_pesos_auto","total_pesos_auto");
				crear_divs_en_area_counts_div();
				bandera_hotel=0;
				bandera_auto=0;
				var estatus_en_edicion_de_itinerario = false;
				<?PHP  if($tipoUsuario == 6 || $tipoUsuario == 5){ ?>
				cc_centrocostos = <?PHP   echo $CentroCostoId; ?>;
				<?PHP  }?>
				$("#centro_de_costos_new").val(cc_centrocostos);
				//establecemos valor a la bandera
				setBanderaHotel();

				$('#llegada').keydown(function(e){					
					ignoraEventKey(e);				
				});
				
				$('#salida').keydown(function(e){
					ignoraEventKey(e);				
				});
				
				$('#llegada2').keydown(function(e){
					ignoraEventKey(e);				
				});

				$('#salida2').keydown(function(e){
					ignoraEventKey(e);				
				});

				$("#tipoHotel, #costoNoche, #iva, #selecttipodivisa").bind('keydown keyup change', function() {
					var totalHospedaje = $('#montoP').val().replace(/,/g,"");
					validarPoliticaHotelAgencia(84);
				});

				$("#tipoAuto, #diasRenta, #costoDia, #tipoDivisa").bind('keydown keyup change', function(){
				});

				$("input").bind("keydown", function(e){
					if(!isAlphaNumeric(e)) return false;
				});
				
				obtenExcepciones($._GET("id"));
				obtenExcepcionesPresupuesto($._GET("id"));
				
			}//fin ready ó inicializarEventos

			function obtenExcepciones(tramite){
				var usuario = $("#iu").val();				
				var param = "excepciones=ok&tramite="+tramite+"&usuario="+usuario;		
				var tr = "";
				var json = obtenJson(param);
				if (json == null)
					ocultarElemento("excepcion_table","hide");
				else{
					json = json.rows;
					for(var i = 1; i <= Objectlength(json); i++ ){
						for(var prop in json[i]){
							if(json[i][prop] == "" || json[i][prop]  == null ) json[i][prop] = "N/A";
						}
						tr+= '<tr><td>'+i+'</td><td>'+json[i]["itinerario"]+'</td><td>'+json[i]["concepto"]+'</td><td>'+json[i]["total"]+'</td><td>'+json[i]["mensaje"]+'</td><td>'+json[i]["politica"]+'</td><td>'+json[i]["excedente"]+'</td><td>'+json[i]["tipoExcepcion"]+'</td></tr>';			
					}
					$("#excepcion_table").append(tr);
				}
			}
			
			function obtenExcepcionesPresupuesto(tramite){
				var param = "excepcionesPresupuesto=ok&tramite="+tramite;		
				var tr = "";
				var json = obtenJson(param);
				if (json != null){
					var filas = obtenTablaLength("#excepcion_table") + 1;
					tr += '<tr><td>'+filas+'</td><td>N/A</td><td><font color="FF0000">Presupuesto</font></td><td>N/A</td><td><strong><font color="FF0000">'+json["mensaje"]+'</font></strong></td><td>N/A</td><td><font color="FF0000">'+json["excedente"]+'</font></td><td>'+json["tipoExcepcion"]+'</td></tr>';			
					$("#excepcion_table").append(tr);
				}
			}
			
			function obtenJson(param){	
				$.ajaxSetup({
					'url':			"services/ajax_solicitudes.php",
					'timeout':		5000,
					'async':		true,
					'cache':		false,
					'type':			'post',
					'dataType':		'json',			
					'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
				});
				
				var response = false;
				$.ajax({
					data: param, 
					async: false,			
					success: function(json){				
						try{
							response = json;
						}catch(e){
							alert("Ha ocurrido un error Inesperado :" + e )
						}				
					},			
					error: function(error){
						console.log(error);
						console.log(param);						
					},
					complete: function(){}
				});
				return response;
			}
			
			function Objectlength(object){
				var cont = 0;
				for(var prop in object)
					cont++;		
				return cont;
			}
			
			function ocultarElemento(elemento, efectoHide){
				var contexto = $("#"+elemento);				
				if(efectoHide == undefined)
					contexto.fadeOut("slow");		
				else
					contexto.hide();				
				resetCampos(contexto);		
			}

			function resetCampos(contexto){
				$(":input", contexto).each(function(){
					$(this).val("").removeClass("req");
				});		
				$("div", contexto).each(function(){
					$(this).text("");
				});				
			}
			//funcion que inicializara una bandera en el caso pin-pong Agencia - Empleado
			function setBanderaHotel(){
				var numHotel=parseInt($("#table_aprobado_hotel>tbody >tr").length);
				 for(var i=1;i<=numHotel;i++){
					 if($("#itinerarioHotelEA"+i).val() != undefined){    	        	
							$("#banderaHotel").val("2");
						 }
				 }    	
			}
			
		   //dibujo de tabla para el caso de (pin pong) Agencia /Empleado
		   function dibujoTablaItinerarios(){
			   var frm=document.detallesItinerarios;
			   var contadorItinerarios=0;
			   var k = 1;
			   var j = 1;
			   var numHotel=parseInt($("#table_aprobado_hotel>tbody >tr").length);
			   if(numHotel >= 1 ){
				   var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);		   		  
						for(var i=1;i<=numHotel;i++){				    
							var nuevaFila='<tr>';
							nuevaFila+="<td>"+k+"</td>";
							nuevaFila+="<td><input type='hidden' name='ciudad_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='ciudad_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#ciudadHotelEA"+i).val()+"' readonly='readonly' />"+$("#ciudadHotelEA"+i).val()+"</td>"; 
							nuevaFila+="<td><input type='hidden' name='hotel_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='hotel_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#nombreHotelEA"+i).val()+"' readonly='readonly' />"+$("#nombreHotelEA"+i).val()+"</td>";	
							nuevaFila+="<td><input type='hidden' name='comentario_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='comentario_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#comentarioHotelEA"+i).val()+"' readonly='readonly' />"+$("#comentarioHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='tipoHotel_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='tipoHotel_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#tipoHotelEA"+i).val()+"' readonly='readonly' /><input type='hidden' name='tipoHoteltxt_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='tipoHoteltxt_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#tipoHotelEA_txt"+i).val()+"' readonly='readonly' />"+$("#tipoHotelEA_txt"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='noches_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='noches_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#nochesHotelEA"+i).val()+"' readonly='readonly' />"+$("#nochesHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='llegada_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='llegada_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#llegadaHotelEA"+i).val()+"' readonly='readonly' />"+$("#llegadaHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='salida_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='salida_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#salidaHotelEA"+i).val()+"' readonly='readonly' />"+$("#salidaHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='noreservacion_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='noreservacion_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#reservacionHotelEA"+i).val()+"' readonly='readonly' />"+$("#reservacionHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='costoNoche_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='costoNoche_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#montonochesHotelEA"+i).val()+"' readonly='readonly' />"+$("#montonochesHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='iva_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='iva_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#ivaHotelEA"+i).val()+"' readonly='readonly' />"+$("#ivaHotelEA"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='total_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='total_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#h_total_pesos"+i).val()+"' readonly='readonly' />"+$("#h_total_pesos"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='selecttipodivisa_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='selecttipodivisa_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#divisaHotelEA"+i).val()+"' readonly='readonly' /><input type='hidden' name='selecttipodivisatxt_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='selecttipodivisatxt_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#divisaHotelEA_txt"+i).val()+"' readonly='readonly' />"+$("#divisaHotelEA_txt"+i).val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='montoP_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='montoP_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+$("#h_total_pesos"+i).val()+"' readonly='readonly' />"+$("#h_total_pesos"+i).val()+"</td>";
							nuevaFila+="<td><div id='delDiv_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+j+"delHotel' id='"+j+"delHotel' onmousedown='/*alert(\"hola--\")*/;borrarHotel(this.id,"+$("#itinerarioHotelEA"+i).val()+",0);/*alert(\"bye--\");*/' onClick='setBanderaElimina("+$("#itinerarioHotelEA"+i).val()+");' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
							nuevaFila+="<input type='hidden' name='subtotal_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' id='subtotal_"+$("#itinerarioHotelEA"+i).val()+"_"+j+"' value='"+frm.subtotal.value.replace(/,/g,"")+"' readonly='readonly' />";//var no_noches = parseFloat(($("#noches").val()).replace(/,/g,""));
							nuevaFila+= '</tr>';
								
							if($("#itinerarioHotelEA"+i).val() != $("#itinerarioHotelEA"+(i+1)).val()){								
								j=1;
								k=0;
							}else{								
								var j= j + 1;  
							}							
							k++;
							$("#hotel_table"+$("#itinerarioHotelEA"+i).val()).append(nuevaFila);
							           		    			
					}		      
			   }  
			}

		   //dibujo de datos para cotizacion de hotel
		   function dibujoCotizacionHotel(){
			   var total_hotel=0;
			   var redondear = 0;
			   var k = 1;
			   var no_de_hoteles = parseInt($("#table_aprobado_hotel>tbody >tr").length);
			   var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
			   for(j=1; j<=count_itinerarios; j++){//indica el numero de itinerarios		   
				 var no_de_hoteles2 = parseInt($("#hotel_table"+j+">tbody >tr").length);
				 if(no_de_hoteles2 > 0){//Encontrara valores de los hoteles que fueron cotizados
					for(i=1; i<=no_de_hoteles2; i++){			
						var nuevaFila="<tr>";
						nuevaFila+="<td align='center'><input type='hidden' id='valorBD"+k+"' name='valorBD"+k+"' value='0'/><div id='num_hotel"+k+"' name='num_hotel"+k+"'>"+k+"</div></td>";
						nuevaFila+="<td align='center'><input type='hidden' name='itinerarioHotelEA"+k+"' id='itinerarioHotelEA"+k+"' value='"+j+"' readonly='readonly' />"+j+"</td>";
						nuevaFila+="<td align='center'><input type='hidden' name='nombreHotelEA"+k+"' id='nombreHotelEA"+k+"' value='"+$("#hotel_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#hotel_"+j+"_"+i).val()+"</td>";				
						nuevaFila+="<td align='center'><input type='hidden' name='ciudadHotelEA"+k+"' id='ciudadHotelEA"+k+"' value='"+$("#ciudad_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#ciudad_"+j+"_"+i).val()+"</td>";  
						nuevaFila+="<td align='center'><input type='hidden' name='comentarioHotelEA"+k+"' id='comentarioHotelEA"+k+"' value='"+$("#comentario_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#comentario_"+j+"_"+i).val()+"</td>";
						nuevaFila+="<td align='center'><input type='hidden' name='tipoHotelEA"+k+"' id='tipoHotelEA"+k+"' value='"+$("#tipoHotel_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#tipoHoteltxt_"+j+"_"+i).val()+"</td>";
						nuevaFila+="<td align='center'><input type='hidden' name='nochesHotelEA"+k+"' id='nochesHotelEA"+k+"' value='"+$("#noches_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#noches_"+j+"_"+i).val()+"</td>"; 
						nuevaFila+="<td align='center'><input type='hidden' name='montonochesHotelEA"+k+"' id='montonochesHotelEA"+k+"' value='"+$("#costoNoche_"+j+"_"+i).val()+"' readonly='readonly' />"+number_format($("#costoNoche_"+j+"_"+i).val(),2,".",",")+"</td>";
						nuevaFila+="<td align='center'><input type='hidden' name='divisaHotelEA"+k+"' id='divisaHotelEA"+k+"' value='"+$("#selecttipodivisa_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#selecttipodivisatxt_"+j+"_"+i).val()+"</td>";  
						nuevaFila+="<td align='center'><input type='hidden' name='llegadaHotelEA"+k+"' id='llegadaHotelEA"+k+"' value='"+$("#llegada_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#llegada_"+j+"_"+i).val()+"</td>"; 
						nuevaFila+="<td align='center'><input type='hidden' name='salidaHotelEA"+k+"' id='salidaHotelEA"+k+"' value='"+$("#salida_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#salida_"+j+"_"+i).val()+"</td>"; 
						nuevaFila+="<td align='center'><input type='hidden' name='ivaHotelEA"+k+"' id='ivaHotelEA"+k+"' value='"+$("#iva_"+j+"_"+i).val()+"'/><input type='hidden' name='h_total_pesos"+k+"' id='h_total_pesos"+k+"' value='"+$("#montoP_"+j+"_"+i).val()+"'/>"+number_format($("#montoP_"+j+"_"+i).val(),2,".",",")+"</td>"; 
						nuevaFila+="<td align='center'><input type='hidden' name='reservacionHotelEA"+k+"' id='reservacionHotelEA"+k+"' value='"+$("#noreservacion_"+j+"_"+i).val()+"' readonly='readonly' />"+$("#noreservacion_"+j+"_"+i).val()+"</td>"; 
						nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+k+"elimHotel' id='"+k+"elimHotel' onmousedown='borrarRenglon(this.id,\"table_aprobado_hotel\",\"rowCountHotelAprovado\",\"rowDelHotelAprovado\",\"num_hotel\",\"\",\"elimHotel\"); calculaTotal(this.id,\"table_aprobado_hotel\",\"h_total_pesos\",\"total_pesos_hotel\"); inicializarValorHotel();' style='cursor:pointer;' /></div></td>";
						nuevaFila+= '</tr>';
						$("#table_aprobado_hotel").append(nuevaFila);
						k++;
						total_hotel = total_hotel+parseFloat($("#montoP_"+j+"_"+i).val().replace(/,/g,""));
						redondear = Math.round(total_hotel * Math.pow(10, 2)) / Math.pow(10, 2);									
					}
				 }else{	
						var valorItinerario=0;
						//excepcion para la eliminacion
						if((banderaElimina == j && no_de_hoteles2 == 0)){		  	 		
						}else{
							for(var i=1;i<=no_de_hoteles;i++){
								if($("#itinerarioHotelEA"+i).val() == j){								
									var nuevaFila="<tr>";
									nuevaFila+="<td align='center'><input type='hidden' id='valorBD"+k+"' name='valorBD"+k+"' value='0'/><div id='num_hotel"+k+"' name='num_hotel"+k+"'>"+k+"</div></td>";
									nuevaFila+="<td align='center'><input type='hidden' name='itinerarioHotelEA"+k+"' id='itinerarioHotelEA"+k+"' value='"+j+"' readonly='readonly' />"+j+"</td>";
									nuevaFila+="<td align='center'><input type='hidden' name='nombreHotelEA"+k+"' id='nombreHotelEA"+k+"' value='"+$("#nombreHotelEA"+i).val()+"' readonly='readonly' />"+$("#nombreHotelEA"+i).val()+"</td>";				
									nuevaFila+="<td align='center'><input type='hidden' name='ciudadHotelEA"+k+"' id='ciudadHotelEA"+k+"' value='"+$("#ciudadHotelEA"+i).val()+"' readonly='readonly' />"+$("#ciudadHotelEA"+i).val()+"</td>";  
									nuevaFila+="<td align='center'><input type='hidden' name='comentarioHotelEA"+k+"' id='comentarioHotelEA"+k+"' value='"+$("#comentarioHotelEA"+i).val()+"' readonly='readonly' />"+$("#comentarioHotelEA"+i).val()+"</td>";
									nuevaFila+="<td align='center'><input type='hidden' name='tipoHotelEA"+k+"' id='tipoHotelEA"+k+"' value='"+$("#tipoHotelEA"+i).val()+"' readonly='readonly' />"+$("#tipoHotelEA_txt"+i).val()+"</td>"; 
									nuevaFila+="<td align='center'><input type='hidden' name='nochesHotelEA"+k+"' id='nochesHotelEA"+k+"' value='"+$("#nochesHotelEA"+i).val()+"' readonly='readonly' />"+$("#nochesHotelEA"+i).val()+"</td>"; 
									nuevaFila+="<td align='center'><input type='hidden' name='montonochesHotelEA"+k+"' id='montonochesHotelEA"+k+"' value='"+$("#montonochesHotelEA"+i).val()+"' readonly='readonly' />"+number_format($("#montonochesHotelEA"+i).val(),2,".",",")+"</td>";
									var divisaAutoCot="";
									if(($("#divisaHotelEA"+i).val() == 1) || ($("#divisaHotelEA"+i).val() == "MXN")){
										divisaAutoCot="MXN";
									}else if(($("#divisaHotelEA"+i).val() == 2) || ($("#divisaHotelEA"+i).val() == "USD")){
										divisaAutoCot="USD";
									}else if(($("#divisaHotelEA"+i).val() == 3)||($("#divisaHotelEA"+i).val() == "EUR")){
										divisaAutoCot="EUR";
									}			
									nuevaFila+="<td align='center'><input type='hidden' name='divisaHotelEA"+k+"' id='divisaHotelEA"+k+"' value='"+$("#divisaHotelEA"+i).val()+"' readonly='readonly' />"+divisaAutoCot+"</td>";  
									nuevaFila+="<td align='center'><input type='hidden' name='llegadaHotelEA"+k+"' id='llegadaHotelEA"+k+"' value='"+$("#llegadaHotelEA"+i).val()+"' readonly='readonly' />"+$("#llegadaHotelEA"+i).val()+"</td>"; 
									nuevaFila+="<td align='center'><input type='hidden' name='salidaHotelEA"+k+"' id='salidaHotelEA"+k+"' value='"+$("#salidaHotelEA"+i).val()+"' readonly='readonly' />"+$("#salidaHotelEA"+i).val()+"</td>"; 
									nuevaFila+="<td align='center'><input type='hidden' name='ivaHotelEA"+k+"' id='ivaHotelEA"+k+"' value='"+$("#ivaHotelEA"+i).val()+"'/><input type='hidden' name='h_total_pesos"+k+"' id='h_total_pesos"+k+"' value='"+$("#h_total_pesos"+i).val()+"'/>"+number_format($("#h_total_pesos"+i).val(),2,".",",")+"</td>"; 
									nuevaFila+="<td align='center'><input type='hidden' name='reservacionHotelEA"+k+"' id='reservacionHotelEA"+k+"' value='"+$("#reservacionHotelEA"+i).val()+"' readonly='readonly' />"+$("#reservacionHotelEA"+i).val()+"</td>"; 
									nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+k+"elimHotel' id='"+k+"elimHotel' onmousedown='borrarRenglon(this.id,\"table_aprobado_hotel\",\"rowCountHotelAprovado\",\"rowDelHotelAprovado\",\"num_hotel\",\"\",\"elimHotel\"); calculaTotal(this.id,\"table_aprobado_hotel\",\"h_total_pesos\",\"total_pesos_hotel\"); inicializarValorHotel();' style='cursor:pointer;' /></div></td>";
									nuevaFila+= '</tr>';
									$("#table_aprobado_hotel").append(nuevaFila);
									k++;						
									total_hotel = total_hotel+parseFloat($("#h_total_pesos"+i).val().replace(/,/g,""));
									redondear = Math.round(total_hotel * Math.pow(10, 2)) / Math.pow(10, 2);
								}					
							}
						}							  	 
				 }
						
			   }
			   
			   //eliminacion de los datos originales		   
			   $("#total_pesos_hotel").html(number_format(redondear,2,".",","));
			   $("#TotHotel").val(number_format(redondear,2,".",","));
		   }

			//funcion que nos permitira levantar una bandera de eliminacion para el caso del (ping-pong) Agencia Empleado
			function setBanderaElimina(itinerario){
				banderaElimina=itinerario;
				//alert("valor de bandera con el itinerario "+banderaElimina);
			}	
			//funcion que permitira vambiar los valores  de las filas generadas cuando estas ya sean dibujadas (Pin-Pong Agencia-Empleado)
			function reinicioFilas(){
				var no_de_hoteles = parseInt($("#table_aprobado_hotel>tbody >tr").length);
				 for(var i=1; i<=no_de_hoteles; i++){
						 $("#valorBD"+i).val("1");
					 }				
			}

			//funcion que nos permitira identificar la cantidad de hoteles cotizados en la etapa de pin-pong Agencia Empleado (Persisitir datos)
			function setPinPongAE(){
				var no_de_hoteles2=0;		
				no_de_hoteles2 = parseInt($("#table_aprobado_hotel>tbody >tr").length);			
				$("#hotelesIngresados").val(no_de_hoteles2);
			}
			
		   //funcion que nos permitira eliminar las filas cuando ya hayan sido dibujadas
		   function eliminacionDatos(){
			   var longitud=parseInt($("#table_aprobado_hotel>tbody>tr").length);
			   var cont=0;
			   for(var i=1;i<=longitud;i++){
					//Se elimina el tr correspondiente(datos de BD)
					if($("#valorBD"+i).val() == "1"){								
						$("#table_aprobado_hotel>tbody >tr").eq(0).remove();
					}				
				} 		
		   }
		   //funcion que nos permitira eliminar los valores de los itinerarios virtuales(Para no causar datos de tipo indefinido)
		   function reinicioItinerarios(){
			 //eliminamos los datos que hay en la tabla de hoteles		
				var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length); 	  
			   for(var j=1; j<=count_itinerarios; j++){//indica el numero de itinerarios		   
				  $("#hotel_table"+j).find("tr:gt(0)").remove();	
				 var no_de_hoteles2 = parseInt($("#hotel_table"+j+">tbody >tr").length);
			   }
		   }	
		  //Seleccionar elemento del combo de cat_cecos_cargado
			function seleccionar(elemento) {
			   var combo = document.detallesItinerarios.centro_de_costos_new;
			   var cantidad = combo.length;
			   for (i = 0; i < cantidad; i++) {
				  var toks=combo[i].text.split("-");
				  if (toks[0] == elemento) {
					 combo[i].selected = true;
					 break;
				  }
			   }
			}

			<?PHP if(isset( $_GET['hist'])){?>
				function Location(){
					location.href='./historial.php';
				}
			<?PHP }elseif(isset($_GET['noti'])){?>
			//Direccionamiento a index de notificaciones
				function Location(){
					location.href='../notificaciones/index.php';
				}	
			<?PHP }else{?>
				function Location(){
					location.href='./index.php?docs=docs&type=1';
				}
			<?PHP }?>
			
			function requerimientos(){
				var frm=document.detallesItinerarios;
				
				if(frm.accion.checked == true){
					$("#resumen").slideDown(500);
					$("#resumen").css("display", "block");
					$("#resumen").addClass("visible");
				}else{
					//ocultar "resumen"
					if($("#resumen").hasClass("visible")){
						$("#resumen").slideUp(500);
						$("#resumen").removeClass("visible");
					}
				}
		   }
			
			function eliminaAvion(valor,id_input){
				if(id_input == "aceptarAvion"){
					var val1 = $("#costoAvion").val().replace(/,/g,"");
					var val2 = $("#ivaAvion").val().replace(/,/g,"");
					var val3 = $("#tuaAvion").val().replace(/,/g,"");
					var val4 = $("#ctAvion").val().replace(/,/g,"");			
					var val5 = $("#aeroAvion").val();
					var val6 = $("#tipo").val();
					var val7 = $("#salida").val();
					var val8 = $("#llegada").val();					
					var val9 = 0;
					$('#TotAvion').val(parseFloat(val4));
				}else{
					var val1 = "(NULL)";
					var val2 = "(NULL)";
					var val3 = "(NULL)";
					var val4 = "(NULL)";
					var val5 = "";
					var val6 = "(NULL)";
					var val7 = "(NULL)";
					var val8 = "(NULL)";
					var val9 = "(NULL)";
					$('#TotAvion').val(0.00);			
				}

				var cadena = "undefined";
				// Si undefined es encontrado en el valor de la variable, indicaremos que la fecha esta vacia. 
				if (cadena.indexOf(val8) != -1){
					val8 = "0000/00/00";
				}
				
				if(valor != ""){
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "id_sol_viaje="+valor+"&val1="+val1+"&val2="+val2+"&val3="+val3+"&val4="+val4+"&val5="+val5+"&val6="+val6+"&val7="+val7+"&val8="+val8+"&val9="+val9+"&idTramiteAvion="+<?PHP  echo $_GET['id'];?>,
						dataType: "json",
						async: false,
						timeout: 10000,
						success: function(json){}, 
						complete: function(json){
							if(id_input == "eliminarCotizacionAvion"){
								$("#table_avion").find("tr:gt(0)").remove();
								$("#total_pesos_avion").html("0.00");
							}else{//recargar_avion(valor);								
								$.ajax({
									type: "POST",
									url: "services/ajax_solicitudes.php",
									data: "datos_avion="+valor,
									dataType: "json",
									async: false,
									timeout: 10000,
									success: function(json){
										var i=0;
										while(datos = json[i]){
											agregar_avion_a_tabla(datos);
											i++;
											return false;
										}
									},
									complete: function(json){
										flotanteActive3(0);
									}
								});
							}
						}
					});
				}else{
					alert("El id de la solicitud es nulo.");
				}
			}
			//funcion para cargar el avion
			function agregar_avion_a_tabla(datos){
				$("#table_avion").find("tr:gt(0)").remove();
				$("#total_pesos_avion").html("0.00");
				//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
				var id = 1;		
				$("#rowDelAvion").val(id);
				
				var idSolicitud = datos['svi_solicitud'];
				var TipoViaje= datos['svi_tipo_viaje'];
				var Viaje=datos['sv_viaje'];							
				var FechaLlegada=datos['svi_fecha_regreso_avion'];		
				var Aerolinea= datos['svi_aerolinea'];
				//poner el formato de fecha
				var FechaSalida= datos['svi_fecha_salida_avion'];		 
				var MontoVuelo= parseFloat(datos['svi_monto_vuelo']);
				var Svi_Iva= parseFloat(datos['svi_iva']);
				var Svi_Tua= parseFloat(datos['svi_tua']);
				var Svi_Tipo_Aerolinea = datos['svi_tipo_aerolinea'];
				var servicioAerolinea = datos['se_nombre'];
				var MontoVueloCotizacion= datos['svi_monto_vuelo_cotizacion'];
				
				var nuevaFila='<tr>';
					nuevaFila+="<td align='center'>"+id+"</td>";
					nuevaFila+="<td align='center'>"+TipoViaje+"</td>"; 
					nuevaFila+="<td align='center'><input type='hidden' name='aeroAvionCot' id='aeroAvionCot' value='"+Aerolinea+"' readonly='readonly' />"+Aerolinea+"</td>";
					nuevaFila+="<td align='center'><input type='hidden' name='tipo_aerolinea' id='tipo_aerolinea' value='"+Svi_Tipo_Aerolinea+"' readonly='readonly' />"+servicioAerolinea+"</td>";
					nuevaFila+="<td align='center'><input type='hidden' name='salAvionCot' id='salAvionCot' value='"+FechaSalida+"' readonly='readonly' />"+FechaSalida+"</td>";
					if(Viaje == 'Redondo' || Viaje == 'Multidestinos' ){
					nuevaFila+="<td align='center'><input type='hidden' name='llAvionCot' id='llAvionCot' value='"+FechaLlegada+"' readonly='readonly' />"+FechaLlegada+"</td>";
					}				
					nuevaFila+="<td align='center'><input type='hidden' name='coAvionCot' id='coAvionCot' value='"+MontoVuelo+"' readonly='readonly' />"+number_format(MontoVuelo,2,".",",")+"</td>";  
					nuevaFila+="<td align='center'><input type='hidden' name='ivaAvionCot' id='ivaAvionCot' value='"+Svi_Iva+"' readonly='readonly' />"+number_format(Svi_Iva,2,".",",")+"</td>";
					nuevaFila+="<td align='center'><input type='hidden' name='tuaAvionCot' id='tuaAvionCot' value='"+Svi_Tua+"' readonly='readonly' />"+number_format(Svi_Tua,2,".",",")+"</td>";
					nuevaFila+="<td align='center'><input type='hidden' name='cotAvionCot' id='cotAvionCot' value='"+MontoVueloCotizacion+"' readonly='readonly' />"+number_format(MontoVueloCotizacion,2,".",",")+"</td>";
					nuevaFila+="<td align='center'><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' id='eliminarCotizacionAvion' name='eliminarCotizacionAvion' onmousedown='eliminaAvion("+idSolicitud+",this.id);' style='cursor:pointer'/></div></td>";
					nuevaFila+= '</tr>';
				$("#table_avion").append(nuevaFila).fadeIn("slow");
				//aqui me quede
				var redondear = Math.round(MontoVueloCotizacion * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				$("#total_pesos_avion").html(number_format(redondear,2,".",","));
			}
			
			//Elimina un Auto
			function eliminaAuto(valor,id_input){	  
				if(id_input == "aceptarAuto"){	
					//alert("verdadero");
					var val1 = $("#costoAvion").val();			
					var val2 = $("#ivaAvion").val();
					var val3 = $("#tuaAvion").val();
					var val4 = $("#ctAvion").val();			
					var val5 = $("#aeroAvion").val();
				}else{
					//inicializaran los valores de el auto cotizado
					id=parseInt(id_input);
					$("#empresaAuto"+id).val("");
					$("#tipoAuto"+id).val(1);
					$("#diasRenta"+id).val(0);
					$("#costoDia"+id).val(0);
					$("#totalAuto"+id).val(0.00);
					$("#montoPesos"+id).val(0.00);
					$("#tipoDivisa"+id).val(1);			
					var val1 = "";
					var val2 = "";
					var val3 = "0";
					var val4 = "0.00";
					var val5 = "0.00";
				}
				
				if(valor != ""){
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "id_auto="+valor+"&val1="+val1+"&val2="+val2+"&val3="+val3+"&val4="+val4+"&val5="+val5,
						dataType: "json",
						success: function(json){					
							$("#TotAuto").val($("#total_pesos_auto").html());					
							valor2 = parseInt(id_input);
							if(id_input == valor2+""+"elimAuto"){
							//$("#table_aprobado_auto").find("tr:gt(0)").fadeOut("normal",function(){$(this).remove();});
							//$("#total_pesos").html("0.00");
							}else{//recargar_auto(valor);
							//location.reload();
							}
						}
					});
					
				}else{
					alert("El id de la solicitud es nulo.");
				}	
			
			}
			//===========
			//Eliminar un Hotel
			function eliminaHotel(idHotel,idItinerario){
				if(idHotel != ""){
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "hotel="+idHotel+"&itinerarioHotel="+idItinerario,
						dataType: "json",
						success: function(json){
							$("#TotHotel").val($("#total_pesos_hotel").html());
							//$Total = parseInt($("#table_aprobado_hotel>tbody>tr").length));
							//i=parseInt(id);
							//alert(i);
							//$("#table_auto").find("tr:gt(0)").fadeOut("normal",function(){$(this).remove();});
							//$("#table_aprobado_hotel").find("tr:gt(0)").fadeOut("normal",function(){$(this).remove();});
							//recargar_avion(valor);
						}
					});
					//$("#total_pesos").html("0.00");
				}else{
					alert("El id de hotel es nulo.");
				}
			}
			//Calcular Total Hospedaje
			function calculaTotal(id_tr,nombre_tabla,campo_oculto,campo_resultado){				
				//Aqui se saca el monto total de hoteles
				id_tr = parseInt(id_tr);
				//alert(campo_oculto);
				//var id = parseInt($("#table_aprobado_hotel>tbody >tr").length);
				var id = parseInt($("#"+nombre_tabla+">tbody >tr").length);
				//alert(id);
				var total_hospedaje = 0;
				for(i=1; i<=id; i++){
					if(($("#"+campo_oculto+i).val() == undefined) && (campo_oculto == "svi_total_pesos_auto")){
						total_hospedaje = parseFloat(total_hospedaje) + parseFloat($('#TotAuto').val());
					}else{
					if(i != id_tr)
						total_hospedaje = parseFloat(total_hospedaje) + parseFloat(($("#"+campo_oculto+i).val()).replace(/,/g,""));
					}
				}
				var redondear = Math.round(total_hospedaje * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				$("#"+campo_resultado).html(number_format(redondear,2,".",","));							
			}
			
			 //funcion para activar la ventana flotante para la cotizacion de boletos de avion
			function flotanteActive3(valor){	
					if(valor == 1){
						document.getElementById('ventanita_avion').style.visibility = 'visible';
						document.getElementById('ventanita_avion').style.display = 'block';
					}else{
						document.getElementById('ventanita_avion').style.visibility = 'hidden';
						document.getElementById('ventanita_avion').style.display = 'none';
					}
				}
				
			/****************VALIDACIONES********************************************/
			/***************************************************************************/
			//VARIABLE GLOBAL
			var textoAnterior = '';

			//ESTA FUNCIÓN DEFINE LAS REGLAS DEL JUEGO
			function cumpleReglas(simpleTexto){
				//la pasamos por una poderosa expresión regular
				//var expresion = new RegExp("^(|([0-9]{1,2}(\\.([0-9]{1,2})?)?))$");
				var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");

				//si pasa la prueba, es válida
				if(expresion.test(simpleTexto))
					return true;
				return false;
			}//end function checaReglas

			//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
			function revisaCadena(textItem){
				//si comienza con un punto, le agregamos un cero
				if(textItem.value.substring(0,1) == '.') 
					textItem.value = '0' + textItem.value;

				//si no cumples las reglas, no te dejo escribir
				if(!cumpleReglas(textItem.value))
					textItem.value = textoAnterior;
				else //todo en orden
					textoAnterior = textItem.value;
			}//end function revisaCadena
			/***************************************************************************/
			
			//funcion que obtiene la suma del costo, TUA e IVA
			function getCostoTotal(){				
				var costo = 0;
				var iva = 0;
				var tua = 0;
				
				costo = parseFloat(($("#costoAvion").val()).replace(/,/g,""));
				iva = parseFloat(($("#ivaAvion").val()).replace(/,/g,""));
				tua = parseFloat(($("#tuaAvion").val()).replace(/,/g,""));
				if($("#ivaAvion").val() == "" && $("#tuaAvion").val() == "" ){
					 iva = 0;
					 tua = 0;					 
				}else if($("#ivaAvion").val() == "" && $("#tuaAvion").val() != "" ){
					 iva = 0;
				}else if($("#ivaAvion").val() != "" && $("#tuaAvion").val() == ""){
					 tua = 0;
				}
				var resultado= costo + iva + tua;						
				document.detallesItinerarios.ctAvion.value = number_format(resultado,2,".",",");
			}

			//funcion que valida Cambios
			function validarCambio(){
				var banderaCambio = 0;
				if(parseFloat($("#costoAvion").val()) != parseFloat($("#coAvionCot").val())){					
					banderaCambio = 1;
				}else if(parseFloat($("#ivaAvion").val()) != parseFloat($("#ivaAvionCot").val())){		
					banderaCambio = 1;
				}else if(parseFloat($("#tuaAvion").val()) != parseFloat($("#tuaAvionCot").val())){		
					banderaCambio = 1;
				}else if($("#aeroAvion").val() != $("#aeroAvionCot").val()){		
					banderaCambio = 1;
				}else if($("#tipo").val() != $("#tipo_aerolinea").val()){		
					banderaCambio = 1;	
				}else if($("#salida").val() != $("#salAvionCot").val()){		
					banderaCambio = 1;
				}else{
					<?PHP  if($tipo_viaje == "Redondo" ){?>
					if($("#llegada").val() != $("#llAvionCot").val()){
						banderaCambio = 1;
					}
					<?PHP }?>					
				}
								
				return banderaCambio;
				
			}
			
			//Funcion que toma los valores para la edicion
			function tomaValores(){				
				$("#costoAvion").val($("#coAvionCot").val());
				$("#aeroAvion").val($("#aeroAvionCot").val());
				$("#ivaAvion").val(parseFloat($("#ivaAvionCot").val()));
				$("#tipo").val($("#tipo_aerolinea").val());
				$("#tuaAvion").val(parseFloat($("#tuaAvionCot").val()));
				$("#ctAvion").val($("#cotAvionCot").val());			
				$("#salida").val($("#salAvionCot").val());
				//alert($("#salAvionCot").val());
				$("#llegada").val($("#llAvionCot").val());		
			}
			
			//funcion que permite obtener los datos para realizar una cotizacion
			function editaItinerario(){
			var indicador=parseInt($("#table_avion>tbody>tr").length);	
				if(indicador >= 1){
					<?PHP if($id_etapa != SOLICITUD_ETAPA_APROBADA){?>					
					alert("La información correspondiente a la cotización del boleto de avión ya ha sido ingresada, se desplegaran los datos para posibles modificaciones.");
					<?PHP }?>					
					tomaValores();							
					flotanteActive3(1);
				}else{
					<?PHP  if($tipo_viaje == "Redondo" ){?>
					document.getElementById("costoAvion").value = "" ;
					document.getElementById("aeroAvion").value = "" ;
					document.getElementById("ivaAvion").value = "" ;
					document.getElementById("tipo").value = "-1" ;
					document.getElementById("tuaAvion").value = "" ;			
					document.getElementById("ctAvion").value = "0.00" ;
					document.getElementById("salida").value ="<?PHP  echo date('d/m/Y'); ?>" ;
					document.getElementById("llegada").value ="<?PHP  echo date('d/m/Y'); ?>" ;
					<?PHP  }else{?>
					document.getElementById("costoAvion").value = "" ;
					document.getElementById("aeroAvion").value = "" ;
					document.getElementById("ivaAvion").value = "" ;
					document.getElementById("tipo").value = "-1" ;
					document.getElementById("tuaAvion").value = "" ;			
					document.getElementById("ctAvion").value = "0.00" ;
					document.getElementById("salida").value ="<?PHP  echo date('d/m/Y'); ?>" ;
					<?PHP  }?>
					flotanteActive3(1);
				}
			}
			
			//funcion que me permitira comparar si la fecha ingresada es mayor o menor
			function comparaFechas(fecha, fecha2){  
				var xMes=fecha.substring(3, 5);  
				var xDia=fecha.substring(0, 2);  
				var xAno=fecha.substring(6,10);  
				var yMes=fecha2.substring(3, 5);  
				var yDia=fecha2.substring(0, 2);  
				var yAno=fecha2.substring(6,10);  
				if (xAno> yAno)  
				{  
					return(true)  
				}  
				else  
				{  
				  if (xAno == yAno)  
				  {   
					if (xMes > yMes)  
					{  
						return(true)  
					}  
					else  
					{   
					  if (xMes == yMes)  
					  {  
						if (xDia> yDia)  
						  return(true);  
						else  
						  return(false);  
					  }  
					  else  
						return(false);  
					}  
				  }  
				  else  
					return(false);  
				}  
			} 
			
			//funcion que valida los datos
			function validarDatos(){	
				//alert("se validaran los datos");			
				var indicador=parseInt($("#table_avion>tbody>tr").length);
				var val7 = $("#salida").val();
				//alert(val7);		
				var val8 = $("#llegada").val();	
				//alert(val8);	
				var enunciado="Los campos (*) son obligatorios. Favor de llenar los datos faltantes.";

				//validaciones de fechas	
				<?PHP  if($tipo_viaje == "Redondo" ){?>
				if(comparaFechas(val7, val8)){//true
					alert("La fecha de regreso es menor que la de salida.");			
				}
				<?PHP  }else{?>
				//alert("Sencillo");
				if($("#salida").val()==""){
					//alert(enunciado);
					$("#salida").focus();
				}
				<?PHP  }?>
				
				if($("#costoAvion").val()=="" || $("#costoAvion").val()=="0"){
				alert(enunciado);
				$("#costoAvion").focus();
				}else if($("#ivaAvion").val()==""){
				alert(enunciado);
				$("#ivaAvion").focus();
				}else if($("#tuaAvion").val()==""){
				alert(enunciado);
				$("#tuaAvion").focus();
				}else if($("#aeroAvion").val()==""){
				alert(enunciado);
				$("#aeroAvion").focus();
				}else{
					// Validacion para poder ocultar el boton o guardar
					if($("#cotizaAvion").val() == "Cotización de boleto de avión"){
						if(indicador >= 1){
							if(validarCambio() == 0){
								flotanteActive3(0);
							}else{
								if(confirm("¿Realmente desea modificar los datos del avión?")){
									eliminaAvion(<?PHP echo $id_Solicitud;?>,"aceptarAvion");
								}else{
									// Limpiar los datos de la pantalla de cotizacion de boleto de avion
									tomaValores();
									return false;
								}
							}
						}else{
							eliminaAvion(<?PHP echo $id_Solicitud;?>,"aceptarAvion");
						}
					}else{
						// Se borrará el registro de la cotización anterior para registrar los nuevos datos. 
						actualizaDatosAvion();
					}
				}
			}
			
			//setItinerarioActualOPosible
			function setItinerarioActualOPosible(id){				
				$("#itinerarioActualOPosible").val(parseInt(id));		
			}
			
			//Para mostrar datos de la ventana_auto_hotel
			function flotanteActive(valor){
					var frm=document.detallesItinerarios;
					var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					$("#no_itinerario").html(num_posible_itinerario);
					$("#origen_label").html($("#origen").val());
					$("#destino_label").html($("#destino").val());
					
					if (valor==1){
						if (bandera_hotel==1 || bandera_hotel==2 ){
							var cadena ="<table id='hotel_table"+num_posible_itinerario+"' class='tablesorter' cellspacing='1'>";
								cadena += "<thead>"
									cadena += "<tr>"
									cadena += "<th width='5%'>No.</th>";
									cadena += "<th width='30%'>Ciudad</th>";
									cadena += "<th width='30%'>Hotel</th>";
									cadena += "<th width='30%'>Comentarios</th>";
									cadena += "<th width='30%'>Tipo</th>";
									cadena += "<th width='30%'>Noches</th>";
									cadena += "<th width='30%'>Llegada</th>";
									cadena += "<th width='30%'>Salida</th>";
									cadena += "<th width='5%'>No. de reservacion</th>";
									cadena += "<th width='20%'>Costo por noche</th>";
									cadena += "<th width='10%'>IVA</th>";
									cadena += "<th width='10%'>Total</th>";
									cadena += "<th width='10%'>Divisa</th>";
									cadena += "<th width='10%'>Monto en MXN</th>";
									cadena += "<th width='5%'>Eliminar</th>";
									cadena += "</tr>"; 
								cadena += "</thead>";
								cadena += "<tbody>";
								cadena += "</tbody>";
							cadena += "</table>";
							
							var num_childNodes_default = document.getElementById('tipo_explorador').childNodes.length;
							
							//alert(num_posible_itinerario+" - "+document.getElementById('area_tabla_div').childNodes.length);
							if(num_posible_itinerario>document.getElementById('area_tabla_div').childNodes.length){
								//Se chaca si ya existe un div para contener la tabla de hotel del itinerario actual o posible
								var nuevo_div = document.createElement("div");
								document.getElementById('area_tabla_div').appendChild(nuevo_div);
								//Se chaca si ya existe un div para contener rowCount_hotel y rowDel_hotel correspondientes del itinerario actual o posible
								var nuevo_div2 = document.createElement("div");
								document.getElementById('area_counts_div').appendChild(nuevo_div2);
							}
							document.getElementById('area_tabla_div').childNodes[parseInt(num_posible_itinerario-1+num_childNodes_default)].innerHTML = cadena;

							//Se agregan rowCount_hotel y rowDel_hotel correspondientes al numero de itinerario.
							var cadena1 = "";
							cadena1 += "<input type='hidden' id='rowCount_hotel"+num_posible_itinerario+"' name='rowCount_hotel"+num_posible_itinerario+"' value='0' readonly='readonly'/>";
							cadena1 += "<input type='hidden' id='rowDel_hotel"+num_posible_itinerario+"' name='rowDel_hotel"+num_posible_itinerario+"' value='0' readonly='readonly'/>"
							document.getElementById('area_counts_div').childNodes[parseInt(num_posible_itinerario-1+num_childNodes_default)].innerHTML = cadena1;			
							//se realizara la carga de datos
							var numHotel=parseInt($("#table_aprobado_hotel>tbody >tr").length);
							if(bandera_hotel == 2){
								dibujoTablaItinerarios();
							}					
						}else{
						}
						//Hace visible la ventanita emergente.
						document.getElementById('ventanita_auto_hotel').style.visibility = 'visible';
						document.getElementById('ventanita_auto_hotel').style.display = 'block';
						//Deshabilita botones
						//document.getElementById('devolver').disabled = true;
						//document.getElementById('rechazar').disabled = true;
						//Dejamos activa solo la tabla actual o posible
						for(var i=0;i<count_itinerarios;i++){ //Recorre las tablas
							if(document.getElementById("hotel_table"+(i+1))){
								if(num_posible_itinerario != (i+1)){
									document.getElementById("hotel_table"+(i+1)).style.display = 'none';
								}else{
									document.getElementById("hotel_table"+(i+1)).style.display = 'block';
								}
							}
						}
						//para la tabla de hotel
						if($("#hotel_agencia").val()== 1){
							document.getElementById('tabla_hotel').style.visibility = 'visible';
							document.getElementById('tabla_hotel').style.display = 'block';
						}else{
							document.getElementById('tabla_hotel').style.visibility = 'hidden';
							document.getElementById('tabla_hotel').style.display = 'none';
						}
						if($("#auto_agencia").val()== 1){
							document.getElementById('tabla_auto').style.visibility = 'visible';
							document.getElementById('tabla_auto').style.display = 'block';
							document.getElementById('aceptarAgencia').disabled = false;
							validarPoliticaAutoAgencia(10);
						}else{
							document.getElementById('tabla_auto').style.visibility = 'hidden';
							document.getElementById('tabla_auto').style.display = 'none';
						}
					}else{
						document.getElementById('ventanita_auto_hotel').style.visibility = 'hidden';
						document.getElementById('ventanita_auto_hotel').style.display = 'block';
						document.getElementById('tabla_auto').style.visibility = 'hidden';
						document.getElementById('tabla_auto').style.display = 'block';
						document.getElementById('tabla_hotel').style.visibility = 'hidden';
						document.getElementById('tabla_hotel').style.display = 'block';
					}
					// Desactivar botones
					$("#aceptarAgencia").attr("disabled", "disabled");			
				}
				
				//verificar
				function verificar_itinerario2(){
					var frm=document.detallesItinerarios;
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					var numHotel=parseInt($("#table_aprobado_hotel>tbody >tr").length);
					//alert(numHotel);
					if($("#hotel_table"+num_posible_itinerario).val() == undefined){				
						 if(numHotel != 0){
							 for(var i=1;i<=numHotel;i++){
								 //alert("valores"+$("#itinerarioHotelEA"+i).val());
								if($("#itinerarioHotelEA"+i).val() != undefined){
									 bandera_hotel=2; //edicion de cotizaciones cuando el empleado envia a agencia .
								 }else{
									 //alert("caso especial");
									 bandera_hotel=1;
								 }
							 }					
						 }else{
							 bandera_hotel=1; //1 para indicar que se agregara una nueva tabla de hoteles al itinerario posible.
						 }				
					}else{				 
						for(var i=1;i<=numHotel;i++){
							 //alert($("#itinerarioHotelEA"+i).val());
							if($("#itinerarioHotelEA"+i).val() != undefined){
								 bandera_hotel=2; //edicion de cotizaciones cuando el empleado envia a agencia .
							 }else{
								 bandera_hotel=0; //0 para indicar que solo se actualizara la tabla de hotel existente.
							 }
						 }
					}
								
					//alert(bandera_hotel);
				}
				function crear_divs_en_area_counts_div(){
					var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
					var frm=document.detallesItinerarios;
					//var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					//alert(count_itinerarios);
					for(i=1; i<=count_itinerarios; i++){
						if(i>document.getElementById('area_tabla_div').childNodes.length){
							//Se chaca si ya existe un div para contener la tabla de hotel del itinerario actual o posible
							var nuevo_div = document.createElement("div");
							document.getElementById('area_tabla_div').appendChild(nuevo_div);
							//Se chaca si ya existe un div para contener rowCount_hotel y rowDel_hotel correspondientes del itinerario actual o posible
							var nuevo_div2 = document.createElement("div");
							document.getElementById('area_counts_div').appendChild(nuevo_div2);
						}
					}	
				}
				
				function agregarHotel(){
						banderaCancelaHotel = 1;						
						var frm=document.detallesItinerarios;           
						var no_de_itinerario = $("#no_itinerario").html();
						//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
						var id = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
						if(verificar()){
							if(isNaN(id)){ 
								id=1; 
							}else{ 
								id+=parseInt(1); 
							}
							//alert("longitud de itinerarios"+$("#rowCount_hotel"+no_de_itinerario).val());
							if(bandera_hotel != 2){
								$("#rowCount_hotel"+no_de_itinerario).val(parseInt($("#rowCount_hotel"+no_de_itinerario).val())+parseInt(1));
							}
							var nuevaFila='<tr>';
							nuevaFila+="<td>"+"<div id='no_"+no_de_itinerario+"_"+id+"' name='no_"+no_de_itinerario+"_"+id+"'>"+id+"</div>"+"<input type='hidden' name='row_"+no_de_itinerario+"_"+id+"' id='row_"+no_de_itinerario+"_"+id+"' value='"+id+"' readonly='readonly' /></td>";
							nuevaFila+="<td><input type='hidden' name='ciudad_"+no_de_itinerario+"_"+id+"' id='ciudad_"+no_de_itinerario+"_"+id+"' value='"+frm.ciudad.value+"' readonly='readonly' />"+frm.ciudad.value+"</td>"; 
							nuevaFila+="<td><input type='hidden' name='hotel_"+no_de_itinerario+"_"+id+"' id='hotel_"+no_de_itinerario+"_"+id+"' value='"+frm.hotel.value+"' readonly='readonly' />"+frm.hotel.value+"</td>"; 
							nuevaFila+="<td><input type='hidden' name='comentario_"+no_de_itinerario+"_"+id+"' id='comentario_"+no_de_itinerario+"_"+id+"' value='"+frm.comentario.value+"' readonly='readonly' />"+frm.comentario.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='tipoHotel_"+no_de_itinerario+"_"+id+"' id='tipoHotel_"+no_de_itinerario+"_"+id+"' value='"+$("#tipoHotel").val()+"' readonly='readonly' /><input type='hidden' name='tipoHoteltxt_"+no_de_itinerario+"_"+id+"' id='tipoHoteltxt_"+no_de_itinerario+"_"+id+"' value='"+$("#tipoHotel option:selected").text()+"' readonly='readonly' />"+$("#tipoHotel option:selected").text()+"</td>"; 
							nuevaFila+="<td><input type='hidden' name='noches_"+no_de_itinerario+"_"+id+"' id='noches_"+no_de_itinerario+"_"+id+"' value='"+frm.noches.value+"' readonly='readonly' />"+frm.noches.value+"</td>";
							//nuevaFila+="<td><input type='hidden' name='llegada_"+no_de_itinerario+"_"+id+"' id='llegada_"+no_de_itinerario+"_"+id+"' value='"+frm.llegada.value+"' readonly='readonly' />"+frm.llegada.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='llegada_"+no_de_itinerario+"_"+id+"' id='llegada_"+no_de_itinerario+"_"+id+"' value='"+$("#llegada2").val()+"' readonly='readonly' />"+$("#llegada2").val()+"</td>";
							//nuevaFila+="<td><input type='hidden' name='salida_"+no_de_itinerario+"_"+id+"' id='salida_"+no_de_itinerario+"_"+id+"' value='"+frm.salida.value+"' readonly='readonly' />"+frm.salida.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='salida_"+no_de_itinerario+"_"+id+"' id='salida_"+no_de_itinerario+"_"+id+"' value='"+$("#salida2").val()+"' readonly='readonly' />"+$("#salida2").val()+"</td>";
							nuevaFila+="<td><input type='hidden' name='noreservacion_"+no_de_itinerario+"_"+id+"' id='noreservacion_"+no_de_itinerario+"_"+id+"' value='"+frm.noreservacion.value+"' readonly='readonly' />"+frm.noreservacion.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='costoNoche_"+no_de_itinerario+"_"+id+"' id='costoNoche_"+no_de_itinerario+"_"+id+"' value='"+frm.costoNoche.value.replace(/,/g,"")+"' readonly='readonly' />"+frm.costoNoche.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='iva_"+no_de_itinerario+"_"+id+"' id='iva_"+no_de_itinerario+"_"+id+"' value='"+frm.iva.value.replace(/,/g,"")+"' readonly='readonly' />"+frm.iva.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='total_"+no_de_itinerario+"_"+id+"' id='total_"+no_de_itinerario+"_"+id+"' value='"+frm.total.value.replace(/,/g,"")+"' readonly='readonly' />"+frm.total.value+"</td>";
							nuevaFila+="<td><input type='hidden' name='selecttipodivisa_"+no_de_itinerario+"_"+id+"' id='selecttipodivisa_"+no_de_itinerario+"_"+id+"' value='"+$("#selecttipodivisa option:selected").val()+"' readonly='readonly' /><input type='hidden' name='selecttipodivisatxt_"+no_de_itinerario+"_"+id+"' id='selecttipodivisatxt_"+no_de_itinerario+"_"+id+"' value='"+$("#selecttipodivisa option:selected").text()+"' readonly='readonly' />"+$("#selecttipodivisa option:selected").text()+"</td>";
							nuevaFila+="<td><input type='hidden' name='montoP_"+no_de_itinerario+"_"+id+"' id='montoP_"+no_de_itinerario+"_"+id+"' value='"+frm.montoP.value.replace(/,/g,"")+"' readonly='readonly' />"+frm.montoP.value+"</td>";
							nuevaFila+="<td><div id='delDiv_"+no_de_itinerario+"_"+id+"' align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name="+id+"delHotel id="+id+"delHotel onClick='validarItinerario(this.id,"+no_de_itinerario+","+$("#itinerarioActualOPosible").val()+"); habilitar();' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
							nuevaFila+="<input type='hidden' name='subtotal_"+no_de_itinerario+"_"+id+"' id='subtotal_"+no_de_itinerario+"_"+id+"' value='"+frm.subtotal.value.replace(/,/g,"")+"' readonly='readonly' />";//var no_noches = parseFloat(($("#noches").val()).replace(/,/g,""));
							nuevaFila+= '</tr>';
							
							$("#hotel_table"+no_de_itinerario).append(nuevaFila);
							$("#no_itinerario").val(parseInt(frm.rowCount.value));
							
							limpiarCamposDeHotel();
							calculaTotalHospedaje();
							document.getElementById('aceptarAgencia').disabled = false;
							$("#warning_msg").val("");
							$("#warning_msg").css("display", "none");
						}												
				}
				
				function habilitar(){
					var no_de_itinerario = $("#no_itinerario").html();
										
						if(($("#empresaAuto").val() != "") && ($("#diasRenta").val() != "" && $("#diasRenta").val() != 0)){
							 if(($("#costoDia").val() != "" || $("#costoDia").val() != 0.00)){										
								 $('#aceptarAgencia').removeAttr("disabled");
								}else{
									$("#aceptarAgencia").attr("disabled", "disabled");										 
								}
							 }else{
								 $("#aceptarAgencia").attr("disabled", "disabled");
							 }
									 							        	 
				}       
			   function verificar(){
						if($("#ciudad").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#ciudad").focus();
							return false;
						} else if($("#noches").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#noches").focus();
							return false;
						} else if($("#hotel").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#hotel").focus();
							return false;
						}else if($("#costoNoche").val()=="" || $("#costoNoche").val()=="0.00"){
							if($("#costoNoche").val()=="0.00"){
								alert('El campo "Costo Noche" no puede tener un valor 0.00, ingrese un valor correcto.' );
							}else{
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							}
							$("#costoNoche").focus();
							return false;
						}else if($("#selecttipodivisa").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#selecttipodivisa").focus();
							return false;
						}else if($("#tipoHotel option:selected").val() == -1){
		                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		                    $("#tipoHotel").focus();
		                    return false;
						}else if($("#llegada2").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#llegada2").focus();
							return false;
						}else if($("#subtotal").val()=="" || $("#subtotal").val()=="0.00" ){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#subtotal").focus();
							return false;
						}else if($("#salida2").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#salida2").focus();
							return false;
						}else if($("#iva").val()==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#iva").focus();
							return false;
						}else if($("#comentario").val()==""){
							if($('#excedeMontoHospedaje').val() == 1){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#comentario").focus();
								return false;
							}else{
								return true;
							}							
						}else{
							return true;
						}
										
				}
				
				function limpiarCamposDeHotel(){
					$("#ciudad").val("");
					$("#hotel").val("");
					$("#llegada2").val("");
					$("#noches").val("0");
					$("#costoNoche").val("");
					$("#subtotal").val("0.00");
					$("#salida2").val("");
					$("#iva").val("");
					$("#noreservacion").val("");
					$("#total").val("0.00");
					$("#montoP").val("0.00");
					$("#comentario").val("");
					$("#selecttipodivisa").val("1");
					$("#tipoHotel").val(-1);
				}
				
				function calculaTotalHospedaje(){
					//Aqui se saca el monto total de hoteles
					var no_de_itinerario = $("#no_itinerario").html();					
					var id = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);					
					var total_hospedaje = 0;
					var total_noches = 0;
					for(i=1; i<=id; i++){
						total_hospedaje = parseFloat(total_hospedaje) + parseFloat(($("#montoP_"+no_de_itinerario+"_"+i).val()).replace(/,/g,""));
						total_noches = parseInt(total_noches) + parseInt(($("#noches_"+no_de_itinerario+"_"+i).val()));
					}
					var redondear = Math.round(total_hospedaje * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
					$("#TotalHospedaje").html(number_format(redondear,2,".",","));
					$("#total_noches").val(total_noches);
				}
				
				//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
				function revisaCadena(textItem){
					//si comienza con un punto, le agregamos un cero
					if(textItem.value.substring(0,1) == '.') 
						textItem.value = '0' + textItem.value;

					//si no cumples las reglas, no te dejo escribir
					if(!cumpleReglas(textItem.value))
						textItem.value = textoAnterior;
					else //todo en orden
						textoAnterior = textItem.value;
				}//end function revisaCadena

				function formato_decimal(campo, valor){
					$("#"+campo).val(number_format(valor,2,".",","));
				}
				 
				function getSubtotal(){
						var no_noches = parseFloat(($("#noches").val()).replace(/,/g,""));
						var co_noches = parseFloat(($("#costoNoche").val()).replace(/,/g,""));
						var resultado= no_noches*co_noches;
						document.detallesItinerarios.subtotal.value = number_format(resultado,2,".",",");

						var subtot = parseFloat(($("#subtotal").val()).replace(/,/g,""));
						var total_iva = parseFloat(($("#iva").val()).replace(/,/g,""));
						if(isNaN(total_iva)){
							total_iva=0;
						}
						
						var resultado2 = subtot+total_iva;
						document.detallesItinerarios.total.value = number_format(resultado2,2,".",",");
						
						var total = parseFloat(($("#total").val()).replace(/,/g,""));
						var divisas = $("#selecttipodivisa").val();

						var tasaNueva = 1;
						if(divisas != 1){ //Si la divisa es diferente a MXN
							//Se obtiene las tasas de las divisas
							var tasa = "<?PHP 
							$query = sprintf('SELECT DIV_ID,DIV_TASA FROM divisa');
							$var = mysql_query($query);
							$aux="";
							while ($arr = mysql_fetch_assoc($var)) {
								$aux.=$arr['DIV_ID'].":".$arr['DIV_TASA'].":";
							}
							echo $aux;?>";
							var tasa2 = tasa.split(":");
							
							//Se obtiene la tasa de la divisa seleccionada
							for(i=0;i<=tasa2.length;i=i+2){
								if(tasa2[i] == divisas){
									tasaNueva = tasa2[i+1];
								}
							}
						}

						var redondear = 0;
						var totalTotal = total * parseFloat(tasaNueva);
						var redondear = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
						document.detallesItinerarios.montoP.value = number_format(redondear,2,".",",");
						//verificaMontoCotizadoAgencia(redondear,$("#itinerarioActualOPosible").val());
					}
				
					function obtiene_fecha() {   
						var fecha_actual = new Date()  
						var dia = fecha_actual.getDate()  
						var mes = fecha_actual.getMonth() + 1  
						var ano = fecha_actual.getFullYear()  
					  
						if (mes < 10)  
							mes = '0' + mes  
						if (dia < 10)  
							dia = '0' + dia  
						return (dia + "/" + mes + "/" + ano);
					}  
					function agregar_datos_a_tabla_cotizacion_agencia(id){
						id = parseInt(id);
						$("#origen").val($("#origen"+id).val());
						$("#destino").val($("#destino"+id).val());
					}
					
					function cancelar_cotizacion_hospedaje(){
						if(bandera_hotel == 2){
							var no_de_itinerario = $("#no_itinerario").html();
							var longitud=parseInt($("#table_aprobado_hotel>tbody>tr").length);
							for(var j=1;j<=longitud;j++){
								if($("#itinerarioHotelEA"+j).val()== no_de_itinerario){	
									$("#hotel_table"+no_de_itinerario).find("tr:gt(0)").remove();					
									borrarCancelar($("#num_hotel"+j).html(),"table_aprobado_hotel","rowCountHotelAprovado","rowDelHotelAprovado","num_hotel","","elimHotel");
								}						 
							 }																					 							
						}else{
							var frm=document.detallesItinerarios;
							//document.getElementById('enviar_hosp_agencia').checked = true;
							var no_de_itinerario = $("#no_itinerario").html();
							var no_de_hoteles = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
							var id_itinerario = $("#no_itinerario").html();
							for(i=1; i<=no_de_hoteles; i++){
								$("#hotel_table"+no_de_itinerario).find("tr:gt(0)").remove(); 
							}
							//Eliminar el div y la tabla hotel correspondiente al itinerario que se iba a cotizar
							if(no_de_itinerario==document.getElementById('area_tabla_div').childNodes.length){
								if(!estatus_en_edicion_de_itinerario){
									document.getElementById('area_tabla_div').removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)]);
								}else{
									document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
								}
							}else if(no_de_itinerario<document.getElementById('area_tabla_div').childNodes.length){
									document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
							}
							//Eliminar su correspondiente rowCount_Hotel y su rowDel_Hotel
							//alert("cancelar "+no_de_itinerario+" - "+document.getElementById('area_counts_div').childNodes.length)
							if(no_de_itinerario==document.getElementById('area_counts_div').childNodes.length){
								if(!estatus_en_edicion_de_itinerario){
									document.getElementById('area_counts_div').removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)]);
								}else{
									document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[1]);
									document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
								}
							}else if(no_de_itinerario<document.getElementById('area_counts_div').childNodes.length){
									document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[1]);
									document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
							}
							
							cargar_hoteles_itinerarios();					
						}			
						//Limpiar los campos del hotel
						limpiarCamposDeHotel();
						/*frm.hospedaje[0].checked = false;
						frm.hospedaje[1].checked = true;
						frm.enviar_hosp_agencia[0].checked = false;
						frm.enviar_hosp_agencia[1].checked = false;
						$("#enviar_agencia_hotel_input").css("display", "none");
						$("#enviar_agencia_hotel").css("display", "none");*/
					}
					
					function getTotal_Auto(){
						habilitar();
						var dias_renta = parseFloat(($("#diasRenta").val()).replace(/,/g,""));
						var costo_dia = parseFloat(($("#costoDia").val()).replace(/,/g,""));
						var resultado= dias_renta*costo_dia;
						document.detallesItinerarios.totalAuto.value = number_format(resultado,2,".",",");
						var divisaAuto = $("#tipoDivisa").val();

						var tasaNueva = 1;
						if(divisaAuto != 1){ //Si la divisa es diferente a MXN
							//Se obtiene las tasas de las divisas
							var tasa = "<?PHP 
							$query = sprintf('SELECT DIV_ID,DIV_TASA FROM divisa');
							$var = mysql_query($query);
							$aux="";
							while ($arr = mysql_fetch_assoc($var)) {
								$aux.=$arr['DIV_ID'].":".$arr['DIV_TASA'].":";
							}
							echo $aux;?>";
							var tasa2 = tasa.split(":");
							
							//Se obtiene la tasa de la divisa seleccionada
							for(i=0;i<=tasa2.length;i=i+2){
								if(tasa2[i] == divisaAuto){
									tasaNueva = tasa2[i+1];
								}
							}
						}

						var redondear = 0;
						var totalTotal = resultado * parseFloat(tasaNueva);
						var redondear = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
						document.detallesItinerarios.montoPesos.value = number_format(redondear,2,".",",");
					}
					
					function verifica_auto(){
						if($("#auto_agencia").val() == 1){
							if(($("#empresaAuto").val()!="" || $("#empresaAuto").val()!=0) ){
								if($("#diasRenta").val()=="" || $("#costoDia").val()==""){
									alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
									return false;
								}
								flotanteActive(0);
							}else{
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							}
						}else{
							flotanteActive(0);
						}
					}
					
				//verificar que cotizara la agencia auto o hotel
				function verificar_auto_hotel(){
					var frm=document.detallesItinerarios;
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					$("#hotel_agencia").val($("#hotel_agencia"+num_posible_itinerario).val());
					$("#auto_agencia").val($("#auto_agencia"+num_posible_itinerario).val());
				}
				
				function verificar_itinerario(){
						var frm=document.detallesItinerarios; 
						var num_posible_itinerario = frm.itinerarioActualOPosible.value;
						var count_auto = parseInt($("#solicitud_table>tbody >tr").length);
						if(num_posible_itinerario > count_auto){
							bandera_auto=1; //1 para indicar que se agregara un nuevo registro en la tabla de cotizacion de auto.
						}else if(num_posible_itinerario <= count_auto){
							$("#empresaAuto").val($("#empresaAuto"+num_posible_itinerario).val());
							
							if($("#costoDia"+num_posible_itinerario).val() == ""){
								$("#costoDia").val("");
							}else{
								$("#costoDia").val(parseFloat($("#costoDia"+num_posible_itinerario).val().replace(/,/g,"")));
							}
							
							$("#tipoDivisa").val($("#tipoDivisa"+num_posible_itinerario).val());
							$("#tipoAuto").val($("#tipoAuto"+num_posible_itinerario).val());
							$("#totalAuto").val(number_format($("#totalAuto"+num_posible_itinerario).val(),2,".",","));
							$("#diasRenta").val($("#diasRenta"+num_posible_itinerario).val());
							$("#montoPesos").val(number_format($("#montoPesos"+num_posible_itinerario).val(),2,".",","));
							bandera_auto = 0; //0 para indicar que solo se actualizara un registro en la tabla de cotizacion de auto.
						}	
				}
				
				function agregarAuto(){
					var frm=document.detallesItinerarios;           
					//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					if(bandera_auto == 1){
						if(($("#empresaAuto").val()!="" || $("#empresaAuto").val()!=0) ){
							if($("#diasRenta").val()=="" || $("#costoDia").val()==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								return false;
							}														
						}else{
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						}						
					}else if(bandera_auto == 0){
						if($("#empresaAuto").val()!="" || $("#empresaAuto").val()!=0){
							if($("#diasRenta").val()=="" || $("#costoDia").val()==""){
								//$("#diasRenta").val(0);
								//$("#costoDia").val(0);
							}
							$("#empresaAuto"+num_posible_itinerario).val($("#empresaAuto").val());
							$("#costoDia"+num_posible_itinerario).val($("#costoDia").val());
							$("#tipoDivisa"+num_posible_itinerario).val($("#tipoDivisa").val());
							$("#tipoAuto"+num_posible_itinerario).val($("#tipoAuto option:selected").val());
							$("#tipoAutoTxt"+num_posible_itinerario).val($("#tipoAuto option:selected").text());
							$("#totalAuto"+num_posible_itinerario).val(parseFloat($("#totalAuto").val().replace(/,/g,"")));
							$("#diasRenta"+num_posible_itinerario).val($("#diasRenta").val());
							$("#montoPesos"+num_posible_itinerario).val(parseFloat($("#montoPesos").val().replace(/,/g,"")));
							//flotanteActive2(0);
						}else{
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						}						
					}
				}
				
				function agregar_registro_tabla_auto(){
					var frm=document.detallesItinerarios;           
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
					var total_auto=0.00;
					var redondear = 0;
					var tipoAuto="";
					var no_de_auto = parseInt($("#table_aprobado_auto>tbody >tr").length);
					for(i=1; i<=no_de_auto; i++){
						$("#table_aprobado_auto").find("tr:gt(0)").remove(); 
					}
					var i = 1;
					for(j=1; j<=count_itinerarios; j++){
						if($("#auto_agencia"+j).val() == 1){
							if($("#empresaAuto"+j).val() != "" ){
								var nuevaFila='<tr>';
									nuevaFila+="<td align='center'>"+i+"</td>";
									nuevaFila+="<td align='center'>"+j+"</td>";
									nuevaFila+="<td align='center'>"+$("#empresaAuto"+j).val()+"</td>";
									nuevaFila+="<td align='center'>"+$("#tipoAutoTxt"+j).val()+"</td>";  
									nuevaFila+="<td align='center'>"+$("#diasRenta"+j).val()+"</td>"; 
									nuevaFila+="<td align='center'>"+number_format(($("#costoDia"+j).val().replace(/,/g,"")),2,".",",")+"</td>";
									//validacion para el tipo de divisa
									var divisaAutoCot="";
									if($("#tipoDivisa"+j).val() == 1){
										divisaAutoCot="MXN";
									}else if($("#tipoDivisa"+j).val() == 2){
										divisaAutoCot="USD";
									}else if($("#tipoDivisa"+j).val() == 3){
										divisaAutoCot="EUR";
									}																		
									nuevaFila+="<td align='center'>"+divisaAutoCot+"</td>"; 
									//nuevaFila+="<td>"+$("#totalAuto"+j).val()+"</td>";									
									nuevaFila+="<td align='center'>"+number_format($("#montoPesos"+j).val(),2,".",",")+"</td>"; 
									nuevaFila+="<td align='center'><div align='center'><img src='../../images/delete.gif' alt='Click aqu&iacute; para eliminar Cotizaci&oacute;n' name='"+j+"del'  id='"+j+"del' onclick='eliminar_cotizacion_auto(this.id);' style='cursor:pointer' /></div></td>";
									nuevaFila+= '</tr>';
									$("#table_aprobado_auto").append(nuevaFila);
									i++;
									total_auto = total_auto+parseFloat($("#montoPesos"+j).val().replace(/,/g,""));
									redondear = Math.round(total_auto * Math.pow(10, 2)) / Math.pow(10, 2);
							}
						}
					}
					//redondea a 2 decimales
					$("#total_pesos_auto").html(number_format(redondear,2,".",","));
					$("#TotAuto").val(number_format(redondear,2,".",","));
				}
				
				function eliminar_cotizacion_auto(id){
					id = parseInt(id);
					$("#empresaAuto"+id).val("");
					$("#tipoAuto"+id).val(1);
					$("#diasRenta"+id).val(0);
					$("#costoDia"+id).val(0);
					$("#totalAuto"+id).val(0.00);
					$("#montoPesos"+id).val(0.00);
					$("#tipoDivisa"+id).val(1);
					//realizamos la eliminacion de el auto en la BD
					var val1 = "";
					var val2 = "";
					var val3 = "0";
					var val4 = "0.00";
					var val5 = "0.00";	
				
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "id_auto="+<?PHP  echo $idItinerario;?>+"&val1="+val1+"&val2="+val2+"&val3="+val3+"&val4="+val4+"&val5="+val5,
						dataType: "json",
						success: function(json){
							//$("#TotAuto").val('0.00');									
						}
					});
					
					agregar_registro_tabla_auto();		
					//calculaTotal("0","table_aprobado_auto","montoPesos","total_pesos_auto");		
				}
				
				function cargar_hoteles_itinerarios(){
					var frm=document.detallesItinerarios;
					var redondear =0;
					var total_hotel =0.00;
					var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					var no_de_hoteles = parseInt($("#table_aprobado_hotel>tbody >tr").length);
					if(bandera_hotel == 2){
						//alert("dibujocotbd");
						dibujoCotizacionHotel();
						eliminacionDatos();
						reinicioFilas();
					}else{
						for(i=1; i<=no_de_hoteles; i++){
							$("#table_aprobado_hotel").find("tr:gt(0)").remove(); 
						}
						//alert(count_itinerarios);
						var k = 1;
						for(j=1; j<=count_itinerarios; j++){
							//alert(j);
							if($("#hotelAgencia"+j).val() == 1){
								if($("#hotel_table"+j).val() != undefined){
									var no_de_hoteles2 = parseInt($("#hotel_table"+j+">tbody >tr").length);
									//alert("entra "+no_de_hoteles2);
									for(i=1; i<=no_de_hoteles2; i++){
										//alert("de i: "+i);
										var nuevaFila='<tr>';
										nuevaFila+="<td align='center'><div id='num_hotel"+k+"' name='num_hotel"+k+"'>"+k+"</div></td>";
										nuevaFila+="<td align='center'>"+j+"</td>";
										nuevaFila+="<td align='center'>"+$("#hotel_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+$("#ciudad_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+$("#comentario_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+$("#tipoHoteltxt_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+$("#noches_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+number_format($("#costoNoche_"+j+"_"+i).val(),2,".",",")+"</td>";
										nuevaFila+="<td align='center'>"+$("#selecttipodivisa_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+$("#llegada_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+$("#salida_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td align='center'>"+number_format($("#montoP_"+j+"_"+i).val(),2,".",",")+"</td>";
										nuevaFila+="<td align='center'>"+$("#noreservacion_"+j+"_"+i).val()+"</td>";
										nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+i+"del' id='"+i+"del' onmousedown='borrarHotel(this.id,"+j+",1);' style='cursor:pointer;' /></div></td>";
										nuevaFila+= '</tr>';
										$("#table_aprobado_hotel").append(nuevaFila);
										k++;
										total_hotel = total_hotel+parseFloat($("#montoP_"+j+"_"+i).val().replace(/,/g,""));
										redondear = Math.round(total_hotel * Math.pow(10, 2)) / Math.pow(10, 2);
									}
								}
							}
						}						
						$("#total_pesos_hotel").html(number_format(redondear,2,".",","));
						$("#TotHotel").val(number_format(redondear,2,".",","));						
					}
				}
				
				function inicializarValorHotel(){
					//se elimna cualquier valor del hotel el cual se elimino			
					$("#TotHotel").val('0.00');
				}
				
				function cancelar_cotizacion_auto_cancelar(){
					var frm=document.detallesItinerarios;
					var num_posible_itinerario = frm.itinerarioActualOPosible.value;
					$("#empresaAuto"+num_posible_itinerario).val("");
					$("#tipoAuto"+num_posible_itinerario).val(1);
					$("#diasRenta"+num_posible_itinerario).val(0);
					$("#costoDia"+num_posible_itinerario).val(0);
					$("#totalAuto"+num_posible_itinerario).val(0.00);
					$("#montoPesos"+num_posible_itinerario).val(0.00);
					$("#tipoDivisa"+num_posible_itinerario).val(1);
					agregar_registro_tabla_auto();
				}
				function mensaje(){
					var no_de_hoteles = parseInt($("#table_aprobado_hotel>tbody >tr").length);           
					var no_de_itinerario = $("#itinerarioActualOPosible").val();
					var id = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
					if($("#auto_agencia").val() == 1){
						if(($("#empresaAuto").val() != "")){
							if(bandera_hotel == 2){
								alert("La informaci\u00f3n correspondiente al hotel y/o renta de auto ser\u00e1  desplegada para posibles modificaciones.");
							}else{
								alert("La informaci\u00f3n correspondiente al hotel y/o renta de auto ya ha sido ingresada.Se desplegaran los datos para posibles modificaciones");
							}					
							return false;
						}
					}
					if($("#hotel_agencia").val() == 1){
						if(id >= 1 ){
							if(bandera_hotel == 2){
								alert("La informaci\u00f3n correspondiente al hotel y/o renta de auto ser\u00e1  desplegada para posibles modificaciones.");
							}else{
								alert("La informaci\u00f3n correspondiente al hotel y/o renta de auto ya ha sido ingresada.Se desplegaran los datos para posibles modificaciones");
							}
							
						}
					}	
				}

				function validaMontoTolerancia(valor){
					// Cambiamos el formato de la cantidad total que hemos obtenido
					var montoNuevoTotal = $("#ctAvion").val().replace(',','');					
					montoNuevoTotal = parseFloat(montoNuevoTotal);

					var montoVueloTotalCotizado = $("#montoVueloTotalCotizado").val().replace(',','');					
					montoVueloTotalCotizado = parseFloat(montoVueloTotalCotizado);
					
					// Se excedió el monto
					var excedio = $("#montoExcede").val();
										
					var indicador = "";
					var diferencia = "";
					var limite = "";
					
					//alert("Empieza validacion");
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "tramiteAvion="+<?PHP echo $_GET['id'];?>+"&monto_vuelo="+montoNuevoTotal,
						dataType: "json",
						async : false,
						timeout: 10000,
						success: function(json){
							indicador = json[0].indicador;
							diferencia = json[0].diferenciaMontos;
							limite = json[0].monto_limite; // Límite establecido por BMW $500, constante definida bajo el nombre de MONTO_EXCEDENTE_VUELO
						},
						complete: function(json){
							if(indicador == 1 && diferencia > limite){
								// Indicamos que es necesario ingresar una observación
								$("#observ").html("Comentarios<span class='style1'>*</span>:");
								$("#montoExcede").val(1);

								if(valor == "verificando"){
									alert("El monto ingresado excede el límite de tolerancia de variación, la solicitud se enviará nuevamente a aprobación.");
								}
								
								if(excedio == 1 && valor == "realizado"){
									// Generara la segunda ruta de aprovacion (G.A | C | F ) y reailzara el envio de la notificacion correspondiente 
									$.ajax({
										type: "POST",
										url: "services/ajax_solicitudes.php",
										data: "tramiteCId="+<?PHP  echo $_GET['id'];?>+"&diferencia="+diferencia,
										async : false,
										success: function(json2){},
										complete: function(json2){											
											if(valor == "realizado"){
												eliminaAvion(<?PHP  echo $id_Solicitud;?>,"aceptarAvion");
												location.href='../notificaciones/index.php?montoexcedido';
											}
										}
									});
								}
							}else{
								$("#observ").html("Comentarios:");
								$("#montoExcede").val(0);

								if(excedio == 0 && valor == "realizado"){
									// Como el monto no excedio se realizara la actualizacion de los datos al igual que la etapa									
									$.ajax({
										type: "POST",
										url: "services/ajax_solicitudes.php",
										data: "tramiteId="+<?PHP  echo $_GET['id'];?>+"&montoAvion="+montoNuevoTotal,
										async : false,
										success: function(json3){},
										complete: function(json3){											
											if(valor == "realizado"){
												eliminaAvion(<?PHP  echo $id_Solicitud;?>,"aceptarAvion");
												location.href='../notificaciones/index.php?confirmacionCompra';
											}
										}
									});
								}
							}
						}
					});
				}

				function cotizaAvionRow(){
					//ingresara los valores para las cotizaciones realizadas del empleado ( Auto y hotel )
					cotizaAutoHotel();			
					//checara si la cotizacion del avion ha sido realizada
					var numAvion = parseInt($("#table_avion>tbody >tr").length);			
					$("#rowDelAvion").val(numAvion);
					//checaremos las otras cotizaciones ( Hotel y Auto)
					var numHotel=parseInt($("#table_aprobado_hotel>tbody >tr").length);					
					$("#rowDelHotel").val(numHotel);			
					var numAuto=parseInt($("#table_aprobado_auto>tbody >tr").length);							
					$("#rowDelAuto").val(numAuto);
					//ingresaremos el numero total de hoteles cotizados cuando es el pin-pong Agencia (cuando la bandera_hotel =2)				
					if($("#banderaHotel").val() == "2"){
						//alert("bandera");
						setPinPongAE();
					}							
				}

				function limpiaFechaL(){
					verificarFechas();
					calculoNoches();				
					if($("#llegada2").val() == ""){
						$("#noches").val("");
					}
				}

				function verificarFechas(){			
					var fechaS=$("#salida2").val(); 
					var fechaL=$("#llegada2").val();	

					if(fechaL == "" ){	
						alert("Seleccione una fecha de llegada.");	
						$("#noches").val("");
						$("#salida2").val("");
					}else if(fechaS == ""){
						$("#noches").val("");
					}else if(comparaFechas(fechaL,fechaS)){//true si mayor
						alert("La fecha de salida es menor a la fecha de llegada. Favor de llenar los datos correctamente.");
						$("#salida2").val(""); 
						$("#llegada2").val("");		
						$("#noches").val("");		
					}else if(fechaS == fechaL ){ //son fechas iguales						
						alert("Las fechas seleccionadas son iguales. Favor de llenar los datos correctamente.");
						$("#salida2").val(""); 
						$("#llegada2").val("");		
						$("#noches").val("");
					}else{ //realizara el calculo de noches					
						calculoNoches();		
					}			
					
				}
				
				function calculoNoches(){
					var diferencia=0;					
					var llegada= $("#llegada2").val();
					var salida= $("#salida2").val();

					if($("#salida2").val() == ""){
						$("#noches").val("");
					}else{
						var dias = days_between(llegada, salida);
						$("#noches").val(dias);
					}
				}
				
			//Funcion que permitira validar las observaciones cuando agencia realiza la confirmacion de compra para el boleto de avion
			function validarObservaciones(tramite,usuario){
				// Se excedió el monto
				var excedio = $("#montoExcede").val();
				
				// Deshabilitamos los botones con el fin de evitar que el usuario de doble click a algún botón
				$("#compraAvion").attr("disabled", "disabled");
				$("#Volver").attr("disabled", "disabled");
				$("#confCompra").attr("disabled", "disabled");
				$("#cancelarCambios").attr("disabled", "disabled");
				
				if($("#observ_to_emple").val() == ""){
					alert("Para poder confirmar su compra. Favor de llenar el campo de observaciones adecuadamente.");
					$("#observ_to_emple").focus();

					$("#compraAvion").removeAttr("disabled");
					$("#Volver").removeAttr("disabled");
					$("#confCompra").removeAttr("disabled");
					$("#cancelarCambios").removeAttr("disabled");
					return false;
				}else{
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "observacionesCompra="+$("#observ_to_emple").val()+"&tramiteCompra="+tramite+"&usuarioCompra="+usuario+"&excede="+excedio,
						dataType: "json",
						timeout: 15000,
						success: function(json){},
						complete: function(){
							validaMontoTolerancia("realizado");	
						},
						error: function(x, t, m) {
							if(t==="timeout") {
								location.reload();
								abort();
							}
						}
					});
				}
			}
			////////////////////////////////////////////
			function imprimir_pdf(id_tramite){
				window.open("reporte_solicitud.php?id="+id_tramite,"imprimir")
			}

			function validaZeroIzquierda(monto,campo){		
				if(monto == 0 || monto == "" || monto == "NaN"){
					$("#"+campo).val(0);
				}else if( monto.substring(monto.length-1,monto.length) === "." || monto.substring(monto.length-2,monto.length) === ".0"){
					$("#"+campo).val(monto);
				}else{
					$("#"+campo).val(parseFloat(monto));
				}
			}

			//funcion que nos permitira obtener las cotizaciones de auto y hotel realizadas por el usuario
			function cotizaAutoHotel(){
				var CotHotel=0;
				var CotAuto=0;
				var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
				for(var i=1;i<=count_itinerarios;i++){
					CotHotel = CotHotel + parseFloat($('#total_h_pesos'+i).val().replace(/,/g,""));
					CotAuto = CotAuto + parseFloat($('#total_A_pesos'+i).val().replace(/,/g,""));		
				}
				$('#rowDelAutoEmpleado').val(CotAuto);
				$('#rowDelHotelEmpleado').val(CotHotel);
				
			}

			function calcelaActualizacion(valor){
				if(confirm("AVISO: Los datos modificados serán eliminados, \n¿Desea continuar?")){
					$.blockUI({
						message: '<h1>Espere un momento...</h1>',
						css:{
						border: 'none', 
						padding: '15px', 
						backgroundColor: '#000', 
						'-webkit-border-radius': '10px', 
						'-moz-border-radius': '10px', 
						opacity: .5, 
						color: '#fff'
						}
					});
					
					$.ajax({
						type: "POST",
						url: "services/ajax_solicitudes.php",
						data: "datos_avion="+valor,
						dataType: "json",
						async: false,
						timeout: 10000,
						success: function(json){
							// Ocultamos los botones de Confirmar Compra y Cancelar
							document.getElementById('confCompra').style.visibility = 'hidden';
							document.getElementById('cancelarCambios').style.visibility = 'hidden';
							
							var i=0;
							
							// Eliminamos el registro de la cotización anterior 
							$("#table_avion").find("tr:gt(0)").remove();
							$("#total_pesos_avion").html("0.00");

							// Insertamos el resgistro anterior
							while(datos = json[i]){
								agregar_avion_a_tabla(datos);
								i++;
								return false;
							}
						},
						complete: function(){
							$("#observ").html("Comentarios:");
							$("#montoExcede").val(0);
							$.unblockUI();
						},
						error: function(x, t, m) {
							if(t==="timeout") {
								location.reload();
								abort();
							}
						}
					});
				}else{
					return false;
				}
			}
j(document).ready(function() {
	j(".editaHotel").click(function(){
		var reg = $(this).attr('rgn');
				//j("#hotelDiv").html('');
				//j("#hotel").hide();		
			if(reg == "Nacional"){
					j(function() {
						j( "#ciudad" ).autocomplete({
							source: "services/carga_hoteles.php",
							 select: function( event, ui ) {
								hoteles(ui.item.label);
							}
						});
					});

					function hoteles(label){
					datos="label="+label;
						var jqxhr = j.getJSON( "services/carga_hoteles.php",datos, function(data) {
						console.log( "success" );
						})
						.done(function() {
						console.log( "second success" );
						})
						.fail(function() {
						console.log( "error" );
						})
						.always(function(data) {
						var option = "";
						option += '<select name="hotel1" type="text" id="hotel1">';
							option+='<option precio="0" value="0" selected=selected>Selecciona</option>';
							for(prop in data){
								var nombreHotel = data[prop].nombre;
								var costoHotel = data[prop].costo;
								option+='<option precio="'+costoHotel+'" value="'+nombreHotel+","+costoHotel+'">'+nombreHotel+",  "+costoHotel+'</option>';
							}
						option += '</select>';
						j("#hotelDiv").html(option);
						});
						jqxhr.complete(function() {
						});
					}
					j( "body" ).on( "change", "#hotel1", function() {
						var result = j("#hotel1").val().split(',');
						price = result[1];
						ht = result[0];
						j("#costoNoche").val(price);
						j("#hotel").val(ht);
					});
			}else{
				$("#hotel").show();
				$("#hotel1").hide();
				$("#hotel1").val(0);
				$("#ciudad").removeClass("ui-autocomplete-input");
					if ($("#ciudad").hasClass("ui-autocomplete-input")) {
						$("#ciudad").autocomplete("destroy");
					}
			}
	});
});		
		</script>

		<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
		<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
		<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
		<link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
		<style> .style1 {color: #FF0000} .alignRight{text-align:right}  .alignLeft{text-align:left}</style>
		<META HTTP-EQUIV="Cache-Control" CONTENT ="no-cache">
		<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
		<META HTTP-EQUIV="expires" CONTENT="0">
	</head>
	<body>	
	<div>
	<center> 
		<form name="detallesItinerarios" id="detallesItinerarios" action="solicitud_view.php" method="post">
		<table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
			<tr><td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n general</strong></div></td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			
			<tr>
				<td width="6%"><div align="left">No. de folio:</div></td>
				<td width="18%"><strong><?PHP  echo $t_id ?></strong></td>
				<td width="2%">&nbsp;</td>
				<td width="12%"><div align="left">Fecha de creaci&oacute;n:</div></td>
				<td width="14%"><div align="left"><strong><?PHP  echo $fecha_tramite ?></strong></div></td>
			</tr>
			
			<tr>
				<td ><div align="left">Solicitante:</div></td>
				<td ><div align="left"><strong><?PHP  echo $iniciador;?></strong></div></td>
				<td >&nbsp;</td>
				<td ><div align="left">Centro de costos:</div></td>
				<td ><div align="left"><strong><?PHP  echo $cc_centrocostos ?> - <?PHP  echo $cc_nombre ?></strong></div></td>
			</tr>
			
			<tr>
				<td ><div align="left">Motivo:</div></td>
				<td ><div align="left"><strong><?PHP  echo $motivo?><input type="hidden" name="motivo" value ="<?PHP  echo $motivo?>" /></strong></div></td>
				<td >&nbsp;</td>				
				<td ><div align="left">Etapa:</div></td>
				<td ><div align="left"><strong><?PHP  echo $etapa ?></strong></div></td>
			</tr>
			
			<tr>
				<td ><div align="left">Fecha de viaje:</div></td>
				<td ><div align="left"><strong><?PHP  echo $fecha_total?><input type="hidden" name="fecha_sal_llegada" value ="<?PHP  echo $fecha_total?>" /></strong></div></td>
				<td >&nbsp;</td>				
				<td ><div align="left">Destino:</div></td>
				<!--<td ><div align="left"><strong><?PHP  //echo $tipo_viaje?></strong></div></td>-->
				<td ><div align="left"><strong><?PHP  echo $destino?></strong></div></td>	
			</tr>
			<tr>
					<td colspan="3">&nbsp;</td>
					<td ><div align="left">Dias de viaje:</div></td>
					<td ><div align="left"><strong><?PHP  echo $dias_de_viaje ?><input type="hidden" name="diasViaje" value ="<?PHP  echo $dias_de_viaje?>" /></strong></div></td>
			</tr> 
			<tr>
				<td ><div align="left">Autorizador(es):</div></td>
				<td colspan="5"><div align="left"><strong><?PHP  echo $autorizadores; ?></strong></div></td>
			</tr>
			<?PHP //if($tipoUsuario == 4 || $tipoUsuario == 1) {?>
				 
			<?PHP // } ?>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>             
		</table>
		<br />
		<!--<table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%"><div align="right">Motivo:</div></td>
				<td width="18%"><strong><?PHP  echo $motivo ?></strong></td>
				<td width="2%">&nbsp;</td>
				<td width="10%"><div align="right">D&iacute;as de viaje:</div></td>
				<td width="14%"><div align="left"><strong><?PHP  echo $totaldias ?></strong></div></td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
		</table>-->
	<br />
	<br />
	<br />
		<div align="center" style="color:#003366"><strong>Itinerarios de viaje</strong></div>
		<table id="solicitud_table" class="tablesorter" cellspacing="1">
			<thead> 
			<!--Para tipo de usuario de finanzas con diferente etapa-->
				<?PHP  if($tipoUsuario == 4 && ($id_etapa == SOLICITUD_ETAPA_EN_COTIZACION || $id_etapa == SOLICITUD_ETAPA_COTIZADA || $id_etapa == SOLICITUD_ETAPA_AGENCIA)) { ?>
					<tr> 				   
						<th width="7%" align="center">No.</th>
						<th width="7%" align="center">Zona Geogr&aacute;fica</th>
						<th width="7%" align="center">Regi&oacute;n</th>	
						<th width="7%" align="center">Origen</th>
						<th width="7%" align="center">Destino</th>
						<th width="7%" align="center">Fecha de salida</th>
						<th width="7%" align="center">Horario de salida</th>
						<th width="7%" align="center">Transporte</th>
						<th width="7%" align="center">Hospedaje</th>
						<th width="7%" align="center">Costo hospedaje MXN</th>
						<th width="7%" align="center">Noches</th>
						<th width="7%" align="center">Renta de auto</th>
						<th width="7%" align="center">Costo renta de auto MXN</th>
						<?PHP if($tipo_viaje =="Redondo"){ ?>
							<th width="7%" align="center">Fecha de regreso</th>
							<th width="7%" align="center">Hora de regreso</th>
						<?PHP }?>	
						<th width="7%" align="center">Agregar cotizaci&oacute;n</th>
					</tr> 
					<!--Para tipo de usuario de finanzas con diferente etapa-->	
				<?PHP  }else if($tipoUsuario == 4 && $id_etapa == SOLICITUD_ETAPA_APROBADA){ ?>
					<tr> 				   
						<th width="7%" align="center">No.</th>
						<th width="7%" align="center">Zona Geogr&aacute;fica</th>
						<th width="7%" align="center">Regi&oacute;n</th>	
						<th width="7%" align="center">Origen</th>
						<th width="7%" align="center">Destino</th>
						<th width="7%" align="center">Fecha de salida</th>
						<th width="7%" align="center">Horario de salida</th>
						<th width="7%" align="center">Transporte</th>
						<th width="7%" align="center">Hospedaje</th>
						<th width="7%" align="center">Costo hospedaje MNX</th>
						<th width="7%" align="center">Noches</th>
						<th width="7%" align="center">Renta de auto</th>
						<th width="7%" align="center">Costo renta de auto MXN</th>
						<?PHP if($tipo_viaje =="Redondo"){ ?>
							<th width="7%" align="center">Fecha de regreso</th>
							<th width="7%" align="center">Hora de regreso</th>
						<?PHP }?>	
					</tr>
				<!--Para tipo de usuario Normal-->	
				<?PHP  }else if(($tipoUsuario == 1) || ($tipoUsuario == 3)){?>
					<tr> 				   
						<th width="7%" align="center">No.</th>
						<th width="7%" align="center">Zona Geogr&aacute;fica</th>
						<th width="7%" align="center">Regi&oacute;n</th>	
						<th width="7%" align="center">Origen</th>
						<th width="7%" align="center">Destino</th>
						<th width="7%" align="center">Fecha de salida</th>
						<th width="7%" align="center">Horario de salida</th>
						<th width="7%" align="center">Transporte</th>
						<th width="7%" align="center">Hospedaje</th>
						<th width="7%" align="center">Costo hospedaje MXN</th>
						<th width="7%" align="center">Noches</th>
						<th width="7%" align="center">Renta de auto</th>
						<th width="7%" align="center">Costo renta de auto MXN</th>
						<?PHP if($tipo_viaje =="Redondo"){ ?>
							<th width="7%" align="center">Fecha de regreso</th>
							<th width="7%" align="center">Hora de regreso</th>
						<?PHP }?>	
					</tr>
					<!--Para tipo de usuario empleado o cualquier otro-->	
				<?PHP }else if($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 2 || $tipoUsuario == 9){ ?>	
					<tr> 				   
						<th width="7%" align="center">No.</th>
						<th width="7%" align="center">Zona Geogr&aacute;fica</th>
						<th width="7%" align="center">Regi&oacute;n</th>	
						<th width="7%" align="center">Origen</th>
						<th width="7%" align="center">Destino</th>
						<th width="7%" align="center">Fecha de salida</th>
						<th width="7%" align="center">Horario de salida</th>
						<th width="7%" align="center">Transporte</th>
						<th width="7%" align="center">Hospedaje</th>
						<th width="7%" align="center">Costo hospedaje MXN</th>
						<th width="7%" align="center">Noches</th>
						<th width="7%" align="center">Renta de auto</th>
						<th width="7%" align="center">Costo renta de auto MXN</th>
					</tr> 
				<?PHP  } ?>
			</thead> 
			<tbody> <div style="color:#0000FF; font-size:9.5px; display:none;">Nota: Para editar itinerario de clic en el icono de edici&oacute;n (&nbsp;<img src='../../images/addedit.png'/>&nbsp;).</div>
				<?PHP  
					$auxItinerarios = array();
					$query = "SELECT svi.svi_id, svi_tipo_viaje, sv.sv_viaje, svi_origen, svi_destino, svi_horario,
							re_nombre, svi_empresa_auto, svi_divisa_auto, svi_tipo_auto, DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') AS fecha_salida,
							svi_horario_llegada, svi_horario_salida, svi_tipo_transporte, svi_total_auto, svi_dias_renta,
							svi_total_pesos_auto, svi_costo_auto, DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') AS fecha_llegada,
							svi.svi_medio, svi_renta_auto_agencia, svi_hotel_agencia,
							IF(svi_renta_auto=1,'SI','NO') AS auto,
							IF(svi_hotel=1,'SI','NO') AS hotel,
							IF(SUM(h_total_pesos),SUM(h_total_pesos),0) AS total,
							IF(SUM(h_noches),SUM(h_noches),0) AS noches, re_id, se_nombre
							FROM sv_itinerario AS svi
							left JOIN solicitud_viaje AS sv ON sv.sv_id = svi.svi_solicitud
							LEFT JOIN cat_regiones AS cr ON cr.re_id = svi.svi_region
							LEFT JOIN hotel AS ho ON ho.svi_id = svi.svi_id 
							left JOIN servicios ON svi.svi_tipo_auto = se_id 							
							WHERE svi.svi_solicitud = {$id_Solicitud} GROUP BY svi.svi_id";
					//error_log($query);
					$rst = $cnn->consultar($query);
						while($datos=mysql_fetch_assoc($rst)){
								array_push($auxItinerarios,$datos);
						}
						$i=1;
						$verBotonCotizaBoletoAvion = false;
						foreach($auxItinerarios as $datosAux){
							$tipoTransporteB=$datosAux["svi_tipo_transporte"];
							if($tipoTransporteB != "Terrestre"){
								$verBotonCotizaBoletoAvion = true;
							}
							if($tipoUsuario == 4 && ($id_etapa == SOLICITUD_ETAPA_EN_COTIZACION || $id_etapa == SOLICITUD_ETAPA_COTIZADA || $id_etapa == SOLICITUD_ETAPA_AGENCIA)) {
				?>
								<tr>
									<td align="center"><?PHP  echo $i ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_viaje"] ?><input type="hidden" id="tipo_viaje<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_viaje"] ?>"/></td>
									<td align="center"><?PHP  echo strtoupper($datosAux["re_nombre"]) ?><input type="hidden" id="region<?PHP  echo $i ?>" name="region<?PHP  echo $i ?>" value="<?PHP  echo  $datosAux["re_id"] ?>"/><input type="hidden" id="regionNombre<?PHP  echo $i ?>" value="<?PHP  echo  strtoupper($datosAux["re_nombre"]); ?>"/></td>
									<td align="center"><input type='hidden' name='origen<?PHP echo $i ?>' id='origen<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_origen"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_origen"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><input type='hidden' name='destino<?PHP echo $i ?>' id='destino<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_destino"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_destino"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><?PHP  echo $datosAux["fecha_salida"] ?><input type="hidden" id="fecha_salida<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["fecha_salida"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["svi_horario_salida"] ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_transporte"] ?><input type="hidden" id="tipo_transporte<?PHP  echo $i ?>" value="<?PHP  $datosAux["svi_tipo_transporte"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["hotel"] ?></td>
									<td align="center"><?PHP  echo number_format($datosAux["total"],2,".",",");?><input type="hidden" id="total_h_pesos<?PHP  echo $i ?>" value="<?PHP echo $datosAux["total"]; ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["noches"]  ?></td>
									<td align="center"><?PHP  echo $datosAux["auto"]?></td>
									<td align="center"><?PHP  echo number_format( $datosAux["svi_total_pesos_auto"],2,".",",");?><input type="hidden" id="total_A_pesos<?PHP  echo $i ?>" value="<?PHP echo $datosAux["svi_total_pesos_auto"]; ?>"/></td>
									<input type='hidden' name='hotelAgencia<?PHP echo $i ?>' id='hotelAgencia<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_hotel_agencia"]?>' readonly='readonly'/>
									<?PHP  if($tipoUsuario==4 && ($id_etapa == SOLICITUD_ETAPA_EN_COTIZACION || $id_etapa == SOLICITUD_ETAPA_COTIZADA || $id_etapa == SOLICITUD_ETAPA_AGENCIA)){ ?>
										<?PHP if($tipo_viaje =="Redondo"){?>
											<td align="center"><?PHP  echo $datosAux["fecha_llegada"] ?></td>
											<td align="center"><?PHP  echo $datosAux["svi_horario_llegada"] ?></td>
										<?PHP }?>
										<?PHP if($datosAux["svi_renta_auto_agencia"] == "1" || $datosAux["svi_hotel_agencia"] == "1"){?>
											<td><div align='center'><img src='../../images/addedit.png' alt='Click aqu&iacute; para agregar Cotizaci&oacute;n' name='<?PHP echo $i."agregar_cotizacion"?>'  id='<?PHP echo $i."agregar_cotizacion"?>' class="editaHotel" rgn = <?echo $datosAux["svi_tipo_viaje"]; ?> onclick='if(bandera_hotel == 2){reinicioItinerarios();} setItinerarioActualOPosible(this.id); $("#warning_msg").css("display", "none"); verificar_auto_hotel(); agregar_datos_a_tabla_cotizacion_agencia(this.id); if($("#auto_agencia").val()==1){verificar_itinerario();}; verificar_itinerario2(); mensaje(); flotanteActive(1); calculaTotalHospedaje(); ' style='cursor:pointer' /></td>
										<?PHP }else if($datosAux["svi_renta_auto_agencia"] == "0" && $datosAux["svi_hotel_agencia"] == "0"){?>
											<td><div align='center'><!--<img src='../../images/addeditdisabled.png' alt='Click aqu&iacute; para agregar Cotizaci&oacute;n' name='<?PHP echo $i."agregar_cotizacion"?>'  id='<?PHP echo $i."agregar_cotizacion"?>' onclick='this.disabled = false' style='cursor:pointer' />--></div></td>
										<?PHP }?>
									<?PHP }?>
									<input type='hidden' name='empresaAuto<?PHP echo $i ?>' id='empresaAuto<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_empresa_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='costoDia<?PHP echo $i ?>' id='costoDia<?PHP echo $i ?>' value='<?PHP echo setValorVacio($datosAux["svi_costo_auto"]);?>' readonly='readonly'/>
									<input type='hidden' name='tipoDivisa<?PHP echo $i ?>' id='tipoDivisa<?PHP echo $i ?>' value='<?PHP echo$datosAux["svi_divisa_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='tipoAuto<?PHP echo $i ?>' id='tipoAuto<?PHP echo $i ?>' value='<?PHP echo$datosAux["svi_tipo_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='tipoAutoTxt<?PHP echo $i ?>' id='tipoAutoTxt<?PHP echo $i ?>' value='<?PHP echo$datosAux["se_nombre"]?>' readonly='readonly'/>
									<input type='hidden' name='totalAuto<?PHP echo $i ?>' id='totalAuto<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_total_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='diasRenta<?PHP echo $i ?>' id='diasRenta<?PHP echo $i ?>' value='<?PHP echo setValorVacio($datosAux["svi_dias_renta"]);?>' readonly='readonly'/>
									<input type='hidden' name='montoPesos<?PHP echo $i ?>' id='montoPesos<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_total_pesos_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='hotel_agencia<?PHP echo $i ?>' id='hotel_agencia<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_hotel_agencia"]?>' readonly='readonly'/>
									<input type='hidden' name='auto_agencia<?PHP echo $i ?>' id='auto_agencia<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_renta_auto_agencia"]?>' readonly='readonly'/>
									<input type='hidden' name='svi_id<?PHP echo $i ?>' id='svi_id<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_id"]?>' readonly='readonly'/>
								</tr>
							<?PHP }else if($tipoUsuario == 4 && $id_etapa == SOLICITUD_ETAPA_APROBADA){?>
								<tr>
									<td align="center"><?PHP  echo $i ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_viaje"] ?><input type="hidden" id="tipo_viaje<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_viaje"] ?>"/></td>
									<td align="center"><?PHP  echo strtoupper($datosAux["re_nombre"]) ?><input type="hidden" id="region<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["sv_viaje"] ?>"/><input type="hidden" id="regionNombre<?PHP  echo $i ?>" value="<?PHP  echo  strtoupper($datosAux["re_nombre"]); ?>"/></td>
									<td align="center"><input type='hidden' name='origen<?PHP echo $i ?>' id='origen<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_origen"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_origen"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><input type='hidden' name='destino<?PHP echo $i ?>' id='destino<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_destino"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_destino"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><?PHP  echo $datosAux["fecha_salida"] ?><input type="hidden" id="fecha_salida<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["fecha_salida"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["svi_horario_salida"] ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_transporte"] ?><input type="hidden" id="tipo_transporte<?PHP  echo $i ?>" value="<?PHP  $datosAux["svi_tipo_transporte"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["hotel"] ?></td>
									<td align="center"><?PHP  echo number_format($datosAux["total"],2,".",",");?></td>
									<td align="center"><?PHP  echo $datosAux["noches"]  ?></td>
									<td align="center"><?PHP  echo $datosAux["auto"]?></td>
									<td align="center"><?PHP  echo number_format( $datosAux["svi_total_pesos_auto"],2,".",","); ?> </td>
									<?PHP  if($tipoUsuario == 4 && $id_etapa == SOLICITUD_ETAPA_APROBADA){ ?>
										<?PHP if($tipo_viaje =="Redondo"){?>
											<td align="center"><?PHP  echo $datosAux["fecha_llegada"] ?></td>
											<td align="center"><?PHP  echo $datosAux["svi_horario_llegada"] ?></td>
										<?PHP }?>	
									<?PHP }?>
									<input type='hidden' name='empresaAuto<?PHP echo $i ?>' id='empresaAuto<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_empresa_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='costoDia<?PHP echo $i ?>' id='costoDia<?PHP echo $i ?>' value='<?PHP echo setValorVacio($datosAux["svi_costo_auto"]);?>' readonly='readonly'/>
									<input type='hidden' name='tipoDivisa<?PHP echo $i ?>' id='tipoDivisa<?PHP echo $i ?>' value='<?PHP echo$datosAux["svi_divisa_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='tipoAuto<?PHP echo $i ?>' id='tipoAuto<?PHP echo $i ?>' value='<?PHP echo$datosAux["svi_tipo_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='tipoAutoTxt<?PHP echo $i ?>' id='tipoAutoTxt<?PHP echo $i ?>' value='<?PHP echo$datosAux["se_nombre"]?>' readonly='readonly'/>
									<input type='hidden' name='totalAuto<?PHP echo $i ?>' id='totalAuto<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_total_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='diasRenta<?PHP echo $i ?>' id='diasRenta<?PHP echo $i ?>' value='<?PHP echo setValorVacio($datosAux["svi_dias_renta"]);?>' readonly='readonly'/>
									<input type='hidden' name='montoPesos<?PHP echo $i ?>' id='montoPesos<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_total_pesos_auto"]?>' readonly='readonly'/>
									<input type='hidden' name='svi_id<?PHP echo $i ?>' id='svi_id<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_id"]?>' readonly='readonly'/>
								</tr>
							<?PHP }else if(($tipoUsuario == 1) || ($tipoUsuario == 3)){?>
								<tr>
									<td align="center"><?PHP  echo $i ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_viaje"] ?><input type="hidden" id="tipo_viaje<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_viaje"] ?>"/></td>
									<td align="center"><?PHP  echo strtoupper($datosAux["re_nombre"]) ?><input type="hidden" id="region<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["sv_viaje"] ?>"/></td>
									<td align="center"><input type='hidden' name='origen<?PHP echo $i ?>' id='origen<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_origen"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_origen"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><input type='hidden' name='destino<?PHP echo $i ?>' id='destino<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_destino"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_destino"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><?PHP  echo $datosAux["fecha_salida"] ?><input type="hidden" id="fecha_salida<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["fecha_salida"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["svi_horario_salida"] ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_transporte"] ?><input type="hidden" id="tipo_transporte<?PHP  echo $i ?>" value="<?PHP  $datosAux["svi_tipo_transporte"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["hotel"] ?></td>
									<td align="center"><?PHP  echo number_format($datosAux["total"] ,2,".",",");?></td>
									<td align="center"><?PHP  echo $datosAux["noches"]  ?></td>
									<td align="center"><?PHP  echo $datosAux["auto"]?></td>
									<td align="center"><?PHP  echo number_format( $datosAux["svi_total_pesos_auto"],2,".",","); ?> </td>
									<?PHP if($tipo_viaje =="Redondo"){?>
										<td align="center"><?PHP  echo $datosAux["fecha_llegada"] ?></td>
										<td align="center"><?PHP  echo $datosAux["svi_horario_llegada"] ?></td>
									<?PHP }?>	
									<input type='hidden' name='svi_id<?PHP echo $i ?>' id='svi_id<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_id"]?>' readonly='readonly'/>
								</tr>
							<?PHP }else if($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 2 || $tipoUsuario == 9){ ?>
								<tr>
									<td align="center"><?PHP  echo $i ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_viaje"] ?><input type="hidden" id="tipo_viaje<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_viaje"] ?>"/></td>
									<td align="center"><?PHP  echo strtoupper($datosAux["re_nombre"]) ?><input type="hidden" id="region<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["sv_viaje"] ?>"/></td>
									<td align="center"><input type='hidden' name='origen<?PHP echo $i ?>' id='origen<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_origen"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_origen"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
									<td align="center"><input type='hidden' name='destino<?PHP echo $i ?>' id='destino<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_destino"]?>' readonly='readonly'/><?PHP  echo strtr(strtoupper($datosAux["svi_destino"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");?></td>
									<td align="center"><?PHP  echo $datosAux["fecha_salida"] ?><input type="hidden" id="fecha_salida<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["fecha_salida"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["svi_horario_salida"] ?></td>
									<td align="center"><?PHP  echo $datosAux["svi_tipo_transporte"] ?><input type="hidden" id="tipo_transporte<?PHP  echo $i ?>" value="<?PHP  $datosAux["svi_tipo_transporte"] ?>"/></td>
									<td align="center"><?PHP  echo $datosAux["hotel"] ?></td>
									<td align="center"><?PHP  echo number_format($datosAux["total"],2,".",",");?></td>
									<td align="center"><?PHP  echo $datosAux["noches"]  ?></td>
									<td align="center"><?PHP  echo $datosAux["auto"]?></td>
									<td align="center"><?PHP  echo number_format( $datosAux["svi_total_pesos_auto"],2,".",","); ?> </td>
									<input type='hidden' name='svi_id<?PHP echo $i ?>' id='svi_id<?PHP echo $i ?>' value='<?PHP echo $datosAux["svi_id"]?>' readonly='readonly'/>
								</tr>
							<?PHP }?>
						<?PHP 
							$i++;
					}
					?>						
				<!-- cuerpo tabla-->
			</tbody>

		</table>
		<?PHP  if($tipoUsuario==4 && ( $_GET['etapa']== SOLICITUD_ETAPA_SEGUNDA_APROBACION || $_GET['etapa']== SOLICITUD_ETAPA_APROBADA ) && $verBotonCotizaBoletoAvion){ ?>
			<div align="center">
				<!-- value="Cotizaci&oacute;n de boleto de avi&oacute;n" -->
				<input type="button" id="compraAvion" name="compraAvion" value="Registrar compra de avi&oacute;n" onclick="editaItinerario();"/>
			</div>
		<?PHP  }else if($tipoUsuario == 4 && $verBotonCotizaBoletoAvion){ ?>
			<div align="center">
				<!-- value="Cotizaci&oacute;n de boleto de avi&oacute;n" -->
				<input type="button" id="cotizaAvion" name="cotizaAvion" value="Cotizaci&oacute;n de boleto de avi&oacute;n" onclick="editaItinerario();"/>
			</div>
		<?PHP  }?>
		<?php $tramite = new Tramite();
		$medioViaje = $tramite->solViajeTransporte($_GET['id']);
		$sql = "SELECT svi_hotel_agencia, svi_renta_auto_agencia
				FROM solicitud_viaje
				LEFT JOIN sv_itinerario ON svi_solicitud  = sv_id
				WHERE svi_tipo_transporte = 'terrestre'
				AND sv_tramite = ".$_GET['id'];
		$res = mysql_query($sql);
		$agenciaAuto = 0;
		$agenciaHotel = 0;
		while($row = mysql_fetch_assoc($res)){
			if($row["svi_hotel_agencia"]==1)$agenciaAuto = 1;
			if($row["svi_renta_auto_agencia"]==1)$agenciaHotel = 1;		
		}
						
		//error_log("----------->>>>>>>>>>>>Medio de viaje: ".$medioViaje);
		if($medioViaje == 1 || $agenciaAuto == 1 || $agenciaHotel == 1 ){?>
		<table align="left">
			<tr>
				<td>&nbsp;</td>		
				<td colspan="4">
					<div align="left">
						Mostrar Resumen de agencia de viaje <input type="checkbox" id="accion" name="accion" onclick="requerimientos();" <?PHP  if($t_etapa_actual == 8){echo "disabled='disabled'";}?>/>
					</div>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr><td colspan="6">&nbsp;</td></tr>		
		</table>
		<br><br><br><br><br><br>
		<div id ="resumen" name="resumen" align="" style="display: none; color:#003366"><strong>Resumen de agencia de viaje</strong>
		<table id="resumen_de_observaciones"  class="tablesorter" cellspacing="1" style="width:95%">
			<thead> 
				<tr> 				   
					<th align="center">Fecha/Hora</th>
					<th align="center">Comentario</th>
					<th align="center">Autor</th>
				</tr> 

				<?PHP  
				$ObsSqlAE="";	
				$aux = array();
				
				$tramiteHO =new Tramite();
				$tramiteHO->Load_Tramite($t_id);
				$iniciador=$tramiteHO->Get_dato("t_iniciador");
					
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Agencia");
				$agencia=$agrup_usu->Get_dato("au_id");
					
				$rstAE=$agrup_usu->Load_Homologacion_Usuarios($agencia);
				while($arre=mysql_fetch_assoc($rstAE)){
					$ObsSqlAE.="OR ob_usuario ='".$arre['hd_u_id']."' ";
				}				
				
				
				$queryAE = "SELECT DATE_FORMAT(ob_fecha,'%d/%m/%Y %h:%i:%s %p')  AS fecha,
						ob_texto AS comentario,
						e.nombre AS autor
						FROM observaciones
						INNER JOIN empleado AS e
						ON ob_usuario = e.idempleado
						WHERE ob_tramite = '{$t_id}'
						#AND (( ob_usuario = '".$iniciador."' ".$ObsSqlAE." )) 
						ORDER BY ob_fecha ASC";
				//error_log($queryAE);
					
					$rst = $cnn->consultar($queryAE);
					while($datos=mysql_fetch_assoc($rst)){
						array_push($aux,$datos);
					}
					foreach($aux as $datosAux){
				?>
					<tr>
						<td width="20%" align="center"><?PHP  echo $datosAux["fecha"] ?></td>
						<td width="20%">
							<div>
								<textarea cols="120" row="7"> <?PHP  echo $datosAux["comentario"]; ?></textarea>
							</div> 
						</td>
						<td width="20%" align="center"><?PHP  echo $datosAux["autor"] ?></td>
					</tr>
				<?PHP 
					}
				?>						
			</thead> 
			<tbody>
				
			</tbody>  
		</table>
		</div>
		<?php }?>
		<br /><br/>
		<div align="center" style="color:#003366"><strong>Cotizaciones</strong></div>
		<!-----------------------------------------------------
							Datos de avion
		------------------------------------------------------>
		<div align="left" style="color:#003366; margin-left:3em"><strong>Datos del Avi&oacute;n</strong></div>
		<table id="table_avion" name="table_avion"class="tablesorter" cellspacing="1" style="">
			<thead>
				<tr> 
					<th width="2%">No.</th> 
					<th width="4%">Tipo de viaje</th>
					<th width="4%">Aerol&iacute;nea</th>
					<th width="4%">Clase</th>
					<th width="4%">Fecha de salida</th>
					<?PHP if(($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 4 || $tipoUsuario == 1 || $tipoUsuario == 3) && ($tipo_viaje =="Redondo" || $tipo_viaje =="Multidestinos")){?>
						<th width="4%">Fecha de regreso</th>
					<?PHP }?>	
					<th width="5%">Subtotal</th>
					<th width="5%">IVA</th>
					<th width="5%">TUA</th>
					<th width="5%">Total MXN</th>
					<?PHP  if($tipoUsuario==4)echo "<th width='3%' align='center'>Eliminar</th>" ?>
				</tr> 
				<?PHP  
					$aux = array();
					$query = "SELECT svi_id,
								svi_tipo_viaje,
								sv.sv_viaje,
								svi_aerolinea,
								svi_monto_vuelo,
								svi_iva,
								svi_tua,
								svi_monto_vuelo_total,
								svi_solicitud,
								svi_tipo_aerolinea,
								DATE_FORMAT(svi_fecha_salida_avion,'%d/%m/%Y') AS fecha_salida,
								DATE_FORMAT(svi_fecha_regreso_avion,'%d/%m/%Y') AS fecha_llegada,
								svi_tipo_aerolinea,
								svi_total_pesos_auto,
								svi_monto_vuelo_cotizacion, 
								se_nombre
								FROM sv_itinerario AS svi
								INNER JOIN solicitud_viaje AS sv ON (sv.sv_id = svi.svi_solicitud) 
								INNER JOIN servicios AS se ON (svi.svi_tipo_aerolinea = se.se_id) 
								WHERE svi.svi_solicitud = '{$id_Solicitud}' GROUP BY svi_solicitud";
					//error_log($query);
					
					$rst = $cnn->consultar($query);
					while($datos=mysql_fetch_assoc($rst)){
						array_push($aux,$datos);
					}
					
					$subtotal_avion=0;
					$iva_avion=0;
					$tua_avion=0;				
					$i=1;
					$id=1;
					
					//Se obtiene la ultima fecha de una solicitud de viaje multidestinos
					$fechaUltimoTramite="";
					$queryMultidestinos="SELECT DATE_FORMAT(MAX(svi_fecha_salida),'%d/%m/%Y') AS 'svi_fecha_salida' FROM sv_itinerario WHERE svi_solicitud='{$id_Solicitud}'";				
					$rstMulti = $cnn->consultar($queryMultidestinos);
					
					while ($fila = mysql_fetch_assoc($rstMulti)) {
						$fechaUltimoTramite=$fila['svi_fecha_salida'];
					}
					
					foreach($aux as $datosAux){					
					if($datosAux["svi_aerolinea"] != null){
					?>
					<tbody>       
						<tr>
							<td align='center'><?PHP  echo $i ?></td>
							<td align='center'><?PHP  echo $datosAux["svi_tipo_viaje"] ?></td>
							<td align='center'><input type="hidden" name="aeroAvionCot" id="aeroAvionCot" value='<?PHP  echo $datosAux["svi_aerolinea"] ?>'/><?PHP  echo $datosAux["svi_aerolinea"] ?></td>
							<td align='center'><input type="hidden" name="tipo_aerolinea" id="tipo_aerolinea" value='<?PHP  echo $datosAux["svi_tipo_aerolinea"] ?>'/><?PHP  echo $datosAux["se_nombre"] ?></td>
							<td align='center'><input type="hidden" name="salAvionCot" id="salAvionCot" value='<?PHP  echo $datosAux["fecha_salida"] ?>'/><?PHP  echo $datosAux["fecha_salida"] ?></td>
							<?PHP if(($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 4 || $tipoUsuario == 1 || $tipoUsuario == 3) && ($tipo_viaje =="Redondo" || $tipo_viaje =="Multidestinos")){?>
								<?PHP  if($tipo_viaje =="Redondo"){?>
								<td align ="center"><input type="hidden" name="llAvionCot" id="llAvionCot" value='<?PHP  echo $datosAux["fecha_llegada"]?>'/><?PHP  echo $datosAux["fecha_llegada"]?></td>
								<?PHP  }else if($tipo_viaje =="Multidestinos"){?>
								<td align ="center"><input type="hidden" name="llAvionCot" id="llAvionCot" value='<?PHP  echo $fechaUltimoTramite; ?>'/><?PHP  echo $fechaUltimoTramite;?></td>							
								<?PHP  }?>
							<?PHP }?>	
							<td align="center"><input type="hidden" name="coAvionCot" id="coAvionCot" value='<?PHP  echo (float)$datosAux["svi_monto_vuelo"] ?>' readonly="readonly" />&nbsp;<?PHP  echo number_format($datosAux["svi_monto_vuelo"],2,".",","); ?></td>
							<td align="center"><input type="hidden" name="ivaAvionCot" id="ivaAvionCot" value='<?PHP  echo (float)$datosAux["svi_iva"] ?>' readonly="readonly" />&nbsp;<?PHP  echo number_format($datosAux["svi_iva"],2,".",","); ?></td>
							<td align="center"><input type="hidden" name="tuaAvionCot" id="tuaAvionCot" value='<?PHP  echo (float)$datosAux["svi_tua"] ?>' readonly="readonly" />&nbsp;<?PHP  echo number_format($datosAux["svi_tua"],2,".",","); ?></td>
							<?PHP  if($id_etapa==SOLICITUD_ETAPA_APROBADA){ ?>
								<td align="center"><input type="hidden" name="cotAvionCot" id="cotAvionCot" value='<?PHP echo $datosAux["svi_monto_vuelo_cotizacion"]?>'/>&nbsp;<?PHP  echo number_format((floatval($datosAux["svi_monto_vuelo_cotizacion"])),2,".",",");?></td>
							<?PHP  }else{ ?>
								<td align="center"><input type="hidden" name="cotAvionCot" id="cotAvionCot" value='<?PHP echo $datosAux["svi_monto_vuelo_cotizacion"]?>'/>&nbsp;<?PHP  echo number_format((floatval($datosAux["svi_monto_vuelo_cotizacion"])),2,".",",");?></td>
								<!--<td align="right">$&nbsp;<?PHP  //echo number_format((floatval($datosAux["svi_monto_vuelo"])+floatval($datosAux["svi_iva"])+floatval($datosAux["svi_tua"])),2,".",",");?></td>-->
							<?PHP  } ?>
							<?PHP  if($tipoUsuario==4){ ?>
								<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' id='eliminarCotizacionAvion' name='eliminarCotizacionAvion' onmousedown='eliminaAvion(<?PHP echo $datosAux["svi_solicitud"]?>,this.id)' style='cursor:pointer'/></div></td>
							<?PHP  } ?>
						</tr>
					</tbody>
					<?PHP }?>
						
						<!--<input type="hidden" name="tipo_aerolinea<?PHP  echo $id ?>" id="tipo_aerolinea<?PHP  echo $id ?>" value="<?PHP  echo $datosAux["svi_tipo_aerolinea"] ?>" readonly="readonly" />-->
				<?PHP 
						$subtotal_avion = floatVal($subtotal_avion)+floatVal($datosAux["svi_monto_vuelo"]);
						$iva_avion = floatVal($iva_avion)+floatVal($datosAux["svi_iva"]);
						$tua_avion = floatVal($tua_avion)+floatVal($datosAux["svi_tua"]);
						$id++;
						$i++;
					}
				?>	
					  <!--<td class="alignRight">Totales:</td>
					  <td align="right">$<input type="hidden" name="" id="" value="<?PHP  //echo number_format($subtotal_avion,2,".",","); ?>" readonly="readonly"/></td>
					  <td align="right">$<input type="hidden" name="" id="" value="<?PHP  //echo number_format($iva_avion,2,".",","); 		?>" readonly="readonly"/></td>
					  <td align="right">$<input type="hidden" name="" id="" value="<?PHP  //echo number_format($tua_avion,2,".",","); 		?>" readonly="readonly"/></td>
					  <td align="right">$<input type="hidden" name="t_avion" id="t_avion" value="<?PHP  //echo number_format((floatVal($subtotal_avion)+floatVal($iva_avion)+floatVal($tua_avion)),2,".",","); ?>" readonly="readonly"/></td>
					  -->      
		  </thead> 
			<tbody> 
				<!-- cuerpo tabla-->
			</tbody>
		</table>
		<table id="costo_avion" border="0" align="right">
			<tr>
				<td>&nbsp;</td>
				<td>
					<div id="total_avion">Total:</div>
				</td>
				<td>
					<?PHP  $totalAvionCotizado=floatVal($subtotal_avion)+floatVal($iva_avion)+floatVal($tua_avion)?>
					<div id="total_pesos_avion"><?PHP  echo number_format($totalAvionCotizado,2,".",","); ?></div>
					<?PHP  if($datosAux["svi_monto_vuelo_cotizacion"]==""){$svi_monto_vuelo_cotizacion=0; ?>				
						<input name="TotAvion" id="TotAvion" type="hidden" readOnly="" value="<?PHP  echo $svi_monto_vuelo_cotizacion;?>"/>
					<?PHP  }else{?>
						<input name="TotAvion" id="TotAvion" type="hidden" readOnly="" value="<?PHP  echo $datosAux["svi_monto_vuelo_cotizacion"] ?>"/>
					<?PHP  }?>
				</td>
				<td>
					<div>MXN</div>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>

		</br>
	<!--	<table id="datosgrales" style="" width="300px" border="1" align="right">
			<tr>
			  <td width="10%" class="alignRight">Totales:</td>
			  <td width="10%">$<input type="text" name="" id="" value="<?PHP  echo $subtotal_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			  <td>$<input type="text" name="" id="" value="<?PHP  echo $iva_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			  <td>$<input type="text" name="" id="" value="<?PHP  echo $tua_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			  <td>$<input type="text" name="" id="" value="<?PHP  echo floatVal($subtotal_avion)+floatVal($iva_avion)+floatVal($tua_avion) ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			</tr>
		</table>-->
		<table id="datosgrales" style="display:none" width="300px" border="0" align="right">
			<tr>
			  <td width="10%" class="alignRight">Subtotal:</td>
			  <td width="10%">$<input type="text" name="anticipo" id="anticipo" value="<?PHP  echo $subtotal_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			  <td>$<input type="text" name="ivaT" id="ivaT" value="<?PHP  echo $iva_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			  <td>$<input type="text" name="tuaT" id="tuaT" value="<?PHP  echo $tua_avion ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			  <td>$<input type="text" name="Total" id="Total" value="<?PHP  echo floatVal($subtotal_avion)+floatVal($iva_avion)+floatVal($tua_avion) ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
			</tr>
		</table>
		<br clear="all">
		<br>

		<!-----------------------------------------------------
							Datos de Hotel
		------------------------------------------------------>
		<div align="left" style="color:#003366; margin-left:3em"><strong>Datos del Hotel</strong></div>
		<table id="table_aprobado_hotel" class="tablesorter" cellspacing="1" style="">
			<thead>
				<tr> 
					<th width="2%">No.</th> 
					<th width="4%">No. Itinerario</th>
					<th width="4%">Nombre hotel</th>
					<th width="4%">Ciudad</th>
					<th width="4%">Comentarios</th>
					<th width="4%">Tipo</th>
					<th width="4%">Noches</th>
					<th width="4%">Monto por noche</th>
					<th width="4%">Divisa</th>
					<?PHP if(($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 4 || $tipoUsuario == 1 || $tipoUsuario == 2 || $tipoUsuario == 3) && ($tipo_viaje =="Redondo" || $tipo_viaje =="Multidestinos" || $tipo_viaje =="Sencillo")){ ?>
						<th width="4%">Fecha de llegada</th>
					<?PHP }?>				
					<th width="4%">Fecha de salida</th>
					<th width="4%">Total MXN</th>
					<th width="5%">No. de reservaci&oacute;n</th>
					<?PHP  if($tipoUsuario==4 && $id_etapa!=SOLICITUD_ETAPA_APROBADA)echo "<th width='3%' align='center'>Eliminar</th>" ?>				
				</tr> 
			</thead> 
			<tbody> 
				<?PHP  
					$aux = array();
					$query = sprintf("SELECT svi.svi_id,
								h_hotel_id,
								h_nombre_hotel,
								h_ciudad,
								h_comentarios,
								h_noches,
								h_iva,
								h_costo_noche,
								ho.div_id AS div_id,
								div_nombre,
								h_tipo_hotel, 
								se_nombre,
								DATE_FORMAT(h_fecha_llegada, '%s') AS fecha_llegada,
								DATE_FORMAT(h_fecha_salida, '%s') AS fecha_salida,
								h_total_pesos,
								svi_hotel,
								svi_hotel_agencia,
								h_no_reservacion
								FROM sv_itinerario AS svi
								INNER JOIN hotel AS ho ON (ho.svi_id = svi.svi_id) 
								INNER JOIN servicios AS se ON (h_tipo_hotel = se_id)
								INNER JOIN divisa AS dv ON (dv.div_id = ho.div_id) 
								WHERE svi.svi_solicitud = %s
								ORDER BY svi_id", '%d/%m/%Y', '%d/%m/%Y', $id_Solicitud);
					//error_log("--->>Consulta de Hoteles: ".$query);					
					$rst = $cnn->consultar($query);				
					while($datos=mysql_fetch_assoc($rst)){
						array_push($aux,$datos);
					}
					$subtotal_de_hotel=0;
					//$otros_carg=0;
					$id=1;
					$i=0;
					foreach($aux as $datosAux){
						//error_log("RETa ".$datosAux["svi_hotel"]."--".$datosAux["svi_hotel_agencia"]);
						if($tipoUsuario == 4 &&  $datosAux["svi_hotel_agencia"] == "1" && $datosAux["h_nombre_hotel"]!=""){
						$i++; ?>
						   <tr>
								<td align="center"><input type="hidden" id="valorBD<?PHP  echo $i ?>" name="valorBD<?PHP  echo $i ?>" value="1"/><div id="<?PHP  echo "num_hotel".$i?>" name="<?PHP  echo "num_hotel".$i?>"><?PHP  echo $i?></div></td>
								<td align="center"><input type="hidden" name="itinerarioHotelEA<?PHP  echo $i ?>" id="itinerarioHotelEA<?PHP  echo $i ?>" value="<?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?>" readonly="readonly" /><?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?></td>
								<td align="center"><input type="hidden" name="nombreHotelEA<?PHP  echo $i ?>" id="nombreHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["h_nombre_hotel"] ?>" readonly="readonly" /><?PHP  echo $datosAux["h_nombre_hotel"] ?></td>
								<td align="center"><input type="hidden" name="ciudadHotelEA<?PHP  echo $i ?>" id="ciudadHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["h_ciudad"] ?>" readonly="readonly" /><?PHP  echo $datosAux["h_ciudad"] ?></td>
								<td align="center"><input type="hidden" name="comentarioHotelEA<?PHP  echo $i ?>" id="comentarioHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["h_comentarios"] ?>" readonly="readonly" /><?PHP  echo $datosAux["h_comentarios"] ?></td>
								<td align="center"><input type="hidden" name="tipoHotelEA<?PHP echo $i ?>" id="tipoHotelEA<?PHP echo $i ?>" value="<?PHP  echo $datosAux["h_tipo_hotel"] ?>" readonly="readonly" /><input type="hidden" name="tipoHotelEA_txt<?PHP echo $i ?>" id="tipoHotelEA_txt<?PHP echo $i ?>" value="<?PHP  echo $datosAux["se_nombre"] ?>" readonly="readonly" /><?PHP  echo $datosAux["se_nombre"] ?></td>
								<td align="center"><input type="hidden" name="nochesHotelEA<?PHP  echo $i ?>" id="nochesHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["h_noches"] ?>" readonly="readonly" /><?PHP  echo $datosAux["h_noches"] ?></td>
								<td align="center"><input type="hidden" name="montonochesHotelEA<?PHP  echo $i ?>" id="montonochesHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["h_costo_noche"] ?>" readonly="readonly" /><?PHP  echo number_format($datosAux["h_costo_noche"],2,".",",");?></td>
								<td align="center"><input type="hidden" name="divisaHotelEA<?PHP  echo $i ?>" id="divisaHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["div_id"] ?>" readonly="readonly" /><input type="hidden" name="divisaHotelEA_txt<?PHP  echo $i ?>" id="divisaHotelEA_txt<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["div_nombre"] ?>" readonly="readonly" /><?PHP  echo $datosAux["div_nombre"];?></td>
								<td align="center"><input type="hidden" name="llegadaHotelEA<?PHP  echo $i ?>" id="llegadaHotelEA<?PHP  echo $i ?>" value="<?PHP   echo $datosAux["fecha_llegada"] ?>" readonly="readonly" /><?PHP  echo $datosAux["fecha_llegada"] ?></td>							
								<td align="center"><input type="hidden" name="salidaHotelEA<?PHP  echo $i ?>" id="salidaHotelEA<?PHP  echo $i ?>" value="<?PHP   echo $datosAux["fecha_salida"] ?>" readonly="readonly" /><?PHP  echo $datosAux["fecha_salida"] ?></td>
								<td align="center"><input type="hidden" name="ivaHotelEA<?PHP  echo $i ?>" id="ivaHotelEA<?PHP  echo $i ?>" value="<?PHP  echo $datosAux['h_iva'] ?>"/><input type="hidden" name="h_total_pesos<?PHP  echo $i ?>" id="h_total_pesos<?PHP  echo $i ?>" value="<?PHP  echo $datosAux['h_total_pesos'] ?>"/><?PHP  echo number_format($datosAux["h_total_pesos"],2,".",",");?></td>					<!--<td align="right"><input type="hidden" name="subtotal_hotel<?PHP  //echo $id ?>" id="subtotal_hotel<?PHP  //echo $id ?>" value="/*<?PHP  //echo $datosAux["svi_costo_hotel"] ?>*/" readonly="readonly" />$&nbsp;<?PHP  /*echo number_format($datosAux["svi_costo_hotel"],2,".",","); */?></td>-->
								<td align="center"><input type="hidden" name="reservacionHotelEA<?PHP  echo $i ?>" id="reservacionHotelEA<?PHP  echo $i ?>" value="<?PHP   echo $datosAux["h_no_reservacion"] ?>" readonly="readonly" /><?PHP  echo $datosAux["h_no_reservacion"] ?></td>
								<!--<td align="right">$&nbsp;<?PHP  /*echo number_format((floatval($datosAux["svi_costo_hotel"])+floatval($datosAux["svi_otrocargo"])),2,".",","); */?></td>
								<td align="right"><input type="hidden" name="no_de_reservacion<?PHP  echo $id ?>" id="no_de_reservacion<?PHP  echo $id ?>" value="" readonly="readonly" /><?PHP  //echo $datosAux["svi_hotel_reservacion"] ?></td>-->
								<?PHP  if($id_etapa !=SOLICITUD_ETAPA_APROBADA){ ?>
									<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' id="<?PHP echo $i."elimHotel" ?>" name="<?PHP echo $i."elimHotel" ?>" onclick="" onmousedown="calculaTotal(this.id,'table_aprobado_hotel','h_total_pesos','total_pesos_hotel'); eliminaHotel(<?PHP echo $datosAux["h_hotel_id"]?>,<?PHP echo $datosAux["svi_id"]?>);borrarRenglon(this.id,'table_aprobado_hotel','rowCountHotelAprovado','rowDelHotelAprovado','num_hotel','','elimHotel'); inicializarValorHotel(); "  style='cursor:pointer'/></div></td>
								<?PHP }?>
							</tr>
							<input type="hidden" name="costo_x_noche<?PHP  echo $id ?>" id="costo_x_noche<?PHP  echo $id ?>" value="" readonly="readonly" />
						<?PHP 
							$subtotal_de_hotel = floatVal($subtotal_de_hotel)+floatVal($datosAux["h_total_pesos"]);
							$total_solicitud = floatVal($subtotal_de_hotel);
						}else if($tipoUsuario == 1 || $tipoUsuario == 2 || $tipoUsuario == 3){?>
							<tr>
								<td align="center"><div id="<?PHP  echo "num_hotel".$id?>" name="<?PHP  echo "num_hotel".$id?>"><?PHP  echo $id?></div></td>
								<td align="center"><?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?></td>
								<td align="center"><?PHP  echo $datosAux["h_nombre_hotel"] ?></td>
								<td align="center"><?PHP  echo $datosAux["h_ciudad"] ?></td>
								<td align="center"><?PHP  echo $datosAux["h_comentarios"] ?></td>
								<td align="center"><?PHP  echo $datosAux["se_nombre"] ?></td>
								<td align="center"><?PHP  echo $datosAux["h_noches"] ?></td>
								<td align="center"><?PHP  echo number_format($datosAux["h_costo_noche"],2,".",",");?></td>	
								<td align="center"><?PHP  echo $datosAux["div_nombre"];?></td>						
								<td align="center"><?PHP  echo $datosAux["fecha_llegada"] ?></td>							
								<td align="center"><?PHP  echo $datosAux["fecha_salida"] ?></td>
								<td align="center"><input type="hidden" name="h_total_pesos<?PHP  echo $id ?>" id="h_total_pesos<?PHP  echo $id ?>" value="<?PHP  echo $datosAux['h_total_pesos'] ?>"/><?PHP  echo number_format($datosAux["h_total_pesos"],2,".",",");?></td>					<!--<td align="right"><input type="hidden" name="subtotal_hotel<?PHP  //echo $id ?>" id="subtotal_hotel<?PHP  //echo $id ?>" value="/*<?PHP  //echo $datosAux["svi_costo_hotel"] ?>*/" readonly="readonly" />$&nbsp;<?PHP  /*echo number_format($datosAux["svi_costo_hotel"],2,".",","); */?></td>-->
								<td align="center"><?PHP  echo $datosAux["h_no_reservacion"] ?></td>
							</tr>
							<?PHP 
								$subtotal_de_hotel = floatVal($subtotal_de_hotel)+floatVal($datosAux["h_total_pesos"]);
								$total_solicitud = floatVal($subtotal_de_hotel);
							?>
						<?PHP }else if($tipoUsuario == 5 || $tipoUsuario == 6){?>
							<tr>
								<td align="center"><div id="<?PHP  echo "num_hotel".$id?>" name="<?PHP  echo "num_hotel".$id?>"><?PHP  echo $id?></div></td>
								<td align="center"><?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?></td>
								<td align="center"><?PHP  echo $datosAux["h_nombre_hotel"] ?></td>
								<td align="center"><?PHP  echo $datosAux["h_ciudad"] ?></td>
								<td align="center"><?PHP  echo $datosAux["h_comentarios"] ?></td>
								<td align="center"><?PHP  echo $datosAux["se_nombre"] ?></td>
								<td align="center"><?PHP  echo $datosAux["h_noches"] ?></td>
								<td align="center"><?PHP  echo number_format($datosAux["h_costo_noche"],2,".",",");?></td>
								<td align="center"><?PHP  echo $datosAux["div_nombre"];?></td>
								<td align="center"><?PHP  echo $datosAux["fecha_llegada"] ?></td>							
								<td align="center"><?PHP  echo $datosAux["fecha_salida"] ?></td>
								<td align="center"><input type="hidden" name="h_total_pesos<?PHP  echo $id ?>" id="h_total_pesos<?PHP  echo $id ?>" value="<?PHP  echo $datosAux['h_total_pesos'] ?>"/><?PHP  echo number_format($datosAux["h_total_pesos"],2,".",",");?></td>					<!--<td align="right"><input type="hidden" name="subtotal_hotel<?PHP  //echo $id ?>" id="subtotal_hotel<?PHP  //echo $id ?>" value="/*<?PHP  //echo $datosAux["svi_costo_hotel"] ?>*/" readonly="readonly" />$&nbsp;<?PHP  /*echo number_format($datosAux["svi_costo_hotel"],2,".",","); */?></td>-->
								<td align="center"><?PHP  echo $datosAux["h_no_reservacion"] ?></td>
							</tr>
						<?PHP 
							$subtotal_de_hotel = floatVal($subtotal_de_hotel)+floatVal($datosAux["h_total_pesos"]);
							//error_log("total ".$subtotal_de_hotel);
							$total_solicitud = floatVal($subtotal_de_hotel);
						}
						$id++;
					} ?>
			</tbody> 
		</table>
		<table id="costo_hotel" border="0" align="right">
			<tr>
				<td>&nbsp;</td>
				<td>
					<div id="total_hotel">Total:</div>
				</td>
				<td> <?PHP if($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 1 || $tipoUsuario == 2 || $tipoUsuario == 3 || $tipoUsuario == 4){?>
						<div id="total_pesos_hotel">0.00</div>
						<input type="hidden" name="TotHotel" id="TotHotel" value="<?PHP  echo $subtotal_de_hotel;?>" readonly="readonly" />
						
					 <?PHP }//else if($tipoUsuario == 4){?>	
						<!--<div id="total_pesos_hotel">0.00</div>-->
					<?PHP //}else if($tipoUsuario !=4){ ?>
						<!--<div id="total_pesos_hotel"><?PHP  //echo number_format((floatVal($subtotal_de_hotel)),2,".",",") ?></div>-->
					 <?PHP //}?>	
				</td>
				<td>
					<div>MXN</div>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<br clear="all">
		<br>

		<!-----------------------------------------------------
							Datos de Auto
		------------------------------------------------------>
		<div align="left" style="color:#003366; margin-left:3em"><strong>Datos del Auto</strong></div>
		<table id="table_aprobado_auto" class="tablesorter" cellspacing="1" style="">
			<thead>
				<tr> 
					<th width="2%">No.</th> 
					<th width="3%">No. Itinerario</th>
					<th width="5%">Empresa</th>
					<th width="3%">Tipo de auto</th>
					<th width="3%">D&iacute;as de renta</th>
					<th width="3%">Costo por d&iacute;a</th>
					<th width="4%">Divisa</th>
					<?PHP //if($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 4){?>
						<!--<th width="4%">Subtotal</th>-->
					<?PHP //}?>	
					<th width="4%">Total MXN </th>
					<?PHP  if($tipoUsuario==4 && $id_etapa !=SOLICITUD_ETAPA_APROBADA)echo "<th width='3%' align='center'>Eliminar</th>" ?>
				</tr>
				</thead> 
				<tbody>
				<?PHP  
					$aux = array();
					$query = "SELECT svi_id,
								svi_renta_auto,
								svi_empresa_auto,
								svi_tipo_auto,
								svi_dias_renta,
								svi_costo_auto,
								svi_total_auto,
								svi_total_pesos_auto,
								svi_renta_auto,
								svi_divisa_auto,
								svi_renta_auto_agencia,
								sv_observaciones,
								svi_solicitud, 
								se_nombre, 
								div_nombre
								FROM sv_itinerario AS svi
								INNER JOIN solicitud_viaje AS sv ON (sv.sv_id = svi.svi_solicitud) 
								INNER JOIN servicios AS se ON (se.se_id = svi.svi_tipo_auto) 
								INNER JOIN divisa AS dv ON (dv.div_id = svi.svi_divisa_auto)
								AND svi.svi_renta_auto = 1 
								WHERE svi.svi_solicitud = {$id_Solicitud}
								AND svi_empresa_auto <> '' ";
					//error_log($query);
					$rst = $cnn->consultar($query);
					while($datos=mysql_fetch_assoc($rst)){
						array_push($aux,$datos);
					}
					$subtotal_de_auto=0;
					$otros_cargo=0;
					$id=1;
					$i=1;
					$total2 =0;
					foreach($aux as $datosAux){
						if($tipoUsuario == 4 && ($datosAux["svi_renta_auto"] == "1" && $datosAux["svi_renta_auto_agencia"] == "1") && $datosAux["svi_empresa_auto"] != ""){
						
						?>
							<tr>
								<td align="center"><div id="<?PHP  echo "num_auto".$i?>" name="<?PHP  echo "num_auto".$i?>"><?PHP  echo $i?></div></td>
								<td align="center"><?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?><input type="hidden" name="id_itinerario_auto<?PHP  echo $i ?>" id="id_itinerario_auto<?PHP  echo $datosAux["svi_id"] ?>" value="<?PHP  echo $datosAux["svi_id"] ?>" readonly="readonly" /></td>
								<td align="center"><input type="hidden" name="empresa_auto<?PHP  echo $i ?>" id="empresa_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_empresa_auto"] ?>" readonly="readonly" /><?PHP  echo $datosAux["svi_empresa_auto"] ?></td>
								<td align="center"><input type="hidden" name="tipo_de_auto<?PHP  echo $i ?>" id="tipo_de_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_auto"] ?>" readonly="readonly" /><?PHP echo $datosAux["se_nombre"]; ?></td>
								<td align="center"><input type="hidden" name="dias_de_renta<?PHP  echo $i ?>" id="dias_de_renta<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_dias_renta"] ?>" readonly="readonly" /><?PHP  echo $datosAux["svi_dias_renta"] ?></td>
								<td align="center"><input type="hidden" name="costo_x_dia<?PHP  echo $i ?>" id="costo_x_dia<?PHP  echo $i ?>" value="<?PHP  echo number_format($datosAux["svi_costo_auto"],2,".",","); ?>" readonly="readonly" />&nbsp;<?PHP  echo number_format($datosAux["svi_costo_auto"],2,".",","); ?></td>
								<td align="center"><input type="hidden" name="divisaAuto<?PHP  echo $i ?>" id="divisaAuto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_divisa_auto"]; ?>" readonly="readonly" /><?PHP  echo $datosAux["div_nombre"]; ?></td>							
								<td align="center"><input type="hidden" name="svi_total_pesos_auto<?PHP  echo $i ?>" id="svi_total_pesos_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_total_pesos_auto"] ?>" readonly="readonly" /><?PHP  echo number_format($datosAux["svi_total_pesos_auto"],2,".",",");?></td>
								<?PHP  if($id_etapa != SOLICITUD_ETAPA_APROBADA){ ?>
									<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' id="<?PHP echo $i."elimAuto" ?>" name="<?PHP echo $i."elimAuto" ?>" onmousedown="calculaTotal(this.id,'table_aprobado_auto','svi_total_pesos_auto','total_pesos_auto');borrarRenglon(this.id,'table_aprobado_auto','rowCountAutoAprovado','rowDelAutoAprovado','num_auto','','elimAuto');eliminaAuto(<?PHP echo $datosAux["svi_id"]?>,this.id);  "  style='cursor:pointer'/></div></td>
								<?PHP  } ?>
							</tr>
							<?PHP 
								$subtotal_de_auto = floatVal($subtotal_de_auto)+floatVal($datosAux["svi_total_pesos_auto"]);								
								$total_solicitud =  floatVal($total_solicitud)+floatVal($datosAux["svi_total_pesos_auto"]);
						}else if(($tipoUsuario == 1 || $tipoUsuario == 2 || $tipoUsuario == 3) && $datosAux["svi_empresa_auto"] != ""){ ?>
							<tr>
								<td align="center"><?PHP  echo $id ?></td>
								<td align="center"><?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?><input type="hidden" name="id_itinerario_auto<?PHP  echo $i ?>" id="id_itinerario_auto<?PHP  echo $datosAux["svi_id"] ?>" value="<?PHP  echo $datosAux["svi_id"] ?>" readonly="readonly" /></td>
								<td align="center"><input type="hidden" name="empresa_auto<?PHP  echo $i ?>" id="empresa_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_empresa_auto"] ?>" readonly="readonly" /><?PHP  echo $datosAux["svi_empresa_auto"] ?></td>
								<td align="center"><input type="hidden" name="tipo_de_auto<?PHP  echo $i ?>" id="tipo_de_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_auto"] ?>" readonly="readonly" /><?PHP echo $datosAux["se_nombre"];?></td>
								<td align="center"><input type="hidden" name="dias_de_renta<?PHP  echo $i ?>" id="dias_de_renta<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_dias_renta"] ?>" readonly="readonly" /><?PHP  echo $datosAux["svi_dias_renta"] ?></td>
								<td align="center"><input type="hidden" name="costo_x_dia<?PHP  echo $i ?>" id="costo_x_dia<?PHP  echo $i ?>" value="<?PHP  echo number_format($datosAux["svi_costo_auto"],2,".",","); ?>" readonly="readonly" />&nbsp;<?PHP  echo number_format($datosAux["svi_costo_auto"],2,".",","); ?></td>
								<td align="center"><input type="hidden" name="divisaAuto<?PHP  echo $i ?>" id="divisaAuto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_divisa_auto"]; ?>" readonly="readonly" /><?PHP  echo $datosAux["div_nombre"]; ?></td>	
								<td align="center"><input type="hidden" name="svi_total_pesos_auto<?PHP  echo $i ?>" id="svi_total_pesos_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_total_pesos_auto"] ?>" readonly="readonly" /><?PHP  echo number_format($datosAux["svi_total_pesos_auto"],2,".",","); ?></td>
								
							</tr>
						<?PHP 
							$subtotal_de_auto = floatVal($subtotal_de_auto)+floatVal($datosAux["svi_total_pesos_auto"]);							
							$total_solicitud =  floatVal($total_solicitud)+floatVal($datosAux["svi_total_pesos_auto"]);
						}else if(($tipoUsuario == 5 && $datosAux["svi_empresa_auto"] != "") || ($tipoUsuario == 6 && $datosAux["svi_empresa_auto"] != "")){ ?>
							<tr>
								<td align="center"><div id="<?PHP  echo "num_auto".$i?>" name="<?PHP  echo "num_auto".$i?>"><?PHP  echo $i?></div></td>
								<td align="center"><?PHP  echo getNoItinerario($auxItinerarios,$datosAux["svi_id"]) ?><input type="hidden" name="id_itinerario_auto<?PHP  echo $i ?>" id="id_itinerario_auto<?PHP  echo $datosAux["svi_id"] ?>" value="<?PHP  echo $datosAux["svi_id"] ?>" readonly="readonly" /></td>
								<td align="center"><input type="hidden" name="empresa_auto<?PHP  echo $i ?>" id="empresa_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_empresa_auto"] ?>" readonly="readonly" /><?PHP  echo $datosAux["svi_empresa_auto"] ?></td>
								<td align="center"><input type="hidden" name="tipo_de_auto<?PHP  echo $i ?>" id="tipo_de_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_tipo_auto"] ?>" readonly="readonly" /><?PHP echo $datosAux["se_nombre"];?></td>
								<td align="center"><input type="hidden" name="dias_de_renta<?PHP  echo $i ?>" id="dias_de_renta<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_dias_renta"] ?>" readonly="readonly" /><?PHP  echo $datosAux["svi_dias_renta"] ?></td>
								<td align="center"><input type="hidden" name="costo_x_dia<?PHP  echo $i ?>" id="costo_x_dia<?PHP  echo $i ?>" value="<?PHP  echo number_format($datosAux["svi_costo_auto"],2,".",","); ?>" readonly="readonly" />&nbsp;<?PHP  echo number_format($datosAux["svi_costo_auto"],2,".",","); ?></td>							
								<td align="center"><input type="hidden" name="divisaAuto<?PHP  echo $i ?>" id="divisaAuto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_divisa_auto"]; ?>" readonly="readonly" /><?PHP  echo $datosAux["div_nombre"]; ?></td>	
								<td align="center"><input type="hidden" name="svi_total_pesos_auto<?PHP  echo $i ?>" id="svi_total_pesos_auto<?PHP  echo $i ?>" value="<?PHP  echo $datosAux["svi_total_pesos_auto"] ?>" readonly="readonly" /><?PHP  echo number_format($datosAux["svi_total_pesos_auto"],2,".",","); ?></td>
							</tr> 
					<?PHP 
							$subtotal_de_auto = floatVal($subtotal_de_auto)+floatVal($datosAux["svi_total_pesos_auto"]);
							$total_solicitud =  floatVal($total_solicitud)+floatVal($datosAux["svi_total_pesos_auto"]);
						}
						$id++;
						$i++;
					}?>	
			</tbody> 
		</table>
		<table id="costo_auto" border="0" align="right">
			<tr>
				<td>&nbsp;</td>
				<td>
					<div id="total_auto">Total:</div>
				</td>
				<td>
					<?PHP if($tipoUsuario == 5 || $tipoUsuario == 6 || $tipoUsuario == 1 || $tipoUsuario == 2 || $tipoUsuario == 3 || $tipoUsuario == 4){ ?>
						<div id="total_pesos_auto">0.00</div>
						<input type="hidden" name="TotAuto" id="TotAuto" value="<?PHP  echo $subtotal_de_auto; ?>" readonly="readonly" />
					<?PHP }?>
				</td>
				<td>
					<div>MXN</div>
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<?PHP  
		 if($tipoUsuario != 4 ){		
					 	
		//muestra detalle de conceptos
			$conceptos = "SELECT ccbmw.cp_concepto,
				svcd.svc_detalle_monto_concepto AS svc_detalle_monto_concepto,
				SUM(svcd.svc_conversion) AS svc_conversion				
				FROM sv_conceptos_detalle AS svcd
				INNER JOIN solicitud_viaje AS sv
				ON sv.sv_tramite = svcd.svc_detalle_tramite
				INNER JOIN tramites AS t
				ON sv.sv_tramite = t.t_id
				INNER JOIN cat_conceptos AS ccbmw
				ON svcd.svc_detalle_concepto = ccbmw.cp_id				
				WHERE t.t_id = '{$t_id}'
				GROUP BY ccbmw.cp_id 
				ORDER BY ccbmw.cp_concepto";
			$resultado=$cnn->consultar($conceptos);
			$filas = mysql_num_rows($resultado);
			if($filas != 0){ ?>
			<center>
			<div align="center" style="width:60%">
			<br />
			<h3>El empleado ha solicitado los siguientes anticipos</h3>
			<table class="tablesorter" id="excepcion_table">
				<thead>
					<tr>
						<th>No.</th>
						<th>No. Itinerario</th>
						<th>Concepto</th>
						<th>Anticipo</th>
						<th>Excepci&oacute;n</th>
						<th>Pol&iacute;tica</th>
						<th>Excedente</th>
						<th>Tipo de Excepci&oacute;n</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			 <?php
			 $total = 0;
			 $svc_dmc = 0;
			 $i =1;				 
			 while($fila=mysql_fetch_assoc($resultado)){
				$svc_dmc = $fila["svc_conversion"];
				$svc_cp_concepto = $fila["cp_concepto"];
				$total = $total + floatval($svc_dmc);
				
				if($svc_cp_concepto != "Hotel" && $svc_cp_concepto != "Renta de auto") $total2 = $total2 + floatval($svc_dmc);
			} ?>
			<div width="60%" align="right">
				Total anticipo:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?PHP echo number_format($total,2,".",",")." MXN";?>&nbsp;&nbsp;
			</div>
			<br>
			</div>
			</center>
			<?PHP 
		}else{
		   echo "<br /><h3>El empleado no solicit&oacute; ning&uacute;n anticipo</h3>";
		} ?>
		<table align="right">
			<tr>
				<?PHP if($tipoUsuario == 5 || $tipoUsuario == 6){?>
				<td>
					<div style="display:none">Centro de costos:</div>
				</td>
				<td>
					<?PHP 
						$cnn = new conexion();
						$idusuario=$_SESSION["idusuario"];
						$query = sprintf("SELECT cc.cc_id AS 'id',
								cc.cc_centrocostos AS 'cc',
								cc.cc_nombre AS 'nombre'
								FROM cat_cecos AS cc 
								WHERE cc_estatus = '1'
								AND cc_empresa_id = '".$_SESSION["empresa"]."' 
								ORDER BY cc.cc_centrocostos");
						$rst = $cnn->consultar($query);
						$fila = mysql_num_rows($rst);
					?>
					<select name="centro_de_costos_new" id="centro_de_costos_new" onchange="" style="display:none">
								<option id="-1" value="-1">Seleccione...</option>
								<?PHP 
								if($fila>0){
									while($fila=mysql_fetch_assoc($rst)){
										echo "<option id=".$fila["id"]." value=".$fila["id"].">".$fila["cc"]."-".$fila["nombre"]."</option>";
									}
								}else{
									echo "<option id='n' value='n'>No hay centro de costos</option>";
								}
							?>
				</select>
					<input type="hidden" id="centro_de_costos_old" name="centro_de_costos_old" value="<?PHP  echo $CentroCostoId; ?>"/>               
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?PHP }?>
				<td>
					<div id="total_sol" align="right">
						<strong>Total solicitud:</strong>
					</div>
			   </td>
			   <td>
			   		<?PHP
					$totalSolRed = (floatVal($subtotal_de_hotel)+floatVal($subtotal_de_auto))+floatVal($total)+floatval($totalAvionCotizado); 
					?>
					<div align="right">
						<strong><?PHP  echo number_format($totalSolRed,2,".",","); //echo number_format($sv_total_viaje,2,".",",");?> MXN</strong>
						<input type="hidden" id="totalSolicitud" name="totalSolicitud" value="<?PHP  echo number_format($totalSolRed,2,".",",");?>" size="10" readonly="readonly" />
					</div>
			  </td>
				<td>
					&nbsp;
				</td>
			</tr>			
		</table>
		<br/>
		<?PHP  } ?>
		<br clear="all">
		<br>
		<?PHP 
		$idTramite=$_GET['id'];
		$tramites = new Tramite();
		$tramites->Load_Tramite($idTramite);
		$t_iniciador=$tramites->Get_dato("t_iniciador");
		$t_dueno=$tramites->Get_dato("t_dueno");
		?>
		<div id="observaciones_para_empleado">
			<table border="0" width="62%">
				<?PHP if(($tipoUsuario != 4 && $tipoUsuario != 7 && $tipoUsuario != 8)){?>
					<tr>
						<td width="5px">&nbsp;</td>
						<td colspan="3">Historial de observaciones: </td>
						<td colspan="3">
						<?PHP  
						$obsAE="";
						$ObsSql="";
						$aux=array();
						
						if($t_etapa_actual != 4 && $t_etapa_actual != 8){
							$queryObserv = sprintf("SELECT sv_observaciones FROM solicitud_viaje WHERE sv_tramite = '%s'", $idTramite);
							$rst = $cnn->consultar($queryObserv);
							$fila = mysql_fetch_assoc($rst);
							$obsAE = $fila["sv_observaciones"];
						}else{
							$tramiteHO =new Tramite();
							$tramiteHO->Load_Tramite($t_id);					
							$iniciador=$tramiteHO->Get_dato("t_iniciador");
							
							$agrup_usu = new AgrupacionUsuarios();
							$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Agencia");
							$agencia=$agrup_usu->Get_dato("au_id");
							
							$rst=$agrup_usu->Load_Homologacion_Usuarios($agencia);					
							while($arre=mysql_fetch_assoc($rst)){
								array_push($aux,$arre);
							}				
							//realizamos el query para la excepcion de agencia y empleado.
							foreach($aux as $datosAux){
										$ObsSql.="AND ob_usuario <>'".$datosAux['hd_u_id']."' ";														
							}					
							
							$query_Concatenacion=sprintf("SELECT ob_texto,ob_usuario FROM observaciones WHERE ob_tramite=%s AND ((ob_usuario <> '%s'".$ObsSql.")) AND ob_texto <> '' ORDER BY ob_fecha DESC",$t_id,$iniciador);
							//error_log("->>>>>>>".$query_Concatenacion);
							$rst_Con=$cnn->consultar($query_Concatenacion);
							$duenoActual01 = new Usuario();
							while($fila=mysql_fetch_assoc($rst_Con)){						
								if($duenoActual01->Load_Usuario_By_ID($fila['ob_usuario'])){
									$dueno_act_nombre = $duenoActual01->Get_dato('nombre');
								}else{
									$agrup_usu = new AgrupacionUsuarios();
									$agrup_usu->Load_Grupo_de_Usuario_By_ID($fila['ob_usuario']);
									$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
								}
								
								$obsAE.=$dueno_act_nombre.": ".$fila['ob_texto']."\n";
							}
						}?>
							<textarea name='campo_historial' id='campo_historial' rows='5' cols='130' readonly="readonly" onkeypress="confirmaRegreso('campo_historial');" onkeydown="confirmaRegreso('campo_historial');" style="resize:both;"><?PHP  echo $obsAE; ?></textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
				<?PHP   } ?>
				<?PHP  if(($t_etapa_actual <= 3 || $t_etapa_actual == 5 || $t_etapa_actual == 8 || $t_etapa_actual == 7 || $t_etapa_actual == 10 || $t_etapa_actual == 11) && ($tipoUsuario == 1 || $tipoUsuario == 3 || $tipoUsuario == 4 || $tipoUsuario == 5 || $tipoUsuario == 6) || $tipoUsuario == 9){?>
				<tr>
					<td width="5px">&nbsp;</td>
					<td colspan="3"><div id="observ">Observaciones: </div></td>
					<td colspan="3">
						<?PHP
						/* No se imprimirán las observaciones para estandarizar que las mismas no se muestren 
						 * en los cuatro flujos.
						 */
						if($t_etapa_actual == 8 && $t_dueno == 3000){
							$query = sprintf("SELECT ob_texto AS ob_texto FROM observaciones WHERE ob_tramite = '%s' ORDER BY ob_fecha DESC",$t_id);
						}else{
							$query = sprintf("SELECT sv_observaciones AS ob_texto FROM solicitud_viaje WHERE sv_tramite = '%s'",$t_id);
						}
						$rst = $cnn->consultar($query);
						$fila = mysql_fetch_assoc($rst);
						$co_observaciones = $fila['ob_texto'];?>
						<textarea name="observ_to_emple" id="observ_to_emple" rows="7" cols="130" ></textarea>
					</td>
					<td>&nbsp;</td>
				</tr>
				<?PHP  } ?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>

	<br>
	<!--<center><b>Anticipo Solicitado</b></center>-->
	<table id="totales" width="30%" border="0" style="display:none">  

		<?PHP  if($divisa!='MXN') { ?>
		<tr>
			<td align='right'>&nbsp;</td>  
			<td align='right'><b>En d&oacute;lares:</b></td>
			<td align='right'><b>En pesos</b></td>
		</tr>    
		<?PHP  } ?>
	 
		<tr>
			<td align='right'><b>Subtotal:</b></td>
			<td align='right'><?PHP  echo "$ " . $monto . " " . $divisa; ?></div></td>  
			<?PHP  if($divisa!='MXN') { ?>
				<td align='right'><?PHP  echo "$ " . $monto * $tasa . " MXP"; ?></div></td>  
			<?PHP  } ?>
		</tr>
		<tr>
			<td align='right'><b>IVA:</b></td>
			<td align='right'><?PHP  echo "$ 0.0 " . $divisa; ?></div></td>  
			<?PHP  if($divisa!='MXP') { ?>
				<td align='right'><?PHP  echo "$ 0.0 MXP"; ?></div></td>  
			<?PHP  } ?>        
		</tr>
		<tr>
			<td align='right'><b>Total:</b></td>
			<td align='right'><?PHP  echo "$ " . $monto . " " . $divisa; ?></div></td>  
			<?PHP  if($divisa!='MXN') { ?>
				<td align='right'><?PHP  echo "$ " . $monto * $tasa . " MXN"; ?></div></td>  
			<?PHP  } ?>        
		</tr>      
	</table>
	<br>
	<table border="0"> 
		<tr>
			<td colspan="10"><br />
			  <div align="center"> 
			  <?PHP  $idTramite=$_GET['id'];
					$tramites = new Tramite();
					$tramites->Load_Tramite($idTramite);
					$t_iniciador=$tramites->Get_dato("t_iniciador");
					$t_dueno=$tramites->Get_dato("t_dueno");
					$t_ruta_autorizacion = $tramites->Get_dato("t_ruta_autorizacion");
					if(isset($_SESSION['iddelegado'])){
						$usuario = $_SESSION['iddelegado']; 
					}else{
						$usuario = $_SESSION["idusuario"];
					}
					
					$encontrado = encuentraAutorizador($t_ruta_autorizacion, $usuario);
					
					$privilegios = 0;
					$delegadodeUsuario = 0;
					if(isset($_SESSION['iddelegado'])){
						$query = sprintf("SELECT privilegios FROM usuarios_asignados WHERE id_asignador = '%s' AND id_delegado = '%s'", $_SESSION["idusuario"], $_SESSION['iddelegado']);
						$rst = $cnn->consultar($query);
						$fila = mysql_fetch_assoc($rst);
						$privilegios = $fila["privilegios"];
						//error_log($query);
						
						$query = sprintf("SELECT id_asignador FROM usuarios_asignados JOIN tramites ON t_iniciador = id_delegado WHERE t_id = '%s'", $idTramite);
						//error_log($query);
						$rst = $cnn->consultar($query);
						
						while($fila = mysql_fetch_assoc($rst)){
							if($fila["id_asignador"] == $_SESSION["idusuario"]){
								$delegadodeUsuario = 1;
								break;
							}
						}
					}
				?>
				<?PHP if(isset($_GET['edit_view']) && $id_etapa == SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR && !isset($_SESSION['iddelegado'])){?>
					<input type="submit" value="    Aprobar" id="aprobar_sv" name="aprobar_sv"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()" />
					<input type="submit" value="    Rechazar" id="rechazar_sv" name="rechazar_sv" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
				<?PHP }else if(isset($_GET['edit_view']) && $id_etapa == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR){?>
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()" />
				<?PHP }else if(isset($_GET['view']) && $tipoUsuario == 2){ // Perfil del Administrador ?> 
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>&nbsp;
				<?PHP }else if($tipoUsuario == 4){?>
					<?PHP if(($id_etapa == SOLICITUD_ETAPA_AGENCIA) || ($id_etapa == SOLICITUD_ETAPA_EN_COTIZACION ) || ($id_etapa == SOLICITUD_ETAPA_COTIZADA)){?>					
						<input type="submit" name="devolver_empleado" id="devolver_empleado" value="     Devolver a empleado" style="background:url(../../images/Arrow_Left.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="cotizaAvionRow();"/>
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<?PHP }else if($id_etapa == SOLICITUD_ETAPA_APROBADA){ ?>							
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; margin-left:127px;" onclick="Location()"/>
					<input type="button"   value="   Confirmar compra"  name="confCompra" id="confCompra" style="background:url(../../images/toolbar/icons/cart.png); background-repeat:no-repeat; background-color:#E1E4EC; margin-left:auto; visibility:hidden;" onclick="validarObservaciones(<?PHP echo $_GET['id'];?>,<?PHP echo $idusuario; ?>);" />
					<input type="button"   value="   Cancelar"  name="cancelarCambios" id="cancelarCambios" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; margin-left:auto; visibility:hidden;" onclick="calcelaActualizacion(<?php echo $id_Solicitud?>);" />
					<?PHP  }?>
				<!--duda sobre la pantalla de consulta y la de gerenteDirector-->
				<?PHP }else if($tipoUsuario == 5 || $tipoUsuario == 6){?> <!-- Controlling y Finanzas -->
					<input type="submit" value="    Autorizar" id="autorizar" name="autorizar"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/" />
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<input type="submit" value="    Rechazar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
				<?PHP }elseif((($tipoUsuario == 1 || $tipoUsuario == 3) && $id_etapa == SOLICITUD_ETAPA_EN_COTIZACION && $tipoTransporteB != "Terrestre")){
					if($t_dueno != $t_iniciador){ ?>					
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
						<input type="submit" value="    Cancelar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
					<?PHP  }else{?>
						<input type="submit" name="devolver_to_emple" id="devolver_to_emple" value="     Devolver a agencia con observaciones" style="background:url(../../images/Arrow_Left.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="" />
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
						<input type="submit" value="    Cancelar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
					<?PHP  }?>	
				<?PHP }else if((($tipoUsuario == 1 || $tipoUsuario == 3) && $id_etapa == SOLICITUD_ETAPA_COTIZADA && $tipoTransporteB != "Terrestre")){ ?>
				<?php 
					$nombreBoton = "    Aprobar cotización";
					if(isset($_SESSION['iddelegado'])){
                		$nombreBoton = "    Enviar a Director";
               		}?>
					<input type="submit" value="<?php echo $nombreBoton;?>" id="autorizar_cotizacion" name="autorizar_cotizacion"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/" />
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<input type="submit" name="devolver_to_emple" id="devolver_to_emple" value="     Devolver a agencia con observaciones" style="background:url(../../images/Arrow_Left.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="" />
					<input type="submit" value="    Cancelar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
				<?PHP }elseif((($tipoUsuario == 1 || $tipoUsuario == 3) && $tipoTransporteB == "Terrestre" && $id_etapa == SOLICITUD_ETAPA_COTIZADA)){?>
					<input type="submit" value="    Aprobar cotizacion" id="autorizar_cotizacion" name="autorizar_cotizacion"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/" />
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<input type="submit" name="devolver_to_emple" id="devolver_to_emple" value="     Devolver a agencia con observaciones" style="background:url(../../images/Arrow_Left.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="" />
					<input type="submit" value="    Cancelar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>           
				<?php }elseif((($tipoUsuario == 1 || $tipoUsuario == 3) && $tipoTransporteB == "Terrestre" && $id_etapa == SOLICITUD_ETAPA_EN_COTIZACION)){
					if($t_dueno != $t_iniciador){ ?>					
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
				<?PHP }else{?>
						<input type="submit" name="devolver_to_emple" id="devolver_to_emple" value="     Devolver a agencia con observaciones" style="background:url(../../images/Arrow_Left.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="" />	
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>					
						<input type="submit" value="    Cancelar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
					<?PHP }?>        					
				<?PHP }elseif((($tipoUsuario == 1 || $tipoUsuario == 3) && $id_etapa == SOLICITUD_ETAPA_AGENCIA) || (($tipoUsuario == 1 || $tipoUsuario == 3) && $id_etapa == SOLICITUD_ETAPA_SIN_ENVIAR) ||(($tipoUsuario == 1 || $tipoUsuario == 3) && $id_etapa == SOLICITUD_ETAPA_CANCELADA) ||(($tipoUsuario == 1 || $tipoUsuario == 3) && $id_etapa == SOLICITUD_ETAPA_RECHAZADA)|| ($_SESSION["idusuario"] == $t_iniciador && $id_etapa == SOLICITUD_ETAPA_EN_APROBACION) || ($_SESSION["idusuario"] == $t_iniciador && $id_etapa == SOLICITUD_ETAPA_APROBADA) || ($_SESSION["idusuario"] == $t_iniciador && $id_etapa == SOLICITUD_ETAPA_SEGUNDA_APROBACION) || ($_SESSION["idusuario"] == $t_iniciador && $id_etapa == SOLICITUD_ETAPA_COMPRADA)){?>
					<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>            	
				<?PHP }else if(($_SESSION["idusuario"] != $t_iniciador && $id_etapa == SOLICITUD_ETAPA_EN_APROBACION) || ($_SESSION["idusuario"] != $t_iniciador && $id_etapa == SOLICITUD_ETAPA_SEGUNDA_APROBACION) || (isset($_SESSION['iddelegado']))){ // <!-- -Autorizaciones -->
					if((isset($_SESSION['iddelegado']) && $privilegios == 1 && $delegadodeUsuario == 1)){ // Si la Sesión del delegado se ha iniciado, y el asignador tiene privilegios de autorizar, se deberán mostrar los botones de Autorizar y Rechazar ?> 
						<input type="submit" value="    Autorizar" id="autorizar" name="autorizar"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/" />
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
						<input type="submit" value="    Rechazar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
					<?PHP  }else if((isset($_SESSION['iddelegado']) && $delegadodeUsuario == 1)){ // Si la Sesión del delegado se ha iniciado, y el asignador no tiene privilegios de autorizar, se deberá mostrar el botón de Volver ?>
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<?PHP  }else if((isset($_SESSION['iddelegado']) && $privilegios == 0 && $delegadodeUsuario == 0)){ // Si la Sesión del delegado se ha iniciado, y el asignador no tiene privilegios de autorizar, se deberá mostrar el botón de Volver ?>
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<?PHP  }else{ // Es el aprobador quien podrá Autorizar o Rechazar la Solicitud ?>
						<input type="submit" value="    Autorizar" id="autorizar" name="autorizar"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/" />
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
						<input type="submit" value="    Rechazar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; /*visibility:hidden*/"/>
					<?PHP  }
					}else if(isset($_GET['hist'])){ ?>
						<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
					<?php 
					}
				//o aprobada
				$sql = "SELECT t_iniciador, t_dueno, t_etapa_actual FROM tramites WHERE t_id = ".$_GET['id'];
				$res = $cnn->consultar($sql);
				$row = mysql_fetch_assoc($res);	
				if($row['t_etapa_actual'] != SOLICITUD_ETAPA_AGENCIA && $row['t_etapa_actual'] != SOLICITUD_ETAPA_EN_COTIZACION && $row['t_etapa_actual'] != SOLICITUD_ETAPA_SIN_ENVIAR && ($tipoUsuario != 4)){ ?>
					<input type="button" value="     Imprimir"  name="Imprimir" id="Imprimir" style="background:url(../../images/icon_Imprimir.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="imprimir_pdf(<?PHP echo $_GET["id"]; ?>);"/>
				<?PHP } ?>				
				<input type="hidden" id="idT" name="idT" value="<?PHP  echo $_GET['id']?>" readonly="readonly" />
				<input type="hidden" name="iu" id="iu" readonly="readonly" value="<?PHP echo $_SESSION["idusuario"];  ?>" />
				<input type="hidden" name="ne" id="ne" readonly="readonly" value="<?PHP echo $_SESSION["idusuario"];  ?>" />
				<!--<input type="hidden" id="cont_itinerarios" name="cont_itinerarios" value="<?PHP  //echo $cont?>" readonly="readonly"/>-->
				<input type="hidden" id="tasa" name="tasa" value="<?PHP  echo $tasa;?>" readonly="readonly"/>
				<input type="hidden" name="tramite" id="tramite" readonly="readonly" value="<?PHP echo $_GET['id'];?>" />
				<input type="hidden" name="rowCountHotelAprovado" id="rowCountHotelAprovado" readonly="readonly" value="" />
				<input type="hidden" name="rowDelHotelAprovado" id="rowDelHotelAprovado" readonly="readonly" value="" />
				<input type="hidden" name="rowCountAutoAprovado" id="rowCountAutoAprovado" readonly="readonly" value="" />
				<input type="hidden" name="rowDelAutoAprovado" id="rowDelAutoAprovado" readonly="readonly" value="" />
				<input type="hidden" name="rowDelAvion" id="rowDelAvion" readonly="readonly" value="" />
				<input type="hidden" name="rowDelAuto" id="rowDelAuto" readonly="readonly" value="" />
				<input type="hidden" name="rowDelHotel" id="rowDelHotel" readonly="readonly" value="" />
				<input type="hidden" name="rowDelAutoEmpleado" id="rowDelAutoEmpleado" readonly="readonly" value="" />
				<input type="hidden" name="rowDelHotelEmpleado" id="rowDelHotelEmpleado" readonly="readonly" value="" />				
				<input type="hidden" name="banderaHotel" id="banderaHotel" readonly="readonly" value="" />
				<input type="hidden" name="hotelesIngresados" id="hotelesIngresados" readonly="readonly" value="" />
				<input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?PHP  if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
				<input type="hidden" name="delegadoNombre" id="delegadoNombre" readonly="readonly" value="<?PHP  if(isset($_SESSION['iddelegado'])){ echo $_SESSION['delegado']; }else{echo 0;}?>" />
				<!---------------------------------------------------------------------->
				<div id="tipo_explorador" style="display:none"></div>
				<input type="hidden" id="itinerarioActualOPosible" name="itinerarioActualOPosible" value="1" readonly="readonly"/>
				<div id="area_counts_div"></div>
				<input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
				<input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
				<input type="hidden" name="tasaUSD" id="tasaUSD" value="<?PHP  echo $div_USD;?>" readonly="readonly" />
                <input type="hidden" name="tasaEUR" id="tasaEUR" value="<?PHP  echo $div_EUR;?>" readonly="readonly" />
                <input type="hidden" name="flujoId" id="flujoId" value="<?PHP echo FLUJO_SOLICITUD;?>" readonly="readonly" />
                <input type="hidden" name="t_iniciador" id="t_iniciador" value="<?PHP echo $t_iniciador;?>" readonly="readonly" />
			  </div>
		  </td>				 
		</tr>
	</table>	
	<div name = "datosAvion" id="datosAvion" style="display:none; visibility:hidden;">
	<!-- Guardaremos en esta capa los datos de la cotización anterior-->
		<table>
			<tr>
				<td><input type="text" id="solicitud" name="solicitud" value="<?php echo $id_Solicitud?>" readonly="readonly" /></td>
				<td><input type="text" id="tipo_viaje" name="tipo_viaje" value="<?php echo $tipo_viaje?>" readonly="readonly" /></td>
				<td><input type="text" id="tipoViaje" name="tipoViaje" value="<?php echo $tipoViaje?>" readonly="readonly" /></td>
				<td><input type="text" id="montoVueloTotalCotizado" name="montoVueloTotalCotizado" value="<?php echo $montoVueloTotalCotizado?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<!-- Fin de guardado-->
	<!-- Ventana emergente para La cotizacion de vuelos de avion -->
	<!--<<<<TABLE QUE FUNCIONA COMO FLOTANTE PARA LA COTIZACION DE VUELOS DE AVION >>>>>>>>>>-->
		<div name = "ventanita_avion" id="ventanita_avion" style="display:none; visibility:hidden; position:absolute; top:50%; left:50%; width:600px; margin-left:-350px; height:; margin-top:; border:1px solid #808080; padding:5px; background-color:#F0F0FF">
			<table bordercolor="#333333" style="font-size:11px" border="0" width="100%">
				<tr bgcolor="#8C8CFF">
					<td colspan="8">
						<font color="#ffffff"><strong>&nbsp;&nbsp;&nbsp;Cotización de boletos de avión</strong></font>
					</td>
					<td width="16px">
						<img src="../../images/close2.ico" alt="Cerrar" align="right" onclick="flotanteActive3(0);" style="cursor:pointer"/>
					</td>
				</tr>
			</table>
			<table bordercolor="#333333"  align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
				<table width="600" align="center" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
						<?PHP  ?>
						<tr  style="text-align:center;">
							<td>&nbsp;</td>
							<td>
								<div align="right">
									Costo<span class="style1">*</span>: 
								</div>
							</td>
							<td>
								<div align="left">
									<input name="costoAvion" type="text" id="costoAvion" value="" size="10" maxlength="100" onkeypress="validaZeroIzquierda(this.value,this.id); getCostoTotal(); return validaNum(event);" onkeyup="getCostoTotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event); " onchange="getCostoTotal();" />
								</div>
							</td>
							<td>
								<div align="right">
									Aerolínea<span class="style1">*</span>: 
								</div>
							</td>
							<td>
								<div align="left">
									<input name="aeroAvion" type="text" id="aeroAvion" value="" size="40"  onkeypress="" onkeyup=" " onchange=" " />
								</div>
							</td>
							
							<td>&nbsp;</td>
						</tr>
						<tr style="text-align:center;">
							<td>&nbsp;</td>
							<td>
								<div align="right">
									IVA<span class="style1">*</span>: 
								</div>
							</td>
							<td>
								<div align="left">
									<input name="ivaAvion" type="text" id="ivaAvion" value="" size="8" maxlength="10" onkeypress="validaZeroIzquierda(this.value,this.id); getCostoTotal(); return validaNum(event);" onkeyup=" getCostoTotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event);" onchange="getCostoTotal();" />
								</div>
							</td>
							<td>
								<div align="right">
									Tipo<span class="style1">*</span>: 
								</div>
							</td>
							<td>
								<div align="left">
									<select name="tipo" id="tipo">
									<option value="-1">Seleccione ...</option>
									<?php 
									$cnn = new conexion();
									$query = sprintf("SELECT se.se_id AS servicio_id, se_nombre
											FROM servicios AS se
											INNER JOIN bandos_servicios AS bs ON (bs.se_id = se.se_id) 
											INNER JOIN bandos AS bn ON (bn.b_id = bs.b_id) 
											INNER JOIN cat_conceptos AS cp ON (cp.cp_id = se.cp_id)
											INNER JOIN empleado AS ep ON (ep.b_id = bn.b_id) 
											INNER JOIN tramites AS tm ON (tm.t_iniciador = ep.idfwk_usuario)
											WHERE bs.f_id = %s 
											AND cp.cp_id = %s 
											AND t_id = %s", FLUJO_SOLICITUD, CONCEPTO_AVION, $_GET['id']);
									//error_log("--->>Consulta Servicios(Avión) según el flujo: ".$query);
									$rst = $cnn->consultar($query);
									while ($fila = mysql_fetch_assoc($rst)) {
										echo  "<option value=".$fila["servicio_id"].">".$fila["se_nombre"]."</option>";
									} ?>
									</select>
								</div>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr >
							<td>&nbsp;</td>
							<td>
								<div align="right">
									TUA<span class="style1">*</span>:
								</div>
							</td>
							<td>
								<div align="left">
									<input name="tuaAvion" type="text" id="tuaAvion" value="" size="8" maxlength="10" onkeypress="validaZeroIzquierda(this.value,this.id); getCostoTotal(); return validaNum(event);"" onkeyup="getCostoTotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event);" onchange="getCostoTotal();" />
								</div>
							</td>
							
							<td>
								<div align="right">
									Tipo de viaje<span class="style1">*</span>: 
								</div>
							</td>
							<td>
								<div  align="left">
									<?PHP  
									$query="SELECT sv_viaje FROM solicitud_viaje WHERE sv_id ={$id_Solicitud}";
									$rst = $cnn->consultar($query);
									while ($fila = mysql_fetch_assoc($rst)) {								
									?>
									<input name="tipoViaje" disabled="disabled" type="text" readonly="readonly" id="tipoViaje" value="<?PHP  echo $fila['sv_viaje']?>" size="10"  onkeypress="" onkeyup=" " onchange=" "/>
									<?PHP 
									} 
									?>
								</div>
							</td>
							<td >
								
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>						
							<td>
								<div align="right">
									Costo total del viaje:
								</div>
							</td>
							<td>
								<div align="left">
									<input name="ctAvion" type="text" readonly="readonly" id="ctAvion" value="0.00" size="8" disabled="disabled" maxlength="2" />
								</div>
							</td>						
							<td>
								<div align="right" >
									Fecha salida<span class="style1">*</span>: 
								</div>
							</td>
							<td width="30%">
								<div align="left">
									<input name="salida" type="text" id="salida" value="<?PHP  echo date('d/m/Y'); ?>" size="10" maxlength="100" onchange=" " />
								</div>
							</td>						
						</tr>
						<?PHP  if($tipo_viaje == "Redondo" ){?>					
						<tr>
						<td>&nbsp;</td>			
						<td>&nbsp;</td>											
						<td>&nbsp;</td>	
						
							<td>
								<div align="right" >
									Fecha regreso<span class="style1">*</span>:
								</div>
							</td>
							<td width="30%">
								<div align="left">
									<input name="llegada" type="text" id="llegada" value="<?PHP  echo date('d/m/Y'); ?>" size="10" maxlength="100" onchange=" " />
								</div>
							</td>
						</tr>
						<?PHP  }?>					
						</br>					
						<table id="solicitud_table" class="tablesorter" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;">
							<thead> 
							</br>
							<div align="center" style="color:#003366"><strong>Destinos considerados en el viaje</strong></div>
							 <tr> 				   
								<th width="7%" align="center">No.</th>
								<th width="7%" align="center">Origen</th>
								<th width="7%" align="center">Destino</th>
								<th width="7%" align="center">Fecha de salida</th>
								<th width="7%" align="center">Fecha de llegada</th>
								<th width="7%" align="center">Tipo de transporte</th>
							 </tr>
							 <?PHP  											 	
								$aux = array();
								$query="SELECT svi_origen,svi_destino,DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') AS 'svi_fecha_salida',
								DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') AS 'svi_fecha_llegada',svi_tipo_transporte FROM sv_itinerario
								INNER JOIN solicitud_viaje
								ON sv_itinerario.svi_solicitud=solicitud_viaje.sv_id
								WHERE sv_itinerario.svi_solicitud={$id_Solicitud}";
								$rst = $cnn->consultar($query);
									while($datos=mysql_fetch_assoc($rst)){
											array_push($aux,$datos);
									}
									$i=1;									
									foreach($aux as $datosAux){
							 ?>
							 <tr>
															
								<td align="center"><?PHP  echo $i?></td>
								<td align="center"><?PHP  echo strtr(strtoupper($datosAux["svi_origen"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></td>
								<td align="center"><?PHP  echo strtr(strtoupper($datosAux["svi_destino"]),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");  ?></td>
								<td align="center"><?PHP  echo $datosAux["svi_fecha_salida"] ?></td>
								<td align="center"><?PHP  echo $datosAux["svi_fecha_llegada"] ?></td>
								<td align="center"><?PHP  echo $datosAux["svi_tipo_transporte"] ?></td>
								
							</tr>
							<?PHP 
								$i++;
								}
							?>	           				 
					   </thead> 
					   </table>		
			<br />
			<table  cellspacing="1" width="100%">			
				<tr>
					<td colspan="8">&nbsp;</td>
					<td width="20%" colspan="3">
						<div align="right">
							<input type="button" name="aceptarAvion" id="aceptarAvion" value="Aceptar"  onClick='validarDatos();' /> 
						</div>
					</td>
					<td width="20%" colspan="3">
						<div align="left">
							<input type="button" name="cancelarAvion" id="cancelarAvion" value="Volver"  onClick="flotanteActive3(0);" /> 
						</div>
					</td>
				</tr>
				<?PHP  
				//Cargara los datos correspondientes a la cotizacion
				$sql="SELECT svi_monto_vuelo, svi_iva, svi_tua,svi_monto_vuelo_cotizacion, svi_aerolinea,
					svi_tipo_aerolinea,  svi_fecha_salida_avion, svi_fecha_regreso_avion FROM sv_itinerario
					WHERE svi_solicitud={$id_Solicitud}";
				$rst = $cnn->consultar($sql);
				$fila = mysql_fetch_assoc($rst);			
				?>
				<!-- Obtener datos para la ventana flotante de cotizaciones -->
				<!-- 
				<input type="hidden" name="coAvionCot" id="coAvionCot" value="<?PHP  echo $fila['svi_monto_vuelo'];?>"/>
				<input type="hidden" name="ivaAvionCot" id="ivaAvionCot" value="<?PHP  echo $fila['svi_iva'];?>"/>
				<input type="hidden" name="tuaAvionCot" id="tuaAvionCot" value="<?PHP  echo $fila['svi_tua'];?>"/>
				<input type="hidden" name="cotAvionCot" id="cotAvionCot" value="<?PHP  echo $fila['svi_monto_vuelo_cotizacion'];?>"/>
				<input type="hidden" name="aeroAvionCot" id="aeroAvionCot" value="<?PHP  echo $fila['svi_aerolinea'];?>"/>
				<input type="hidden" name="tipoaeroAvionCot" id="tipoaeroAvionCot" value="<?PHP  echo $fila['svi_tipo_aerolinea'];?>"/>
				<?PHP  
					//Formato adecuado para la fecha de salida(Edicion)	
					//  $salidaAvionFila =explode("-",$fila['svi_fecha_salida_avion']);
					//  $salidaAvion =$salidaAvionFila[2]."/".$salidaAvionFila[1]."/".$salidaAvionFila[0];				
				?>
				<input type="hidden" name="salAvionCot" id="salAvionCot" value="<?PHP  //echo $salidaAvion;?>"/>
				<?PHP  
					//Formato adecuado para la fecha de salida(Edicion)	
					//  $llegadaAvionFila =explode("-",$fila['svi_fecha_regreso_avion']);
					//  $llegadaAvion =$llegadaAvionFila[2]."/".$llegadaAvionFila[1]."/".$llegadaAvionFila[0];				
				?>
				<input type="hidden" name="llAvionCot" id="llAvionCot" value="<?PHP  //echo $llegadaAvion; ?>"/> --> 
			</table>        
		</div>
		<!--<<<<Ventana funciona como flotante para la cotizacion de auto, hospedaje>>>>>>>>>>-->
		<div name = "ventanita_auto_hotel" id="ventanita_auto_hotel" style="display:none; visibility:hidden; position:absolute; top:50%; left:50%; width:820px; margin-left:-350px; height:; margin-top:; border:1px solid #808080; padding:5px; background-color:#F0F0FF">
			<table id="" bordercolor="#333333" style="font-size:11px" border="0" width="828">
				<tr bgcolor="#8C8CFF">
					<td colspan="6">
						<font color="#ffffff"><strong>&nbsp;&nbsp;&nbsp;Cotización de hospedaje por agencia</strong></font>
					</td>
					<td width="16px">
						<img src="../../images/close2.ico" alt="Cerrar" align="right" onclick=" cancelaOperacionHotelAgencia();flotanteActive(0);" style="cursor:pointer"/>
					</td>
				</tr>
			</table>
			<table width="828" border="0"align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
				<tr >
					<td colspan="8" ><center><h3>Datos de itinerario</h3></center></td>
				</tr>
					<td colspan="8">&nbsp;</td>
				<tr >
					<td colspan="2">&nbsp;</td>
					<td align="center">No de itinerario:</td>
					<td ><div id="no_itinerario"></div></td>
					<td >Origen:</td>
					<td ><div id="origen_label"></div></td>
					<td >Destino:</td>
					<td ><div id="destino_label"></div></td>
				</tr>
			</table>
			<table  width="828" id="tabla_hotel" name="tabla_hotel"   align="center" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
				<tr>
					<td>&nbsp;</td>
					<td colspan="8" ><center><h3>Hotel</h3></center></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="8">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr colspan="2" style="text-align:center;">
					<td>&nbsp;</td>
					<td width="20%">
						<div align="right">
							Ciudad<span class="style1">*</span>: 
						</div>
					</td>
					<td width="30%">
						<div align="left">
							<input name="ciudad" type="text" id="ciudad" value="" size=40 maxlength="100"/>
						</div>
					</td>
					<td width="20%">
						<div align="right">
							Noches<span class="style1">*</span>: 
						</div>
					</td>
					<td width="20%">
						<div align="left">
							<input name="noches" type="text" id="noches" value="" size="10" maxlength="10" disabled="disabled" readonly="readonly" />
						</div>
					</td>
					<td colspan="4">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="text-align:center;">
					<td>&nbsp;</td>
					<td>
						<div align="right">
							Hotel<span class="style1">*</span>:
						</div>
					</td>
					<td width="30%">
						<div align="left">
							<div id="hotelDiv"></div>
							<input style="display:none" name="hotel" type="text" id="hotel" value="" />
						</div>
					</td>
					<td width="20%">
						<div align="right">
							Costo por noche<span class="style1">*</span>: 
						</div>
					</td>
					<td width="20%">
						<div align="left">
							<input name="costoNoche" type="text" id="costoNoche" value="" size="15" maxlength="10" onkeypress="validaZeroIzquierda(this.value,this.id); getSubtotal(); return validaNum(event);" onkeyup="getSubtotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event);" onchange="getSubtotal();" />
						</div>
					</td>
					<td width="20%" colspan="3">
						<div align="right">
							Divisa<span class="style1">*</span>: 
						</div>
					</td>
					<td colspan="1">
						<div  align="left">
							<select name="selecttipodivisa" id="selecttipodivisa" onChange="getSubtotal(); " >
							<option value="1">MXN</option>
							<option value="2">USD</option>
							<option value="3">EUR</option>
							</select>
						</div>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="text-align:center;">
					<td>&nbsp;</td>
					<td nowrap="nowrap">
							<div align="right">
								Tipo de Hotel<span class="style1">*</span>:
							</div>
					</td>
					<td width="30%">
						<div align="left">
							<select name="tipoHotel" id="tipoHotel">
							<option value="-1">Seleccione ...</option>
							<?php 
							$cnn = new conexion();
							$query = sprintf("SELECT se.se_id AS servicio_id, se_nombre
									FROM servicios AS se
									INNER JOIN bandos_servicios AS bs ON (bs.se_id = se.se_id) 
									INNER JOIN bandos AS bn ON (bn.b_id = bs.b_id) 
									INNER JOIN cat_conceptos AS cp ON (cp.cp_id = se.cp_id)
									INNER JOIN empleado AS ep ON (ep.b_id = bn.b_id) 
									INNER JOIN tramites AS tm ON (tm.t_iniciador = ep.idfwk_usuario)
									WHERE bs.f_id = %s 
									AND cp.cp_id = %s 
									AND t_id = %s", FLUJO_SOLICITUD, CONCEPTO_HOTEL, $_GET['id']);
							//error_log("--->>Consulta Servicios(Hotel) según el flujo: ".$query);
							$rst = $cnn->consultar($query);
							while ($fila = mysql_fetch_assoc($rst)) {
								echo "<option value=".$fila["servicio_id"].">".$fila["se_nombre"]."</option>";
							} ?>
							</select>
						</div>
					</td>
					<td width="20%">
						<div align="right">
							Subtotal<span class="style1">*</span>: 
						</div>
					</td>
					<td width="20%">
						<div align="left">
							<input name="subtotal" type="text" id="subtotal" value="0" size="15" maxlength="10" disabled="disabled" readonly="readonly"/>
						</div>
					</td>
					<td colspan="4">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="text-align:center;">
					<td>&nbsp;</td>
					<td>
						<div align="right">
							Llegada<span class="style1">*</span>:
						</div>
					</td>
					<td width="30%">
						<div align="left">
							<input name="llegada2" type="text" id="llegada2" value="" size="10" maxlength="100" onchange="limpiaFechaL();"/>
						</div>
					</td>
					<td width="20%">
						<div align="right">
							IVA<span class="style1">*</span>: 
						</div>
					</td>
					<td width="20%">
						<div align="left">
							<input name="iva" type="text" id="iva" value="" size="15" maxlength="5" onkeypress="validaZeroIzquierda(this.value,this.id); getSubtotal(); return validaNum(event);" onkeyup="getSubtotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event);" onchange="getSubtotal();"/>
						</div>
					</td>
					<td colspan="4">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="text-align:center;">
					<td>&nbsp;</td>
					<td>
						<div align="right">
							Salida<span class="style1">*</span>:
						</div>
					</td>
					<td width="30%">
						<div align="left">
							<input name="salida2" type="text" id="salida2" value="" size="10" maxlength="100" />
						</div>
					</td>
					<td width="20%">
						<div align="right">
							Total: 
						</div>
					</td>
					<td width="20%">
						<div align="left">
							<input name="total" type="text" id="total" value="0" size="15" maxlength="15" disabled="" autocomplete="off" readonly="readonly"/>
						</div>
					</td>
					<td colspan="4">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<div align="right">
							No. de reservación:
						</div>
					</td>
					<td width="30%">
						<div align="left">
							<input name="noreservacion" type="text" id="noreservacion" value="" size=10 maxlength="100"/>
						</div>
					</td>
					<td width="20%">
						<div align="right">
							Monto en pesos: 
						</div>
					</td>
					<td width="20%">
						<div align="left">
							<input name="montoP" type="text" id="montoP" value="0.00" size="15" maxlength="15" disabled="disabled" autocomplete="off" readonly="readonly"/>&nbsp;MXN
						</div>
					</td>
					<td colspan="4">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr><td colspan="10"><div>&nbsp;</div></td></tr>
				<tr><td colspan="10"><div id="warning_msg" style="display: none;">&nbsp;</div></td></tr>
				<tr><td colspan="10"><input type="hidden" id="excedeMontoHospedaje" name="excedeMontoHospedaje" value="0" size="5" readonly="readonly" /></td></tr>
				<tr>
					<td>&nbsp;</td>
					<td align="right" valign="top"><div id="obsjus">Comentarios<span class="style1"></span>:</div></td>
					<td colspan="8" ><div id="areatext"><textarea name="comentario" cols="95" rows='5' id="comentario"></textarea></div></td>				
				</tr>
				<tr><td colspan="10"><div>&nbsp;</div></td></tr>
				<tr>
					<td>&nbsp;</td>
					<td width="20%" colspan="8">
						<div align="center">
						<!--onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>-->
							<input type="button" name="agregar_Hotel" id="agregar_Hotel" value="     Agregar Hotel" onclick="agregarHotel();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/> 
						</div>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="8" style="text-align:center">
						<div id="area_tabla_div" ></div>						
					</td>
				<tr>
					<td>&nbsp;</td>
					<td colspan="6" align="right">Total hospedaje:</td><td align="center"><div  name="TotalHospedaje" id="TotalHospedaje">0.00 </div></td><td>&nbsp;MXN</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="8">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<!-- Para los componentes de auto -->
			<table id="tabla_auto" name="tabla_auto" width="828" bordercolor="#333333" align="center" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
				<tr >
					<td>&nbsp;</td>
					<td colspan="6" ><center><h3>Renta de Auto</h3></center></td>
					<td>&nbsp;</td>
				</tr>
				<tr  style="text-align:center;">
					<td>&nbsp;</td>
					<td>
						<div align="right">
							Empresa<span class="style1">*</span>: 
						</div>
					</td>
					<td>
						<div align="left">
							<input name="empresaAuto" type="text" id="empresaAuto" value="" size="20" maxlength="100" onkeyup="habilitar();" onchange="habilitar();"/>
						</div>
					</td>
					<td>
						<div align="right">
							Costo por día<span class="style1">*</span>: 
						</div>
					</td>
					<td>
						<div align="left">
							<input name="costoDia" type="text" id="costoDia" value="" size="10" maxlength="10" onkeypress="validaZeroIzquierda(this.value,this.id); getTotal_Auto(); return validaNum(event);" onkeyup="getTotal_Auto(); validaZeroIzquierda(this.value,this.id); return validaNum(event);" onchange="getTotal_Auto();" onblur="formatCurrency(this.value,this.id);" />
						</div>
					</td>
					<td colspan="1">
						<div align="right">
							Divisa<span class="style1">*</span>: 
						</div>
					</td>
					<td>
						<div  align="left">
							<select name="tipoDivisa" id="tipoDivisa" onChange="getTotal_Auto();habilitar();" >
							<option value="1">MXN</option>
							<option value="2">USD</option>
							<option value="3">EUR</option>
							</select>
						</div>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="text-align:center;">
					<td>&nbsp;</td>
					<td>
						<div align="right">
							Tipo de auto<span class="style1">*</span>:
						</div>
					</td>
					<td colspan="1">
						<div align="left">
							<select name="tipoAto" id="tipoAuto" onChange="habilitar();" >
							<?php 
							$cnn = new conexion();
							$query = sprintf("SELECT se.se_id AS servicio_id, se_nombre
									FROM servicios AS se
									INNER JOIN bandos_servicios AS bs ON (bs.se_id = se.se_id) 
									INNER JOIN bandos AS bn ON (bn.b_id = bs.b_id) 
									INNER JOIN cat_conceptos AS cp ON (cp.cp_id = se.cp_id)
									INNER JOIN empleado AS ep ON (ep.b_id = bn.b_id) 
									INNER JOIN tramites AS tm ON (tm.t_iniciador = ep.idfwk_usuario)
									WHERE bs.f_id = %s 
									AND cp.cp_id = %s 
									AND t_id = %s", FLUJO_SOLICITUD, CONCEPTO_RENTA_AUTO, $_GET['id']);
							//error_log("--->>Consulta Servicios(Renta de Auto) según el flujo: ".$query);
							$rst = $cnn->consultar($query);
							while ($fila = mysql_fetch_assoc($rst)) {
								echo "<option value=".$fila["servicio_id"].">".$fila["se_nombre"]."</option>";
							} ?>
							</select>
						</div>
					</td>
					<td>
						<div align="right">Total: </div>
					</td>
					<td>
						<div align="left">
							<input name="totalAuto"  disabled type="text" id="totalAuto" value="0.00" size="15" maxlength="10" onkeypress="" onkeyup="revisaCadena(this); getSubtotal();" onchange="getSubtotal();" autocomplete="off" readonly="readonly"/>
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr >
					<td>&nbsp;</td>
					<td>
						<div align="right">
							Dias de renta<span class="style1">*</span>:
						</div>
					</td>
					<td>
						<div align="left">
							<input name="diasRenta" type="text" id="diasRenta" value="" size="3" maxlength="2" onkeypress="validaZeroIzquierda(this.value,this.id);" onkeyup="revisaCadena(this); getTotal_Auto();habilitar();validaZeroIzquierda(this.value,this.id);" onchange="getTotal_Auto();habilitar();"/>
						</div>
					</td>
					<td colspan="1"><div align="right">Monto en pesos: </div></td>
					<td ><div align="left"><input name="montoPesos" disabled type="text" id="montoPesos" value="0.00" size="15" maxlength="15" autocomplete="off" readonly="readonly"/>MXN</div></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="6"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="6"><div id="warning_msg_auto" style="display: none;">&nbsp;</div></td>
					<td><input type="hidden" id="excedeMontoAuto" name="excedeMontoAuto" value="0" size="5" readonly="readonly" /></td>
				</tr>
			</table>
			<table  cellspacing="1" width="100%">
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="8">
						<div align="center">
						<input type="button" name="aceptarAgencia" id="aceptarAgencia" value="Aceptar" disabled="disabled" onClick='/*verifica_auto();*/limpiarCamposDeHotel(); cargar_hoteles_itinerarios(); if($("#auto_agencia").val()==1){agregarAuto();agregar_registro_tabla_auto();}; if($("#empresaAuto").val()!="" || $("#empresaAuto").val()!=0){flotanteActive(0);};if($("#empresaAuto").val()=="" && $("#hotel_agencia").val()==1 && $("#auto_agencia").val()==0){flotanteActive(0);};setBanderaCancelaHotel();' /> 
						<input type="button" name="volver" id="volver" value="Volver" onclick="limpiarCamposDeHotel(); flotanteActive(0);" /> 					
						</div>
					</td>
			   </tr>
			</table>
			<!--Para el total de noches-->
			<input type='hidden' name='total_noches' id='total_noches' value='0' readonly='readonly' />
		</div>
		<!--Ocultos-->
		<input name="origen" type="hidden" id="origen" value="" size="20" />
		<input name="destino" type="hidden" id="destino" value="" size="20" />
		<input name="hotel_agencia" type="hidden" id="hotel_agencia" value="" size="20" />
		<input name="auto_agencia" type="hidden" id="auto_agencia" value="" size="20" />
		<input type="hidden" name="montoExcede" id="montoExcede" value="0" size="20" readonly="readonly" />
		</form>
		</center>
	  </div><!---->
	  <br/> 
	  
	</body>
<?PHP 
echo ('<script language="JavaScript" type="text/javascript">searchSolicitud()</script>');
	if(isset( $_GET['edit_view'])){
		echo "<script language='javascript'>
			/*document.getElementById('autorizar').style.visibility = 'visible';*/
			/*document.getElementById('rechazar').style.visibility = 'visible';*/
			/*document.getElementById('cancel').style.visibility = 'hidden';*/
		</script>";
	}
	if(isset( $_GET['hist'])){
		echo "<script language='javascript'>
			document.getElementById('observ').style.display = 'none';
			document.getElementById('observ_to_emple').style.display = 'none';
		</script>";
	}
?>	
