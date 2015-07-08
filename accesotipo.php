<?php
	session_start();
	require_once("./lib/php/constantes.php");
	require_once("$RUTA_A/Connections/fwk_db.php");
	require_once("$RUTA_A/functions/utils.php");	

	if(isset($_POST["obtener_privilegios"]))
		guardar();
	elseif(isset($_POST["volver"]))
		exit(header("Location: ./index.php"));        
	
	
	/**
 	 * Funcion Guardar!
	 *
	 * Cuando el tipoUser es 1 o 3, si el usuario tiene delegados, envia a la pantalla de delegados
	 * Cuando el tipoUser es 1 o 3, si el usuario no tiene delegados, manda a la pantalla inicio
	 * De lo contrario cuando el tipo de usuario sea 2 se enviara a la pantalla del administrador
	 */
	function guardar(){		
		$U = new Usuario();
		$U->Load_Usuario($_SESSION["idusuario"]);		
		
		if($_POST["tipoUser"] == "1" || $_POST["tipoUser"] == "3"){
			if($U->find_delegaciones($_SESSION["idusuario"]))
				exit(header("Location: ./flujos/delegacion/delegaciones.php"));
			else{
				$_SESSION["perfil"]	= ($_POST["tipoUser"]);
				exit(header("Location: ./inicial.php"));
			}
		}else{
			$_SESSION["perfil"]	= ($_POST["tipoUser"]);			
			if($_SESSION["perfil"] == 2)
				exit(header("Location: ./admin/index.php"));
			else if($_SESSION["perfil"] == 11)
				exit(header("Location: ./fiscal/index.php"));
			else if($_SESSION["perfil"] == 12)
				exit(header("Location: ./rh/index.php"));
			else
				exit(header("Location: ./inicial.php"));
		}
	}
 ?>

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
			<script type="text/javascript">
				function cargarinfousuario(valor){
					if(valor!=""){
						$.ajax({
							type: "POST",
							url: "../../admin/usuarios/services/ajax_usuario.php",
							data: "uid="+valor,
							dataType: "json",
							success: function(json){
								$("#tipoUser").val(json.tipo);
							}
						});
					}
				}
				$(function(){
					$('form').jqTransform({imgPath:'images/frms'});										
				});
			</script>
			<style type="text/css">
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
				<tr>
					<td rowspan="5">&nbsp;</td>
					<td rowspan='4' valign="middle">
						<div align="center"><img src='images/logo2.png' width="140" height="90"></div>
					</td>
					<td colspan="2">&nbsp;</td>
					<td rowspan="5">&nbsp;</td>
				</tr>
				
				<tr><td colspan="2" valign="top" ><strong>Accesar con el perfil de:</strong></td></tr>
				<tr>
					<td align="right" >Tipo de usuario:</td>
					<td>						
						<select id="tipoUser" name="tipoUser">
							<?php
							$Usu = new Usuario();				
							foreach($Usu->Load_tipo_usuario_id($_SESSION["idusuario"]) as $datos)
								echo '<option value="'.$datos['ut_tipo'].'">'.$datos['tu_nombre'].'</option>';						
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<span class="submit"><input type="submit" value="Aceptar" name="obtener_privilegios" id="obtener_privilegios" /></span>						
						<span class="submit"><input type="submit" value="Volver" name="volver" id="volver" /></span>
					</td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
			</table>				
			</form>
		</body>
	</html>
		