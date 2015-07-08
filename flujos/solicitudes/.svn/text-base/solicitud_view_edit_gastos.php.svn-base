<?php	
    require_once "../../Connections/fwk_db.php";
    require_once "$RUTA_A/functions/Tramite.php";
    require_once "$RUTA_A/functions/Usuario.php";
    require_once("$RUTA_A/functions/utils.php");
    
    //$empleado		= $_SESSION["idusuario"];
    $cnn			= new conexion();
    $aux= array();
    
	if(isset($_GET['id'])){
	    $query="SELECT sg_id, t_iniciador, t_id, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y') AS fecha_registro, t_etiqueta, sg_monto, sg_divisa, 
			sg_monto_pesos, 
			IF(sg_ciudad = '', 'N/A', sg_ciudad) AS sg_ciudad, sg_observaciones, DATE_FORMAT(sg_fecha_gasto,'%d/%m/%Y') AS fecha_gasto, 
			IF(sg_lugar = '', 'N/A', sg_lugar) AS sg_lugar, sg_requiere_anticipo, 
			sg_concepto, t_dueno, cc_id, cc_centrocostos, t_etapa_actual, t_flujo, cc_nombre, t_delegado, et_etapa_nombre, nombre, div_nombre, 
			cp_concepto 
			FROM solicitud_gastos as sg
			INNER JOIN tramites as t ON (t.t_id = sg.sg_tramite) 
			INNER JOIN empleado ON (idfwk_usuario = t_iniciador) 
			LEFT JOIN cat_cecos as cc ON (sg.sg_ceco = cc.cc_id) 
			INNER JOIN etapas ON (et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo) 
			LEFT JOIN divisa ON (div_id = sg_divisa) 
			INNER JOIN cat_conceptos ON (cp_id = sg_concepto) 
			WHERE t_id = {$_GET['id']}";
	}
    $rst=$cnn->consultar($query);
	//error_log("--->>Consulta Solicitud de Gastos: ".$query);	
		
    while($datos=mysql_fetch_assoc($rst)){
        array_push($aux,$datos);
    }
	
    // Datos del tramite
    $id_Solicitud = mysql_result($rst,0, "sg_id");
    $iniciador = mysql_result($rst,0, "t_iniciador");
    $iniciadorT = mysql_result($rst,0, "t_iniciador");
    $t_id = mysql_result($rst,0, "t_id");
    $fecha_tramite = mysql_result($rst,0, "fecha_registro");
    $motivo = mysql_result($rst,0, "t_etiqueta");
    $fechagasto = mysql_result($rst,0, "fecha_gasto");
    $lugar = mysql_result($rst,0, "sg_lugar");
    $dueno = mysql_result($rst,0,"t_dueno");
	$monto = mysql_result($rst,0,"sg_monto");
	$divisa = mysql_result($rst,0,"div_nombre");
	$monto_pesos = mysql_result($rst,0,"sg_monto_pesos");
	$ciudad = mysql_result($rst,0,"sg_ciudad");
    $observaciones = mysql_result($rst,0,"sg_observaciones");
    $CentroCostoId = mysql_result($rst,0,"cc_id");
    $cc_centrocostos = mysql_result($rst,0,"cc_centrocostos");
    $t_etapa = mysql_result($rst,0,"t_etapa_actual"); 
    $t_etapa_nombre = mysql_result($rst,0,"et_etapa_nombre");
    $t_flujo = mysql_result($rst,0,"t_flujo");
    $cc_nombre = mysql_result($rst,0,"cc_nombre");
    $nombreUsuarioIniciador = mysql_result($rst, 0, "nombre");
    $requiereAnticipo = mysql_result($rst, 0, "sg_requiere_anticipo");
    $sg_concepto = mysql_result($rst, 0, "cp_concepto");
    $idConcepto = mysql_result($rst, 0, "sg_concepto");
    $t_delegado = mysql_result($rst, 0, "t_delegado");
    
    // Traerá la ruta de autorización de la solicitud correspondiente
    $rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = $rutaAutorizacion->getNombreAutorizadores($t_id);
    
    // Carga de Invitados
    $comensales = new Comensales();
    $invitados = $comensales->Load_comensales_solicitud_by_tramite($t_id);
    
    // Verificamos si la solicitud fue realizada por un delegado; de ser asï¿½ imprimiremos el nombre de los involucrados
    if($t_delegado != 0){
    	$duenoActual = new Usuario();
    	$duenoActual->Load_Usuario_By_ID($t_delegado);
    	$nombredelegado = $duenoActual->Get_dato('nombre');
    	$nombreUsuarioIniciador = "<font color='#0000CA'>".$nombredelegado."</font>".strtoupper(" en nombre de: ").$nombreUsuarioIniciador;
    }
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html>
    <head>
    <script type="text/javascript" src="../../lib/js/jquery/jquery-1.3.2.js" ></script>
    <script type="text/javascript" src="../../lib/js/jquery/jquery.date_input.js" ></script>  
    <script type="text/javascript" src="../../lib/js/jquery/jquery.autocomplete.js" ></script>
    <script type="text/javascript" src="../../lib/js/jquery/jquery.bgiframe.js" ></script>
    <script type="text/javascript" src="../../lib/js/dom-drag.js" ></script>
    <script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script>
    <script type="text/javascript" src="../../lib/js/formatNumber.js" ></script>
    <script type="text/javascript" src="../../lib/js/withoutReloading.js" ></script>
    <script type="text/javascript" src="../comprobaciones/js/backspaceGeneral.js" ></script>
    <script type="text/javascript" src="js/configuracionSolicitudView.js" ></script>
	<script type="text/javascript" src="js/cargarDatos.js" ></script>
	
    <link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
    <style>
        .style1 {color: #FF0000}
        .alignRight{
            text-align:right
        }
        .alignLeft{
            text-align:left
        }
    </style>
    <META HTTP-EQUIV="Cache-Control" CONTENT ="no-cache">
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
    <META HTTP-EQUIV="expires" CONTENT="0">
    </head>
  <body>
  <br /> 	
  <div>
  <center>
  <form name="table_itienerarios" id="table_itienerarios" action="" method="post">
    <table id="comprobacion_table" border="0" cellspacing="6" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
        <tr>
            <td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n de la Solicitud </strong></div></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
          <td width="6%"><div align="right">No. de Folio:</div></td>
            <td width="18%"><strong><?php echo $t_id ?></strong></td>
            <td width="2%">&nbsp;</td>
            <td ><div align="right">Lugar/Restaurante:</div></td>
            <td ><div align="left"><strong><?php echo $lugar;?></strong></div></td>
        </tr>
        <tr>
            <td ><div align="right">Solicitante:</div></td>
            <td ><div align="left"><strong><?php echo $nombreUsuarioIniciador;?></strong></div></td>
            <td >&nbsp;</td>
            <td width="10%"><div align="right">Fecha de creaci&oacute;n:</div></td>
            <td width="14%"><div align="left"><strong><?php echo $fecha_tramite ?></strong></div></td>
        </tr>
        <tr>
            <td ><div align="right">Motivo:</div></td>
            <td ><div align="left"><strong><?php echo $motivo;?></strong></div></td>
            <td >&nbsp;</td>
            <td ><div align="right">Centro de costos:</div></td>
            <td ><div align="left"><strong><?php echo $cc_centrocostos.' - '.$cc_nombre?></strong></div></td>
        </tr>
        <tr>
            <td ><div align="right">Fecha de gasto:</div></td>
            <td ><div align="left"><strong><?php echo $fechagasto;?></strong></div></td>
            <td >&nbsp;</td>
            <td ><div align="right">Etapa de la solicitud:</div></td>
            <td ><div align="left"><strong><?php echo $t_etapa_nombre;?></strong></div></td>
        </tr>
        <tr>
            <td ><div align="right">Concepto:</div></td>
            <td ><div align="left"><strong><?php echo $sg_concepto;?></strong></div></td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
        </tr>
        <tr>
            <td ><div align="right">Autorizador(es):</div></td>
            <td colspan="5"><div align="left"><strong><?php echo $autorizadores; ?></strong></div></td>
        </tr>
        <tr>
            <td colspan="5">
            </td>
        </tr>                
    </table>
    <br /><br />
    <?php if($idConcepto == COMIDAS_REPRESENTACION){ ?>
    <div align="center" style="color:#003366"><strong>Lista de invitados</strong></div>
    <table id="invitados_table" class="tablesorter" cellspacing="1" style="width:65%">
        <thead> 
            <tr>
                <th width="4%">No.</th>
                <th width="28%">Nombre</th>
                <th width="28%">Puesto</th> 					
                <th width="28%">Empresa</th>
                <th width="12%">Tipo</th>
            </tr>
            <?php 
                $cont=0;
                foreach($invitados as $datosAux){ 
                	$cont++;?>
                <tr>
                	<td><?php echo $cont ?></td>
                    <td><?php echo $datosAux["c_nombre_invitado"] ?></td>
                    <td><?php echo $datosAux["c_puesto_invitado"] ?></td>
                    <td><?php echo $datosAux["c_empresa_invitado"] ?></td>
                    <td><?php echo $datosAux["c_tipo_invitado"] ?></td>
                </tr>
            <?php } ?>
        </thead>
    </table>
    <table style="width:65%">
    	<tr>
    		<td width="4%"><div align="right">&nbsp;</div></td>
    		<td width="28%">&nbsp;</td>
    		<td width="28%">&nbsp;</td>
    		<td width="28%"><div align="right">Total de invitados:</div></td>
    		<td width="12%" align="center" valign="middle"><div><strong><?php echo $cont; ?></strong></div></td>
    	</tr>
    </table>
    <br /><br />
    <?php } ?>
    <table id="datosgrales" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
        <tr>
            <td colspan="5"><div align="center" style="color:#003366"><strong>Solicitud de Gastos</strong></div></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
			<td width="24%"><div align="right">Ciudad:</div></td>
            <td width="24%"><strong><?php echo $ciudad;?></strong></td>
            <td width="4%">&nbsp;</td>
            <td width="24%"><div align="right">Total Monto Solicitado:</div></td>
            <td width="24%"><div align="left"><strong><?php echo number_format($monto,2,".",",")." ".$divisa;?></strong></div></td>
        </tr>
        <tr id="tr_totalAnticipo">
            <td><div align="right">&nbsp;</td>
            <td><div align="left"><?php if(isset($_GET['edit_view'])){ ?><strong><?php if($requiereAnticipo){ echo "<font color='#FF0000'><b>La Solicitud requiere anticipo.</b></font>"; } }?></strong></div></td>
            <td>&nbsp;</td>
            <td><div align="right">Total en pesos:</div></td>
            <td><div align="left"><div align="left"><strong><?php echo number_format($monto_pesos,2,".",",")." MXN"; ?></strong></div></div></td>
        </tr>
        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        <tr id="row_ExcepcionesSolcitud">
        	<td colspan="5">
        	<h1 align="center">Excepciones</h1>
	        	<!-- Inicio Tabla de Partidas de Excepciones -->
				<table id="excepcion_table" class="tablesorter" cellspacing="1">
					<thead>
						<tr>
							<th>Concepto</th>
							<th>Mensaje</th>
							<th>Excedente</th>
							<th>Tipo de Excepci&oacute;n</th>
						</tr>
					</thead>
				<tbody></tbody>
				</table>
				<!-- Fin Tabla de Partidas -->
        	</td>
        </tr>
        <tr>
			<td align="right" valign="top"><div align="right">Historial de Observaciones:</div></td>
			<td colspan="3" rowspan="1" class="alignLeft" >
			<textarea name="historial_observaciones" id="historial_observaciones" cols="80" rows="5" readonly="readonly" onkeypress="confirmaRegreso('historial_observaciones');" onkeydown="confirmaRegreso('historial_observaciones');"><? echo $observaciones;?></textarea>
			</td>
			<td >&nbsp;</td>
		</tr>
        <?php if(isset($_GET['edit_view']) || $t_etapa == SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR){ ?>
        <tr>
			<td valign="top"><div align="right">Observaciones:</div></td>
			<td colspan="3" rowspan="1" class="alignLeft" valign="top">
			<textarea name="observ" cols="80" rows="5" id="observ"></textarea>
			</td>
			<td >&nbsp;</td>
        </tr>
        <?php } ?>
        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5">
              <div align="center">
              	<input type="submit" value="    Autorizar" id="autorizar_sol_gts" name="autorizar_sol_gts"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
	            <input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
	            <input type="submit" value="    Rechazar" id="rechazar_sol_gts" name="rechazar_sol_gts" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden"/>
                <input type="hidden" id="idt" name="idt" value="<?php echo $_GET['id'];?>" />
                <input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
                <input type="hidden" name="ne" id="ne" readonly="readonly" value="<? //echo $_SESSION["idusuario"];  ?>" />
                <input type="hidden" name="hist" id="hist" readonly="readonly" value="<? echo $_GET['hist']; ?>" />
                <input type="hidden" name="representante" id="representante" readonly="readonly" value="<?php if(isset($_SESSION['idrepresentante'])){ echo $_SESSION['idrepresentante']; }else{echo 0;}?>" />
              </div>
          </td>
        </tr>
        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        <?php if($t_etapa != SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR){ ?>
        <tr>
        	<td colspan="5" align="center" valign="middle">
        		<input type="button" value="     Imprimir"  name="Imprimir" id="Imprimir" style="background:url(../../images/icon_Imprimir.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
        	</td>
        </tr>
        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        <?php } ?>
    </table>
    </form> 
    </center>
      </div>
      <br/>
   </body>
   <?php
	if(isset( $_GET['edit_view']) || 
			($iniciador == $_SESSION['idusuario'] && 
			$t_etapa == SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR && 
			!isset($_SESSION['idrepresentante'])) ){
		echo "<script language='javascript'>
			document.getElementById('autorizar_sol_gts').style.visibility = 'visible';
			document.getElementById('rechazar_sol_gts').style.visibility = 'visible';
			</script>";
	}
   ?>
