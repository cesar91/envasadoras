<?php 
/********************************************************
*         T&E Módulo de Nueva Comprobación              *
* Creado por:	  Jorge Usigli Huerta 11-Ene-2010		*
* Modificado por: Jorge Usigli Huerta 23-Abril-2010		*
* PHP, jQuery, JavaScript, CSS                          *
*********************************************************/

//error_log(print_r($_POST, True));

// Esta seccion muestra la forma de alta de nueva solicitud
if(isset($_GET['new3'])){ 

	$UsuOb=new Usuario();	
	$UsuOb->Load_Usuario_By_No_Empleado($_SESSION['empleado']);
	$idcentrocosto=$UsuOb->Get_dato('idcentrocosto');
}

// Guarda la comprobacion
if(isset($_POST['guardarComp'])){
	require_once("services/func_comprobacion.php");
	require_once("../solicitudes/services/C_SV.php");
    
	//valida que haya partidas
	if(isset($_POST['rowCount']) || $_POST['rowCount']!=0)
    {	
		$tipo="";
	    $tAmnt=$_POST['t_subtotal'];
	    $tIVA=$_POST['t_iva'];
        $tTotal=$_POST['t_total'];
        $cat_cecos_cargado = $_POST['cat_cecos_cargado'];
        
		if(isset($_POST['observ']) && $_POST['observ']!="")			
			$sObser=$_POST['observ'];
		else
			$sObser="";        
        
	    //valida que haya montos
		if($tAmnt==0 || $tTotal==0 || $tAmnt=='NaN' || $tIVA=='NaN' || $tTotal=='NaN' || $tAmnt==NULL || $tIVA==NULL || $tTotal==NULL)
        {
		    header("Location: ./index.php?erramnt");
		    die();
	    }
			
		$forma_gasto="Efectivo";
        
		//Excedente, Retenciones
		$montoexc=0.00;
		$ivaexc=0.00;
		$kilometraje=0;
		$ivaR=0.00;
		$isrR=0.00;
	
	    // Datos del empleado
		$iduser=$_POST['iu'];
		$numempleado=$_POST['ne'];
        $idempresa   = $_POST["empresa"];         
		$Empleado= new Usuario();
		$Empleado->Load_Usuario_By_No_Empleado($numempleado);
		$CreditCard="";
		$motivoComprobacion = $_POST["motive"];
        $cMotive="Comprobacion de Caja Chica";
        
        // Datos del aprobador
        $Usu = new Usuario();
        $fecha = date('Y-m-d');     
        $ruta_autorizadores = $Usu->buscaAprobadorParaComprobacion($cat_cecos_cargado, $tTotal, 0, $fecha);
        $aprobador          = $ruta_autorizadores[0];
        
        // Registra nuevo tramite
        $tramite = new Tramite();
        $tramite->insertar("BEGIN WORK");  
        $idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_CAJA_CHICA_ETAPA_COMPROBACION_CAJA_CHICA, 
                                                FLUJO_REEMBOLSO_CAJA_CHICA, $cMotive);
                
        // OJO: El tipo de comprobacion 3 es de caja chica
        $idSolicitud = -1;        
        $refacturar="";
        $idComprobacion = Add_new($idTramite,$idSolicitud,$tAmnt, $tIVA, $tTotal, $montoexc,$motivoComprobacion,$cat_cecos_cargado,3,$CreditCard,$refacturar,$sObser,0);

        $status_c=1;
        for($i=1;$i<=$_POST['rowCount'];$i++){
            //Partidas
            $cNo=$_POST['row'.$i]; 		
            $cDate=$_POST['fecha'.$i]; 
            $cCargo="";		
            $cConc=$_POST['concepto'.$i];
            $cRef=$_POST['referencia'.$i]; 	
            $cProv=$_POST['proveedor'.$i]; 	
            $cP_RFC=$_POST['rfc'.$i]; 		
            $cAmt=$_POST['mnt'.$i];
            $cExch=$_POST['divisa'.$i]; 		
            $cRate=$_POST['tasa'.$i]; 		
            $cImp=$_POST['pimpuesto'.$i]; 	
            $cImpVal=$_POST['imp'.$i];		
            $cTotal=$_POST['tot'.$i];
            $cComensales=$_POST['cant_asistentes'.$i];
            $cFolio=$_POST['flag_factura'.$i];
            if($cFolio!="" || $cFolio!=0){
                $cFlagFactura=1;
            }else{
                $cFlagFactura=0;
            }			

            // ID del concepto
            $cnn    = new conexion();
            $query = sprintf("select dc_id from cat_conceptos where cp_concepto='%s' and cp_empresa_id = " . $idempresa,$cConc) ;
            $rst    = $cnn->consultar($query);
            $fila=mysql_fetch_assoc($rst);
            $cConc=$fila['dc_id'];

            // ID proveedor
            $cnn    = new conexion();
            $query = sprintf("select pro_id from proveedores where pro_proveedor='%s'",$cProv) ;
            $rst    = $cnn->consultar($query);
            $num_rows=mysql_num_rows($rst);
            $fila=mysql_fetch_assoc($rst);
            $idProv=$fila['pro_id'];
            if ($num_rows>0)
                $idProv=$fila['pro_id'];
            else
                $idProv=0;
                
            //************Detalle Comprobación
            Add_detalle($idComprobacion,$cCargo,$cConc,$cRef,$cP_RFC,$cAmt,$cImp,$cImpVal,$cTotal,0,$forma_gasto,$montoexc,$ivaexc,$idProv,$kilometraje,$cDate,$ivaR,$isrR,$cDate,$cExch,$cComensales,$cRate,$tipo,$cFlagFactura,$cFolio,$cRespPart="9771",$cRespPart="");

        } //for	
        
        // Envia el tramite a aprobacion
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador);
        $mensaje = sprintf("El reembolso de Caja Chica <strong>%05s</strong> ha sido <strong>CREADO</strong> y asignado a <strong>%s</strong> para su aprobaci&oacute;n",
                                $idTramite, $usuarioAprobador->Get_dato('nombre'));
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_APROBACION, FLUJO_REEMBOLSO_CAJA_CHICA, $aprobador, $ruta_autorizadores);
        $tramite->EnviaMensaje($idTramite, $mensaje); 
        
        // Termina transacción
        $tramite->insertar("COMMIT");     

        header("Location: ./index.php?oksave");

	}
    else
    {	
	   header("Location: ./index.php?errsave");
	   die();
	}//if row	
}//if guarda comp
//*****************************//COMPROBACION DE VIAJE//*****************************//
if(isset($_GET['new3'])){ 	
	function forma_comprobacion(){
?>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tableEditor.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.fadeSliderToggle.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>

		
<script language="JavaScript" type="text/javascript">
//variables
var doc;
var validaFactura=1;
var flagSolicitud=0;
var validaCeco=0;
	

doc = $(document);
doc.ready(inicializarEventos);//cuando el documento esté listo
function inicializarEventos(){	
			var frm=document.detallecomp;		
			//genera la lista de sugerencia: nombre proveedor
			$("#proveedor").autocomplete("services/catalogo_proveedores.php", {				
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:seleccionaItem2,
			onFindValue:buscaRFC,
			formatItem:arreglaItem,
			autoFill:false,
			extraParams:{tip:1}
			});//fin autocomplete

			//genera la lista de sugerencia: rfc proveedor
			$("#rfc").autocomplete("services/catalogo_proveedores.php", {				
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:seleccionaItem,
			onFindValue:buscaProveedor,
			formatItem:arreglaItem,
			autoFill:false,
			extraParams:{tip:2}
			});//fin autocomplete			

			//$("#idCount").val(parseInt(0));
			//ajusta tabla
			$("#comprobacion_table").tablesorter({ 
					//cabeceras deshabilitadas del ordenamiento
					headers: { 
						4: {sorter: false }, 
						7: {sorter: false },
						9: {sorter: false },
						11:{sorter: false }  
					} //headers
   		 	}); //tabla
			
		$(".fadeNext").click(function(){   
  			$(this).next().fadeSliderToggle()

 			return false;
 		});
			
			if(navigator.appName!='Netscape'){			
				document.getElementById('msg_div').style.width="800px";
				document.getElementById('msg_div').style.height="300px";
				document.getElementById('msg_div').style.top="25%";
				document.getElementById('msg_div').style.left="18.5%";
			}		
			
			borrarPartida();
			guardaComprobacion();
}//fin ready ó inicializarEventos


function verificaFactura(){
var frm=document.detallecomp;	
	if (frm.fact_chk.checked){
		validaFactura=1;
		$("#rfc_prov_busq_div").html("RFC<span class='style1'>*</span>: ");
		$("#name_prov_busq_div").html("Razón Social<span class='style1'>*</span>: ");
		$("#div_folio").html("Folio Factura<span class='style1'>*</span>: ");
		frm.d_folio.disabled=false;
	}else{
		validaFactura=0;
		$("#rfc_prov_busq_div").html("RFC: ");
		$("#name_prov_busq_div").html("Razón Social: ");
		$("#div_folio").html("Folio Factura: ");
		frm.d_folio.disabled=true;
	}
}


function verificaMotivoSolicitud(id){
var frm=document.detallecomp;
	if (frm.chk_sol.checked){
	flagSolicitud=1;
	frm.sol_select.style.visibility = "visible"; 
	}else{
	flagSolicitud=0;
	frm.motive.value="";
	frm.sol_select.style.visibility = "hidden"; 
	}
}

function cambiaMotivo(){

var frm=document.detallecomp;

	if(frm.sol_select.value!="-1" && frm.sol_select.value!="n"){
	
		var tramite=frm.sol_select.options[frm.sol_select.selectedIndex].value;			
		frm.motive.value="Comprobaci\xf3n de la Solicitud No."+tramite;		
		frm.typedoc.value="1|"+tramite;			
		getSaldoAnticipo(tramite);		
	
	}else{
	
		frm.motive.value="";		
		frm.typedoc.value="";			
		$("#g_saldo").html("");
	}
}

function getSaldoAnticipo(tramite){
	$("#g_saldo").html("Cargando espere...");
	$.ajax({
        url:  "services/Ajax_comprobacion.php",
		type: "POST",
		data: "ant_viaje="+tramite,
		success: function(datos){
				if (datos==""){				
					$("#g_saldo").html("");
					$("#sol_id").val("");
				}else{
				
					$("#g_saldo").html("Total Anticipo: "+datos);
					$("#sol_id").val(tramite);
				}
			}
});//fin ajax
}

function fichaDepos()
{
    datos_total=$("#g_tot").html().split(":");
    datos_anticipo=$("#g_saldo").html().split(":");
    total = datos_total[1];
    total_anticipo = datos_anticipo[1];
    ant_arr = total_anticipo;//.split(".");
    anticipo = ant_arr;
    t = Number(total);
    a = Number(anticipo);
    if(t >= a)
    {
        $("#ficha_deposito").css("display", "none");
    }
    else
    {
        $("#ficha_deposito").css("display", "block");
    }
}

function getSolID(){
	
var frm=document.detallecomp;

    //idSol = document.getElementById("sol_select").value;
    //alert(frm.sol_select.value);
    if(frm.sol_select.value!="-1" || frm.sol_select.value!="n"){
	//frm.motive.value="Comprobaci\xf3n de la Solicitud No."+frm.sol_select.options[frm.sol_select.selectedIndex].value
	getSaldoAnticipo(frm.sol_select.value);

	}else{
	//frm.motive.value="";
	$("#g_saldo").html("");
	}

}

function buscaProveedor(li) {
	if(li==null){ 
	return null;
	}
	if(!!li.extra){
	var valorLista=li.extra[0];
	}else{ 
	var valorLista=li.selectValue;
			$("#load_div").html("Cargando espere...");
			$.ajax({
				//busca el nombre del proveedor en el catalogo en base al RFC
				url: 'services/catalogo_proveedores.php',
				type: "POST",
				data: "nombre="+valorLista+"&tip=1",
				dataType: "html",
				success: function(datos){
						$("#proveedor").val(datos);
						$("#load_div").html("");
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
			$("#load_div").html("Cargando espere...");
			$.ajax({
				//busca el rfc del proveedor en el catalogo en base al nombre
				url: 'services/catalogo_proveedores.php',
				type: "POST",
				data: "nombre="+valorLista+"&tip=2",
				dataType: "html",
				success: function(datos){
						$("#rfc").val(datos);
						$("#load_div").html("");
						}
				});//fin ajax	
	}
}//fin buscaRFC

function seleccionaItem(li) {
	buscaProveedor(li);
}//fin seleccionaItem

function seleccionaItem2(li) {
	buscaRFC(li);
}//fin seleccionaItem

function arreglaItem(row) {
//da el formato a la lista
	return row[0];
}//fin arreglaItem



//Agrega Nuevo Proveedor al catálogo
function nuevoProveedor(nombreProveedor,rfcProveedor,dirFiscal){
	if(rfcProveedor<12 || rfcProveedor>13){
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
		return false;
	}else if(dirFiscal==""){
		alert("Debe ingresar la dirección fiscal del proveedor.");
		return false;
	}else{			
		$.ajax({
		url: 'services/catalogo_proveedores.php',
		type: "POST",
		data: "submit=&nameprov="+nombreProveedor+"&rfcprov="+rfcProveedor+"&dirf="+dirFiscal,
		success: function(datos){
				if (datos==""){				
					$("#proveedor").val("");
					$("#rfc").val("");
					$('#new_proveedor').val("");
					$('#new_p_rfc').val("");
					$('#new_p_addr').val("");					
					$("#proveedor").focus();
				}else{	
				alert(datos);
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
	}
	});
		
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

//ricardo: valida fecha de comprobante
/*******************INICIA********************/
function validaFechaComprob(){
	
}

 function esDigito(sChr){
    var sCod = sChr.charCodeAt(0);
    return ((sCod > 47) && (sCod < 58));
   }
 
   function valSep(oTxt){
    var bOk = false;
    bOk = bOk || ((oTxt.value.charAt(2) == "-") && (oTxt.value.charAt(5) == "-"));
    bOk = bOk || ((oTxt.value.charAt(2) == "/") && (oTxt.value.charAt(5) == "/"));
    return bOk;
   }
 
   function finMes(oTxt){
    var nMes = parseInt(oTxt.value.substr(3, 2), 10);
    var nAno = parseInt(oTxt.value.substr(6), 10);
    var nRes = 0;
    switch (nMes){
     case 1: nRes = 31; break;
     case 2: nRes = 28; break;
     case 3: nRes = 31; break;
     case 4: nRes = 30; break;
     case 5: nRes = 31; break;
     case 6: nRes = 30; break;
     case 7: nRes = 31; break;
     case 8: nRes = 31; break;
     case 9: nRes = 30; break;
     case 10: nRes = 31; break;
     case 11: nRes = 30; break;
     case 12: nRes = 31; break;
    }
    return nRes + (((nMes == 2) && (nAno % 4) == 0)? 1: 0);
   }
 
   function valDia(oTxt){
    var bOk = false;
    var nDia = parseInt(oTxt.value.substr(0, 2), 10);
    bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));
    return bOk;
   }
 
   function valMes(oTxt){
    var bOk = false;
    var nMes = parseInt(oTxt.value.substr(3, 2), 10);
    bOk = bOk || ((nMes >= 1) && (nMes <= 12));
    return bOk;
   }
 
   function valAno(oTxt){
    var bOk = true;
    var nAno = oTxt.value.substr(6);
    bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));
    if (bOk){
     for (var i = 0; i < nAno.length; i++){
      bOk = bOk && esDigito(nAno.charAt(i));
     }
    }
    return bOk;
   }
 
   function valFecha(oTxt){
    var bOk = true;
    if (oTxt.value != ""){
     bOk = bOk && (valAno(oTxt));
     bOk = bOk && (valMes(oTxt));
     bOk = bOk && (valDia(oTxt));
     bOk = bOk && (valSep(oTxt));
     return bOk;
    }
   }
 
   function fechaMayorOIgualQue(fec0, fec1){
    var bRes = false;
    var sDia0 = fec0.value.substr(0, 2);
    var sMes0 = fec0.value.substr(3, 2);
    var sAno0 = fec0.value.substr(6, 4);
    var sDia1 = fec1.value.substr(0, 2);
    var sMes1 = fec1.value.substr(3, 2);
    var sAno1 = fec1.value.substr(6, 4);
    if (sAno0 > sAno1) bRes = true;
    else {
     if (sAno0 == sAno1){
      if (sMes0 > sMes1) bRes = true;
      else {
       if (sMes0 == sMes1)
        if (sDia0 >= sDia1) bRes = true;
      }
     }
    }
    return bRes;
   }
 
   function valFechas(){
    var bOk = false;
    if (valFecha(document.f1.fec0)){
     if (valFecha(document.f1.fec1)){
      if (fechaMayorOIgualQue(document.f1.fec1, document.f1.fec0)){
       bOk = true;
       alert("Ok");
      } else {
       alert("Rango inválido");
       document.f1.fec1.focus();
      }
     } else {
      alert("Fecha inválida");
      document.f1.fec1.focus();
     }
    } else {
     alert("Fecha inválida");
     document.f1.fec0.focus();
    }
   }

/*******************TERMINA******************/

//cambia el valor del input
function cambiaTexto(){
var frm=document.detallecomp;
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
	var frm				= document.detallecomp;
	
	if (frm.select_concepto.value!="-1"){	
	
		var cb_concept		= frm.select_concepto.options[frm.select_concepto.selectedIndex].text;
		var cb_value		= frm.select_concepto.value.split(".");
		frm.c_nam_cb.value	= cb_concept;
	
	
		if(cb_value[1]==1){
			frm.impuesto.disabled=false;
			frm.select_impuesto.disabled=false;
		}else{
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.select_impuesto.disabled=true;
		}
	
	}else{

		frm.c_nam_cb.value="";
	
	}

}

function cambiaResponsable(value){

	$.ajax({
			url: 'services/Ajax_comprobacion.php',
			type: "POST",
			data: "getJefe="+value,
			success: function(datos){
						$("#datos_empleado2").val(datos);
				}//datos
		});//fin ajax

}
		
//IVA
function valor($combo)
{
	var frm=document.detallecomp;
	if(frm.monto.value=="" || frm.monto.value==0)
    {
	    frm.monto.value=0;
	    frm.impuesto.value=0;
	    frm.total.value=0;
		if(frm.select_impuesto.value==0)
        {
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.total.value=parseFloat(frm.monto.value);
		}
        else
        {
            //frm.impuesto.disabled=false;
            //impuesto
            Total = frm.total.value;
            alert(Total)
            Iva = ((frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value / 100)+1);
			var imp = Total / Iva;
            //SubTotal
            frm.impuesto.disabled=false;
			frm.monto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
            //Iva
            MontoIva = (Iva - 1) * imp;
            var tot=MontoIva;
			frm.impuesto.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		}
	}
    else
    {
		if(frm.select_impuesto.value==0)
        {
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.total.value=parseFloat(frm.monto.value);
		}
        else
        {
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
	var frm=document.detallecomp;
	switch (combo) {
		case "0":
		frm.tasa.value=parseFloat(frm.tasa.value);
		break
		case "1":
		frm.tasa.value=parseFloat(frm.tasa.value);
		break
		case "2":
		frm.tasa.value=parseFloat(frm.tasa.value);
		break
	} 
}//Tipo cambio

function getTotal()
{
	var frm=document.detallecomp;
	if(frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value==0 || frm.impuesto.value==0)
    {
        if(frm.monto.value=="" || frm.monto.value==0)
        {
		    frm.total.value=0;
	    }
        else
        {
            frm.total.value=parseFloat(frm.monto.value);
        }
    }
    else
    {
        //impuesto
        var imp=(frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value*0.01)*(frm.monto.value);
        frm.impuesto.disabled=false;
        frm.impuesto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
        //total
        var tot=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value);
        frm.total.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
    }
}

//VALIDA TODOS LOS CAMPOS Y PARAMETROS
function agregarPartida(){

	var frm			= document.detallecomp;
	var partidas	= new Array(frm.select_concepto.options[frm.select_concepto.selectedIndex].text,frm.tasa.value,frm.monto.value);
	var responsable = "";
	//var cat_cecos	= frm.cat_cecos.options[frm.cat_cecos.selectedIndex].text;
	var datos_emp	= frm.datos_empleado.value.split("|");
	var ceco_emp	= datos_emp[0];
	var jefe_emp	= datos_emp[1];
	var idsol		= frm.typedoc.value.split("|");
	

	
	switch(frm.moneda.value){
		case "0":
		   divisa="MXP"
		   break
		case "1":
		   divisa="USD"
		   break
		case "2":
		  divisa="EUR"
		   break
		} 		
	
	//Revisa parametros
	var param		= revisaParametros(partidas,idsol[1],frm.select_concepto.options[frm.select_concepto.selectedIndex].text,frm.total.value,frm.tasa.value,divisa,frm.no_asistentes.value);
	
	//ricardo: probar la función checafecha() con 5 días
	//var dat_fact	= checafecha(frm.fecha.value, 60);	
	var dat_fact	= checafecha(frm.fecha.value, 5);	
	
	
	//Valida que los campos obligatorios esten llenos
	if(frm.select_concepto.value=="" || frm.moneda.value=="" || frm.select_impuesto.value=="" || frm.impuesto.value=="" || frm.fecha.value=="" || frm.monto.value=="" || frm.monto.value==0 || frm.total.value=="" || frm.total.value==0 || frm.referencia_corta.value==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		return false;	
				
	}else if(frm.motive.value.length<5){
		alert("El motivo debe tener al menos 5 caracteres.");
		frm.motive.focus();
		return false;
		
	}else if(dat_fact==false){
		alert("Tu comprobante excede el plazo permitido de 5 dias.");
		return false;
		
	}else if(validaFactura==1){
	
		if(frm.proveedor.value.length==0 || frm.rfc.value.length==0){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		frm.proveedor.focus();
		return false;
		
		}else if(frm.rfc.value.length<12 || frm.rfc.value.length>13){
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
		frm.rfc.focus();
		return false;
		
		}else if(frm.d_folio.value==""){
			alert("Debe ingresar el folio de la factura.");		
			frm.d_folio.focus();
			return false;
			
		}else if(frm.no_asistentes.value>1){
				var asistentes	= frm.no_asistentes.value;
				var monto_part	= frm.monto.value
				var rst_aem		= parseFloat(monto_part)/parseFloat(asistentes);
				var divisa 		= frm.moneda.value
			//verifica que el monto por persona no exceda la politica
			$.ajax({
			url: 'services/Ajax_comprobacion.php',
			type: "POST",
			data: "asistentes="+rst_aem+"&divisa="+divisa,
			success: function(datos){
					if (datos==""){	
						construyePartida();	
						return true;
					}else{
						alert(datos);
						frm.monto.focus();
						return false;		
					}//fin if datos
				}//datos
			});//fin ajax
				
		}else if(frm.d_folio.value!=0 || frm.d_folio.value!=""){
				 var folio_factura=$('#d_folio').attr('value');
				var rfc_proveedor=$('#rfc').attr('value'); 
				
			//busca que el folio este disponible para este proveedor
			$.ajax({
			url: 'services/catalogo_proveedores.php',
			type: "POST",
			data: "find=&folio="+folio_factura+"&rfcprov="+rfc_proveedor,
			success: function(datos){
					if (datos==""){	
						construyePartida();	
						return true;
					}else{
						alert(datos);
						frm.d_folio.focus();
						return false;		
					}//fin if datos
				}//datos
			});//fin ajax
				
		}else if(frm.proveedor.value.length>0 || frm.rfc.length>0){
		//busca al proveedor
		var nombreProveedor = $('#proveedor').attr('value');
		var rfcProveedor = $('#rfc').attr('value'); 
		var dirFiscal="";
		var id=$("#idCount").val(parseInt($("#idCount").val()) + 1);
			$.ajax({
			url: 'services/catalogo_proveedores.php',
			type: "POST",
			data: "busca=&nameprov="+nombreProveedor+"&rfcprov="+rfcProveedor,
			success: function(datos){
					if (datos==""){	
						if(confirm("El proveedor no se encuentra registrado. ¿Desea agregarlo?")){
							nuevoProveedor(nombreProveedor,rfcProveedor,dirFiscal);
							construyePartida();
							return true;
						}else{
							return false;
						}
					}else{	
						construyePartida();			
					}//fin if datos
				}//datos
			});//fin ajax				
		}//if 	
		
	}else if (dat_fact==false){
		return false;
	}else{
			construyePartida();	
			//fichaDepos();
	}//fin if
}//agregar partida


//ricardo: funcion para validar la fecha
function checafecha(strFecha, diasmax){
		var toks=strFecha.split("/");
		
		strFechaN=toks[2]+"/"+toks[1]+"/"+toks[0];		
		var fecha = new Date();
		fecha.setTime(Date.parse(strFechaN));			
		var today=new Date()
    	var agedays = Math.floor((today - fecha) / 24 / 60 / 60 / 1000);
		if(agedays>diasmax){
			var hoystr=<?php echo "\"".date("d/m/Y")."\";";?>			
			document.detallecomp.fecha.value=hoystr;
			return false;
		}else{
			return true;
		}

}

function revisaParametros(partidas,id_solicitud,concepto,monto,tipocambio,moneda,asistentes){		
		//alert(partidas)
		$("#prm_valido").val("");
		$.ajax({
			url: 'services/Ajax_comprobacion.php',
			type: "POST",
			data: "part="+partidas+"&idsol="+id_solicitud+"&con="+concepto+"&monto="+monto+"&tc="+tipocambio+"&ex="+moneda+"&nasist="+asistentes,
			success: function(datos){
					$("#prm_valido").val(datos);
				}//datos
		});//fin ajax  		
		
}


function construyePartida(){
		
		
		var frm			= document.detallecomp;
		var responsable = "";
		var ceco_rst 	= "";
		//var cat_cecos	= frm.cat_cecos.options[frm.cat_cecos.selectedIndex].text;
		var datos_emp	= frm.datos_empleado.value.split("|");
		var ceco_emp	= datos_emp[0];
		var jefe_emp	= datos_emp[1];		
		var valido		= frm.prm_valido.value;		
		var moneda		= frm.moneda.value;
		var datos_emp2	= frm.datos_empleado2.value.split("|");
		var ceco_new	= datos_emp2[0];
		var jefe_new	= datos_emp2[1];		
		
		switch(moneda){
		case "0":
		   divisa="MXP"
		   break
		case "1":
		   divisa="USD"
		   break
		case "2":
		  divisa="EUR"
		   break
		} 		
		
		
			if(frm.typedoc.value!=""){
				var idsol=frm.typedoc.value.split("|");				
			}else{
				var idsol = new Array(0,0);
				
			}
				
	
			
		//if (valido==""){
			
			$("#msg_div").html("");
			$("#msg_div").removeClass('trans');
			$("#msg_div").removeAttr('style');
			
			//Busca al responsable
			if((ceco_new!=ceco_emp) && (frm.datos_empleado2.value!="")){
				responsable	=jefe_new;
				ceco_rst	=ceco_new;
			}else{
				responsable	=jefe_emp;
				ceco_rst	=ceco_emp;
			}
		
			//conteo de la partida
			frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
			
			//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
			id=parseInt($("#comprobacion_table").find("tr:last").find("td").eq(0).html());
		
			if(isNaN(id)){			
				id=1;		
			}else{			
				id+=parseInt(1);
			}
		
			var row=frm.rowCount.value;
		
			if (frm.proveedor.value!=""){
				prov_name=frm.proveedor.value;
			}else{
				prov_name="Sin Proveedor";
			}
		
			if (frm.rfc.value!=""){
				prov_rfc=frm.rfc.value;
			}else{
				prov_rfc="";
			}
		
			if (frm.referencia_corta.value!=""){
				ref_cort=frm.referencia_corta.value;
			}else{
				ref_cort="Sin Comentario";
			}
			
			if (validaFactura==1){
				fact_doc="Factura:"+frm.d_folio.value;
			}else{
				fact_doc="Sin Factura";
			}			
			//Creamos la nueva fila y sus respectivas columnas
			var nuevaFila='<tr>';
			nuevaFila+="<td>"+id+"<input type='hidden' name='row"+id+"' id=row'"+id+"' value='"+id+"' readonly='readonly' /></td>";
			nuevaFila+="<td><input type='hidden' name='fecha"+id+"' id='fecha"+id+"' value='"+frm.fecha.value+"' readonly='readonly' />"+frm.fecha.value+"</td>";
			nuevaFila+="<td><input type='hidden' name='concepto"+id+"' id='concepto"+id+"' value='"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"' 	readonly='readonly' />"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"</td>";
			nuevaFila+="<td><input type='hidden' name='referencia"+id+"' id='referencia"+id+"' value='"+ref_cort+"' readonly='readonly' />"+ref_cort+"</td>"; 
			nuevaFila+="<td><input type='hidden' name='cant_asistentes"+id+"' id='cant_asistentes"+id+"' value='"+frm.no_asistentes.value+"' readonly='readonly' />"+frm.no_asistentes.value+"</td>";
			nuevaFila+="<td><input type='hidden' name='proveedor"+id+"' id='proveedor"+id+"' value='"+prov_name+"' readonly='readonly' />"+prov_name+" "+"<input type='hidden' name='rfc"+id+"' id='rfc"+id+"' value='"+prov_rfc+"' readonly='readonly' />"+prov_rfc+"</td>"; 
			nuevaFila+="<td><input type='hidden' name='flag_factura"+id+"' id='flag_factura"+id+"' value='"+frm.d_folio.value+"' readonly='readonly' />"+fact_doc+"</td>";
			//nuevaFila+="<td><input type='hidden' name='resp"+id+"' id='resp"+id+"' value='"+responsable+"|"+ceco_rst+"' readonly='readonly' />"+responsable+"</td>";
			nuevaFila+="<td id='m'><input type='hidden' name='mnt"+id+"' id='mnt"+id+"' value='"+frm.monto.value+"' readonly='readonly' />$"+frm.monto.value+"</td>"; 
			nuevaFila+="<td><input type='hidden' name='divisa"+id+"' id='divisa"+id+"' value='"+divisa+"' readonly='readonly' />"+divisa+"</td>"; 
			nuevaFila+="<td><input type='hidden' name='tasa"+id+"' id='tasa"+id+"' value='"+frm.tasa.value+"' readonly='readonly' />$"+frm.tasa.value+"</td>"; 
			nuevaFila+="<td><input type='hidden' name='pimpuesto"+id+"' id='pimpuesto"+id+"' value='"+frm.select_impuesto.value+"' readonly='readonly' />"+frm.select_impuesto.value+" "+"<input type='hidden' name='imp"+id+"' id='imp"+id+"' value='"+frm.impuesto.value+"' readonly='readonly' />$"+frm.impuesto.value+"</td>"; 
			nuevaFila+="<td><input type='hidden' name='tot"+id+"' id='tot"+id+"' value='"+frm.total.value+"' readonly='readonly' />$"+frm.total.value+"</td>";
			nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='"+id+"' onmousedown='borrarPartida(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
			nuevaFila+= '</tr>'; 
			//gran total
			var sumMnt=parseFloat(frm.monto.value)+parseFloat(frm.t_subtotal.value);
			var sumIVA=parseFloat(frm.impuesto.value)+parseFloat(frm.t_iva.value);
			var sumTotal=parseFloat(frm.total.value)+parseFloat(frm.t_total.value);		
			frm.t_subtotal.value=Math.round(sumMnt * Math.pow(10, 2)) / Math.pow(10, 2);
			frm.t_iva.value=Math.round(sumIVA * Math.pow(10, 2)) / Math.pow(10, 2);
			frm.t_total.value=Math.round(sumTotal * Math.pow(10, 2)) / Math.pow(10, 2);
			$("#g_sbt").html("Subtotal: "+parseFloat(frm.t_subtotal.value));
			$("#g_iva").html("IVA: "+parseFloat(frm.t_iva.value));
			$("#g_tot").html("Total: "+parseFloat(frm.t_total.value));
			
			
			//restablece los campos
			$("#prm_valido").val("");
			$("#comprobacion_table").append(nuevaFila);
			$("#monto").val(0);
			$("#tasa").val(1);
			$("#impuesto").val(0);
			$("#total").val(0);
			$("#referencia_corta").val("");
			$("#d_folio").val("");
			$("#proveedor").val("");
			$("#rfc").val("");
			$('#select_concepto').find('option:first').attr('selected', 'selected').parent('select');	
			$('#moneda').find('option:first').attr('selected', 'selected').parent('select');
			$('#select_impuesto').find('option:first').attr('selected', 'selected').parent('select');			
			frm.impuesto.disabled=false;
			frm.select_impuesto.disabled=false;
			
			guardaComprobacion();
		
		//}else{
		//	$("#msg_div").fadeIn(2500);
		//	$("#msg_div").addClass("trans");
		//	$("#msg_div").attr('style',"z-index:1;filter:alpha(opacity=82);float:left;-moz-opacity:.82;opacity:.82;   display:");
		//	$("#msg_div").html("<p>Monto invalido, verifique su politica de viaticos</p><p><input type='button' name='close_msg' id='close_msg' value='Aceptar' class='boton' onclick='closeMsg();'></p>");
		//	return false;
		//	frm.monto.focus();
		//}
}//construye partida

function borrarPartida(id){
var firstRow=parseInt($("#comprobacion_table").find("tr:first").find("td").eq(0).html());
var lastRow=parseInt($("#comprobacion_table").find("tr:last").find("td").eq(0).html());
var frm=document.detallecomp;
var row=frm.rowCount.value;
	$("img.elimina").click(function(){
			var mnt=parseFloat($("#mnt"+id).val());
			var iva=parseFloat($("#imp"+id).val());
			var total=parseFloat($("#tot"+id).val());			
			var subtractMnt=0;
			var subtractIVA=0;
			var subtractTotal=0;	
			//substraccion		
			subtractMnt=parseFloat(frm.t_subtotal.value)-mnt;
			subtractIVA=parseFloat(frm.t_iva.value)-iva;
			subtractTotal=parseFloat(frm.t_total.value)-total;
			//calculo final
			frm.t_subtotal.value=Math.round(subtractMnt * Math.pow(10, 2)) / Math.pow(10, 2);
			frm.t_iva.value=Math.round(subtractIVA * Math.pow(10, 2)) / Math.pow(10, 2);
			frm.t_total.value=Math.round(subtractTotal * Math.pow(10, 2)) / Math.pow(10, 2);			
			
			//impresion en pantalla
			$("#g_sbt").html("Subtotal: "+parseFloat(frm.t_subtotal.value));
			$("#g_iva").html("IVA: "+parseFloat(frm.t_iva.value));
			$("#g_tot").html("Total: "+parseFloat(frm.t_total.value));
			$("#prm_valido").val("");
			$(this).parent().parent().parent().fadeOut("normal", function () { 
			$(this).remove();
			
			guardaComprobacion();
			//fichaDepos();
		});


		return false;
	});
	
}//borrar partida


function guardaComprobacion(){
var frm=document.detallecomp;
	id= parseInt($("#comprobacion_table").find("tr:last").find("td").eq(0).html());
	if(isNaN(id)){
	//$("#guardarComp").attr("disabled", "disabled"); 
	frm.t_subtotal.value=parseFloat(0.00);
	frm.t_iva.value=parseFloat(0.00);
	frm.t_total.value=parseFloat(0.00);
	$("#g_sbt").html("Subtotal: "+frm.t_subtotal.value);
	$("#g_iva").html("IVA: "+frm.t_iva.value);
	$("#g_tot").html("Total: "+frm.t_total.value);
	}
    else
    {
        datos_total=$("#g_tot").html().split(":");
        datos_anticipo=$("#g_saldo").html().split(":");
        if(datos_anticipo != "&nbsp;")
        {
           total = datos_total[1];
           anticipo = datos_anticipo[1];
           t = Number(total);
           a = Number(anticipo);
        }
        else
        {
           total = datos_total[1];
           ant_arr = total_anticipo;//.split(".");
           anticipo = 0;
           t = Number(total);
           a = Number(anticipo);
        }
        
        /*
        if(t >= a)
        {
            $("#guardarComp").removeAttr("disabled");
        }
        else
        {
            $("#guardarComp").attr("disabled", "disabled");
        }*/
    }
 }

function closeMsg(){
			$("#msg_div").fadeOut(2500);
			$("#msg_div").html("");
			$("#msg_div").removeClass('trans');
			$("#msg_div").removeAttr('style');

}

function cambiaNombreBtn(obj){
	if(obj.value=='Agregar Nuevo Proveedor'){
		$("#msg_div").removeAttr('style');
		$('#add_rem_prv').attr("style","background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
		obj.value='Cerrar Panel de Nuevo Proveedor' ;
	}else{
		$("#msg_div").removeAttr('style');
		$('#add_rem_prv').attr("style","background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
		obj.value='Agregar Nuevo Proveedor' ;
	}
}

function CxD(){	
	var frm=document.detallecomp;
	var DiasV=0;
	
	if($("#TotalDias").val()==0 || $("#TotalDias").val()=="")
		DiasV=1;

	else
		DiasV=$("#TotalDias").val();
	
	var auxAnt=0;
	auxAnt=number_format(parseInt(DiasV)* parseFloat(frm.TotC.value),2,".",",");
	frm.anticipoC.value=auxAnt;
	frm.anticipo.value=auxAnt;
}
</script>
<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
<style type="text/css">
.style1 {color: #FF0000}
.fader{opacity:0;display:none;}
.trans{
	background-color:#D7D7D7;
	color:#0000FF;
	position:absolute;
	vertical-align:middle;	
	width:690px;
	height:200px;	     
    padding:65px;
	font-size:15px;
	font-weight:bold;
	top:26%;
	left:18%;
	
    }
	.boton{	
	background:#666666;
	color:#FFFFFF;
	border-color:#CCCCCC;
	}
</style>
<div>
  
</div>
<div id="Layer1">
<form action="comprobacion_cash_drawer.php?save" method="post" name="detallecomp" id="detallecomp">
	<table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="170">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td  colspan="9">Motivo de la comprobaci&oacute;n<span class="style1">*</span>:
    &nbsp;
    <input type="text" name="motive" id="motive" size="50"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>    
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="4">&nbsp;</td>
    <td width="85">&nbsp;</td>
    <td>&nbsp;</td>
    <td width="7">&nbsp;</td>
    <td width="5">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="66">&nbsp;</td>
  </tr></table><br />
  <table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;"><tr><td colspan="2">
      <tr>
        <td colspan="13"><h3 align="center">Registro de Partidas</h3></td>
      </tr>
      <tr>
        <td width="2">&nbsp;</td>
        <td width="85"><div align="left">Concepto<span class="style1">*</span>: <br />
        </div></td>
        <td colspan="4"><div align="left">

        <select name="select_concepto" id="select_concepto" onchange="">
	            	<!-- <option value="-1">Seleccione...</option> -->
	          	    <?php
						//Cat&aacute;logo de conceptos
						$idusuario=$_SESSION["idusuario"];

						function Get_clasificaciones2($usuario){

							$nivelUser=$_SESSION["nivelUser"];

				        	$cnn    = new conexion();
					        $query ="select cp_concepto,cp_retencion,dc_id, cp_deducible as deducible from cat_conceptos where cp_activo=true and dc_catalogo=2 and cp_empresa_id = " . $_SESSION["empresa"] . " order by cp_concepto";
                            //error_log($query);
					        $rst    = $cnn->consultar($query);

							while($fila=mysql_fetch_assoc($rst)){
						        echo "<option id=".$fila["cp_concepto"]." value=".$fila["dc_id"]."&".$fila["p_cantidad"].">".$fila["cp_concepto"]."</option>";
				    	    }
					    }
						$cat=Get_clasificaciones2($idusuario);
					?>
			</select>
            <input type="hidden" name="c_nam_cb" id="c_nam_cb" readonly="readonly" value="" />
        </div></td>
        <td width="4">&nbsp;</td>
        <td width="10">&nbsp;</td>
        <td><div align="left">Fecha Comprobante<span class="style1">*</span>: </div></td>
        <td colspan="3"><div align="left">
          <input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="15" readonly="readonly" onfocus="return checafecha(document.detallecomp.fecha.value, 5);" />
        <img src="../../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /></div></td>
        <td width="32">&nbsp;</td>
      </tr>
      <tr>
        <td width="2" height="26">&nbsp;</td>
        <td width="85"><div align="left">Subtotal<span class="style1">*</span>: $ </div></td>
        <td width="83"><div align="left">
            <input name="monto" id="monto" value="0" onkeyup="getTotal();" onkeypress="return validaNum(event)"  size="10" autocomplete="off"/>
        </div></td>
        <td width="60"><div align="left">IVA<span class="style1">*</span>: </div></td>
        <td colspan="2"><div align="left">
            <select name="select_impuesto" id="select_impuesto" onchange="valor(this.value);">
              <?php
			$porcentajes=array("0","16","11");
			for($i=0;$i<count($porcentajes);$i++){
			echo "<option id=".$i." value=".$porcentajes[$i].">".$porcentajes[$i]."</option>";
			}
			?>
            </select>
          %
          &nbsp;</div></td>
        <td width="4">&nbsp;</td>
        <td width="10">&nbsp;</td>
        <td width="143"><div align="left">Divisa<span class="style1">*</span>: </div></td>
        <td width="77"><div align="left">
          <select name="moneda" id="moneda" onchange="tipoCambio(this.value);" >
            <?php
			$tipoCambio=array("MXP","USD");	
			for($i=0;$i<count($tipoCambio);$i++){
			echo "<option value=".$i.">".$tipoCambio[$i]."</option>";
			}
			?>
          </select>
        </div></td>
        <td colspan="2">&nbsp;</td>
        <td width="32">&nbsp;</td>
      </tr>
      <tr>
        <td width="2">&nbsp;</td>
        <td width="85"><div align="left">Total<span class="style1">*</span>: $ </div></td>
        <td width="83"><div align="left">
            <input name="total"  id="total" value="0" size="10" onkeypress="return validaNum(event)" autocomplete="off" />
        </div></td>
        <td width="60"><div align="left">IVA<span class="style1">*</span>: $</div></td>
        <td colspan="2"><div align="left">
            <input name="impuesto" id="impuesto" value="0" size="8" onkeypress="return validaNum(event);" autocomplete="off" />
        </div></td>
        <td width="4">&nbsp;</td>
        <td width="10">&nbsp;</td>
        <td><div align="left">Tasa MXP: $ </div></td>
        <td><div align="left">
          <input name="tasa" id="tasa" value="1.00" size="8" onkeypress="return validaNum(event)" autocomplete="off" />
        </div></td>
        <td colspan="2">&nbsp;</td>
        <td width="32">&nbsp;</td>
      </tr>
      <tr>
        <td width="2">&nbsp;</td>
        <td width="85"><div align="left">Comentario<span class="style1">*</span>: </div></td>
        <td colspan="10"><div align="left">
            <input name="referencia_corta" id="referencia_corta" size="84" />
        </div></td>
        <td width="32">&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><!--<div align="left">No.Asistentes</div>--></td>
        <td colspan="2"><div align="left">
            <input type="hidden" name="no_asistentes" id="no_asistentes" value="1" size="10" onkeypress="return validaNum(event)" />
        </div></td>
        <td width="57">&nbsp;</td>
        <td width="61">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        <td width="1">&nbsp;</td>
        <td width="128">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
	  <tr>
        <td colspan="13">&nbsp;</td>
      </tr>

      <tr>
        <td colspan="13"><h3 align="center">Datos de proveedor</h3></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="4">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="left">Documento:</div></td>
        <td colspan="2"><input type="checkbox" name="fact_chk" id="fact_chk" checked="checked" onclick="verificaFactura();" />
          Cuenta con Factura </td>
        <td>&nbsp;</td>
        <td colspan="3"><div align="left" id="div_folio">Folio Factura<span class="style1">*</span>:</div></td>
        <td colspan="2"><div align="left">
          <input name="d_folio" id="d_folio" size="8" onkeypress="return validaNum(event)" />
        </div></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div id="rfc_prov_busq_div" align="left">RFC<span class="style1">*</span>: 
        </div></td>
        <td colspan="3"><div align="left">
      <input name="rfc" type="text" id="rfc" value="" size="30" maxlength="13"  onkeyup="this.value = this.value.toUpperCase();" />
    </div></td>
        <td colspan="3"><div id="name_prov_busq_div" align="left">
          <div align="left">Raz&oacute;n Social<span class="style1">*</span>: </div>
        </div></td>
        <td colspan="2"><div align="left">
          <input name="proveedor" type="text" id="proveedor" value="" size="30" />
        </div></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="2">&nbsp;</td>
        <td colspan="11"><div align="center" class="style1" id="load_div" ></div></td>
        <td width="32">&nbsp;</td>
      </tr>
   </td>
    </tr>
  </table>
<br />
<center><input type="button" class="fadeNext" style="background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"  name="add_rem_prv" id="add_rem_prv" onclick="cambiaNombreBtn(this);" value="Agregar Nuevo Proveedor">
<div class="fader" align="right">

<table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
  <tr>
    <td colspan="8"><div align="center">
          <h3>Agregar nuevo proveedor</h3>
  </div>  </tr>
  <tr>
    <td width="38">&nbsp;</td>
    <td width="132"><div align="right"><em><strong>Raz&oacute;n Social: </strong></em></div></td>
    <td colspan="5"><div align="left">
      <input name="new_proveedor" type="text" id="new_proveedor" value="" size="50" />
    </div></td>
    <td width="77">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div align="right"><em><strong>RFC: </strong></em></div></td>
    <td width="194"><div align="left">
      <input name="new_p_rfc" type="text" id="new_p_rfc" value="" size="30" maxlength="13"/>
    </div></td>
    <td width="7">&nbsp;</td>
    <td width="7">&nbsp;</td>
    <td width="37">&nbsp;</td>
    <td width="266">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div align="right"><em><strong>Domicilio Fiscal:</strong></em></div></td>
    <td colspan="5"><input name="new_p_addr" id="new_p_addr" value="" size="80" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="6">
      <div align="center">
        <input type="button" name="agregar" value="    Agregar" onclick="nuevoProveedor(new_proveedor.value,new_p_rfc.value,new_p_addr.value);" style="background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
        </div></td><td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="6">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
</center>
<br />
<div id="msg_div" align="center" >
  </div><br />
<div align="center">
	    <input name="registrar_comp" type="button" id="registrar_comp" value="    Registrar partida"  onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
        <input type="hidden" name="prm_valido" id="prm_valido" readonly="readonly" value="" />
  </div>

	
	
	<?php
	}

		?> <center><h3>Detalle de la Comprobaci&oacute;n de Caja Chica</h3></center> <?php
		forma_comprobacion();
		?>
<table id="comprobacion_table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    <th width="3%">No.</th> 
	<th width="5%">Fecha</th>
    <th width="7%">Concepto</th>
	<th width="8%">Comentario</th> 
    <th width="8%">No. Asist.</th> 
	<th width="13%">Proveedor</th>
    <th width="9%">Documento</th>
    <th width="5%">Monto </th>
	<th width="5%">Divisa</th> 
    <th width="6%">Tasa</th>  
    <th width="6%">IVA</th>
	<th width="4%">Total</th>
	<th width="10%">Herramientas</th>
</tr> 
</thead> 
<tbody> 
<!-- cuerpo tabla-->
</tbody> 
</table> 
<div align="right">
 <div id="g_saldo">&nbsp;</div><br />
 <div id="g_sbt">Total: 0.00</div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="0.00" /><br />
  <div id="g_iva">IVA: 0.00</div><input type="hidden" readonly="readonly" name="t_iva" id="t_iva" value="0.00" /><br />
  <div id="g_tot">Saldo a reembolsar: 0.00</div><input type="hidden" readonly="readonly" name="t_total" id="t_total" value="0.00" />
</div>
<!--
<div id="ficha_deposito">
<table width="550" align="center" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
   <tr>
      <td colspan="4" align="center" style="color:#DF0101">
         <b>El monto a comprobar es menor al anticipo, la comprobaci&oacute;n no se puede guardar.</b><br>
      </td>
   </tr>
</table>
</div>
-->
<br />

<div align="center">
<table>
 <tr>
    <td width="100">&nbsp;</td>
    <td ></td>
    <td ></td>
	<td colspan="2">Departamento al que se cargar&aacute;:</td>
    <td colspan="1">
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
    <td></td>
  </tr>

  <tr>
    <td width="80">&nbsp;</td>
    <td ></td>
	<td colspan="5">
	</td>
    <td></td>
  </tr>  

  <tr>
    <td width="80">&nbsp;</td>
    <td width="80">&nbsp;</td>
    <td  valign="top">
		<div align="right" id="req">Observaciones:</div>
	</td>
    <td colspan="4">
		<div align="left">
      		<textarea name="observ" cols="80"rows='5' id="observ"></textarea>
    	</div>
	</td>
    <td width="80">&nbsp;</td>
  </tr>
</table>
</div>
<br>
<div align="center">
  <input type="submit" id="guardarComp" name="guardarComp" value="     Guardar Comprobacion"  onclick="guardaComprobacion();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
</div>
<input type="hidden" name="typedoc" id="typedoc" readonly="readonly"  value="" />
<input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
<input type="hidden" name="ne" id="ne" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
<input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
<input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
<input type="hidden" name="datos_empleado" id="datos_empleado" value="<?php echo $datos;?>" readonly="readonly" />
<input type="hidden" name="datos_empleado2" id="datos_empleado2" value="" readonly="readonly" />
<input type="hidden" id="sol_id" name="sol_id" value="0" readonly="readonly"/>
<input type="hidden" id="rowCountCecos" name="rowCountCecos" value="0" readonly="readonly"/>
<input type="hidden" id="porcentA" name="porcentA" value="0" readonly="readonly"/>
<input type="hidden" name="empresa" id="empresa" value="<?php echo $_SESSION["empresa"]; ?>" readonly="readonly" />
</form>
<?php
}
?>
