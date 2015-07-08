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
			isset($_POST['nombre_real']) && $_POST['nombre_real']!="")
		{
            $usuario_id=$_POST['usuario_id']; 
            $empleado_id=$_POST['empleado_id']; 
            
			$u_nombre=$_POST['nombre_real']; 
			$u_apaterno=$_POST['paterno_real'];
			$u_materno=$_POST['materno_real'];
			$u_nombre_completo=$u_nombre." ".$u_apaterno."  ".$u_materno;			
			$u_usuario=$_POST['user'];
			$u_passwd=$_POST['passwd'];
			$u_email=$_POST['email'];
			$u_tipo=0;
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
			$cnn = new conexion();
			// Obtener tasa USD
			//echo $resJefe;
			// Actualiza en tabla usuarios
			$Usu->update_Usuario($u_empresa,$u_nombre,$u_apaterno,$u_materno,$u_usuario,$u_proveedor,$u_producto,$u_passwd,$u_email,$usuario_id,$u_estatus,$u_cuentaporcobrar);
			
            // Actualiza en tabla empleados
			$jeferes=$_POST['jefe'];
			if($jeferes > 0){
			$jefe = explode("|",$jeferes);
			$idjefe= $jefe[0];
			$query = "SELECT * FROM empleado WHERE idempleado = $idjefe LIMIT 1";
			$rst = $cnn->consultar($query);
				while ($fila = mysql_fetch_assoc($rst)) {
					$resJefe = $fila['idempleado'];
				}
			}
			$Usu->update_Empleado($u_nombre_completo,$u_telefono,$u_tarjeta,$u_tarjeta_gas,$u_centrocosto,$u_puesto,$empleado_id,$u_directorgeneral,$u_estatus,$resJefe);			//actualizar usuario_tipo
			$Usu->delete_Usuario_Tipo($usuario_id);
			$Usu->delete_Homog_dueno($usuario_id);
			//inserta en la tabla usuario_tipo
			foreach ($_POST['seleccion'] as $id){
				$Usu->add_Usuario_Tipo($usuario_id,$id);
				if ($id==4 || $id==5 || $id==6){
					$Usu->add_Homog_dueno($usuario_id,$id);
				}
			} 
			header("Location: index.php");
		} else {
            header("Location: index.php?error");
        }
	}
	function jefeNombre($idjefe){
		$cnn = new conexion();
		// Obtener tasa USD
		$query = "SELECT * FROM empleado WHERE idempleado = $idjefe";
		$rst = $cnn->consultar($query);
		$item = array();
		while ($fila = mysql_fetch_assoc($rst)) {
			$nombreJefe = $fila['idempleado'].'|'.$fila['nombre'];
		}
		return $nombreJefe;
	}

	function Muestra(){
        
		$Usu= new Usuario();
		if(isset($_GET['usuario_id'])){
			$usuario_id=$_GET['usuario_id'];    
            $Usu->Load_Usuario_By_ID_Edit($usuario_id);
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
<link rel="stylesheet" href="../../lib/js/jquery-ui-1.10.4/development-bundle/themes/base/jquery.ui.all.css">
<script src="../../lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.core.js"></script>
<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.widget.js"></script>
<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.tabs.js"></script>
<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.position.js"></script>
<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.menu.js"></script>
<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.autocomplete.js"></script>
<script language="javascript">
var j = jQuery.noConflict();

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
			
	if( validateForm("nombre_real","","El nombre es un campo requerido",1)){
		if( validateForm("paterno_real","","El apellido paterno es un campo requerido",1)){
			if(validateForm(document.getElementById("amex2").value,"","El número de tarjeta de crédito es inválido",4)){
					if(validateForm("user","","El usuario es un campo requerido",1)){
						if(validateForm("passwd","passwd2","La contraseña es un campo requerido",2)){
							if(validateForm("email","","El mail es un campo requerido",3)){
								if (validaTipos==1){
									if(validateForm("ceco","","El ceco es un campo requerido",1)){
											if(validateForm("puesto","","El puesto es un campo requerido",1)){
												if(validatorForm()){
													return true;
												}else return false;
											}else return false;
									}else return false
								}else {alert("Favor de seleccionar por lo menos un tipo de usuario"); return false;}
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
	$("#imagen").slideDown(1000);
	$("#imagen2").slideUp(1000);
}
function ocultarImagen(){	
	$("#imagen").slideUp(1000);
	$("#imagen2").slideDown(1000);
}
	j(function() {
		j( "#jefe" ).autocomplete({
			source: "services/carga_jefe.php",
			 select: function( event, ui ) {
			}
		});
	});
		j(document).ready(function() {
			j("#limpiajefe").click(function(){
				j("#jefe").val("");
			});
	});
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
			 <td align="right" width="30%"> Nombre<span class="Estilo1">*</span>: 	</td><td> <input value="<?php echo $Usu->Get_dato("u_nombre");?>" type="text" name="nombre_real" id="nombre_real" size="40" /> 										</td></tr>
				 <tr>
			     <td align="right" width="30%"> Apellido Paterno<span class="Estilo1">*</span>: 	</td><td> <input value="<?php echo $Usu->Get_dato("u_paterno");?>" type="text" name="paterno_real" id="paterno_real" size="40" /> 										</td></tr>
				 <tr>
			     <td align="right" width="30%"> Apellido Materno : 	</td><td> <input value="<?php echo $Usu->Get_dato("u_materno");?>" type="text" name="materno_real" id="materno_real" size="40" /> 										</td></tr>
			<tr>
				<td align="right" width="30%">Usuario<span class="Estilo1">*</span>: 			
				</td>
				<td> 
					<input type="text" name="user" id="user" size="40" value="<?php echo $Usu->Get_dato("u_usuario");?>" onKeypress="return bloqueaEspacio(event);"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Contrase&ntilde;a<span class="Estilo1">*</span>:					
				</td>
				<td> 
					<input type="password" name="passwd"  id="passwd" size="40" value="<?php echo $Usu->Get_dato("u_passwd");?>" onKeypress="return bloqueaEspacio(event);"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">Confirma contrase&ntilde;a<span class="Estilo1">*</span>:					
				</td>
				<td> 
					<input type="password" name="passwd2" id="passwd2" size="40" value="<?php echo $Usu->Get_dato("u_passwd")?>" onKeypress="return bloqueaEspacio(event);"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%"> 
					E-mail<span class="Estilo1">*</span>:					
				</td>
				<td>
					<input type="text" name="email" id="email" size="40" value="<?php echo $Usu->Get_dato("u_email")?>" onKeypress="return bloqueaEspacio(event);"/>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%"> 
					Agregar Jefe Directo<span class="Estilo1">*</span>:					
				</td>
				<td>
				<?php
				$usuarioJefe = $Usu->Get_dato("jefe");
				$userJ = ($usuarioJefe > 0) ? jefeNombre($usuarioJefe) : '';
				?>
					<input type="text" name="jefe" id="jefe" size="40" value="<?php echo  $userJ; ?>" onKeypress="return bloqueaEspacio(event);"/>
					<input type="button" name="limpiaJefe" id="limpiajefe" value="Limpiar"/>
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
							$Usu->Load_Usuario_By_ID_Edit($usuario_id);	
							$arregloGuardados=$Usu->get_usuario_tipo_valores($Usu->Get_dato("idfwk_usuario"));
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
								<tr>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<?php
										$esta=0; 
										for ($tmp2=0;$tmp2<count($arregloGuardados);$tmp2++){
											if ($arregloGuardados[$tmp2]['ut_tipo']==$arrTiposUsuario[$indicTiposUsuario2][0]){?> 									
												<td align="left"><input checked type="checkbox" <?php echo "id='sel".$indicTiposUsuario2."'"; ?> name="seleccion[]" value="<?php echo $arrTiposUsuario[$indicTiposUsuario2][0];?>"><?php echo $arrTiposUsuario[$indicTiposUsuario2][1];?></input></td>
												<?php $esta=1;break;}?>
											<?php }?>
											<?php if ($esta==0){?>
											 	<td align="left"><input type="checkbox" name="seleccion[]" <?php echo "id='sel".$indicTiposUsuario2."'"; ?> value="<?php echo $arrTiposUsuario[$indicTiposUsuario2][0];?>"><?php echo $arrTiposUsuario[$indicTiposUsuario2][1];?></input></td>
											<?php }?>										
										<?php $esta=0;
										for ($tmp2=0;$tmp2<count($arregloGuardados);$tmp2++){											
											if ($arregloGuardados[$tmp2]['ut_tipo']==$arrTiposUsuario[$divisiones+$indicTiposUsuario2][0]){?>																					 		
											<td align="left"><input checked type="checkbox" <?php echo "id='sel".($indicTiposUsuario2+$divisiones)."'"; ?> name="seleccion[]" value="<?php echo $arrTiposUsuario[$divisiones+$indicTiposUsuario2][0];?>"><?php echo $arrTiposUsuario[$divisiones+$indicTiposUsuario2][1];?></input></td>
										<?php $esta=1;break;}?>
										<?php }?>
											<?php if ($esta==0){?>
												<td align="left"><input type="checkbox" <?php echo "id='sel".($indicTiposUsuario2+$divisiones)."'"; ?> name="seleccion[]" value="<?php echo $arrTiposUsuario[$divisiones+$indicTiposUsuario2][0];?>"><?php echo $arrTiposUsuario[$divisiones+$indicTiposUsuario2][1];?></input></td>
										<?php }?>
									</tr>
							<?php								
							}
							?>						
				
				</table>
				<table width="80%"  align="center" cellpadding="4" cellspacing="4" border="0" bgcolor="#f4f4f4" style="padding-top: 20px;">
				<tr>
				<td align="right">&nbsp;</td>
				<td><strong>Informaci&oacute;n del Empleado</strong></td>
		    </tr>    		    
		    <?php
		    	$idDirGral=$Usu->Load_dir_general();		    	
		    	$Usu->Load_Usuario_By_ID_Edit($usuario_id);
		    	if ($idDirGral==$Usu->Get_dato("idfwk_usuario")){ 		    	
		    		error_log("Es un director general?".$Usu->Get_dato("director_general"));?>
				<tr> 
		    			<td align="right" width="30%">
			         		Director General:
		         		</td>		    	
		    	<?php 
		    	if ($Usu->Get_dato("director_general")==0){?>		    		
		         		<td>
				         	<input type="checkbox" id="dirgeneral" name="dirgeneral" />
		         		</td>
		         <?php }else{?>
		         		<td>
				         	<input type="checkbox" id="dirgeneral" name="dirgeneral" checked/>
		         		</td>
		         		<?php }?>
		    	</tr>        
		    	<?php }?>
			<tr>
				<td align="right" width="30%"> 
					Empresa:					</td>
				<td> 
				<?php
					$empresa=new Empresa();
					$arr=$empresa->Load_all_activas();
				?>
					<select name="empresa_id" id="empresa_id">
						<?php
						error_log(count($arr));
						if (count($arr)<=0){ 
							$arr2=$empresa->Load_all();
							foreach($arr2 as $arrE){
								if($Usu->Get_dato("u_empresa")==$arrE['e_id']){?>
									<option value=<?php echo $arrE['e_codigo']?>><?php echo $arrE['e_codigo']?></option>								
								<?php }
							}							
							?>
													
						<?php }else{foreach($arr as $arrE){
						?>
							<option name="<?php echo $arrE['e_codigo'];?>" id="<?php echo $arrE['e_codigo'];?>" 
								value="<?php echo $arrE['e_codigo'];?>"
								<?php
									if($Usu->Get_dato("u_empresa")==$arrE['e_codigo'])
										echo "selected";
								?>
							>
							<?php echo $arrE['e_codigo'];?>							
							</option>
						<?php
						}}
						?>
					</select>					
				</td>
			</tr>
             <tr>
               <td align="right">Centro de Costos:</td>
                <td> <select name='ceco_id'>
                    <?php 
                        $query=sprintf("SELECT cc_id, cc_centrocostos, cc_nombre FROM cat_cecos c WHERE cc_estatus = 1");
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
				<input type="hidden" name="producto" id="producto" size="40" value="" />
			</tr>
			<tr>
				<td align="right">Puesto<span class="Estilo1">*</span>:</td>
				<td><input type="text" name="puesto" id="puesto" size="40" value="<?php echo $Usu->Get_dato("npuesto");?>"/></td>
		    </tr>
			<tr>
            	<td align="right">Tarjeta de Cr&eacute;dito:
				</td>
			  	<td>
					<input type="text" name="amex2" id="amex2" size="40" onkeypress="return validaNum(event);" value="<?php echo $Usu->Get_dato("notarjetacredito");?>" />
				</td>
		    </tr>
			<tr>
				 	<td align="right" width="30%">Estatus:</td>
                        <td>
                            <select name='estatus'>
                            <?php if ($Usu->Get_dato("u_activo")==1){?>
                            	<option value='1' selected>ACTIVO</option>
                                <option value='0'>INACTIVO</option>                                
                             <?php }else{?>
                            	<option value='1'>ACTIVO</option>
                                <option value='0' selected>INACTIVO</option>
                                <?php } ?>
                                                                
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