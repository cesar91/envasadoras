<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

$empleado = $_SESSION["idusuario"];
$usuario  = $_SESSION["usuario"];
$Usu=new Usuario();

//$Usu->Load_usuario($empleado);
//$Usu->Load_empleado($empleado);
//$rowsEmpleado=$Usu->Load_empleado($empleado);
$Usu->Load_Usuario_Edit($empleado);
$rowsEmpleado=$Usu->Load_Usuario_Edit($empleado);

$cnn	= new conexion();
$MsjDelegar="";
// cancela delegacion
if(isset($_GET['action']) && $_GET['action']=="cancel" &&  isset($_GET['id']) && $_GET['id']!=""){
	$asignadoa=$Usu->SetRechazaDelegado($_GET['id']);
	
	$N=new Notificacion();
	
	$U=new Usuario();
	//$U->Load_empleado($asignadoa);
	$U->Load_Usuario_Edit($asignadoa);
	
	//$cad="Estimado <strong>".$U->Get_dato_Empleado('nombre')."</strong>  el usuario <strong> {$_SESSION["usuario"]}</strong> ha rechazado la delegacion que le solicito.";
	$cad="Estimado <strong>".$U->Get_dato('nombre')."</strong>  el usuario <strong> {$_SESSION["usuario"]}</strong> ha <strong>RECHAZADO</strong> la delegaci&oacute;n que le solicit&oacute;.";
	
	//$N->Add(utf8_encode($cad),0,$asignadoa,$coment="Ninguno",0);
	$N->Add(utf8_encode($cad),0,$U->Get_dato('idempleado'),$coment="Ninguno",0);
			
	$cad.="<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.131.127/eexpenses'><strong>aqu&iacute;</strong></a>.";
				
	$N->set_contenido($cad);
	$N->set_destinatario($U->Get_dato("u_email"));
	//$N->notificaUsu();
	
	$cnn=new conexion();
	$query=sprintf("update notificaciones set nt_activo=2 where nt_id ='%s'",$_GET['id']);
	
	$cnn->insertar($query);
	
	$completeUrl="";
	
	if(isset($_GET['ltotal'])&& isset($_GET['lactual']))
		$completeUrl="ltotal=".$_GET['ltotal']."&lactual=".$_GET['lactual'];
	
	header("Location: ./index.php?activo=yes&".$completeUrl);
}
//acepta delegacion
else if(isset($_GET['action']) && $_GET['action']=="aceptar" &&  isset($_GET['id']) && $_GET['id']!=""){

	$asignadoa=$Usu->SetAceptaDelegado($_GET['id']);
	
	$N=new Notificacion();
	
	$U=new Usuario();
	//$U->Load_empleado($asignadoa);
	$U->Load_usuario($asignadoa);
			
	//$cad="Estimado <strong>".$U->Get_dato_Empleado('nombre')."</strong>  el usuario <strong> {$_SESSION["usuario"]}</strong> ha aceptado la delegacion que le solicito.";
	$cad="Estimado <strong>".$U->Get_dato('nombre')."</strong>  el usuario <strong> {$_SESSION["usuario"]}</strong> ha <strong>ACEPTADO</strong> la delegaci&oacute;n que le solicito.";
	
	//$N->Add(utf8_encode($cad),0,$asignadoa,$coment="Ninguno",0);
	$N->Add(utf8_encode($cad),0,$U->Get_dato('idempleado'),$coment="Ninguno",0);
			
	$cad.="<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.131.127/eexpenses_danone'><strong>aqu&iacute;</strong></a>.";
				
	$N->set_contenido($cad);
	$N->set_destinatario($U->Get_dato("u_email"));
	//$N->notificaUsu();
	
	$cnn=new conexion();
	$query=sprintf("update notificaciones set nt_activo=0,  nt_aceptado=1 where nt_id ='%s'",$_GET['id']);
	
	$cnn->insertar($query);
	
	$completeUrl="";
	
	if(isset($_GET['ltotal'])&& isset($_GET['lactual']))
		$completeUrl="ltotal=".$_GET['ltotal']."&lactual=".$_GET['lactual'];
	
	header("Location: ./index.php?activo=yes&".$completeUrl);
}


?>
<html>
<head>

<script src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script src="../../lib/js/jquery/jquery.fadeSliderToggle.js" type="text/javascript"></script>
<script src="../../lib/js/withoutReloadingUsuario.js" type="text/javascript"></script>
<script src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>

<script type="text/javascript">
		$(document).ready(function() {
			$('.fadeNext').click(function(){
				//alert($('#abrir').attr("class"))
  				$(this).next().slideToggle("slow")
  				$(this).next().css("opacity","100")
  				$(this).next().css("display","block")
 				return false;
 			})
 		});	
</script>

<script type="text/javascript">
	var doc;
	var valPs=false;

	doc = $(document);
	//doc.ready(inicializarEventos);
	
	function inicializarEventos(){

		// $(".fadeNext").click(function(){   
  			// $(this).next().fadeSliderToggle()
 			// return false;
 		// });

		$("#name_h").autocomplete("ingreso_sin_recargar_proceso_usuario.php", {
			minChars:3,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:seleccionaItem,
			onFindValue:buscaIdUser,
			formatItem:arreglaItem,
			autoFill:false,
			extraParams:{aux:1}
			});//fin autocomplete
			
		$("#id_user").autocomplete("ingreso_sin_recargar_proceso_usuario.php", {
			minChars:1,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:seleccionaItem2,
			onFindValue:buscaUser,
			formatItem:arreglaItem,
			autoFill:false,
			extraParams:{aux:2}
			});//fin autocomplete*/
		
		$("#CheckP").click(function(){
			if($(this).attr('checked') == true){
				
				$("#name_h").removeAttr("disabled");
				$("#id_user").removeAttr("disabled");
				$("#coment").removeAttr("disabled");
				
			}
			else{
				
				if($("#id_user").val()!=""){
					$.post("ingreso_sin_recargar_proceso_usuario.php",{ id_userUnsigned:$("#id_user").val()}, function(data){
						$("#Proceso").html("Se a removido la delegación al usuario...");
					$("#Proceso").fadeOut(2700);});
				}
				$("#name_h").val("");
				$("#id_user").val("");
				$("#name_h").attr("disabled", "disabled");
				$("#id_user").attr("disabled", "disabled");
				$("#coment").attr("disabled", "disabled");
				
			}
		});
	}
	
	function seleccionaItem(li) {
		buscaIdUser(li);
	}//fin seleccionaItem
	
			
	function arreglaItem(row) {
	//da el formato a la lista
		return row[0];
	}//fin arreglaItem
	
	
	function seleccionaItem2(li) {
		buscaUser(li);
	}//fin seleccionaItem
	
	function buscaUser(li) {
		if(li==null){ 
		return null;
		}
		if(!!li.extra){
		var valorLista=li.extra[0];
		}else{ 
			var valorLista=li.selectValue;
			$("#load_div").html("Cargando espere...");
			$.ajax({
				//busca el nombre del usuario en base al id
				url: 'ingreso_sin_recargar_proceso_usuario.php',
				type: "POST",
				data: "nombre="+valorLista+"&aux=2",
				dataType: "html",
				success: function(datos){
						$("#name_h").val(datos);
						$("#load_div").html("");
						}
				});//fin ajax	
		}
	}//fin buscaUser

	function buscaIdUser(li) {
		if(li==null){ 
		return null;
		}
		if(!!li.extra){
		var valorLista=li.extra[0];
		}else{ 
			var valorLista=li.selectValue;
			$("#load_div").html("Cargando espere...");
			$.ajax({
				//busca id de proveedor en base al nombre de usuario				
				url: 'ingreso_sin_recargar_proceso_usuario.php',
				type: "POST",
				data: "nombre="+valorLista+"&aux=1",
				dataType: "html",
				success: function(datos){
						$("#id_user").val(datos);
						$("#load_div").html("");
						}
				});//fin ajax	
		}
	}//fin buscaIdUser
	
	
	
	function validaNum(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		
		if(cTecla==8) return true;
		if(cTecla==0) return true;
		if(cTecla==122) return true;
		if(cTecla==120) return true;
		if(cTecla==118) return true;
		if(cTecla==99) return true;
			patron=/^([0-9]{1,2})?$/;
			cTecla= String.fromCharCode(cTecla);			
		return patron.test(cTecla);
	}
	
	function validaTelefono(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		
		if(cTecla==8) return true;
		if(cTecla==0) return true;
		if(cTecla==122) return true;
		if(cTecla==120) return true;
		if(cTecla==118) return true;
		if(cTecla==99) return true;
			patron=/^([0-9]{1,2})?-?$/;
			cTecla= String.fromCharCode(cTecla);			
		return patron.test(cTecla);
	}
	
	function bloqueaEspacio(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		if(cTecla==32)
			return false;
		else
			return true;
	}

	function activePS(){
		if(!valPs)
			this.valPs=true;
		else{
			this.valPs=false;
			$("#passwd").val("");
			$("#passwd2").val("");
		}
	}
	
	
// 	});

	var bandEmail=false;
	
	function validatorForm(){

		if($("#email").val()!= ""){
			if(this.valPs==true && ( $("#passwd").val()=="" || $("#passwd2").val()=="") ){
				alert("Los campos marcados con (*) son obligatorios.");
				return false;
			}
			else if ($("#email").val().indexOf('@', 1) == -1 || $("#email").val().indexOf('.',$("#email").val().indexOf('@', 0)) == -1) {			
				alert("Dirección de email inválida");
				return false;
			}
			else if (!(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test($("#email").val()))){
				alert("Dirección de email inválida");
				return false;
			}
			else if($("#telefono").val() != "" && !(/^[0-9]+(-? ?[0-9]+)*$/.test($("#telefono").val()))){
					alert("Numero de telefono invalido");
					return false;
			}
			// //return false;
			else if($("#passwd").val() != $("#passwd2").val()){
				alert("La contraseña no coincide, por favor intente nuevamente");
			 	return false;				
			 }
			 else if(!searchJ){
				 alert("El número de empleado de jefe no existe");
				 return false;
			 }
			else{
				sendUpdate();
				$("#contrasena").fadeOut(100);
				$("#contrasena").css("display", "none");
			}
		}
		else 
			alert("Los campos marcados con (*) son obligatorios");
		
	}
	
	function checa(st,id){
		if(st==1){
			$("#name_h").attr("disabled", "disabled");
			$("#id_user").attr("disabled", "disabled");
			$("#coment").attr("disabled", "disabled");
			$('input[name=CheckP]').attr('checked', false);			
			//alert ($('#CheckP').attr('checked'));						
		}
			
		else{
			$('input[name=CheckP]').attr('checked', true);
			$("#name_h").removeAttr("disabled");
			$("#id_user").removeAttr("disabled");
			$("#coment").removeAttr("disabled");
			$("#id_user").val(id);
						
			$.post("ingreso_sin_recargar_proceso_usuario.php",{ nombre: id, aux:2 }, function(data){$("#name_h").val(data)});						
			//alert ($('#CheckP').attr('checked'));
		}
	}
	
	function validatorFormGrant(){
		if( $("#name_h").val()!="" && $("#id_user").val()!="" ){
			sendUpdateGrant();			
		}
		else{					
			alert("Los campos con * son obligatorios");
			return false;		
		}
		
	}
	
	function deleteClass(){
		//$("#divTable").removeClass("fader");
		$("#divTable").addClass("fader");
		//$('divTable').removeClass('fader');
	}
</script>
<meta http-equiv="Pragma" content="no-cache">
<style>
	.fader{opacity:0;display:none;}
</style>
<style>
	.style1 {color: #FF0000; vertical-align:top}
	.divProceso { color:#FF0000; font-size:14px}
</style>
<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
</head>

<body>
<?php

$I	= new Interfaz("Usuarios:: Actualización de mi Perfil",true);
?>
<br><br>

<form name="dataUser" id="dataUser" method="post">
<center>
<table>
<tr><td valign="top" width="45%">
<table width="400" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="#f5f5f5">
    <tr>
    	<td class="formlabel">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
    	<td class="formlabel"><div align="right">Correo electr&oacute;nico: </div></td>
        <td>
			<input name="email" type="text" class="SrcInput" id="email" value="<?php echo $Usu->Get_dato('u_email'); ?>" disabled="disabled"/>
			<input name="email" type="hidden" class="SrcInput" id="email" value="<?php echo $Usu->Get_dato('u_email'); ?>" readonly="readonly"/>
		</td>
    </tr>
    <tr>
    	<td class="formlabel"><div align="right">Tel&eacute;fono:</div></td>
        <td>
			<input name="telefono" type="text" class="SrcInput" id="telefono" value="<?php if($rowsEmpleado>0) echo $Usu->Get_dato('telefono'); ?>" onKeyPress="return validaTelefono(event);">
		</td>
    </tr>
    <tr>
		<td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <!--
    <tr>
    	<td nowrap><div align="right"><strong>No. Empleado del Jefe inmediato<span class="style1">*</span>: </strong></div></td>
        <td><input name="jefe" type="text" class="SrcInput" id="jefe" value="<?php if($rowsEmpleado>0) //echo $Usu->Get_dato_Empleado('jefe'); ?>" onKeyPress="return validaNum(event);" onKeyUp="searchJefe()"></td>
    </tr>
    -->
	<tr>
    	<td class="formlabel" colspan="2">
			&nbsp;&nbsp;
			
			<a href="#" class="fadeNext" onClick="activePS()">Cambiar contrase&ntilde;a</a>
			<div id="contrasena" class="fader">
				<table>
					<tr>
						<td width="185">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td align="right" valign="middle" class="formlabel">Nueva contrase&ntilde;a<span class="style1">*</span>:</td>
						<td align="center" valign="middle"><input name="passwd" type="password" id="passwd" class="SrcInput" /></td>
					</tr>
					<tr>
						<td align="right" valign="middle" class="formlabel">Confirmar contrase&ntilde;a<span class="style1">*</span>:</td>
						<td align="center" valign="middle"><input name="passwd2" type="password" id="passwd2" class="SrcInput" /></td>
					</tr>
				</table>
			</div>
	
		</td>
    </tr>
   
    <tr>
    	<td colspan="2" class="txtBluAr9">&nbsp;</td>
    </tr>    
	<tr>
    	<td>
			<!--<input name="idempleado" type="hidden" id="idempleado" value="<?php //echo //$row_empleado['idempleado']; ?>">
        	<input name="idusuario" type="hidden" id="idusuario" value="<?php //echo //$row_usuario['idfwk_usuario']; ?>"></td>-->
        <td align="right">
			<input name="actualiza" type="button" id="actualiza" onClick="return validatorForm();" value="Actualizar">&nbsp;&nbsp;
			</td>
	</tr>	
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
    <tr>
    	<td colspan="2"><div class="txtBluAr9" align="center">Los campos marcados con (<span class="style1">*</span>) son obligatorios. </div></td>       
    </tr>
    <tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>
</td>

<!--
<td valign="top" width="60%">
<table width="400" border="0" align="center" cellpadding="2" cellspacing="0">
	<tr>
    	<td colspan="2" class="formlabel">
			<a href="#" class="fadeNext" onClick="">Heredar privilegios</a>
		
			<div class="fader" align="right">
				
				<br>
				<div class="txtBluAr9" align="left" style="width:355px"><strong>Nota: Si desea heredar privilegios marque la opci&oacute;n heredar; en caso contrario si desea quitar los privilegios al usuario desmarque la opci&oacute;n.</strong></div>
				<br>				
				<div align="left" style="width:365px; color:#333333">				
					<input type="checkbox" id="CheckP" name="CheckP">Heredar
					<br><br>
					Buscar por:
				</div>
				
				Nombre de usuario<span class="style1">*</span>:
    			<input name="name_h" type="text" id="name_h" style="width:205px">
				&nbsp;
				<br>
				<br>
				ID de usuario<span class="style1">*</span>:
				<input name="id_user" type="text" id="id_user" style="width:130" onKeyPress="return validaNum(event)">
				&nbsp;
				<br><br>
				<div align="center">Comentario:</div><br>
				<textarea name="coment" cols="30" id="coment"></textarea>
				&nbsp;

				<?php				
					if($rowsEmpleado==0 || $Usu->Get_dato('delegado')==0 || $Usu->Get_dato('delegado')==""){
				?>
						<script type="text/javascript">
							checa(1);
						</script>
					<?php
					}
					
					else{
					?>
						<script type="text/javascript">
							checa(2,<?php echo $Usu->Get_dato('delegado');?>);
						</script>
					<?php
					//echo $Usu->Get_dato_Empleado('delegado');
					}				
					?>
					<div align="center" class="style1" id="load_div" ></div>
					<br>
					<input name="actualiza" type="button" id="actualiza" onClick="return validatorFormGrant();" value="Heredar">&nbsp;&nbsp;					
			</div>
			<br>
			<br>

			<a href="#" class="fadeNext" onClick="">Delegaciones Pendientes por aprobar</a>
			
				<div id="divTable"  align="right">
					<?php	
						$paginacion="";
						if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!="")		
							$paginacion="ltotal=".$_GET["ltotal"]."&lactual=".$_GET["lactual"]."&";
						
							$L	= new Lista("activo=yes");
							$L->Cabeceras("Folio");
							$L->Cabeceras("Solicitante");
							$L->Cabeceras("Fecha Registro");
							$L->Herramientas("C","./index.php?".$paginacion."activo=yes&action=cancel&id=");
							$L->Herramientas("A","./index.php?".$paginacion."activo=yes&action=aceptar&id=");
		
							//$query="select nt_id, nombre, nt_fecha from notificaciones inner join usuario  on (nt_creador=u_id ) inner join empleado on(u_usuario = numempleado) where nt_asignado_a='".$_SESSION["idusuario"]."' and nt_activo=1 and nt_aceptado=0 and nt_tramite=0 and nt_asignado_a!=0 order by nt_id desc";
							$query="select nt_id,
nombre,
nt_fecha
from notificaciones
inner join usuario
on (nt_remitente=u_id )
inner join empleado
on (nt_remitente=idempleado)
where nt_asignado_a=".$_SESSION["idusuario"]."
and nt_activo=1
and nt_aceptado=0
and nt_tramite=0
and nt_asignado_a!=0
order by nt_id desc";
						
							$L->muestra_lista($query,0);
					?>
				</div>
			<?php 
				if( !isset($_GET['activo']))
				{						
			?>					
					<script language="javascript" type="text/javascript">
						deleteClass();
					</script>
			<?php
			} ?>				
		</td>
    </tr>
</table>
</td>
-->
</tr>
</table>
</center>
</form>
<center><div id="Proceso" class="divProceso"></div></center>
<?php	
	$I->Footer();
?>
</body>
</html>