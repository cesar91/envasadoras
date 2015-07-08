<?php
	session_start();
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";

	$I = new Interfaz("Parámetros",true);
?>
	<script>
		function actualizaDivisa(){
			$.ajax({					
				url: 'services/ingresoSinCargarDivisa.php',
				type: "POST",
				error:function() {
					$("#warning").html("Ha ocurrido un error al guardar...").fadeIn(700).fadeOut(4700);					
				},
				data: "Aceptar=1"+"&euros="+$('#euros').val()+"&dolares="+$('#dolares').val()+"&pesos="+$('#pesos').val(),
				dataType: "html",
				success: function(dato){
					if(dato!=""){
						$("#warning").html("Ha ocurrido un error al guardar...").fadeIn(700).fadeOut(4700);						
					}
					else{
						$("#warning").html("Se actualizo correctamente...").fadeIn(700).fadeOut(3700);						
					}
				}
			});
		}
		
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
?>	
		<h3>Edici&oacute;n de Tasas</h3>
		<form>
		<table  style="border:1px #CCCCCC solid;margin-top:5px;text-align:left; background-color:#F8F8F8">
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr style="display:none">
				<td>&nbsp;</td>
				<td align="right">Pesos:</td>
				<td align="left">
					<input type="hidden" id="pesos" name="pesos" value="<?php echo $tasaPesos;?>" class="cajarigth" onKeyPress="return validaNum(event)">
				</td>
				<td>&nbsp;</td>
			</tr>
				<td>&nbsp;</td>
					<td align="right">D&oacute;lares:</td>
					<td align="left">
						<input type="text" id="dolares" name="dolares" value="<?php echo $tasaDolares;?>" class="cajarigth" onKeyPress="return validaNum(event)">
					</td>
				<td>&nbsp;</td>
			</tr>
			<tr>	
				<td>&nbsp;</td>
				<td align="right">Euros: </td>
				<td align="left">
					<input type="text" id="euros" name="euros" value="<?php echo $tasaEuros;?>" class="cajarigth" onKeyPress="return validaNum(event)">
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr><td colspan="4"><div id="divLoad"></div></td></tr>
			<tr>
				<td>&nbsp;</td>	
				<td colspan="2" align="right"><input type="button" id="Aceptar" name="Aceptar" value="Aceptar" onclick="actualizaDivisa();"></td>
				<td>&nbsp;</td>
			</tr>
			<tbody>Ingrese el nuevo valor de divisa para:</tbody>
		</table>
		<div id="warning" style="color:#FF0000"></div>
		</form>
	</center>	
<?php
	$I->Footer();
?>