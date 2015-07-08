var regionNacional = 2;

function validarPoliticaComprobaciones(){
	var idUsuario = $("#iu").val();
	var flujo = $("#flujoId").val();
	var concepto = $("#select_concepto option:selected").val();
	var conceptoTexto = $("#select_concepto option:selected").text();
	var montoPesos = parseFloat($('#total_dolares').val().replace(/,/g,""));
	var region = regionNacional;
	var textoExcepcion = "";
	
	var politica = {
		'idUsuario' : idUsuario, 
		'idConcepto' : concepto, 
		'flujo': flujo, 
		'region' : region
	};
	
	var montoLimitePolitica = obtenerPolitica(politica);
	
	if(montoLimitePolitica == 0){
		$("#comentarioReq").html("Comentario:");
		$("#excedePolitica").val(0);
		$("#textoExcepcion").val("");
	}else{
		if(montoLimitePolitica < montoPesos){
			$("#comentarioReq").html("Comentario<span class='style1'>*</span>:");
			$("#excedePolitica").val(1);
			$("#textoExcepcion").val("Esta rebasando la pol&iacute;tica del concepto " + conceptoTexto + ". <br>El monto m&aacute;ximo es de " + montoLimitePolitica + " MXN.");
		}else{
			$("#comentarioReq").html("Comentario:");
			$("#excedePolitica").val(0);
			$("#textoExcepcion").val("");
		}
	}
}