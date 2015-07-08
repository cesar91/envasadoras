<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
  
// Formatea el numero
function format_pesos($val){
    return number_format($val,2,".",","); 
}  
  
// Llena los campos para mostrar la pantalla de autorizacion
if(isset($_GET['id'])){
        
    require_once("services/func_comprobacion.php");	
    
	// Carga los datos del tramite
	$Tramite = new Tramite();	
	$idTramite = $_GET['id'];
	$Tramite->Load_Tramite($idTramite);
    $idUser	        = $Tramite->Get_dato("t_iniciador");
    $referencia     = $Tramite->Get_dato("t_etiqueta");    
    $t_etapa_actual = $Tramite->Get_dato("t_etapa_actual"); 
    
    // Carga los datos del aprobador
    $UsuarioAprobador= new Usuario();
    $IdAprobador = $Tramite->Get_dato("t_dueno");
    $UsuarioAprobador->Load_Usuario_By_ID($IdAprobador);

    // Carga los datos del iniciador
    $Usuario = new Usuario();
    $Usuario->Load_Usuario_By_ID($idUser);
	$noEmpleado		= $Usuario->Get_dato("u_usuario");
    $nombreEmpleado	= $Usuario->Get_dato("nombre");
	$CeCoEmpleado	= $Usuario->Get_dato("idcentrocosto");
        
    // Carga los datos de la comprobacion
	$Comprobacion = new Comprobacion();
	$Comprobacion->Load_Comprobacion($idTramite);
	$refacturacion = $Comprobacion->Get_Dato("co_refacturar");
	$co_id		   = $Comprobacion->Get_Dato("co_id");
	$co_tramite    = $Comprobacion->Get_dato("co_tramite");
    $co_fecha_registro = $Comprobacion->Get_dato("co_fecha_registro");
    $motivo            = $Comprobacion->Get_dato("co_motivo");
    $observaciones     = $Comprobacion->Get_dato("co_observaciones");
    $co_subtotal       = $Comprobacion->Get_dato("co_subtotal");
    $co_iva            = $Comprobacion->Get_dato("co_iva");
    $co_total          = $Comprobacion->Get_dato("co_total");
    $co_total_aprobado = $Comprobacion->Get_dato("co_total_aprobado");
    $observaciones     = $Comprobacion->Get_dato("co_observaciones");
    $co_cc_clave       = $Comprobacion->Get_dato("co_cc_clave");      
    $total_pendiente   = $co_total - $co_total_aprobado;    
    
    // datos de la solicitud y el centro de costos
    $cnn = new conexion();
    $Vsql = "SELECT * FROM solicitud_viaje WHERE sv_tramite = $co_tramite";
	$rstSol    = $cnn->consultar($Vsql);
	$filaSol = mysql_fetch_assoc($rstSol); 
    $IdSolc          = $filaSol['sv_id'];   
    $MontoSolc       = $filaSol['sv_anticipo'];       
    
    // datos del centro de costos
    $Vsql = "SELECT * FROM cat_cecos WHERE cc_id = $co_cc_clave";
	$rstSol    = $cnn->consultar($Vsql);
	$filaSol = mysql_fetch_assoc($rstSol);
    $cc_id          = $filaSol['cc_id'];    
    $cc_centrocostos=$filaSol['cc_centrocostos'];
    $cc_nombre      =$filaSol['cc_nombre'];       
    
    // Carga presupuesto
    $Ceco=new CentroCosto();
    $presupuesto_disponible = $Ceco->get_presupuesto_disponible($cc_id, $co_fecha_registro);    
        
} 

// Regresa a la pantalla anterior
else if(isset($_POST["volver"])) {
    header("Location: ./index.php");
    die();
} 

// Autoriza los campos que el usuario haya seleccionado,
// Si todos los campos se autorizaron se manda a APROBACION, si queda algun
// campo por autorizar se le regresa al usuario para que la complete
else if(isset($_POST["autorizar"]))
{
    // Datos de la autorizacion
    $idTramite	       = $_POST["idTramite"];
    $observaciones	   = $_POST["observaciones"];  

    // Carga datos del tramite
    $Tramite	= new Tramite();
    $Tramite->Load_Tramite($idTramite);
    $fecha_Comprobacion = $Tramite->Get_dato("t_fecha_registro");
    $aprobador          = $Tramite->Get_dato("t_dueno");
    $t_etapa_actual     = $Tramite->Get_dato("t_etapa_actual"); 
    $t_flujo            = $Tramite->Get_dato("t_flujo"); 
    $iniciador          = $Tramite->Get_dato("t_iniciador");
    
    // Carga datos de la comprobacion
    $Comprobacion = new Comprobacion();
    $Comprobacion->Load_Comprobacion($idTramite);
    $TotalComp          = $Comprobacion->Get_dato("co_total");
    $TipoComp           = $Comprobacion->Get_dato("co_tipo");           // Tipo de comprobacion: 1: Viaje, 2: Amex, 3: Caja Chica              
    $cc_id_comprobacion = $Comprobacion->Get_dato("co_cc_clave");       // El ceco se captura en el alta de la comprobacion       
    $co_fecha_registro  = $Comprobacion->Get_dato("co_fecha_registro"); // Fecha de la comprobacion
    $co_id              = $Comprobacion->Get_dato("co_id");
                            
    // Datos del aprobador
    $Usuario		= new Usuario();
    $DatosAprobador = $Usuario->Load_Usuario_By_ID($aprobador);
    $NumAprobador   = $Usuario->Get_dato("u_usuario");
    
    // Actualiza las observaciones de la comprobacion
    $Comprobacion->Actualiza_Observaciones_Comprobacion($co_id, $observaciones);
    
    // Obtiene el no. de conceptos registrados en la comprobacion
    $cnn = new conexion();
    $query	= sprintf("SELECT dc_estatus AS dc_estatus FROM detalle_comprobacion where dc_comprobacion=%s", $co_id);
    //error_log($query);
    $rst = $cnn->consultar($query);
    $no_conceptos = mysql_num_rows($rst);

    // Itera sobre los conceptos para ver cuales han sido autorizados por CXP
    for ($i = 0; $i < $no_conceptos; $i++) {
       $dc_id = $_POST['chk_part_'.$i];        
       if ($dc_id!=""){
          $Comprobacion->Aprueba_Partida_Comprobacion_CxP($dc_id);
       } 
    }
    
    // Checa si queda algun concepto pendiente de autorizar
    $query	= sprintf("SELECT COALESCE(count(1), 0) as total_conceptos_no_aprobados FROM detalle_comprobacion where dc_comprobacion=%s and dc_estatus=1", $co_id);
    $rst = $cnn->consultar($query);
    $fila = mysql_fetch_assoc($rst);
    $total_conceptos_no_aprobados = $fila['total_conceptos_no_aprobados'];
    
    // En este caso la comprobacion esta completa y lista para cerrarse
    if($total_conceptos_no_aprobados==0){
        
       // Aqui hace falta la logica para dividirlo en varios centros de costos.
        // Por el momento solo lo metemos en el que viene
        $sql = sprintf("INSERT INTO ceco_detalle ( ceco_detalle_ceco, ceco_detalle_cantidad, ceco_detalle_tramite ) 
                        VALUES ( '%s', '%s', '%s' )" , $cc_id_comprobacion, $TotalComp, $idTramite);
        $cnn = new conexion();
        $ceco_detalle_id = $cnn->insertar($sql);


                    /*
                            // Guarda detalle de centros de costos asignados
                            $filas = $_POST['rowCountCecos'];
                            $Estatus = 1;
                            if($filas != 0)
                            {
                                for($i=1;$i<=$filas;$i++)
                                {
                                    //cacha datos de montos repartidos centro de costos
                                    $Ceco=$_POST['ConceptoCeco'.$i];
                                    $Monto=$_POST['Porcentaje'.$i];
                                    // guarda detalle de CECOS en deco_detalle con el tramite de la comp
                                    if($Ceco != "")
                                    {
                                        $Vsql = "
                                            INSERT INTO ceco_detalle
                                            (
                                                ceco_detalle_ceco,
                                                ceco_detalle_cantidad,
                                                ceco_detalle_estatus,
                                                ceco_detalle_tramite
                                            )
                                            VALUES
                                            (
                                                '$Ceco',
                                                '$Monto',
                                                '$Estatus',
                                                '$idTramite');";
                                            $cnn = new conexion();
                                            $Res = $cnn->insertar($Vsql);
                                    }
                                }//for
                            }
                            else
                            {
                              //Solamente guarda el monto total al centro de costos por default que tiene el empleado... :-)
                              $Vsql = "
                                       INSERT INTO ceco_detalle
                                       (
                                          ceco_detalle_ceco,
                                          ceco_detalle_cantidad,
                                          ceco_detalle_estatus,
                                          ceco_detalle_tramite
                                       )
                                       VALUES
                                       (
                                          '$idCC',
                                          '$TotalComp',
                                          '$Estatus',
                                          '$idTramite');";
                                    $cnn = new conexion();
                                    $Res = $cnn->insertar($Vsql);
                            }
                            ##################
                    */
        
        // Envia el tramite a la etapa correcta basado en el flujo
        $mensaje = sprintf("La comprobaci&oacute;n <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%s</strong>.",
                                    $idTramite, $Usuario->Get_dato('nombre'));        
        
        $tramite = new Tramite();
        $tramite->EnviaMensaje($idTramite, $mensaje);
        if($t_flujo==FLUJO_COMPROBACION){
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_APROBADA, FLUJO_COMPROBACION, $iniciador);
                           
        } else if($t_flujo==FLUJO_COMPROBACION_TDC){
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_TDC_ETAPA_APROBADA, FLUJO_COMPROBACION_TDC, $iniciador);
            
        } else if($t_flujo==FLUJO_REEMBOLSO_CAJA_CHICA){
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_APROBADA, FLUJO_REEMBOLSO_CAJA_CHICA, $iniciador);
        }                                    
                  
        // Se modifica el campo de co_status_erp a 1 que quiere decir que ya esta autorizado
        $Csql = "UPDATE comprobaciones SET co_status_erp = $ESTATUS_ERP_AUTORIZADO WHERE co_mi_tramite = '$idTramite';";                                                                
        $RCsql = $cnn->insertar($Csql);
        
        $VSql = "UPDATE tramites SET t_fecha_cierre = NOW();";
        $Rvsql = $cnn->insertar($VSql);                       
                  
        header("Location: ./index.php?action=autorizar");        
        
    } else {

        //
        // En este caso la comprobacion no esta completa y se envia al usuario
        //

        // Envia el tramite a la etapa correcta basado en el flujo
        $mensaje = sprintf("La comprobaci&oacute;n <strong>%05s</strong> ha sido <strong>REGRESADA</strong> por <strong>%s</strong>.",
                                    $idTramite, $Usuario->Get_dato('nombre'));        
        
        $tramite = new Tramite();
        $tramite->EnviaMensaje($idTramite, $mensaje);
        if($t_flujo==FLUJO_COMPROBACION){
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_APROBADA_PARCIAL, FLUJO_COMPROBACION, $iniciador);
                           
        } else if($t_flujo==FLUJO_COMPROBACION_TDC){
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_TDC_ETAPA_APROBADA_PARCIAL, FLUJO_COMPROBACION_TDC, $iniciador);
            
        } else if($t_flujo==FLUJO_REEMBOLSO_CAJA_CHICA){
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_APROBADA_PARCIAL, FLUJO_REEMBOLSO_CAJA_CHICA, $iniciador);
        }                                    
                  
        header("Location: ./index.php?action=regresar");
    }

// Rechaza la solicitud de manera definitiva	
} else if(isset($_POST["rechazar"])){ 
    
    // Datos del tramite
    $idTramite	       = $_POST["idTramite"];
    $observaciones	   = $_POST["observaciones"];    
        
    // Actualiza el campo de observaciones
    $Csv=new C_SV();    
    $Csv->Load_Solicitud_tramite($idTramite);
    $Csv->Modifica_Observaciones($idTramite, $observaciones, FLUJO_COMPROBACION);        
    
    // Envia el tramite a cancelacion
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);    
    $iniciador      = $tramite->Get_dato("t_iniciador");
    $aprobador      = $tramite->Get_dato("t_dueno");
    $t_etapa_actual = $tramite->Get_dato("t_etapa_actual"); 
    $t_flujo        = $tramite->Get_dato("t_flujo");    
    
    $usuarioAprobador = new Usuario();
    $usuarioAprobador->Load_Usuario_By_ID($aprobador);   
    $mensaje = sprintf("La comprobaci&oacute;n <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por <strong>%s</strong>.",
                            $idTramite, $usuarioAprobador->Get_dato('nombre'));
    $tramite->EnviaMensaje($idTramite, $mensaje); // Notese que el mensaje se envia antes que se cambia la etapa 
    if($t_flujo==FLUJO_COMPROBACION){
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_RECHAZADA, FLUJO_COMPROBACION, $iniciador);
                       
    } else if($t_flujo==FLUJO_COMPROBACION_TDC){
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_TDC_ETAPA_RECHAZADA, FLUJO_COMPROBACION_TDC, $iniciador);
        
    } else if($t_flujo==FLUJO_REEMBOLSO_CAJA_CHICA){
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_RECHAZADA, FLUJO_REEMBOLSO_CAJA_CHICA, $iniciador);
    }       
                               
	header("Location: ./index.php?action=rechazar");
}  
?>

<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>

<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<!--<script language="JavaScript" src="../../lib/js/jquery/jquery.tableEditor.js" type="text/javascript"></script>-->
<script language="JavaScript" src="../../lib/js/jquery/jquery.fadeSliderToggle.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
<script language="javascript">
function conceptos()
{
    var filasTabla=document.getElementById("tableConceptos").rows.length;
    alert(filasTabla-1);
}

 function verificaImputacion(){
ActivaCeco();
var frm=document.action_form;
       if (frm.ceco_chk.checked){
               validaCeco=1;
               document.getElementById("combo_cecos").style.display  = "block";
       }else{
               validaCeco=0;
               document.getElementById("combo_cecos").style.display  = "none";
       }
}
//validación campos numericos
function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}

</script>

<script type="text/javascript">

$(document).ready(function(){
	//Seleccionar todos los Checkbox de la tabla
	$("input[name=checktodos]").change(function(){
		$('input[class=check_lote]').each( function() {
			if($("input[name=checktodos]:checked").length == 1){
				this.checked = true;
				ActivaCeco();
			} else {
				this.checked = false;
				ActivaCeco();
			}
		});
	});
});

function ActivaCeco()
{
    $("#MontosMuestra").css("display", "none");
    $("#centroDefault").css("display", "block");
    id = parseInt($("#descrip_comprobacion_table").find("tr:last").find("td").eq(0).html());
    seleccion = $("input[class=check_lote]:checked").length;
    tipo=0;
    if(seleccion == id)
    {
        $("#ceco_chk").removeAttr("disabled");
        var isChk = $('#ceco_chk').is(':checked'); // return true o false
        if(isChk)
        {
           $("#autorizar").attr("disabled", "disabled");
           $("#MontosMuestra").css("display", "block");
           $("#centroDefault").css("display", "none");
        }
        else
        {
           $("#autorizar").removeAttr("disabled");
           $("#MontosMuestra").css("display", "none");
           $("#centroDefault").css("display", "block");
        }
    }
    else
    {
        $('input[name=ceco_chk]').attr('checked', false);
        $("#ceco_chk").attr("disabled", "disabled");
    }
}
</script>

<table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">

    <tr>
        <td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n de la Comprobaci&oacute;n</strong></div></td>
    </tr>

    <tr>
        <td colspan="5">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="6%"><div align="right">Tr&aacute;mite:</div></td>
        <td width="18%"><strong><?php echo $idTramite; ?></strong></td>
        <td width="2%">&nbsp;</td>
        <td width="10%"><div align="right">Fecha de creaci&oacute;n:</div></td>
        <td width="14%"><div align="left"><strong><?php echo $co_fecha_registro ?></strong></div></td>
    </tr>
    
    <tr>
        <td ><div align="right">Solicitante:</div></td>
        <td ><div align="left"><strong><?php echo $nombreEmpleado;?></strong></div></td>
        <td >&nbsp;</td>
        <td ><div align="right">Autorizador:</div></td>
        <td ><div align="left"><strong><?php echo $UsuarioAprobador->Get_Dato('u_nombre');?></strong></div></td>
    </tr>						
                
    <tr>
        <td ><div align="right">Motivo:</div></td>
        <td ><div align="left"><strong><?php echo $motivo;?></strong></div></td>
        <td >&nbsp;</td>
        <td ><div align="right">Referencia:</div></td>
        <td ><div align="left"><strong><?php echo $referencia;?></strong></div></td>    
    </tr>

    <tr>
        <td colspan="5"><hr>
        </td>
    </tr>                
                
    <tr>
        <td ><div align="right">Departamento:</div></td>
        <td ><div align="left"><strong><?php echo $cc_centrocostos . " - " . $cc_nombre;?></strong></div></td>
        <td >&nbsp;</td>
        <?php if($co_tipo==2){ ?> 
        <td ><div align="right">Tarjeta de Cr&eacute;dito</div></td>
        <td ><div align="left"><strong><?php echo $notarjetacredito;?></strong></div></td>    
        <? } else { ?>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <? } ?>
    </tr>            
        
</table>

<br />
<form name="action_form" action="comprobacion_cxp.php" method="post">
<table id="descrip_comprobacion_table" class="tablesorter" cellspacing="1">
  <thead>
    <tr>
     <th width="3%">No.</th>
      <th width="8%">Concepto</th>
      <th width="9%">Comentario</th>
      <th width="10%">No.Asistentes</th>
      <th width="9%">Proveedor</th>
      <th width="7%">Factura</th>
      <th width="10%">Subtotal </th>
      <th width="6%">Divisa</th>
      <th width="4%">Tasa</th>
      <th width="4%">IVA</th>
      <th width="6%">Total</th>
      <th width="13%">Total Aprobado CxP</th>
      <th width="10%">Acci&oacute;n
      <input name="checktodos" type="checkbox" onclick=" ActivaCeco(); verificaImputacion();"/>
      </th>
    </tr>
  </thead>
  <tbody>
    <!-- cuerpo tabla-->
    <?php 
        #$query=sprintf("SELECT * FROM det_comps_pend where dc_comprobacion in (select co_id from comprobaciones where co_mi_tramite=%s) and estatus=1;",$tramite_comprobacion);
        $query  = sprintf("select co_tipo from comprobaciones where co_mi_tramite='%s'",$tramite_comprobacion) ;
        $rst    = $cnn->consultar($query);
        
        
        $fila2	= mysql_fetch_assoc($rst);
        if($fila2['co_tipo']==2)
        {
            $query = "SELECT
                         d.dc_id AS dc_id,
                         (SELECT
                             c.cp_clasificacion AS cp_clasificacion
                         FROM
                             cat_conceptos c
                         WHERE
                             (c.dc_id = d.dc_concepto)) AS clasificacion,
                         (SELECT
                             c.cp_deducible AS cp_deducible
                         FROM
                             cat_conceptos c
                         WHERE
                             (c.dc_id = d.dc_concepto)) AS deducible,
                         (SELECT
                             c.cp_concepto AS cp_concepto
                         FROM
                             cat_conceptos c
                         WHERE (c.dc_id = d.dc_concepto)) AS concepto,
                         d.dc_comensales AS asistentes,
                         d.dc_rfc AS rfc,
                         d.dc_monto AS monto,
                         d.dc_porcentaje_iva AS porcentaje,
                         d.dc_iva AS iva,
                         d.dc_total AS total,
                         d.dc_total_aprobado AS total_aprobado,
                         d.dc_descripcion AS dc_descripcion,
                         d.dc_tipocambio AS tasa,
                         d.dc_divisa AS dc_divisa,
                         d.dc_responsable AS dc_responsable,
                         d.dc_comprobacion AS dc_comprobacion,
                         d.dc_folio_factura AS factura,
                         d.dc_estatus AS estatus,
                         d.dc_info_extra as dc_info_extra
                     FROM
                         detalle_comprobacion d
                     WHERE
                         -- (d.dc_estatus = 1) AND
                         dc_comprobacion = $co_id";
        }
        else
        {
             $query = "SELECT
                         d.dc_id AS dc_id,
                         (SELECT
                             c.cp_clasificacion AS cp_clasificacion
                         FROM
                             cat_conceptos c
                         WHERE
                             (c.dc_id = d.dc_concepto)) AS clasificacion,
                         (SELECT
                             c.cp_deducible AS cp_deducible
                         FROM
                             cat_conceptos c
                         WHERE
                             (c.dc_id = d.dc_concepto)) AS deducible,
                         (SELECT
                             c.cp_concepto AS cp_concepto
                         FROM
                             cat_conceptos c
                         WHERE (c.dc_id = d.dc_concepto)) AS concepto,
                         d.dc_comensales AS asistentes,
                         d.dc_rfc AS rfc,
                         d.dc_monto AS monto,
                         d.dc_porcentaje_iva AS porcentaje,
                         d.dc_iva AS iva,
                         d.dc_total AS total,
                         d.dc_total_aprobado AS total_aprobado,
                         d.dc_total_aprobado_cxp AS total_aprobado_cxp,                         
                         d.dc_descripcion AS dc_descripcion,
                         d.dc_tipocambio AS tasa,
                         d.dc_divisa AS dc_divisa,
                         d.dc_responsable AS dc_responsable,
                         d.dc_comprobacion AS dc_comprobacion,
                         d.dc_folio_factura AS factura,
                         d.dc_estatus AS estatus,
                         d.dc_info_extra as dc_info_extra
                     FROM
                         detalle_comprobacion d
                     WHERE
                         -- (d.dc_estatus = 1) AND
                         dc_comprobacion = $co_id";
        }
        
		$cont1=1;
		$cont2=0;
		$cont3=0;
		$cont4=0;
		$rst	=$cnn->consultar($query);
		$rows 	=mysql_num_rows($rst);
		
        $no_fila = 0;
		while($fila = mysql_fetch_assoc($rst)){
			$fila["monto"] = number_format($fila["monto"],2,".",",");
			$fila["iva"] = number_format($fila["iva"],2,".",",");
			$fila["total"] = number_format($fila["total"],2,".",",");
			$fila["total_aprobado"]	= number_format($fila["total_aprobado"],2,".",",");
			
			$No_Comprobacion = $fila['dc_comprobacion'];		
			$cont2 = $cont2 + 1;
			$cont3 = $cont3 + 1;
			$cont4 = $cont4 + 1;
						
			echo "<tr><td>".$cont1++;
			echo "<td>".$fila["concepto"]."</td>";
			if($fila["dc_info_extra"]!="" || !empty($fila["dc_info_extra"])){
			echo "<td>".$fila["dc_info_extra"]."</td>";
				}else{
			echo "<td>Sin Comentario</td>";
				}
			echo "<td>".$fila["asistentes"]."</td>";
            if($fila["rfc"]!=''){
                echo "<td>".$fila["rfc"]."</td>";
			}else{
                echo "<td>Sin Factura</td>";
			}   
            if($fila["factura"]!=''){
                echo "<td>".$fila["factura"]."</td>";
			}else{
                echo "<td>Sin Factura</td>";
			}
			?>
            <td>
               <div id="monto_conceptos<?php echo $cont2?>">
                  $<?php echo $fila["monto"]?>
                  <SPAN STYLE="font-size: 10px; cursor: pointer; color:#ff0000" onClick="EditaMontos(<?echo $fila["dc_id"];?>,<?echo $cont2?>);">
                  <!--[Editar Monto]</SPAN>
                  <input type="hidden" name="txtmonto<?php //echo $cont2?>" id="txtmonto<?php //echo $cont2?>" value="<?php //echo $fila['monto']?>">--->
               </div>
            </td>
            <?
			echo "<td>".$fila["dc_divisa"]."</td>";
			echo "<td>$".$fila["tasa"]."</td>";
			echo "<td>".$fila["porcentaje"]."% / $". $fila["iva"]."</td>";
			echo "<td>$".$fila["total"]."</td>";
            echo "<td>$".$fila["total_aprobado_cxp"]."</td>";
            ?>
            <td>
               <div align='center'>
               <?
               if($fila["estatus"]==1)
               {
               ?>
                  <input type='checkbox' name='chk_part_<?php echo $no_fila; ?>' value="<?php echo $fila['dc_id']?>" class='check_lote' onclick='ActivaCeco(); verificaImputacion();'>Seleccionar                            
               <?
               }
               else
               {
                  echo "Partida Aprobada";
               }
               ?>
               </div>
            </td>
            </tr>
       <?
       $no_fila = $no_fila+1;
       }?>
  </tbody>
</table>
<div align="right">
    <table>
    <tr><td><strong>Subtotal:&nbsp;</strong></td><td align='right'><?php echo format_pesos($co_subtotal);?></td></tr>
    <tr><td><strong>IVA:&nbsp;</strong></td><td align='right'><?php echo format_pesos($co_iva);?></td></tr>
    <tr><td><strong>Total:&nbsp;</strong></td><td align='right'><?php echo format_pesos($co_total);?></td></tr>
    <tr><td><strong><font color='#00CC00'>Aprobado:&nbsp;</font></strong></td><td align='right'><?php echo format_pesos($co_total_aprobado);?></td></tr>
    </table>
</div>

<br><br>
<table border="0" align="center">
       <tr>
           <td colspan="5" align="center">
              <div id="centroDefault"><b>Departamento al que se cargar&aacute; el monto:&nbsp;<br><?php echo "$cc_centrocostos - $cc_nombre";?></b></div>
           </td>
       </tr>
       <tr>
       </tr>
       <tr>
           <?php //Centro costos
               $Usu=new Usuario();
               $Usu->Load_Usuario_By_No_Empleado($_SESSION['empleado']);
               $idcentrocosto=$Usu->Get_dato('idcentrocosto');
           ?>
       </tr>
   </table>
<br><br>

<div align="center">Agregar observaciones:<br />
   <textarea cols="47" rows="5" wrap="physical" id="observaciones" name="observaciones"><?echo $observaciones?></textarea>
</div>
<br><br>
<tr><td>
<div align="center">
   <input type="submit" value="Autorizar" id="autorizar" name="autorizar" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
   <input type="submit" value="Volver" id="volver" name="volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
   <input type="submit" value="Rechazar" id="rechazar" name="rechazar"  style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
</div>
</div>
</td>
</tr>
</br>

<input type="hidden" id="idTramite" name="idTramite" value="<?php echo $idTramite; ?>" readonly="readonly" />
</table>
</form>
