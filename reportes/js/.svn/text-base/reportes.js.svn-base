function inicializa_vice(){
	var frm= document.formReporte;
	frm.viceOpciones.selectedIndex=0;
}


function obtener_cecos(empresa){

	var frm=document.formReporte;
	if(empresa != null){
		$.ajax({			
			type: "POST",	
			url: "services/ajax_reportes.php",
			data: "empresa_id="+empresa,
			dataType: "json",
			success: function(json){
				if(json==null){					
					$("#CecoOpciones").append(new Option("Sin Datos"));					
				}else{					
					LlenarCombo(json, frm.CecoOpciones);
				}
			}
		});
	}
	
}

function obtener_empleado(idCeco){
	
	var frm=document.formReporte;
	if(idCeco != null){
		$.ajax({			
			type: "POST",	
			url: "services/ajax_reportes.php",
			data: "cecoId="+idCeco,
			dataType: "json",
			success: function(json){
				if(json==null){					
					$("#EmpleadoOpciones").append(new Option("Sin Datos"));					
				}else{					
					LlenarCombo(json, frm.EmpleadoOpciones);
				}
			}
		});
	}
}


	function LimpiarCombo(combo){
		while(combo.length > 0){
			combo.remove(combo.length-1);
		}
	}
	
	function LlenarCombo(json, combo){
		LimpiarCombo(combo);
		combo.options[0]=new Option('Selecciona un item', '');
		for(var i=0;i<json.length;i++){
			var str=json[i];
			var str1=str.slice(str.search(":")+1);//2
			var str2=str.substr(0,str.search(":"));//1
			combo.options[combo.length] = new Option(str1,str2);
		}
	}


