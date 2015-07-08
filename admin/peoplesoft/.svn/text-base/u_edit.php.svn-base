<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
	    
	if(isset($_POST["Actualizar"])){
		Actualizar();
	} else if(isset($_POST["Cancelar"])){
		header("Location: ./index.php");
        exit();
	} else {
		Muestra();
	}	
		
	function Actualizar(){
        $Usu= new Usuario();
		$iduser=0;
		if(isset($_POST['empresa_id']) && $_POST['empresa_id']!="" &&
			isset($_POST['nombre']) && $_POST['nombre']!="")
		{
            $usuario_id=$_POST['usuario_id'];
            $empleado_id=$_POST['empleado_id'];
            
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
            
			// Actualiza en tabla usuarios
			$Usu->update_Usuario($u_empresa,$u_nombre,$u_usuario,$u_proveedor,$u_producto,$u_passwd,$u_email,$u_tipo,$usuario_id);
			
            // Actualiza en tabla empleados
			$Usu->update_Empleado($u_empresa,$u_nombre,$u_usuario,$departamento,$telefono,$notarjetacredito,$idcentrocosto,$idnivel,$npuesto,$grupo_aprobador,$empleado_id);
			header("Location: index.php");
		} else {
            header("Location: index.php?error");
        }
	}

	function Muestra(){
        
		$Usu= new Usuario();
		if(isset($_GET['usuario_id'])){
			$usuario_id=$_GET['usuario_id'];    
            $Usu->Load_Usuario_By_ID($usuario_id);
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

<br><br>
	<form name="form1" method="post" action=""/>
		<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
			<tr>
				<td align="right">&nbsp;</td>
				<td><strong>Informaci&oacute;n de acceso al sistema</strong></td>
		    </tr>
			<tr>
				<td align="right">&nbsp;</td>
				<td>
					<input type="hidden" name="usuario_id" id="usuario_id" value="<?php echo $Usu->Get_dato("u_id");?>" readonly="readonly" style="border-color:#FFFFFF" />
					<input type="hidden" name="empleado_id"  id="empleado_id" value="<?php echo $Usu->Get_dato("idempleado");?>" readonly="readonly"  style="border-color:#FFFFFF"/>
					
				</td>
		    </tr>
			<tr>
				<td align="right" width="30%">Nombre Completo<span class="Estilo1">*</span>: 	</td><td> <input type="text" name="nombre" id="nombre" size="70" value="<?php echo $Usu->Get_dato("u_nombre");?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Usuario<span class="Estilo1">*</span>: 			
				</td>
				<td> 
					<input type="text" name="user" id="user" size="40" value="<?php echo $Usu->Get_dato("u_usuario");?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Contrase&ntilde;a<span class="Estilo1">*</span>:					
				</td>
				<td> 
					<input type="password" name="passwd"  id="passwd" size="40" value="<?php echo $Usu->Get_dato("u_passwd");?>"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Contrase&ntilde;a<span class="Estilo1">*</span>:					
				</td>
				<td> 
					<input type="password" name="passwd2" id="passwd2" size="40" value="<?php echo $Usu->Get_dato("u_passwd")?>" />
				</td>
			</tr>
			<tr>
				<td align="right" width="30%"> 
					E-mail<span class="Estilo1">*</span>:					
				</td>
				<td>
					<input type="text" name="email" id="email" size="40" value="<?php echo $Usu->Get_dato("u_email")?>" />
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">
					Tipo de usuario:
				</td>
				<td>
					<?php
					//$Usu = new Usuario();
					?>
					<select id="tipoUser" name="tipoUser" >
						<?php
							foreach($Usu->Load_tipo_usuario() as $datos){
								if($datos['tu_id']==1 || $datos['tu_id']==2 || $datos['tu_id']==5){
						?>
									<option value="<?php echo $datos['tu_id']; ?>" 
										<?php 
											if( $Usu->Get_dato("u_tipo")==$datos['tu_id'])
											echo "selected";
										?>
									>
										<?php echo $datos['tu_nombre'];?>
									</option>
						<?php
								}
							}
						?>
					</select>
				</td>
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
					$arr=$empresa->Load_all();
				?>
					<select name="empresa_id" id="empresa_id">
						<?php	
						foreach($arr as $arrE){
						?>
							<option name="<?php echo $arrE['e_codigo'];?>" id="<?php echo $arrE['e_codigo'];?>" 
								value="<?php echo $arrE['e_id'];?>"
								<?php
									if($Usu->Get_dato("u_empresa")==$arrE['e_id'])
										echo "selected";
								?>
							>
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
				<td><input type="text" name="depto" id="depto" size="40" value="<?php echo $Usu->Get_dato("departamento");?>"/></td>
		    </tr>
            -->
             <tr>
               <td align="right">Departamento<span class="Estilo1">*</span>:</td>
                <td> <select name='ceco_id'>
                    <?php 
                        $query=sprintf("SELECT cc_id, cc_centrocostos, cc_nombre FROM cat_cecos c WHERE cc_estatus = 0");
                        $var=mysql_query($query);
                        while($arr=mysql_fetch_assoc($var)){
                            if ($Usu->Get_dato("idcentrocosto") == $arr['cc_id']) {
                                echo sprintf("<option value='%s' selected='selected'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);    
                            } else {
                                echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);    
                            }                            
                        }
                    ?>                    
                </select>
                </td>                   
             </tr>            
			<tr>
				<td align="right">Proveedor (VENDOR_ID)<span class="Estilo1">*</span>:</td>
				<td><input type="text" name="proveedor" id="proveedor" size="40" value="<?php echo $Usu->Get_dato("u_proveedor");?>" /></td>
			</tr>
			<tr>
				<td align="right">Producto (PRODUCT)<span class="Estilo1">*</span>:</td>
				<td><input type="text" name="producto" id="producto" size="40" value="<?php echo $Usu->Get_dato("u_producto");?>" /></td>
			</tr>
			<tr>
				<td align="right">Puesto<span class="Estilo1">*</span>:</td>
				<td><input type="text" name="puesto" id="puesto" size="40" value="<?php echo $Usu->Get_dato("npuesto");?>"/></td>
		    </tr>
			<tr>
				<td align="right" width="30%">
					Nivel de Usuario<span class="Estilo1">*</span>: 	
				</td>
				<td>  
					<?php echo llena_combo_vector("tipo",array(0=>"Auxiliar.",1=>"Jefe Dpto.",2=>"Gerente",4=>"Director"),$Usu->Get_dato("idnivel")); ?>
				</td>
				</tr>

            <!--
			<tr>
				<td align="right">&nbsp;</td>
				<td><strong>Informaci&oacute;n Adicional</strong></td>
		    </tr>
			<tr>
            	<td align="right">Tarjeta de Cr&eacute;dito<span class="Estilo1">*</span>:
				</td>
			  	<td>
					<input type="text" name="amex2" id="amex2" size="40" value="<?php echo $Usu->Get_dato("notarjetacredito");?>" />
				</td>
		    </tr>
			<tr>
            	<td align="right">Tel&eacute;fono:</td>
				<td><input type="text" name="tel2" id="tel2" size="40" onkeypress="return validaNum (event)" value="<?php echo $Usu->Get_dato("telefono");?>" /></td>
		    </tr>
            -->
            
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
