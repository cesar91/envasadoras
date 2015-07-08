<?php
/********************************************************
*         T&E Módulo de Nueva Comprobación              *
* Creado por:	  Jorge Usigli Huerta 11-Ene-2010		*
* Modificado por: Jorge Usigli Huerta 17-Mar-2010		*
* PHP, jQuery, JavaScript, CSS                          *
*********************************************************/
if(isset($_POST['guardarComp'])){
	require_once("services/func_comprobacion.php");
	//valida que haya partidas
	if(isset($_POST['rowCount']) || $_POST['rowCount']!=0){		
		$cMotive=$_POST['motive'];
		$tAmnt=$_POST['t_subtotal'];
		$tIVA=$_POST['t_iva'];
		$tTotal=$_POST['t_total'];
                
		//valida que haya montos
		if($tAmnt==0 || $tTotal==0 || $tAmnt=='NaN' || $tIVA=='NaN' || $tTotal=='NaN' || $tAmnt==NULL || $tIVA==NULL || $tTotal==NULL){
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
	
		//Datos del empleado
		$iduser=$_POST['iu'];
		$numempleado=$_POST['ne'];
		$Empleado= new Usuario();
		$Empleado->Load_Usuario_By_No_Empleado($numempleado);
		$jefeEmpleado=$Empleado->Get_dato("jefe");
        $Grupo = $Empleado->Get_dato("grupo_aprobador");
        $centroCosto = $Empleado->Get_dato("idcentrocosto");
        $autor = $Empleado->Get_dato("idempleado");
        $CreditCard="";

        $cnn    = new conexion();
        $qCC = "select cc_id from cat_cecos where cc_centrocostos='$centroCosto'";
		$Res    = $cnn->consultar($qCC);
		$ceco = mysql_result($Res,0,"cc_id");

        //verifica que tenga jefe
		//////////////////////////////////
        $Usu = new Usuario();
        $jefeEmpleado = $Usu->searchAprobador($Grupo,$tTotal,$ceco,3);

        if($jefeEmpleado==-1)
        {
            header("Location: ./index.php?ccNegative");
            exit();
        }
        $auxId=$Usu->SearchDelegado($jefeEmpleado);

        if($auxId!=0)
            $jefeEmpleado=$auxId;

        $Usu->Load_Usuario($jefeEmpleado);
        $jefeEmpleado=$Usu->Get_dato("u_id");
        ///////////////////////////////////
		
		if($jefeEmpleado!=0 || $jefeEmpleado!=""){
		//obtiene el id usuario del jefe
        $Usuario= new Usuario();
		$Usuario->Load_Usuario_By_ID($jefeEmpleado);
		$jefeEmpleado=$Usuario->Get_dato("u_id");

        //indica si se esta comprobando una solicitud
		#$comprobacion_solicitud = trim($_POST['typedoc']);
		#$datos 	= explode("|",$comprobacion_solicitud);
		#$tipo	= $datos[0];
		#$idSolicitud = $datos[1];
        #exit();
        /*if($comprobacion_solicitud!="")
        {
  			echo $montoSolicitado = Get_Dato("sa_anticipo",$tipo);
            if($tTotal!=$montoSolicitado)
            {
		        comprobacionParcial($idSolicitud, $tTotal);
            }
            else
            {
		        comprobacionSolicitud($idSolicitud, $tTotal);
            }
		}*/


		//************Trámite
		$comprobacion = 0;
	    $comprobacion=$_POST['com'];
        //Add_new ($idTramite,$tAmnt, $tIVA, $tTotal, $montoexc,$cMotive,$ccosto,1,$CreditCard);
        actualizaComprobacion($comprobacion,$tAmnt, $tIVA, $tTotal, $montoexc,$cMotive,$ceco,1,$CreditCard);
		//************Comprobación
		//obtiene el id de la comprobacion
		$cnn    = new conexion();
		$query="select co_id, co_tipo from comprobaciones where co_mi_tramite=".$comprobacion.";" ;
		$rst    = $cnn->consultar($query);
		$fila=mysql_fetch_assoc($rst);
		$co_id=$fila['co_id'];
		$tipo = $fila['co_tipo'];#tipo de comprobacion 1:comprobacion de viaje, 2:comprobacion Amex

		for($i=1;$i<=$_POST['rowCount'];$i++)
        {
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
			   if($cFolio!="" || $cFolio!=0)
               {
			       $cFlagFactura=1;
			   }
               else
               {
			       $cFlagFactura=0;
			   }
					
					//ID concepto
					$cnn    = new conexion();
					$query = sprintf("select dc_id from cat_conceptos where cp_concepto='%s' and cp_empresa_id = " . $_SESSION["empresa"],$cConc) ;
					$rst    = $cnn->consultar($query);
					$fila=mysql_fetch_assoc($rst);
					$cConc=$fila['dc_id'];
					
					//ID proveedor
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
					$resp = 0;
					Add_detalle($co_id,$cCargo,$cConc,$cRef,$cP_RFC,$cAmt,$cImp,$cImpVal,$cTotal,$forma_gasto,$montoexc,$ivaexc,$idProv,$kilometraje,$cDate,$ivaR,$isrR,$cDate,$cExch,$cComensales,$cRate,$tipo,$cFlagFactura,$cFolio,$resp,$ceco);
					
			}//for	
		$sObser = "";
        $Tramite = new Tramite();
        $Tramite -> Load_Tramite($comprobacion);
        $Tramite -> Modifica_Etapa(3);
        $Tramite->Nex_Set($comprobacion,$sObser,$jefeEmpleado,"Comprobacion");
        $observaciones=$_POST['observaciones'];
	    if($observaciones==""){
			header("Location: ./index.php?errsave");
		}else{
            $Tramite->save_observaciones($observaciones,$comprobacion,$autor);
  		}
		header("Location: ./index.php?oksave");
		}else{
		header("Location: ./index.php?errjefe");
		die();
		}//if jefe
		
	}else{	
	header("Location: ./index.php");
	die();
	}//if row	
}//if guarda comp




//*****************************//COMPROBACION GENERAL//*****************************//
if(isset($_GET['edit'])){ 	

	function forma_comprobacion(){
?>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tableEditor.js" type="text/javascript"></script>

		
<script language="JavaScript" type="text/javascript">
//variables
var doc;
var validaFactura=1;
var flagSolicitud=0;

function verificaFactura(){
var frm=document.detallecomp;	
	if (frm.fact_chk.checked){
		validaFactura=1;
		$("#rfc_prov_busq_div").html("RFC<span class='style1'>*</span>: ");
		$("#name_prov_busq_div").html("Razón Social<span class='style1'>*</span>: ");
		$("#div_folio").html("Folio<span class='style1'>*</span>: ");
		frm.d_folio.disabled=false;
	}else{
		validaFactura=0;
		$("#rfc_prov_busq_div").html("RFC: ");
		$("#name_prov_busq_div").html("Razón Social: ");
		$("#div_folio").html("Folio: ");
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
		data: "submit="+tramite,
		success: function(datos){
				if (datos==""){				
					$("#g_saldo").html("");
				}else{	
				
					$("#g_saldo").html("Total Anticipo");
				}
			}
});//fin ajax


}


doc = $(document);
doc.ready(inicializarEventos);//cuando el documento esté listo
function inicializarEventos(){			
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

			
			borrarPartida();
			guardaComprobacion();
			//observaciones();
}//fin ready ó inicializarEventos


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
				//alert(datos);
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
var frm=document.detallecomp;
	if(frm.select_concepto.value==1){
		frm.impuesto.disabled=false;
		frm.select_impuesto.disabled=false;
	}else{
		frm.impuesto.value=0;
		frm.impuesto.disabled=true;
		frm.select_impuesto.disabled=true;
	}

}
		
//IVA
function valor($combo){
	var frm=document.detallecomp;
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
	var frm=document.detallecomp;
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

function getTotal(){
var frm=document.detallecomp;
	if(frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value==0 || frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value=="No" || frm.impuesto.value==0){
		if(frm.monto.value=="" || frm.monto.value==0){
		frm.total.value=0;
		}else{
		frm.total.value=parseFloat(frm.monto.value);
		}
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


function agregarPartida(){
	var frm=document.detallecomp;
		//Valida que los campos obligatorios esten llenos
	if(frm.select_concepto.value=="" || frm.moneda.value=="" || frm.select_impuesto.value=="" || frm.impuesto.value=="" || frm.fecha.value=="" || frm.monto.value=="" || frm.monto.value==0 || frm.total.value=="" || frm.total.value==0 || from.ref_cort.value == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes");
		return false;	
				
	}else if(frm.motive.value.length<5){
		alert("El motivo debe tener al menos 5 caracteres.");
		frm.motive.focus();
		return false;
		
	}else if(validaFactura==1){
		if(frm.proveedor.value.length==0 || frm.rfc.value.length==0){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes");
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
						//alert(datos);
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
		
	}else{
			construyePartida();	
	}//fin if
}//agregar partida



function construyePartida(){
		var frm=document.detallecomp;
		//conteo de la partida
		frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		id=parseInt($("#comprobacion_table").find("tr:last").find("td").eq(0).html());
		if(isNaN(id)){
			id=1;
		}else{
			id+=parseInt(1);
		}
		//Creamos la nueva fila y sus respectivas columnas
		var moneda=frm.moneda.value;
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
		
		var nuevaFila='<tr>';
		
		nuevaFila+="<td>"+id+"<input type='hidden' name='row"+id+"' id=row'"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='fecha"+id+"' id='fecha"+id+"' value='"+frm.fecha.value+"' readonly='readonly' />"+frm.fecha.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='concepto"+id+"' id='concepto"+id+"' value='"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"' 	readonly='readonly' />"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"</td>";
		nuevaFila+="<td><input type='hidden' name='referencia"+id+"' id='referencia"+id+"' value='"+ref_cort+"' readonly='readonly' />"+ref_cort+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='cant_asistentes"+id+"' id='cant_asistentes"+id+"' value='"+frm.no_asistentes.value+"' readonly='readonly' />"+frm.no_asistentes.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='proveedor"+id+"' id='proveedor"+id+"' value='"+prov_name+"' readonly='readonly' />"+prov_name+"</td>";
		nuevaFila+="<td><input type='hidden' name='rfc"+id+"' id='rfc"+id+"' value='"+prov_rfc+"' readonly='readonly' />"+prov_rfc+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='flag_factura"+id+"' id='flag_factura"+id+"' value='"+frm.d_folio.value+"' readonly='readonly' />"+fact_doc+"</td>";
		nuevaFila+="<td id='m'><input type='hidden' name='mnt"+id+"' id='mnt"+id+"' value='"+frm.monto.value+"' readonly='readonly' />$"+frm.monto.value+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='divisa"+id+"' id='divisa"+id+"' value='"+divisa+"' readonly='readonly' />"+divisa+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='tasa"+id+"' id='tasa"+id+"' value='"+frm.tasa.value+"' readonly='readonly' />$"+frm.tasa.value+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='pimpuesto"+id+"' id='pimpuesto"+id+"' value='"+frm.select_impuesto.value+"' readonly='readonly' />"+frm.select_impuesto.value+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='imp"+id+"' id='imp"+id+"' value='"+frm.impuesto.value+"' readonly='readonly' />$"+frm.impuesto.value+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='tot"+id+"' id='tot"+id+"' value='"+frm.total.value+"' readonly='readonly' />$"+frm.total.value+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='"+id+"' onmousedown='borrarPartida(this.id);'  /></div></td>";
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
}//construye partida

function borrarPartida(id, tipo){
var frm=document.detallecomp;
var row=frm.rowCount.value;
	$("img.elimina").click(function(){
	$.ajax({
		url: 'services/Ajax_comprobacion.php',
		type: "POST",
		data: "delete=&idtramite="+id+"&tipo="+tipo,
        success: function(datos){
                if (datos=="")
                {	
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
				}
                else
                {	
					alert("No se ha podido elminiar la partida.");
				}
			}
		});//fin ajax	
		$(this).parent().parent().parent().fadeOut("normal", function () { 
		$(this).remove();	
		guardaComprobacion();
				
		});
		return false;		
		
	});
	
}//borrar partida


function guardaComprobacion(){
var frm=document.detallecomp;
	id= parseInt($("#comprobacion_table").find("tr:last").find("td").eq(0).html());
	if(isNaN(id)){
	$("#guardarComp").attr("disabled", "disabled"); 
	frm.t_subtotal.value=parseFloat(0.00);
	frm.t_iva.value=parseFloat(0.00);
	frm.t_total.value=parseFloat(0.00);
	$("#g_sbt").html("Subtotal: "+frm.t_subtotal.value);
	$("#g_iva").html("IVA: "+frm.t_iva.value);
	$("#g_tot").html("Total: "+frm.t_total.value);
	}
    else
    {
        datos_total=parseFloat($("#t_total").val());
        datos_anticipo=parseFloat($("#t_total_pendiente").val());
        if(datos_anticipo >= datos_total)
        {
            $("#guardarComp").attr("disabled", "disabled");
        }
        else
        {
            $("#guardarComp").removeAttr("disabled");
        }
    }
}

function observaciones(){
var frm=document.detallecomp;
 /*if (frm.observaciones.value==""){
 	$("#guardarComp").attr("disabled", "disabled"); 
 }else{
 	$("#guardarComp").removeAttr("disabled"); 
 }*/
}

</script>
<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
<style type="text/css">
.style1 {color: #FF0000}
</style>
<div id="Layer1">
<form action="edit_comprobacion.php?save" method="post" name="detallecomp" id="detallecomp">
	<table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td width="170">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="9">Comprobaci&oacute;n n&uacute;mero: <?php echo $_GET['edit']; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="9">Motivo de la comprobaci&oacute;n<span class="style1">*</span>: 
    <?php 
    $cnn	= new conexion();
    $query	= sprintf("SELECT * FROM comprobaciones where co_mi_tramite=%s",$_GET['edit']);
    $rst	= $cnn->consultar($query);
    $fila	= mysql_fetch_assoc($rst);
    ?>
      <input name="motive" type="text" id="motive" size="40" maxlength="100"  value="<?php echo $fila["co_motivo"];?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="4">&nbsp;</td>
    <td width="85">&nbsp;</td>
    <td colspan="4">&nbsp;</td>
    <td width="7">&nbsp;</td>
    <td width="5">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td width="66">&nbsp;</td>
  </tr>
  <tr>
    <td width="4">&nbsp;</td>
    <td width="85"><div align="left">Concepto<span class="style1">*</span>: <br />
    </div></td>
    <td colspan="4"><div align="left">
      <select name="select_concepto" id="select_concepto" onchange="activaCb();" >
	   <?php 
	//Cat&aacute;logo de conceptos		
	$idusuario=$_SESSION["idusuario"];	
	function Get_clasificaciones2($usuario){
        $cnn    = new conexion();
        $query=sprintf("select cp_concepto,cp_retencion,dc_id, cp_iva as iva, cp_deducible as deducible from cat_conceptos DC  where cp_activo=true and dc_catalogo=1 and cp_empresa_id = " . $_SESSION["empresa"] . " order by cp_concepto");
        //error_log($query);
        $rst    = $cnn->consultar($query);
        while($fila=mysql_fetch_assoc($rst)){           
        echo "<option id=".$fila["cp_concepto"]." value=".$fila["iva"].">".$fila["cp_concepto"]."</option>";
        }
    }
	$cat=Get_clasificaciones2($idusuario);
	?>
      </select>
    </div></td>
    <td width="7">&nbsp;</td>
    <td width="5">&nbsp;</td>
    <td colspan="2"><div align="left">Fecha Comprobante<span class="style1">*</span>: </div></td>
    <td><div align="left">
      <input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="15" readonly="readonly" />
      <img src="../../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> </div></td>
    <td width="66">&nbsp;</td>
  </tr>
  <tr>
    <td width="4" height="26">&nbsp;</td>
    <td width="85"><div align="left">Monto<span class="style1">*</span>: $ </div></td>
    <td width="74"><div align="left">
      <input name="monto" id="monto" value="0" onkeyup="getTotal();" onkeypress="return validaNum(event)"  size="10"/>
    </div></td>
    <td width="54"><div align="left">IVA<span class="style1">*</span>: </div></td>
    <td colspan="2"><div align="left">
      <select name="select_impuesto" id="select_impuesto" onchange="valor(this.value);">
        <?php
			$porcentajes=array("0","16","11","No aplica");		
			for($i=0;$i<count($porcentajes);$i++){
			echo "<option id=".$i." value=".$porcentajes[$i].">".$porcentajes[$i]."</option>";
			}
			?>
      </select>
      %
      &nbsp;</div></td>
    <td width="7">&nbsp;</td>
    <td width="5">&nbsp;</td>
    <td width="118"><div align="left">Divisa<span class="style1">*</span>: </div></td>
    <td width="48"><div align="left">
      <select name="moneda" id="moneda" onchange="tipoCambio(this.value);" >
        <?php
			$tipoCambio=array("MXP","USD");		
			for($i=0;$i<count($tipoCambio);$i++){
			echo "<option value=".$i.">".$tipoCambio[$i]."</option>";
			}
			?>
      </select>
    </div></td>
    <td>&nbsp;</td>
    <td width="66">&nbsp;</td>
  </tr>
  <tr>
    <td width="4">&nbsp;</td>
    <td width="85"><div align="left">Total<span class="style1">*</span>: $ </div></td>
    <td width="74"><div align="left">
      <input name="total"  id="total" value="0" size="10" onkeypress="return validaNum(event)"/>
    </div></td>
    <td width="54"><div align="left">IVA<span class="style1">*</span>: $</div></td>
    <td colspan="2"><div align="left">
      <input name="impuesto" id="impuesto" value="0" size="8" onkeypress="return validaNum(event);" />
    </div></td>
    <td width="7">&nbsp;</td>
    <td width="5">&nbsp;</td>
    <td><div align="left">Tasa MXP: $ </div></td>
    <td><div align="left">
      <input name="tasa" id="tasa" value="1.00" size="8" />
    </div></td>
    <td>&nbsp;</td>
    <td width="66">&nbsp;</td>
  </tr>
  <tr>
    <td width="4">&nbsp;</td>
    <td width="85"><div align="left">Comentario: </div></td>
    <td colspan="9"><div align="left">
      <input name="referencia_corta" id="referencia_corta" size="84" />
    </div></td>
    <td width="66">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div align="left">Documento:</div></td>
    <td colspan="2"><input type="checkbox" name="fact_chk" id="fact_chk" checked="checked" onclick="verificaFactura();" />
      Cuenta con Factura </td>
    <td width="51"><div align="left" id="div_folio">Folio<span class="style1">*</span>:</div></td>
    <td width="64"><div align="left">
      <input name="d_folio" id="d_folio" size="8" onkeypress="return validaNum(event)" />
    </div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div align="left">No.Asistentes</div></td>
    <td colspan="2"><div align="left">
      <input name="no_asistentes" id="no_asistentes" value="1" size="10" onkeypress="return validaNum(event)" />
    </div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="4">&nbsp;</td>
    <td colspan="10">&nbsp;</td>
    <td width="66">&nbsp;</td>
  </tr>
</table>
<br />
<table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
  <tr>
    <td colspan="8"><h3 align="center">Datos de proveedor</h3></td>
  </tr>
  <tr>
    <td colspan="8"><div align="center">B&uacute;squeda:</div></td>
  </tr>
  <tr>
    <td width="38">&nbsp;</td>
    <td width="54"><div id="rfc_prov_busq_div" align="right">RFC<span class="style1">*</span>: </div></td>
    <td width="202"><div align="left">
      <input name="rfc" type="text" id="rfc" value="" size="30" maxlength="13"  onkeyup="this.value = this.value.toUpperCase();" />
    </div></td>
    <td width="21">&nbsp;</td>
    <td width="8">&nbsp;</td>
    <td width="101"><div id="name_prov_busq_div" align="left">Raz&oacute;n Social<span class="style1">*</span>: </div></td>
    <td width="260"><div align="left">
    <input name="proveedor" type="text" id="proveedor" value="" size="50" />
      
    </div></td>
    <td width="74">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="6"><div align="center" class="style1" id="load_div" ></div></td>
    <td>&nbsp;</td>
  </tr>
</table>
<br />
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
<br />
<div align="center">
	    <input name="registrar_comp" type="button" id="registrar_comp" value="    Registrar partida"  onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
  </div>

	
	
	<?php
	}

		?> <center><h3>Detalle de la Comprobaci&oacute;n</h3></center> <?php
		forma_comprobacion();
		?>
<table id="comprobacion_table" class="tablesorter" cellspacing="1"> 
<thead> 
<tr> 
    <th width="4%">No.</th> 
	<th width="5%">Fecha</th>
    <th width="8%">Concepto</th>
	<th width="9%">Comentario</th> 
    <th width="15%">No. Asistentes</th> 
	<th width="8%">Proveedor</th>
	<th width="12%">RFC Proveedor</th>
    <th width="12%">Documento</th>
    <th width="7%">Monto </th>
	<th width="6%">Divisa</th> 
    <th width="9%">Tasa MXP</th> 
    <th width="9%">%IVA</th>
	<th width="6%">$IVA</th>
	<th width="7%">Total</th>
	<th width="10%">Herramientas</th>
</tr> 
</thead> 
<tbody>
    <!-- cuerpo tabla-->
    <?php //Detalle de la comprobacion
		$cnn	= new conexion(); 
        $Comp = "SELECT * FROM comprobaciones WHERE co_mi_tramite =".$_GET['edit'];
        $res = $cnn->consultar($Comp);
        $DatosComprobacion = mysql_fetch_assoc($res);
        $TipoComprobacion = $DatosComprobacion["co_tipo"];
        $query	= sprintf("select dc_id, dc_fecha,(select DC.cp_clasificacion from cat_conceptos DC where dc_id=C.dc_concepto) as clasificacion,(select DC.cp_concepto from cat_conceptos DC where dc_id=C.dc_concepto) as concepto, dc_comensales as asistentes ,(select PR.pro_proveedor from proveedores PR where dc_rfc=PR.pro_rfc ) as proveedor,dc_rfc as rfc, dc_factura_impresa as factura,dc_folio_factura as folio, dc_monto as monto, dc_porcentaje_iva as porcentaje, dc_iva as iva, dc_total as total, dc_total_aprobado as total_aprobado,dc_descripcion, dc_tipocambio as tasa,dc_divisa from detalle_comprobacion C where dc_comprobacion in (select co_id from comprobaciones where co_mi_tramite=%s) and dc_estatus=1",$_GET['edit']);
        
		$cont=1;
		$rst	=$cnn->consultar($query);
		while($fila= mysql_fetch_assoc($rst)){
			$fila["monto"]				=number_format($fila["monto"],2,".",",");
			$fila["iva"]				=number_format($fila["iva"],2,".",",");
			$fila["total"]				=number_format($fila["total"],2,".",",");
			$fila["total_aprobado"]		=number_format($fila["total_aprobado"],2,".",",");
			
			
			echo "<tr><td>".$cont++."</td>";
			echo "<td>".$fila["dc_fecha"]."</td>";
			echo "<td>".$fila["concepto"]."</td>";
				if($fila["dc_descripcion"]!="" || !empty($fila["dc_descripcion"])){
			echo "<td>".$fila["dc_descripcion"]."</td>";
				}else{
			echo "<td>Sin Comentario</td>";
				}
			echo "<td>".$fila["asistentes"]."</td>";
			echo "<td>".$fila["proveedor"]."</td>";
			echo "<td>".$fila["rfc"]."</td>";
				if($fila["factura"]==1){
			echo "<td> Folio: ".$fila["folio"]."</td>";
			}else{
			echo "<td>Sin Factura</td>";
			}			
			echo "<td>$".$fila["monto"]."</td>";
			echo "<td>".$fila["dc_divisa"]."</td>";
			echo "<td>$".$fila["tasa"]."</td>";
			echo "<td>".$fila["porcentaje"]."% / $". $fila["iva"]."</td>";
			echo "<td>$".$fila["total"]."</td>";
			echo "<td>$".$fila["total_aprobado"]."</td>";
            ?>
        	<td>
               <div align='center'>
                  <img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='del' id='<? echo $fila["dc_id"]?>' onclick='borrarPartida(this.id, <?echo $TipoComprobacion?>);'/>
               </div>
            </td>
         </tr>
		<? } ?>
  </tbody>
</table> 
<?php 
$cnn	= new conexion(); 
$query	= sprintf("SELECT co_total,co_subtotal,co_iva,co_total_aprobado FROM comprobaciones where co_mi_tramite=%s",$_GET['edit']);

$totalPendiente = 0;

$rst	= $cnn->consultar($query);
$fila	= mysql_fetch_assoc($rst);
$fila["co_iva"]		=number_format($fila["co_iva"],2,".",",");
$total = $fila["co_subtotal"]=number_format($fila["co_subtotal"],2,".",",");
$totalAprobado = $fila["co_total_aprobado"] = number_format($fila["co_total_aprobado"],2,".",",");
$totalMonto = $fila["co_total"] = number_format($fila["co_total"],2,".",",");
$totaPendiente =  $totalMonto -  $totalAprobado;
?>
<div align="right">
  <div id="g_sbt">Total: <?php echo $fila["co_subtotal"];  ?></div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="<?php echo $fila["co_subtotal"];  ?>" /><br />
  <div id="g_iva">IVA: <?php echo $fila["co_iva"];  ?></div><input type="hidden" readonly="readonly" name="t_iva" id="t_iva" value="<?php echo $fila["co_iva"];  ?>" /><br />
  <div id="g_tot">Saldo a reembolsar: <?php echo $fila["co_total"];  ?></div><input type="hidden" readonly="readonly" name="t_total" id="t_total" value="<?php echo $fila["co_total"];  ?>" /><br />
  <div id="g_tot_comp"><b>Total de comprobaci&oacute;n: <?php echo $fila["co_total"];  ?></div><input type="hidden" readonly="readonly" name="t_total_com" id="t_total_com" value="<?php echo $fila["co_total"];  ?>" /></b>
  <div id="tot_apro"><strong><font color='#00CC00'>Monto Aprobado: </font></strong><?php echo $fila["co_total_aprobado"];?></div><input type="hidden" readonly="readonly" name="t_total_aprobado" id="t_total_aprobado" value="<?php echo $fila["co_total_aprobado"];  ?>" />
  <div id="monto_apro"><strong><font color='#FF0000'>Monto a Aprobar: </font></strong><?php echo $totaPendiente  ?></div><input type="hidden" readonly="readonly" name="t_total_pendiente" id="t_total_pendiente" value="<?php echo $totaPendiente  ?>" /><br />
</div>
<br />
<br />
<br><br>
<div align="center">Agregar observaciones:</div>
<div align="center"><textarea name="observaciones" cols="47" rows="5" wrap="physical" id="observaciones" ></textarea></div>
<br />
<br /><br />
<div align="center">
  <input type="submit" id="guardarComp" name="guardarComp" value="Guardar Comprobacion"  onclick="guardaComprobacion();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
</div>
<input type="hidden" name="typedoc" id="typedoc" readonly="readonly"  value="" />
<input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
<input type="hidden" name="ne" id="ne" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
<input type="hidden" name="com" id="com" readonly="readonly" value="<?php echo $_GET['edit'];?>"  />
<input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
<input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
</form>
</p>

<?php	}			
		

?>
