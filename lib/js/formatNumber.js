function number_format(number, decimals, dec_point, thousands_sep) {
var n = !isFinite(+number) ? 0 : +number, 

prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
s = '',toFixedFix = function (n, prec) {
	var k = Math.pow(10, prec);
	return '' + Math.round(n * k) / k;        };
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
			
	if (s[0].length > 3) {
    		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);    }
			
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}    return s.join(dec);
}
function number_format_sin_redondeo(number) {
	var index_punto = number.indexOf('.');
	if(index_punto != '-1'){
		var parte_entera = number.substring(0,index_punto);
	}else{
		var parte_entera = number;	
	}
	var parte_entera_nueva = "";
	for(i=parte_entera.length;i>0;i=i-3){
		if(i>0)
			parte_entera_nueva += parte_entera.charAt(i-1);
		if(i>1)
			parte_entera_nueva += parte_entera.charAt(i-2);
		if(i>2){
			parte_entera_nueva += parte_entera.charAt(i-3);
			parte_entera_nueva += ",";
		}
	}
	parte_entera_nueva = parte_entera_nueva.split("").reverse().join("");
	if(parte_entera_nueva.charAt(0)==","){
		parte_entera_nueva = parte_entera_nueva.substring(1);
	}
	if(parte_entera_nueva == ""){
		parte_entera_nueva = "0";
	}
	if(index_punto != '-1'){
		return parte_entera_nueva+number.substring(index_punto,index_punto+3);
	}else{
		return parte_entera_nueva+".00";
	}
}

//Funcion para formato 
function formatCurrency(num,id){
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
    num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();
    if(cents<10)
    cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
    num = num.substring(0,num.length-(4*i+3))+','+
    num.substring(num.length-(4*i+3));
    $("#"+id).val((((sign)?'':'-')  + num + '.' + cents));
}

//validación campos numericos
function validaNum(valor){	
	cTecla=(document.all)?valor.keyCode:valor.which;	
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);	
	return patron.test(cTecla);		 						
}

//validacion de ceros
function validaCeros(valor,campo){
	ceros_izq = new RegExp('^0*','g');		
	var nuevo_numero = valor.replace(ceros_izq,"");
	$("#"+campo).val(nuevo_numero);		
}