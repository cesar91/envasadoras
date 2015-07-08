<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/functions/Empresa.php";

	if(isset($_POST["registrar"])){
		Guardar();
	}else if(isset($_POST["cancelar"])){
		header("Location: ./index.php");
		exit();
	}else{		
		Muestra();
	}
	
	function Guardar(){
		$empresa = new Empresa();
		if(isset($_POST['nombre']) && $_POST['nombre']!="" &&
			isset($_POST['codigo']) && $_POST['codigo']!="" &&
			isset($_POST['estatus']) && $_POST['estatus']!="")
		{
			$nombre=$_POST['nombre'];
			$codigo=$_POST['codigo'];
			$estatus=$_POST['estatus'];
            $empresa->Nueva_Empresa($nombre,$codigo,$estatus);
            header("Location: index.php?oksave");
		} else {
            header("Location: index.php?error");
        }
	}
?>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/validateForm.js" type="text/javascript"></script>
<script language="javascript">
	function validate(){	
		if($("#nombre").val() == ""){
			alert("El nombre es un campo requerido.");
			$("#nombre").focus();		
			return false;
		}         
		if($("#codigo").val() == ""){
			alert("El código es un campo requerido.");
			$("#codigo").focus();
			return false;
		}           
		var url = "services/Ajax_empresa.php";
		var codigo = $("#codigo").val();
		var regresa = true;				
		
		$.ajaxSetup({async:false});
		$.post(url,{codigo:codigo},function(data){			
			if(data!=''){
				alert("El código ya ha sido asignado a otra empresa.");
				$("#codigo").focus();		
				regresa = false;		
			}
		});        
		return regresa;
	}
</script>
<?php
	function Muestra(){
		$I  = new Interfaz("Empresa:: Nueva Empresa",true);
		?>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
		<br><br>
		<form name="form1" method="post">
			<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Nueva Empresa</strong></td>
                 </tr>
				 <tr>
                    <td align="right" width="30%">Nombre<span class="Estilo1">*</span>:</td><td> <input type="text" name="nombre" id="nombre" size="70" /></td>
                 </tr>
				 <tr>
				 	<td align="right" width="30%">C&oacute;digo<span class="Estilo1">*</span>:</td><td> <input type="text" name="codigo" id="codigo" size="40"/></td>
				</tr>
				<tr>
				 	<td align="right" width="30%">Estatus :</td><td> <select name="estatus" id="estatus">
				 		<option value="1">Activo</option>
				 		<option value="0">Inactivo</option>
				 	</select></td>
				</tr>
				 <tr><td colspan="2">&nbsp;</td></tr>
				 <tr>
				 	<td colspan="2" align="center">
				 		<input type="submit" value="Registrar" name="registrar" onclick="return validate(); ">
				 		<input type="submit" value="Cancelar" name="cancelar">				 	</td>
				 </tr>
			</table>
        </form>
		
		<?php
		
		$I->Footer();
	}
?>
