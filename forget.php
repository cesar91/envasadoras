	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="ISO-8859-1">
			<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
			<link rel='stylesheet' href='css/jqtransform.css' type='text/css' media='all' />
			<link rel='stylesheet' href='css/forms.css' type='text/css' media='all' />			
			<script type='text/javascript' src='lib/js/jquery/jquery-1.10.1.min.js'></script>
			<script type='text/javascript' src='lib/js/jquery/jquery-1.3.2.js'></script>
			<script type='text/javascript' src='lib/js/jquery/jquery.jqtransform.js' ></script>
			<script type="text/javascript" src="lib/js/validateForm.js"></script>			
			<style type='text/css'>
			<!--
			body{background: #1e3344;}
			.required{text-align:left; font-size:13px; color:#FF0000;}
			.linkps{color:#333333; font-size:14.5px;}
			-->
			</style>	
			<script language="javascript">
				function sendRecovery(){	
					console.log("53w5");
					var url = "admin/skill/ingreso_sin_recargar_proceso_usuario.php";
					$.post(url,{recoveryMail: $("#recoveryMail").val()}, 
						function (data){
							$("#divAlert").html(data); 
							$("#recoveryMail").val("")
						}
					);
				}
				
				$(function(){
					$('form').jqTransform({imgPath:'images/frms'});		
					
					
					$("#ingresa").click(function(){	
						var url = "admin/skill/ingreso_sin_recargar_proceso_usuario.php";
						$.post(url, {recoveryMail:$("#recoveryMail").val()}, 
						function(data){							
							$("#divAlert").html(data).hide().fadeIn();
							$("#recoveryMail").val("");
						});			
					});				
				});
			</script>
		</head>
		<body>
			<table width='838' border='0' cellspacing='1'>
				<tr><td colspan='3'>&nbsp;</td></tr>
				<tr>
					<td width='60'>&nbsp;</td>
					<td width='60'>&nbsp;</td>
					<td width='700'>&nbsp;</td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
			</table>	
			<form method='post' id="frm">
			<table width='467' height='235' align='center' cellspacing='2' class='login'>	
				<tr><td colspan="5" style="font-size:24px;">&nbsp;</td></tr>
				<tr>
					<td rowspan="6">&nbsp;</td>
					<td rowspan="5" valign="middel"><div align="center"><img src='images/logo2.png' width='140' height='90'></div></td>
					<td colspan="2" style="font-size:13px; text-align: justify;">Si olvidaste tu contrase&ntilde;a, ingresa el correo electr&oacute;nico con el que estas registrado y te enviaremos un mail con tu usuario y password.</td>
					<td rowspan="6">&nbsp;</td>
				</tr>
				<tr><td colspan="2" style="font-size:1px;">&nbsp;</td></tr>				
				<tr>
					<td valign="top">Correo Electr&oacute;nico:</td>
					<td width="55%"><div class='rowElem'><input type='recoveryMail' name='recoveryMail' id='recoveryMail' size="15"/></div></td>					
				</tr>
				<tr><td align='center' colspan="2"><input type="button" name="ingresa" id="ingresa" value="Enviar"/></td></tr>
				<tr>
					<td colspan="2" colspan="2" style="font-size:13px;"><span id="divAlert" class="required" style="font-size:13px;"></span></td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<div class="linkps"><a href="index.php" target="_self" >Ingresar al sistema</a></div>					
					</td>
				</tr>
				<tr><td colspan='5'>&nbsp;</td></tr>				
			</table>
			</form>
		</body>
	</html>	