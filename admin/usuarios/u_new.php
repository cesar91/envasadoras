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
			$u_nombre=$_POST['nombre'];            
			$u_apaterno=$_POST['apaterno'];   
			$u_amaterno=$_POST['amaterno'];   
			$u_nombre_completo=$u_nombre." ".$u_apaterno."  ".$u_amaterno;
			$u_usuario=$_POST['user'];
			$u_passwd=$_POST['passwd'];
			$u_email=$_POST['email'];			
			$u_empresa=$_POST['empresa_id'];
			$u_centrocosto=$_POST['ceco_id'];            
			$u_proveedor=$_POST['proveedor'];
			$u_producto=$_POST['producto'];
			$u_puesto=$_POST['puesto'];
			$u_nivel=$_POST['tipo'];
            $u_tarjeta=$_POST['amex2'];
            $u_tarjeta_gas=$_POST['amex2gas'];
            $u_telefono="";			
            $u_departamento="";
			$u_estatus=$_POST['estatus'];
			$u_cuentaporcobrar=$_POST['cuentaporcobrar'];
			if (isset($_POST['dirgeneral'])){
				$u_directorgeneral=1;
			}
			else{
				$u_directorgeneral=0;
			}			
			//inserta en tabla usuarios
            $Usu = new Usuario();
			$iduser=$Usu->add_Usuario($u_empresa,$u_nombre,$u_apaterno,$u_amaterno,$u_usuario,$u_proveedor,$u_passwd,$u_email,$u_estatus,$u_cuentaporcobrar);
            			
            //inserta en tabla empleados
			$Usu->add_Empleado($u_empresa,$u_nombre_completo,$u_usuario,$iduser,$u_telefono,$u_tarjeta,$u_tarjeta_gas,$u_centrocosto,$u_puesto,$u_directorgeneral,$u_estatus);

			//inserta en la tabla usuario_tipo
			foreach ($_POST['seleccion'] as $id){
				$Usu->add_Usuario_Tipo($iduser,$id);
				if ($id==4 || $id==5 || $id==6){
					$Usu->add_Homog_dueno($iduser,$id);
				}
   				//echo $id."<br>"; 
			}  

			header("Location: index.php?oksave");
			//echo "<br/>oksave";
            
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
	var validaTipos=1;	
	if (!document.getElementById("sel0").checked && !document.getElementById("sel1").checked && !document.getElementById("sel2").checked && !document.getElementById("sel3").checked && !document.getElementById("sel4").checked && !document.getElementById("sel5").checked && !document.getElementById("sel6").checked && !document.getElementById("sel7").checked){		
		validaTipos=0;	
	}	
			
	if( validateForm("nombre","","El nombre es un campo requerido",1)){
		if( validateForm("apaterno","","El apellido paterno es un campo requerido",1)){
			if(validateForm(document.getElementById("amex2").value,"","El número de tarjeta de crédito es inválido",4)){
				if(validateForm(document.getElementById("amex2gas").value,"","El número de tarjeta de crédito de gasolina es inválido",5)){
					if(validateForm("user","","El usuario es un campo requerido",1)){
						if(validateForm("passwd","passwd2","La contraseña es un campo requerido",2)){
							if(validateForm("email","","El mail es un campo requerido",3)){
								if (validaTipos==1){
									if(validateForm("ceco","","El ceco es un campo requerido",1)){
										if(validateForm("proveedor","","El proveedor es un campo requerido",1)){							
											if(validateForm("puesto","","El puesto es un campo requerido",1)){
												if(validatorForm()){
													return true;
												}else return false;
											}else return false;
										}else return false
									}else return false
								}else {alert("Favor de seleccionar por lo menos un tipo de usuario"); return false;}
							}else return false;
						}else return false;
					}else return false;
				}else return false;
			}else return false;
		}else return false;
	}else return false;
}
function validatorForm(){
		if ($("#email").val().indexOf('@', 1) == -1 || $("#email").val().indexOf('.',$("#email").val().indexOf('@', 0)) == -1) {			
			alert("Dirección de email inválida");
			return false;
		}
		else if (!(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test($("#email").val()))){
			alert("Dirección de email inválida");
			return false;
		}
		else if($("#passwd").val() != $("#passwd2").val()){
			alert("La contraseña no coincide, por favor intente nuevamente");
			return false;				
		}else
			return true;
}
function bloqueaEspacio(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==32)
		return false;
	else
		return true;
}
//validación campos numericos
function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}

function mostrarImagen(){
	//$("#imagen").show();
	$("#imagen").slideDown(1000);
	$("#imagen2").slideUp(1000);
}
function ocultarImagen(){	
	$("#imagen").slideUp(1000);
	$("#imagen2").slideDown(1000);
	//$("#imagen").hide();
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
			     <td align="right" width="30%"> Nombre<span class="Estilo1">*</span>: 	</td><td> <input type="text" name="nombre" id="nombre" size="40" /> 										</td></tr>
				 <tr>
			     <td align="right" width="30%"> Apellido Paterno<span class="Estilo1">*</span>: 	</td><td> <input type="text" name="apaterno" id="apaterno" size="40" /> 										</td></tr>
				 <tr>
			     <td align="right" width="30%"> Apellido Materno : 	</td><td> <input type="text" name="amaterno" id="ampaterno" size="40" /> 										</td></tr>
				 <tr>
				 	<td align="right" width="30%">	Usuario<span class="Estilo1">*</span>: 			</td><td> <input type="text" name="user" id="user" size="40" onKeypress="return bloqueaEspacio(event);"/> 											
					</td>
				</tr>
				 <tr>
				 	<td align="right" width="30%"> Contrase&ntilde;a<span class="Estilo1">*</span>:					</td>
					<td> <input type="password" name="passwd"  id="passwd" size="40" onKeypress="return bloqueaEspacio(event);"/>					</td>
				</tr>
				<tr>
				 	<td align="right" width="30%">Confirma contrase&ntilde;a<span class="Estilo1">*</span>:					</td>
					<td> <input type="password" name="passwd2" id="passwd2" size="40" onKeypress="return bloqueaEspacio(event);"/>					</td>
				</tr>
				 <tr>
				 	<td align="right" width="30%"> 
						E-mail<span class="Estilo1">*</span>:					
					</td>
					<td>
						<input type="text" name="email" id="email" size="40" onKeypress="return bloqueaEspacio(event);"/>					
					</td>
				</tr>				
				</table>
				<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
				<tr align="center">
						<td width=500>
							<strong>Tipos de usuario:</strong>
						</td>
				</tr>
				</table>
				<table width="80%" align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">				
						<tr><td colspan="6" align="center">
							<div id="imagen2">							
							<a onclick="mostrarImagen();" style='cursor:pointer'><h1>Mostrar tabla de permisos</h1></a>
							</div>
							<div id="imagen" style="display:none;">
							<a onclick="ocultarImagen();" style='cursor:pointer'><h1>Ocultar tabla de permisos</h1></a>
							<img src="../../images/admin/Tabla de roles permitidos 3.jpg" width="800" align="center">
							<div>
						</td></tr>		
						<?php
						$Usu = new Usuario();
						?>						
						<?php
							$arrTiposUsuario=array();
							$indicTiposUsuario=0;							
							foreach($Usu->Load_tipo_usuario() as $datos){
								$arrTiposUsuario[$indicTiposUsuario][0]=$datos['tu_id'];
								$arrTiposUsuario[$indicTiposUsuario][1]=$datos['tu_nombre'];
								$indicTiposUsuario++;
							}
							$divisiones=$indicTiposUsuario/2;
							?>
							<?php for ($indicTiposUsuario2=0;$indicTiposUsuario2<$divisiones;$indicTiposUsuario2++){?>
								<tr><div style="position:relative">
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="left"><input type="checkbox" <?php echo "id='sel".$indicTiposUsuario2."'"; ?>  name="seleccion[]" value="<?php echo $arrTiposUsuario[$indicTiposUsuario2][0];?>"><?php echo $arrTiposUsuario[$indicTiposUsuario2][1];?></input></td>
									<td align="left"><input type="checkbox" <?php echo "id='sel".($indicTiposUsuario2+$divisiones)."'"; ?>  name="seleccion[]" value="<?php echo $arrTiposUsuario[$divisiones+$indicTiposUsuario2][0];?>"><?php echo $arrTiposUsuario[$divisiones+$indicTiposUsuario2][1];?></input></td>
									</div>
								</tr>
							<?php								
							}
							?>						
				
				</table>
				<table width="80%"  align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td>&nbsp;</td>
				 </tr>
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Informaci&oacute;n del Empleado</strong></td>
		         </tr>
		         <?php 		         
		         $idDirGral=$Usu->Load_dir_general();
		         if ($idDirGral==0){?>
			         <tr>
			         	<td align="right" width="30%">
			         		Director General:
			         	</td>
			         	<td>
			         		<input type="checkbox" id="dirgeneral" name="dirgeneral"/>
			         	</td>
			         </tr>
			     <?php } ?>
                 <tr>
				 	<td align="right" width="30%"> 
						Empresa:					</td>
					<td> 
					<?php
						$empresa=new Empresa();
					?>
					<select name="empresa_id" id="empresa_id">
						<?php	
						foreach($empresa->Load_all_activas() as $arrE){
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
				   <td align="right">Centro de Costos :</td>
                    <td> <select name='ceco_id'>
                        <?php 
                            $query=sprintf("SELECT cc_id, cc_centrocostos, cc_nombre FROM cat_cecos c WHERE cc_estatus = 1");
                            $var=mysql_query($query);
                            while($arr=mysql_fetch_assoc($var)){
                                echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
                            }
                        ?>                    
                    </select>
                    </td>                   
                 </tr>				
				 <tr>
				   <td align="right">Proveedor<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="proveedor" id="proveedor" size="40" /></td>
				 </tr>
				 <tr>				   
				   <input type="hidden" name="producto" id="producto" size="40" />
				 </tr>                
				 <tr>
				   <td align="right">Puesto<span class="Estilo1">*</span>:</td>
				   <td><input type="text" name="puesto" id="puesto" size="40" /></td>
                 </tr>
			  	                 
				 <tr>
                   <td align="right">Tarjeta de Cr&eacute;dito:</td>
				   <td><input type="text" name="amex2" id="amex2" size="16" onkeypress="return validaNum(event);"/></td>
		         </tr>
				 <tr>
                   <td align="right">Tarjeta de Cr&eacute;dito Gasolina:</td>
				   <td><input type="text" name="amex2gas" id="amex2gas" size="16" onkeypress="return validaNum(event);"/></td>
		         </tr>
		         <tr>
				 	<td align="right" width="30%">Estatus:</td>
                        <td>
                            <select name='estatus'>
                            	<option value='1'>ACTIVO</option>
                                <option value='0'>INACTIVO</option>
                                
                            </select>
                        </td>
				</tr>
				<tr>
				   <td align="right">Cuenta por cobrar:</td>
				   <td><input type="text" name="cuentaporcobrar" id="cuentaporcobrar" size="40"  onkeypress="return validaNum(event);"/></td>
				 </tr>
				
				 <tr>                
                
                 <!--
				 <tr>
				   <td align="right">&nbsp;</td>
				   <td><strong>Informaci&oacute;n Adicional</strong></td>
                 </tr>
                   <td align="right">Tel&eacute;fono:</td>
				   <td><input type="text" name="tel2" id="tel2" size="40" onkeypress="return validaNum (event)" /></td>
		         </tr>
                 -->
			
				 <tr><td colspan="2">&nbsp;</td></tr>
				 <tr>
				 	<td colspan="2" align="center">
				 		<input type="submit" value="Registrar" name="registrar" onclick="return validate(); ">
				 		<input type="submit" value="Cancelar" name="cancelar">				 	
				 	</td>
				 </tr>
			</table>
		</form>
		
		<?php
		
		$I->Footer();
	}


?>
