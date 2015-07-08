<?php	
    require_once "../../Connections/fwk_db.php";
    require_once "$RUTA_A/functions/Tramite.php";
    require_once "$RUTA_A/functions/Usuario.php";
    require_once("$RUTA_A/functions/utils.php");
    
    //$empleado		= $_SESSION["idusuario"];
    $cnn			= new conexion();
    $aux= array();
    $invitados= array();
	if(isset($_GET['id'])){
	    $query="SELECT si_id, t_iniciador, t_id, /*DATE_FORMAT(t_fecha_registro,'%d/%m/%Y %H:%i:%S'),*/
			DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), t_etiqueta, si_monto, si_divisa, si_monto_pesos,
			si_ciudad, si_observaciones, DATE_FORMAT(si_fecha_invitacion,'%d/%m/%Y'), si_lugar, t_dueno,
			cc_id, cc_centrocostos, t_etapa_actual, t_flujo, cc_nombre, si_num_invitados, si_hubo_exedente, t_delegado 
			FROM solicitud_invitacion as si
			INNER JOIN tramites as t on t.t_id=si.si_tramite
			LEFT JOIN cat_cecos as cc on si.si_ceco = cc.cc_id
			WHERE t_id={$_GET['id']}";
	}
    $rst=$cnn->consultar($query);
	//error_log($query);	
		
    while($datos=mysql_fetch_assoc($rst)){
        array_push($aux,$datos);
    }
	
    // Datos del tramite
    $id_Solicitud=mysql_result($rst,0,0);
    $iniciador=mysql_result($rst,0,1);
    $iniciadorT=mysql_result($rst,0,1);
    $t_id=mysql_result($rst,0,2);
    $fecha_tramite=mysql_result($rst,0,3);
    $motivo=mysql_result($rst,0,4);
    $fechainv=mysql_result($rst,0,10);
    $lugarinv=mysql_result($rst,0,11);
    $dueno=mysql_result($rst,0,"t_dueno");
	$monto=mysql_result($rst,0,"si_monto");
	$divisa=mysql_result($rst,0,"si_divisa");
	$monto_pesos=mysql_result($rst,0,"si_monto_pesos");
	$ciudad=mysql_result($rst,0,"si_ciudad");
    $observaciones=mysql_result($rst,0,"si_observaciones");
    $CentroCostoId=mysql_result($rst,0,"cc_id");
    $cc_centrocostos=mysql_result($rst,0,"cc_centrocostos");
    $t_etapa=mysql_result($rst,0,"t_etapa_actual"); 
    $t_flujo=mysql_result($rst,0,"t_flujo");
    $cc_nombre=mysql_result($rst,0,"cc_nombre");  
    $si_num_invitados=mysql_result($rst,0,"si_num_invitados");  
    $si_hubo_exedente=mysql_result($rst,0,"si_hubo_exedente");
    $t_delegado=mysql_result($rst,0,"t_delegado");
    
    //error_log("Hubo excedente.....".$si_hubo_exedente);

    $query="SELECT dci_nombre_invitado,
		dci_puesto_invitado,
		dci_empresa_invitado,
		dci_tipo_invitado
		FROM comensales_sol_inv
		WHERE dci_solicitud_inv = {$id_Solicitud}";
    $rst=$cnn->consultar($query);
	//error_log($query);	
    while($datos=mysql_fetch_assoc($rst)){
        array_push($invitados,$datos);
    }
    
    // Datos del usuario
    $query=" SELECT * FROM empleado where idempleado={$iniciador}";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $centrocosto=$fila["idcentrocosto"];
    $iniciador=$fila["nombre"];

    // Dueño de la solitud
    $queryJefe="SELECT * from usuario where u_id='$dueno'";        
    $rst=$cnn->consultar($queryJefe);
    $aprobador = mysql_result($rst,0,"u_nombre");

    //Nombre de la etapa actual
    $queryetapa="SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id='$t_etapa' AND et_flujo_id='$t_flujo'";
    $rst4=$cnn->consultar($queryetapa);
    $nom_etapa = mysql_result($rst4,0,"et_etapa_nombre");
    
    //Traerá la ruta de autorización de la solicitud correspondiente
    $rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = $rutaAutorizacion->getNombreAutorizadores($t_id);
    
    // Verificamos si la solicitud fue realizada por un delegado; de ser así imprimiremos el nombre de los involucrados
    if($t_delegado != 0){
    	$duenoActual = new Usuario();
    	$duenoActual->Load_Usuario_By_ID($t_delegado);
    	$nombredelegado = $duenoActual->Get_dato('nombre');
    	$iniciador = "<font color='#0000CA'>".$nombredelegado."</font>".strtoupper(" en nombre de: ").$iniciador;
    }	
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html>
    <head>
    <script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>  
    <script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/dom-drag.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
    <script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
    <script language="JavaScript" src="../comprobaciones/js/backspaceGeneral.js" type="text/javascript"></script>
    <script language="javascript" type="text/javascript">		
        function validaNum(valor){
			cTecla=(document.all)?valor.keyCode:valor.which;
			if(cTecla==8) return true;
			patron=/^([0-9.]{1,2})?$/;
			cTecla= String.fromCharCode(cTecla);
			return patron.test(cTecla);
        }
        
        var doc;

        doc = $(document);
        doc.ready(inicializarEventos);//cuando el documento esta listo

        function inicializarEventos(){
			if(navigator.appName!='Netscape'){
            }
        }//fin ready ó inicializarEventos

        <?php if($_SESSION["perfil"]==5) { ?>
        function Location(){
            location.href='../notificaciones/index.php';
        }
        <?php } else { ?>
        function Location(){
            location.href='./index.php?docs=docs&type=2';
        }        
        <?php } ?>
		
	</script>
 
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
  <!--
  <form name="table_itienerarios" id="table_itienerarios" action="solicitud_view_amex.php" method="post">-->
  <form name="table_itienerarios" id="table_itienerarios" action="" method="post">
    <table id="comprobacion_table" border="0" cellspacing="6" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
        <tr>
            <td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n de la Solicitud </strong></div></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        
        <tr>
          <td width="6%"><div align="right">Tr&aacute;mite:</div></td>
            <td width="18%"><strong><?php echo $t_id ?></strong></td>
            <td width="2%">&nbsp;</td>
            <td ><div align="right">Lugar de invitaci&oacute;n/Restaurante:</div></td>
            <td ><div align="left"><strong><?php echo $lugarinv;?></strong></div></td>
        </tr>
        
        <tr>
            <td ><div align="right">Solicitante:</div></td>
            <td ><div align="left"><strong><?php echo $iniciador;?></strong></div></td>
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
            <td ><div align="right">Fecha de invitaci&oacute;n:</div></td>
            <td ><div align="left"><strong><?php echo $fechainv;?></strong></div></td>
            <td >&nbsp;</td>
            <td ><div align="right">Etapa de la solicitud:</div></td>
            <td ><div align="left"><strong><?php echo $nom_etapa;?></strong></div></td>
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
    <br />
    <br />
    <br />
	<div align="center" style="color:#003366"><strong>Lista de invitados</strong></div>
    <table id="invitados_table" class="tablesorter" cellspacing="1" style="width:75%">
        <thead> 
            <tr>
                <th width="4%">No.</th>
                <th width="28%">Nombre</th>
                <th width="28%">Puesto</th> 					
                <th width="28%">Empresa</th>
                <th width="12%">Tipo</th>
            </tr> 

            <?php 
                $cont=1;
                foreach($invitados as $datosAux){
            ?>
                <tr>
                    <td><?php echo $cont ?></td>
                    <td><?php echo $datosAux["dci_nombre_invitado"] ?></td>
                    <td><?php echo $datosAux["dci_puesto_invitado"] ?></td>
                    <td><?php echo $datosAux["dci_empresa_invitado"] ?></td>
                    <td><?php echo $datosAux["dci_tipo_invitado"] ?></td>
                </tr>
            <?php
                 $cont++;
                } 
            ?>
        </thead> 
        <tbody>
            <!-- cuerpo tabla-->
        </tbody>
    </table>
    <br/>
    <br/>
    <table id="datosgrales" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
        <tr>
            <td colspan="5"><div align="center" style="color:#003366"><strong>Solicitud de invitaci&oacute;n</strong></div></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
			<td width="24%"><div align="right">No. Invitados:</div></td>
            <td width="24%"><strong><?php echo $si_num_invitados ?></strong></td>
            <td width="4%">&nbsp;</td>
            <td width="24%"><div align="right">Total Monto solicitado:</div></td>
            <td width="24%"><div align="left"><strong><?php echo number_format($monto,2,".",",")." ".$divisa;?></strong></div></td>
        </tr>
        <tr>
            <td ><div align="right">Ciudad:</div></td>
            <td ><div align="left"><strong><?php echo $ciudad;?></strong></div></td>
            <td >&nbsp;</td>
            <td ><div align="right">Total en pesos:</div></td>
            <td ><div align="left"><div align="left"><strong><?php echo number_format($monto_pesos,2,".",",")." MXN" ?></strong></div></div></td>
        </tr>
        <tr>
        	<td >&nbsp;</td>
            <td colspan="3" align="left" valign="middle"><div><?php if(isset($_GET['edit_view']) && $si_hubo_exedente == 1){ echo "<font color='#FF0000'><b>Excede el monto l&iacute;mite de 50.00 EUR por persona.</b></font>"; }?></div></td>
            <td >&nbsp;</td>
        </tr>
        <tr>
			<td align="right" valign="top"><div align="right">Historial de Observaciones:</div></td>
			<td colspan="3" rowspan="1" class="alignLeft" >
			<textarea name="historial_observaciones" id="historial_observaciones" cols="80" rows="5" readonly="readonly" onkeypress="confirmaRegreso('historial_observaciones');" onkeydown="confirmaRegreso('historial_observaciones');"><? echo $observaciones;?></textarea>
			</td>
			<td >&nbsp;</td>
		</tr>
        <?if(isset($_GET['edit_view']) || (isset( $_GET['view']) && $t_etapa == SOLICITUD_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR && !isset($_SESSION['iddelegado']))){?>
        <tr>
            <td ><div align="right">&nbsp;</div></td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
        </tr>
        <tr>
			<td valign="top"><div align="right">Observaciones:</div></td>
			<td colspan="3" rowspan="1" class="alignLeft" valign="top">
			<textarea name="observ" cols="80" rows="5" id="observ"></textarea>
			</td>
			<td >&nbsp;</td>
        </tr>
		<?}?>
        <tr><td colspan="5"></td></tr>
        <tr>
            <td colspan="5"><br />
              <div align="center">
				<?php
				
				$idTramite=$_GET['id'];
				$tramites = new Tramite();
				$tramites->Load_Tramite($idTramite);
				$t_iniciador=$tramites->Get_dato("t_iniciador");
				$t_dueno=$tramites->Get_dato("t_dueno");
				$t_ruta_autorizacion = $tramites->Get_dato("t_ruta_autorizacion");
				$iduser = $_SESSION["idusuario"];
				$encontrado = encuentraAutorizador($t_ruta_autorizacion, $iduser);
				
				$privilegios = 0;
				if(isset($_SESSION['iddelegado'])){
					$query = sprintf("SELECT privilegios FROM usuarios_asignados WHERE id_asignador = '%s' AND id_delegado = '%s'", $_SESSION["idusuario"], $_SESSION['iddelegado']);
					$rst = $cnn->consultar($query);
					$fila = mysql_fetch_assoc($rst);
					$privilegios = $fila["privilegios"];
					//error_log($query);
				}
				
				if(isset( $_GET['view']) && $t_etapa == SOLICITUD_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR && !isset($_SESSION['iddelegado'])){?>
					<input type="submit" value="    Aprobar" id="aprobar_si" name="aprobar_si"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
	                <input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
	                <input type="submit" value="    Rechazar" id="rechazar_si" name="rechazar_si" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
				<?php }else if((isset($_SESSION['iddelegado']) && $privilegios == 1)){ // Si la Sesión del delegado se ha iniciado, y el asignador tiene privilegios de autorizar, se deberán mostrar los botones de Autorizar y Rechazar ?>
					<input type="submit" value="    Autorizar" id="autorizar_sol_inv" name="autorizar_sol_inv"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
	                <input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
	                <input type="submit" value="    Rechazar" id="rechazar_sol_inv" name="rechazar_sol_inv" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden"/>
	            <?php }else if((isset($_SESSION['iddelegado']) && $privilegios == 0)){  // Si la Sesión del delegado se ha iniciado, y el asignador no tiene privilegios de autorizar, se deberá mostrar el botón de Volver ?>
	            	<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
                <?php }else{ // Es el aprobador quien podrá Autorizar o Rechazar la Solicitud?>
                	<input type="submit" value="    Autorizar" id="autorizar_sol_inv" name="autorizar_sol_inv"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
	                <input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
	                <input type="submit" value="    Rechazar" id="rechazar_sol_inv" name="rechazar_sol_inv" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden"/>
	            <?php }?>
                <input type="hidden" id="idt" name="idt" value="<?php echo $_GET['id'];?>" />
                <input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
                <input type="hidden" name="ne" id="ne" readonly="readonly" value="<? //echo $_SESSION["idusuario"];  ?>" />
                <input type="hidden" id="cont_invitados" name="cont_invitados" value="<?php echo $cont?>"/>
                <input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
                <input type="hidden" name="delegadoNombre" id="delegadoNombre" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['delegado']; }else{echo 0;}?>" />
              </div>
          </td>
        </tr>
    </table>
    </form> 

    </center>
      </div>
      <br/>
   </body>
   <?php
    //echo ('<script language="JavaScript" type="text/javascript">searchSolicitud_amex();</script>');
	if(isset( $_GET['edit_view'])){
		echo "<script language='javascript'>
			document.getElementById('autorizar_sol_inv').style.visibility = 'visible';
			document.getElementById('rechazar_sol_inv').style.visibility = 'visible';
			</script>";
	}
   ?>	
