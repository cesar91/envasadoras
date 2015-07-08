<?php
//session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
	    
	if(isset($_POST["volver"])){
		header("Location: ./index.php");
	} else {
        Muestra();
    }
	
	function Muestra(){
		$empresa= new Empresa();
		
		if(isset($_GET['empresa_id'])){
			$empresa_id=$_GET['empresa_id'];
			$empresa->Load_EmpresaporID($empresa_id);
		} else {
			header("Location: index.php");
		}
	
		$I  = new Interfaz("Usuarios:: Editar Usuario",true);
		?>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
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
		var id = <?PHP echo $_GET['empresa_id']?>;
		$.ajaxSetup({async:false});
		$.post(url,{codigo:codigo,id:id},function(data){						
			if(data!=''){
				alert("El código ya ha sido asignado a otra empresa.");
				$("#codigo").focus();		
				regresa = false;		
			}
		});        
		return regresa;
	}
</script>

<br><br>
	<form name="form1" method="post" action="">
		<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
			<tr>
				<td align="right">&nbsp;</td>
				<td><strong>Editar Empresa</strong></td>
		    </tr>
			<tr>
				<td align="right">&nbsp;</td>
				<td>
					<input type="hidden" name="empresa_id" id="empresa_id" value="<?php echo $empresa_id;?>" readonly="readonly" style="border-color:#FFFFFF" />
				</td>
		    </tr>
			<tr>
				<td align="right" width="30%">Nombre<span class="Estilo1">*</span>: 	</td>
                <td> <input type="text" name="nombre" id="nombre" size="70" value="<?php echo $empresa->Get_dato("e_nombre");?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">C&oacute;digo<span class="Estilo1">*</span>: 			
				</td>
				<td> 
					<input type="text" name="codigo" id="codigo" size="40" value="<?php echo $empresa->Get_dato("e_codigo");?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Estatus :</td><td> 
					<select name="estatus" id="estatus">
						<?php if ($empresa->Get_dato("e_estatus")==1){ ?>
									<option selected value="1">Activo</option>
									<option value="0">Inactivo</option>
							  <?php }else{?>
							  		<option value="1">Activo</option>
									<option selected value="0">Inactivo</option>
							  <?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2" align="center">
				 	<input type="submit" value="Volver" name="volver">				 	
				</td>
			</tr>
			</table>
</form>
<?php
	$I->Footer();
}
?>
