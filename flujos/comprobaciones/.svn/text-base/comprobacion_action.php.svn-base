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
	$co_tipo	   = $Comprobacion->Get_Dato("co_tipo");    // 1 Viaje, 2 Amex, 3 Caja Chica
	$co_tramite    = $Comprobacion->Get_dato("co_tramite");
    $co_fecha_registro = $Comprobacion->Get_dato("co_fecha_registro");
    $motivo            = $Comprobacion->Get_dato("co_motivo");
    $co_subtotal       = $Comprobacion->Get_dato("co_subtotal");
    $co_iva            = $Comprobacion->Get_dato("co_iva");
    $co_total          = $Comprobacion->Get_dato("co_total");
    $co_total_aprobado = $Comprobacion->Get_dato("co_total_aprobado");
    $observaciones     = $Comprobacion->Get_dato("co_observaciones");
    $co_cc_clave       = $Comprobacion->Get_dato("co_cc_clave");    
    $total_pendiente   = $co_total - $co_total_aprobado;
    
    // datos de la solicitud y el centro de costos
    $cnn = new conexion();
    $Vsql = "SELECT * FROM solicitud_viaje WHERE sv_tramite = '".$co_tramite."'";
	error_log($Vsql);
    $rstSol    = $cnn->consultar($Vsql);
	$filaSol = mysql_fetch_assoc($rstSol); 
    $IdSolc          = $filaSol['sv_id'];   
    $MontoSolc       = $filaSol['sv_anticipo'];       
    
    // datos del centro de costos
    $Vsql = "SELECT * FROM cat_cecos WHERE cc_id = '".$co_cc_clave."'";
    error_log($Vsql);
    $rstSol    = $cnn->consultar($Vsql);
	$filaSol = mysql_fetch_assoc($rstSol);
    $cc_id          = $filaSol['cc_id'];    
    $cc_centrocostos=$filaSol['cc_centrocostos'];
    $cc_nombre      =$filaSol['cc_nombre'];    
    
    // Carga presupuesto
   // $Ceco=new CentroCosto();
   // $presupuesto_disponible = $Ceco->get_presupuesto_disponible($cc_id, $co_fecha_registro);    

} 

// Regresa a la pantalla anterior
else if(isset($_POST["volver"])) {
    header("Location: ./index.php");
    die();
} 

//
// Autoriza el gasto adicional de la comprobacion
//
else if(isset($_POST["autorizar"]))
{            
    // Obtiene el usuario actual de la sesion
    $idusuario_actual=$_SESSION["idusuario"];    
    
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
    $iniciador          =  $Tramite->Get_dato("t_iniciador");
    $siguiente_autorizador = $Tramite->Get_Siguiente_Autorizador($idTramite); 
    
    // Carga datos de la comprobacion
    $Comprobacion = new Comprobacion();
    $Comprobacion->Load_Comprobacion($idTramite);
    $TipoComp           = $Comprobacion->Get_dato("co_tipo");           // Tipo de comprobacion: 1: Viaje, 2: Amex, 3: Caja Chica              
    $cc_id_comprobacion = $Comprobacion->Get_dato("co_cc_clave");       // El ceco se captura en el alta de la comprobacion       
    $co_fecha_registro  = $Comprobacion->Get_dato("co_fecha_registro"); // Fecha de la comprobacion
    $co_id              = $Comprobacion->Get_dato("co_id");
    $co_total           = $Comprobacion->Get_dato("co_total");
    $co_total_aprobado  = $Comprobacion->Get_dato("co_total_aprobado");
    $co_pendiente       = $co_total - $co_total_aprobado;   // Cuanto falta por aprobar
                            
    // Datos del aprobador
    $Usuario		= new Usuario();
    $DatosAprobador = $Usuario ->Load_Usuario_By_ID($aprobador);
    $NumAprobador   = $Usuario ->Get_dato("u_usuario");
    
    // Actualiza las observaciones de la comprobacion
    $Comprobacion->Actualiza_Observaciones_Comprobacion($co_id, $observaciones);
    
    if($siguiente_autorizador!="-1"){
    
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador); 
        $usuarioAprobadorAdicional = new Usuario();
        $usuarioAprobadorAdicional->Load_Usuario_By_ID($siguiente_autorizador); 

        // Envia el tramite a la etapa correcta basado en el flujo
        $tramite = new Tramite();
        
        if($t_flujo==FLUJO_COMPROBACION){

            $mensaje = sprintf("La comprobaci&oacute;n <strong>%05s</strong> ha sido <strong>APROBADA</strong> por <strong>%s</strong> y enviada a <strong>%s</strong> para su aprobaci&oacute;n adicional.",
                    $idTramite, $usuarioAprobador->Get_dato('nombre'), $usuarioAprobadorAdicional->Get_dato('nombre'));            

            $tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_APROBACION, FLUJO_COMPROBACION, $siguiente_autorizador);                           
        } else if($t_flujo==FLUJO_COMPROBACION_TDC){

            $mensaje = sprintf("El reembolso TDC <strong>%05s</strong> ha sido <strong>APROBADO</strong> por <strong>%s</strong> y enviada a <strong>%s</strong> para su aprobaci&oacute;n adicional.",
                    $idTramite, $usuarioAprobador->Get_dato('nombre'), $usuarioAprobadorAdicional->Get_dato('nombre'));            

            $tramite->Modifica_Etapa($idTramite, COMPROBACION_TDC_ETAPA_APROBACION, FLUJO_COMPROBACION_TDC, $siguiente_autorizador);            
        } else if($t_flujo==FLUJO_REEMBOLSO_CAJA_CHICA){

            $mensaje = sprintf("El reembolso de Caja Chica <strong>%05s</strong> ha sido <strong>APROBADO</strong> por <strong>%s</strong> y enviado a <strong>%s</strong> para su aprobaci&oacute;n adicional.",
                    $idTramite, $usuarioAprobador->Get_dato('nombre'), $usuarioAprobadorAdicional->Get_dato('nombre'));            

            $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_APROBACION, FLUJO_REEMBOLSO_CAJA_CHICA, $siguiente_autorizador);
        }               
        $tramite->AgregaSiguienteAutorizador($idTramite, $aprobador);
        
        // Manda el mensaje a las 3 partes de la transaccion
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $siguiente_autorizador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $iniciador);           
                
    } else {
                
        // Aprueba el total
        $CC = "UPDATE comprobaciones SET co_total_aprobado = co_total WHERE co_mi_tramite = '$idTramite'";
        $cnn=new conexion();
        $cnn->insertar($CC);        
        
        // Actualiza el presupuesto
        $Cc=new CentroCosto();
        $Cc->resta_presupuesto($cc_id_comprobacion, $co_pendiente, $co_fecha_registro);       

        // Obtiene el nombre del usuario CxP que le corresponde a esta comprobacion
        $u = new Usuario();            
        $aprobadorCxP = $u->buscaAprobadorCxPParaComprobacion($cc_id_comprobacion);
        
        // Envia el tramite directo a CxP
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobadorCxP);
        
        // Envia el tramite a la etapa correcta basado en el flujo
        $tramite = new Tramite();
        
        if($t_flujo==FLUJO_COMPROBACION){
            
            $mensaje = sprintf("El gasto adicional de la comprobaci&oacute;n <strong>%05s</strong> ha sido <strong>AUTORIZADO</strong> y asignada a <strong>%s</strong> para su revisi&oacute;n",
                                        $idTramite, $usuarioAprobador->Get_dato('nombre'));            
                
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_CXP, FLUJO_COMPROBACION, $aprobadorCxP);                           
        } else if($t_flujo==FLUJO_COMPROBACION_TDC){
            
            $mensaje = sprintf("El reembolso de TDC <strong>%05s</strong> ha sido <strong>AUTORIZADO</strong> y asignado a <strong>%s</strong> para su revisi&oacute;n",
                                        $idTramite, $usuarioAprobador->Get_dato('nombre'));            
            
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_TDC_ETAPA_CXP, FLUJO_COMPROBACION_TDC, $aprobadorCxP);            
        } else if($t_flujo==FLUJO_REEMBOLSO_CAJA_CHICA){
            
            $mensaje = sprintf("El reembolso de Caja Chica <strong>%05s</strong> ha sido <strong>AUTORIZADO</strong> y asignado a <strong>%s</strong> para su revisi&oacute;n",
                                        $idTramite, $usuarioAprobador->Get_dato('nombre'));                
                            
            $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_CXP, FLUJO_REEMBOLSO_CAJA_CHICA, $aprobadorCxP);
        }  
        $tramite->AgregaSiguienteAutorizador($idTramite, $aprobador);
        
        // Manda el mensaje a las 3 partes de la transaccion
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobadorCxP); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $iniciador);           
    }
    
    header("Location: ./index.php?action=autorizar");
    die();    

//
// Rechaza el gasto adicional
//

} else if(isset($_POST["rechazar"])){ 
    
    // Datos del tramite
    $idTramite	       = $_POST["idTramite"];
    $observaciones	   = $_POST["observaciones"];     
    
    // Carga datos del tramite
    $Tramite	= new Tramite();
    $Tramite->Load_Tramite($idTramite);
    $fecha_Comprobacion = $Tramite->Get_dato("t_fecha_registro");
    $aprobador          = $Tramite->Get_dato("t_dueno");
    $t_etapa_actual     = $Tramite->Get_dato("t_etapa_actual"); 
    $t_flujo            = $Tramite->Get_dato("t_flujo"); 
    $iniciador          =  $Tramite->Get_dato("t_iniciador");
    
    // Carga datos de la comprobacion
    $Comprobacion = new Comprobacion();
    $Comprobacion->Load_Comprobacion($idTramite);
    $TipoComp           = $Comprobacion->Get_dato("co_tipo");           // Tipo de comprobacion: 1: Viaje, 2: Amex, 3: Caja Chica              
    $cc_id_comprobacion = $Comprobacion->Get_dato("co_cc_clave");       // El ceco se captura en el alta de la comprobacion       
    $co_fecha_registro  = $Comprobacion->Get_dato("co_fecha_registro"); // Fecha de la comprobacion
    $co_id              = $Comprobacion->Get_dato("co_id");
    $co_total           = $Comprobacion->Get_dato("co_total");
    $co_total_aprobado  = $Comprobacion->Get_dato("co_total_aprobado");
    $co_pendiente       = $co_total - $co_total_aprobado;   // Cuanto falta por aprobar
                            
    // Datos del aprobador
    $Usuario		= new Usuario();
    $DatosAprobador = $Usuario ->Load_Usuario_By_ID($aprobador);
    $NumAprobador   = $Usuario ->Get_dato("u_usuario");
    
    // Actualiza las observaciones de la comprobacion
    $Comprobacion->Actualiza_Observaciones_Comprobacion($co_id, $observaciones);
    
    // Obtiene el nombre del usuario CxP que le corresponde a esta comprobacion
    $u = new Usuario();            
    $aprobador = $u->buscaAprobadorCxPParaComprobacion($cc_id_comprobacion);
    
    // Envia el tramite directo a CxP
    $usuarioAprobador = new Usuario();
    $usuarioAprobador->Load_Usuario_By_ID($aprobador);
    $mensaje = sprintf("El gasto adicional de la comprobaci&oacute;n <strong>%05s</strong> ha sido <strong>RECHAZADO</strong> y asignada a <strong>%s</strong> para su revisi&oacute;n",
                                $idTramite, $usuarioAprobador->Get_dato('nombre'));
    
    // Envia el tramite a la etapa correcta basado en el flujo
    $tramite = new Tramite();
    $tramite->EnviaMensaje($idTramite, $mensaje);    
    if($t_flujo==FLUJO_COMPROBACION){
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_ETAPA_CXP, FLUJO_COMPROBACION, $aprobador);                           
    } else if($t_flujo==FLUJO_COMPROBACION_TDC){
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_TDC_ETAPA_CXP, FLUJO_COMPROBACION_TDC, $aprobador);            
    } else if($t_flujo==FLUJO_REEMBOLSO_CAJA_CHICA){
        $tramite->Modifica_Etapa($idTramite, COMPROBACION_CAJA_CHICA_ETAPA_CXP, FLUJO_REEMBOLSO_CAJA_CHICA, $aprobador);
    }                                    
        
    header("Location: ./index.php?action=rechazar");
    die();
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
<div align="center">
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
    <tr>
        <td ><div align="right">Presupuesto Disponible:</div></td>
        <td ><div align="left"><strong><?php echo "$ ". number_format($presupuesto_disponible);?></strong></div></td>
        <td >&nbsp;</td>
        
        <td ><div align="right">Presupuesto Restante:</div></td>
        <td ><div align="left"><strong><?php echo "$ ". number_format($presupuesto_disponible - $total_pendiente);?></strong></div></td>                
    </tr>
</table>

<br />
<form name="action_form" action="comprobacion_action.php" method="post">
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
    </tr>
  </thead>
  <tbody>
    <!-- cuerpo tabla-->
    <?php 
        if($co_tipo==2)
        {//                         d.dc_total_aprobado AS total_aprobado,
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
                         d.dc_descripcion AS dc_descripcion,
                         d.dc_tipocambio AS tasa,
                         d.dc_divisa AS dc_divisa,
                         d.dc_responsable AS dc_responsable,
                         d.dc_comprobacion AS dc_comprobacion,
                         d.dc_folio_factura AS factura,
                         d.dc_estatus AS estatus,
                         d.dc_info_extra AS dc_info_extra                         
                     FROM
                         detalle_comprobacion d
                     WHERE
                         (d.dc_estatus = 1) AND
                         dc_comprobacion = '".$co_id."'";
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
                         d.dc_descripcion AS dc_descripcion,
                         d.dc_tipocambio AS tasa,
                         d.dc_divisa AS dc_divisa,
                         d.dc_comprobacion AS dc_comprobacion,
                         d.dc_folio_factura AS factura,
                         d.dc_estatus AS estatus
                     FROM
                         detalle_comprobacion d
                     WHERE
                         (d.dc_estatus = 1) AND
                         dc_comprobacion = '".$co_id."'";
        }
        
        //error_log($query);
        
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
			<tr>
            <td>
               <div id="monto_conceptos<?php echo $cont2?>">
                  $<?php echo $fila["monto"]?>
                  <SPAN STYLE="font-size: 10px; cursor: pointer; color:#ff0000" onClick="EditaMontos(<?echo $fila["dc_id"];?>,<?echo $cont2?>);">
                  <!--[Editar Monto]</SPAN>
                  <input type="hidden" name="txtmonto<?php //echo $cont2?>" id="txtmonto<?php //echo $cont2?>" value="<?php //echo $fila['monto']?>">--->
                  </SPAN>
               </div>
            </td>
            <?
			echo "<td>".$fila["dc_divisa"]."</td>";
			echo "<td>$".$fila["tasa"]."</td>";
			echo "<td>".$fila["porcentaje"]."% / $". $fila["iva"]."</td>";
			echo "<td>$".$fila["total"]."</td>";
            ?>
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
    <tr><td><strong><font color='#00CC00'>Aprobado Anteriormente:&nbsp;</font></strong></td><td align='right'><?php echo format_pesos($co_total_aprobado);?></td></tr>
    <tr><td><strong><font color='#FF0000'>Pendiente de Aprobar:&nbsp;</font></strong></td><td align='right'><?php echo format_pesos($total_pendiente);?></td></tr>
    </table>
</div>
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
<table>
<tr>
<td>
<div align="center">
   <input type="submit" value="Autorizar" id="autorizar" name="autorizar" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
   <input type="submit" value="Volver" id="volver" name="volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
   <input type="submit" value="Rechazar" id="rechazar" name="rechazar"  style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
</div>
</td>
</tr>
<tr>
	<td><input type="hidden" id="idTramite" name="idTramite" value="<?php echo $idTramite; ?>" readonly="readonly" /></td>
</tr>
</table>
</form>
</div>
