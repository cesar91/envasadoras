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
             isset($_POST['empresa_id']) && $_POST['empresa_id']!="")
		{
			$nombre=$_POST['nombre'];
			$codigo=$_POST['codigo'];
			$empresa_id=$_POST['empresa_id'];
			$estatus=$_POST['estatus'];
			$responsable=$_POST['responsable'];
			$directorarea=$_POST['directorarea'];
			//$grupo_id=$_POST['grupo_id'];
			
            $ceco->Nuevo_CentroCosto($nombre,$codigo,$empresa_id,$estatus,$responsable,$directorarea);
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
		var url = "/eexpensesv2/admin/centros/services/Ajax_cecos.php";
		var codigo = $("#codigo").val();
		var regresa = true;		
		$.ajaxSetup({async:false});
		$.post(url,{codigo:codigo},function(data){		
			if(data!=''){
				alert("El código ya ha sido asignado a otro Centro de Costos.");
				$("#codigo").focus();		
				regresa = false;		
			}
		});        
		return regresa;
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
				   <td><strong>Nuevo Centro de Costos</strong></td>
                 </tr>
				 <tr>
                    <td align="right" width="30%">Nombre<span class="Estilo1">*</span>:</td><td> <input type="text" name="nombre" id="nombre" size="70" /></td></tr>
				 <tr>
				 	<td align="right" width="30%">C&oacute;digo<span class="Estilo1">*</span>:</td><td> <input type="text" name="codigo" id="codigo" size="40"/></td>
				</tr>
				 <tr>
				 	<td align="right" width="30%">Empresa :</td>
                    <td> <select name='empresa_id'>
                        <?php 
                            $query=sprintf("SELECT e_id, e_codigo FROM empresas WHERE e_estatus = 0");
                            $var=mysql_query($query);
                            while($arr=mysql_fetch_assoc($var)){
                                echo sprintf("<option value='%s'>%s</option>", $arr['e_id'], $arr['e_codigo']);
                            }
                        ?>                    
                    </select>
                    </td>
				</tr>
				<tr>
                	<td align="right" width="30%">Estatus :</td>
                	<td> <select name='estatus'>                
                            <option value=0>Activo</option>
                            <option value=1>Inactivo</option>                                                                        
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
                            echo sprintf("<option value='%s'>%s</option>", $arr['idfwk_usuario'], $arr['nombre']);                            
                        }
                    ?>                    
                	</select>
                	</td>
            	</tr>
				
   				<!--  tr>
                <!-- td align="right" width="30%">Autorizadores<span class="Estilo1">*</span>:</td>
                <td> <select name='grupo_id'>
                    <?php                                                                     
                     /*   $query=sprintf("SELECT g_id, g_nombre FROM grupos");
                        $var=mysql_query($query);
                        while($arr=mysql_fetch_assoc($var)){                            
                            echo sprintf("<option value='%s'>%s</option>", $arr['g_id'], $arr['g_nombre']);                            
                        }*/
                    ?>                    
                </select>
                </td>
            	</tr-->
            	              
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
