<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

?>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
<style type="text/css">
	.style1 {color: #FF0000}
	.cuestionDel{
		vertical-align:middle;
		display:block;
		background-color:#CCCCCC;
		width:30%;
		top:35%;
		left:35%;	
		position:absolute;		
	}
	.im{	
		
		border:0;
		
	}
	.tds{
	text-align:center;
	color:#FF4646;
	font-weight:bold;
	}
	.cajarigth{
		text-align:right;
		width:74px
	}
	

</style>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="javascript">
	$(function(){
		$("#Aceptar").click(function(){
			$.ajax({
				
				url: 'services/ingresoSinCargarDivisa.php',
				type: "POST",
				error:function() {
					$("#warning").html("Ha ocurrido un error al guardar...");
					$("#warning").fadeIn(700);
					$("#warning").fadeOut(4700);
				},
				//data: "Aceptar=1",
				data: "Aceptar=1"+"&euros="+$('#euros').val()+"&dolares="+$('#dolares').val()+"&pesos="+$('#pesos').val(),
				dataType: "html",
				success: function(dato){
					if(dato!=""){
						$("#warning").html("Ha ocurrido un error al guardar...");
						$("#warning").fadeIn(700);
						$("#warning").fadeOut(4700);
					}
					else{
						$("#warning").html("Se actualizo correctamente...");
						$("#warning").fadeIn(700);
						$("#warning").fadeOut(3700);
                        document.location.reload();
					}

				}
			});//fin ajax			
				
		});

	});
	
	function validaNum(valor){
        cTecla=(document.all)?valor.keyCode:valor.which;
        if(cTecla==8) return true;
        patron=/^([0-9.]{1,2})?$/;
        cTecla= String.fromCharCode(cTecla);
        return patron.test(cTecla);
	}
	
</script>
</head>
<body>
<?php
$I  = new Interfaz("Parámetros",true);

$divisa= new Divisa();
//Carga valores de tasas
foreach($divisa->Load_all() as $arrD){
	if($tipoDivisa=$arrD['nombre']=="Pesos"){
		$tasaPesos=$arrD['tasa'];
	}
	
	elseif($tipoDivisa=$arrD['nombre']=="Dólares"){
		$tasaDolares=$arrD['tasa'];		
	}
	
	elseif($tipoDivisa=$arrD['nombre']=="Euros"){
		$tasaEuros=$arrD['tasa'];		
	}
}
?>

<center>
	<h1>Cat&aacute;logo De Divisas</h1>
<?php
    $paginacion="";

    $L = new Lista("divisa");
    $L->Cabeceras("Nombre");
    $L->Cabeceras("Tasa");
    $L->Cabeceras("Fecha de Actualizaci&oacute;n");
	$L->Cabeceras("Historial");        
    $query="SELECT div_nombre, div_tasa, div_ultima_fecha_modificacion,if (div_nombre!='MXN',concat ('<a href=\"index_historico.php?id=',div_id,'\"><img src=\"../../images/admin/reloj.jpg\"/></a>'),'') as img FROM divisa order by div_id";
    $L->muestra_lista($query,0);
?>
</center>

<center>
	<h3>Edici&oacute;n de Tasas</h3>
	<form>
		<table  style="border:1px #CCCCCC solid;margin-top:5px;text-align:left; background-color:#F8F8F8">
			<tr >	
				<td>&nbsp;
					
				</td>
				<td align="right">
					&nbsp;
				</td>
				<td align="left">
					&nbsp;
				</td>
				<td>&nbsp;
					
				</td>
			</tr>
			<tr>
				<td>&nbsp;
					
				</td>
				<td align="right">
					Pesos:
				</td>
				<td align="left">
					<input type="text" id="pesos" name="pesos" value="<?php echo $tasaPesos;?>" class="cajarigth" onKeyPress="return validaNum(event)">
				</td>
				<td>&nbsp;
					
				</td>
			</tr>
			<tr>
				<td>&nbsp;
					
				</td>
				<td align="right">
					D&oacute;lares:
				</td>
				<td align="left">
					<input type="text" id="dolares" name="dolares" value="<?php echo $tasaDolares;?>" class="cajarigth" onKeyPress="return validaNum(event)">

				</td>
				<td>&nbsp;
					
				</td>
			</tr>
			<tr>	
				<td>&nbsp;
					
				</td>
				<td align="right">
					Euros:
				</td>
				<td align="left">
					<input type="text" id="euros" name="euros" value="<?php echo $tasaEuros;?>" class="cajarigth" onKeyPress="return validaNum(event)">
				</td>
				<td>&nbsp;
					
				</td>
			</tr>
			<tr>	
				<td colspan="4">
					<div id="divLoad"></div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2" align="right">
					<input type="button" id="Aceptar" name="Aceptar" value="Aceptar">
				</td>
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
</body>
</html>
<?php
?>
