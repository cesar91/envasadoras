<?php
/********************************************************
*         T&E Módulo de Edición y vista de 			*
*				Solicitud de Viajes      				*
* Creado por:	Luis Daniel Barojas Cortes				*
* PHP, jQuery, JavaScript, CSS                          *
*********************************************************/
				
    require_once "../../Connections/fwk_db.php";
    $empleado		= $_SESSION["idusuario"];
    $cnn			= new conexion();
    $aux= array();
    $query="SELECT sv_id, t_iniciador, t_id, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y %H:%i:%S') as fecha_registro, t_etiqueta, 
                 sv_anticipo, sv_dias_viaje, sv_observaciones, svi_id, svi_tipo_viaje, svi_origen, svi_destino,  
                   svi_medio, svi_kilometraje,  svi_horario, DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') as fecha_salida, 
                        DATE_FORMAT(svi_fecha_llegada,'%d/%m/%Y') as fecha_llegada,
                      svi_noches_hospedaje, svi_monto_peaje, svi_iva, svi_tua, svi_nombre_hotel, svi_costo_hotel, 
                        svi_otrocargo, svi_hotel, t_etapa_actual, t_dueno, sv_divisa, sv_tasa, cc_centrocostos, cc_nombre, 
                           cc_id, sv_fecha_viaje, sv_anticipo, t_flujo, (select sum(svc_detalle_monto_concepto)
                             FROM sv_conceptos_detalle WHERE svc_detalle_tramite = t_id) as monto 
                FROM sv_itinerario INNER JOIN solicitud_viaje ON (sv_itinerario.svi_solicitud=solicitud_viaje.sv_id) 
                                   INNER JOIN tramites ON (tramites.t_id=solicitud_viaje.sv_tramite) 
                                   LEFT JOIN cat_cecos on (sv_ceco_paga = cc_id) where t_id={$_GET['id']} ";
    //error_log($query);
    $rst=$cnn->consultar($query);
    while($datos=mysql_fetch_assoc($rst)){
        array_push($aux,$datos);
    }
	
    // Datos de la solicitud
    $id_Solicitud=mysql_result($rst,0,"sv_id");
    $iniciador=mysql_result($rst,0,"t_iniciador");
    $tramite=mysql_result($rst,0,"t_id");
    $fecha_tramite=mysql_result($rst,0,"fecha_registro");
    $motivo=mysql_result($rst,0,"t_etiqueta");
    $anticipo=mysql_result($rst,0,"sv_anticipo");
    $totaldias=mysql_result($rst,0,"sv_dias_viaje");
    $observaciones=mysql_result($rst,0,"sv_observaciones");
    $flujo=mysql_result($rst,0,"t_flujo");    
    $etapa=mysql_result($rst,0,"t_etapa_actual");
    $dueno=mysql_result($rst,0,"t_dueno");
    $divisa=mysql_result($rst,0,"sv_divisa");
    $tasa=mysql_result($rst,0,"sv_tasa");
    $cc_centrocostos=mysql_result($rst,0,"cc_centrocostos");
    $cc_nombre=mysql_result($rst,0,"cc_nombre");    
    $CentroCostoId=mysql_result($rst,0,"cc_id");
    $sv_fecha_viaje=mysql_result($rst,0,"sv_fecha_viaje");
    $monto=mysql_result($rst,0,"sv_anticipo");
    
    // Carga presupuesto
    $Ceco=new CentroCosto();
    $presupuesto_disponible = $Ceco->get_presupuesto_disponible($CentroCostoId, $sv_fecha_viaje);
            
    // Carga datos del usuario
    $query=" SELECT * FROM usuario where u_id={$iniciador}";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $iniciador=$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"];
    
    // Carga datos del empleado
    $query=" SELECT * FROM empleado where numempleado='{$fila['u_usuario']}'";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $puesto=$fila["npuesto"];
    $telefono=$fila["telefono"];
    $departamento=$fila["departamento"];

    // Dueño de la solitud
    $queryJefe="SELECT * from usuario where u_id='$dueno'";        
    $rst=$cnn->consultar($queryJefe);
    $aprobador = mysql_result($rst,0,"u_nombre");
?>

<head>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/dom-drag.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
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
    doc.ready(inicializarEventos);

    function inicializarEventos(){	

    //ajusta tabla
    $("#solicitud_table").tablesorter({ 
            //cabeceras deshabilitadas del ordenamiento
            headers: { 
                4: {sorter: false }, 
                7: {sorter: false },
                9: {sorter: false },
                11:{sorter: false }  
            } //headers
        }); //tabla
    }//fin ready ó inicializarEventos

    function Location(){
        location.href='<?php echo $RUTA_R; ?>inicial.php';
    }
    
</script>
</head>

<!-- El encabezado se pone en la pagina de solicitud_view.php -->
<br /> 	
<div>
<center> 
<form name="table_itienerarios" id="table_itienerarios" action="solicitud_view.php" method="post">
<table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
    <tr>
        <td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n de la Solicitud </strong></div></td>
    </tr>
    <tr>
        <td colspan="5">&nbsp;</td>
    </tr>
    
    <tr>
      <td width="6%"><div align="right">Tr&aacute;mite:</div></td>
        <td width="18%"><strong><?php echo $tramite ?></strong></td>
        <td width="2%">&nbsp;</td>
        <td width="10%"><div align="right">Fecha de creaci&oacute;n:</div></td>
        <td width="14%"><div align="left"><strong><?php echo $fecha_tramite ?></strong></div></td>
    </tr>
    
    <tr>
        <td ><div align="right">Solicitante:</div></td>
        <td ><div align="left"><strong><?php echo $iniciador;?></strong></div></td>
        <td >&nbsp;</td>
        <td ><div align="right">Autorizador:</div></td>
        <td ><div align="left"><strong><?php echo $aprobador;?></strong></div></td>
    </tr>						
                
    <tr>
        <td class="alignRight" valign="top">Motivo:</td>
        <td colspan="4" class="alignLeft">
            <strong>						
                <?php echo $motivo?>
            </strong>
        </td>
    </tr>

    <tr>
        <td colspan="5"><hr>
        </td>
    </tr>                
                
    <tr>
        <td class="alignRight" valign="top">Departamento:</td>
        <td colspan="4" class="alignLeft">
            <strong>
                <?php echo $cc_centrocostos.' - '.$cc_nombre?>
            </strong>
        </td>
    </tr>            
    
    <?php if (($dueno == $idusuario) && 
               (($flujo==FLUJO_ANTICIPO && $etapa==ANTICIPO_ETAPA_APROBACION) ||
                ($flujo==FLUJO_AMEX && $etapa==ANTICIPO_AMEX_ETAPA_APROBACION))) { ?>
    <tr>
        <td class="alignRight" valign="top">Presupuesto Disponible:</td>
        <td colspan="2" class="alignLeft" valign="top">
            <strong>
                <?php echo "$ ". number_format($presupuesto_disponible);?>
            </strong>
        </td>
        
        <td class="alignRight" valign="top">Presupuesto Restante:</td>
        <td colspan="1" class="alignLeft" valign="top">
            <strong>
                <?php echo "$ ". number_format($presupuesto_disponible - $monto);?>
            </strong>
        </td>                
    </tr>                
        
    <?php } ?>                 
    
</table>
<br />
<br />
<br />
<table id="solicitud_table" class="tablesorter" cellspacing="1"> 
    <thead> 
        <tr> 				   
            <th width="4%">Origen</th>
            <th width="4%">Destino</th> 					
            <th width="3%">Salida</th>					
            <th width="3%">Llegada</th>			
        </tr> 

        <?php 
            $cont=0;
            foreach($aux as $datosAux){
        ?>
            <tr>
                <td><?php echo $datosAux["svi_origen"] ?></td>
                <td><?php echo $datosAux["svi_destino"] ?></td>						
                <td><?php echo $datosAux["fecha_salida"] ?></td>
                <td><?php echo $datosAux["fecha_llegada"] ?></td>					  					 
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
<?php   
    //muestra detalle de conceptos
    $conceptos = "select
                     cd.svc_detalle_id,
                     cd.svc_detalle_tramite,
                     cd.svc_detalle_concepto,
                     cd.svc_detalle_monto_concepto,
                     c.cp_concepto,
                     c.dc_id,
                     c.cp_cuenta,
                     s.sv_id,
                     s.sv_monto_peaje,
                     s.sv_motivo,
                     s.sv_tramite,
                     s.sv_anticipo,
                     s.sv_dias_viaje,
                     s.sv_monto_maximo_disponible,
                     t.t_id,
                     t_iniciador,
                     t_etapa_actual
                  from
                     sv_conceptos_detalle cd,
                     cat_conceptos c,
                     solicitud_viaje s,
                     tramites t
                  where
                     c.dc_id = cd.svc_detalle_concepto AND
                     cd.svc_detalle_tramite = t.t_id AND
                     s.sv_tramite = t.t_id AND
                     t.t_id = $tramite";
                    
    $resultado=$cnn->consultar($conceptos);
    $filas = mysql_num_rows($resultado);
    if($filas != 0)
    {
    ?>
        <center>
        <div align="center" style="width:50%">
        <br />
        <h3>Conceptos de vi&aacute;ticos</h3>
        <table id="datosConceptos" class="tablesorter">
           <thead>
                <tr>
                    <th width="2%">No.</th>
                    <th width="55%">Concepto</th>
                    <th width="11%">Monto</th>
                </tr>
          </thead>
          <tbody>
         <?
         $monto_total = 0;
         for($i=0; $i<$filas; $i++)
         {
         ?>
            <tr>
               <td width="2%"><? echo $i+1;?></td>
               <td width="15%"><? echo mysql_result($resultado, $i, "cp_concepto")?></td>
               <td width="8%">
                    $<? echo number_format(mysql_result($resultado, $i, "svc_detalle_monto_concepto"),2,".",",");?>
               </td>
            </tr>
         <?
         }
         ?>
           </tbody>
        </table>
        <br><br>
        </div>
        </center>
    <?
    } else {
       echo "<h3>No hay conceptos solicitados</h3>";
    }
?>

<br />

<center><b>Anticipo Solicitado</b></center>
<table id="totales" width="30%" border="0">  

    <?php if($divisa!='MXP') { ?>
    <tr>
        <td align='right'>&nbsp;</td>  
        <td align='right'><b>En d&oacute;lares:</b></td>
        <td align='right'><b>En pesos</b></td>
    </tr>    
    <?php } ?>
 
    <tr>
        <td align='right'><b>Subtotal:</b></td>
        <td align='right'><?php echo "$ " . $monto . " " . $divisa; ?></div></td>  
        <?php if($divisa!='MXP') { ?>
            <td align='right'><?php echo "$ " . $monto * $tasa . " MXP"; ?></div></td>  
        <?php } ?>
    </tr>
    <tr>
        <td align='right'><b>IVA:</b></td>
        <td align='right'><?php echo "$ 0.0 " . $divisa; ?></div></td>  
        <?php if($divisa!='MXP') { ?>
            <td align='right'><?php echo "$ 0.0 MXP"; ?></div></td>  
        <?php } ?>        
    </tr>
    <tr>
        <td align='right'><b>Total:</b></td>
        <td align='right'><?php echo "$ " . $monto . " " . $divisa; ?></div></td>  
        <?php if($divisa!='MXP') { ?>
            <td align='right'><?php echo "$ " . $monto * $tasa . " MXP"; ?></div></td>  
        <?php } ?>        
    </tr>      
</table>

 <br><br>
 <table border="0">
    <tr>
        <td colspan="10"><br />
          <div align="center"> 
            <input type="submit" value="    Autorizar" id="autorizar" name="autorizar"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
            <input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
            <input type="submit" value="    Rechazar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden"/>
            <input type="hidden" id="idT" name="idT" value="<?php echo $_GET['id']?>" readonly="readonly" />
            <input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
            <input type="hidden" name="ne" id="ne" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
            <input type="hidden" id="cont_itinerarios" name="cont_itinerarios" value="<?php echo $cont?>" readonly="readonly"/>
            <input type="hidden" id="tasa" name="tasa" value="<?php echo $tasa;?>" readonly="readonly"/>
          </div>
      </td>				 
    </tr>
</table>

</center>
  </div>
  <br/>    	
</body>
<?php
echo ('<script language="JavaScript" type="text/javascript">searchSolicitud()</script>');
   if(isset( $_GET['edit_view'])){
        echo "<script language='javascript'>
                document.getElementById('autorizar').style.visibility = 'visible';
                document.getElementById('rechazar').style.visibility = 'visible';
                document.getElementById('cancel').style.visibility = 'hidden';
              </script>";
    }
?>	
