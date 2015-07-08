 function deshabilitaBotones(){
	//desactivar / Activar para cotizacion de auto
	 if($("#empresaAuto").val() != ""){
		  $('#aceptarAuto').removeAttr("disabled");
		 $('#cancelarAuto').removeAttr("disabled");
		}else{
			$("#aceptarAuto").attr("disabled", "disabled");
			$("#cancelarAuto").attr("disabled", "disabled");
						
		}
 }
 
 
function deshabilita_aceptar(){
	var frm = document.detallesItinerarios;
	var no_de_itinerario = frm.itinerarioActualOPosible.value;
	var no_de_hoteles = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	if(no_de_hoteles == 0){		
		// Desactivar botones
		$("#aceptarHotel").attr("disabled", "disabled");
		$("#cancelarHotel").attr("disabled", "disabled");
		if(($("#empresaAuto").val() != "") && ($("#diasRenta").val() != "" && $("#diasRenta").val() != 0)){
			 if(($("#costoDia").val() != "" || $("#costoDia").val() != 0.00)){
				 $('#aceptarAgencia').removeAttr("disabled");
			 }else{
				 $('#aceptarAgencia').attr("disabled", "disabled"); 
			 }
		}else{
			$('#aceptarAgencia').attr("disabled", "disabled");
		}		
		
	}else if(no_de_hoteles >= 1){
		 $('#aceptarAgencia').removeAttr("disabled");
	}
}
//Funcion encargada de eliminar la cotizacion de hotel de manera adecuada.
function validarItinerario(id,no_de_itinerario,noItinerarioSet){
	//alert("Valor de itinerario de ventana"+no_de_itinerario);
	//alert("valor del itinerario seleccionado"+noItinerarioSet);
	
	//Funciones que permitiran recorrer los indices de las cotizaciones de hotel realizadas
	 $("#rowCount_hotel"+no_de_itinerario).bind("restar",function(e,data,data1){
	    	e.stopImmediatePropagation();
				$("#rowCount_hotel"+no_de_itinerario).val(parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length));
	        });
	    $("#rowDel_hotel"+no_de_itinerario).bind("cambiar",function(e,inicio,tope){
	        e.stopImmediatePropagation();
			var nextno = "";
			var no = "";
			var jqueryno = "";
			var nextrow = "";
			var row = "";
			var jqueryrow = "";
			var nextciudad = "";
			var ciudad = "";
			var jqueryciudad ="";
			var nexthotel = "";
			var hotel = "";
			var jqueryhotel ="";
			var nextcomentario = "";
			var comentario = "";
			var jquerycomentario ="";
			var nextnoches = "";
			var noches = "";
			var jquerynoches ="";
			var nextllegada = "";
			var llegada = "";
			var jqueryllegada ="";
			var nextsalida = "";
			var salida = "";
			var jquerysalida ="";
			var nextnoreservacion = "";
			var noreservacion = "";
			var jquerynoreservacion ="";
			var nextcostoNoche = "";
			var costoNoche = "";
			var jquerycostoNoche ="";
			var nextiva = "";
			var iva = "";
			var jqueryiva ="";
			var nexttotal = "";
			var total = "";
			var jquerytotal ="";
			var nextselecttipodivisa = "";
			var selecttipodivisa = "";
			var jqueryselecttipodivisa ="";
			var nextmontoP = "";
			var montoP = "";
			var jquerymontoP ="";
			var nextdel = "";
			var del = "";
			var jquerydel ="";
			var nextsubtotal = "";
			var subtotal = "";
			var jquerysubtotal ="";
			var nextdeldiv = "";
			var deldiv = "";
			var jquerydeldiv ="";
			
	/*		*/
			
			for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){
				nextno="#no_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				no="no_"+no_de_itinerario+"_" + parseInt(i);
				jqueryno="#no_"+no_de_itinerario+"_"+parseInt(i);

				nextrow="#row_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				row="row_"+no_de_itinerario+"_" + parseInt(i);
				jqueryrow="#row_"+no_de_itinerario+"_"+parseInt(i);
				
				nextciudad="#ciudad_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				ciudad="ciudad_"+no_de_itinerario+"_" + parseInt(i);
				jqueryciudad="#ciudad_"+no_de_itinerario+"_"+parseInt(i);
				nexthotel="#hotel_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				hotel="hotel_"+no_de_itinerario+"_" + parseInt(i);
				jqueryhotel="#hotel_"+no_de_itinerario+"_"+parseInt(i);
				nextcomentario="#comentario_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				comentario="comentario_"+no_de_itinerario+"_" + parseInt(i);
				jquerycomentario="#comentario_"+no_de_itinerario+"_"+parseInt(i);
				nextnoches="#noches_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				noches="noches_"+no_de_itinerario+"_" + parseInt(i);
				jquerynoches="#noches_"+no_de_itinerario+"_"+parseInt(i);
				nextllegada="#llegada_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				llegada="llegada_"+no_de_itinerario+"_" + parseInt(i);
				jqueryllegada="#llegada_"+no_de_itinerario+"_"+parseInt(i);
				nextsalida="#salida_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				salida="salida_"+no_de_itinerario+"_" + parseInt(i);
				jquerysalida="#salida_"+no_de_itinerario+"_"+parseInt(i);
				nextnoreservacion="#noreservacion_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				noreservacion="noreservacion_"+no_de_itinerario+"_" + parseInt(i);
				jquerynoreservacion="#noreservacion_"+no_de_itinerario+"_"+parseInt(i);
				nextcostoNoche="#costoNoche_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				costoNoche="costoNoche_"+no_de_itinerario+"_" + parseInt(i);
				jquerycostoNoche="#costoNoche_"+no_de_itinerario+"_"+parseInt(i);
				nextiva="#iva_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				iva="iva_"+no_de_itinerario+"_" + parseInt(i);
				jqueryiva="#iva_"+no_de_itinerario+"_"+parseInt(i);
				nexttotal="#total_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				total="total_"+no_de_itinerario+"_" + parseInt(i);
				jquerytotal="#total_"+no_de_itinerario+"_"+parseInt(i);
				nextselecttipodivisa="#selecttipodivisa_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				selecttipodivisa="selecttipodivisa_"+no_de_itinerario+"_" + parseInt(i);
				jqueryselecttipodivisa="#selecttipodivisa_"+no_de_itinerario+"_"+parseInt(i);
				nextmontoP="#montoP_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				montoP="montoP_"+no_de_itinerario+"_" + parseInt(i);
				jquerymontoP="#montoP_"+no_de_itinerario+"_"+parseInt(i);
				nextsubtotal="#subtotal_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				subtotal="subtotal_"+no_de_itinerario+"_" + parseInt(i);
				jquerysubtotal="#subtotal_"+no_de_itinerario+"_"+parseInt(i);
				
				nextdeldiv="#delDiv_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
				deldiv="delDiv_"+no_de_itinerario+"_" + parseInt(i);
				jquerydeldiv="#delDiv_"+no_de_itinerario+"_"+parseInt(i);

				del=parseInt(i)+"delHotel";
				jquerydel="#"+parseInt(i)+"delHotel";
				nextdel="#"+((parseInt(i)+(1)))+"delHotel";

				$(nextno).attr("id",no);
				$(jqueryno).attr("name",no);
				$(jqueryno).html(parseInt(i));
				
				$(nextrow).attr("id",row);
				$(jqueryrow).attr("name",row);
				
				$(nextciudad).attr("id",ciudad);
				$(jqueryciudad).attr("name",ciudad);
				$(nexthotel).attr("id",hotel);
				$(jqueryhotel).attr("name",hotel);
				$(nextcomentario).attr("id",comentario);
				$(jquerycomentario).attr("name",comentario);
				$(nextnoches).attr("id",noches);
				$(jquerynoches).attr("name",noches);
				$(nextllegada).attr("id",llegada);
				$(jqueryllegada).attr("name",llegada);
				$(nextsalida).attr("id",salida);
				$(jquerysalida).attr("name",salida);
				$(nextnoreservacion).attr("id",noreservacion);
				$(jquerynoreservacion).attr("name",noreservacion);
				$(nextcostoNoche).attr("id",costoNoche);
				$(jquerycostoNoche).attr("name",costoNoche);
				$(nextiva).attr("id",iva);
				$(jqueryiva).attr("name",iva);
				$(nexttotal).attr("id",total);
				$(jquerytotal).attr("name",total);
				$(nextselecttipodivisa).attr("id",selecttipodivisa);
				$(jqueryselecttipodivisa).attr("name",selecttipodivisa);
				$(nextmontoP).attr("id",montoP);
				$(jquerymontoP).attr("name",montoP);
				$(nextsubtotal).attr("id",subtotal);
				$(jquerysubtotal).attr("name",subtotal);
				$(nextdel).attr("id",del);
				$(jquerydel).attr("name",del);
				$(nextdeldiv).attr("id",deldiv);
				$(jquerydeldiv).attr("name",deldiv);
			}
	    });
	    //END de funciones.	    	
			//alert("elimna click");
			//$(this).parent().parent().parent().fadeOut("normal", function () {
			//$(this).fadeOut("normal", function () {
			$("#hotel_table"+no_de_itinerario+">tbody").find("tr").eq((parseFloat(id))-1).fadeOut("normal", function () {					
				var i=0;			
				$(this).remove();	
				$("#rowCount_hotel"+no_de_itinerario).trigger("restar");
				$("#rowCount_hotel"+no_de_itinerario).unbind("restar");
					 var tope=$("#rowCount_hotel"+no_de_itinerario).val();
					 i=parseFloat(id);
				$("#rowDel_hotel"+no_de_itinerario).trigger("cambiar",[i,tope]);
				$("#rowDel_hotel"+no_de_itinerario).unbind("cambiar");
				calculaTotalHospedaje();
				deshabilita_aceptar();				
	        });
}
// Elimina un hotel
function borrarHotel(id,no_de_itinerario,bandera){
    $("#rowCount_hotel"+no_de_itinerario).bind("restar",function(e,data,data1){
    	e.stopImmediatePropagation();
			$("#rowCount_hotel"+no_de_itinerario).val(parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length));
        });
    $("#rowDel_hotel"+no_de_itinerario).bind("cambiar",function(e,inicio,tope){
        e.stopImmediatePropagation();
		var nextno = "";
		var no = "";
		var jqueryno = "";
		var nextrow = "";
		var row = "";
		var jqueryrow = "";
		var nextciudad = "";
		var ciudad = "";
		var jqueryciudad ="";
		var nexthotel = "";
		var hotel = "";
		var jqueryhotel ="";
		var nextcomentario = "";
		var comentario = "";
		var jquerycomentario ="";
		var nextnoches = "";
		var noches = "";
		var jquerynoches ="";
		var nextllegada = "";
		var llegada = "";
		var jqueryllegada ="";
		var nextsalida = "";
		var salida = "";
		var jquerysalida ="";
		var nextnoreservacion = "";
		var noreservacion = "";
		var jquerynoreservacion ="";
		var nextcostoNoche = "";
		var costoNoche = "";
		var jquerycostoNoche ="";
		var nextiva = "";
		var iva = "";
		var jqueryiva ="";
		var nexttotal = "";
		var total = "";
		var jquerytotal ="";
		var nextselecttipodivisa = "";
		var selecttipodivisa = "";
		var jqueryselecttipodivisa ="";
		var nextmontoP = "";
		var montoP = "";
		var jquerymontoP ="";
		var nextdel = "";
		var del = "";
		var jquerydel ="";
		var nextsubtotal = "";
		var subtotal = "";
		var jquerysubtotal ="";
		var nextdeldiv = "";
		var deldiv = "";
		var jquerydeldiv ="";
		
/*		*/
		
		for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){
			nextno="#no_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			no="no_"+no_de_itinerario+"_" + parseInt(i);
			jqueryno="#no_"+no_de_itinerario+"_"+parseInt(i);

			nextrow="#row_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			row="row_"+no_de_itinerario+"_" + parseInt(i);
			jqueryrow="#row_"+no_de_itinerario+"_"+parseInt(i);
			
			nextciudad="#ciudad_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			ciudad="ciudad_"+no_de_itinerario+"_" + parseInt(i);
			jqueryciudad="#ciudad_"+no_de_itinerario+"_"+parseInt(i);
			nexthotel="#hotel_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			hotel="hotel_"+no_de_itinerario+"_" + parseInt(i);
			jqueryhotel="#hotel_"+no_de_itinerario+"_"+parseInt(i);
			nextcomentario="#comentario_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			comentario="comentario_"+no_de_itinerario+"_" + parseInt(i);
			jquerycomentario="#comentario_"+no_de_itinerario+"_"+parseInt(i);
			nextnoches="#noches_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			noches="noches_"+no_de_itinerario+"_" + parseInt(i);
			jquerynoches="#noches_"+no_de_itinerario+"_"+parseInt(i);
			nextllegada="#llegada_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			llegada="llegada_"+no_de_itinerario+"_" + parseInt(i);
			jqueryllegada="#llegada_"+no_de_itinerario+"_"+parseInt(i);
			nextsalida="#salida_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			salida="salida_"+no_de_itinerario+"_" + parseInt(i);
			jquerysalida="#salida_"+no_de_itinerario+"_"+parseInt(i);
			nextnoreservacion="#noreservacion_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			noreservacion="noreservacion_"+no_de_itinerario+"_" + parseInt(i);
			jquerynoreservacion="#noreservacion_"+no_de_itinerario+"_"+parseInt(i);
			nextcostoNoche="#costoNoche_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			costoNoche="costoNoche_"+no_de_itinerario+"_" + parseInt(i);
			jquerycostoNoche="#costoNoche_"+no_de_itinerario+"_"+parseInt(i);
			nextiva="#iva_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			iva="iva_"+no_de_itinerario+"_" + parseInt(i);
			jqueryiva="#iva_"+no_de_itinerario+"_"+parseInt(i);
			nexttotal="#total_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			total="total_"+no_de_itinerario+"_" + parseInt(i);
			jquerytotal="#total_"+no_de_itinerario+"_"+parseInt(i);
			nextselecttipodivisa="#selecttipodivisa_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			selecttipodivisa="selecttipodivisa_"+no_de_itinerario+"_" + parseInt(i);
			jqueryselecttipodivisa="#selecttipodivisa_"+no_de_itinerario+"_"+parseInt(i);
			nextmontoP="#montoP_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			montoP="montoP_"+no_de_itinerario+"_" + parseInt(i);
			jquerymontoP="#montoP_"+no_de_itinerario+"_"+parseInt(i);
			nextsubtotal="#subtotal_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			subtotal="subtotal_"+no_de_itinerario+"_" + parseInt(i);
			jquerysubtotal="#subtotal_"+no_de_itinerario+"_"+parseInt(i);
			
			nextdeldiv="#delDiv_"+no_de_itinerario+"_"+((parseInt(i)+(1)));
			deldiv="delDiv_"+no_de_itinerario+"_" + parseInt(i);
			jquerydeldiv="#delDiv_"+no_de_itinerario+"_"+parseInt(i);

			del=parseInt(i)+"delHotel";
			jquerydel="#"+parseInt(i)+"delHotel";
			nextdel="#"+((parseInt(i)+(1)))+"delHotel";

			$(nextno).attr("id",no);
			$(jqueryno).attr("name",no);
			$(jqueryno).html(parseInt(i));
			
			$(nextrow).attr("id",row);
			$(jqueryrow).attr("name",row);
			
			$(nextciudad).attr("id",ciudad);
			$(jqueryciudad).attr("name",ciudad);
			$(nexthotel).attr("id",hotel);
			$(jqueryhotel).attr("name",hotel);
			$(nextcomentario).attr("id",comentario);
			$(jquerycomentario).attr("name",comentario);
			$(nextnoches).attr("id",noches);
			$(jquerynoches).attr("name",noches);
			$(nextllegada).attr("id",llegada);
			$(jqueryllegada).attr("name",llegada);
			$(nextsalida).attr("id",salida);
			$(jquerysalida).attr("name",salida);
			$(nextnoreservacion).attr("id",noreservacion);
			$(jquerynoreservacion).attr("name",noreservacion);
			$(nextcostoNoche).attr("id",costoNoche);
			$(jquerycostoNoche).attr("name",costoNoche);
			$(nextiva).attr("id",iva);
			$(jqueryiva).attr("name",iva);
			$(nexttotal).attr("id",total);
			$(jquerytotal).attr("name",total);
			$(nextselecttipodivisa).attr("id",selecttipodivisa);
			$(jqueryselecttipodivisa).attr("name",selecttipodivisa);
			$(nextmontoP).attr("id",montoP);
			$(jquerymontoP).attr("name",montoP);
			$(nextsubtotal).attr("id",subtotal);
			$(jquerysubtotal).attr("name",subtotal);
			$(nextdel).attr("id",del);
			$(jquerydel).attr("name",del);
			$(nextdeldiv).attr("id",deldiv);
			$(jquerydeldiv).attr("name",deldiv);
		}
    });
    
    $("img.elimina").click(function(){
    	//alert("numero de itinerario"+no_de_itinerario);
    	//alert("itinerario en uestion"+itinerarioActual);		
		//alert("elimna click");
		//$(this).parent().parent().parent().fadeOut("normal", function () {
		//$(this).fadeOut("normal", function () {
		$("#hotel_table"+no_de_itinerario+">tbody").find("tr").eq((parseFloat(id))-1).fadeOut("normal", function () {			
			//alert("numero de itinerario"+no_de_itinerario);
			var i=0;			
			$(this).remove();	
			$("#rowCount_hotel"+no_de_itinerario).trigger("restar");
			$("#rowCount_hotel"+no_de_itinerario).unbind("restar");
				 var tope=$("#rowCount_hotel"+no_de_itinerario).val();
				 i=parseFloat(id);
			$("#rowDel_hotel"+no_de_itinerario).trigger("cambiar",[i,tope]);
			$("#rowDel_hotel"+no_de_itinerario).unbind("cambiar");
			calculaTotalHospedaje();			
			deshabilita_aceptar();
			if(bandera == 1)
				cargar_hoteles_itinerarios();
        });
		
		return false;    
    });
}
// Elimina un itinerario
function borrarRenglon(id,tabla,rowCount,rowDel,count,edit,del,rowActualOPosible){
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
		
		for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){
			fila_act = tab.getElementsByTagName('tr')[i];
			for (j=0; celda_act = fila_act.getElementsByTagName('input')[j]; j++){
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
		}
    });
    
    $("img.elimina").click(function(){
		$(this).parent().parent().parent().fadeOut("normal", function () {
			var i=0;
			$(this).remove();
			$("#"+rowCount).trigger("restar");
			$("#"+rowCount).unbind("restar");
				 var tope=$("#"+rowCount).val();
				 i=parseFloat(id);
			$("#"+rowDel).trigger("cambiar",[i,tope]);
			$("#"+rowDel).unbind("cambiar");
        });
		return false;
    });
}

// Elimina un renglon con 1 indice sin eventos
function borrarRenglon4(id,tabla,rowCount,rowDel,count,edit,del,rowActualOPosible){
	var i=0;
	$("#"+tabla+">tbody").find("tr").eq((parseFloat(id))-1).remove();
	$("#"+rowCount).val(parseInt($("#"+tabla+">tbody>tr").length));
	if(rowActualOPosible != ""){
		$("#"+rowActualOPosible).val(parseInt($("#"+tabla+">tbody>tr").length)+parseInt(1));
	}
	var tope=$("#"+rowCount).val();
	i=parseFloat(id);
	var inicio = i;
	
	tab = document.getElementById(tabla);
	for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){
		fila_act = tab.getElementsByTagName('tr')[i];
		for (j=0; celda_act = fila_act.getElementsByTagName('input')[j]; j++){
			if(celda_act.getAttribute('type') == 'hidden' || celda_act.getAttribute('type') == 'text'){
				var aux = celda_act.getAttribute('id');
				var re = /[0-9]*$/;
				aux = aux.replace(re, "");
				
				var elemento_sig = "#"+aux+((parseInt(i)+(1)));
				var elemento_act =  ""+aux+parseInt(i);
				$(elemento_sig).attr("name",elemento_act);
				$(elemento_sig).attr("id",elemento_act);
			}
		}
		for (j=0; celda_act = fila_act.getElementsByTagName('select')[j]; j++){
			var aux = celda_act.getAttribute('id');
			var re = /[0-9]*$/;
			aux = aux.replace(re, "");
			
			var elemento_sig = "#"+aux+((parseInt(i)+(1)));
			var elemento_act =  ""+aux+parseInt(i);
			$(elemento_sig).attr("name",elemento_act);
			$(elemento_sig).attr("id",elemento_act);
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
	}
	return false;
}

function borrarRenglon3(id,tabla,rowCount,rowDel,count,edit,del,rowActualOPosible){
	$("#"+rowCount).bind("restar",function(e,data,data1){
    	e.stopImmediatePropagation();
			$("#"+rowCount).val(parseInt($("#"+tabla+">tbody>tr").length));
			if(rowActualOPosible != ""){
				$("#"+rowActualOPosible).val(parseInt($("#"+tabla+">tbody>tr").length)+parseInt(1));
			}
        });
    $("#"+rowDel).bind("cambiar",function(e,inicio,tope){
        e.stopImmediatePropagation();
		
		var num = /\d+$/;
		var k = tabla.match(num);
		tab = document.getElementById(tabla);
		for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){
			fila_act = tab.getElementsByTagName('tr')[i];
			for (j=0; celda_act = fila_act.getElementsByTagName('input')[j]; j++){
				if(celda_act.getAttribute('type') == 'hidden'){
					var aux = celda_act.getAttribute('id');
					var re = /(_[0-9]*){2}$/;
					//alert("aux = "+aux);
					aux = aux.replace(re, "");
					//alert("aux = "+aux);
					
					var elemento_sig = "#"+aux+"_"+parseInt(k)+"_"+(parseInt(i)+(1));
					var elemento_act =  ""+aux+"_"+parseInt(k)+"_"+parseInt(i);
					$(elemento_sig).attr("name",elemento_act);
					$(elemento_sig).attr("id",elemento_act);
				}
			}
			
			//Recorre el div contador de registro
			if(count != ""){
				count_sig = "#"+count+"_"+parseInt(k)+"_"+((parseInt(i)+(1)));
				count_act = ""+count+"_"+parseInt(k)+"_"+parseInt(i);
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
				del_sig = "#"+(parseInt(i)+(1))+"_"+parseInt(k)+"_"+del;
				del_act = ""+parseInt(i)+"_"+parseInt(k)+"_"+del;
					$(del_sig).attr("name",del_act);
					$(del_sig).attr("id",del_act);
			}
		}
    });
    
    //$("img.elimina").click(function(){
		//$(this).parent().parent().parent().fadeOut("normal", function () {
		$("#"+tabla+">tbody").find("tr").eq((parseFloat(id))-1).fadeOut("normal", function () {
			var i=0;
			$(this).remove();
			$("#"+rowCount).trigger("restar");
			$("#"+rowCount).unbind("restar");
				 var tope=$("#"+rowCount).val();
				 i=parseFloat(id);
			$("#"+rowDel).trigger("cambiar",[i,tope]);
			$("#"+rowDel).unbind("cambiar");
        });
		return false;
    //});
}

function borrarRenglon2(id,tabla,rowCount,rowDel,count,edit,del,rowActualOPosible){
	var i=0;
	$("#"+tabla+">tbody").find("tr").eq((parseFloat(id))-1).remove();
	$("#"+rowCount).val(parseInt($("#"+tabla+">tbody>tr").length));
	if(rowActualOPosible != ""){
		$("#"+rowActualOPosible).val(parseInt($("#"+tabla+">tbody>tr").length)+parseInt(1));
	}
	var tope=$("#"+rowCount).val();
	i=parseFloat(id);
	var num = /\d+$/;
	var k = tabla.match(num);
	tab = document.getElementById(tabla);
	for (var i=parseFloat(i);i<=parseFloat(tope);i++){
		fila_act = tab.getElementsByTagName('tr')[i];
		for (j=0; celda_act = fila_act.getElementsByTagName('input')[j]; j++){
			//if(celda_act.getAttribute('type') == 'hidden'){
				var aux = celda_act.getAttribute('id');
				var re = /(_[0-9]*){2}$/;
				//alert("aux = "+aux);
				aux = aux.replace(re, "");
				//alert("aux = "+aux);
				
				var elemento_sig = "#"+aux+"_"+parseInt(k)+"_"+(parseInt(i)+(1));
				var elemento_act =  ""+aux+"_"+parseInt(k)+"_"+parseInt(i);
				$(elemento_sig).attr("name",elemento_act);
				$(elemento_sig).attr("id",elemento_act);
			//}
		}
		
		//Recorre el div contador de registro
		if(count != ""){
			count_sig = "#"+count+"_"+parseInt(k)+"_"+((parseInt(i)+(1)));
			count_act = ""+count+"_"+parseInt(k)+"_"+parseInt(i);
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
			del_sig = "#"+(parseInt(i)+(1))+"_"+parseInt(k)+"_"+del;
			del_act = ""+parseInt(i)+"_"+parseInt(k)+"_"+del;
				$(del_sig).attr("name",del_act);
				$(del_sig).attr("id",del_act);
		}
	}
	return false;
}

// Elimina una tabla las cuales tienen inputs ocultos con id y name con dos indices
// tales como nombre_2_4 (nombre del input)(guion bajo)(primer indice)(guion bajo)(segundo indice)
function borrarTabla(primer_indice,tabla,count,edit,del,counts_div){
	var tope = $("#rowCount").val();
	primer_indice = parseFloat(primer_indice);
	
	for(var k=primer_indice;k<tope;k++){ //Recorre las tablas
		if(document.getElementById(tabla+""+(k+1))){
			//alert("existe "+k+"     -     "+tabla+""+k);
			
			//var tab = document.getElementById(tabla+""+k);
			//var count_trs = tab.getElementsByTagName('tr').length;
			var tab_sig = document.getElementById(tabla+""+(k+1));
			var count_trs_sig = tab_sig.getElementsByTagName('tr').length;
			//alert("count_trs_sig = "+count_trs_sig);
			for (var i=1;i<parseFloat(count_trs_sig);i++){ //Recorre los TR
				fila_act = tab_sig.getElementsByTagName('tr')[i];
				for (var j=0; celda_act = fila_act.getElementsByTagName('input')[j]; j++){
					if(celda_act.getAttribute('type') == 'hidden'){
						var aux = celda_act.getAttribute('id');
						//var re = /[0-9]*$/;
						var re = /(_[0-9]*){2}$/;
						//alert("aux = "+aux);
						aux = aux.replace(re, "");
						//alert("aux = "+aux);
						
						var elemento_sig = "#"+aux+"_"+(parseInt(k)+(1))+"_"+parseInt(i);
						var elemento_act =  ""+aux+"_"+parseInt(k)+"_"+parseInt(i);
						$(elemento_sig).attr("name",elemento_act);
						$(elemento_sig).attr("id",elemento_act);
					}
				}
				//Recorre el div contador de registro
				if(count != ""){
					var count_sig = "#"+count+"_"+(parseInt(k)+(1))+"_"+parseInt(i);
					var count_act = ""+count+"_"+parseInt(k)+"_"+parseInt(i);
						$(count_sig).html(parseInt(i));
						$(count_sig).attr("name",count_act);
						$(count_sig).attr("id",count_act);
				}
				//Recorre la imagen de eliminar registro
				var string_aux ="<img class=\"elimina\" src=\"../../images/delete.gif\" alt=\"Click aqu&iacute; para Eliminar\" name="+i+"delHotel id="+i+"delHotel onmousedown=\"borrarHotel(this.id,"+k+");\" style=\"cursor:pointer;\"/>";
				if(del != ""){
					var count_sig = "#"+del+"_"+(parseInt(k)+(1))+"_"+parseInt(i);
					var count_act = ""+del+"_"+parseInt(k)+"_"+parseInt(i);
						$(count_sig).html(string_aux);
						$(count_sig).attr("name",count_act);
						$(count_sig).attr("id",count_act);
				}
			}
			$("#"+tabla+""+(k+1)).attr("id",tabla+""+k);
			
			//Recorre counts_div
			var div_sig = document.getElementById(counts_div).childNodes[k];
			//alert(div_sig.childNodes[0].getAttribute('id'));
			//alert(div_sig.childNodes[1].getAttribute('id'));
			for (j=0; celda_act = div_sig.getElementsByTagName('input')[j]; j++){
				if(celda_act.getAttribute('type') == 'hidden'){
					var aux = celda_act.getAttribute('id');
					var re = /[0-9]*$/;
					aux = aux.replace(re, "");
					
					var elemento_sig = "#"+aux+((parseInt(k)+(1)));
					var elemento_act =  ""+aux+parseInt(k);
					$(elemento_sig).attr("name",elemento_act);
					$(elemento_sig).attr("id",elemento_act);
				}
			}
			
		}else{
			//alert("no existe "+i+"     -     "+tabla+""+i);
		}
	}
	document.getElementById('area_tabla_div').removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(primer_indice-1)]);
	document.getElementById(counts_div).removeChild(document.getElementById(counts_div).childNodes[parseInt(primer_indice-1)]);
	return false;
}

//Funcion que realiza la eliminacion de la ultima operacion en hoteles-Para creacion de sol. de viaje
function cancelarOperacionHotel(){	
	var frm=document.detallesItinerarios;  
	var no_de_itinerario = $("#no_itinerario").html();
	var no_de_hoteles=parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	//Eliminamos la ultima operacion realizada
	if(banderaCancelaHotel == 1){	
		var eliminaProceso=no_de_hoteles-1;	
		$("#hotel_table"+no_de_itinerario).find("tr:gt("+eliminaProceso+")").remove();
		setRowCount_hotel(no_de_itinerario);
		banderaCancelaHotel=0;				
		reinicioBotonesOperacionHotel(no_de_itinerario);
	}else{			
		cancelar_cotizacion_hospedaje(0);
		reinicioBotonesOperacionHotel(no_de_itinerario);
	}
}

//Reinicio de contador para el ingreso de los hoteles virtuales
function setRowCount_hotel(no_de_itinerario){
	var no_de_hoteles=parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	$("#rowCount_hotel"+no_de_itinerario).val(parseInt(no_de_hoteles));
	//alert("row hotel"+$("#rowCount_hotel"+no_de_itinerario).val());
}

function reinicioBotonesOperacionHotel(no_de_itinerario){
	//verificamos cuantos hoteles hay en la tabla para reiniciar los botones
	var frm=document.detallesItinerarios;
	var no_de_hoteles=parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	
	if(no_de_hoteles == 0){
		limpiarCamposDeHotel();
		frm.enviar_hosp_agencia[0].checked = true;		
		$("#divbotonHotel").css("display", "none");
	}else{
		limpiarCamposDeHotel();
		frm.hospedaje[0].checked = true;
		frm.hospedaje[1].checked = false;
		frm.enviar_hosp_agencia[0].checked = false;
		frm.enviar_hosp_agencia[1].checked = true;	
		$("#divbotonHotel").css("display", "block");		
	}
}

//cancela ultima operacion (Boton cerrar) - Agencia
function cancelaOperacionHotelAgencia(){ 
	
	var no_de_itinerario = $("#no_itinerario").html();
	var no_de_hoteles=parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	//Eliminamos la ultima operacion realizada
	if(banderaCancelaHotel == 1){
		var eliminaProceso=no_de_hoteles-1;	
		$("#hotel_table"+no_de_itinerario).find("tr:gt("+eliminaProceso+")").remove();
		setRowCount_hotel(no_de_itinerario);
		banderaCancelaHotel=0;
		limpiarCamposDeHotel();
	}else{
		setRowCount_hotel(no_de_itinerario);
		limpiarCamposDeHotel();
	}	
}

function setBanderaCancelaHotel(){
	banderaCancelaHotel=0;
}

//Calcula el no. de dias entre dos fechas
function days_between(date_inicial, date_final){
    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24;

    date_inicial_splitted = date_inicial.split("/");
    date_final_splitted = date_final.split("/");    
    date1 = date_inicial_splitted[1]+'/'+date_inicial_splitted[0]+'/'+date_inicial_splitted[2];
    date2 = date_final_splitted[1]+'/'+date_final_splitted[0]+'/'+date_final_splitted[2];

    // Convert both dates to milliseconds
    var date1_ms = Date.parse(date1); //date1.getTime()
    var date2_ms = Date.parse(date2); //date2.getTime()

    // Calculate the difference in milliseconds
    //var difference_ms = Math.abs(date2_ms - date1_ms);
    var difference_ms = (date2_ms - date1_ms); 
                
    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY);

} // Fin de calcula el no. de dias entre dos fechas

function restringeHotel(){	
	var frm=document.detallesItinerarios;		
	var longitud = parseInt($("#solicitud_table>tbody >tr").length);
	
	if(estatus_en_edicion_de_itinerario){//esta editando un itinerario
		var itinerario=$("#itinerarioActualOPosible").val();
		for(var i=1;i<=longitud;i++){
			if(itinerario != i){
				if(frm.hospedaje[0].checked == true || frm.hospedaje[1].checked == false ){
					if($("#fecha").val() == $("#salida"+i).val()){
						if(($("#CheckHAgencia"+i).val() == true || $("#CheckHAgencia"+i).val() == 'true')){
							alert("No es posible registrar un hotel en la fecha seleccionada debido a que ya cuenta con uno.");
							return false;												
						}
					}
				}
			}
		}		
	}else{
		for(var i=1;i<=longitud;i++){			
			if( $("#fecha").val() == $("#salida"+i).val() && ($("#CheckHAgencia"+i).val() == true || $("#CheckHAgencia"+i).val() == 'true') 
				&& ((frm.hospedaje[0].checked == true && frm.enviar_hosp_agencia[0].checked == true ) || (frm.hospedaje[0].checked == true && frm.enviar_hosp_agencia[0].checked == false))){			
				alert("No es posible registrar un hotel en la fecha seleccionada debido a que ya cuenta con uno.");
				return false;
			}
		}		
	}		
}

