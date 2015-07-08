<?php
	session_start();
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";	

		if(isset($_POST["obtener_privilegios"]))
			guardar();
		elseif(isset($_POST["omitir"])){	
			$_SESSION["perfil"]	= "1";
			$_SESSION['delegado'] = 0;
			exit(header("Location: ../../inicial.php"));
		}

		
		function guardar(){	
			$_SESSION['perfil'] = $_POST['tipoUser'];
			$u = new Usuario();
			$u->Load_Usuario_By_ID($_POST['usuario_dueno']);
			$_SESSION['idrepresentante'] = $_POST['idusuario'];
			$_SESSION['representante'] = $_SESSION['usuario'];
			$_SESSION['idusuario'] = $_POST['usuario_dueno'];
			$_SESSION['usuario'] = trim($u->Get_dato("u_paterno"))." ".trim($u->Get_dato("u_materno"))." ".trim($u->Get_dato("u_nombre"));
			$_SESSION['privilegios'] = $u->obtenerPrivilegios($_SESSION['idusuario'], $_SESSION['idrepresentante']);
			
			exit(header("Location: ../../inicial.php"));
		}		
?>

	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="ISO-8859-1">
			<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
			<link rel='stylesheet' href='../../css/jqtransform.css' type='text/css' media='all' />
			<link rel='stylesheet' href='../../css/forms.css' type='text/css' media='all' />			
			<script type='text/javascript' src='../../lib/js/jquery/jquery-1.10.1.min.js'></script>
			<script type='text/javascript' src='../../lib/js/jquery/jquery-1.3.2.js'></script>
			<script type='text/javascript' src='../../lib/js/jquery/jquery.jqtransform.js' ></script>
			<script type="text/javascript" src="../../lib/js/validateForm.js"></script>
			<script type="text/javascript">
			
				doc = $(document);
				doc.ready(inicializarEventos);
				
				function inicializarEventos(){
					var frm=document.completar_registro;
					var idusuario=$("#usuario_dueno").val();
					cargarinfousuario(idusuario);
				}

				$(function(){
					$('form').jqTransform({imgPath:'images/frms'});					
					$("#frm").validate({
						submitHandler: function(form){
						   form.submit();
						}
					});
				});
				
				function cargarinfousuario(valor){
					var frm=document.completar_registro;
					if(valor!=""){
						$.ajax({
							type: "POST",
							url: "../../admin/usuarios/services/ajax_usuario.php",
							data: "uid="+valor,
							dataType: "json",
							async: false, 
							timeout: 10000,
							success: function(json){
								$("#tipoUser").val(json.tipo);				
							},
							complete: function(json){
								$.ajax({
									type: "POST",
									url: "../../admin/usuarios/services/ajax_usuario.php",
									data: "uid_tipo_usuario="+valor+"&uid_delegado="+$("#idusuario").val(),
									dataType: "json",
									async: false, 
									timeout: 10000,
									success: function(json2){
										frm.tipoUser.options[0] = new Option('Seleccione...', '');
										LimpiarCombo(frm.tipoUser);
										for(var i=0;i<json2.length;i++){
											var str=json2[i];
											frm.tipoUser.options[frm.tipoUser.length] = new Option(str.tu_nombre,str.id_tipo);
										}
									},
									complete: function(json2){
										 $('#obtener_privilegios').removeAttr("disabled");
									}, 
									error: function(x, t, m) {
										if(t==="timeout") {
											location.reload();
											abort();
										}
									}
								});
							}, 
							error: function(x, t, m) {
								if(t==="timeout") {
									location.reload();
									abort();
								}
							}
						});
					}
				}

				function LimpiarCombo(combo){
					while(combo.length > 0){
						combo.remove(combo.length-1);
					}
				}
			</script>
			<style type="text/css">
				.Estilo1 {color: #FF0000}
				body{background: #1e3344;}
				.required,.login{font-type=arial; font-family:sans-serif; font-size:12pt;}
				.linkps {color:#666666; font-size:11.5px;}
				/* De esta forma si funciona */
				table-layout: fixed;
			</style>
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
			<form name="completar_registro" method="post">
			<table width='467' height='235' align='center' cellspacing='2' class='login'>	
				<tr><td colspan="4">&nbsp;</td></tr>
				<tr>
					<td rowspan="5">&nbsp;</td>
					<td rowspan='4' valign="bottom">
						<div align="center"><img src='../../images/logo2.png' width="145px" height="130px"></div>
					</td>
					<td colspan="2">&nbsp;</td>
					<td rowspan="5">&nbsp;</td>
				</tr>		
				<tr><td colspan="2" valign="top" ><strong>Selecciona el usuario con el que deseas entrar:</strong></td></tr>
				<tr>				
					<td align="right" width="30%">Usuario<span class="Estilo1">*</span>:</td>
					<td>
						<select name="usuario_dueno" id="usuario_dueno" onchange="cargarinfousuario(this.value);">
						<?php 				
						$Usu = new Usuario();
						$con = new Conexion();
						$sql = sprintf("SELECT u_id,u_nombre,u_paterno, u_materno FROM usuario AS u JOIN usuarios_asignados AS ua ON u.u_id=ua.id_delegado WHERE ua.id_asignador='".$_SESSION['idusuario']."' GROUP BY u_id ORDER BY u_nombre");
						$var = $con->consultar($sql);
						while($arr = mysql_fetch_assoc($var)){
							echo sprintf("<option value='%s'>%s %s %s</option>", $arr['u_id'], ($arr['u_nombre']), ($arr['u_paterno']), ($arr['u_materno']));                       
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td align="right" width="30%">Tipo de usuario:</td>
					<td><select id="tipoUser" name="tipoUser"></select></td>
				</tr>
				<tr>
					<td colspan="3" align="center">
					<input type="hidden" name="idusuario" id="idusuario" value="<?php echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
					<input type="submit" value="Aceptar" name="obtener_privilegios" id="obtener_privilegios" disabled="disabled" />					
					<input type="submit" value="Omitir" name="omitir" id="omitir" />
					</td>
				</tr>
				<tr><td colspan="4">&nbsp;</td></tr>
			</table>
			</form>
		</body>
	</html>