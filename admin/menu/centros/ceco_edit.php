<?php
//session_start();
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
              isset($_POST['empresa_id']) && $_POST['empresa_id']!="")            			
		{
			$ceco_id=$_POST['ceco_id'];
			$nombre=$_POST['nombre'];
			$codigo=$_POST['codigo'];
			$empresa_id=$_POST['empresa_id'];
			$estatus=$_POST['estatus'];
			$responsable=$_POST['responsable'];
			$directorarea=$_POST['directorarea'];
			//$grupo_id=$_POST['grupo_id'];            
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
		var url = "/eexpensesv2/admin/centros/services/Ajax_cecos.php";
		var codigo = $("#codigo").val();
		var regresa = true;		
		var id = <?PHP echo $_GET['ceco_id']?>;
		$.ajaxSetup({async:false});
		$.post(url,{codigo:codigo,id:id},function(data){						
			if(data!=''){
				alert("El código ya ha sido asignado a otro Centro de Costos.");
				$("#codigo").focus();		
				regresa = false;		
			}
		});        
		return regresa;
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
					<input type="hidden" name="ceco_id" id="ceco_id" value="<?php echo $ceco_id;?>" readonly="readonly" style="border-color:#FFFFFF" />
				</td>
		    </tr>
			<tr>
				<td align="right" width="30%">Nombre<span class="Estilo1">*</span>: 	</td>
                <td> <input type="text" name="nombre" id="nombre" size="70" value="<?php echo $ceco->Get_dato("cc_nombre");?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">C&oacute;digo<span class="Estilo1">*</span>: 			
				</td>
				<td> 
					<input type="text" name="codigo" id="codigo" size="40" value="<?php echo $ceco->Get_dato("cc_centrocostos");?>"/>
				</td>
			</tr>
            <tr>
                <td align="right" width="30%">Empresa :</td>
                <td> <select name='empresa_id'>
                    <?php 
                        $cc_empresa_id = $ceco->Get_dato("cc_empresa_id");
                    
                        $query=sprintf("SELECT e_id, e_codigo FROM empresas WHERE e_estatus = 0");
                        $var=mysql_query($query);                      
						$arr=mysql_fetch_assoc($var);
						
						if (count($arr)>1){
							do{                        
	                            if ($cc_empresa_id == $arr['e_id']) {
                                	echo sprintf("<option value='%s' selected='selected'>%s</option>", $arr['e_id'], $arr['e_codigo']);
                            	} else {
	                                echo sprintf("<option value='%s'>%s</option>", $arr['e_id'], $arr['e_codigo']);
                            	}
                        	}
                        	while($arr=mysql_fetch_assoc($var));
						}
						else{
							$query=sprintf("SELECT e_id, e_codigo FROM empresas WHERE e_id =".$cc_empresa_id);
							$var=mysql_query($query);                      
							$arr=mysql_fetch_assoc($var);																									
							echo sprintf("<option value='%s'>%s</option>", $cc_empresa_id, $arr['e_codigo']);
						}
							
                    ?>                    
                </select>
                </td>
            </tr>        
            
            <tr>
                	<td align="right" width="30%">Estatus :</td>
                	<td> <select name='estatus'>                
                		<?php if ($ceco->Get_dato("cc_estatus")==0){?>
                            		<option selected value=0>Activo</option>
                            		<option value=1>Inactivo</option>
                        <?php }else{?>
                        			<option value=0>Activo</option>
                            		<option selected value=1>Inactivo</option>
                           		
						<?php }?>                            		                                                                        
                	</select>
                	</td>
            	</tr>
            <tr>
                	<td align="right" width="30%">Responsable CC :</td>
                	<td> <select name='responsable'>
                    <?php                                                                     
                        $query=sprintf("SELECT idfwk_usuario, nombre FROM empleado");
                        $var=mysql_query($query);
                        while($arr=mysql_fetch_assoc($var)){
                        	if ($ceco->Get_dato("cc_responsable")==$arr['idfwk_usuario'])                            
                            	echo sprintf("<option value='%s' selected>%s</option>", $arr['idfwk_usuario'], $arr['nombre']);
                            else                             
                            	echo sprintf("<option value='%s'>%s</option>", $arr['idfwk_usuario'], $arr['nombre']);
                        }
                    ?>                    
                	</select>
                	</td>
            </tr>
            <tr>
                	<td align="right" width="30%">Director de Área :</td>
                	<td> <select name='directorarea'>
                    <?php                                                                     
                        $query=sprintf("SELECT idfwk_usuario, nombre FROM empleado INNER JOIN usuario_tipo ON empleado.idfwk_usuario=usuario_tipo.ut_usuario WHERE usuario_tipo.ut_tipo=1");
                        $var=mysql_query($query);
                        while($arr=mysql_fetch_assoc($var)){                        	
                        	if ($ceco->Get_dato("cc_director_de_area")==$arr['idfwk_usuario'])                            
                            	echo sprintf("<option value='%s' selected>%s</option>", $arr['idfwk_usuario'], $arr['nombre']);
                            else
                                echo sprintf("<option value='%s'>%s</option>", $arr['idfwk_usuario'], $arr['nombre']);                  
                        }
                    ?>                    
                	</select>
                	</td>
            </tr>
                      
                
			<tr>
				<td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" value="Actualizar" name="Actualizar" onclick="return validate(); ">
				 	<input type="submit" value="Cancelar" name="Cancelar">				 	
				</td>
			</tr>
			</table>
</form>
<?php
	$I->Footer();
}
?>
