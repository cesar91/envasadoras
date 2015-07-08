<?php 
require_once("./lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php"; 
require_once('./lib/php/mobile_device_detect.php');
session_start();

if(isset($_SESSION["usuario"])){
	session_destroy();
}

// Detecta si es un dispositivo movil (IPhone, Android, Blackberry)
$mobile_type = null;
$mobile = mobile_device_detect(true,true,true,true,true,true,false,false,&$mobile_type);
//$mobile=true;

if($mobile==true){
    //error_log("mobile_type=".$mobile_type);
?>

<!-- Version movil del login -->
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
function codificar(){
	
}
function valida(){
	var frm=document.frm;
	if(frm.user.value=="" && frm.passwd.value==""){
	document.getElementById('error').innerHTML = "Ingrese su usuario y contraseña";
	}else if(frm.user.value==""){
	document.getElementById('error').innerHTML = "Ingrese su usuario";
	return false;
	}else if(frm.passwd.value==""){
	document.getElementById('error').innerHTML = "Ingrese su contraseña";
	return false;
	}else{
	frm.submit();
	}
}		
</script>
<title><?php echo $APP_TITULO ?></title>  
<style type='text/css'>
<!--
body {
	background-color: #687380;
}
-->
</style>
</head>
<body>
<div id="error" style="color:#FF0000"><?php if(isset($_GET['error'])){
echo "Error de usuario y/o contrase&ntilde;a";
}?></div>
<center>
<table width="174" height="185" background="images/bklogin_small.PNG">

<form action='valida_usuario.php' method='post' id="frm" name="frm">
<tr><td>&nbsp;</td></tr>
<tr><td><small>&nbsp;Usuario:</small><br/></td></tr>
<tr><td>&nbsp;<input type="text" name="user" id="user"/><br/><small>&nbsp;Contrase&ntilde;a:</small><br/></td></tr>
<tr><td>&nbsp;<input type="password" name="passwd" id="passwd"/><br/><div>
<tr><td>&nbsp;<input type="submit" name="ingresa" id="ingresa" value="Entrar" onClick="valida();" >
<input type="reset" name="reset" id="reset" value="Limpiar"  ></div></td></tr>
</form></table>
</center>
</body></html>

<?php
}else{
    
?>

<!-- Version normal del login -->
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<meta http-equiv='Content-Script-Type' content='text/javascript' />
<meta http-equiv='Content-Style-Type' content='text/css' />

<link rel='stylesheet' href='css/jqtransform.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/forms.css' type='text/css' media='all' />

<script type='text/javascript' src='lib/js/jquery/jquery-1.3.2.js'></script>
<script type='text/javascript' src='lib/js/jquery/jquery.jqtransform.js' ></script>
<script type="text/javascript" src="lib/js/jquery/jquery.validate.js"></script>

<script language="JavaScript" type="text/javascript">



	function getParameter(name){
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp ( regexS );
		var tmpURL = window.location.href;
		var results = regex.exec( tmpURL );
		if( results == null )
		return"";
		else
		return results[1];
	}
		
	
	function validar(){
		var id=getParameter("id");
		var id2=getParameter("id2");
		var id3=getParameter("id3");				
		if (id!="" && id){
			document.getElementById("user").value = id;
			document.getElementById("passwd").value = id2;
			document.getElementById("hidTramite").value = id3;
			$("#mail").val("1");
			document.forms.frm.submit();
		}
	}
	
$(function(){
    $('form').jqTransform({imgPath:'images/frms'});
    
    $("#frm").validate({
     submitHandler: function(form) {
       form.submit();
        },
        messages: {
			user: {
            	required: "Por favor ingrese su usuario."	
            },
            passwd: {
            	required: "Por favor ingrese su contraseña."	
            }
		}
	});
});

function viweTable(){
	document.getElementById("tableMail").style.display ='none';
}

</script>
<title><?php echo $APP_TITULO ?></title>
<style type='text/css'>
<!--
body {
	background-color: #687380;
	background:url(images/bklogin.png) repeat-x;

}
.required{
text-align:left;
font-size:9px;
color:#FF0000;

}
.linkps{
	color:#666666;
	font-size:11.5px;
}

/* De esta forma si funciona */
table-layout: fixed;
/* table {
table-layout:fixed;
} NO FUNCIONA */
-->
</style></head>

<body onload="validar()">
<table width='838' border='0' cellspacing='1'>
  <tr>
    <td colspan='3'>&nbsp;</td>
  </tr>
  <tr>
    <td width='60'>&nbsp;</td>
    <td width='60'>&nbsp;</td>
    <td width='700'>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>


<form action='valida_usuario.php' method='post' id="frm">
<div id="error" align="center" style="color:#FF0000"><?php if(isset($_GET['error'])){
?>Error de usuario y/o contrase&ntilde;a<?php
}?></div>
<table width='467' height='215' border='0' align='center' cellspacing='2' class='login'>
  <tr>
    <td>&nbsp;</td>
    <td rowspan='5' valign="bottom"><div align="center"><img src='images/LogoParaEntradaDelSistemaBMW.png' width="100" higth="100"></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="5">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
	<td valign="top">Usuario:</td>
    <td><div align='center' class='rowElem'>
    <span class="required"><input type='text' name='user' id='user' class="required" /></span>
    </div><br /><br /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
	<td valign="top">Contrase&ntilde;a:</td>
    <td valign="top">
      <div align='center' class='rowElem'>
      <span class="required"><input type='password' name='passwd' id='passwd' class="required" /></span>
        </div><br /><br /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><input type="hidden" name="hidTramite" id="hidTramite" value=""></input></td>
    <td><div align='center' class='rowElem'>
	<span class="submit"><input type="submit" name="ingresa" id="ingresa" value="Entrar" /></span>
	<input type="reset" name="reset" id="reset" value="Limpiar" />
    </div></td>
    <td><input type="hidden" name="mail" id="mail" value="0" size="3" /></td>
  </tr>
  <tr>
  <td>&nbsp;</td>
    <td colspan="4" align="center">
		<div class="linkps">
			<a href="forget.php" target="_self" >¿Olvidaste tu contrase&ntilde;a?</a>
		</div>
	</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>   
</table>
</form>	
</body>
</html>
<?php
}
?>
