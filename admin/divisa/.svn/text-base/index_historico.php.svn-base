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

error_log("Tipo divisa:".$_GET['id']);

?>

<script language="JavaScript" type="text/javascript">//Fecha

$(function() { 	 
	$("#finicial").date_input();
	$("#ffinal").date_input();
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
	return dom + "-" + month + "-" + date.getFullYear();
	}
	});
		
	//Opciones de Idioma del Calendario
	jQuery.extend(DateInput.DEFAULT_OPTS, {
	month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});

	function validaNum(valor){
        cTecla=(document.all)?valor.keyCode:valor.which;
        if(cTecla==8) return true;
        patron=/^([0-9.]{1,2})?$/;
        cTecla= String.fromCharCode(cTecla);
        return patron.test(cTecla);
	}

	function regresar(){
		location="index.php";
	}
	

//fin Fecha
</script>
</head>
<body>


<h1>Hist&oacute;rico De Divisas</h1>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<div style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;">
<form action="<?php echo $_SERVER ['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>" method="post">
 Fecha Inicial:
  <input name="finicial" id="finicial" size="10" readonly="readonly"/>
    Fecha Final:
  <input name="ffinal" id="ffinal" size="10" readonly="readonly"/>
	<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
	</div>	
</form>
<?php
    $paginacion="";
	if(isset($_GET['id'])){
		$_SESSION['div_id'] = $_GET['id'];
	}
	$tipoDivisa=$_SESSION['div_id'];
    $L = new Lista("divisa");
    $L->Cabeceras("Divisa");
    $L->Cabeceras("Tasa");
    $L->Cabeceras("Fecha de Modificaci&oacute;n");
    $fechaInicial="";
    $fechaFinal="";
    
    if (isset($_POST['finicial'])){
    	$fechaInicial=$_POST['finicial'];
    }
    if (isset($_POST['ffinal'])){
    	$fechaFinal=$_POST['ffinal'];
    }
    if ($fechaInicial=="" && $fechaFinal==""){    	 
    	$query="SELECT if (div_id=2,'USD','EUR') as divisa, hd_tasa, hd_fecha_modificacion FROM historial_divisa where div_id=$tipoDivisa order by hd_id desc";
    }
    if ($fechaInicial=="" && $fechaFinal!=""){
    	$fechaFinal=substr($fechaFinal,6,4)."-".substr($fechaFinal,3,2)."-".substr($fechaFinal,0,2);    	
    	$query="SELECT if (div_id=2,'USD','EUR') as divisa, hd_tasa, hd_fecha_modificacion FROM historial_divisa where div_id=$tipoDivisa and hd_fecha_modificacion<='$fechaFinal 23:59:59' order by hd_id desc";
    }
    if ($fechaInicial!="" && $fechaFinal==""){
    	$fechaInicial=substr($fechaInicial,6,4)."-".substr($fechaInicial,3,2)."-".substr($fechaInicial,0,2);
    	$query="SELECT if (div_id=2,'USD','EUR') as divisa, hd_tasa, hd_fecha_modificacion FROM historial_divisa where div_id=$tipoDivisa and hd_fecha_modificacion>='$fechaInicial 00:00:00' order by hd_id desc";
    }
    if ($fechaInicial!="" && $fechaFinal!=""){
    	$fechaInicial=substr($fechaInicial,6,4)."-".substr($fechaInicial,3,2)."-".substr($fechaInicial,0,2);
    	$fechaFinal=substr($fechaFinal,6,4)."-".substr($fechaFinal,3,2)."-".substr($fechaFinal,0,2);
    	$query="SELECT if (div_id=2,'USD','EUR') as divisa, hd_tasa, hd_fecha_modificacion FROM historial_divisa where div_id=$tipoDivisa and (hd_fecha_modificacion>='$fechaInicial 00:00:00' and hd_fecha_modificacion<='$fechaFinal 23:59:59')order by hd_id desc";
    }
    error_log($query);
    $L->muestra_lista($query,0);
?>
<center>
	<input type="button" onclick="regresar()" value="Volver"/>
</center>	
<?php
$I->Footer();
?>
</body>
</html>
<?php
?>
