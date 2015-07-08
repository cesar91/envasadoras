<?php
session_start();

require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once('../lib/php/utils.php');

$userar = $_SESSION["usuario"];
$var = "pero";
$I	= new Interfaz("Reportes",true);

?>
<html>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/formatNumber.js" type="text/javascript"></script>
<script language="JavaScript" src="../lib/js/withoutReloading.js" type="text/javascript"></script>



<script language="JavaScript" type="text/javascript">
//Fecha
$(function() {
	//campo
	$("#fechaInicio").date_input();
	$("#fechaFin").date_input();
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
	},
	rangeStart: function(date) {
     return this.changeDayTo(this.start_of_week, new Date(date.getFullYear(), date.getMonth()), -1);
    },

    rangeEnd: function(date) {
    return this.changeDayTo((this.start_of_week - 1) % 7, new Date(date.getFullYear(), date.getMonth() + 1, 0), 1);
    }

	})

	//Opciones de Idioma del Calendario
	jQuery.extend(DateInput.DEFAULT_OPTS, {
	month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});
//fin Fecha

</script>

<link rel="stylesheet" type="text/css" href="../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../css/table_style.css"/>
<link rel="stylesheet" type="text/css" href="../css/style_Table_Edit.css"/>
<style type="text/css">
.style1 {color: #FF0000}

</style>
<center><h3>Reporte de Gastos AMEX no Comprobados</h3></center>
<FORM ACTION="reporte_de_gastos_amex_no_comprobados.php" METHOD="post" >
<table align='center', width='30%'>
    <tr>
        <td><input name="fechaInicio" id="fechaInicio" value="<?php echo date('d/m/Y',strtotime("-1 months")); ?>" size="15" />
			<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> Fecha Inicio</td>
        <td><input name="fechaFin" id="fechaFin" value="<?php echo date('d/m/Y'); ?>" size="15" />
			<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> Fecha Final</td>
    </tr>
</table>
  
<table align='center', width='30%'>    
    <!-- Submit -->    
    <tr><td colspan='2' align='center'>&nbsp;</td><tr>    
    <tr>
        <td colspan='2' align='center'>
           <INPUT TYPE="submit" name='buscar' id='buscar' value="buscar">
        </td>
    </tr>     
</table>

</FORM>
<?php
if(isset ($_POST['buscar']) && isset($_POST['fechaInicio']) && $_POST['fechaInicio']!="" && isset($_POST['fechaFin']) && $_POST['fechaFin']!=""){
	$array = array();
	$fechaInicio = $_POST['fechaInicio'];
	$fechaFin = $_POST['fechaFin'];
	$count = 0;
	$aux2 = 0;


	// Convierte fechas a formato de mysql
	$date=explode("/",$fechaInicio);
	$fechaInicio=$date[2] ."-".$date[1]."-".$date[0];
	$date=explode("/",$fechaFin);
	$fechaFin=$date[2] ."-".$date[1]."-".$date[0];

	$sql="call getReporteGastosAMEX(\"".$fechaInicio."\",\"".$fechaFin."\",\"".$aux2."\");";
	mysql_query($sql);
	$sql="select * from RESULTADO;";
	$res=mysql_query($sql);
	while($row = mysql_fetch_array($res)) {
	  $array[] = $row;
	}
	$count = mysql_num_rows($res);
	$sql="DROP TABLE IF EXISTS RESULTADO;";
	mysql_query($sql);

	$_SESSION['fechaInicio'] = $fechaInicio;
	$_SESSION['fechaFin'] = $fechaFin;
	$_SESSION['count'] = $count;
	$_SESSION['nombre'] = "reporteGastosAMEXnoComprobados";
	$_SESSION['head'] = "REPORTE GASTOS AMEX NO COMPROBADOS";
	$_SESSION['aux2'] = $aux2;

    $aux="";
    $aux.="<html>";
    $aux.="<table  align='center' cellspacing='4' cellpadding='4' >";

    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>FECHA</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>EMPLEADO</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>TRANSACCIÓN</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>NÚMERO DE TARJETA</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>TIPO DE TARJETA</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>CONCEPTO</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>MONTO</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>MONEDA</strong></td>";

	foreach ($array as $row){
		$aux.="<tr>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['FECHA']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['EMPLEADO']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['TRANSACCION']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['NUMERO DE TARJETA']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['TIPO DE TARJETA']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['CONCEPTO']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['MONTO']}</td>";
		$aux.="<td style=\"font-size: 10px;\" align='left'>{$row['MONEDA']}</td>";
		$aux.="</tr>";
	}

	$aux.="<tr>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="<td style=\"font-size: 10px;\" align='left'></td>";
	$aux.="</tr>";
	$aux.="</table>";
	
	if($count > 0){
		echo "<center><font color=blue>Reporte entre ".$fechaInicio." y ".$fechaFin.", ".$count." registros encontrados.</font></center>";
		echo $aux;
		?>
			<FORM ACTION="exportar.php" METHOD="get" name="rep1" target="_blank" id="rep1">
				<table align='center', width='30%'>    
					<!-- Submit -->    
					<tr><td colspan='2' align='center'>&nbsp;</td><tr>    
					<tr>
						<td colspan='2' align='center'>
							<INPUT TYPE="submit" name='exportar' id='exportar' value="Exportar a excel">
						</td>
					</tr>     
				</table>
			</FORM>
		<?
	}else{
		echo "<center><font color=red>Sus parametros de buqueda no generaron resultados.</font></center>";
	}
}

$I->footer();
?>
</html>
