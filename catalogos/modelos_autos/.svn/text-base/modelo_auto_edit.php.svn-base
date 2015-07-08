<?php
//session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
	    
	if(isset($_POST["volver"])){
		header("Location: ./index.php");
	}else{
        Muestra();
    }
	
	function Muestra(){
		$modeloAuto= new ModeloAuto();
		
		if(isset($_GET['ma_id'])){
			$modeloAuto_id = $_GET['ma_id'];
			$modeloAuto->Load_modeloAuto($ma_id);
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
	
    if(!validateForm("nombre","","El nombre es un campo requerido",1)){
        return false;
    }          
    if(!validateForm("factor","","El factor es un campo requerido",1)){
        return false;
    }
	if(!validateForm("estatus","","El estatus es un campo requerido",1)){
        return false;
    }                   
    return true;
}

function confirma(){
	if(!confirm("¿Seguro que desea cancelar la actualización de los datos?")){
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

<br><br>
	<form name="form1" method="post" action=""/>
		<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
			<tr>
				<td align="right">&nbsp;</td>
				<td><strong>Editar Centro de Costos</strong></td>
		    </tr>
			<tr>
				<td align="right">&nbsp;</td>
				<td>
					<input type="hidden" name="ma_id" id="ma_id" value="<?php echo $modeloAuto_id;?>" readonly="readonly" style="border-color:#FFFFFF" />
				</td>
		    </tr>
			<tr>
				<td align="right" width="30%">Nombre<span class="Estilo1">*</span>: 	</td>
                <td> <input type="text" name="nombre" id="nombre" size="70" value="<?php echo $modeloAuto->Get_Nombre($modeloAuto_id);?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Factor<span class="Estilo1">*</span>: 			
				</td>
				<td> 
					<input type="text" name="factor" id="factor" size="40" onkeypress="return validaNum(event);" value="<?php echo $modeloAuto->Get_Factor($modeloAuto_id);?>"/>
				</td>
			</tr>
             <tr>
                <td align="right" width="30%">Estatus :</td>
                <td> <select name='estatus'>
				 <?php 
                        $query=sprintf("SELECT ma_estatus, IF(ma_estatus=1,'Activo','Inactivo') as est FROM modelo_auto WHERE ma_id = ".$modeloAuto_id);
                        $var=mysql_query($query);
                        while($arr=mysql_fetch_assoc($var)){
							$selected = "selected";
							if ($arr['ma_estatus'] == 1){
								echo '<option value="1" '.$selected.'>Activo</option>';	
								echo '<option value="0">Inactivo</option>';   
							}else{
								echo '<option value="1">Activo</option>';	
								echo '<option value="0" '.$selected.'>Inactivo</option>';   															  
							}													                                                        
                        }						
						
						   
                ?>                          
                </select>
                </td>
            </tr>        
			<tr>
				<td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2" align="center">
				 	<input type="button" value="Volver" name="volver" onclick="confirma();">				 	
				</td>
			</tr>
			</table>
</form>
<?php
	$I->Footer();
}
?>
