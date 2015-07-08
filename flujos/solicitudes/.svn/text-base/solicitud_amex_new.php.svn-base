<?php 

// Convierte una fecha del formato "27/02/2011" al formato "2011-02-27"
function fecha_to_mysql($fecha){
    $pieces = explode("/", $fecha);
    return $pieces[2]."-".$pieces[1]."-".$pieces[0];
}

if(isset($_POST['guardarComp'])){

	require_once("../comprobaciones/services/func_comprobacion.php");
    require_once("services/C_SV.php");
    
	if(isset($_POST['rowCount']) || $_POST['rowCount']!=0){
        
		// Información de tramite
		$motivo                = $_POST['motive'];
		$cat_cecos_cargado     = $_POST['cat_cecos_cargado'];
		$cat_cecos_beneficiado = $_POST['cat_cecos_beneficiado'];  // TODO: Este valor se tiene que guardar como informativo
        
		// Informacion de solicitud
        $TotalDias = $_POST['TotalDias'];
		$sObser    = $_POST['observ'];
        
        // Fecha de viaje (salida)
        $sFecha=$_POST['salida'.'1'];        
        $FechaMySQL = fecha_to_mysql($sFecha);        

		//objeto creado (services/C_SV.php)
		$CViaje= new C_SV();
				
		//Datos del empleado
		$iduser      = $_POST["idusuario"];
		$numempleado = $_POST["empleado"];        
        $idempresa   = $_POST["empresa"];           
        $cnn    = new conexion();

        // Buscamos quien debe aprobar esta solicitud
		$Us=new Usuario();
		$Us->Load_Usuario_By_No_Empleado($numempleado);
        $aprobador = $Us->buscaAgenciaViajesParaSolicitud($cat_cecos_cargado);
        
        // Registra nuevo tramite
        $tramite = new Tramite();
        $tramite->insertar("BEGIN WORK");  
        $idTramite = $tramite->Crea_Tramite($iduser, $idempresa, ANTICIPO_AMEX_ETAPA_SOLICITUD, FLUJO_AMEX, $motivo);        
        
        // Registramos la solicitud Amex
        $amexID = $CViaje->Add_amex($TotalDias,$sObser,$motivo,$cat_cecos_cargado,$cat_cecos_beneficiado,$FechaMySQL,$idTramite);
        if($amexID<=0){
            $CViaje->insertar("ROLL BACK");
            echo "Error al Registrar el Viaje";
            exit();
        }
        
        // Registra los conceptos
        if(isset($_POST['rowCountConceptos'])&& $_POST['rowCountConceptos']!=""){
            for($i=1;$i<=$_POST['rowCountConceptos'];$i++){
                $concepto=$_POST['Concepto'.$i];
                $CViaje->add_conceptos_detalle($concepto,$idTramite,0);
            }
        }

        //  Registra las partidas del itinerario
        for($i=1;$i<=$_POST['rowCount'];$i++){
            $sTipoViaje=$_POST['select_tipo_viaje'.$i];
            $sFechaSalida=$_POST['salida'.$i];
            $sFechaLlegada=$_POST['llegada'.$i];
            $sOrigen=$_POST['origen'.$i];
            $sDestino=$_POST['destino'.$i];
            $sSelect_hora_salida=$_POST['hora'.$i];

            if($sOrigen!="" && $sOrigen!=NULL)
                if($CViaje->Add_Itinerario_sa($sOrigen,$sDestino,$sFechaSalida,$sFechaLlegada,$sSelect_hora_salida,$sTipoViaje)<=0){
                $CViaje->insertar("ROLL BACK");
                echo "Error al Registrar el Itinerario";
                exit();
                }
        
        }//for
               
        // Envia el tramite a aprobacion
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador);
        $mensaje = sprintf("La solicitud Amex <strong>%05s</strong> ha sido <strong>CREADA</strong> y asignada a <strong>%s</strong> para su revisi&oacute;n",
                                $idTramite, $usuarioAprobador->Get_dato('nombre'));
        $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_AGENCIA, FLUJO_AMEX, $aprobador);
        $tramite->EnviaMensaje($idTramite, $mensaje);
        
        // Termina transacción
        $tramite->insertar("COMMIT");        
        
        // Regresa a la pagina del index
		header("Location: ./index.php?oksave");
		
	} else{	        
        header("Location: ./index.php?errsave");
        die();
	}//if row	
}//if guarda comp



if(isset($_GET['new2'])){
//*****************************//COMPROBACION GENERAL//*****************************//	

	$UsuOb=new Usuario();
	
	$UsuOb->Load_Usuario_By_No_Empleado($_SESSION['empleado']);
	$idcentrocosto=$UsuOb->Get_dato('idcentrocosto');

	function forma_comprobacion(){
	
		$cnn= new conexion();
		$arrHoteles= array();
		$arrCd= array();
		
		$query=sprintf("select distinct 'pro_ciudad', pro_ciudad from  proveedores where pro_ciudad !=''");
		$rst=$cnn->consultar($query);

		while($datos=mysql_fetch_assoc($rst)){
			array_push($arrCd,$datos);
		}
		
?>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>


		
<script language="JavaScript" type="text/javascript">
//variables
var doc;

doc = $(document);
doc.ready(inicializarEventos);//cuando el documento esté listo
function inicializarEventos(){	

			/*//ajusta tabla
			$("#solicitud_table").tablesorter({ 
					//cabeceras deshabilitadas del ordenamiento
					headers: { 
						4: {sorter: false }, 
						7: {sorter: false },
						9: {sorter: false },
						11:{sorter: false }  
					} //headers
   		 	}); //tabla*/
			borrarPartida();
			guardaComprobacion();

   $("#selectCd").change(function(){
				$.post("services/ingreso_sin_recargar_proceso.php",{ idCd:$(this).val() },function(data){$("#capselectHotel2").html(data);})
			});

			$.post("services/ingreso_sin_recargar_proceso.php",{ idCd:$("#selectCd").val() },function(data){$("#capselectHotel2").html(data);});
}//fin ready ó inicializarEventos

function buscaProveedor(li) {
	if(li==null){ 
	return null;
	}
	if(!!li.extra){
	var valorLista=li.extra[0];
	}else{ 
	var valorLista=li.selectValue;
			$.ajax({
				//busca el nombre del proveedor en el catalogo en base al RFC
				url: 'services/catalogo_proveedores.php',
				type: "POST",
				data: "nombre="+valorLista+"&tip=1",
				dataType: "html",
				success: function(datos){
						$("#proveedor").val(datos);
						}
				});//fin ajax	
	}
}//fin buscaProveedor

function buscaRFC(li) {
	if(li==null){ 
	return null;
	}
	if(!!li.extra){
	var valorLista=li.extra[0];
	}else{ 
	var valorLista=li.selectValue;
			$.ajax({
				//busca el rfc del proveedor en el catalogo en base al nombre
				url: 'services/catalogo_proveedores.php',
				type: "POST",
				data: "nombre="+valorLista+"&tip=2",
				dataType: "html",
				success: function(datos){
						$("#rfc").val(datos);
						}
				});//fin ajax	
	}
}//fin buscaRFC

//var selectedItemDestino=false;

function seleccionaItem(li) {
	if($("#noches").val()>0){
		onloadHotels();
		//selectedItemDestino=true;
	}
}//fin seleccionaItem

function seleccionaItem2(li) {
	buscaRFC(li);
}//fin seleccionaItem

function arreglaItem(row) {
//da el formato a la lista
	return row[0];
}//fin arreglaItem

//Agrega Nuevo Proveedor al catálogo
function nuevoProveedor(nombreProveedor,rfcProveedor){
	if(rfcProveedor<12 || rfcProveedor>13){
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
		return false;
	}else{			
		$.ajax({
		url: 'services/catalogo_proveedores.php',
		type: "POST",
		data: "submit=&nameprov="+nombreProveedor+"&rfcprov="+rfcProveedor,
		success: function(datos){
				if (datos==""){				
					$("#proveedor").val("");
					$("#rfc").val("");
					$('#new_proveedor').val("");
					$('#new_p_rfc').val("");
					$("#proveedor").focus();
				}else{	
				alert("El proveedor que intenta ingresar es incorrecto \xf3 ya se encuentra registrado.");
					$("#new_proveedor").focus();
				}
			}
		});//fin ajax
		return false;
	}//fin if rfc
}//fin nuevoProveedor
		
//Fecha
$(function() {
	//campo 
	$("#fecha").date_input();
	//$("#fechaLlegada").date_input();
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

//validación campos numericos
function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}

//cambia el valor del input
function cambiaTexto(){
var frm=document.detallesItinerarios;
	if(!isNaN(frm.monto.value)||frm.monto.value!=null){
		if(frm.select_impuesto.value!=0 || frm.select_impuesto.value!="No"){
		var imp=(frm.select_impuesto.value*0.01)*(frm.monto.value);			
		frm.impuesto.disabled=false;		
		frm.impuesto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		//total
		var tot=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value);
		frm.total.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		
		}
	}else{
	frm.impuesto.value=0;
	frm.total.value=0;
	
	}
}

function activaCb(){
var frm=document.detallesItinerarios;
	if(frm.rAnti.value==0){
		frm.anticipo.disabled=false;		
	}else{
		
	}

}
		
//IVA
function valor($combo){
	var frm=document.detallesItinerarios;
	if(frm.monto.value=="" || frm.monto.value==0){
	frm.monto.value=0;
	frm.impuesto.value=0;
	frm.total.value=0;
		if(frm.select_impuesto.value=="No"){
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.total.value=parseFloat(frm.monto.value);
		}else{
			frm.impuesto.disabled=false;
			}
	}else{
		if(frm.select_impuesto.value=="No"){
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.total.value=parseFloat(frm.monto.value);
		}else{
			//impuesto
			var imp=(frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value*0.01)*(frm.monto.value);			
			frm.impuesto.disabled=false;		
			frm.impuesto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			//total
			var tot=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value);
			frm.total.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		}
			
	}
}//fin valor

//Divisa
function tipoCambio(combo){
	var frm=document.detallesItinerarios;
	switch (combo) {
		case "0":
		frm.tasa.value=parseFloat(1.00);//este valor cambiará cuando sea parametrizado
		break
		case "1":
		frm.tasa.value=parseFloat(12.87);//este valor cambiará cuando sea parametrizado
		break
		case "2":
		frm.tasa.value=parseFloat(18.11);//este valor cambiará cuando sea parametrizado
		break
	} 
}//Tipo cambio


function agregarPartida(){
	var frm=document.detallesItinerarios;
		//Valida que los campos obligatorios esten llenos
	if(frm.fecha.value=="" || frm.origen.value==""  || frm.destino.value=="" || frm.select_hora_salida.value==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes");
		return false;
	}	
	
else if (!compare_dates(frm.fecha.value,1)){
  
	alert("La fecha seleccionada no es válida, intente con una fecha igual o posterior a la actual");
    return false;
} 

/*
else if (!compare_dates(frm.fechaLlegada.value,frm.fecha.value)){

	alert("La fecha de llegada no es valida, intente con un fecha igual o posterior a la actual");
    return false;
}*/

else if(frm.motive.value.length<5){
		alert("El motivo debe tener al menos 5 caracteres.");
		frm.motive.focus();
		return false;
	}else{
		construyePartida();
	}//fin if
}//agregar partida

function construyePartida(){
		var frm=document.detallesItinerarios;
		//conteo de la partida
		frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		id=parseInt($("#solicitud_table").find("tr:last").find("td").eq(0).html());
		if(isNaN(id)){	
			id=1;
		}else{
			id+=parseInt(1);
		}
		
		var Hotel="";
		//Creamos la nueva fila y sus respectivas columnas
		
		var row=frm.rowCount.value;
		var nuevaFila='<tr>';
		
		nuevaFila+="<td>"+id+"<input type='hidden' name='row"+id+"' id=row'"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='origen"+id+"' id='origen"+id+"' value='"+frm.origen.value+"' readonly='readonly' />"+frm.origen.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='destino"+id+"' id='destino"+id+"' value='"+frm.destino.value+"' readonly='readonly' />"+frm.destino.value+"</td>";  
		nuevaFila+="<td><div align='left'><input type='hidden' name='salida"+id+"' id='salida"+id+"' value='"+frm.fecha.value+"' readonly='readonly' />"+frm.fecha.value+"</td>";
        
        
		//nuevaFila+="<td><div align='left'><input type='hidden' name='llegada"+id+"' id='llegada"+id+"' value='"+frm.fechaLlegada.value+"' readonly='readonly' />"+frm.fechaLlegada.value+"</td>";
		
        
        nuevaFila+="<td><input type='hidden' name='hora"+id+"' id='hora"+id+"' value='"+frm.select_hora_salida.options[frm.select_hora_salida.selectedIndex].text+"' readonly='readonly' />"+frm.select_hora_salida.options[frm.select_hora_salida.selectedIndex].text+"</td>";
		nuevaFila+="<td><div align='left'><input type='hidden' name='select_tipo_viaje"+id+"' id='select_tipo_viaje"+id+"' value='"+frm.select_tipo_viaje.value+"' readonly='readonly' />"+frm.select_tipo_viaje.value+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='"+id+"'  ondblclick='borrarPartida(this.id);'  style='cursor:pointer' /></div></td>";
		nuevaFila+="<input type='hidden' name='nameHotel"+id+"' id='nameHotel"+id+"' value='"+Hotel+"'>";
		nuevaFila+= '</tr>';

		//restablece los campos
		$("#solicitud_table").append(nuevaFila);	
        var destino = $("#destino").val();
        var origen = $("#origen").val();
		$("#origen").val(destino);
		$("#destino").val(origen);
		$("#noches").val("0");
		$("#kilometraje").val("");				
		$('#select_hora_salida').find('option:first').attr('selected', 'selected').parent('select');
		$('#select_tipo_viaje').find('option:first').attr('selected', 'selected').parent('select');	
		$('#select_medio_transporte').find('option:first').attr('selected', 'selected').parent('select');	
		
		$("#capSelectHotel").fadeOut(1500);											
		$('#capSelectHotel').find('option:first').attr('selected', 'selected').parent('select');
		$.post("services/ingreso_sin_recargar_proceso.php",{ idCd:$("#selectCd").val() },function(data){$("#capselectHotel2").html(data);});
		
		
		guardaComprobacion();
}//construye partida

function borrarPartida(id){
var firstRow=parseInt($("#solicitud_table").find("tr:first").find("td").eq(0).html());
var lastRow=parseInt($("#solicitud_table").find("tr:last").find("td").eq(0).html());
var frm=document.detallesItinerarios;
var row=frm.rowCount.value;
	$("img.elimina").click(function(){
			//var delDias=parseInt($("#noche"+id).val());
			//impresion en pantalla
			frm.TotalDias.value=parseInt(frm.TotalDias.value)-parseInt($("#noche"+id).val())-parseInt(1);			
			//impresion en pantalla
			//$("#TotalDias").val(parseInt(frm.TotalDias.value)-delDias);
			$(this).parent().parent().parent().fadeOut("normal", function () { 
			$(this).remove();
			
			guardaComprobacion();
		});
		return false;
	});
	
}//borrar partida


function guardaComprobacion(){
var frm=document.detallesItinerarios;
    id= parseInt($("#solicitud_table").find("tr:last").find("td").eq(0).html());
	if(isNaN(id)){
    $("#guardarComp").attr("disabled", "disable"); 
	$("#registrar_comp").removeAttr("disabled");
	$("#TotalDias").val("0");
	}else{
		if (id==2)
			$("#registrar_comp").attr("disabled", "disable");
		else
			$("#registrar_comp").removeAttr("disabled"); 
		    $("#guardarComp").removeAttr("disabled");
	}
}

 function compare_dates(fecha,fecha2)
  {
  	if(fecha2==1){
		var now=new Date();
		var yMonth=now.getMonth() + 1;
    	var yDay=now.getDate();
	    var yYear=now.getFullYear();
	}
	else{
		var yMonth=fecha2.substring(3, 5);
	    var yDay=fecha2.substring(0, 2);
	    var yYear=fecha2.substring(6,10);
	}
    var xMonth=fecha.substring(3, 5);
    var xDay=fecha.substring(0, 2);   
    var xYear=fecha.substring(6,10);   
    if (xYear> yYear)   
    {   
        return(true)   
    }   
    else  
    {   
      if (xYear == yYear)   
      {    
        if (xMonth> yMonth)   
        {   
            return(true)   
        }   
        else  
        {    
          if (xMonth == yMonth)   
          {   
            if (xDay>= yDay)   
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
//////////////////////////////////////////////////////////////////////////////////////
	function viewHotel(){		
		if($("#noches").val()>=1){			
			$("#capSelectHotel").fadeIn(1500);
			onloadHotels();
			
		}
		else{		
			$("#capSelectHotel").fadeOut(1500);											
			$('#capSelectHotel').find('option:first').attr('selected', 'selected').parent('select');
			$.post("services/ingreso_sin_recargar_proceso.php",{ idCd:$("#selectCd").val() },function(data){$("#capselectHotel2").html(data);});
			$('#selectHotel').val("");
		}
	}
	
	function onloadHotels(){
		$('#capSelectHotel').html("<strong>Cd:</strong>"+$('#destino').val()+'&nbsp;&nbsp;&nbsp;&nbsp;<strong>Hotel:</strong><div id="capselectHotel2" style="display:inline; width:123px"><select id="selectHotel" name="selectHotel"></select></div>');
			$.post("services/ingreso_sin_recargar_proceso.php",
				{ 
					idCd:$("#destino").val() 
				},
				function(data){	
					$("#capselectHotel2").html(data);
					try{
						var lista = document.getElementById("selectHotel").options.length;
										
						if(lista==0){
							$('#capSelectHotel').html("<strong>Cd:</strong>"+$('#destino').val()+'&nbsp;&nbsp;&nbsp;&nbsp;<strong>Hotel:</strong>No hay hoteles asignados<input type="hidden" name="selectHotel" id="selectHotel">');
							}
					}
					catch(e){}
				}
			);
	}
	function cargaPrecioHotel(){
	}
	
	function construyePartidaConcepto(){
		var frm=document.detallesItinerarios;
		//conteo de la partida
		frm.rowCountConceptos.value=parseInt(frm.rowCountConceptos.value)+parseInt(1);
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		id=parseInt($("#conceptos_table").find("tr:last").find("td").eq(0).html());
		if(isNaN(id)){	
			id=1;
		}else{
			id+=parseInt(1);
		}
		
		//Creamos la nueva fila y sus respectivas columnas
		
		var row=frm.rowCountConceptos.value;
		
		var arrDatos=$("#select_concepto").val().split("&");

		var nuevaFila='<tr>';
		nuevaFila+="<td>"+id+"<input type='hidden' name='rowConcepto"+id+"' id=rowConcepto'"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td>"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"<input type='hidden' name='Concepto"+id+"' id='Concepto"+id+"' value='"+arrDatos[0]+"' readonly='readonly' /></td>";

		nuevaFila+="<td><div align='center'><img class='eliminar' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='dele' id='"+id+"'  onmousedown='borrarPartidaConcepto(this.id);'  style='cursor:pointer'/></div></td>";
		nuevaFila+="</tr>";
		
		$("#conceptos_table").append(nuevaFila);
	}//construye partida


	function borrarPartidaConcepto(id){
		var firstRow=parseInt($("#conceptos_table").find("tr:first").find("td").eq(0).html());
		var lastRow=parseInt($("#conceptos_table").find("tr:last").find("td").eq(0).html());

	
		$("img.eliminar").click(function(){
			
			if(id!=0){

				$(this).parent().parent().parent().fadeOut("normal", function () { 
					$(this).remove();
				});
				id=0;
				return false;
			}
		});
	
	}//borrar partida

</script>

<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
<link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
<style type="text/css">
.style1 {color: #FF0000}

</style>


<div id="Layer1">
<form action="solicitud_amex_new.php?save" method="post" name="detallesItinerarios" id="detallesItinerarios">
<center>
	<table width="785" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">  
  
 <tr>   
	<td colspan="11" ><div align="center">Motivo de la solicitud de Amex <span class="style1">*</span>:
		<input name="motive" type="text" id="motive" size=50 maxlength="100"/>
		</div>
	</td>		   
 </tr> 

  <tr>
    <td width="1">&nbsp;</td>
    <td colspan="2"><div align="left"></div></td>
    <td width="128"><div align="left"></div></td>
    <td width="34">&nbsp;</td>
  </tr>
  
  <tr>
    <td width="117">&nbsp;</td>
    <td width="153"><div align="right">Tipo de viaje <span class="style1">*</span>: <br />
    </div></td>
    <td colspan="3"><div align="left">
      <select name="select_tipo_viaje" id="select_tipo_viaje">
	   <?php
			$tipoViaje=array("Nacional","Extranjero");
			for($i=0;$i<count($tipoViaje);$i++){
				echo "<option id=".$i." value=".$tipoViaje[$i].">".$tipoViaje[$i]."</option>";
			}

		?>
      </select>
    </div></td>
    <td width="7">&nbsp;</td>
    <td width="1">&nbsp;</td>
    <td colspan="2"><div align="left"></div></td>
    <td width="128"><div align="left"></div></td>
    <td width="34">&nbsp;</td>
  </tr>

  
  <tr>
    <td width="117">&nbsp;</td>
    <td width="153"><div align="right">Fecha<span class="style1">*</span>: <br />
    </div></td>
    <td colspan="8">
		<div align="left">
			<input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="15" readonly="readonly" />
			<img src="../../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" />
			&nbsp;&nbsp;
            <!--
			Llegada:
			<input name="fechaLlegada" id="fechaLlegada" value="<?php echo date('d/m/Y'); ?>" size="15" readonly="readonly" />
			<img src="../../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" />
            -->
		</div>
	</td>
    <td width="34">&nbsp;</td>
  </tr>

  <tr>
    <td width="117">&nbsp;</td>
    <td width="153"><div align="right">Origen<span class="style1">*</span>: <br />
    </div></td>
    <td colspan="3"><div align="left"><input name="origen" type="text" id="origen" value="" size="40" /></div></td>
    <td width="7">&nbsp;</td>
    <td width="1">&nbsp;</td>
    <td colspan="2"><div align="left"></div></td>
    <td width="128"><div align="left"></div></td>
    <td width="34">&nbsp;</td>
  </tr>
  
  <tr>
    <td width="117">&nbsp;</td>
    <td width="153"><div align="right">Destino<span class="style1">*</span>: <br />
    </div></td>
    <td colspan="3">
		<div align="left">
			<input name="destino" type="text" id="destino" value="" size="40" onchange=""/>
		</div>
	</td>
    <td width="7">&nbsp;</td>
    <td width="1">&nbsp;</td>
    <td colspan="2"><div align="left"></div></td>
    <td width="128"><div align="left"></div></td>
    <td width="34">&nbsp;</td>
  </tr>
  
   <tr>
    <td width="117">&nbsp;</td>
    <td width="153"><div align="right">Horario de salida<span class="style1">*</span>: <br />
    </div></td>
    <td colspan="3">
		<div align="left">
	      <select name="select_hora_salida" id="select_hora_salida" onchange="">
			<?php 
				$tipoViaje=array("Cualquier horario","Mañana","Tarde","Noche");		
				for($i=0;$i<count($tipoViaje);$i++){
					echo "<option id=".$i." value=".$tipoViaje[$i].">".$tipoViaje[$i]."</option>";
				}
			?>
    	  </select>
	    </div>
	</td>
    <td width="7">&nbsp;</td>
    <td width="1">&nbsp;</td>
    <td colspan="2"><div align="left"></div></td>
    <td width="128"><div align="left"></div></td>
    <td width="34">&nbsp;</td>
  </tr>

  <tr>
  	<td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

  <tr>
    <td width="117">&nbsp;</td>
    <td colspan="6"></td>
	<td colspan="3"><div align="center">
      <input name="registrar_comp" type="button" id="registrar_comp" value="Registrar Itinerario"  onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
    </div></td>
    <td width="34">&nbsp;</td>
  </tr>
  
  <tr>
    <td width="117">&nbsp;</td>
    <td colspan="9">&nbsp;</td>
    <td width="34">&nbsp;</td>
  </tr>
</table>
</center>
<br />
<div align="center"></div>

	
	
<?php } ?> 

    
    <?php
    
        // Obtiene el usuario actual de la sesion
        $idusuario_actual=$_SESSION["idusuario"];

        // Checa si el usuario tiene comprobaciones pendientes
        $sql = "SELECT * FROM solicitud_viaje s inner join tramites on sv_tramite = t_id where t_comprobado = 0 and t_iniciador =" . $idusuario_actual;
        $rst=mysql_query($sql);
        $no_solicitudes_pendientes_de_aprobar = mysql_num_rows($rst);
    
        if($BLOQUEA_NUEVAS_SOLICITUDES_SI_HAY_COMPROBACIONES_PENDIENTES && ($no_solicitudes_pendientes_de_aprobar > 0)) {
            echo "<center><h3>Hay solicitudes pendientes de comprobar, no se pueden crear solicitudes nuevas.</h3></center>";
        } else { 
            echo "<center><h3>Ingrese los Datos de la nueva Solicitud de Amex</h3></center>";
            echo "<center>Se le recuerda que es necesario definir cada etapa del itinerario de su viaje.</center>";
            forma_comprobacion();
            ?>
            
            <table id="solicitud_table" class="tablesorter" cellspacing="1"> 
            <thead> 
            <tr> 
                <th width="2%">No.</th> 
                <th width="8%">Origen</th>
                <th width="9%">Destino</th> 
                <th width="8%">Fecha</th>
                <!--<th width="8%">Llegada</th>-->
                <th width="9%">Horario</th>
                <th width="9%">Tipo Viaje</th>
                <th width="7%">Herramientas</th>
            </tr> 
            </thead> 
            <tbody> 
            <!-- cuerpo tabla-->
            </tbody>
            </table>
            <center>
            <table width="785" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
              <tr>
                <td colspan="8"><h3 align="center">Informaci&oacute;n General </h3></td>
              </tr>
              
              <tr>      
                <td colspan="8" align='center'>	                        
                    Concepto<span class="style1">*</span>:
                        <select name="select_concepto" id="select_concepto" onchange="">
                                <!-- <option value="-1">Seleccione...</option> -->
                                <?php 
                                    //Catálogo de conceptos		
                                    $idusuario=$_SESSION["idusuario"];												
                                    function Get_clasificaciones2($usuario){
                                        $nivelUser=$_SESSION["nivelUser"];
                                        $empresa=$_SESSION["empresa"];
                                        $cnn   = new conexion();
                                        $query = "select dc_id, cp_concepto,cp_retencion from cat_conceptos  where cp_activo=true and dc_catalogo=1 and cp_amex = 1 and cp_empresa_id = " . $_SESSION["empresa"] . " order by cp_concepto desc";
                                        error_log($query);
                                        $rst    = $cnn->consultar($query);
                                        while($fila=mysql_fetch_assoc($rst)){           
                                            echo "<option id=".$fila["cp_concepto"]." value=".$fila["dc_id"].">".$fila["cp_concepto"]."</option>";
                                        }
                                    }
                                    $cat=Get_clasificaciones2($idusuario);
                                ?>
                        </select>    
                        <input name="addConcepto" type="button" id="addConcepto" value="    Agregar a mi lista"  onclick="construyePartidaConcepto();" style="background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
                </td>
              </tr>                
            
              <tr>
                    <td colspan="8">			
                        <center>
                            <div align="center" style="width:50%">
                                <br />
                                <h3>Lista de mis Conceptos por los que solicito vi&aacute;ticos</h3>
                                <table id="conceptos_table" class="tablesorter" cellspacing="1"> 
                                    <thead> 
                                    <tr> 
                                        <th width="2%">No.</th>
                                        <th width="15%">Concepto</th>
                                        <th width="9%">Herramientas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- cuerpo tabla-->
                                    </tbody>
                                </table>
                            </div>
                            
                        </center>  	
                    </td>
              </tr>
              <tr>
                <td colspan="8" align='center'>
                    Total de d&iacute;as de hospedaje <span class="style1">*</span>:<input name="TotalDias" type="text" id="TotalDias" value="0" size="8" maxlength="8" onkeypress="return validaNum (event)"/>
                </td>
              </tr>

              <tr>      
                <td colspan="8" align='center'>	                        
                    &nbsp;
                </td>
              </tr>    
                        
              <tr>      
                <td colspan="8" align='center'>	                        
                    <b>Observaciones:</b>
                </td>
              </tr>              
              <tr>      
                <td colspan="8" align='center'>	                        
                    <textarea name="observ" cols="40" rows="4" id="observ"></textarea>
                </td>
              </tr>
              
              <tr>      
                <td colspan="8" align='center'>	                        
                    &nbsp;
                </td>
              </tr>               
              
              <tr>      
                <td colspan="8" align='center'>	                        
                    Departamento al que se cargara:
                    <select name='cat_cecos_cargado'>
                        <?php 
                            $query=sprintf("SELECT cc_id,cc_centrocostos,cc_nombre FROM cat_cecos WHERE cc_estatus = 0 AND cc_empresa_id = " . $_SESSION["empresa"] . " order by  cc_centrocostos desc");
                            $var=mysql_query($query);
                            while($arr=mysql_fetch_assoc($var)){
                                if($arr['cc_id']==$idcentrocosto) {
                                    echo sprintf("<option value='%s' selected='selected'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
                                } else {
                                    echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
                                }
                                
                            }
                        ?>                    
                    </select>                     
                </td>
              </tr>         
              
              <tr>      
                <td colspan="8" align='center'>	                        
                    &nbsp;
                </td>
              </tr>                     

            </table>
            </center>
            <br />

            <div align="center">
              <input type="submit" id="guardarComp" name="guardarComp" value="Guardar Solicitud"  onclick="guardaComprobacion();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
            </div>
            <input type="hidden" name="h" id="h" readonly="readonly" />
            <input type="hidden" name="idusuario" id="idusuario" value="<?php echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
            <input type="hidden" name="empleado" id="empleado" value="<?php echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
            <input type="hidden" name="empresa" id="empresa" value="<?php echo $_SESSION["empresa"]; ?>" readonly="readonly" />
            <input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
            <input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
            <input type="hidden" id="rowCountConceptos" name="rowCountConceptos" value="0" readonly="readonly"/>
            </center>
            </form>
            
            
            <?php
        }
    ?>
<?php
}
?>
