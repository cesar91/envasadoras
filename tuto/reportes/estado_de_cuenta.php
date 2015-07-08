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
<center><h3>Estado de Cuenta</h3></center>
<FORM ACTION="estado_de_cuenta.php" METHOD="post" >
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
     <!-- Usuario -->    
    <tr><td colspan='2' align='left'>&nbsp;</td><tr>    
    <tr>
        <td colspan='1' align='right'>Documento:</td>
        <td colspan='1' align='left'>
           <select name="tipo" id="tipo">
                <option name="Todos" id="Todos" value="Todos">Todos</option>
                <option name="Anticipo" id="Anticipo" value="1">Anticipo</option>
                <option name="Amex" id="Amex" value="2">Amex</option>                
                <option name="Comprobacion" id="Comprobacion" value="3">Comprobación</option>
                <option name="ComprobacionTHC" id="ComprobacionTHC" value="4">Comprobación THC</option>
                <option name="CajaChica" id="CajaChica" value="5">Reembolso Caja Chica</option>
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

function Saldos_Comprobar($tipo, $inicio, $fin, $empresa, $centro,$usuario){
    $cnn	= new conexion();
    $aux	= array();
    $extra	="";

	$extra	="";
	if($empresa	>0 && $empresa!='Todas'){
		$extra.=" and e_id='{$empresa}'";
	}
	
	if($centro != "Todas"){
		$extra.=" and cc_id='{$centro}'";
	}
	
	if($usuario	!= "Todas"){
		$extra.=" and u_id='{$usuario}'";	
	}

	if($tipo	!= "Todos"){
		$extra.=" and t_flujo='{$tipo}'";	
	}
	
	$query = sprintf("SELECT e_id, empresa, cc_id, ceco, u_id, usuario,  t_etiqueta, sv_tramite, sv_fecha_registro, 
sv_anticipo,  co_id, co_fecha_registro, cp_concepto, dc_monto, t_flujo
FROM (
SELECT 
(SELECT e_id FROM empresas WHERE e_id = 
(SELECT cc_empresa_id FROM cat_cecos WHERE cc_id = sv_ceco_paga) ) as e_id, 
(SELECT e_codigo FROM empresas WHERE e_id = 
(SELECT cc_empresa_id FROM cat_cecos WHERE cc_id = sv_ceco_paga) ) as empresa, 
(SELECT cc_id FROM cat_cecos WHERE cc_id = sv_ceco_paga) as cc_id,
(SELECT cc_centrocostos FROM cat_cecos WHERE cc_id = sv_ceco_paga) as ceco,
u_id, u_nombre as usuario, t_etiqueta, 
sv_tramite, sv_fecha_registro, sv_anticipo,  co_id, co_fecha_registro,  
'' as cp_concepto, '' as dc_monto, t_flujo
FROM solicitud_viaje s 
INNER JOIN tramites t ON t.t_id = s.sv_tramite
INNER JOIN usuario U on(t.t_iniciador=U.u_id) 
INNER JOIN empleado EP on (U.u_id=EP.idfwk_usuario)
INNER JOIN cat_cecos CECO ON (EP.idcentrocosto = CECO.cc_id)
LEFT JOIN comprobaciones c ON c.co_tramite = s.sv_tramite 

UNION

SELECT 
(SELECT e_id FROM empresas WHERE e_id = 
(SELECT cc_empresa_id FROM cat_cecos WHERE cc_id = sv_ceco_paga) ) as e_id, 
(SELECT e_codigo FROM empresas WHERE e_id = 
(SELECT cc_empresa_id FROM cat_cecos WHERE cc_id = sv_ceco_paga) ) as empresa, 
(SELECT cc_id FROM cat_cecos WHERE cc_id = sv_ceco_paga) as cc_id,
(SELECT cc_centrocostos FROM cat_cecos WHERE cc_id = sv_ceco_paga) as ceco,
u_id, u_nombre as usuario, '' as t_etiqueta, 
sv_tramite, sv_fecha_registro, 0 as sv_anticipo, co_id, co_fecha_registro,  
cp_concepto, dc_monto, t_flujo
FROM solicitud_viaje s 
INNER JOIN tramites t ON t.t_id = s.sv_tramite
INNER JOIN usuario U on(t.t_iniciador=U.u_id) 
INNER JOIN empleado EP on (U.u_id=EP.idfwk_usuario)
INNER JOIN cat_cecos CECO ON (EP.idcentrocosto = CECO.cc_id)
LEFT JOIN comprobaciones c ON c.co_tramite = s.sv_tramite 
LEFT JOIN detalle_comprobacion ON (dc_comprobacion = co_id)
LEFT JOIN cat_conceptos cc ON (dc_concepto = cc.dc_id)
) AS a WHERE sv_fecha_registro >= '%s  00:00:01' AND sv_fecha_registro <= '%s 23:59:59' %s 
ORDER by usuario, sv_tramite", $inicio, $fin, $extra);
	
	error_log($query);
	$rst=$cnn->consultar($query);
	$faltante=0;
	while($fila=mysql_fetch_assoc($rst)){
		$faltante+=$fila["faltante"];
		if($fila["t_flujo"]=="1"){
			$fila["t_flujo"]="Anticipo";
		}
		if($fila["t_flujo"]=="2"){
			$fila["t_flujo"]="Amex";
		}
		if($fila["t_flujo"]=="3"){
			$fila["t_flujo"]="Comprobación";
		}
		if($fila["t_flujo"]=="4"){
			$fila["t_flujo"]="Comprobación THC";
		}
		if($fila["t_flujo"]=="5"){
			$fila["t_flujo"]="Reembolso Caja Chica";
		}
		array_push($aux,
			array(                                
				"empresa"	=>$fila["empresa"], 
				"ceco"	    =>$fila["ceco"],                                 
				"usuario"	=>$fila["usuario"], 
				"t_flujo"	    =>$fila["t_flujo"],
				"t_etiqueta"	=>$fila["t_etiqueta"], 
				"sv_fecha_registro"	=>$fila["sv_fecha_registro"], 
				"sv_anticipo"	=>$fila["sv_anticipo"],                                                               
				"cp_concepto"	=>$fila["cp_concepto"], 
				"dc_monto"	    =>$fila["dc_monto"]
			)
		);                
	}	

    return($aux);
    
}//FIN DE Saldos_Comprobar

if(isset($_POST['empresalist']) && $_POST['empresalist']!="" && 
    isset ($_POST['buscar']) && isset($_POST['fechaInicio']) && $_POST['fechaInicio']!="" && 
                                    isset($_POST['fechaFin']) && $_POST['fechaFin']!=""){

        $fechaInicio=$_POST['fechaInicio'];
        $fechaFin=$_POST['fechaFin'];
        $empresalist=$_POST['empresalist'];
        $centro=$_POST['centro'];
        $usuariolist=$_POST['usuariolist'];
        $tipo=$_POST['tipo'];
        
        
        // Convierte fechas a formato de mysql
        $date=explode("/",$fechaInicio);
        $fechaInicio=$date[2] ."-".$date[1]."-".$date[0];
        $date=explode("/",$fechaFin);
        $fechaFin=$date[2] ."-".$date[1]."-".$date[0];        
		
        $aux.="<br>";
		$aux.="<table width='90%' align='center' cellspacing='2' cellpadding='2'>";
		$aux.="<tr>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Empresa</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Departamento</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Usuario</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Documento</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Motivo del Viaje</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Fecha Solicitud</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Anticipo</strong></td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Concepto</strong></td>";        
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Monto</strong></td>";
		$aux.="</tr>";
		
                            
		$resultado=Saldos_Comprobar($tipo,$fechaInicio,$fechaFin,$empresalist,$centro,$usuariolist);
		$suma=array("sv_anticipo"=>0, "dc_monto"=>0);
		
		foreach ($resultado as $fila){
			$aux.="<tr>";
			foreach ($fila as $nombre=>$dato){
				$xls.="{$dato}\t";
				$aux.="<td style=\"font-size: 9px;\" align='right'>{$dato}</td>";
				foreach ($suma as $valores=>$valor){
					if($valores==$nombre){
						$suma[$valores]=number_format($valor + $dato,2,".","");
					}
				} 
			}
			$aux.="</tr>";
		}
		$aux.="<tr>";
		$aux.="<td colspan='5'>&nbsp;</td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>{$suma['sv_anticipo']}</strong></td>";
        $aux.="<td style=\"font-size: 9px;\" align='right'>&nbsp;</td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>{$suma['dc_monto']}</strong></td>";

		$aux.="</tr>";
		$aux.="</table>";
		$aux.="</body>";
		$aux.="</html>";
        
	echo $aux;

}
$I->footer();
?>
</html>
