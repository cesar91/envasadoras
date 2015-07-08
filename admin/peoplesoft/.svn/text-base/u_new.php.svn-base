<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

	if(isset($_POST["registrar"])){
		Guardar();
	}else if(isset($_POST["cancelar"])){
		header("Location: ./index.php");
		exit();
	}else{		
		Muestra();
	}
	
	function Guardar(){

		if(isset($_POST['empresa_id']) && $_POST['empresa_id']!="" &&
			isset($_POST['nombre']) && $_POST['nombre']!="")
		{
			$u_empresa=$_POST['empresa_id'];
			$u_nombre=$_POST['nombre'];
			$u_usuario=$_POST['user'];
			$u_proveedor=$_POST['proveedor'];
			$u_producto=$_POST['producto'];
			$u_passwd=$_POST['passwd'];
			$u_email=$_POST['email'];
			$u_tipo=$_POST['tipoUser'];
        
            $telefono="";
            $notarjetacredito="";
            $departamento="";
			$idcentrocosto= $_POST['ceco_id'];
			$idnivel= $_POST['tipo'];
			$npuesto=$_POST['puesto'];
			$grupo_aprobador=$_POST['grupoAp'];
            
			//inserta en tabla usuarios
            $Usu= new Usuario();
            $iduser=0;
			$iduser=$Usu->add_Usuario($u_empresa,$u_nombre,$u_usuario,$u_proveedor,$u_producto,$u_passwd,$u_email,$u_tipo);
            			
            //inserta en tabla empleados
			$Usu->add_Empleado($u_empresa,$u_nombre,$u_usuario,$departamento,$iduser,$telefono,$notarjetacredito,$idcentrocosto,$idnivel,$npuesto,$grupo_aprobador);
			header("Location: index.php?oksave");
            
		}
		else {header("Location: index.php?error");}
	}
?>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/validateForm.js" type="text/javascript"></script>
<script language="javascript">

function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}
function validate(){
	
		if( validateForm("nombre","","El nombre es un campo requerido",1)){
			if(validateForm("user","","El usuario es un campo requerido",1)){
				if(validateForm("passwd","passwd2","El password es un campo requerido",2)){
					if(validateForm("email","","El mail es un campo requerido",3)){
							if(validateForm("ceco","","El ceco es un campo requerido",1)){
								if(validateForm("proveedor","","El proveedor es un campo requerido",1)){							
									if(validateForm("puesto","","El puesto es un campo requerido",1)){
											return true;
									}else return false;
								}else return false
							}else return false
					}else return false;
				}else return false;
			}else return false;
		}else return false;
}

</script>
<?php
	function Muestra(){
		$I  = new Interfaz("Usuarios:: Nuevo Usuario",true);
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
				   <td><strong>Informaci&oacute;n de acceso al sistema</strong></td>
		      </tr>
				 <tr>
			     <td align="right" width="30%"> Nombre Completo<span class="Estilo1">*</span>: 	</td><td> <input type="text" name="nombre" id="nombre" size="70" /> 										</td></tr>
				 <tr>
				 	<td align="right" width="30%">	Usuario<span class="Estilo1">*</span>: 			</td><td> <input type="text" name="user" id="user" size="40"/> 											
					</td>
				</tr>
				 <tr>
				 	<td align="right" width="30%"> Contrase&ntilde;a<span class="Estilo1">*</span>:					</td>
					<td> <input type="password" name="passwd"  id="passwd" size="40"/>					</td>
				</tr>
				<tr>
				 	<td align="right" width="30%"> Contrase&ntilde;a<span class="Estilo1">*</span>:					</td>
					<td> <input type="password" name="passwd2" id="passwd2" size="40" />					</td>
				</tr>
				 <tr>
				 	<td align="right" width="30%"> 
						E-mail<span class="Estilo1">*</span>:					
					</td>
					<td>
						<input type="text" name="email" id="email" size="40" />					
					</td>
				</tr>
				 <tr>
				 	<td align="right" width="30%">
						Tipo de usuario:
					</td>
					<td>
						<?php
						$Usu = new Usuario();
						?>
						<select id="tipoUser" name="tipoUser" >
							<?php
							foreach($Usu->Load_tipo_usuario() as $datos){
								if($datos['tu_id']==1 || $datos['tu_id']==2 || $datos['tu_id']==5){
							?>
								<option value="<?php echo $datos['tu_id']; ?>"><?php echo $datos['tu_nombre'];?></option>
							<?php
								}
							}
							?>
						</select>
					</td>
				</tr>
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td>&nbsp;</td>
				 </tr>
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Informaci&oacute;n del Empleado</strong></td>
		         </tr>
                 <tr>
				 	<td align="right" width="30%"> 
						Empresa<span class="Estilo1">*</span>:					</td>
					<td> 
					<?php
						$empresa=new Empresa();
					?>
					<select name="empresa_id" id="empresa_id">
						<?php	
						foreach($empresa->Load_all() as $arrE){
						?>
							<option name="empresa_id" id="empresa_id" value="<?php echo $arrE['e_id'];?>">
                                <?php echo $arrE['e_codigo'];?>
                            </option>
						<?php
						}
						?>
					</select>					
					</td>
				 </tr>       
                 <!--       
				 <tr>
				   <td align="right">Departamento<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="depto" id="depto" size="40" /></td>
                 </tr>
                 -->
				 <tr>
				   <td align="right">Departamento<span class="Estilo1">*</span>:</td>
                    <td> <select name='ceco_id'>
                        <?php 
                            $query=sprintf("SELECT cc_id, cc_centrocostos, cc_nombre FROM cat_cecos c WHERE cc_estatus = 0");
                            $var=mysql_query($query);
                            while($arr=mysql_fetch_assoc($var)){
                                echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
                            }
                        ?>                    
                    </select>
                    </td>                   
                 </tr>
				 <tr>
				   <td align="right">Proveedor (VENDOR_ID)<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="proveedor" id="proveedor" size="40" /></td>
				 </tr>
				 <tr>
				   <td align="right">Producto (PRODUCT)<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="producto" id="producto" size="40" /></td>
				 </tr>                
				 <tr>
				   <td align="right">Puesto<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="puesto" id="puesto" size="40" /></td>
                 </tr>
			  	 <tr>
				 	<td align="right" width="30%">
						Nivel de Usuario<span class="Estilo1">*</span>: 	
					</td>
					<td> 
						<?php echo llena_combo_vector("tipo",array(0=>"Auxiliar.",1=>"Jefe Dpto.",2=>"Gerente",4=>"Director")); ?>
				 	</td>
				</tr> 
                
                 <!--
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Informaci&oacute;n Adicional</strong></td>
                 </tr>
				 <tr>
                   <td align="right">Tarjeta de Cr&eacute;dito<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="amex2" id="amex2" size="40" /></td>
		         </tr>
				 <tr>
                   <td align="right">Tel&eacute;fono:</td>
				   <td><input type="text" name="tel2" id="tel2" size="40" onkeypress="return validaNum (event)" /></td>
		         </tr>
                 -->
			
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
