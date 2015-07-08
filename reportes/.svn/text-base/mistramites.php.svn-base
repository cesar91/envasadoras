<?php 
session_start();
require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "../functions/report.php";

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
	$("#fecha").date_input();
	$("#fechaLlegada").date_input();
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



<FORM ACTION="mistramites.php" METHOD="post" >
<center>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha Inicio:
  <input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="15" readonly="readonly" />
			<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> 
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha Final:
  <input name="fechaLlegada" id="fechaLlegada" value="<?php echo date('d/m/Y'); ?>" size="15" readonly="readonly" />
			<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" />

<br>
</center>
	<center>
	 &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Empresa:&nbsp;&nbsp;&nbsp;
  			<?php
                                               $empresa=new Empresa();
                                       ?>
                                       <select name="empresa" id="empresa">
                                               <?php        
                                               foreach($empresa->Load_all() as $arrE){
                                               ?>
                                                       <option name="<?php echo $arrE['e_nombre'];?>" id="<?php echo $arrE['e_nombre'];?>" value="<?php echo $arrE['e_id'];?>">                                                                <?php echo $arrE['e_nombre'];?>                                                        </option>
                                               <?php
                                               }
                                               ?>
                                       </select> 
</center>
<br>

  <center>
    Departamento:&nbsp;&nbsp;&nbsp;
  			<?php
                                               $centro=new CentroCosto();
                                       ?>
                                       <select name="centro" id="centro">
                                               <?php        
                                               foreach($centro->Load_all() as $arrE){
                                               ?>
                                                       <option name="<?php echo $arrE['cc_centrocostos'];?>" id="<?php echo $arrE['cc_centrocostos'];?>" value="<?php echo $arrE['cc_centrocostos'];?>">                                                                <?php echo $arrE['cc_centrocostos'];?>                                                        </option>
                                               <?php
                                               }
                                               ?>
                                       </select> 
</center>
    <br>
<center>
	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Empleado:&nbsp;&nbsp;&nbsp;
			<?php
                                               $usuario=new Usuario();
                                       ?>
                                       <select name="usuario" id="usuario">
                                               <?php        
                                               foreach($usuario->Load_all() as $arrE){
                                               ?>
                                                       <option name="<?php echo $arrE['u_nombre'];?>" id="<?php echo $arrE['u_nombre'];?>" value="<?php echo $arrE['u_id'];?>">                                                                <?php echo $arrE['u_nombre'];?>                                                        </option>
                                               <?php
                                               }
                                               ?>
                                       </select>
</center>
<br>
<center>
	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo:&nbsp;&nbsp;&nbsp;
 			<select name="Tramite" id="Tramite"> 
					<option value="1">Solicitud de Viaje</option>
					<option value="2">Solicitud Amex</option>
					<option value="3">Comprobaciones</option>
			</select> 
</center>
	<br> 
<center>
<INPUT TYPE="submit" VALUE="Buscar" name="buscar" id="buscar">
</center>
</FORM>
<?php

if(isset($_POST['empresa']) && $_POST['empresa']!="" && isset ($_POST['buscar']) && isset($_POST['fechaLlegada']) && $_POST['fechaLlegada']!="" && isset($_POST['fecha']) && $_POST['fecha']!=""){
//if(isset($_POST['buscar'])){
	$fechaLlegada=$_POST['fechaLlegada'];
	$fecha=$_POST['fecha'];
	$empresa=$_POST['empresa'];
	$centro=$_POST['centro'];
	$usuario=$_POST['usuario'];
	$Tramite=$_POST['Tramite'];	
	
	$reporte=new report();
	$datos=$reporte->Load_mis_tramites($fecha,$fechaLlegada,$empresa,$usuario,$centro);


$aux="";
$aux.="<html>";
$aux.="<table  align='center' cellspacing='16' cellpadiing='19' >";

$aux.="<td style=\"font-size: 13px;\"><br><br><strong>Tramite</strong></td>";
$aux.="<td style=\"font-size: 13px;\"><br><br><strong>F.Registro</strong></td>";
$aux.="<td style=\"font-size: 13px;\"><br><br><strong>Referencia</strong></td>";
$aux.="<td style=\"font-size: 13px;\"><br><br><strong>Usuario</strong></td>";
$aux.="<td style=\"font-size: 13px;\"><br><br><strong>Activo</strong></td>";
$aux.="<td style=\"font-size: 13px;\"><br><br><strong>Cerrado</strong></td>";
$aux.="<td style=\"font-size: 13px;\"><br><br><strong>Cancelado</strong></td>";


	foreach ($datos as $fila){
	
			$aux.="<tr>";
			
			$aux.="<td style=\"font-size: 11px;\" align='left'>{$fila['tramite']}</td>";
			$aux.="<td style=\"font-size: 11px;\" align='left'>{$fila['fecha']}</td>";
			$aux.="<td style=\"font-size: 11px;\" align='left'>{$fila['referencia']}</td>";
			$aux.="<td style=\"font-size: 11px;\" align='left'>{$fila['nombre']}</td>";
			$aux.="<td style=\"font-size: 11px;\" align='left'><img src=\"{$fila['activo']}\"></td>";
			$aux.="<td style=\"font-size: 11px;\" align='left'><img src=\"{$fila['cerrado']}\"></td>";
			$aux.="<td style=\"font-size: 11px;\" align='left'><img src=\"{$fila['cancelado']}\"></td>";
			
			
			$aux.="</tr>";

		}


	$aux.="</table>";
	echo $aux;

}
$I->footer();
?>
</html>
