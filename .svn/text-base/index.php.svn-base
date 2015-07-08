<?php 
	require_once("./lib/php/constantes.php");
	session_start();

	if(!empty($_SESSION))
		session_destroy();
?>		
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="ISO-8859-1">
			<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
			<link rel='stylesheet' href='css/jqtransform.css' type='text/css' media='all' />
			<link rel='stylesheet' href='css/forms.css' type='text/css' media='all' />
			<script type='text/javascript' src='lib/js/jquery/jquery-1.3.2.js'></script>
			<script type='text/javascript' src='lib/js/jquery/jquery.jqtransform.js' ></script>
			<script type="text/javascript" src="lib/js/jquery/jquery.validate.js"></script>
			<script type='text/javascript' src='lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js'></script>
			<script type='text/javascript' src='lib/js/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.js'></script>
			<link rel='stylesheet' href='lib/js/jquery-ui-1.10.4/css/smoothness/jquery-ui-1.10.4.custom.css' type='text/css' />
			<script type="text/javascript" language="JavaScript" >
				var j = jQuery.noConflict();
				$( document ).ready(function() {
					j("#idUser").html('');
					var validaSub = false;
					var validaSub2 = false;
					$( "#ingresa" ).live( "click", function() {

					var user = $("#user").val();
						var pass = $("#passwd").val();
							if(user != "" && pass != ""){
							var datos = "leido=ok&user="+user+"&pass="+pass;
							$.getJSON( "service.php", datos,  function( json ) {
								if(json.existeUser == true){
									if(json.leido == 1){
											if(validaSub ==false){
												$("#ingresa").attr('type', 'submit');
												$( "#ingresa" ).click();
												validaSub = true;
											}
										}else{
											var input = "<input type='text' name='txtidUser' id='txtidUser' value='"+json.id+"' />";
											j("#idUser").html(input);
											j( "#dialog" ).dialog( "open" );
										}
								}else{
									if(validaSub2 ==false){
										$("#ingresa").attr('type', 'submit');
										$( "#ingresa" ).trigger( "click" );
										validaSub2 = true;
									}	
								}
							});
						}
					});
				});
				function getParameter(name){
					var regexS = "[\\?&]"+name+"=([^&#]*)";
					var regex = new RegExp ( regexS );
					var tmpURL = window.location.href;
					var results = regex.exec( tmpURL );
					if( results == null )
						return "";
					else
						return results[1];
				}					
				
				function validar(){
					var id=getParameter("id");				
					if (id!="" && id){
						document.getElementById("hidTramite").value = id;
						$("#mail").val("1");
					}
				}
				
				$(function(){
					$('form').jqTransform({imgPath:'images/frms'});					
					$("#frm").validate({
						submitHandler: function(form){
						   form.submit();
						},
						messages: {
							user: {
								required: "Por favor ingrese su usuario."	
							},
							passwd: {
								required: "Por favor ingrese su contraseña."	
							}
						}
					});
				});

				function viweTable(){
					document.getElementById("tableMail").style.display ='none';
				}
			</script>
			 <script>
			 j( document ).ready(function() {
				j(function() {
					j( "#dialog" ).dialog({
						height: 300,
						width: 600,
						autoOpen: false,
						show: {
						effect: "blind",
						duration: 1000
						},
						hide: {
						effect: "explode",
						duration: 1000
						},
						 modal: true,
							buttons: {
							ok: function() {
								if (j('#acepto').is(":checked"))
								{
									var usuarioId = j("#txtidUser").val();
									var datos = "updateLeido=ok&usuarioId="+usuarioId;
									$.getJSON( "service.php", datos,  function( json ) {
									});
									alert("Usted confirmo haber leido el Anexo");
									j("#ingresa").attr('type', 'submit');
									j( "#ingresa" ).trigger( "click" );
								}
							j( this ).dialog( "close" );
							}
						}
					});
				});
			});
			</script>			
			<title><?php echo $APP_TITULO ?></title>
			<style type='text/css'>
			#div {
			width: 470px;
			height: 180px;

			-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=78)";
			filter: alpha(opacity=78);
			-moz-opacity: 0.78;
			-khtml-opacity: 0.78;
			opacity: 0.78;
			border: 1px #153bef solid;
			background-color: #ffffff;
			-webkit-border-radius: 30px;
			-moz-border-radius: 30px;
			border-radius: 30px;

			-moz-box-shadow: inset 0px 0px 12px 15px #888888;
			-webkit-box-shadow: inset 0px 0px 12px 15px #888888;
			box-shadow: inset 0px 0px 12px 15px #888888;
			top:0;
			bottom: 0;
			left: 0;
			right: 0;
			margin: auto;			
			}
				<!--
				body{background: #1e3344;}
				.required{text-align:left; font-size:9px; color:#FF0000; }
				.linkps{color:#666666; font-size:11.5px;}
				/* De esta forma si funciona */
				table-layout: fixed;
				/* table {
				table-layout:fixed;
				} NO FUNCIONA */
				-->
			</style>
		</head>

		<body onload="validar()">
			<table width='838' border='0' cellspacing='1'>
				<tr><td colspan='3'>&nbsp;</td></tr>
				<tr>
					<td width='60'>&nbsp;</td>
					<td width='60'>&nbsp;</td>
					<td width='700'>&nbsp;</td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
			</table>

			<form action='valida_usuario.php' method='post' id="frm">
			<div id="error" align="center" style="color:#FF0000">
				<?php if(isset($_GET['error'])) echo "Error de usuario y/o contrase&ntilde;a"?>
			</div>
			<table width='467' height='215' align='center' cellspacing='2' class='login'>
				<tr>
					<td rowspan="5">&nbsp;</td>
					<td rowspan='5' valign="middle"><div align="center"><img src='images/logo2.png' width="140" height="90"></div></td>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr>
					<td rowspan="3">&nbsp;</td>
					<td valign="top">Usuario:</td>
					<td>
						<div align='center' class='rowElem'>
							<span class="required"><input type='text' name='user' id='user' autocomplete="on" class="required" size="15" /></span>
						</div><br /><br />
					</td>
					<td rowspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top">Contrase&ntilde;a:</td>
					<td valign="top">
						<div align='center' class='rowElem'>
							<span class="required"><input type='password' name='passwd' id='passwd' class="required" size="15" /></span>
						</div><br /><br />
					</td>					
				</tr>
				<tr>					
					<td><input type="hidden" name="hidTramite" id="hidTramite" value="0"/></td>
					<td>
						<div align='center' class='rowElem'>
							<span class="submit"><input type="button" name="ingresa" id="ingresa" value="Entrar" /></span>
							<input type="reset" name="reset" id="reset" value="Limpiar" />
							<input type="hidden" name="mail" id="mail" value="0" size="3" />
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<div class="linkps">
							<a href="forget.php" target="_self" ><? echo utf8_decode("¿Olvidaste tu contraseña?"); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>   
			</table>
			</form>
			<div id="div" align="center">
			<table>
				<tr>
					<th>Manuales:</th>
				</tr>
				<tr>
					<td><a href="download.php?file=manualExpenses.pdf" target="_blank"><img src="images/descargar.png" width="20" height="20"></img>Descarga el manual de usuario E-xpenses V 2.0.</a></td>
				</tr>					
				<tr>
					<td><a href="manuales/montos.pdf" target="_blank"><img src="images/pdf.png" width="20" height="20"></img>Anexo 1. Montos autorizados</a></td>
				</tr>				
				<tr>
					<td><a href="manuales/requisitos.pdf" target="_blank"><img src="images/pdf.png" width="20" height="20"></img>Anexo 2. Requisitos fiscales</a></td>
				</tr>
				<tr>
					<td><a href="manuales/tablas.pdf" target="_blank"><img src="images/pdf.png" width="20" height="20"></img>Anexo 3. Tablas de distancias entre ciudades autorizadas</a></td>
				</tr>
				<tr>
					<td><a href="manuales/politicas.pdf" target="_blank"><img src="images/pdf.png" width="20" height="20"></img>Anexo 4. Pol&iacute;ticas de gastos</a></td>
				</tr>			
			</table>
			</div>
				<div id="dialog" title="Alerta">
				<div id="idUser" style="display:none"></div>
				
				<p>Es muy importante que lea con atenci&oacute;n las pol&iacute;ticas de gastos de la compa&ntilde;&iacute;a antes de ingresar al sistema, las cuales puede consultar haciendo clic en el link  <a href="manuales/politicas.pdf" target="_blank"><img src="images/pdf.png" width="20" height="20"></img>"Anexo 4. Pol&iacute;ticas de gastos"</a></p>
				<p><input type="checkbox" id="acepto" name="acepto" value="Acepto" />Acepto haber le&iacute;do la pol&iacute;tica y estoy de acuerdo en la aplicaci&oacute;n de la misma</p>
				</div>			
		</body>
	</html>