<?php
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	require_once "$RUTA_A/functions/CentroCosto.php";

	if(isset($_POST["registrar"])){
		Guardar();
	}else if(isset($_POST["cancelar"])){
		header("Location: ./index.php");
		exit();
	}else{		
		Muestra();
	}
		
	function Guardar(){
		$ceco = new CentroCosto();
		if(isset($_POST['nombre']) && $_POST['nombre']!="" &&
		   isset($_POST['codigo']) && $_POST['codigo']!="" &&
		   isset($_POST['empresa_id']) && $_POST['empresa_id']!="" ){
			$nombre = $_POST['nombre'];
			$codigo = $_POST['codigo'];
			$empresa_id = $_POST['empresa_id'];
			$estatus = $_POST['estatus'];
			$responsable = $_POST['responsable'];
			$directorarea = $_POST['directorarea'];			
			
			$ceco->Nuevo_CentroCosto($nombre,$codigo,$empresa_id,$estatus,$responsable,$directorarea);
			header("Location: index.php?oksave");
		}else{
			header("Location: index.php?error");
		}
	}
?>
	<link rel="stylesheet" href="../../lib/js/jquery-ui-1.10.4/development-bundle/themes/base/jquery.ui.all.css">
	<script src="../../lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
	<script language="javascript">
		$(document).ready(function(e){
			$("#registrar").on("click",function(){
			var nombre = $("#nombre").val();
			var codigo = $("#codigo").val();
			var empresa = $("#empresa_id").val();
			var url = "services/Ajax_cecos.php";
			var accion = "?accion=selectcco";
			var returnVal = true;
				if(nombre == ""){
					alert("El nombre es un campo requerido.");
					return false;
				}
				if(codigo == ""){
					alert("El código es un campo requerido.");
					return false;
				}
				if(empresa == 0){
					alert("Debes elegir una empresa");
					return false;
				}
				$.ajaxSetup({async:false});
				 var datos = "codigo="+codigo;
				$.post(url+accion,datos,function(data){
					if(data != ''){
						alert("El código ya ha sido asignado a otro Centro de Costos.");
						returnVal = false;
						return false;
					}
				});
				return returnVal;
			});
			$("#empresa_id").on("change",function(){
				var empresa = $("#empresa_id").val();				
				var url = "services/Ajax_cecos.php";
				var accion = "?accion=selectresponable";
				var i = 0;
				var datos = "empresa="+empresa;
				var html = "";
				$.getJSON(url+accion,datos,function(data){
					html += "<select name='responsable'>";
					if(data.usuario != null){
						for(i=0; i<data.usuario.length; i++){
							html += "<option value='"+data.usuario[i]+"'>"+data.nombre[i]+"</option>";
						}
					}
					html += "</select>";
					$("#divResponsable").html(html);					
				});
			});
			$("#empresa_id").on("change",function(){
				var empresa = $("#empresa_id").val();				
				var url = "services/Ajax_cecos.php";
				var accion = "?accion=selectresponable";
				var i = 0;
				var datos = "empresa="+empresa;
				var html = "";
				$.getJSON(url+accion,datos,function(data){
					html += "<select name='directorarea'>";
					if(data.usuario != null){
						for(i=0; i<data.usuario.length; i++){
							html += "<option value='"+data.usuario[i]+"'>"+data.nombre[i]+"</option>";
						}
					}
					html += "</select>";
					$("#divDirectorarea").html(html);					
				});
			});			
		});
	</script>

<?php
	function Muestra(){
		$I  = new Interfaz("CentroCosto:: Nueva CentroCosto",true);
?>
		<br><br>
		<form name="form1" method="post"/>
			<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
				<tr>
					<td align="right">&nbsp;</td>
					<td><strong>Nuevo Centro de Costos</strong></td>
			    </tr>
				<tr><td align="right" width="30%">Nombre<span class="Estilo1">*</span>:</td><td> <input type="text" name="nombre" id="nombre" size="70" /></td></tr>
				<tr><td align="right" width="30%">C&oacute;digo<span class="Estilo1">*</span>:</td><td> <input type="text" name="codigo" id="codigo" size="40"/></td></tr>
			    <tr>
					<td align="right" width="30%">Empresa :</td>
					<td><select name='empresa_id' id='empresa_id'>
							<option value="0">Seleccione...</option>
					<?php 
						$query = "SELECT e_id, e_codigo FROM empresas WHERE e_estatus = 1";
						$var = mysql_query($query);
						while($arr = mysql_fetch_assoc($var)){
							echo sprintf("<option value='%s'>%s</option>", $arr['e_codigo'], $arr['e_codigo']);
						}
					?>                    
					</select></td>
				</tr>
				<tr>
					<td align="right" width="30%">Estatus :</td>
					<td><select name='estatus'>                
						<option value=1>Activo</option>
						<option value=0>Inactivo</option>                                                                        
					</select></td>
				</tr>			
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
				<td colspan="2" align="center">
					<input type="submit" value="Registrar" id="registrar" name="registrar">
					<input type="submit" value="Cancelar" name="cancelar">				 	</td>
				</tr>
			</table>
		</form>			
		<?php			
		$I->Footer();
	}
?>