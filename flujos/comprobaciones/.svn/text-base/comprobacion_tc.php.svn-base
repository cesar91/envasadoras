<?php require_once('Connections/tyeconn.php'); ?>
<?php require_once('Connections/fwk_db.php'); ?>
<?php require_once('lib/php/constantes.php'); ?>
<?php require_once('lib/php/utils.php'); ?>
<?php require_once('lib/php/mail.php'); ?>
<?php
session_start();
$PAGELEVEL=$FWK_PRIV_GENERAL;
$AUTH_redirectTo="index.php?notLogged=true";
pageClearence($PAGELEVEL,$AUTH_redirectTo);

//busca la variable partidas en la sesión
$partidas=array();
if(isset($_SESSION['PARTIDASCOMPTC'])){
	$partidas=$_SESSION['PARTIDASCOMPTC'];
}
//evento borrar una partida
if(isset($_GET['del'])){
	$index=$_GET['del'];
	if(isset($partidas[$index])){
		$preslice=array();
		if($index>0){
			$preslice=array_slice($partidas,0,$index);
		}
		$postslice=array();
		if($index<count($partidas)){
			$postslice=array_slice($partidas,$index+1);
		}
		$partidas=array_merge($preslice,$postslice);
		$_SESSION['PARTIDASCOMPTC']=$partidas;
		$insertGoTo="tye_nueva_comprobacion_tc.php?idempleado=".$_GET['idempleado'];
		 header(sprintf("Location: %s", $insertGoTo));
	}
}
//evento agregar
if(isset($_POST['agregar'])){
		//recupera variables y construye la partida.
		$part=array();
		$part["concepto"]=$_POST['concepto'];
		$part["moneda"]=$_POST['moneda'];
		if ($_POST['monto']=='')
				{$part["monto"]=0;}
				else {$part["monto"]=$_POST['monto'];}
		$part["fecha"]=$_POST['fecha'];
		if ($_POST['tipocambio']=='')
				{$part["tipocambio"]=1;}
				else{$part["tipocambio"]=$_POST['tipocambio'];}
		$part["idamex"]=$_POST['cargos'];
		if ($_POST['iva']=='')
				{$part["iva"]=0;}
				else{$part["iva"]=$_POST['iva'];}
		$part["iva"]=$_POST['iva'];

mysql_select_db($database_tyeconn, $tyeconn);
$query_cargostc = sprintf("SELECT idamex, notransaccion, tarjeta, date_format(fecha,'%%d/%%m/%%Y') as fecha, monto, estatus, moneda, TC, concepto FROM amex WHERE idamex = %s ", $part["idamex"]);
$cargostc = mysql_query($query_cargostc, $tyeconn) or die(mysql_error());
$row_cargostc = mysql_fetch_assoc($cargostc);
$totalRows_cargostc = mysql_num_rows($cargostc);

		$part["cargo"]=$row_cargostc['notransaccion'].": ".$row_cargostc['fecha']." $".$row_cargostc['monto']."";
		$part["comentario"]=$_POST['comentario'];
		$part["comentario"] = depuraCaracteres($part["comentario"]);
		//agrega partida al lote
		array_push($partidas,$part);
		$_SESSION['PARTIDASCOMPTC']=$partidas;
		$insertGoTo="tye_nueva_comprobacion_tc.php?idempleado=".$_POST['idempleado'];
		 header(sprintf("Location: %s", $insertGoTo));
}
//evento guardar
if(isset($_POST['guardar'])){
	if(count($partidas)>0){
		//valida que los cargos fueron comprobados por completo.
		//agrupa cargos por idamex.
		$agrupados=array();
		$agrupadosVal=0;
		for($row=0;$row<count($partidas);$row++){
			$partrow=$partidas[$row];
			if(array_key_exists($partrow['idamex'],$agrupados)){
				$agrupadosVal=$agrupados[$partrow['idamex']];
				$agrupadosVal=$partrow['monto']+$partrow['iva']+$agrupadosVal;
				$agrupados[$partrow['idamex']]=$agrupadosVal;
			}else{
				$agrupadosVal=($partrow['monto']+$partrow['iva']);
				$agrupados[$partrow['idamex']]=$agrupadosVal;
			}
		}
	$llaves=array_keys($agrupados);
	mysql_select_db($database_tyeconn, $tyeconn);
	foreach($llaves as $key){

		$montoCapturado=$agrupados[$key];


		$query_cargostc = sprintf("SELECT idamex,  monto, notransaccion FROM amex WHERE idamex = %s ", $key);
		$cargostc = mysql_query($query_cargostc, $tyeconn) or die(mysql_error());
		$row_cargostc = mysql_fetch_assoc($cargostc);
		$variacion=round(doubleval($row_cargostc['monto'])-doubleval($montoCapturado));


		if($variacion< -1.0 || $variacion>=1.0){
			$mesage="El cargo ".$row_cargostc['notransaccion']." no est&aacute; comprobado completamente, por favor verifique";
			$insertGoTo=sprintf("tye_nueva_comprobacion_tc.php?errormsg=%s&idempleado=%s",base64_encode($mesage),$_POST['idempleado']);
			header(sprintf("Location: %s", $insertGoTo));
			die();
		}


	}
	//cargos completos.

		$idempleado=base64_decode($_SESSION['IDEMPLEADO']);
		$idusuariologeado=base64_decode($_SESSION['FWK_USERID']);
		$_parametros=getParametros();
		//primer punto de aprobacion siempre es el jefe.
		$jefe=buscaJefe($idempleado);
		
		
		//inserta registro de comprobación.
	//refacturacion
				$refacturar=0;
		if($row_empleado['idunidadnegocio']==$TYE_UNIDADNEGOCIO_DANONE || isAuthorized($FWK_PRIV_CONFIGURACION) ){
			$refacturar=$_POST['refacturar'];
			}
	// fin refacturacion
		/*mysql_select_db($database_tyeconn, $tyeconn);
		$insertBase=sprintf("INSERT INTO comprobacionamex(fecha,usuario, refacturar) values(now(),'%s','%s')",$idusuariologeado, $refacturar);*/
		
		//JUH 2010/01/11 modificacion al query al campo usuario ya que utilizaba el idfwk_usuario el cual coincidia con el idempleado, sin embargo por ciertas discrepancias en la BD esta coincidencia ya no existe y las comprobaciones de amex se iban a otro empleado y caian mal en SAP
		
				$insertBase=sprintf("INSERT INTO comprobacionamex(fecha,usuario, refacturar) values(now(),'%s','%s')",$idempleado, $refacturar);
		$insert = mysql_query($insertBase, $tyeconn) or die(mysql_error());
		$idComprob=mysql_insert_id();


		$tipocomprobacion=$TYE_TIPO_COMPROBACION_TARJETA;

		//inserta gastos de la comprobación

		$insertBase="INSERT INTO gastosamex(monto, impuesto, idcat_gastos, idamex, moneda, TC, estatus, autorizador,fechacomprobacion,observaciones,comentario,idcomprobacionamex) VALUES('%s','%s','%s','%s','%s','%s','%s','%s',now(),'%s','%s','%s')";
		for($row=0;$row<count($partidas);$row++){
			$statusDef=$TYE_STATUS_COMPROBACIONES_PEND;
			$partrow=$partidas[$row];

			if($partrow['concepto']==62 || $partrow['concepto']==63){
				$statusDef=100;
			}

$observacion=($_POST['observaciones']);
$observacion=depuraCaracteres($observacion);

			$sqlInsert=sprintf($insertBase,$partrow['monto'],$partrow['iva'],mysql_real_escape_string($partrow['concepto']),
			$partrow['idamex'],$partrow['moneda'],
			$partrow['tipocambio'],
			$statusDef,$jefe[0],$observacion,
			$partrow['comentario'],$idComprob);
			/*echo $jefe[0];
			exit;*/
			$insert = mysql_query($sqlInsert, $tyeconn) or die(mysql_error());
		}

		//===================NOTIFICA DE COMPROBACION=======================

			//al jefe

	$noTarjeta=buscaTarjetaAmex($idempleado);
	$event=sprintf($TYE_NOTIF_ALTACOMPROBACION_TC,$noTarjeta,$TYE_STATUS_COMPROBACIONES[$TYE_STATUS_COMPROBACIONES_PEND]," para su autorizacion");
	notifica($event, base64_decode($_SESSION['FWK_USERID']),$jefe[2],$TYE_TIPO_NOTIFICACION_NORMAL,$idusuariologeado,$TYE_TIPOOBJ_NOTIFICACION_TC,$TYE_STATUS_COMPROBACIONES_PEND);
 	$_mto=$jefe[1];
	$_mname="";
	$_msubject=$TYE_MAIL_SUBJECT_ACT;
	$_mbody=sprintf($TYE_MAIL_GENERICO,"usuario",$event);
    sendMail($_msubject, $_mbody,$_mto,$_mname);


		//===================FIN NOTIFICACION===============================

		$_SESSION['PARTIDASCOMPTC']=array();
		session_unregister($_SESSION['PARTIDASCOMPTC']);
		$insertGoTo="tye_comprobaciones.php?successmsg=".base64_encode("Comprobación guardada exitosamente");
		header(sprintf("Location: %s", $insertGoTo));
	}else{


		$insertGoTo="tye_nueva_comprobacion_tc.php?idempleado=".$_POST['idempleado'];
		header(sprintf("Location: %s", $insertGoTo));
	}
}

//evento limpiar
if(isset($_POST['limpiar'])){
	$partidas=array();
	$_SESSION['PARTIDASCOMPTC']=$partidas;
	$insertGoTo="tye_nueva_comprobacion_tc.php?idempleado=".$_POST['idempleado'];
	header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tyeconn, $tyeconn);
$query_rs_parametros = "SELECT * FROM fwk_parametro";
$rs_parametros = mysql_query($query_rs_parametros, $tyeconn) or die(mysql_error());
$row_rs_parametros = mysql_fetch_assoc($rs_parametros);
$totalRows_rs_parametros = mysql_num_rows($rs_parametros);
$TYE_parametros=array();
do{
	$clavePars=$row_rs_parametros['clave'];
	$TYE_parametros[$clavePars]=$row_rs_parametros['valor'];
}while($row_rs_parametros = mysql_fetch_assoc($rs_parametros));

$colname_subtipo = "-1";
if (isset($_GET['subtipo'])) {
  $colname_subtipo = (get_magic_quotes_gpc()) ? $_GET['subtipo'] : addslashes($_GET['subtipo']);
}

$colname_conceptos = $TYE_TIPO_GASTO_REEMBOLSO;
mysql_select_db($database_tyeconn, $tyeconn);
$query_conceptos = sprintf("SELECT * FROM cat_gastos WHERE tipo = '%s' and subtipo ='%s' and estatus=0 ORDER BY nombre ASC", $colname_conceptos, $colname_subtipo);
$query_conceptos2 = sprintf("SELECT * FROM cat_gastos WHERE tipo = '%s' and estatus=0 ORDER BY nombre ASC", $colname_conceptos);

$conceptos = mysql_query($query_conceptos, $tyeconn) or die(mysql_error());
$conceptos2 = mysql_query($query_conceptos2, $tyeconn) or die(mysql_error());

$row_conceptos = mysql_fetch_assoc($conceptos);
$row_conceptos2 = mysql_fetch_assoc($conceptos2);

$totalRows_conceptos = mysql_num_rows($conceptos);
$totalRows_conceptos2 = mysql_num_rows($conceptos2);
$conceptosArray=array();
do{
	$conceptosArray[$row_conceptos2['idcat_gastos']]=$row_conceptos2['nombre'];
}while($row_conceptos2 = mysql_fetch_assoc($conceptos2));
if($totalRows_conceptos>0){
mysql_data_seek($conceptos,0);
$row_conceptos = mysql_fetch_assoc($conceptos);}

$idempleado=base64_decode($_SESSION['IDEMPLEADO']);
mysql_select_db($database_tyeconn, $tyeconn);
$query_empleado = sprintf("SELECT idunidadnegocio, numempleado, idempleado, notarjetacredito FROM empleado WHERE idempleado = %s", $idempleado);
$empleado = mysql_query($query_empleado, $tyeconn) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
$noTarjeta=buscaTarjetaAmex($idempleado);


$colname_cargostc = $noTarjeta;
$fechalimite=fnc_date_calc(date("Y-m-d"),$TYE_parametros['MaximoDiasComprobacionTC']*-1);
mysql_select_db($database_tyeconn, $tyeconn);
$query_cargostc = sprintf("SELECT amex.idamex, amex.notransaccion,amex.tarjeta,
date_format(amex.fecha,'%%d/%%m/%%Y') as fecha,
amex.monto, amex.estatus, amex.moneda, amex.TC,
amex.concepto, gastosamex.idgastosamex, sum(gastosamex.estatus) as sumaestatus
FROM amex left join gastosamex on (gastosamex.idamex=amex.idamex)
WHERE amex.tarjeta = '%s' and amex.estatus=0 and amex.fecha>='%s'
group by amex.idamex
ORDER BY amex.fecha ASC", $colname_cargostc,$fechalimite);

$cargostc = mysql_query($query_cargostc, $tyeconn) or die(mysql_error());
$row_cargostc = mysql_fetch_assoc($cargostc);
$totalRows_cargostc = mysql_num_rows($cargostc);

mysql_select_db($database_tyeconn, $tyeconn);
$query_totalamex = sprintf("SELECT sum(amex.monto) as totalamex FROM amex  left join gastosamex on (gastosamex.idamex=amex.idamex) WHERE amex.tarjeta = '%s' and amex.estatus=0 and amex.fecha>='%s' and (gastosamex.estatus IS NULL or gastosamex.estatus=0) GROUP BY tarjeta", $colname_cargostc,$fechalimite);

$totalamex = mysql_query($query_totalamex, $tyeconn) or die(mysql_error());
$row_totalamex = mysql_fetch_assoc($totalamex);
$totalRows_totalamex = mysql_num_rows($totalamex);
$totalAmexMonto=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="es-MX"><head>
<!-- base href="http:://www.masnegocio.com" -->
<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1"><title><?php echo $APP_TITULO?></title>
<!--Incluir librería del Calendario -->
<script src="lib/js/popcalendar.js"></script>
<script src="lib/js/global.js"></script>
<link type="text/css" rel="stylesheet" href="css/estilo.css">
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}

function calculaiva(){
	
	var ivaval=document.form1.monto.value*0.15;
	document.form1.iva.value=ivaval.toFixed(2);
}


function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function checa(){
	if(document.form1.concepto.value!=-1){
		if((document.form1.concepto.value==13 || document.form1.concepto.value==37) && document.form1.comentario.value== "" ){
			  alert("Para el concepto de Comidas con Clientes debe ingresar el nombre del cliente en los comentarios.");
	 		 return false;
		}
		else if((document.form1.concepto.value==38 || document.form1.concepto.value==14 || document.form1.concepto.value==22)&& document.form1.comentario.value=="")
	 	{
			alert( "Para el concepto de no deducibles se debe justificar el gasto.");
			 return false;
	    }
			else if(document.form1.moneda.value!='MXP' && document.form1.tipocambio.value==1 )
	 {alert( "El tipo de cambio no puede ser igual a 1 en monedas extranjeras.");
	 return false;
	 }
	else if(document.form1.moneda.value!='MXP' && document.form1.tipocambio.value <8 )
	 {alert( "El tipo de cambio no puede ser menor a 8 en monedas extranjeras.");
	 return false;
	 }
	 	else{
	  	return true;
		}
	}
	else {
		alert("Seleccione un tipo de gasto y asigne el monto en la respectiva casilla.");
		return false;
	}

}
function checafecha(strFecha, diasmax){
		var toks=strFecha.split("/");
		strFechaN=toks[2]+"/"+toks[1]+"/"+toks[0];
		var fecha = new Date();
		fecha.setTime(Date.parse(strFechaN));
		var today=new Date();
    	var agedays = Math.floor((today - fecha) / 24 / 60 / 60 / 1000);
		if(agedays>diasmax){
			alert("Tu comprobante excede el plazo permitido");
			var hoystr=<?php echo "\"".date("d/m/Y")."\";";?>
			document.form1.fecha.value=hoystr;
			return false;
		}else{
			return true;
		}

}

function calculaiva(){
	document.form1.iva.value=document.form1.monto.value*(document.form1.tasaiva.value/100);
}
//-->
</script>

<meta http-equiv="Pragma" content="no-cache">
</head>

<body topmargin="6" onLoad="MM_preloadImages('images/header_bot_7.gif')" bgcolor="#e4e4e4">
<div align="center">
  <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="900">
    <tbody><tr>
      <td colspan="2"><table width="750" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="400" valign="bottom"><img src="images/header_interiores2.png" border="0" height="80" width="400"></td>
          <td width="175" rowspan="3" valign="bottom"><img src="images/brandingtye.jpg" width="93" height="115"></td>
          <td width="175" valign="top"><div align="right"><img src="images/<?php if(base64_decode($_SESSION['IDUNIDADNEGOCIO'])==$TYE_UNIDADNEGOCIO_DANONE){?>danone-peq.png<?php } else {?>bonafont.jpg<?php } ?>" alt="Grupo Danone"></div></td>
        </tr>
        <tr>
          <td rowspan="2" valign="top"><img src="images/ctrlgastos.png" width="400" height="35"></td>
          <td width="175" valign="bottom"><a href="index.php?logout=true" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('bot_redNeg','','images/header_bot_7.gif',1)"><img src="images/header_bot_8.gif" name="bot_redNeg" border="0" height="20" width="133"></a></td>
        </tr>
        <tr>
          <td valign="bottom"><div align="right"><span class="version"><?php echo base64_decode($_SESSION['FWK_USERNAME']); ?></span></div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="753" valign="top" width="180"><table width="180" border="0" cellpadding="0" cellspacing="0" class="backmenu">
        <tbody>
          <tr>
            <td valign="top" bgcolor="#CAD4D4"><table border="0" cellpadding="0" cellspacing="0" width="180">
                <tbody>
                  <tr>
                    <td class="txtUser"><?php include("menus/menupal.php"); ?></td>
                  </tr>
                </tbody>
            </table>
                <br></td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><table align="left" border="0" cellpadding="0" cellspacing="0" width="180">
              <tbody>
                <tr>
                  <td height="8"><img src="images/tit_int_ligasRel.gif"></td>
                </tr>
                <tr>
                  <td class="txtDGreyAr9" bgcolor="#f5f5f5"><a href="help/manual_usuario.php" target="_self" class="txtMenu">Manual de usuario</a></td>
                </tr>
                <tr>
                  <td bgcolor="#f5f5f5" height="4"><img src="images/spacer.gif" height="1" width="1"></td>
                </tr>
                <tr>
                  <td class="txtDGreyAr9" bgcolor="#f5f5f5"><a href="mailto:soporte@masnegocio.com" class="txtMenu"></a></td>
                </tr>
                <tr>
                  <td bgcolor="#f5f5f5" height="4"><img src="images/spacer.gif" height="1" width="1"></td>
                </tr>
                <tr>
                  <td valign="bottom"><img src="images/puntos.gif" alt="" border="0" width="180"></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </tbody>
      </table>
      </td>
      <td valign="top" width="570">
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" bgcolor="#E5EAEA"><span class="titulo1"><img src="images/bullet_tit_interiores.gif">&nbsp; Principal &nbsp;<img src="images/bullet_tit_interiores.gif">&nbsp; Comprobaciones &nbsp;<img src="images/bullet_tit_interiores.gif">&nbsp; Nueva comprobaci&oacute;n</span> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="txtBluVer13B">Tarjeta de cr&eacute;dito : <?php echo $row_empleado['notarjetacredito']; ?></td>
        </tr>
        <tr>
          <td class="txtBluVer13B">&nbsp;</td>
        </tr>
        <tr>
          <td class="txtBluVer13B"><?php if(isset($_GET['successmsg'])){ echo displayMessage(base64_decode($_GET['successmsg']),$FWK_MSG_SUCCESS);} ?>
            <?php if(isset($_GET['errormsg'])){ echo displayMessage(base64_decode($_GET['errormsg']),$FWK_MSG_ERROR);} ?></td>
        </tr>
        <tr>
          <td>  <form name="form1" method="post" action="tye_nueva_comprobacion_tc.php">
                  <table width="100%" border="0" cellspacing="0" cellpadding="2">
				  <tr>
    <td valign="top"><div align="right" class="formlabel">Cargos: </div></td>
    <td colspan="9" valign="top"><select name="cargos" id="cargos">
      <option value="0">Seleccione</option>
      <?php
      $totalAmexMonto=0;
do {  if($row_cargostc['sumaestatus']==0) {

		$totalAmexMonto+=($row_cargostc['monto']*$row_cargostc['TC']);
?>
      <option <?php if(isset($_GET['cargo']) && $_GET['cargo']==$row_cargostc['idamex'] ){echo "selected=true";}?> value="<?php echo $row_cargostc['idamex']?>"><?php echo $row_cargostc['notransaccion']?>: <?php echo $row_cargostc['fecha']?> (<?php echo $row_cargostc['concepto']?>)  $<?php echo $row_cargostc['monto']?> <?php echo $row_cargostc['moneda']?></option>
      <?php
} } while ($row_cargostc = mysql_fetch_assoc($cargostc));
  $rows = mysql_num_rows($cargostc);
  if($rows > 0) {
      mysql_data_seek($cargostc, 0);
	  $row_cargostc = mysql_fetch_assoc($cargostc);
  }
?>
    </select>
      <label>
      <div align="left"></div>      </label></td>
    </tr>
                    <tr>
                      <td width="10%" nowrap class="formlabel">Tipo de gasto: </td>
                      <td colspan="2"><select name="select" onChange="MM_goToURL('parent','tye_nueva_comprobacion_tc.php?idempleado=<?php echo $_GET['idempleado']?>&subtipo='+this.value+'&cargo='+document.form1.cargos.value);return document.MM_returnValue">
                        <option value="-1" <?php if(!isset($_GET['subtipo']) || $_GET['subtipo']==-1)echo "selected=\"selected\""; ?>>Seleccione</option>
					    <option value="1" <?php if(isset($_GET['subtipo']) && $_GET['subtipo']==1)echo "selected=\"selected\""; ?>>Vi&aacute;ticos</option>
                        <option value="2" <?php if(isset($_GET['subtipo']) && $_GET['subtipo']==2)echo "selected=\"selected\""; ?>>Micel&aacute;neos o menores</option>
                      </select>&nbsp;</td>
                      <td width="16%">&nbsp;</td>
                      <td width="10%"><div align="right"><span class="formlabel"> Gasto:</span></div></td>
                      <td colspan="4" nowrap><span class="formlabel">
                        <select name="concepto" id="concepto">
                          <option value="-1">Seleccione</option>

                          <?php
do {
?>
                          <option value="<?php echo $row_conceptos['idcat_gastos']?>"><?php echo $row_conceptos['nombre']?></option>
                          <?php
} while ($row_conceptos = mysql_fetch_assoc($conceptos));
  $rows = mysql_num_rows($conceptos);
  if($rows > 0) {
      mysql_data_seek($conceptos, 0);
	  $row_conceptos = mysql_fetch_assoc($conceptos);
  }
?>
                        </select>
                      </span></td>
                      <td width="8%"></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td width="12%">&nbsp;</td>
                      <td width="9%" nowrap class="formlabel">&nbsp;</td>
                      <td nowrap class="formlabel">&nbsp;</td>
                      <td nowrap class="formlabel">&nbsp;</td>
                      <td width="10%" nowrap>&nbsp;</td>
                      <td width="17%">&nbsp;</td>
                      <td width="17%">&nbsp;</td>
                      <td width="8%" nowrap>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><span class="formlabel">Monto</span></td>
                      <td><label><span class="formlabel">
                        <input name="monto" type="text" id="monto" onBlur="MM_callJS('calculaiva();')" size="9">
                      </span></label></td>
                      <td nowrap class="formlabel"><div align="right"><strong>Moneda</strong><strong>:</strong></div></td>
                      <td nowrap class="formlabel"><select name="moneda" id="moneda">
                        <option value="MXP" selected>MXP</option>
                        <option value="USD">USD</option>
                      </select>
                        <input name="idempleado" type="hidden" id="idempleado" value="<?php echo $_GET['idempleado']; ?>"></td>
                      <td nowrap class="formlabel"><div align="right">Tasa de C: </div></td>
                      <td nowrap><label>
                        <input name="tipocambio" type="text" id="tipocambio" value="1.0" size="9">
                      </label></td>
                      <td><div align="right" class="formlabel">IVA:</div></td>
                      <td><label>
                        <select name="tasaiva" id="tasaiva" onChange="MM_callJS('calculaiva();')">
                          <option value="16">16%</option>
						  <option selected="selected" value="15">15%</option>
						  <option value="11">11%</option>
                          <option value="10">10%</option>
                          <option value="0">0%</option>
                        </select>
                      </label></td>
                      <td nowrap><input name="iva" type="text" id="iva" size="9" readonly="true"></td>
                      <td><input name="agregar" type="submit" id="agregar"  onClick="return checa()"  value="Agregar"></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td nowrap class="formlabel">&nbsp;</td>
                      <td nowrap class="formlabel">&nbsp;</td>
                      <td nowrap class="formlabel">&nbsp;</td>
                      <td nowrap>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td nowrap>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="formlabel"><div align="right">Comentario:</div></td>
                      <td colspan="5"><input name="comentario" type="text" id="comentario" size="75" maxlength="254"></td>
					  <td ><div align="right" class="formlabel">Fecha del comprobante:</div></td>
					  <td nowrap ><input name="fecha" type="text" id="fecha" size="15" onFocus="return checafecha(document.form1.fecha.value, 60);"    value="<?php  echo date("d/m/Y") ; ?>" readonly="true">
				      &nbsp;<a href="#" onClick="popUpCalendar(document.form1.fecha,document.form1.fecha, 'dd/mm/yyyy');"><img src="images/b_calendar.png" alt="calendario" width="16" height="16" border="0" align="absmiddle"></a></td>
					  <td colspan="2" nowrap><label></label></td>
                    </tr>
                  </table>
                  <label></label>
                </form>     <SCRIPT language="JavaScript">
						 var frmvalidator  = new Validator("form1");
						 frmvalidator.addValidation("monto","dec");
						 frmvalidator.addValidation("tipocambio","dec");
						 frmvalidator.addValidation("iva","dec");

					</script>   </td>
        </tr>
        <tr>
          <td class="txtGreenBVer11"><div align="right">Recuerde usar la tasa de cambio en la que compr&oacute; la divisa</div></td>
        </tr>
		<tr>
		  <td class="titulo1"><div align="right">
		    <form name="form2" method="post" action="tye_nueva_comprobacion_tc.php">
		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="2" cellpadding="2">
                    <tr>
                      <td class="tablehead">op</td>
                      <td class="tablehead">No.</td>
                      <td class="tablehead">Cargo</td>
                      <td class="tablehead">Gasto</td>
                      <td class="tablehead">Monto</td>
                      <td class="tablehead">IVA</td>
                      <td class="tablehead">Moneda</td>
                      <td class="tablehead">Tasa</td>
					  <td class="tablehead">Monto mxp</td>
				<td class="tablehead">IVA mxp</td>
                      <td class="tablehead">Fecha</td>
                    </tr>
                    <?php
					      $totalGastos=0;
						  $totalGastosIVA=0;
					for($row=0; $row<count($partidas); $row++){
					      $partrow=$partidas[$row];
						  $totalGastos+=($partrow['monto']*$partrow['tipocambio']);
						  $totalGastosIVA+=($partrow['iva']*$partrow['tipocambio']);
				    ?>
                    <tr class="backDark">
                      <td><a href="tye_nueva_comprobacion_tc.php?idempleado=<?php echo $_GET['idempleado']; ?>&del=<?php echo $row; ?>"><img src="images/fwk/edit-delete.png" alt="borrar" border="0"></td>
                      <td><?php echo $row+1; ?></td>
                      <td><?php echo $partrow['cargo']; ?></td>
                      <td><?php echo $conceptosArray[$partrow['concepto']]; ?> <?php if(!empty($partrow['comentario'])) {echo "(".$partrow['comentario'].")";} ?></td>
                      <td><?php echo number_format($partrow['monto'],2); ?></td>
                      <td><?php echo number_format($partrow['iva'],2); ?></td>
                      <td><?php echo $partrow['moneda']; ?></td>
                      <td><?php echo number_format($partrow['tipocambio'],2); ?></td>
					  <td><?php echo number_format($partrow['monto']*$partrow['tipocambio'],2); ?></td>
				      <td><?php echo number_format($partrow['iva']*$partrow['tipocambio'],2); ?></td>
                      <td><?php echo $partrow['fecha']; ?></td>
                    </tr>
                    <?php } ?>
                  </table></td>
                </tr>
                <tr>
                  <td><table width="100%" border="0" cellspacing="2" cellpadding="2">
                    <tr>
                      <td width="20%"><div align="right">Total Cargos: </div></td>
                      <td width="70%">$-<?php echo number_format($totalAmexMonto,2);?> <strong>MXP</strong> (Pendientes de comprobar desde <?php echo $fechalimite; ?> a la fecha.) </td>
                      <td width="10%">&nbsp;</td>
                    </tr>
                    <tr>
                      <td><div align="right">Total Gastos: </div></td>
                      <td>$<?php echo number_format($totalGastos,2); ?><strong>MXP</strong></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><div align="right">+IVA:</div></td>
                      <td>$<?php echo number_format($totalGastosIVA,2); ?><strong>MXP </strong></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><div align="right">Saldo:</div></td>
                      <td>$<?php echo number_format($totalGastos+$totalGastosIVA-$totalAmexMonto,2); ?><strong>MXP</strong></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><div align="right">Observaciones:</div></td>
                      <td><label>
                        <textarea name="observaciones" cols="50" rows="5" id="observaciones"></textarea>
                      </label></td>
                      <td>&nbsp;</td>
                    </tr>
                    <?php if($row_empleado['idunidadnegocio']==$TYE_UNIDADNEGOCIO_DANONE || isAuthorized($FWK_PRIV_CONFIGURACION) ){?>
							<tr>
                              <td><div align="right">Refacturar:</div></td>

								<td><select name="refacturar" id="refacturar">
                          <option value="0" selected>No refacturar</option>
                          <option value="4">Bonafont Themis</option>
						  <option value="5">Bonafont Training</option>
						  <option value="6">Bonafont IT</option>
						  <option value="7">Bonafont Compensaciones</option>
                          <option value="1">Colombia</option>
                          <option value="2">Guatemala</option>
                          <option value="3">El Salvador</option>
                        </select>                             </td>
                              <td>&nbsp;</td>
                            </tr>
							<?php }?>
                    <tr>
                      <td>&nbsp;</td>
                      <td><label>
                        <input name="limpiar" type="submit" id="limpiar" value="Limpiar">
                        <input name="guardar" type="submit" id="guardar" onClick="return confirm('¿está seguro que desea guardar esta comprobación?');" value="Guardar">
                        <input name="idempleado" type="hidden" id="idempleado" value="<?php echo $_GET['idempleado']; ?>">
						<input name="idamex" type="hidden" id="idamex" value="<?php echo $part["idamex"]; ?>">
                      </label></td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
              </table>
		      </form>
		    </div></td>
		    </tr>
		<tr>
		  <td>&nbsp;</td>
		  </tr>
		<tr>
		  <td class="formlabel">&nbsp;</td>
		  </tr>
		<tr>
		<td><span class="formlabel"></span></td>
        </tr>
      </table>
	  <p>&nbsp;</p>	  </td>
    </tr>


<tr>

      <td colspan="2" height="50">Portal Administrador por <a href="http://www.masnegocio.com">MasNegocio.com</a>
        <div align="center"><font color="#336699" face="Verdana, Arial, Helvetica, sans-serif" size="1">



          </font></div>
	    </td></tr>

<tr bgcolor="#e4e4e4">
      <td colspan="2" class="txtGreyVer9" bgcolor="#e4e4e4" height="50"><div align="center"> </div></td>
    </tr>
	<form name="sFriend" action="?action=sendfriend&amp;sectionID=7&amp;catID=105" method="GET"></form>

  </tbody></table>
  <p>&nbsp;</p>
</div>
</body></html>
<?php


mysql_free_result($conceptos);

mysql_free_result($cargostc);
?>
