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
		$modeloAuto = new ModeloAuto();
		if(isset($_POST['nombre']) && $_POST['nombre']!="" &&
			isset($_POST['factor']) && $_POST['factor']!="" &&
             isset($_POST['estatus']) && $_POST['estatus']!="")
		{
			$nombre=$_POST['nombre'];
			$factor=$_POST['factor'];
			$estatus=$_POST['estatus'];
			$modeloAuto->add_ModeloAuto($nombre,$factor,$estatus);
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
	
    if(!validateForm("nombre","","El nombre es un campo requerido",1)){
        return false;
    }          
    if( !validateForm("factor","","El factor es un campo requerido",1)){
        return false;
    }
	if( !validateForm("estatus","","El estatus es un campo requerido",1)){
        return false;		
    }                   
    return true;
}

function confirma(){
	if(!confirm("¿Seguro que desea cancelar el registro?")){
		return false;
	}else{
		location.href="index.php?";
	}
	
}
//validación campos numericos
function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}
</script>
<?php
	function Muestra(){
		$I  = new Interfaz("CentroCosto:: Nueva CentroCosto",true);
		?>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
		<br><br>
		<form name="form1" method="post"/>
			<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
            
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Nuevo Modelo de Auto</strong></td>
                 </tr>
				 <tr>
                    <td align="right" width="30%">Nombre<span class="Estilo1">*</span>:</td><td> <input type="text" name="nombre" id="nombre" size="70" /></td></tr>
				 <tr>
				 	<td align="right" width="30%">Factor<span class="Estilo1">*</span>:</td><td> <input type="text" onkeypress="return validaNum(event);" name="factor" id="factor" size="40"/></td>
				</tr>
				 <tr>
				 	<td align="right" width="30%">Estatus :</td>
                    <td> <select name='estatus'>
                        <option value="1">Activo</option>
						<option value="0">Inactivo</option>
                    </select>
                    </td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
				 	<td colspan="2" align="center">
				 		<input type="submit" value="Registrar" name="registrar" onclick="return validate(); ">
				 		<input type="button" value="Cancelar" name="cancelar" onclick="confirma();">				 	</td>
				</tr>
				 
			</table>
        </form>
		
		<?php
		
		$I->Footer();
	}
?>
