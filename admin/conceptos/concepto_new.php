<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/functions/Concepto.php";

	if(isset($_POST["registrar"])){
		Guardar();
	}else if(isset($_POST["cancelar"])){
		header("Location: ./index.php");
		exit();
	}else{		
		Muestra();
	}
	
	function Guardar(){
		$concepto = new Concepto();
		if(isset($_POST['catalogo']) && $_POST['catalogo']!="" &&
            isset($_POST['clasificacion']) && $_POST['clasificacion']!="" &&
              isset($_POST['concepto']) && $_POST['concepto']!="" &&
                isset($_POST['cuenta']) && $_POST['cuenta']!="")
		{
			$dc_catalogo=$_POST['catalogo'];
			$cp_clasificacion=$_POST['clasificacion'];
			$cp_concepto=$_POST['concepto'];
			$cp_cuenta=$_POST['cuenta'];
			$cp_estatus=$_POST['estatus'];
            $concepto->Nuevo_Concepto($dc_catalogo, $cp_clasificacion, $cp_concepto, $cp_cuenta, $cp_estatus);
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
		if($("#concepto").val() == ""){
			alert("El concepto es un campo requerido.");
			$("#concepto").focus();		
			return false;
		}         
		if($("#cuenta").val() == ""){
			alert("El cuenta es un campo requerido.");
			$("#cuenta").focus();
			return false;
		}           
		var url = "services/Ajax_conceptos.php";
		var codigo = $("#cuenta").val();
		var regresa = true;		
		$.ajaxSetup({async:false});
		$.post(url,{codigo:codigo},function(data){						
			if(data!=''){
				alert("La cuenta ya ha sido asignado a otro Concepto.");
				$("#cuenta").focus();		
				regresa = false;		
			}
		});        
		return regresa;
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
		$I  = new Interfaz("Concepto:: Nuevo Concepto",true);
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
				   <td><strong>Nuevo Concepto</strong></td>
                 </tr>
				 <tr>
                    <td align="right" width="30%" > <input type="hidden" name="catalogo" id="catalogo" size="70" value="1"/></td>
                </tr>
				 <tr>
                    <td align="right" width="30%">Concepto<span class="Estilo1">*</span>:</td><td> <input type="text" name="concepto" id="concepto" size="70" /></td>
                </tr>
				 <tr>
                    <td align="right" width="30%">Cuenta<span class="Estilo1">*</span>:</td><td> <input type="text" name="cuenta" id="cuenta" size="70" onkeypress="return validaNum(event);"/></td>
                </tr>                
				 <tr>
				 	<td align="right" width="30%">Clasificaci&oacute;n :</td>
                        <td>
                            <select name='clasificacion'>
                                <option value='1'>DEDUCIBLE</option>
                                <option value='0'>NO DEDUCIBLE</option>
                            </select>
                        </td>
				</tr>
				 <tr>
				 	<td align="right" width="30%">Estatus :</td>
                        <td>
                            <select name='estatus'>
                            	<option value='1'>ACTIVO</option>
                                <option value='0'>INACTIVO</option>                                
                            </select>
                        </td>
				</tr>
				 <tr><td colspan="2">&nbsp;</td></tr>
				 <tr>
				 	<td colspan="2" align="center">
				 		<input type="submit" value="Registrar" name="registrar" onclick="return validate(); ">
				 		<input type="submit" value="Cancelar" name="cancelar"></td>
				 </tr>
			</table>
        </form>
		
		<?php
		
		$I->Footer();
	}
?>
