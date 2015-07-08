<?php
	session_start();
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";

	$I = new Interfaz("Parámetros",true);
?>
	<script>
		function validaNum(valor){
			cTecla=(document.all)?valor.keyCode:valor.which;
			if(cTecla==8) return true;
			patron=/^([0-9.]{1,2})?$/;
			cTecla= String.fromCharCode(cTecla);
			return patron.test(cTecla);
		}		
	</script>
<?PHP
	$divisa = new Divisa();
	
	$tasaPesos = '';
	$tasaDolares = '';
	$tasaEuros = '';
	foreach($divisa->Load_all() as $arrD){
		if($tipoDivisa = $arrD['nombre'] == "MXN"){
			$tasaPesos = $arrD['tasa'];
		}elseif($tipoDivisa = $arrD['nombre'] == "USD"){
			$tasaDolares = $arrD['tasa'];		
		}elseif($tipoDivisa = $arrD['nombre'] == "EUR"){
			$tasaEuros = $arrD['tasa'];		
		}
	}
?>
	<center>
		<h1>Cat&aacute;logo De Divisas</h1>
<?php
	$L = new Lista("divisa");
	$L->Cabeceras("Nombre");
	$L->Cabeceras("Tasa");
	$L->Cabeceras("Fecha de Actualizaci&oacute;n");
	$L->Cabeceras("Historial");        
	$query = "SELECT div_nombre, div_tasa, div_ultima_fecha_modificacion,IF (div_nombre!='MXN',CONCAT ('<a href=\"index_historico.php?id=',div_id,'\"><img src=\"../../images/admin/reloj.jpg\" border=\"0\"/></a>'),'') AS img 
			FROM divisa ORDER BY div_id";
	$L->muestra_lista($query,0);
	
	$I->Footer();
?>