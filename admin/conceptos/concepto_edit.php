<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
	    
error_log(print_r($_POST, True));
        
	if(isset($_POST["Actualizar"])){
		Actualizar();
	}else if(isset($_POST["Cancelar"])){
		header("Location: ./index.php");
	} else {
        Muestra();
    }
		
	function Actualizar(){
		$concepto = new Concepto();
		if(isset($_POST['catalogo']) && $_POST['catalogo']!="" &&
            isset($_POST['clasificacion']) && $_POST['clasificacion']!="" &&
              isset($_POST['concepto']) && $_POST['concepto']!="" &&
                isset($_POST['cuenta']) && $_POST['cuenta']!="" &&
			      isset($_POST['dc_id']) && $_POST['dc_id']!="")
		{
            $dc_id=$_POST['dc_id'];
			$dc_catalogo=$_POST['catalogo'];
			$cp_clasificacion=$_POST['clasificacion'];
			$cp_concepto=$_POST['concepto'];
			$cp_cuenta=$_POST['cuenta'];        
            $cp_estatus=$_POST['estatus'];
                                      
            $concepto->Edita_Concepto($dc_id, $cp_clasificacion, $cp_concepto, $cp_cuenta, $cp_estatus);
            header("Location: index.php?oksave");
		} else {
            header("Location: index.php?error");
        }
		
	}

	function Muestra(){
		$concepto= new Concepto();
		if(isset($_GET['concepto_id'])){
			$concepto_id=$_GET['concepto_id'];
			$concepto->Load_Concepto($concepto_id);
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
		var id = <?PHP echo $_GET['concepto_id']?>;
		$.ajaxSetup({async:false});
		$.post(url,{codigo:codigo,id:id},function(data){						
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

<br><br>
		<form name="form1" method="post"/>
			<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Editar Concepto</strong></td>
                 </tr>
                <tr>
                    <td align="right">&nbsp;</td>
                    <td>
                        <input type="hidden" name="dc_id" id="dc_id" value="<?php echo $_GET['concepto_id']; ?>" readonly="readonly" style="border-color:#FFFFFF" />
                    </td>
                </tr>                 
				 <tr>
                    <td align="right" width="30%"><input type="hidden" name="catalogo" id="catalogo" size="70" value="0"/></td>
                </tr>
				 <tr>
                    <td align="right" width="30%">Concepto<span class="Estilo1">*</span>:</td><td> <input type="text" name="concepto" id="concepto" size="70" value="<?php echo $concepto->Get_dato("cp_concepto");?>"/></td>
                </tr>
				 <tr>
                    <td align="right" width="30%">Cuenta<span class="Estilo1">*</span>:</td><td> <input type="text" name="cuenta" id="cuenta" size="70" onkeypress="return validaNum(event);" value="<?php echo $concepto->Get_dato("cp_cuenta");?>"/></td>
                </tr>                
				 <tr>
				 	<td align="right" width="30%">Clasificaci&oacute;n :</td>
                        <td>
                            <select name='clasificacion'>
                                <?php if($concepto->Get_dato("cp_deducible")==1) { ?>
                                    <option selected='selected' value='1'>DEDUCIBLE</option>
                                    <option value='0'>NO DEDUCIBLE</option>                                
                                <?php } else { ?>
                                    <option value='1'>DEDUCIBLE</option>
                                    <option selected='selected' value='0'>NO DEDUCIBLE</option>                                   
                                <?php } ?>
                            </select>
                        </td>
				</tr>
				<tr>
				 	<td align="right" width="30%">Estatus :</td>
                        <td>
                            <select name='estatus'>
                            	<?php if ($concepto->Get_dato("cp_activo")==1){?>
                            		<option selected value=1>ACTIVO</option>
                            		<option value=0>INACTIVO</option>
                        <?php }else{?>
                        			<option value=1>ACTIVO</option>
                            		<option selected value=0>INACTIVO</option>
                           		
						<?php }?>            
                                
                            </select>
                        </td>
				</tr>
                                 
                
				 <tr><td colspan="2">&nbsp;</td></tr>
				 <tr>
				 	<td colspan="2" align="center">
				 		<input type="submit" value="Actualizar" name="Actualizar" onclick="return validate(); ">
				 		<input type="submit" value="Cancelar" name="Cancelar">				 	</td>
				 		<input type="hidden" value="name_concepto" id="name_concepto" value=""/>				 	
				 </tr>
			</table>
        </form>
		
		<?php
		
		$I->Footer();
	}
?>