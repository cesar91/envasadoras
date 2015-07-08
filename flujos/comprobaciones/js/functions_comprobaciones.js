var valorComidasRepresentacion = 7;
var cCObligatorio = 0;
var validaFactura=1;

var bandera = false;

var estatus_en_edicion_de_comprobacion = false;

var arreglovalores=new Array();
var arregloestatus=new Array();
var arreglodescripcion=new Array();

function gup(name){
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp ( regexS );
	var tmpURL = window.location.href;
	var results = regex.exec( tmpURL );
	
	if( results == null )
		return"";
	else
		return results[1];
}

function buscaProveedor(li){
    if(li==null){ 
        return null;
    }
    if(!!li.extra){
        var valorLista=li.extra[0];
    }else{ 
        var valorLista=li.selectValue;
        $("#load_div").html("Cargando Razï¿½n Social espere...");
        $.ajax({
            //busca el nombre del proveedor en el catalogo en base al RFC
            url: 'services/catalogo_proveedores.php',
            type: "POST",
            data: "nombre="+valorLista+"&tip=1",
            dataType: "html",
            timeout: 10000,
            success: function(datos){
                $("#proveedor").val(datos);
                $("#load_div").html("");
            },
            error: function(x, t, m) {
				if(t==="timeout") {
					buscaProveedor(li);
				}
			}
        });//fin ajax	
    }
} // Fin buscaProveedor

function seleccionaItem(li) {
    buscaProveedor(li);
} // Fin seleccionaItem

function arreglaItem(row) {
    //da el formato a la lista
    return row[0];
} // Fin arreglaItem

function actualiza_status_idamex(idamex, estado){				
	$.ajax({
		url:  "services/Ajax_comprobacion.php",
		type: "POST",
		data: "idamex="+idamex+"&status="+estado,
		dataType: "json",
		async: false,
		timeout: 10000,
		success: function(json){},
		complete: function(json){
			verificar_tipo_tarjeta();
		},
		error: function(x, t, m){
			if(t==="timeout"){
				actualiza_status_idamex(idamex, estado);
			}
		}
	});//fin ajax
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

//Generar partidas de conceptos comprobados
function construyePartida(){
	$("#tipo").removeAttr("disabled");
	$("#select_tipo_tarjeta").removeAttr("disabled");
	$("#select_tarjeta_cargo").removeAttr("disabled");
	
	var frm = document.detallecomp;
    var moneda = frm.moneda.value;
	var totalComp = frm.total.value;
    var divisa = moneda;
    var id=0;
    var prov_name="";
    var prov_rfc="";
    var propina="";
    var propina_val=0;
    var impuesto_hospedaje="";
    var asistentes="";
    var tipo_comprobacion="";
    var cargo_asociado=0;
    var total=0;
    var fact_doc="";
    var ref_cort="";
    var indice=$("#tipo").val();
    var factura ="";
	var tiponum="";
	var excepcion = "";

	//Si se esta editando la partida
	//Si esta en forma de edicion, este tomara el valor del tramite en cuestion.
	if($("#cargar_com_viaje").val() == '1'){
		tramiteId = $('#sol_select').val();
	}	
	
    if(frm.typedoc.value!=""){
        var idsol=frm.typedoc.value.split("|");				
    } else {
        var idsol = new Array(0,0);
    }                                                                                                                                                           
    var concepto = $("#select_concepto").val();
	
    //Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
    id=parseInt($("#comprobacion_table").find("tr:last").find("div").eq(0).html());

    if(isNaN(id)){			
        id=1;		
    }else{			
        id+=parseInt(1);
    }
	
    if (validaFactura==1){
        fact_doc=frm.d_folio.value;
    }else{
        fact_doc="Sin Factura";
    }
    
	switch(frm.tipo.value){
        case "1": tiponum="1";tipo_comprobacion="Anticipo";break;
        case "2": tiponum="2";tipo_comprobacion="Amex";break;
        case "3": tiponum="3";tipo_comprobacion="Reembolso";break;
		case "4": tiponum="4";tipo_comprobacion="Amex externo";break;
    }
         
	/*===== Nuevo ===*/
	if(tipo_comprobacion=="Amex"){
        cargo_asociado=frm.select_tarjeta_cargo.value;
    }else{
        cargo_asociado="0";
    }
	if($("#tipo option[value='"+ indice +"']").text() != "Amex"){
		$("#no_transaccion").html("N/A");
	}
	var valorcomida=$("#tipocomidadato").val();
	if (frm.referencia_corta.value!=""){
        ref_cort=frm.referencia_corta.value;
    }else{
        ref_cort="Sin Comentarios";
    }
	if($("#select_concepto").val() != 6){
        asistentes = "N/A";
		var comida = "0";					
    }else{
        asistentes =$("#no_asistentes").val();
		var comida = $("#tipocomidadato").val();					
    }				
	if ($("#proveedor").val()!=""){
        prov_name=$("#proveedor").val();
    }else{
        prov_name="N/A";
    }
	if ($("#rfc").val()!=""){
        prov_rfc = $("#rfc").val();
    }else{
        prov_rfc="N/A";
    }
	if($("#d_folio").val()!=""){
		factura = $("#d_folio").val();
	}else{
		factura = "N/A";
	}
	
	if(($("#concepto_alim_hot").val()=="alimentacion") || ($("#concepto_alim_hot").val()=="hospedaje") || ($("#select_concepto").val() == valorComidasRepresentacion)){//informacion a persistir en la base de datos referente al concepto elegido
        if($("#propina_dato").val()!=""){
            propina=$("#propina_dato").val();
           	propina_val=parseFloat(propina);
        }
    }else{
        propina="0";
        propina_val=0;
    }			
	
	if(($("#concepto_alim_hot").val()=="hospedaje")){//informacion a persistir en la base de datos referente al concepto elegido
        if($("#impuesto_hotel").val()!="" || $("#impuesto_hotel").val()!=0){
            iva_hospedaje=$("#impuesto_hotel").val();
        }
    }else{
        iva_hospedaje="0";
        $("#impuesto_hotel").val("0");
    }
	if(divisa == "MXN"){
		total=(Math.round(parseFloat($("#total").val().replace(/,/g,"")) * Math.pow(10, 2)) / Math.pow(10, 2));
		var totalmxn = total;
		var tiponumDivisa ="1";					
	}else if(divisa == "USD"){
		total=(Math.round(parseFloat($("#total").val().replace(/,/g,"")) * Math.pow(10, 2)) / Math.pow(10, 2));
		var totalmxn = total * parseFloat($("#valorDivisaUSD").val());
		var tiponumDivisa ="2";					
	}else if(divisa == "EUR"){
		total=(Math.round(parseFloat($("#total").val().replace(/,/g,"")) * Math.pow(10, 2)) / Math.pow(10, 2));
		var totalmxn = total * parseFloat($("#valorDivisaEUR").val());
		var tiponumDivisa ="3";					
	}
					
	if($("#idAmex").val()== "" || $("#idAmex").val()== null || $("#tipo").val() != 2 ){
		var valorIdmex = 0;
	}else{
		var valorIdmex = $("#idAmex").val();
	}				
	
	if($("#total_dolares").val() == "" || $("#total_dolares").val() == null){
		var total_dolares = 0;
	}else{
		var total_dolares = $("#total_dolares").val().replace(/,/g,"");
	}				
	if($("input[name=fact_chk]:checked").val()=="on"){
		valor_check="on";
	}else{
		valor_check="off";
	}			
	var tipoTarjeta = $("#select_tipo_tarjeta").val();
	var tipoCargo = $("#select_tarjeta_cargo").val();
    frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
	
	if(concepto == 6){
		var tipoAlimentacion = frm.tipocomidadato.options[frm.tipocomidadato.selectedIndex].text + "- ";
	}else{ 
		var tipoAlimentacion = "";	
	}
	
	//Se tomara el valor de la fecha que acaba de ingresar
	fechaSeleccionada = frm.fecha.value;
	
	// Excepción del concepto seleccionado para la partida
	validarPoliticaComprobaciones();
	excepcion = $("#textoExcepcion").val();
	
	//Se checa si se AGREGA o se ACTUALIZA un gasto.
	if(estatus_en_edicion_de_comprobacion){
		if($("#etapaComprobacion").val() == 5){
			// Dehabilitamos Campos de Montos en caso que la Comprobaciï¿½n este Devuelta con Observaciones 
			$("#monto").attr("disabled", "disabled");
			$("#moneda").attr("disabled", "disabled");
			$("#referencia_corta").attr("disabled", "disabled");
		}
		
		$("#registrar_comp").attr("value","     Registrar Gasto");
		estatus_en_edicion_de_comprobacion = false;
		partidaEdicion = 0;
		id = frm.itinerarioActualOPosible.value;
		$(".elimina").css("display","block");//devuelve el boton eliminar
		$(".del_part").html("Eliminar Partida");
		
		var nuevaFila='';
		nuevaFila+="<td>"+"<div id='renglonS"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='cargo_asociado"+id+"'id='cargo_asociado"+id+"' value='"+tipo_comprobacion+"'readonly='readonly' />"+tipo_comprobacion+"<input type='hidden' name='cargo_factura"+id+"'id='cargo_factura"+id+"' value='"+$("#monto_cargo").html().replace(/,/g,"")+"'readonly='readonly' /><input type='hidden' name='tipoComprobacion"+id+"'id='tipoComprobacion"+id+"' value='"+tiponum+"'readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='no_transaccion"+id+"'id='no_transaccion"+id+"' value='"+$("#no_transaccion").html()+"'readonly='readonly' />"+$("#no_transaccion").html()+"</td>";
		nuevaFila+="<td><input type='hidden' name='fecha"+id+"' id='fecha"+id+"' value='"+frm.fecha.value+"' readonly='readonly' />"+frm.fecha.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='tipo_comida"+id+"' id='tipo_comida"+id+"' value='"+valorcomida+"' 	readonly='readonly' /><input type='hidden' name='concepto"+id+"' id='concepto"+id+"' value='"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"' 	readonly='readonly' />"+tipoAlimentacion+""+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"</td>";
		nuevaFila+="<td><input type='hidden' name='comentario"+id+"' id='comentario"+id+"' value='"+ref_cort+"' readonly='readonly' />"+ref_cort+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='no_asistentes"+id+"' id='no_asistentes"+id+"' value='"+asistentes+"' readonly='readonly' />"+asistentes+"</td>";
		nuevaFila+="<td><input type='hidden' name='rfc"+id+"' id='rfc"+id+"' value='"+prov_rfc+"' readonly='readonly' />"+prov_rfc+"</td>";
		nuevaFila+="<td><input type='hidden' name='proveedor"+id+"' id='proveedor"+id+"' value='"+prov_name+"' readonly='readonly' />"+prov_name+"</td>";
		nuevaFila+="<td><input type='hidden' name='flag_factura"+id+"' id='flag_factura"+id+"' value='"+factura+"' readonly='readonly' />"+factura+"</td>";
		nuevaFila+="<td id='m'><input type='hidden' name='mnt"+id+"' id='mnt"+id+"' value='"+$("#monto").val()+"' readonly='readonly' />"+number_format($("#monto").val(),2,".",",")+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='pimpuesto"+id+"' id='pimpuesto"+id+"' value='"+frm.impuesto.value+"' readonly='readonly' />"+number_format($("#impuesto").val(),2,".",",")+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='propina"+id+"' id='propina"+id+"' value='"+propina+"' readonly='readonly' />"+number_format(propina,2,".",",")+"</td>"; 					
		nuevaFila+="<td><input type='hidden' name='impuesto_hospedaje"+id+"' id='impuesto_hospedaje"+id+"' value='"+iva_hospedaje+"' readonly='readonly' />"+number_format(iva_hospedaje,2,".",",")+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='tot"+id+"' id='tot"+id+"' value='"+total+"' readonly='readonly' />"+number_format(total,2,".",",")+"<input type='hidden' name='mensaje"+id+"' id='mensaje"+id+"' value='0' readonly='readonly' /><input type='hidden' name='diferencia"+id+"' id='diferencia"+id+"' value='0' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='divisa"+id+"' id='divisa"+id+"' value='"+divisa+"' readonly='readonly' /><input type='hidden' name='tipoDivisa"+id+"' id='tipoDivisa"+id+"' value='"+tiponumDivisa+"' readonly='readonly' />"+divisa+"</td>";  
		nuevaFila+="<td><input type='hidden' name='totalMxn"+id+"' id='totalMxn"+id+"' value='"+totalmxn+"' readonly='readonly' />"+number_format(totalmxn,2,".",",")+"</td>";  
		nuevaFila+="<td><div align='center'><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Comprobacion' name='"+id+"edit' id='"+id+"edit' onclick='editarComprobacion(this.id);' style='cursor:pointer'/></div><div align='center'>Editar Partida</div></td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarRenglonComprobacion(this.id,\"comprobacion_table\",\"rowCount\",\"rowDel\",\"renglonS\",\"edit\",\"del\",\"itinerarioActualOPosible\");' onclick='conceptoElimina(this.id); reiniciaComentarioObligatorio();' style='cursor:pointer;' /></div><div class='del_part' id='del_part"+id+"' align='center'>Eliminar Partida</div></td>";
		nuevaFila+= "<input type='hidden' id='idAmex"+id+"' name='idAmex"+id+"' value='"+valorIdmex+"' readonly='readonly'/><input type='hidden' id='totalDolares"+id+"' name='totalDolares"+id+"' value='"+total_dolares+"' readonly='readonly'/><input type='hidden' id='tipoComida"+id+"' name='tipoComida"+id+"' value='"+comida+"' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='numConcepto"+id+"' name='numConcepto"+id+"' value='"+concepto+"' readonly='readonly'/><input type='hidden' id='check"+id+"' name='check"+id+"' value='"+valor_check+"' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='tipoTarjeta"+id+"' name='tipoTarjeta"+id+"' value='"+tipoTarjeta+"' readonly='readonly'/><input type='hidden' id='listaCargo"+id+"' name='listaCargo"+id+"' value='"+tipoCargo+"' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='exConcepto"+id+"' name='exConcepto"+id+"' value='" + excepcion + "' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='difConcepto"+id+"' name='difConcepto"+id+"' value='0' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='idDetalleComp"+id+"' name='idDetalleComp"+id+"' value='0' readonly='readonly'/>";
		nuevaFila+= '';
		
		$("#comprobacion_table>tbody").find("tr").eq(id-1).html(nuevaFila);

		// Apagamos la bandera de Ediciï¿½n 
		$("#edicionAux").val(0);
		//alert("Etapa_actual: "+$("#etapaComprobacion").val());

		var idamex = $("#select_tarjeta_cargo").val();
		var total_idamex = 0;
		var no_partidas = parseInt($("#comprobacion_table>tbody>tr").length);
		var amex_pesos = $("#amex_pesos_val").val().replace(/,/g,"");
		
		for(var i=1; i<=no_partidas; i++){
			if(parseInt($("#idAmex"+i).val()) == parseInt(idamex)){
				total_idamex+= parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			}
		}
		amex_pesos = parseFloat(amex_pesos).toFixed(2);
		total_idamex = parseFloat(total_idamex).toFixed(2);
		//alert("Total cargo AMEX: "+amex_pesos);
		//alert("Total gastos AMEX: "+total_idamex);
		
		if(($("#etapaComprobacion").val() == 0) && (amex_pesos == total_idamex)){ // Nuevo registro de Comprobaciï¿½n 
			//Actualizar estatus idamex a 3 
			actualiza_status_idamex(idamex, 3);
		}else if(($("#etapaComprobacion").val() != 0) && (amex_pesos == total_idamex)){ // Ediciï¿½n de una Comprobaciï¿½n 
			//Actualizar estatus idamex a 1 
			actualiza_status_idamex(idamex, 1);
		}else{
			//Actualizar estatus idamex a 0 
			actualiza_status_idamex(idamex, 0);
		}
		
	}else{					
		//agrega 1 al contador de la partida 
		$("#rowCount").val(parseInt($("#rowCount").val())+parseInt(1)); 
		$("#rowCountCecos").val(parseInt($("#rowCountCecos").val())+parseInt(1)); 
		
		var nuevaFila='<tr>';
		nuevaFila+="<td>"+"<div id='renglonS"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='cargo_asociado"+id+"'id='cargo_asociado"+id+"' value='"+tipo_comprobacion+"'readonly='readonly' />"+tipo_comprobacion+"<input type='hidden' name='cargo_factura"+id+"'id='cargo_factura"+id+"' value='"+$("#monto_cargo").html().replace(/,/g,"")+"'readonly='readonly' /><input type='hidden' name='tipoComprobacion"+id+"'id='tipoComprobacion"+id+"' value='"+tiponum+"'readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='no_transaccion"+id+"'id='no_transaccion"+id+"' value='"+$("#no_transaccion").html()+"'readonly='readonly' />"+$("#no_transaccion").html()+"</td>";
		nuevaFila+="<td><input type='hidden' name='fecha"+id+"' id='fecha"+id+"' value='"+frm.fecha.value+"' readonly='readonly' />"+frm.fecha.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='tipo_comida"+id+"' id='tipo_comida"+id+"' value='"+valorcomida+"' 	readonly='readonly' /><input type='hidden' name='concepto"+id+"' id='concepto"+id+"' value='"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"' 	readonly='readonly' />"+tipoAlimentacion+""+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"</td>";
		nuevaFila+="<td><input type='hidden' name='comentario"+id+"' id='comentario"+id+"' value='"+ref_cort+"' readonly='readonly' />"+ref_cort+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='no_asistentes"+id+"' id='no_asistentes"+id+"' value='"+asistentes+"' readonly='readonly' />"+asistentes+"</td>";
		nuevaFila+="<td><input type='hidden' name='rfc"+id+"' id='rfc"+id+"' value='"+prov_rfc+"' readonly='readonly' />"+prov_rfc+"</td>";
		nuevaFila+="<td><input type='hidden' name='proveedor"+id+"' id='proveedor"+id+"' value='"+prov_name+"' readonly='readonly' />"+prov_name+"</td>";
		nuevaFila+="<td><input type='hidden' name='flag_factura"+id+"' id='flag_factura"+id+"' value='"+factura+"' readonly='readonly' />"+factura+"</td>";
		nuevaFila+="<td id='m'><input type='hidden' name='mnt"+id+"' id='mnt"+id+"' value='"+$("#monto").val()+"' readonly='readonly' />"+number_format($("#monto").val(),2,".",",")+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='pimpuesto"+id+"' id='pimpuesto"+id+"' value='"+frm.impuesto.value+"' readonly='readonly' />"+number_format($("#impuesto").val(),2,".",",")+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='propina"+id+"' id='propina"+id+"' value='"+propina+"' readonly='readonly' />"+number_format(propina,2,".",",")+"</td>"; 					
		nuevaFila+="<td><input type='hidden' name='impuesto_hospedaje"+id+"' id='impuesto_hospedaje"+id+"' value='"+iva_hospedaje+"' readonly='readonly' />"+number_format(iva_hospedaje,2,".",",")+"</td>"; 
		nuevaFila+="<td><input type='hidden' name='tot"+id+"' id='tot"+id+"' value='"+total+"' readonly='readonly' />"+number_format(total,2,".",",")+"<input type='hidden' name='mensaje"+id+"' id='mensaje"+id+"' value='0' readonly='readonly' /><input type='hidden' name='diferencia"+id+"' id='diferencia"+id+"' value='0' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='divisa"+id+"' id='divisa"+id+"' value='"+divisa+"' readonly='readonly' /><input type='hidden' name='tipoDivisa"+id+"' id='tipoDivisa"+id+"' value='"+tiponumDivisa+"' readonly='readonly' />"+divisa+"</td>";  
		nuevaFila+="<td><input type='hidden' name='totalMxn"+id+"' id='totalMxn"+id+"' value='"+totalmxn+"' readonly='readonly' />"+number_format(totalmxn,2,".",",")+"</td>";  
		nuevaFila+="<td><div align='center'><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Comprobacion' name='"+id+"edit' id='"+id+"edit' onclick='editarComprobacion(this.id);' style='cursor:pointer'/></div><div align='center'>Editar Partida</div></td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarRenglonComprobacion(this.id,\"comprobacion_table\",\"rowCount\",\"rowDel\",\"renglonS\",\"edit\",\"del\",\"itinerarioActualOPosible\");' onclick='conceptoElimina(this.id); reiniciaComentarioObligatorio();' style='cursor:pointer;' /></div><div id='del_part"+id+"' align='center'>Eliminar Partida</div></td>";
		nuevaFila+= "<input type='hidden' id='idAmex"+id+"' name='idAmex"+id+"' value='"+valorIdmex+"' readonly='readonly'/><input type='hidden' id='totalDolares"+id+"' name='totalDolares"+id+"' value='"+total_dolares+"' readonly='readonly'/><input type='hidden' id='tipoComida"+id+"' name='tipoComida"+id+"' value='"+comida+"' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='numConcepto"+id+"' name='numConcepto"+id+"' value='"+concepto+"' readonly='readonly'/><input type='hidden' id='check"+id+"' name='check"+id+"' value='"+valor_check+"' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='tipoTarjeta"+id+"' name='tipoTarjeta"+id+"' value='"+tipoTarjeta+"' readonly='readonly'/><input type='hidden' id='listaCargo"+id+"' name='listaCargo"+id+"' value='"+tipoCargo+"' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='exConcepto"+id+"' name='exConcepto"+id+"' value='" + excepcion + "' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='difConcepto"+id+"' name='difConcepto"+id+"' value='0' readonly='readonly'/>";
		nuevaFila+= "<input type='hidden' id='idDetalleComp"+id+"' name='idDetalleComp"+id+"' value='0' readonly='readonly'/>";
		
		nuevaFila+= '</tr>';							
		indiceGlobal = -1;
		
		 //restablece los campos 
		$("#prm_valido").val("");
		$("#comprobacion_table").append(nuevaFila);

		//Actualizar estatus idamex a 3 
		var idamex = $("#select_tarjeta_cargo").val();
		var total_idamex = 0;
		var no_partidas = parseInt($("#comprobacion_table>tbody>tr").length);
		var amex_pesos = $("#amex_pesos_val").val().replace(/,/g,"");
	
		for(var i=1; i<=no_partidas; i++){
			if(parseInt($("#idAmex"+i).val()) == parseInt(idamex)){
				total_idamex+= parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			}
		}
		
		amex_pesos = parseFloat(amex_pesos).toFixed(2);
		total_idamex = parseFloat(total_idamex).toFixed(2);
		
		if(amex_pesos == total_idamex){
			actualiza_status_idamex(idamex, 3);
		}else{
			actualiza_status_idamex(idamex, 0);
		}
	}
	
	if($("#select_concepto option:selected").val() == valorComidasRepresentacion){
		// Eliminar el concepto de Comidas Representación, para evitar que el usuario intente ingresar dos veces el concepto
		$("#select_concepto option[value="+valorComidasRepresentacion+"]").remove();
		$("#seccion_inivitados").css("display","none");
	}
	
	frm.itinerarioActualOPosible.value = parseInt($("#comprobacion_table>tbody >tr").length)+parseInt(1);
	
    frm.no_Comprobaciones_parciales.value=parseInt(frm.no_Comprobaciones_parciales.value)+1;
    $("#tipo").val(-1);
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
    $('#tipocomidadato').find('option:first').attr('selected', 'selected').parent('select');			
    $('#select_tarjeta_cargo').find('option:first').attr('selected', 'selected').parent('select');			
    $("#no_corporacion_val").val("");
    $("#moneda_local_val").val("");
    $("#moneda_fact").html("");             
    $("#moneda_local").html("");
	$("#total_dolares").val(0);
	$("#fecha").val(obtiene_fecha());
	$("#tipo_comida_label").css("display","none");
	$("#div_tipo_comida_label").css("display","none");
	
	// Campos de excepciones
	$("#excedePolitica").val(0);
    $("#textoExcepcion").val("");
    $("#comentarioReq").html("Comentario:");
    
	if(tipo_comprobacion=="Amex"){
		limpiar_cuadro_amex();
		if($("#amex_form").hasClass("visible")){
            $("#amex_form").slideUp(1000);
            $("#amex_form").removeClass("visible");
		}
		$("#fact_chk:checkbox").removeAttr("checked");
		$("#div_iva").css("display", "none"); 
		$("#impuesto").css("display", "none");
		verificaFactura();
	}else{
		limpiar_cuadro_amex();
		$("#moneda_fact_val").val("");
		$("#fecha_cargo_val").val("");
		$("#establecimiento_cargo_val").val("");
		$("#monto_cargo_val").val("");
		$("#amex_pesos_val").val("");
		$("#amex_dolar_val").val("");
		$("#rfc_cargo_val").val("");
		$("#moneda_local_val").val("");
		$("#select_tipo_tarjeta").val("-1");
		$("#tipo_cambio").val("");
		$("#propina_dato").val("0");
		$("#impuesto_hotel").val("0");
		$("#d_folio").val("");
		$("#rfc").val("");
		$("#proveedor").val("");
		$("#fact_chk:checkbox").removeAttr("checked");
		$("#div_iva").css("display", "none"); 
		$("#impuesto").css("display", "none"); 
		verificaFactura();
	}                
	borraPropina();
	
	$("#sol_select").attr("disabled","disbaled");
	
	if(!bandera){
		$("#registrar_comp").removeAttr("disabled");
		$("#guardarPrev").removeAttr("disabled");
		
		if(buscaConceptoComidasRepresentacion() && ($("#comidasRepresentacion").val() == 1)){
			$("#guardarComp").removeAttr("disabled");
			$("#mensajeInformativo").slideUp(1000);
	        $("#mensajeInformativo").css("display", "none");
		}else if ($("#comidasRepresentacion").val() == 0){
			$("#guardarComp").removeAttr("disabled");
		}
	}else{
		$("#registrar_comp").attr("disabled","true");
		$("#guardarPrev").attr("disabled","true");
		$("#guardarComp").attr("disabled","true");
	}
	cCObligatorio = 0;
	
} // Construye partida

// Validar campos AMEX
function validarcamposAMEX(){
	if($("#select_tipo_tarjeta").val() == -1){
		alert("Debe seleccionar el tipo de tarjeta para comprobar. Favor de llenar los datos faltantes.");
		$("#select_tipo_tarjeta").focus();
		return false;
	}else if($("#select_tarjeta_cargo").val() == -1){
		alert("Debe seleccionar un cargo para comprobar. Favor de llenar los datos faltantes.");
		$("#select_tarjeta_cargo").focus();
		return false;
	}else{
		return true;
	}
}

function validaProveedor(rfc,nombre){				
	var url = "services/Ajax_comprobacion.php";
	var valor = true;
	$.ajaxSetup({async:false});				
	$.post(url,{proveedorRFC:rfc,proveedorNombre:nombre},function(data){				
		valor = (data == 0) ? false : true;
	});
	if(!valor) alert("El proveedor ingresado no se encuentra registrado, favor de dar de alta al proveedor");
	return valor;			
}

// Validar campos de Proveedor 
function validarcamposProveedor(){
	if($("#impuesto").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#impuesto").focus();
		return false;
	}else if($("#d_folio").val() == ""){
		alert("Debe ingresar un folio de factura. Favor de llenar los datos faltantes.");
		$("#d_folio").focus();
		return false;
	}else if($("#rfc_prov_busq_div").val() == ""){
		alert("Debe ingresar el RFC del Proveedor. Favor de llenar los datos faltantes.");
		$("#rfc_prov_busq_div").focus();
		return false;
	}else if(($("#rfc").val().length<12)||($("#rfc").val().length>13)){
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
		$("#rfc").focus();
		return false;
	}else if(!validaProveedor($("#rfc").val(), $("#proveedor").val())){
		return false;
	}else{
		return true;
	}
}

// Validar campos obligatorios
function validarcamposGenerales(){
	if($("#select_concepto").val() == -1){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#select_concepto").focus();
		return false;
	}else if($("#fecha").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#fecha").focus();
		return false;
	}else if($("#monto").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#monto").focus();
		return false;
	}else if($("#moneda").val() == -1){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#moneda").focus();
		return false;
	}else if($("#moneda").val() == 1 && $("#impuesto").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#impuesto").focus();
		return false;
	}if($("#select_concepto").val() == valorComidasRepresentacion){
		if($("#lugar").val() == ""){
			alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
			$("#lugar").focus();
			return false;
		}else if($("#ciudad").val() == ""){
			alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
			$("#ciudad").focus();
			return false;
		}else if(($("#excedePolitica").val() == 1) && ($("#referencia_corta").val().length == 0)){
			alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
			$("#referencia_corta").focus();
			return false;
		}else{
			return true;
		}
	}else if(($("#excedePolitica").val() == 1) && ($("#referencia_corta").val().length == 0)){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#referencia_corta").focus();
		return false;
	}else{
		return true;
	}
}

function validarcampos(){
	if($("#tipo").val() == -1){
		alert("Debe seleccionar un tipo de comprobación.\nFavor de llenar los datos faltantes.");
		$("#tipo").focus();
		return false;
	}else if($("#ccentro_costos").val() == -1){
		alert("Debe seleccionar un centro de costos.\nFavor de llenar los datos faltantes.");
		$("#ccentro_costos").focus();
		return false;
	}else if($("#tipo").val() == 2){
		if(validarcamposAMEX()){
			if($("#checkbox").is(':checked')){
				if(validarcamposProveedor()){
					if(validarcamposGenerales()){
						construyePartida();
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
				if(validarcamposGenerales()){
					construyePartida();
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}else if($("#checkbox").is(':checked')){
		if(validarcamposProveedor()){
			if(validarcamposGenerales()){
				construyePartida();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		if(validarcamposGenerales()){
			construyePartida();
		}else{
			return false;
		}
	}
}

// Elimina el concepto personal cuando el gasto es por reembolso
function modifica_conceptos(){
	if( $("#tipo").val()==3){
		$("#select_concepto option[value='31']").remove();					
	}else{				
		$("#select_concepto option[value='31']").remove();				
		$("#select_concepto").append('<option value="31">Personal</option>');							
	}							
}

function LimpiarCombo(combo){
	while(combo.length > 0){
		combo.remove(combo.length-1);
	}
}

// Inicializar los campos de la Sección AMEX
function limpiar_cuadro_amex(limpiaTarjeta){
	var frm= document.detallecomp;
	arreglovalores.splice(0,arreglovalores.length);
	arregloestatus.splice(0,arregloestatus.length);
	arreglodescripcion.splice(0,arreglodescripcion.length);
	LimpiarCombo(frm.select_tarjeta_cargo);
	
	$("#fecha_cargo").html("");
	$("#establecimiento_cargo").html("");
	$("#monto_cargo").html(""); 
	$("#rfc_cargo").html("");
	$("#moneda_local").html("");
	$("#amex_pesos").html("");
	$("#amex_dolar").html("");
	$("#moneda_fact_val").val("");
	$("#fecha_cargo_val").val("");
	$("#establecimiento_cargo_val").val("");
	$("#monto_cargo_val").val("");
	$("#amex_pesos_val").val("");
	$("#amex_dolar_val").val("");
	$("#rfc_cargo_val").val("");
	$("#moneda_local_val").val("");
	$("#no_tarjeta_credito").html("");
	if(limpiaTarjeta != ""){
		$("#select_tipo_tarjeta").val("-1");
	}
	$("#no_transaccion").html("");
	$("#tipo_cambio").val("");
	$("#div_tipo_cambio").html("");
	$("#d_folio").val("");
	$("#rfc").val("");
	$("#proveedor").val("");
}

function tipo_de_comprobacion(valor){
	var frm=document.detallecomp;
	
	if(frm.tipo.value == 2){
		$("#amex_form").slideDown(1000);            
        $("#amex_form").css("display", "block");
        $("#amex_form").addClass("visible");				
	}else{					
		if($("#amex_form").hasClass("visible")){
            $("#amex_form").slideUp(1000);
            $("#amex_form").removeClass("visible");
        }
	}
	
	if(valor == 3){
		$("#g_reembolso").html();
		$("#t_reembolso").val();
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_comprobado").html();
		$("#t_comprobado").val();
	}else if(valor == "amex"){
		$("#g_amex_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_amex_comprobado").val($("#monto_pesos").val());
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_comprobado").val($("#monto_pesos").val());
	}else if(valor == "-1"){
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html("0.00 MXN");
		$("#t_comprobado").val(0);
	}
	
	// Reinicio de valores Cuadro AMEX y Proveedor 
	limpiar_cuadro_amex();
	$("#fact_chk").attr("checked",false);
	$("#factura_form").slideUp(1000);
	$("#rfc").val('');
	$("#d_folio").val('');
	$("#proveedor").val('');
	$("#fact_chk").removeAttr("disabled");
}

function checafecha(strFecha, diasmax){
    var toks=strFecha.split("/");
    var strFechaN="";                                                                                                                                        		
    strFechaN=toks[2]+"/"+toks[1]+"/"+toks[0];		
    var fecha = new Date();
    fecha.setTime(Date.parse(strFechaN));			
    var today=new Date()
    var agedays = Math.floor((today - fecha) / 24 / 60 / 60 / 1000);
    if(agedays>diasmax){
    	var f = new Date();
    	$("#fecha").val(f.getDate()+"/"+f.getMonth()+"/"+f.getFullYear());
        return false;
    }else{
        return true;
    }

}

function guardaComprobacion(){                           
    var frm=document.detallecomp;                
    if(parseInt(frm.no_Comprobaciones_parciales.value)>=1){
        $("#guardarComp").removeAttr("disabled");
    }else{
        $("#guardarComp").attr("disabled", "disabled");
    }
}

function calculartotal(){
	var frm=document.detallecomp;
	var dato=frm.rowCount.value;
	var anticipo=parseFloat($("#t_saldo").val());
	var i=0;
	var total=0;
	var totalamex=0;
	var totalreembolso=0;
	var descuento=0;
	var monto_descuento=0;
	var monto_reembolso=0;
	var totalcomprobacion=0;
	
	if(anticipo=="NaN"){
		anticipo=0;
		}

	 for (i=1;i<=parseInt(frm.rowCount.value);i++){
		 var jquerymonto="#tot"+parseInt(i);
		 var jquerytipo="#tipo"+parseInt(i);

		 if($(jquerytipo).val()==2){			 
			 totalamex=parseFloat(totalamex)+parseFloat($(jquerymonto).val());
		 }else if($(jquerytipo).val()==3){
				totalreembolso=parseFloat(anticipo)-(parseFloat(totalreembolso)+parseFloat($(jquerymonto).val()));
		}
			
		 total=parseFloat(total)+parseFloat($(jquerymonto).val());
		if(parseFloat(anticipo)>parseFloat(total)){
			monto_descuento=parseFloat(anticipo)-parseFloat(total);
		}else{
			monto_descuento=parseFloat(anticipo)-parseFloat(total);
		}

		if(parseFloat(totalreembolso)!=0){
			monto_reembolso=parseFloat(monto_reembolso)+parseFloat(totalreembolso);
		}else if(total>anticipo){
			monto_reembolso=(parseFloat(total)-parseFloat(anticipo))+parseFloat(monto_reembolso);
		}
		
	}
		
	 $("#g_sbt").html(parseFloat(total));
	 $("#t_subtotal").val(parseFloat(total));
	 $("#g_amex_comprobado").html(parseFloat(totalamex));
	 $("#t_amex_comprobado").val(parseFloat(totalamex));
	 $("#g_comprobado").html(parseFloat(total));
	 $("#t_comprobado").val(parseFloat(total));
	 $("#g_nocomprobado").html(parseFloat(monto_descuento));
	 $("#t_nocomprobado").val(parseFloat(monto_descuento));
	 $("#g_reembolso").html(parseFloat(monto_reembolso));
	 $("#t_reembolso").val(parseFloat(monto_reembolso));
 
}

function borrarPartida(id){ 
	var firstRow=parseInt($("#comprobacion_table").find("tr:first").find("td").eq(0).html());
    var lastRow=parseInt($("#comprobacion_table").find("tr:last").find("td").eq(0).html());                
    var frm=document.detallecomp;
    var row=frm.rowCount.value;
        
    $("img.elimina").click(function(){
		var tipo=$("#tipo"+parseInt(id)).val();
        var total=parseFloat($("#tot"+parseInt(id)).val());			
        var subtractTotal=0;
      	//Devolver estatus idamex a 0
        if($("#cargo_asociado"+parseInt(id)).val() != 0){
        	devolver_status_idamex($("#cargo_asociado"+parseInt(id)).val());
        }
        $("#prm_valido").val("");
        $(this).parent().parent().parent().fadeOut("normal", function () {
        	
            $(this).remove();
            frm.no_Comprobaciones_parciales.value=parseInt(frm.no_Comprobaciones_parciales.value)-1;
            frm.rowCount.value=parseInt(frm.rowCount.value)-parseInt(1);
            //alert(parseInt(frm.rowCount.value));
            var i=0;
            for (i=parseInt(id);i<=parseInt(frm.rowCount.value);i++){
            	var nextrenglon="renglonS"+((parseInt(i)+(1)));
				var renglon="renglonS"+parseInt(i);
				var nextrow="row"+((parseInt(i)+(1)));
				var row="row"+parseInt(i); 
				var jqueryrow="input[name=row"+parseInt(i)+"]";
				var nextcargo="cargo_asociado"+((parseInt(i)+(1)));
				var cargo="cargo_asociado"+parseInt(i);
				var jquerycargo="#cargo_asociado"+parseInt(i);
				var nexttipo="tipo"+((parseInt(i)+(1)));
				var tipo="tipo"+parseInt(i);
				var jquerytipo="#tipo"+parseInt(i);
				var nextfecha="fecha"+((parseInt(i)+(1)));
				var fecha="fecha"+parseInt(i);
				var jqueryfecha="#fecha"+parseInt(i);
				var nextconcepto="concepto"+((parseInt(i)+(1)));
				var concepto="concepto"+parseInt(i);
				var jqueryconcepto="#concepto"+parseInt(i);
				var nextcomentario="comentario"+((parseInt(i)+(1)));
				var comentario="comentario"+parseInt(i);
				var jquerycomentario="#comentario"+parseInt(i);
				var nextasistentes="no_asistentes"+((parseInt(i)+(1)));
				var asistentes="no_asistentes"+parseInt(i);
				var jqueryasistentes="#no_asistentes"+parseInt(i);
				var nextproveedor="proveedor"+((parseInt(i)+(1)));
				var proveedor="proveedor"+parseInt(i);
				var jqueryproveedor="#proveedor"+parseInt(i);
				var nextrfc="rfc"+((parseInt(i)+(1)));
				var rfc="rfc"+parseInt(i);
				var jqueryrfc="#rfc"+parseInt(i);
				var nextfactura="flag_factura"+((parseInt(i)+(1)));
				var factura="flag_factura"+parseInt(i);
				var jqueryfactura="#flag_factura"+parseInt(i);
				var nextmonto="mnt"+((parseInt(i)+(1)));
				var monto="mnt"+parseInt(i);
				var jquerymonto="#mnt"+parseInt(i);
				var nextdivisa="divisa"+((parseInt(i)+(1)));
				var divisa="divisa"+parseInt(i);
				var jquerydivisa="#divisa"+parseInt(i);
				var nexttasa="tasa"+((parseInt(i)+(1)));
				var tasa="tasa"+parseInt(i);
				var jquerytasa="#tasa"+parseInt(i);
				var nextpimpuesto="pimpuesto"+((parseInt(i)+(1)));
				var pimpuesto="pimpuesto"+parseInt(i);
				var jquerypimpuesto="#pimpuesto"+parseInt(i);            				
				var nextimpuesto="imp"+((parseInt(i)+(1)));
				var impuesto="imp"+parseInt(i);
				var jqueryimpuesto="#imp"+parseInt(i);
				var nextpropina="propina"+((parseInt(i)+(1)));
				var propina="propina"+parseInt(i);
				var jquerypropina="#propina"+parseInt(i);
				var nextimpuestohospedaje="impuesto_hospedaje"+((parseInt(i)+(1)));
				var impuestohospedaje="impuesto_hospedaje"+parseInt(i);
				var jqueryimpuestohospedaje="#impuesto_hospedaje"+parseInt(i);
				var nextimpH="impH"+((parseInt(i)+(1)));
				var impH="impH"+parseInt(i);
				var jqueryimpH="#impH"+parseInt(i);
				var nexttotal="tot"+((parseInt(i)+(1)));
				var total="tot"+parseInt(i);
				var jquerytotal="#tot"+parseInt(i);            				
				var nextmensaje="mensaje"+((parseInt(i)+(1)));
				var mensaje="mensaje"+parseInt(i);
				var jquerymensaje="#mensaje"+parseInt(i);           				
				var nextdiferencia="diferencia"+((parseInt(i)+(1)));
				var diferencia="diferencia"+parseInt(i);
				var jquerydiferencia="#diferencia"+parseInt(i);
				var nextdelete=((parseInt(i)+(1)))+"delete";
				var del=parseInt(i)+"delete";
				var jquerydel="#"+parseInt(i)+"delete";
				var nexttipocomida="tipo_comida"+((parseInt(i)+(1)));
				var tipocomida="tipo_comida"+parseInt(i);
				var jquerytipocomida="#tipo_comida"+parseInt(i); 
				
				 document.getElementById(nextrenglon).setAttribute("id",renglon);
				 document.getElementById(renglon).innerHTML=i;	
				 document.getElementById(nextrow).setAttribute("name",row);
				 $(jqueryrow).attr("id",row);
				 $(jqueryrow).val(parseInt(i));
		       	 document.getElementById(nextcargo).setAttribute("id",cargo);
		         $(jquerycargo).attr("name",cargo);
		         document.getElementById(nexttipo).setAttribute("id",tipo);
		         $(jquerytipo).attr("name",tipo);
		         document.getElementById(nextfecha).setAttribute("id",fecha);
		         $(jqueryfecha).attr("name",fecha);
		         document.getElementById(nextconcepto).setAttribute("id",concepto);
		         $(jqueryconcepto).attr("name",concepto);
		         document.getElementById(nextcomentario).setAttribute("id",comentario);
		         $(jquerycomentario).attr("name",comentario);
		         document.getElementById(nextasistentes).setAttribute("id",asistentes);
		         $(jqueryasistentes).attr("name",asistentes);
		         document.getElementById(nextproveedor).setAttribute("id",proveedor);
		         $(jqueryproveedor).attr("name",proveedor);
		         document.getElementById(nextrfc).setAttribute("id",rfc);
		         $(jqueryrfc).attr("name",rfc);
		         document.getElementById(nextfactura).setAttribute("id",factura);
		         $(jqueryfactura).attr("name",factura);
		         document.getElementById(nextmonto).setAttribute("id",monto);
		         $(jquerymonto).attr("name",monto);
		         document.getElementById(nextdivisa).setAttribute("id",divisa);
		         $(jquerydivisa).attr("name",divisa);
		         document.getElementById(nexttasa).setAttribute("id",tasa);
		         $(jquerytasa).attr("name",tasa);
		         document.getElementById(nextpimpuesto).setAttribute("id",pimpuesto);
		         $(jquerypimpuesto).attr("name",pimpuesto);
		         document.getElementById(nextimpuesto).setAttribute("id",impuesto);
		         $(jqueryimpuesto).attr("name",impuesto);
		         document.getElementById(nexttipocomida).setAttribute("id",tipocomida);
		         $(jquerytipocomida).attr("name",tipocomida);
		         document.getElementById(nextimpuestohospedaje).setAttribute("id",impuestohospedaje);
		         $(jqueryimpuestohospedaje).attr("name",impuestohospedaje);
		         
		         document.getElementById(nextimpH).setAttribute("id",impH);
		         $(jqueryimpH).attr("name",impH);
		         
		         document.getElementById(nextpropina).setAttribute("id",propina);
		         $(jquerypropina).attr("name",propina);
		         
		         document.getElementById(nexttotal).setAttribute("id",total);
		         $(jquerytotal).attr("name",total);
		         
		         document.getElementById(nextmensaje).setAttribute("id",mensaje);
		         $(jquerymensaje).attr("name",mensaje);
		         document.getElementById(nextdiferencia).setAttribute("id",diferencia);
		         $(jquerydiferencia).attr("name",diferencia);
		         document.getElementById(nextdelete).setAttribute("id",del);
		         $(jquerydel).attr("name",del);

		                             
             }
            guardaComprobacion();
            calculartotal();
        });

        return false;
    });                                                                                                                                                                	
} // Borrar partida

function supervisar_status_idamex(usuario){
	$.ajax({
    	url:  "services/Ajax_comprobacion.php",
        type: "POST",
        data: "idamex_ini=3&usuario="+usuario,
        dataType: "json",
        async: false,
        timeout: 10000,
        success: function(){
        	verificar_tipo_tarjeta();
        },
        complete:function(){
        	borrarPartida();                
            guardaComprobacion();
			muestraValoresGasolina();
        },
        error: function(x, t, m) {
			if(t === "timeout") {
				supervisar_status_idamex(usuario);
			}
		}
    });//fin ajax 
}

// Muestra-oculta ventana emergente de comprobación de gasolina
function muestraVentanaGasolina(valor){		
	if (valor==1){					
		var no_partidas = $("#comprobacion_table>tbody>tr").length;
		var extra = (6.5*parseInt(no_partidas)-6.5); 
		var top = ($("#tipo").val() == 2) ? 150+extra+"%" : 115+extra+"%" ;					
		$("#ventanaGasolina").css("top", top);
		$("#ventanaGasolina").fadeIn();
		$("#ventanaGasolina").css("visibility","visible");															
	}else{
		$("#select_tipo_auto").val('');	
		$("#select_modelo").val('');
		$("#kilometraje").val('');
		$("#monto_gasolina").val('');
		$("#ruta_detallada").val('');
		$("#ventanaGasolina").css("visibility","hidden");					
	}				
}

function calculaTotalDolares(){
	var moneda = $("#moneda").val();
	var monto = parseFloat($("#monto").val().replace(/,/g,""));
	var iva = ($("#impuesto").val() =="")? 0 : parseFloat($("#impuesto").val().replace(/,/g,""));				
	var propina = ($("#propina_dato").val() == "") ? 0 : parseFloat($("#propina_dato").val().replace(/,/g,""));
	var impHospedaje = ($("#impuesto_hotel").val() == "") ? 0 : parseFloat($("#impuesto_hotel").val().replace(/,/g,""));
	var total = parseFloat(iva)+parseFloat(monto)+parseFloat(propina)+parseFloat(impHospedaje);
	total = total.toFixed(2);
	$("#total").val(number_format(total,2,".",","));
	
	if(moneda == -1){
		$("#total_dolares").val(number_format(total,2,".",","));
		//ComentariosObligatorios();
	}else{
		$.ajax({      
	        type: "POST",
	        url: "services/Ajax_comprobacion.php",
	        data: "convierteDivisa="+parseFloat($("#total").val().replace(/,/g,""))+"&divisa="+moneda,
	        dataType: "json",
	        timeout: 10000,
	        success: function(json){
	        	$("#total_dolares").val(number_format(json,2,".",","));		        	
	        },complete: function(json){
	        	ComentariosObligatorios();
		    },
		    error: function(x, t, m) {
				if(t==="timeout") {
					calculaTotalDolares();
				}
			}
		});
	}
}

function cargar_detalles(){
    $("#impuesto").val(0);
    $("#div_iva").css("display", "block"); 
	$("#impuesto").css("display", "block");				
	//funcion para rellenar combos.
	var frm=document.detallecomp;
	var cargo_localizar=frm.select_tarjeta_cargo.value;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "no_cargo="+cargo_localizar,
		dataType: "json",
		async : false,
		timeout: 10000,
		success: function(json){		
			if(json[0].idamex == null){
				$("#fecha_cargo").html("");
				$("#establecimiento_cargo").html("");
				$("#monto_cargo").html(""); 
				$("#rfc_cargo").html("");
				$("#moneda_local").html("");
				$("#amex_pesos").html("");
				$("#amex_dolar").html("");
				$("#moneda_fact_val").val("");
				$("#fecha_cargo_val").val("");
				$("#establecimiento_cargo_val").val("");
				$("#monto_cargo_val").val("");
				$("#amex_pesos_val").val("");
				$("#amex_dolar_val").val("");
				$("#rfc_cargo_val").val("");
				$("#moneda_local_val").val("");
				$("#tipo_cambio").val("");
				$("#div_tipo_cambio").html("");
			}else{					
				$("#rfc_cargo_val").val(json[0].rfc_establecimiento);
				$("#fecha_cargo").html(json[0].fecha_cargo);   
				$("#establecimiento_cargo").html(json[0].concepto);   
				$("#monto_cargo").html(number_format(json[0].monto,2,".",",")+" "+json[0].moneda_local);
				$("#amex_dolar").html(number_format(json[0].montoAmex,2,".",",")+" "+json[0].monedaAmex);
				$("#amex_dolar_val").val(json[0].montoAmex+" "+json[0].monedaAmex);
				$("#moneda_local").html(json[0].moneda_local);
				$("#moneda_fact_val").val(json[0].monedaAmex);   
				$("#fecha_cargo_val").val(json[0].fecha_cargo);   
				$("#establecimiento_cargo_val").val(json[0].concepto);   
				$("#monto_cargo_val").val(json[0].monto);
				$("#moneda_local_val").val(json[0].moneda_local);
				var amex_pesos = parseFloat(json[0].conversion_pesos);	
				amex_pesos = amex_pesos.toFixed(2);										
				$("#amex_pesos_val").val(amex_pesos);				
				
				
				$("#no_transaccion").html(json[0].notransaccion);
				$("#idAmex").val(json[0].idamex);	
				var tipo_cambio = parseFloat(json[0].montoAmex/json[0].monto);
				$("#tipo_cambio").val(tipo_cambio.toFixed(2));
				$("#div_tipo_cambio").html(tipo_cambio.toFixed(5));							
				amex_pesos = number_format(amex_pesos,2,".",",");
				$("#amex_pesos").html(amex_pesos);
				
				if(json[0].rfc_establecimiento == "" || json[0].rfc_establecimiento == null || $("#select_tipo_tarjeta").val() == 2 || json[0].moneda_local != "MXN"){
					$("#rfc_cargo_val").val(0);
					$("#rfc_cargo").html("N/A");
					$("#fact_chk").attr("checked",false);
					$("#fact_chk").removeAttr("disabled",true);
					$("#factura_form").slideUp(1000);
					$("#d_folio").val("");
					$("#rfc").val("");
					$("#proveedor").val("");
					//$("#div_iva").hide();
					//$("#impuesto").hide();
				}else{								
					$("#rfc_cargo_val").val(json[0].rfc_establecimiento);
					$("#rfc_cargo").html(json[0].rfc_establecimiento);		
					$("#fact_chk").attr("disabled",true);											
					$("#fact_chk").attr("checked",true);									
					$("#factura_form").slideDown(1000);																								
					$("#div_iva").css("display", "block"); 
					$("#impuesto").css("display", "block"); 
				} 											 
			}
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				cargar_detalles();
			}
		}
	});
	// Realizar calculo de nuevo
	calculaTotalDolares();											
}

function LlenarCombo(json, combo){
	combo.options[0] = new Option('Selecciona un item', '');
	for(var i=0;i<json.length;i++){
		var str=json[i];
		var str1=str.slice(str.search(":")+1);
		var str2=str.substr(0,str.search(":"));
		arreglovalores[arreglovalores.length]=str1;
		arreglodescripcion[arreglodescripcion.length]=str2;
		if(arregloestatus[arregloestatus.length]!=false)
		arregloestatus[arregloestatus.length]=true;	
	}
}

function LlenarCombo2(arregloval,arreglodesc,arreglosts,combo){
	var frm=document.detallecomp;
	LimpiarCombo(combo);
	combo.options[0]=new Option('Selecciona un item', '');
	for(var i=0;i<arregloval.length;i++){
		if(arreglosts[i]==true){
			combo.options[combo.length] = new Option(arregloval[i],arreglodesc[i]);
			if(combo.options[i+1].value == frm.id_cargo_amex_seleccionado.value){
				combo.options[i+1].selected = true;
				cargar_detalles();
			}
		}
	}
}

function obtenercargos(valor){
	var frm = document.detallecomp;
	var tramite = $("#tramiteComprobacion").val();
	LimpiarCombo(frm.select_tarjeta_cargo);
	if(valor != ""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "no_tarjeta="+valor+"&comprobacion="+tramite,
			dataType: "json",
			async: false,
			timeout: 10000,
			success: function(json){
				if(json==null){
					$("#select_tarjeta_cargo").append(new Option("Sin Datos"));
				}else{
					arreglovalores.splice(0,arreglovalores.length);
					arregloestatus.splice(0,arregloestatus.length);
					arreglodescripcion.splice(0,arreglodescripcion.length);
					LlenarCombo(json, frm.select_tarjeta_cargo);
					LlenarCombo2(arreglovalores,arreglodescripcion,arregloestatus,frm.select_tarjeta_cargo);
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					obtenercargos(valor);
				}
			}
		});
	}
}

//Solo seleccionaremos el cargo AMEX del Gasto que se registro previamente 
function cargaGastoUnicoAmex(idAmex_en_edicion){
	var frm=document.detallecomp;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		dataType: "json",
		data: "idcargoAMEX="+idAmex_en_edicion,
		async: false,
		timeout: 10000,
		success: function(json){
			if(json==null){
				$("#select_tarjeta_cargo").append(new Option("Sin Datos"));
			}else{
				arreglovalores.splice(0,arreglovalores.length);
				arregloestatus.splice(0,arregloestatus.length);
				arreglodescripcion.splice(0,arreglodescripcion.length);
				LlenarCombo(json, frm.select_tarjeta_cargo);
				LlenarCombo2(arreglovalores,arreglodescripcion,arregloestatus,frm.select_tarjeta_cargo);
			}
		},
		complete: function(json){
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				cargaGastoUnicoAmex(idAmex_en_edicion);
			}
		}
	});
}

function verificar_tipo_tarjeta(){
    var tipo_tarjeta = $("#select_tipo_tarjeta option:selected").val();
    var noemple = $("#iu").val();
	(tipo_tarjeta == 2) ? $("#fact_chk").attr("disabled",true):$("#fact_chk").removeAttr("disabled");

	// Apartado para seleccionar el gasto AMEX mediante su ID 
	var aux = $("#edicionAux").val();
	aux = aux.split("|");
	var bandera_gasto_en_edicion = aux[0];
	var idAmex_en_edicion = aux[1];
	
	if(tipo_tarjeta != "" && tipo_tarjeta != "-1"){
		limpiar_cuadro_amex(0);
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "tipo_tarjeta="+tipo_tarjeta+"&usuario="+noemple,
			dataType: "html",
			async: false,
			timeout: 10000,
			success: function(dato){
				if(dato == ""){
					$("#no_tarjeta_credito").html("Datos no Encontrados");
					$("#select_tarjeta_cargo").empty();
					$("#select_tarjeta_cargo").append('<option value="-1">Sin Datos</option>');
				}else{
					$("#no_tarjeta_credito").html(dato);
					if(bandera_gasto_en_edicion == 1){ // Solo se cargara el gasto cuyo ID corresponda 
						cargaGastoUnicoAmex(idAmex_en_edicion);
					}else{
						obtenercargos(dato);
					}
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					verificar_tipo_tarjeta();
				}
			}
		});
	}else{
		limpiar_cuadro_amex();
	}
}

function borraPropina(){
	$("#impuesto_hotel").val('0');				
	$("#propina_dato").val('0');
	if( $("#select_concepto").val() != 6 ){
		$("#tipocomidadato").val('0');
	}
	calculaTotalDolares();
}

function validaPoliticasComentario(){
	if($("#select_concepto").val() == 6){
		if($("#no_asistentes").val() > 1 ){
			$("#comentarioReq").html("Comentario<span class='style1' >*</span>:");
		}else{
			if($("#excedeMontoComentarios").val() == 1){
				$("#comentarioReq").html("Comentario<span class='style1' >*</span>:");
			}else{							
				$("#comentarioReq").html("Comentario:");
			}
		}
	}else if($("#select_concepto").val() == 25){
		/* Validará que si el concepto es Otros Gastos, colocaría el asterisco para indicar que el campo 
		 * de Comentarios es obligatorio.
		*/ 
		$("#comentarioReq").html("Comentario<span class='style1' >*</span>:");					
	}else{
		$("#comentarioReq").html("Comentario:");
	}	
}

function validaImpuestoHotelRequerido(){
	if($("#select_concepto").val() == 5 && $("#fact_chk").is(":checked") == true){
		$("#div_impuesto_hospedaje").html("Impuesto hospedaje<span class='style1' >*</span>:");
	}else{
		$("#div_impuesto_hospedaje").html("Impuesto hospedaje:");
	}					
}

function verificar_concepto(valor){
	// Para verificar si el valor es comida y visualiza en div de asistentes
	if(valor == 6){
		$("#propina").css("display", "block");
		$("#div_propina").css("display", "block");
        $("#asistentes").css("display", "block");
        $("#asistentes2").css("display", "block");
        $("#no_asistentes").val("1");
        $("#div_impuesto_hospedaje").css("display", "none");
        $("#div_impuesto_hospedaje2").css("display", "none");
        $("#concepto_alim_hot").val("alimentacion");
        $("#tipo_comida_label").css("display","block");
		$("#div_tipo_comida_label").css("display","block");
	}else if(valor==5 ){
        $("#no_asistentes").val("1");
        $("#div_impuesto_hospedaje").css("display", "block");
        $("#div_impuesto_hospedaje2").css("display", "block");
        $("#concepto_alim_hot").val("hospedaje");
		$("#propina").css("display", "block");
        $("#div_propina").css("display", "block");
		$("#tipo_comida_label").css("display","none");
		$("#div_tipo_comida_label").css("display","none");
        $("#asistentes").css("display", "none");
        $("#asistentes2").css("display", "none");
	}else if(valor == valorComidasRepresentacion){
		// Desahabilitar el botón de envio de Comprobación
		$("#guardarComp").attr("disabled","true");
		// Mostrar Campo de Propina
		$("#propina").css("display", "block");
		$("#div_propina").css("display", "block");
        // Mostrar sección de invitados
		$("#seccion_inivitados").css("display","block");
		// Cargar los invitados de la solicitud
		cargarInvitadosTramite(0);
	}else{
		// Restablecer los campos involucrados con el concepto de Comidas de Representación
		restablececamposComidasRepresentacion();
		
		$("#tipo_comida_label").css("display","none");
		$("#div_tipo_comida_label").css("display","none");
		$("#asistentes").css("display", "none");
        $("#asistentes2").css("display", "none");
		$("#propina").css("display", "none");
		$("#propina_dato").val("0");
        $("#div_propina").css("display", "none");
		$("#div_impuesto_hospedaje").css("display", "none");
        $("#div_impuesto_hospedaje2").css("display", "none");
	}
  
    if(valor!=6 && valor!=5 && valor!=30 && valor!=31 && valor!=32 && valor!=valorComidasRepresentacion){
        $("#propina").css("display", "none");
		$("#propina_dato").val("0");
        $("#asistentes").css("display", "none");
        $("#no_asistentes").val("1");
        $("#imp_hospedaje").css("display", "none");
        $("#imp_hosp_dato").val("0");
        $("#concepto_alim_hot").val("0");                                                       
    }
}

// Función que permitirá distinguir si es un concepto de tipo Alimento (id 6) y/o Hotel (id 5)
function tipoConcepto(){			
	conceptoNombre = "";			
	//obtenemos el nombre del concepto seleccionado
	var frm=document.detallecomp;			
	conceptoNombre=frm.select_concepto.options[frm.select_concepto.selectedIndex].text;
	// tramiteId = document.getElementById("sol_select").value;
	tramiteId = 0;
	//obtenemos el id del concepto seleccionado
	conceptoId=$("#select_concepto").val();

	//parametros de los 3 conceptos : Comentarios Obligatorios
	if((conceptoNombre == "Alimentos") || ( conceptoNombre == "Hotel") || (conceptoNombre == "Lavander"+arrayAcentos["i"]+"a")){
		validacionParametro(conceptoNombre,tramiteId,conceptoId);
	}
}

// Función que nos permitirá saber el tipo de comida que selecciono
function tipoComida(valor){
	var frm=document.detallecomp;
	idComida=frm.tipocomidadato.options[valor].text;				
}

// Muestra el div y el campo de IVA
function muestraIva(){
	var divisa = $("#moneda").val();
	if (divisa == "MXN"){
		var impuesto = $("#monto").val()*.16;
		$("#div_iva").css("display", "block"); 
		$("#impuesto").css("display", "block"); 
		$("#impuesto").val(impuesto.toFixed(2));
		$("#total").val( parseFloat($("#monto").val()) + parseFloat($("#impuesto").val()));									
	}else if(!$("#fact_chk").is(":checked")){
		$("#div_iva").css("display", "none"); 
		$("#impuesto").css("display", "none"); 				
		$("#impuesto").val(0);
		$("#total").val( parseFloat($("#monto").val()));	
	}			
	calculaTotalDolares();
}

function muestraIva2(){				
	if ($("#fact_chk").is(":checked")){
		var impuesto = $("#monto").val()*.16;
		$("#div_iva").css("display", "block"); 
		$("#impuesto").css("display", "block"); 
		$("#impuesto").val(impuesto.toFixed(2));		
		$("#total").val( parseFloat($("#monto").val()) + parseFloat($("#impuesto").val()) );				
	}else if($("#moneda").val()!="MXN"){
		$("#div_iva").css("display", "none"); 
		$("#impuesto").css("display", "none"); 		
		$("#impuesto").val(0);										
		$("#total").val( parseFloat($("#monto").val()));	
	}
	calculaTotalDolares();
}

function verificaFactura(){
    var frm=document.detallecomp;	
    if (frm.fact_chk.checked){
        validaFactura=1;
        $("#factura_form").slideDown(1000);
        $("#factura_form").css("display","block");
        $("#rfc_prov_busq_div").html("RFC<span class='style1'>*</span>: ");
        $("#name_prov_busq_div").html("Raz"+arrayAcentos["o"]+"n Social<span class='style1'>*</span>: ");
        $("#div_folio").html("Folio Factura<span class='style1'>*</span>: ");
        frm.d_folio.disabled=false;                    
    }else{
        validaFactura=0;
        $("#factura_form").slideUp(1000);
        frm.d_folio.disabled=true;
		$("#rfc").val('');
		$("#d_folio").val('');
		$("#proveedor").val('');                    
    }
}

function flotanteActive(valor){	
	if(valor == 1){
		var top = ($("#tipo").val() == 2) ? "85%" : "50%" ;					
		$("#proveedor_form").css("top", top);	
		$("#proveedor_form").css("visibility","visible");		
		$("#proveedor_form").css("display","block");												
    }else{
		$("#proveedor_form").css("visibility","hidden");	
		$("#proveedor_form").css("display","none");			    
	}
}

// Calcula monto de gasolina
function calculaMontoGasolina(){				
	var km = $("#kilometraje").val();
	var ma = $("#select_modelo").val();
	$.ajax({
        type: "POST",
        url: "services/Ajax_comprobacion.php",
        data: "ma_id="+ma,
        dataType: "json",
        async : false,
        timeout: 10000,
        success: function(json){
        	ma = json;
			ma = ma*km;
			$("#monto_gasolina").val(number_format(ma,2,".",","));
        },
        error: function(x, t, m) {
			if(t==="timeout") {
				calculaMontoGasolina();
			}
		}
	});
}

// Obenter ma_factor
function obtenMafactor(valor){
	var ma = $("#select_modelo").val();
	$.ajax({      
        type: "POST",
        url: "services/Ajax_comprobacion.php",
        data: "ma_id="+ma,
        dataType: "json",
        async : false,
        timeout: 10000,
        success: function(json){
        	$("#factor").html("Factor gasolina: "+ json);		        	
        },
        complete: function(json){
        	calculaMontoGasolina();
	    },
	    error: function(x, t, m) {
			if(t==="timeout") {
				obtenMafactor(valor);
			}
		}
	});
}

// Válida Gasolina	
function validaExcepcionGasolina(){
	var politicaGasolina = $("#valpoliticaGasolina").val();
	if (confirm('Los datos ingresados en la pantalla no podrán modificarse posteriormente.')){			
		$("#sol_select").removeAttr("disabled");
		var totalGasolina = 0;
		var gasolina = false;
		var numFilas = parseInt($("#comprobacion_table>tbody >tr").length);
		var excedente;
		var msg = "El total excede el monto por factor. La diferencia será descontada váa nómina o no reembolsada";
		$("#monto_gasolina").removeAttr("disabled");
		
		if($("#select_tipo_auto").val()!=1){
			for(var i=1; i<=numFilas; i++){
				// Extreremos el valor del cargo AMEX (Contiene monto y moneda original)
				var montoCargoAMEX = $("#cargo_factura"+i).val();
				// Obtenemos el inicio de los tres últimos caracteres
				var longitudCargo = parseInt(montoCargoAMEX.length) - 3;
				// Obtener moneda original del cargo AMEX
				var monedaCargoAMEX = montoCargoAMEX.substr(longitudCargo, 3);
				
				if(monedaCargoAMEX == "MXN" || (monedaCargoAMEX == "" && $("#divisa"+i).val() == "MXN")){
					if(($("#numConcepto"+i).val() == 12)) {
						totalGasolina += parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
						gasolina = true;
					}
				}
			}
			
			excedente = (parseFloat($("#monto_gasolina").val().replace(/,/g,"")) - parseFloat(totalGasolina));
			if(excedente < -(parseFloat(politicaGasolina))){
				$("#mensaje_excepcion_gasolina").val(msg);
				$("#dif_gasolina").val(excedente);
				alert(msg);
			}
		}
		return true;
	}else{
		return false;
	}			
}

// Válida los detalle de gasolina
function validaDatosGasolina(){
	if( ($("#select_tipo_auto").val() == -1) || ($("#select_modelo").val() == -1) || ($("#kilometraje").val() == '') || ($("#monto_gasolina").val() == '') ||  ($("#ruta_detallada").val() == '') || ($("#kilometraje").val() == '0')){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		return false;
	}else{
		return validaExcepcionGasolina();
	}
}

function recalcula(){
	var no_partidas = parseInt($("#comprobacion_table>tbody >tr").length);
	var anticipo_solicitado = parseFloat($("#anticipo_solicitado2").val().replace(/,/g,""));
	var anticipo_comprobado = 0;
	var personal_anticipo = 0;
	var personal_amex = 0;
	var personal_amex_externo = 0;
	var comprobado_amex = 0;
	var personal_efectivo = 0;
	var efectivo_comprobado = 0;
	var amex_externo = 0;
	
	for(var i = 1; i <= no_partidas; i++){
		// suma el personal y el total por anticipo
		if ($("#cargo_asociado"+i).val() == "Anticipo"){
			anticipo_comprobado+= parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			if($("#concepto"+i).val() == "Personal"){
				personal_anticipo+= parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			}
		}
		
		// suma el personal y el total por amex
		if($("#cargo_asociado"+i).val() == "AMEX"  || $("#cargo_asociado"+i).val() == "Amex"){
			comprobado_amex+=parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			if($("#concepto"+i).val() == "Personal"){
				personal_amex+=parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			}
		}
		
		// suma el personal y el total por efectivo Reembolo para empleado
		if($("#cargo_asociado"+i).val() == "Reembolso" || $("#cargo_asociado"+i).val() == "Reembolso"){
			efectivo_comprobado+=parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
		}
		
		// suma el personal y el total por efectivo Reembolo para empleado
		if($("#cargo_asociado"+i).val() == "Amex externo"){
			amex_externo+=parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			if($("#concepto"+i).val() == "Personal"){
				personal_amex_externo += parseFloat($("#totalMxn"+i).val().replace(/,/g,""));
			}
		}
	}
	
	$("#anticipo_comprobado_autorizado_BMW2").val(anticipo_comprobado);
	$("#anticipo_comprobado_autorizado_BMW1").html(number_format(anticipo_comprobado,2,".",",") + " MXN");
	
	$("#personal_descontar2").val(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo));
	$("#personal_descontar1").html(number_format(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo),2,".",",")+ " MXN");
	
	$("#amex_comprobado_autorizado_BMW2").val(comprobado_amex);
	$("#amex_comprobado_autorizado_BMW1").html(number_format(comprobado_amex,2,".",",") + " MXN");
	
	$("#efectivo_comprobado_autorizado_BMW2").val(efectivo_comprobado);
	$("#efectivo_comprobado_autorizado_BMW1").html(number_format(efectivo_comprobado,2,".",",") + " MXN");
	
	$("#monto_a_descontar2").val(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo));
	$("#monto_a_descontar1").html(number_format(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo),2,".",",") + " MXN");
	
	$("#monto_a_reembolsar2").val(efectivo_comprobado);
	$("#monto_a_reembolsar1").html(number_format(efectivo_comprobado,2,".",",") + " MXN");
	
	//$("#amex_externo2").val(amex_externo);
	//$("#amex_externo1").html(number_format(amex_externo,2,".",",") + " MXN");
}

function confirmSave(val){
	var valor = false;
	var msg = (val == 1) ? "Est"+arrayAcentos["a"]+" seguro que desea enviar la Comprobaci"+arrayAcentos["o"]+"n" : "Est"+arrayAcentos["a"]+" seguro que desea guardar est"+arrayAcentos["o"]+" Comprobaci"+arrayAcentos["o"]+"n como previo";
	if(confirm("AVISO: "+arrayAcentos["Int"]+""+msg+"?")){
		$("#sol_select").removeAttr("disabled");
		valor = true;
	}
	return valor;
}

function validaGasolina(){
	var gasolina = false;
	var Alimentos = false;
	var Hotel = false;
	var Lavanderia = false;
	
	var numFilas = parseInt($("#comprobacion_table>tbody>tr").length);
	for(var i=1; i<=numFilas; i++){
		if($("#divisa"+i).val()=="MXN"){
			if( ($("#numConcepto"+i).val() == 12)) {
				gasolina = true;										
			}else if($("#concepto"+i).val() == "Alimentos"){
				Alimentos = true;
			}else if($("#concepto"+i).val() == "Hotel"){
				Hotel = true;
			}else if($("#concepto"+i).val() == "Lavanderï¿½a"){
				Lavanderia = true;
			}
		}
	}
	
	$("#validacionCompleta").val(1);
	
	if(gasolina){
		//$("#select_concepto").attr("disabled","true");
		//validacionViaje(tramiteId,Alimentos,Hotel,Lavanderia);
		if($("#validacionCompleta").val() == 1){
			muestraVentanaGasolina(1);
			return false;
		}
	}else{
		//validacionViaje(tramiteId,Alimentos,Hotel,Lavanderia);
		if($("#validacionCompleta").val() == 1){
			return true;
		}
	}
}

function validacion(){
	//Validacion concepto
	if(!confirmSave(1)) return false;
	
	var no_conceptos = parseInt($("#comprobacion_table>tbody >tr").length);
	var miArray = new Array();
	for(i = 1; i <= no_conceptos; i++){
		if($("#cargo_asociado"+i).val() == "Amex" || $("#cargo_asociado"+i).val() == "AMEX"){
			var cargo_factura = $("#totalMxn"+i).val().replace(/,/g,"");
			var transacion = $("#idAmex"+i).val();
			var existe = false;
			
			for(j=0;j<miArray.length;j++){
				if(transacion == miArray[j][0]){
					existe = true;
				}
			}
			if(!existe){
				miArray[miArray.length] = transacion;
			}
		}
	}
	
	var ver_fal=true;
	for(z=0;z<miArray.length;z++){
		var resultado = valida_suma_cargo2(miArray[z]);
		resultado = resultado.toFixed(2);
		var idamex = miArray[z];
		var url = "services/Ajax_comprobacion.php";
		$.ajaxSetup({async:false});
		$.post(url,{resultadoAmex:resultado,transacionAmex:idamex}, function(data){
			if (data != "0"){
				alert("El total de los conceptos ingresados en la transacci"+arrayAcentos["o"]+"n "+ data +" difiere al Total Amex");
				ver_fal = false;
			}
		});
	}
	
	if(ver_fal){
		return (validaGasolina());																
	}else{
		return false;
	}
}

function limpiarCamposDeProveedor(){
	$("#new_proveedor").val("");
	$("#new_p_rfc").val("");
	$("#new_p_addr").val("");
}

//Agrega Nuevo Proveedor al catálogo
function nuevoProveedor(nombreProveedor,rfcProveedor,dirFiscal){
	var frm=document.detallecomp;
	if(nombreProveedor == "" || nombreProveedor == 0){
		alert("Debe ingresar una raz"+arrayAcentos["o"]+"n social.");
        return false;
	}else if(valida_formatoRFC($("#new_p_rfc").val()) == null){ 
		frm.new_p_rfc.value = ""; 
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente");
		return false;
	}else if((rfcProveedor<12 || rfcProveedor>13) &&  frm.fact_chk.checked==true){
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
        	timeout: 10000,
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
                    $("#new_p_rfc").focus();
                }
            },
            error: function(x, t, m) {
				if(t==="timeout") {
					nuevoProveedor(nombreProveedor,rfcProveedor,dirFiscal);
				}
			}
        }); // Fin ajax
        return false;
    }//fin if rfc
} // Fin nuevoProveedor

function valida_formatoRFC(campo){
	var resultado = campo.match(/[A-Z]{3,4}[0-9]{6}((([A-Z]|[a-z]|[0-9]){3}))/); 
	return resultado;
}

/*
* Sección de edición de partidas
*/

// Edita un concepto de comprobacion 
function editarComprobacion(id){
	// Restablecer las opciones de la lista de Conceptos solo si la partida en edición es Comidas de Representación
	if($("#numConcepto"+parseInt(id)).val() == valorComidasRepresentacion){
		$("#mensajeInformativo").slideDown(1000);
        $("#mensajeInformativo").css("display", "block");
		restablecerOpciones();
	}
	
	// Regresa el estatus a 0 para edicion
	var idamex = $("#idAmex"+parseInt(id)).val();
	var detalleComp = $("#idDetalleComp"+parseInt(id)).val();
	
	if(!estatus_en_edicion_de_comprobacion){				
		if(idamex > 0){ // No devolveremos el estatus del cargo a 0. 
			//devolver_status_idamex(idamex);
			id = parseInt(id);
			cCObligatorio = id;
		}
	}
	
	// Bandera que ayudará a saber si un cargo esta siedo editado 
	$("#edicionAux").val(1+"|"+idamex);
	$("#id_cargo_amex_seleccionado").val(idamex);
	
	// Habilitar Campos de Montos  
	$("#monto").removeAttr("disabled");
	$("#moneda").removeAttr("disabled");
	$("#referencia_corta").removeAttr("disabled");
	
	var concepto = $("#concepto"+parseInt(id)).val();
	
	if(concepto == "Alimentos"){
		$("#concepto_alim_hot").val("alimentacion");
	}else if(concepto == "Hotel"){
		$("#concepto_alim_hot").val("hospedaje");
	}else{
		$("#concepto_alim_hot").val(0);
	}
	
	$("#guardarPrev").attr("disabled","true");
	$("#guardarComp").attr("disabled","true");			

	if(bandera)	$("#registrar_comp").removeAttr("disabled");
	
	//Se checa si exite un itinerario en edicion
	if(estatus_en_edicion_de_comprobacion){
		alert("Se esta editando una comprobacion, de click antes en Actualizar Itinerario");
		return false;
	}
	
	$("#registrar_comp").attr("value","     Actualizar Gasto");
	var frm=document.detallecomp;			
	id = parseInt(id);
	$(".elimina").css("display","none");//oculta boton eliminar
	$(".del_part").html("N/A");
	//Al editar un itinerario, se guarda el numero de itinerario que se va a editar.
	frm.itinerarioActualOPosible.value = id;
	$("#tipo").val($("#tipoComprobacion"+id).val()); //Anticipo, Amex, Reembolso
	$("#tipo").attr("disabled", "disabled");
	
	if($("#tipoComprobacion"+id).val() == 2){
		tipo_de_comprobacion($("#tipoComprobacion"+id).val());
		$("#select_tipo_tarjeta").val($("#tipoTarjeta"+id).val());
		verificar_tipo_tarjeta();
		$("#select_tarjeta_cargo").val(idamex);
		$('#select_tarjeta_cargo> option[value="'+idamex+'"]').attr('selected', 'selected');
		cargar_detalles();
		
		$("#select_tipo_tarjeta").attr("disabled", "disabled");
		$("#select_tarjeta_cargo").attr("disabled", "disabled");
	}
	ocultaAmex();			
	$("#select_concepto").val($("#numConcepto"+id).val());//concepto

	// Extraer limites de las Politï¿½cas 
	tipoConcepto();
	
	$("#div_propina").hide();
	$("#propina").hide();
	$("#asistentes").hide();
	$("#asistentes2").hide();
	$("#div_tipo_comida_label").hide();
	$("#tipo_comida_label").hide();
	$("#div_impuesto_hospedaje").hide();				
	$("#div_impuesto_hospedaje2").hide();
	if($("#numConcepto"+id).val()!= 24){
		$("#comentarioReq").html('Comentario :');
	}
	if($("#numConcepto"+id).val()== 5){
		$("#propina").css("display", "block");
		$("#div_propina").css("display", "block");
		$("#propina_dato").val($("#propina"+id).val().replace(/,/g,""));
		$("#div_impuesto_hospedaje").css("display", "block");
        $("#div_impuesto_hospedaje2").css("display", "block");
		$("#impuesto_hotel").val($("#impuesto_hospedaje"+id).val().replace(/,/g,""));
	}else if($("#numConcepto"+id).val()== 6){
		$("#asistentes").css("display", "block");
        $("#asistentes2").css("display", "block");
		$("#no_asistentes").val($("#no_asistentes"+id).val());
		$("#propina").css("display", "block");
		$("#div_propina").css("display", "block");
		$("#propina_dato").val($("#propina"+id).val().replace(/,/g,""));
		$("#tipo_comida_label").css("display","block");
		$("#div_tipo_comida_label").css("display","block");
		$("#tipocomidadato").val($("#tipo_comida"+id).val());
	}else if($("#numConcepto"+id).val() == valorComidasRepresentacion){
		// Desahabilitar el botón de envio de Comprobación
		$("#guardarComp").attr("disabled","true");
		// Mostrar Campo de Propina
		$("#propina").css("display", "block");
		$("#div_propina").css("display", "block");
        // Mostrar sección de invitados
		$("#seccion_inivitados").css("display","block");
		// Mostrar el valor introducido en la propina
		$("#propina_dato").val($("#propina"+id).val().replace(/,/g,""));
	}
	
	$("#fecha").val($("#fecha"+id).val());//fecha
	$("#monto").val($("#mnt"+id).val().replace(/,/g,""));//monto origen
	$("#total").val($("#tot"+id).val());//total
	$("#moneda").val($("#divisa"+id).val());//tipo de divisa
	if($("#comentario"+id).val() == "Sin Comentarios"){
		$("#referencia_corta").val("");//comentario
	}else{
		$("#referencia_corta").val($("#comentario"+id).val());//comentario
	}
	if($("#check"+id).val()== "on"){
		$('input[name=fact_chk]').attr('checked', true);
		$("#d_folio").val($("#flag_factura"+id).val());
		$("#rfc").val($("#rfc"+id).val());
		$("#proveedor").val($("#proveedor"+id).val());
		$("#impuesto").val($("#pimpuesto"+id).val().replace(/,/g,""));
		$("#div_iva").css("display", "block"); 
		$("#impuesto").css("display", "block"); 
		verificaFactura();	
	}
	
	if($("#divisa"+id).val() == "MXN" || $("#rfc"+id).val() != "N/A" ){
		$("#impuesto").val($("#pimpuesto"+id).val().replace(/,/g,""));
		$("#div_iva").css("display", "block"); 
		$("#impuesto").css("display", "block"); 
	}
	
	// Cargar excepciones
	if($("#exConcepto"+id).val() != ""){
		$("#comentarioReq").html("Comentario<span class='style1'>*</span>:");
		$("#textoExcepcion").val($("#exConcepto"+id).val());
		$("#excedePolitica").val(1);
	}
	
	calculaTotalDolares();
	
	//Se pone el estatus en edicion
	estatus_en_edicion_de_comprobacion = true;
	// obtenemos el id del registro para la edicion de comentarios obligatorios
	if(estatus_en_edicion_de_comprobacion == true){
		//alert("com"+cCObligatorio);
		cCObligatorio = id;
	}
	
	/*conceptoNombre=frm.select_concepto.options[frm.select_concepto.selectedIndex].text;
	if(conceptoNombre == "Alimentos"){
		tramiteId = $('#sol_select').val();
		conceptoId = $('#numConcepto'+id).val();
	}*/

	// Desactivar las listas Tipo de Tarjeta y Lista de Cargos en el caso 
	// que la Comprobaciï¿½n sea Devuelta con Observaciones por Finanzas 
	if($("#etapaComprobacion").val() == 5){
		$("#select_tipo_tarjeta").attr("disabled", "disabled");
		$("#select_tarjeta_cargo").attr("disabled", "disabled");
	}
}

// Elimina el resgistro de un concepto
function borrarRenglonComprobacion(id,tabla,rowCount,rowDel,count,edit,del,rowActualOPosible){
		
	$("#"+rowCount).bind("restar",function(e,data,data1){
		e.stopImmediatePropagation();
		$("#"+rowCount).val(parseInt($("#"+tabla+">tbody>tr").length));
		if(rowActualOPosible != ""){
			$("#"+rowActualOPosible).val(parseInt($("#"+tabla+">tbody>tr").length)+parseInt(1));
		}
	});
	
	$("#"+rowDel).bind("cambiar",function(e,inicio,tope){
		e.stopImmediatePropagation();			
		tab = document.getElementById(tabla);			
		for (var i=parseInt(id);i<=parseFloat(tope);i++){		
			fila_act = tab.getElementsByTagName('tr')[i];		
			for (j=0;celda_act = fila_act.getElementsByTagName('input')[j]; j++){
				if(celda_act.getAttribute('type') == 'hidden'){
					var aux = celda_act.getAttribute('id');
					var re = /[0-9]*$/;
					aux = aux.replace(re, "");
											
					var elemento_sig = "#"+aux+((parseInt(i)+(1)));
					var elemento_act =  ""+aux+parseInt(i);
					$(elemento_sig).attr("name",elemento_act);
					$(elemento_sig).attr("id",elemento_act);						
				}
			}
			
			//Recorre el div contador de registro
			if(count != ""){
				count_sig = "#"+count+((parseInt(i)+(1)));
				count_act = ""+count+parseInt(i);
					$(count_sig).html(parseInt(i));
					$(count_sig).attr("name",count_act);
					$(count_sig).attr("id",count_act);
			}
			//Recorre la imagen de editar registro
			if(edit != ""){
				edit_sig = "#"+(parseInt(i)+(1))+edit;
				edit_act = ""+parseInt(i)+edit;
					$(edit_sig).attr("name",edit_act);
					$(edit_sig).attr("id",edit_act);
			}
			//Recorre la imagen de eliminar registro
			if(del != ""){
				del_sig = "#"+(parseInt(i)+(1))+del;
				del_act = ""+parseInt(i)+del;
					$(del_sig).attr("name",del_act);
					$(del_sig).attr("id",del_act);
			}
			$("#row"+i).val(i);	
		}						
	});
	
	$("img.elimina").click(function(){
					
		$(this).parent().parent().parent().fadeOut(0, function () {
			var i=0;
			$(this).remove();
			$("#"+rowCount).trigger("restar");
			$("#"+rowCount).unbind("restar");
			var tope=$("#"+rowCount).val();
			i=parseFloat(id);
			$("#"+rowDel).trigger("cambiar",[i,tope]);
			$("#"+rowDel).unbind("cambiar");								
		});
		recalculaConceptoDetalle(conceptoAEliminar);								
		recalcula();
		// Volvemos a mostrar el mensaje que solicita el concepto de Comidas Representacion como obligatorio.
		$("#mensajeInformativo").slideDown(1000);
        $("#mensajeInformativo").css("display", "block");
        
        // No restablecemos los conceptos si el concepto de Comidas Representación no ha sido eliminado
        if(!buscaConceptoComidasRepresentacion()){
        	restablecerOpciones();
        	restablececamposComidasRepresentacion();
        }
        
		return false;
	});	 	
	
	//regresa el estatus a 0 para edicion
	var idamex = $("#idAmex"+parseInt(id)).val();
	var detalleComp = $("#idDetalleComp"+parseInt(id)).val();
	if (idamex > 0){
		devolver_status_idamex(idamex);
	}		
		
	var no_partida = parseInt($("#comprobacion_table>tbody>tr").length);
	if (no_partida == 1){
		$("#guardarPrev").attr("disabled","true");
		$("#guardarComp").attr("disabled","true");	
		$("#sol_select").removeAttr("disabled");
	}					
}

//genera las filas para cargar el previo
function agregarfila_de_basedatos(datos){
	var frm=document.detallecomp;
	
	// Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
	var id = parseInt($("#comprobacion_table>tbody >tr").length);
	if(isNaN(id)){
		id = 1;
	}else{
		id+= parseInt(1);
	}
	
	var tipoComprobacion_db;
	// Busca tipo de comprobación	
	if (datos.tipo_bd == 1){
		tipoComprobacion_db = "Anticipo";
	}else if(datos.tipo_bd == 2){
		tipoComprobacion_db = "Amex";
	}else if(datos.tipo_bd == 3){
		tipoComprobacion_db = "Reembolso";
	}else if(datos.tipo_bd == 4){
		tipoComprobacion_db = "Amex externo";
	}

	// Se obtiene el concepto de tipo Alimentos: Para seguir con la validación
	var conceptoABuscar=/Alimentos/g;
	var conceptoBD ="";				
	if(conceptoABuscar.test(datos.concepto_db)){
		conceptoBD = "Alimentos";
	}else{
		conceptoBD=datos.concepto_db;
	}				
			
	var aux = "N/A";
	var nuevaFila = '<tr>';

	nuevaFila+="<td>"+"<div id='renglonS"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
	nuevaFila+="<td>"+"<input type='hidden' name='cargo_asociado"+id+"'id='cargo_asociado"+id+"' value='"+tipoComprobacion_db+"'readonly='readonly' />"+tipoComprobacion_db + "<input type='hidden' name='cargo_factura"+id+"'id='cargo_factura"+id+"' value='"+datos.cargo_factura_db+"' readonly='readonly' />" +"<input type='hidden' name='tipoComprobacion"+id+"'id='tipoComprobacion"+id+"' value='"+ datos.tipo_bd +"'readonly='readonly' /></td>";
	nuevaFila+="<td>" + "<input type='hidden' name='no_transaccion"+id+"'id='no_transaccion"+id+"' value='"+ datos.transaccion_bd +"'readonly='readonly' />"+ datos.transaccion_bd +"</td>";
	nuevaFila+="<td>"+"<input type='hidden' name='fecha"+id+"' id='fecha"+id+"' value='"+datos.fecha_db+"' readonly='readonly' />"+datos.fecha_db+"</td>";
	nuevaFila+="<td>"+"<input type='hidden' name='tipo_comida"+id+"' id='tipo_comida"+id+"' value='"+datos.tipoComida_db+"' 	readonly='readonly' />"+"<input type='hidden' name='concepto"+id+"' id='concepto"+id+"' value='"+conceptoBD+"' 	readonly='readonly' />"+datos.concepto_db+"</td>";
	nuevaFila+="<td>"+"<input type='hidden' name='comentario"+id+"' id='comentario"+id+"' value='"+datos.comentario_db+"' readonly='readonly' />"+datos.comentario_db+"</td>"; 
	nuevaFila+="<td>"+"<input type='hidden' name='no_asistentes"+id+"' id='no_asistentes"+id+"' value='"+datos.asistentes_db+"' readonly='readonly' />"+datos.asistentes_db+"</td>";
	nuevaFila+="<td>"+"<input type='hidden' name='rfc"+id+"' id='rfc"+id+"' value='"+datos.rfc_db+"' readonly='readonly' />"+datos.rfc_db+"</td>";
	nuevaFila+="<td>"+"<input type='hidden' name='proveedor"+id+"' id='proveedor"+id+"' value='"+datos.proveedor_db+"' readonly='readonly' />"+datos.proveedor_db+"</td>";
	nuevaFila+="<td>"+"<input type='hidden' name='flag_factura"+id+"' id='flag_factura"+id+"' value='"+datos.factura_db+"' readonly='readonly' />"+datos.factura_db+"</td>";
	nuevaFila+="<td id='m'>"+"<input type='hidden' name='mnt"+id+"' id='mnt"+id+"' value='"+datos.monto_db+"' readonly='readonly' />"+datos.monto_db+"</td>"; 
	nuevaFila+="<td>"+"<input type='hidden' name='pimpuesto"+id+"' id='pimpuesto"+id+"' value='"+datos.iva_db+"' readonly='readonly' />"+datos.iva_db+"</td>"; 
	nuevaFila+="<td>"+"<input type='hidden' name='propina"+id+"' id='propina"+id+"' value='"+datos.propinas_db+"' readonly='readonly' />"+datos.propinas_db+"</td>"; 					
	nuevaFila+="<td><input type='hidden' name='impuesto_hospedaje"+id+"' id='impuesto_hospedaje"+id+"' value='"+datos.impHos_db+"' readonly='readonly' />"+datos.impHos_db+"</td>"; 
	nuevaFila+="<td>"+"<input type='hidden' name='tot"+id+"' id='tot"+id+"' value='"+datos.total_db+"' readonly='readonly' />"+datos.total_db+"<input type='hidden' name='mensaje"+id+"' id='mensaje"+id+"' value='0' readonly='readonly' />"+"<input type='hidden' name='diferencia"+id+"' id='diferencia"+id+"' value='0' readonly='readonly' /></td>";
	nuevaFila+="<td>"+"<input type='hidden' name='divisa"+id+"' id='divisa"+id+"' value='"+datos.divisaTipo_db+"' readonly='readonly' />"+"<input type='hidden' name='tipoDivisa"+id+"' id='tipoDivisa"+id+"' value='"+datos.divisa_db+"' readonly='readonly' />"+datos.divisaTipo_db+"</td>";  
	nuevaFila+="<td>"+"<input type='hidden' name='totalMxn"+id+"' id='totalMxn"+id+"' value='"+datos.totalPesos_db.replace(",","")+"' readonly='readonly' />"+datos.totalPesos_db+"</td>";  
	nuevaFila+="<td>"+"<div align='center'><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Comprobacion' name='"+id+"edit' id='"+id+"edit' onclick='editarComprobacion(this.id);' style='cursor:pointer'/></div><div align='center'>Editar Partida</div></td>";
	nuevaFila+="<td>"+"<div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarRenglonComprobacion(this.id,\"comprobacion_table\",\"rowCount\",\"rowDel\",\"renglonS\",\"edit\",\"del\",\"itinerarioActualOPosible\");' onclick='conceptoElimina(this.id); reiniciaComentarioObligatorio();' style='cursor:pointer;' /></div><div class='del_part' id='del_part"+id+"' align='center'>Eliminar Partida</div></td>";
	nuevaFila+= "<input type='hidden' id='idAmex"+id+"' name='idAmex"+id+"' value='"+datos.idamex_db+"' readonly='readonly'/>"+"<input type='hidden' id='totalDolares"+id+"' name='totalDolares"+id+"' value='"+datos.totalUsd_db+"' readonly='readonly'/>"+"<input type='hidden' id='tipoComida"+id+"' name='tipoComida"+id+"' value='"+ datos.tipoComida_db +"' readonly='readonly'/>";
	nuevaFila+= "<input type='hidden' id='numConcepto"+id+"' name='numConcepto"+id+"' value='"+datos.conceptoId_db+"' readonly='readonly'/><input type='hidden' id='check"+id+"' name='check"+id+"' value='"+datos.tipoProveedor_db+"' readonly='readonly'/>";
	nuevaFila+= "<input type='hidden' id='tipoTarjeta"+id+"' name='tipoTarjeta"+id+"' value='"+datos.tipoTarjeta_db+"' readonly='readonly'/>"+"<input type='hidden' id='listaCargo"+id+"' name='listaCargo"+id+"' value='"+datos.idamex_db+"' readonly='readonly'/>";
	nuevaFila+= "<input type='hidden' id='exConcepto"+id+"' name='exConcepto"+id+"' value='' readonly='readonly'/>";
	nuevaFila+= "<input type='hidden' id='difConcepto"+id+"' name='difConcepto"+id+"' value='0' readonly='readonly'/>";
	nuevaFila+= "<input type='hidden' id='idDetalleComp"+id+"' name='idDetalleComp"+id+"' value='"+datos.detalleID+"' readonly='readonly'/>";
	nuevaFila += '</tr>';
	$("#comprobacion_table").append(nuevaFila);
}

function excepcionEdit(id_tramiteEdicion){			
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "idTramiteExc="+id_tramiteEdicion,
		dataType: "json",
		timeout: 10000,
		success: function(json){						
			partidas = json.length;
			 for(var i = 0; i < partidas; i++){							
				 $('#exConcepto'+(i+1)).val(json[i].ex_mensaje);
				 $('#difConcepto'+(i+1)).val(json[i].dif_exc);							
			 }
		},
		complete: function (json){
			$.unblockUI();
			recalcula();
		},
		error: function(x, t, m){
			if(t==="timeout"){
				excepcionEdit(id_tramiteEdicion);
			}
		 }
	});				
}

// Función para CArgar el CECO guardado en el Previo de la Comprobación
function cargaINFOtramite(id_tramite){
	var cecoSeleccionado = 0;
	var observaciones = "";
	var lugarRestaurante = "";
	var ciudad = "";
	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion_gastos.php",
		data: "idTramiteINFO="+id_tramite,
		dataType: "json",
		timeout: 10000,
		success: function(json){
			cecoSeleccionado = json[0].ceco;
			observaciones = json[0].observaciones;
			lugarRestaurante = json[0].lugar_restaurante;
			ciudad = json[0].ciudad;
		},
		complete: function (json){
			$("#ccentro_costos").val(cecoSeleccionado);
			$("#observ").val(observaciones);
			
			// Datos de Comidas de Representacion
			$("#lugar").val(lugarRestaurante);
			$("#ciudad").val(ciudad);
			
			recalcula();
			
			// Habilitar los botones de guardado
			$("#registrar_comp").removeAttr("disabled");
			$("#guardarPrev").removeAttr("disabled");
			$("#guardarComp").removeAttr("disabled");
			$.unblockUI();
		},
		error: function(x, t, m){
			if(t==="timeout"){
				cargaINFOtramite(id_tramite);
			}
		 }
	});				
}

// Función para cargar los datos del tramite dado en el formulario de nueva comprobación de gastos.
function fillform(id_tramite){
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion_gastos.php",
		data: "idTramiteComprobacionGastos="+id_tramite,
		dataType: "json",
		timeout: 10000,
		success: function(json){
			$("#sol_select").attr("disabled","disbaled");
			
			var no_partidas=0; 
			if(json != null){
				no_partidas = json.length;
				for(var i=0; i < json.length; i++){
					agregarfila_de_basedatos(json[i]);																
				}
				$("#rowCount").val(no_partidas);							
				$("#rowCountCecos").val(no_partidas);		
			}
		},
		complete:function(json){
			// Extraer excepciones de detalle
			//excepcionEdit(id_tramite); // Función para cargar excepciones
			//obtenerTramite(); // La Comprobación no procede de una Solicitud de Viaje
			// Cargar el CEO Selecionado en el guardado del previo
			cargaINFOtramite(id_tramite);
			verificarEstatusAmex(id_tramite);
			
			// Ocultar mensaje de alerta
			if(buscaConceptoComidasRepresentacion()){
				// Eliminar el concepto de Comidas Representación, para evitar que el usuario intente ingresar dos veces el concepto
				$("#select_concepto option[value="+valorComidasRepresentacion+"]").remove();
				
				$("#mensajeInformativo").slideUp(1000);
		        $("#mensajeInformativo").css("display", "none");
		        cargarInvitadosTramite(1);
			}
		},
		error: function(x, t, m){
			if(t==="timeout") {
				location.reload();
				abort();
			} 
		 }
	});
}

// Cargar los datos necesarios de la solicitud
function cargaSolicitud(){
	var tramiteSolicitud = $("#sol_select option:selected").val();
	var requiereAnticipo = 0;
	var anticipoSolicitado = parseFloat(0);
	var existeSolicitud = 0;
	var conceptoSolicitud = 0;
	var lugar = "";
	var ciudad = "";
	var centrocostos = 0;
	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion_gastos.php",
		data: "idTramiteSolicitudGastos="+tramiteSolicitud,
		dataType: "json",
		timeout: 10000,
		async: false,
		success: function(json){
			anticipoSolicitado = json[0].anticipoSolicitado;
			existeSolicitud = json[0].exixteSolicitud;
			conceptoSolicitud = json[0].conceptoSolicitud;
			lugar = json[0].lugarComida;
			ciudad = json[0].ciudadComida;
			requiereAnticipo = json[0].requiereanticipo;
			centrocostos = json[0].ceco;
			// Eliminamos las opciones de Anticipo para evitar se dupliquen
			$("#tipo option[value=1]").remove();
		},complete:function(json){
			// Agregar la opción de Anticipo si en la solicitud si solicito Anticipo
			if(requiereAnticipo == 1){
				//Se agregara la opcion "Anticipo" a la lista de Tipo de Comprobación
				$("#tipo").append('<option value="1">Anticipo</option>');
				// Mostrar el total de anticipo Solicitado
				$("#anticipo_solicitado1").html(number_format(anticipoSolicitado, 2, ".", ",")+" MXN");
				$("#anticipo_solicitado2").val(number_format(anticipoSolicitado, 2, ".", ","));
			}else{
				// Si no se comprueba una solicitud, se deberá quitar la opción de Anticipo
				$("#tipo option[value=1]").remove();
				// Inicializar Anticipo
				$("#anticipo_solicitado1").html("0.00 MXN");
				$("#anticipo_solicitado2").val("0.00");
			}
			
			// Mostrar liga hacia la pantalla de consulta de la solicitud
			if(existeSolicitud){
				$("#consulta_solicitud").html('<a href="../solicitudes/reporte_solicitud_gastos.php?&id='+tramiteSolicitud+'" target="_blank"> <img border="0" title="Consultar" src="../../images/btn-search.gif"> Consulta Solicitud </a>&nbsp;&nbsp;&nbsp;&nbsp;');
				$("#consulta_solicitud").slideDown(1000);
		        $("#consulta_solicitud").css("display", "block");
		        // Cargar el CECO de la Solicitud
		        $("#ccentro_costos").val(centrocostos);
			}else{
				$("#consulta_solicitud").slideUp(1000);
		        $("#consulta_solicitud").css("display", "none");
		        $("#consulta_solicitud").html("");
		        // Cargar el CECO de la Solicitud
		        $("#ccentro_costos").val(centrocostos);
			}
			
			// Mostrar Alerta si la Solicitud elegida tiene el concepto de Comidas de Representación
			if(conceptoSolicitud == valorComidasRepresentacion){
				$("#comidasRepresentacion").val(1);
				$("#mensajeInformativo").html("<strong>El concepto de Comidas de Representac&oacute;n ha sido utilizado en la Solicitud de Gastos, <br />favor de comprobar dicho concepto.</strong>");
				$("#mensajeInformativo").slideDown(1000);
		        $("#mensajeInformativo").css("display", "block");
		        $("#lugar").val(lugar);
		        $("#ciudad").val(ciudad);
			}else{
				$("#comidasRepresentacion").val(0);
				$("#mensajeInformativo").slideUp(1000);
		        $("#mensajeInformativo").css("display", "none");
		        $("#mensajeInformativo").html("");
		        $("#lugar").val("");
		        $("#ciudad").val("");
			}
				
		},
		error: function(x, t, m){
			if(t==="timeout") {
				cargaSolicitud();
			} 
		 }
	});
}
