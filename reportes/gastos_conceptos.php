<?php
session_start();


require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "../functions/report.php";

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
<center><h3>Reporte de Gastos</h3></center>
<FORM ACTION="gastos_conceptos.php" METHOD="post" >
<table align='center', width='30%'>
    <tr>
        <td><input name="fechaInicio" id="fechaInicio" value="<?php echo date('d/m/Y',strtotime("-1 months")); ?>" size="15" />
			<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> Fecha Inicio</td>
        <td><input name="fechaFin" id="fechaFin" value="<?php echo date('d/m/Y'); ?>" size="15" />
			<img src="../images/b_calendar.png" alt="Calendario" name="fechaIMG" width="16" height="16" id="fechaIMG" /> Fecha Final</td>
    </tr>
</table>
  
<table align='center', width='30%'>    
    <!-- Empresa -->
    <tr><td colspan='2' align='left'>&nbsp;</td><tr>
    <tr>
        <td colspan='1' align='right'>Empresa:</td>
        <td colspan='1' align='left'>
           <select name="empresalist" id="empresalist">
               <option name="Todas" id="Todas" value="Todas">Todas</option>
               <?php      
                $empresalist=new Empresa();  
               foreach($empresalist->Load_all() as $arrE){
               ?>
                    <option name="<?php echo $arrE['e_codigo'];?>" id="<?php echo $arrE['e_codigo'];?>" value="<?php echo $arrE['e_id'];?>"><?php echo $arrE['e_nombre'] . ' - ' . $arrE['e_codigo'];?></option>
               <?php
               }
               ?>
           </select> 
        </td>           
    </tr>

    <!-- Ceco -->    
    <tr><td colspan='2' align='left'>&nbsp;</td><tr>    
    <tr>
        <td colspan='1' align='right'>Departamento:</td>
        <td colspan='1' align='left'>
           <select name="centro" id="centro">
                <option name="Todas" id="Todas" value="Todas">Todos</option>
               <?php      
               $centro=new CentroCosto();
               foreach($centro->Load_all() as $arrE){
               ?>
                    <option name="<?php echo $arrE['cc_centrocostos'];?>" id="<?php echo $arrE['cc_centrocostos'];?>" value="<?php echo $arrE['cc_id'];?>"><?php echo $arrE['cc_centrocostos'] . ' - ' . $arrE['cc_nombre'];?></option>
               <?php
               }
               ?>
           </select> 
        </td>           
    </tr>    
    
    <!-- Concepto -->    
    <tr><td colspan='2' align='left'>&nbsp;</td><tr>    
    <tr>
        <td colspan='1' align='right'>Concepto:</td>
        <td colspan='1' align='left'>
           <select name="concepto" id="concepto">
                <option name="Todas" id="Todas" value="Todas">Todos</option>
               <?php      
               $concepto=new Concepto();
               foreach($concepto->Load_all() as $arrE){
               ?>
                    <option name="<?php echo $arrE['dc_id'];?>" id="<?php echo $arrE['dc_id'];?>" value="<?php echo $arrE['dc_id'];?>"><?php echo $arrE['cp_concepto'];?></option>
               <?php
               }
               ?>
           </select> 
        </td>           
    </tr>       
    
    <!-- Usuario -->    
    <tr><td colspan='2' align='left'>&nbsp;</td><tr>    
    <tr>
        <td colspan='1' align='right'>Usuario:</td>
        <td colspan='1' align='left'>
           <select name="usuariolist" id="usuariolist">
                <option name="Todas" id="Todas" value="Todas">Todos</option>
               <?php      
               $usuariolist=new Usuario();
               foreach($usuariolist->Load_all() as $arrE){
               ?>
                    <option name="<?php echo $arrE['u_nombre'];?>" id="<?php echo $arrE['u_id'];?>" value="<?php echo $arrE['u_id'];?>"><?php echo $arrE['u_nombre'];?></option>
               <?php
               }
               ?>
           </select> 
        </td>
    </tr>     
    
    <!-- Submit -->    
    <tr><td colspan='2' align='center'>&nbsp;</td><tr>    
    <tr>
        <td colspan='2' align='center'>
           <INPUT TYPE="submit" name='buscar' id='buscar' value="buscar">
        </td>
    </tr>     
    
<table>

</FORM>
<?php
$v0=$_POST['empresalist'];


if(isset($_POST['empresalist']) && $_POST['empresalist']!="" && 
    isset ($_POST['buscar']) && isset($_POST['fechaInicio']) && $_POST['fechaInicio']!="" && 
                                    isset($_POST['fechaFin']) && $_POST['fechaFin']!=""){

	$fechaInicio=$_POST['fechaInicio'];
	$fechaFin=$_POST['fechaFin'];
	$empresalist=$_POST['empresalist'];
	$centro=$_POST['centro'];
	$usuariolist=$_POST['usuariolist'];
	$concepto=$_POST['concepto'];

	$reporte=new report();
	$datos=$reporte->Reporte_Gastos($fechaInicio,$fechaFin,$empresalist,$usuariolist,$centro,$concepto);

    $aux="";
    $aux.="<html>";
    $aux.="<table  align='center' cellspacing='4' cellpadding='4' >";

    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>Empresa</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>Departamento</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>Usuario</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>Concepto</strong></td>";
    $aux.="<td style=\"font-size: 12px;\"><br><br><strong>Total Aprobado</strong></td>";

    $suma=0;

	foreach ($datos as $fila){
			$aux.="<tr>";
			$aux.="<td style=\"font-size: 10px;\" align='left'>{$fila['e_codigo']}</td>";
			$aux.="<td style=\"font-size: 10px;\" align='left'>{$fila['cc_centrocostos']}</td>";
			$aux.="<td style=\"font-size: 10px;\" align='left'>{$fila['u_nombre']}</td>";
			$aux.="<td style=\"font-size: 10px;\" align='left'>{$fila['cp_concepto']}</td>";
			$aux.="<td style=\"font-size: 10px;\" align='right'>{$fila['total']}</td>";
			$aux.="</tr>";
            
            $suma = $suma + $fila['total'];
		}

        $aux.="<tr>";
        $aux.="<td style=\"font-size: 10px;\" align='left'></td>";
        $aux.="<td style=\"font-size: 10px;\" align='left'></td>";
        $aux.="<td style=\"font-size: 10px;\" align='left'></td>";
        $aux.="<td style=\"font-size: 10px;\" align='left'></td>";
        $aux.=sprintf("<td style=\"font-size: 10px;\" align='right'><strong>%s</strong></td>", number_format($suma,2,".",""));
        $aux.="</tr>";


	$aux.="</table>";
	echo $aux;

}
$I->footer();
?>
</html>
