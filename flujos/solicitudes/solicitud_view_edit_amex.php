<?php
/********************************************************
*         T&E Módulo de Edición y vista de 				*
*				Solicitud de Viajes      				*
* Creado por:	Luis Daniel Barojas Cortes				*
* PHP, jQuery, JavaScript, CSS
*            *
*********************************************************/
				
    require_once "../../Connections/fwk_db.php";
    $empleado		= $_SESSION["idusuario"];
    $cnn			= new conexion();
    $aux= array();

    $query="SELECT sa_id,t_iniciador, t_id, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y %H:%i:%S'), t_etiqueta, sa_anticipo, sa_observaciones, sa_dias_viaje, sa_id, sai_tipo_viaje, sai_origen, 
                  sai_destino, sai_horario, sai_monto_vuelo, sai_numero_transporte, sai_iva, sai_tua, DATE_FORMAT(sai_fecha_salida,'%d/%m/%Y') as fecha_salida, sai_hora_salida, sai_aerolinea, 
                     t_dueno, cc_id, sai_fecha_salida, cc_centrocostos, t_etapa_actual, cc_nombre, sai_otrocargo, sai_apartado_boleto
                FROM sa_itinerario INNER JOIN solicitud_amex on (sa_itinerario.sai_solicitud=solicitud_amex.sa_id) 
                   INNER JOIN tramites on (tramites.t_id=solicitud_amex.sa_tramite) 
                      LEFT JOIN cat_cecos on (sa_ceco_paga = cc_id)
                        WHERE t_id={$_GET['id']} ";
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
    $anticipo=mysql_result($rst,0,5);
    $totaldias=mysql_result($rst,0,6);
    $dueno=mysql_result($rst,0,"t_dueno");
    $observaciones=mysql_result($rst,0,"sa_observaciones");
    $CentroCostoId=mysql_result($rst,0,"cc_id");
    $cc_centrocostos=mysql_result($rst,0,"cc_centrocostos");
    $sv_fecha_viaje=mysql_result($rst,0,"sai_fecha_salida");    
    $t_etapa=mysql_result($rst,0,"t_etapa_actual");  
    $cc_nombre=mysql_result($rst,0,"cc_nombre");  
    
    $sai_monto_vuelo=mysql_result($rst,0,"sai_monto_vuelo");    
    $sai_iva=mysql_result($rst,0,"sai_iva");   
    $sai_tua=mysql_result($rst,0,"sai_tua"); 
    $sai_otrocargo=mysql_result($rst,0,"sai_otrocargo");
    
    // Datos del usuario
    $query=" SELECT * FROM usuario where u_id={$iniciador}";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $iniciador=$fila["u_nombre"]." ".$fila["u_paterno"]." ".$fila["u_materno"];
    $query=" SELECT * FROM empleado where numempleado='{$fila['u_usuario']}'";
    $rst=$cnn->consultar($query);
    $fila=mysql_fetch_assoc($rst);
    $centrocosto=$fila["idcentrocosto"];

    // Dueño de la solitud
    $queryJefe="SELECT * from usuario where u_id='$dueno'";        
    $rst=$cnn->consultar($queryJefe);
    $aprobador = mysql_result($rst,0,"u_nombre");
    
    // Carga presupuesto
    $Ceco=new CentroCosto();
    $presupuesto_disponible = $Ceco->get_presupuesto_disponible($CentroCostoId, $sv_fecha_viaje); 

    // Carga datos de autorizadores
    $tramite = new Tramite();
    $ruta_autorizadores = $tramite->GetRutaAutorizacion($t_id);
    $autorizaciones     = $tramite->GetAutorizaciones($t_id);  
    
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

        /*//ajusta tabla
        $("#solicitud_table").tablesorter({ 
                //cabeceras deshabilitadas del ordenamiento
                headers: { 
                    4: {sorter: false }, 
                    7: {sorter: false },
                    9: {sorter: false },
                    11:{sorter: false }  
                } //headers
            }); //tabla*/
            
            if(navigator.appName!='Netscape'){
                Drag.init(document.getElementById("ventanita"));
                document.getElementById('ventanita').style.cursor="move";
                document.getElementById('ventanita').style.width="50%";
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
        
        function calculaView(){
            var frm=document.table_itienerarios;
            frm.ant.value="1";
        }
        
        function flotanteActive(valor,id){

            var frm=document.table_itienerarios;
            if (valor==1){					
                document.getElementById('ventanita').style.visibility = 'visible';				

                frm.id_itinerario.value=id;
                Edit_itinerario(id);
                
                
                document.getElementById('datosgrales').disabled = true;
                document.getElementById('datosgrales').style.opacity = .3;

                //Deshabilita botones	
                                
                document.getElementById('Volver').disabled = true;
                document.getElementById('rechazar').disabled = true;
            }
            else{

                
                document.getElementById('ventanita').style.visibility = 'hidden';
                document.getElementById('datosgrales').disabled = false;
                document.getElementById('datosgrales').style.opacity = 1;
                                    
                //Habilita botones					
                document.getElementById('Volver').disabled = false;
                document.getElementById('rechazar').disabled = false;

            }
        }								
            
        function activaSave(){
            var frm=document.table_itienerarios;
    
            if(frm.costoViaje.value=="" || frm.iva.value=="")
                $("#aceptarnueva").attr("disabled", "disabled");

            else
                $("#aceptarnueva").removeAttr("disabled"); 
        }	
        
                
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
  <form name="table_itienerarios" id="table_itienerarios" action="solicitud_view_amex.php" method="post">
    <table id="comprobacion_table" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
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
            <td width="10%"><div align="right">Fecha de creaci&oacute;n:</div></td>
            <td width="14%"><div align="left"><strong><?php echo $fecha_tramite ?></strong></div></td>
        </tr>
        
        <tr>
            <td ><div align="right">Solicitante:</div></td>
            <td ><div align="left"><strong><?php echo $iniciador;?></strong></div></td>
            <td >&nbsp;</td>
            <td ><div align="right">Autorizador(es):</div></td>
            <td ><div align="left">
                <strong><?php 
                        $u = new Usuario();
                        foreach ($ruta_autorizadores as $autorizador){
                            $nombre = $u->Get_Nombre($autorizador);
                            if (in_array($autorizador,$autorizaciones)){
                                echo $nombre .  " - AUTORIZADO <br>";
                            } else {
                                echo $nombre .  " - PENDIENTE <br>";
                            }
                        } ?>
                </strong></div>
            </td>
        </tr>						
                    
        <tr>
            <td  width="8%" class="alignRight" valign="top">Motivo de Solicitud:</td>
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
        
        <?php if($t_etapa==ANTICIPO_AMEX_ETAPA_APROBACION){ ?>
        
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
                    <?php echo "$ ". number_format($presupuesto_disponible - $anticipo);?>
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
                <th width="5%">Tipo Viaje</th>
                <th width="4%">Origen</th>
                <th width="4%">Destino</th> 					
                <th width="3%">Salida</th>
                <th width="5%">Horario</th>
                <th width="5%">Aerol&iacute;nea</th>
                <th width="5%"># Vuelo</th>
                <th width="3%">Subtotal</th>
                <th width="3%">IVA</th>
                <th width="3%">TUA</th>
                <th width="3%">Otros Impuestos</th>
                <th width="3%">Total</th>
                <?php 
                    if($_SESSION["perfil"]==4)
                        echo "
                    <th width='3%'>Editar</th>";
                ?>					
            </tr> 

            <?php 
                $sai_monto_vuelo_total = 0;
                $sai_iva_total = 0;
                $sai_tua_total = 0;
                $sai_otrocargo_total = 0;                                                
                $cont=0;
                foreach($aux as $datosAux){
                    
                    $sai_monto_vuelo_total = $sai_monto_vuelo_total + $datosAux["sai_monto_vuelo"];
                    $sai_iva_total = $sai_iva_total + $datosAux["sai_iva"];
                    $sai_tua_total = $sai_tua_total + $datosAux["sai_tua"];
                    $sai_otrocargo_total = $sai_otrocargo_total + $datosAux["sai_otrocargo"];                     
            ?>
                <tr>
                    <td><?php echo $datosAux["sai_tipo_viaje"] ?></td>
                    <td><?php echo $datosAux["sai_origen"] ?></td>
                    <td><?php echo $datosAux["sai_destino"] ?></td>
                    <td><?php echo $datosAux["fecha_salida"]?></td>
                    <td><?php echo $datosAux["sai_hora_salida"]?></td>
                    <td><?php echo $datosAux["sai_aerolinea"]?></td>
                    <td><?php echo $datosAux["sai_numero_transporte"]?></td>
                    <td><?php echo $datosAux["sai_monto_vuelo"]?></td>
                    <td><?php echo $datosAux["sai_iva"]?></td>
                    <td><?php echo $datosAux["sai_tua"]?></td>
                    <td><?php echo $datosAux["sai_otrocargo"]?></td>
                    <td>
                       <div id="divTotalItinerario<?php echo $cont?>"></div>
                    </td>
                  
                  <?php 
                    if($_SESSION["perfil"]==4){					
                    ?>						
                        <td>
                            <div align="center"><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Itinerario'  onclick="flotanteActive(1,<?php echo $datosAux['svi_id']?>)" style="cursor:pointer"/>
                            </div>
                        </td>
                    <?php }
                    ?>
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
                        
    <br />
    
    <?php   
        //muestra detalle de conceptos
        $conceptos = "select
                         c.cp_concepto
                      from
                         sv_conceptos_detalle cd,
                         cat_conceptos c,
                         tramites t
                      where
                         c.dc_id = cd.svc_detalle_concepto AND
                         cd.svc_detalle_tramite = t.t_id AND
                         t.t_id = $t_id";
                        
           // error_log($conceptos);
                        
        $resultado=$cnn->consultar($conceptos);
        $filas = mysql_num_rows($resultado);
        
        if($filas != 0)
        {
            ?>
            <center>
            <div align="center" style="width:30%">
            <br />
            <h3>Conceptos de vi&aacute;ticos</h3>
            <table id="datosConceptos" class="tablesorter">
               <thead>
                    <tr>
                        <th width="2%">No.</th>
                        <th width="30%">Concepto</th>
                    </tr>
              </thead>
              <tbody>
             <?
             for($i=0; $i<$filas; $i++)
             {
             ?>
                <tr>
                   <td width="2%"><? echo $i+1;?></td>
                   <td width="15%"><? echo mysql_result($resultado, $i, "cp_concepto")?></td>
                </tr>
             <?
             }
             ?>
               </tbody>
            </table>
            <br>
            </div>
            </center>
            <?
        }
        else
        {
           echo "<h3>No hay conceptos solicitados</h3>";
        }
    ?>    
    
    
    <br />
    <table id="datosgrales" width="100%">
        <tr>
            <?
            if(!isset($_GET['edit_view']))
            {
            ?>
            <td rowspan="5" class="alignRight" valign="top"><b>Observaciones:<b></td>
            <td width="70%" rowspan="5" class="alignLeft" valign="top"><?echo $observaciones?></td>
            <?
            }
            else
            {
            ?>
            <td rowspan="5" class="alignRight" valign="top">Observaciones:</td>
            <td width="70%" rowspan="5" class="alignLeft" valign="top">
               <textarea name="observ" cols="60" id="observ"><?echo $observaciones?></textarea>
            </td>
            <?
            }

            ?>
            <td ><div align="right">Subtotal:</div></td>			    
            <td ><div align="left">$<input type="text" name="subtotalT" id="subtotalT" value="<?php echo number_format($sai_monto_vuelo_total,2,".",","); ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></div>				
            </td>			   	
        </tr>			
        <tr>				
            <td class="alignRight">IVA:</td>
            <td>$<input type="text" name="iva" id="iva" value="<?php echo number_format($sai_iva_total,2,".",","); ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/></td>
            
        </tr>
        <tr>
            <td class="alignRight">TUA:</td>
            <td>$<input type="text" name="tuaT" id="tuaT" value="<?php echo number_format($sai_tua_total,2,".",","); ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/>
            </td>
            
        </tr>
        <tr>
            <td class="alignRight">Otros impuestos:</td>
            <td>$<input type="text" name="otrosT" id="otrosT" value="<?php echo number_format($sai_otrocargo_total,2,".",","); ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/>
            </td>
            
        </tr>                
        <tr>
            <td class="alignRight">Total:</td>
            <td>$<input type="text" name="TotalT" id="TotalT" value="<?php echo number_format($sai_monto_vuelo_total + $sai_iva_total + $sai_tua_total + $sai_otrocargo_total,2,".",","); ?>" style="background-color:#CCCCCC; width:70px; text-align:right" readonly="readonly"/>
            </td>
            
        </tr>
        <tr><td colspan="5"></td></tr>
        
        <tr>
            <td colspan="5"><br />
              <div align="center"> 
                <input type="submit" value="Autorizar" id="autorizar" name="autorizar"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
                <input type="button" value="Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
                <input type="submit" value="Rechazar" id="rechazar" name="rechazar" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden"/>
                <input type="hidden" id="idT" name="idT" value="<?php echo $_GET['id']?>" />
                <input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
                <input type="hidden" name="ne" id="ne" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
                <input type="hidden" id="cont_itinerarios" name="cont_itinerarios" value="<?php echo $cont?>"/>
              </div>
          </td>				 
        </tr>
    
    </table>


    <!--<<<<TABLE QUE FUNCIONA COMO FLOTANTE>>>>>>>>>>-->
    
    <div id="ventanita" style="visibility:hidden; position:absolute; top:300; left:200; width:40%; background-color:#F0F0FF">
        <table bordercolor="#333333" style="font-size:9px">
            <tr bgcolor="#8C8CFF">
                <td colspan="7" align="right" valign="top"><img src="../../images/close2.ico" alt="Cerrar" align="right" onclick="flotanteActive(0)"/>       </td>
            </tr>
            <tr>
              <td colspan="7"><h3>Editando itinerario</h3></td></tr>
            <tr>
            <tr>
                <td colspan="5" width="50%" class="alignRight">Fecha de Salida:<input type="text"  id="fechasalida" value="" readonly="readonly" style="width:70px;" disabled="disabled"/>
                </td>
                <td >&nbsp;</td>
            </tr>
            <tr>
                <td width="3%">&nbsp;
                    <input type="hidden"  id="id_itinerario" name="id_itinerario" value=""/><input type="hidden" id="fechasalida" name="fechasalida" readonly="readonly" />
                    
              </td>
                <th colspan="2">
                    <div align="left" id="labelPasaje" style="font-size:11px; color:#003366">No. de Autobus:</div>
                </td>					
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="alignRight" width="18%">Costo<span class="style1">*</span>:</td>
                <td width="20%" class="alignLeft">
                    <div id="divCostoViaje"></div>
                </td>
                <td width="5%" class="alignRight">IVA<span class="style1">*</span>:
                </td>
                <td width="18%">
                    <div id="divIva">
                    </div>
                </td>
                <td width="3%" class="alignRight">
                    <div id="eTua" style="visibility">TUA<span class="style1">*</span>:
                    </div>
                </td>
                <td>
                    <div id="divTua"></div>
                </td>					
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td width="37" class="alignRight">&nbsp;</td>
                <td colspan="5" class="alignLeft">					
                </td>
            </tr>
            
            
            <tr>					
                <td colspan="6"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <th  class="alignLeft" style="font-size:11px; color:#003366">Hotel</th>
                <td colspan="5"></td>
            </tr>					
            
            <tr>
                <td>&nbsp;</td>
                <td class="alignRight">						
                    Nombre:
                </td>
                <td>
                    <div id="divNombreHotel" style="font-weight:bold;"></div>
                </td>
                <td class="alignLeft" colspan="2">
                    <div id="divOtrocargo" align="center"></div>														
                </td>
                <td>	
                    
                </td>
                <td class="alignLeft">						
                </td>				
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td class="alignRight">						
                    Tipo Hab:
                </td>
                <td>
                    <div id="divTipoHab" style="font-weight:bold"></div>
                </td>
                <td width="14%" class="alignRight">
                    
                </td>
                <td class="alignLeft" width="12%">	
                    
                </td>
                <td class="alignRight">&nbsp;</td>
                <td class="alignLeft">
                    
                </td>				
            </tr>				
            
            <tr>
                <td>&nbsp;</td>					
                <td class="alignRight">Costo:				  </td>
                <td >
                    <div id="divCostoHotel"></div>
                </td>	
                <td colspan="4"></td>											
            </tr>			
            
            <tr>
                <td></td>
                <td colspan="2" class="alignLeft">
                </td>
                <td colspan="4">&nbsp;</td>
            </tr>
            

            <tr>
                <td colspan="7">
                    <input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>	
                </td>
            </tr>


            <tr>
                <td></td>
                <td colspan="3"></td>
                <td>&nbsp;</td>
                <td ><div align="right"><input name="aceptarnueva" id="aceptarnueva" type="button" value="Aceptar" onclick="return sendUpdate();" disabled="disabled" onmouseout=""/></div>
                </td>
                <td >
                    <input name="aceptarnueva" type="button" value="Cancelar" onclick="flotanteActive(0)"/>
                    
                </td>
            </tr>			
            
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>
    </table>
    </div>
    <div id="Proceso" class="divProceso"></div>
    </form> 

    </center>
      </div>
      <br/>    	
   </body>
   <?php
    echo ('<script language="JavaScript" type="text/javascript">searchSolicitud_amex();</script>');
       if(isset( $_GET['edit_view'])){
            echo "<script language='javascript'>
                    document.getElementById('autorizar').style.visibility = 'visible';
                    document.getElementById('rechazar').style.visibility = 'visible';
                    document.getElementById('coment').style.visibility = 'visible';
                    document.getElementById('comentt').style.visibility = 'visible';
                    </script>";
        }
   ?>	
