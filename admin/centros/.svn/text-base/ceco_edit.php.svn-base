<?php
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
			
	if(isset($_POST["Actualizar"])){
		Actualizar();
	}else if(isset($_POST["Cancelar"])){
		header("Location: ./index.php");
	} else {
		Muestra();
	}
			
	function Actualizar(){
		$ceco= new CentroCosto();
		if(isset($_POST['ceco_id']) && $_POST['ceco_id']!="" &&
           isset($_POST['nombre']) && $_POST['nombre']!="" && 
		   isset($_POST['codigo']) && $_POST['codigo']!="" &&
		   isset($_POST['empresa_id']) && $_POST['empresa_id']!=""){
			$ceco_id = $_POST['ceco_id'];
			$nombre = $_POST['nombre'];
			$codigo = $_POST['codigo'];
			$empresa_id = $_POST['empresa_id'];
			$estatus = $_POST['estatus'];
			$responsable = $_POST['responsable'];
			$directorarea = $_POST['directorarea'];
				
			$ceco->Edita_CentroCosto($ceco_id,$nombre,$codigo,$empresa_id,$estatus,$responsable,$directorarea);
			header("Location: index.php?okupdate");
		}
		else {header("Location: index.php?error");}			
	}

	function Muestra(){
		$ceco= new CentroCosto();
		
		if(isset($_GET['ceco_id'])){
			$ceco_id=$_GET['ceco_id'];
			$ceco->Load_CeCo($ceco_id);
		}else{
			header("Location: index.php");
		}
		
		$I  = new Interfaz("Usuarios:: Editar Usuario",true);
	?>
		
	<link rel="stylesheet" href="../../lib/js/jquery-ui-1.10.4/development-bundle/themes/base/jquery.ui.all.css">
	<script src="../../lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
	<script language="javascript">
		$(document).ready(function(){
			$("#Actualizar").on("click",function(){
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
				 var id = <?PHP echo $_GET['ceco_id']?>;
				 var datos = "codigo="+codigo+"&id="+id;
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
		<br><br>
		<form name="form1" method="post" action=""/>
		<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
			<tr>
				<td align="right">&nbsp;</td>
				<td><strong>Editar Centro de Costos</strong></td>
			</tr>
			<tr>
				<td align="right">&nbsp;</td>
				<td><input type="hidden" name="ceco_id" id="ceco_id" value="<?php echo $ceco_id;?>" readonly="readonly" style="border-color:#FFFFFF" /></td>
			</tr>
			<tr>
				<td align="right" width="30%">Nombre<span class="Estilo1">*</span>: 	</td>
				<td> <input type="text" name="nombre" id="nombre" size="70" value="<?php echo $ceco->Get_dato("cc_nombre");?>"/></td>
			</tr>
			<tr>
				<td align="right" width="30%">C&oacute;digo<span class="Estilo1">*</span>: 	</td>
				<td> <input type="text" name="codigo" id="codigo" size="40" value="<?php echo $ceco->Get_dato("cc_centrocostos");?>"/></td>
			</tr>
			<?php $empresa = $ceco->Get_dato("cc_empresa_id"); ?>
			<tr>
				<td align="right" width="30%">Empresa :</td>
				<td><select name='empresa_id' id='empresa_id'>
					<?php 
						$cc_empresa_id = $ceco->Get_dato("cc_empresa_id");
					
						$query = "SELECT e_id, e_codigo FROM empresas WHERE e_estatus = 1";
						$var = mysql_query($query);                      
						$arr = mysql_fetch_assoc($var);
						
						if(count($arr)>1){
							do{                        
								if ($cc_empresa_id == $arr['e_id']) {
									echo sprintf("<option value='%s' selected='selected'>%s</option>", $arr['e_codigo'], $arr['e_codigo']);
								} else {
									echo sprintf("<option value='%s'>%s</option>", $arr['e_codigo'], $arr['e_codigo']);
								}
							}
							while($arr=mysql_fetch_assoc($var));
						}
						else{
							$query = "SELECT e_id, e_codigo FROM empresas WHERE e_id =".$cc_empresa_id;
							$var = mysql_query($query);                      
							$arr = mysql_fetch_assoc($var);																									
							echo "<option value='%s'>%s</option>", $cc_empresa_id, $arr['e_codigo'];
						}								
					?>                    
				</select></td>
			</tr>     
			<tr>
				<td align="right" width="30%">Estatus :</td>
				<td> <select name='estatus'>                
				<?php if ($ceco->Get_dato("cc_estatus")==1){?>
					<option selected value="1">Activo</option>
					<option value="0">Inactivo</option>
				<?php }else{?>
					<option value="1">Activo</option>
					<option selected value="0">Inactivo</option>
				<?php }?>                            		                                                                        
				</select></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" value="Actualizar" name="Actualizar" id="Actualizar">
					<input type="submit" value="Cancelar" name="Cancelar">				 	
				</td>
			</tr>
		</table>
		</form>
	<?php
		$I->Footer();
	}
?>