<?PHP
	/**
	 * Registro & Edicion de Comprobaciones => Vista
	 * Creado por: IHV 2013-06-13
	 */	 
?>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script> 
	<script type="text/javascript" src="../../lib/js/formatNumber.js"></script>
	<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>	
	<script type="text/javascript" src="../../lib/js/jquery/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.price_format.1.6.min.js"></script>		
	<script type="text/javascript" src="js/backspaceGeneral.js"></script>	
	<script type="text/javascript" src="js/configuracion.js"></script>
	<script type="text/javascript" src="js/cargaDatos.js"></script>
	<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/smoothness/jquery-ui-1.10.3.custom.min.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
	
	<style>
		.style1{
			color: #FF0000
		}
		.tablaDibujada{			
			border: 1px #CCCCCC solid;
			margin: auto;
			margin-top: 5px;
			text-align:left;
			width: 785px;
		}
		.button_agregar{
			background:url(../../images/add.png); 
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.button_ok{
			background:url(../../images/ok.png);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.button_cancelar{
			background:url(../../images/reject.png);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.button_enviar{
			background:url(../../images/save.gif);
			background-position:left; 
			background-repeat:no-repeat; 
			background-color:#E1E4EC;
		}
		.div_flotante{
			background-color: #FAFAFA;
			position:absolute; 
			margin-left:0%; 
			margin-right:0%; 
			margin-top:0%; 
			border:1px solid #808080; 
			padding: 5px; 			
		}
		#div_gasolina{
			top:115%; 
			left:35%; 
			rigth:35%; 
			width:450px; 		
		}
		#div_nuevoProveedor{
			top:50%;
			left:25%; 
			rigth:25%; 
			width:650px; 
		}
		.div_close{
			font-size:11px; 
			bordercolor:#333333:
			border: px1;
			background-color: #A9A9F5;
			color: #FFFFFF;
			width: 100%;
			font-weight: bold;
		}
		.montos_rojos{
			color: #DF0101;
		}
    </style>	
	
	<div id="Layer1">
		<center><h3>Detalle de la Comprobaci&oacute;n</h3></center>
        <form action="controller_comprobacion_travel.php" method="post" name="form_comprobacion" id="form_comprobacion">		
			<!-- Inicia Datos Generales de la Comprobaciones -->
			<table class="tablaDibujada">
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr>
					<td rowspan="4" width="70px"></td>
					<td colspan="4">
						Solicitud<span class="style1">*</span>:
						<select name="solicitud" id="solicitud"></select> 
					</td>
				</tr>					
				<tr id="tr_gasolina">
					<td>
						Motivo<span class="style1">*</span>: 
						<input name="motivo" id="motivo" size="25" />						
					</td>
					<td colspan="3">
						Fecha Inicial<span class="style1">*</span>: 
						<input name="fechaInicial" id="fechaInicial" size="12" value=""readonly />
						Fecha Final<span class="style1">*</span>: 
						<input name="fechaFinal" id="fechaFinal" size="12" value=""readonly />						
					</td>
				</tr>					
				<tr>
					<td colspan="4">
						Seleccione el tipo de comprobaci&oacute;n que desea realizar<span class="style1">*</span>:                            
						<select name="tipoComprobacion" id="tipoComprobacion"></select>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						Centro de Costos al que se cargar&aacute;<span class="style1">*</span>:
						<select name="centroCostos" id="centroCostos"  ></select>
					</td>
				</tr>
				<tr><td colspan="5">&nbsp;</td></tr>
			</table>
			<!-- Fin Datos Generales de la Comprobaciones -->				
			<br />				
				
			<!-- Inicia Datos AMEX -->
			<div id="div_amex" >
				<table class="tablaDibujada">
					<tr>
						<td rowspan="10">&nbsp;</td>
						<td colspan="2"><h3 align="center">Amex</h3></td>
						<td rowspan="10">&nbsp;</td>
					</tr>
					<tr>
						<td>
							Tipo de tarjeta<span class="style1">*</span>: 
							<select name="tipoTarjeta" id="tipoTarjeta"></select>
						</td>
						<td>
							Tarjeta de Cr&eacute;dito:&nbsp;
							<div name="noTarjeta" id="noTarjeta"></div>
						</td>							
					</tr>
					<tr>
						<td colspan="2">
							Lista de Cargos<span class="style1">*</span>: 
							<select name="cargoTarjeta" id="cargoTarjeta">
							<option id="0" value="0">- Seleccione -</option>
							</select>
						</td>							
					</tr>
					<tr>
						<td colspan="2">
							No. Transacci&oacute;n:
							<div id="div_noTransaccion"></div>
						</td>							
					</tr>
					<tr>
						<td colspan="2"><h3 align="center">Detalle del cargo</h3></td>
					</tr>
					<tr>
						<td>
							Establecimiento:
							<div id="div_establecimiento" ></div>
						</td>
						<td>
							Total origen factura: 
							<div id="div_montoCargo"></div>
							<input type="hidden" id="montoCargo" name="montoCargo" />
							<input type="hidden" id="monedaCargo" name="monedaCargo" />							
						</td>
					</tr>
					<tr>
						<td>
							Fecha: 
							<div id="div_fechaCargo"/>							
						</td>
						<td>
							Total Amex: 
							<div id="div_totalAmex" ></div>
							<input type="hidden" id="totalAmex"/>							
						</td>                            
					</tr>
					<tr>
						<td>
							RFC: 							
							<div id="div_rfc"></div>
						</td>
						<td>
							Total MXN: 
							<div id="div_totalPesos" ></div>
							<input type="hidden" id="totalPesos" name="totalPesos"/>							
						</td>							
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							Tipo de cambio: 
							<div id="div_tipoCambio"></div>
							<input type="hidden" id="tipoCambio" name="tipoCambio"/>							
						</td>							
					</tr>                       
					<tr><td colspan="2">&nbsp;</td></tr>
				</table>     
			</div>              
			<!-- Fin Datos AMEX -->
				
				
			<!-- Inicioa captura de Partidas -->
			<table id="table_regisrtoPartidas" class="tablaDibujada">
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr>
					<td rowspan="14">&nbsp;</td>
					<td colspan="6"><h3 align="center">Registro de Partidas</h3></td>
					<td rowspan="14">&nbsp;</td>
				</tr>
				<tr>
					<td>Concepto<span class="style1">*</span>:</td>
					<td colspan="3"><select name="concepto" id="concepto" ></select></td>
					<td>Fecha Comprobante<span class="style1">*</span>:</td>
					<td><input name="fecha" id="fecha" size="12" value="" readonly /></td>                        
				</tr>
				<tr id="tr_tipoComida">
					<td>Tipo<span class="style1">*</span>:</td>
					<td><select name="tipoComida" id="tipoComida" /></select></td>
					<td colspan="4"></td>
				</tr>
				<tr> 
					<td>Proveedor Nacional:</td>
					<td><input type="checkbox" name="proveedorNacional" id="proveedorNacional"  /></td>
					<td colspan="2"><a href="http://www.oanda.com/lang/es/currency/converter/" target="_black">Realiza la conversi&oacute;n a tu divisa</a></td>
					<td colspan="2" rowspan="7" valign="top">
						<!-- Inicio Pantalla de Proveedores -->						
						<table id="table_proveedor" class="tablaDibujada" style="width:250px">
							<tr>
								<td rowspan="6">&nbsp;</td>
								<td colspan="4" ><h3 align="center">Datos de proveedor</h3></td>
								<td rowspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td>Folio Factura<span class="style1">*</span>:</td>
								<td><input type="text" name="folio" id="folio" size="8"/></td>										
							</tr>
							<tr>
								<td>RFC<span class="style1">*</span>:</td>
								<td><input type="text" name="rfc" id="rfc" value="" size="13" maxlength="13" /></td>										
							</tr>	
							<tr>
								<td>Raz&oacute;n Social<span class="style1">*</span>:</td>
								<td><input name="proveedor" type="text" id="proveedor" value="" size="24" /></td>										
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td colspan="2" align="center">
									<input class="button_agregar" type="button" name="muestraAgregarProveedor" id="muestraAgregarProveedor" value="     Agregar Nuevo Proveedor" />											
								</td>										
							</tr>
						</table>						
						<!-- Fin Pantalla de Proveedores -->							
					</td>                        
				</tr>
				<tr>             
					<td>Subtotal/Monto<span class="style1">*</span>:</td>
					<td><input name="monto" id="monto" value="0" size="12"/></td>                       
					<td>Divisa<span class="style1">*</span>:</td>
					<td><select name="divisa" id="divisa"></select></td>
				</tr>
				<tr>                    
					<td>Total<span class="style1">*</span>: </td>
					<td><input name="total"  id="total" value="0.00" size="12" /></td>
					<td id="td_iva1">IVA<span class="style1">*</span>: $</td>
					<td id="td_iva2"><input name="iva" id="iva" value="0" size="8" /></td>
				</tr>                   
				<tr>
					<td>Total en pesos:</td>
					<td colspan="3"><input name="totalPartida" value="0.00" id="totalPartida" size="12" /></td>                        
				</tr>
				<tr>
					<td>Comentario<label id="label_comentarioReq"></label>:</td>
					<td colspan="3"><textarea name="comentario" id="comentario" cols="50" rows="3" maxlength="36" /></textarea></td>                        
				</tr>
				<tr id="tr_asistentes">
					<td>No. Asistentes<span class="style1">*</span>:</td>
					<td colspan="3"><input name="asistentes" id="asistentes" value="1" size="10"/></td>						
				</tr>
				<tr id="tr_propina">
					<td>Propina:</td>
					<td colspan="3"><input name="propina" value="0" id="propina" size="10" /></td>					
				</tr>
				<tr id="tr_impuestoHospedaje">                        
					<td>Impuesto hospedaje<span class="style1">*</span>:</td>
					<td colspan="5"><input name="impuestoHospedaje" value="0" id="impuestoHospedaje" size="10" /></td>						
				</tr>
				<tr><td colspan="6">&nbsp;</td></tr>
				<tr><td colspan="6">&nbsp;</td></tr>        
			</table>
			<!-- Fin captura de Partidas -->							
            
			<br/>
			
			<!-- Inicio Div Gasolina -->				
			<div id="div_gasolina" class="div_flotante" >
				<table class="div_close">
					<tr>
						<td>Comprobaci&oacute;n de Gasolina</td>
						<td width="16px"><img src="../../images/close2.ico" id="cerrar_div_gasolina"alt="Cerrar" align="right" style="cursor:pointer"/></td>
					</tr>
				</table>
				
				<table  class="tablaDibujada" style="width:445px">
					<th colspan="3"><h3 align="center">Comprobaci&oacute;n de Gasolina</h3></th>
					<tr>
						<td>Modelo de Auto<span class="style1">*</span>:</td>
						<td><select name="modeloAuto" id="modeloAuto">
							<option id="0" value="0">- Seleccione -</option>
							</select></td>
						<td><div id="div_factor" name="div_factor"></div></td>
					</tr>
					<tr>
						<td>Kilometraje<span class="style1">*</span>:</td>
						<td><input name="kilometraje" id="kilometraje"  value="0.00"></td>
						<td><a target="_blank" href="https://maps.google.com/">Buscar Kilometraje</a></td>
					</tr>
					<tr>
						<td>Monto de gasolina<span class="style1">*</span>:</td>
						<td><input name="monto_gasolina" value="0.00" id="monto_gasolina"></td>
						<td>MXN</td>
					</tr>
					<tr>
						<td>Ruta detallada<span class="style1">*</span>:</td>
						<td colspan="2"><textarea name="ruta_detallada" id ="ruta_detallada" cols="40" rows="5"></textarea></td>
					</tr>
					<tr>
						<td colspan="3" align="center">
							<input class="button_enviar" type="button" value="     Aceptar" name="enviarComprobacionGasolina" id="enviarComprobacionGasolina" />
							<input class="button_cancelar" type="button" value="     Cancelar" name="cancelarGasolina" id="cancelarGasolina" />
						</td>
					</tr>
				</table>
			</div>
			<!-- Fin Div Gasolina -->
			<br/>							
			
			<!-- Inicio Div Proveedores -->                
			<div id="div_nuevoProveedor" class="div_flotante">
				<table class="div_close">
					<tr>
						<td>Pantalla proveedores</td>						
						<td><img src="../../images/close2.ico" id="cerrar_div_nuevoProveedor" alt="Cerrar" align="right" style="cursor:pointer"/></td>
					</tr>
				</table>
				<br />
				<table class="tablaDibujada" style="width:300px">
					<tr><td colspan="2" align="center"><h3>Agregar nuevo proveedor</h3></td></tr>
					<tr>
						<td align="right">Raz&oacute;n Social<span class="style1">*</span>:</td>
						<td><input name="nuevoProveedor" type="text" id="nuevoProveedor" value="" size="50" /></td>
					</tr>
					<tr>
						<td align="right">RFC<span class="style1">*</span>:</td>
						<td><input name="nuevoRfc" type="text" id="nuevoRfc" value="" size="30" maxlength="13"></td>
					</tr>
					<tr>
						<td align="right">Domicilio Fiscal<span class="style1">*</span>:</td>
						<td><input name="nuevoDomicilio" id="nuevoDomicilio" value="" size="80" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<input class="button_ok" type="button" name="agregarProveedor" id="agregarProveedor" value="     Agregar"  />
							<input class="button_cancelar" type="button" name="cancelarAgregarProveedor" id="cancelarAgregarProveedor" value="      Cerrar Panel de Nuevo Proveedor"/>
						</td>
					</tr>
				</table>
			</div>
			<!-- Fin Div Proveedores -->
			
			<br />
			<div id="div_mensajes" align="center" ></div>
			<br />
			
			<div align="center"> 
				<input class="button_ok" name="registrarPartida" type="button" id="registrarPartida" value="     Registrar gasto" />					
			</div>
        
            <center><h3>Detalle de la Comprobaci&oacute;n</h3></center>             
			<!-- Inicio Tabla de Partidas -->
            <table id="comprobacion_table" class="tablesorter" cellspacing="1"> 
                <thead> 
                    <tr> 
                        <th>No.</th>
                        <th>Tipo</th>
						<th>No. Trasacci&oacute;n</th>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Comentario</th> 
                        <th>No. Asistentes</th> 
                        <th>RFC</th>
                        <th>Proveedor</th>
                        <th>Factura</th>
                        <th>Monto </th> 
                        <th>IVA</th>
                        <th>Propina</th>
                        <th>Impuesto Hospedaje</th>
                        <th>Total</th>
						<th>Divisa</th>
						<th>Total MXN</th>
						<th>Editar</th>
                        <th>Eliminar</th>
                    </tr> 
                </thead> 
                <tbody>                     
                </tbody> 
            </table>  
			<!-- Fin Tabla de Partidas -->
			
			<!-- Inicio Tabla de PArtidas -->
            <table id="excepcion_table" class="tablesorter" cellspacing="1"> 
				<thead> 									
                    <tr> 
                        <th>No.</th>
                        <th>Concepto</th>
						<th>Mensaje</th>
						<th>Fecha</th>
						<th>Partidas</th>
						<th>Total</th>
                        <th>Excedente</th>
                    </tr> 
                </thead> 
                <tbody>                     
                </tbody> 
            </table>  
			<!-- Fin Tabla de Partidas -->
			
            </br>
            </br>
			<!-- Iniciao Observaciones y Resumen -->
            <div align="center">            	
                <table>
                   	<tr id="tr_historialObservaciones">
                   	 	<td colspan="2" align="center">
							<table>
								<tr>
									<td>Historial de observaciones: </td>
									<td>
										<textarea name="historialObervaciones" id="historialObervaciones" cols="70" rows="6" ></textarea>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table align="center">
								<tr>
									<td>Observaciones:</td>
									<td>
										<textarea name="observaciones" cols="70" rows="6" id="observaciones"></textarea>
									</td>                        
								</tr>
							</table>
                        </td>				
                        <td>
							<table class="tablaDibujada" style="width:300px; text-align:right;" >
                                <tr>
                                    <td colspan="2" style="text-align: center;"><h3>Resumen</h3></td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr>
                                    <td width="50%">Anticipo solicitado:</td>
                                    <td> 
										<div id="div_anticipo">$ 0.00</div>
										<input type="hidden" name="anticipo" id="anticipo" value="0" />
									</td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr>
                                    <td>Anticipo comprobado: </td>
                                    <td>
										<div id="div_anticipoComprobado">$ 0.00</div>
										<input type="hidden" name="anticipoComprobado" id="anticipoComprobado" value="" />
									</td>
                                </tr><tr>
                                    <td>Personal a descontar: </td>
                                    <td>
										<div id="div_personalComprobado" >$ 0.00</div>
										<input type="hidden" name="personalComprobado" id="personalComprobado" value="" />
									</td>
                                </tr>
                                <tr>
                                    <td>Amex comprobado:  </td>
                                    <td>
										<div id="div_amexComprobado" >$ 0.00</div>
										<input type="hidden" name="amexComprobado" id="amexComprobado" value="" />
									</td>
                                </tr>
								<tr>
                                    <td>Efectivo comprobado:  </td>
                                    <td>
										<div id="div_efectivoComprobado">$ 0.00</div>
										<input type="hidden" name="efectivoComprobado" id="efectivoComprobado" value="" />
									</td>
                                </tr>                                
								<tr>
                                    <td>Amex externo:  </td>
                                    <td>
										<div id="div_amexExternoComprobado">$ 0.00</div>
										<input type="hidden" name="amexExternoComprobado" id="amexExternoComprobado" value="" />
									</td>
                                </tr>
								<tr><td colspan="2">&nbsp;</td></tr>
                                <tr class="montos_rojos">
                                    <td>Monto a descontar:</td>
                                    <td>
										<div id="div_montoDescontar">$ 0.00</div>
										<input type="hidden" name="montoDescotar" id="montoDescontar" value="" />
									</td>
                                </tr>
                                <tr class="montos_rojos"> 
                                    <td>Monto a reembolsar:</td>
                                    <td>
										<div id="div_montoReembolsar">$ 0.00</div>
										<input type="hidden" name="montoReembolsar" id="montoReembolsar" value="" />
									</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
			<!-- Fin Observaciones y Resumen -->

            <br />
			<br />                
			<table id="table_botones" align="center">
				<tr align="center">
					<td>
						<input class="button_enviar" type="button" id="guardarPrevio" name="guardarPrevio" value="      Guardar Previo"  />							
					</td>
					<td>&nbsp;</td>
					<td>
						<input class="button_enviar"type="button" id="enviarComprobacion" name="enviarComprobacion" value="      Enviar Comprobacion" /> 
					</td>
				</tr>
			</table>
			
			<input type="text" name="idusuario" id="idusuario" value="<?PHP echo $_SESSION["idusuario"];?>" />
			<input type="text" name="empresa" id="empresa" value="<?PHP echo $_SESSION["empresa"];?>" />
			<input type="text" name="perfil" id="perfil" value="<?PHP echo $_SESSION["perfil"];?>" />			
			<input type="text" name="tipoViaje" id="tipoViaje" value="" />			
			<input type="text" name="diasViaje" id="diasViaje" value="" />			
			<input type="text" name="region" id="region" value="" />			
			<input type="text" name="tramiteEdit" id="tramiteEdit" value="" />	
			<input type="text" name="totalPartidas" id="totalPartidas" value="" />	
			<input type="text" name="totalExcepciones" id="totalExcepciones" value="" />	
		</form>
	</div>