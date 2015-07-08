<?php
?>
<br />
<br />
<center>
<h1>Edici&oacute;n de Par&aacute;metros</h1>
<form name="addConcepto" id="addConcepto" action="index.php?catalogo=cg" method="post">
	<table style="background:#F8F8F8">
		<tr>
			<td>Nombre<span class="style1">*</span>:
			</td>
			
			<td>				
				<input type="text" size=50  disabled name="name_parameter" id="name_parameter" readonly="readonly"/>
			</td>
			
			<td>Valor par&aacute;metro<span class="style1">*</span>:			</td>			
			<td>
				<input type="text" name="valor_parametro" id="valor_parametro" style="width:90px" onkeypress="return validaNum(event)"/>
			</td>
		</tr>
		<tr>
			<td>Regi&oacute;n<span class="style1">*</span>: </td>
			<td>				
				<input type="text" disabled size=40 name="name_region" id="name_region" readonly="readonly"/>
			</td>
			<td>Divisa<span class="style1">*</span>:			</td>			
			<td>
				<input type="text" name="divisa" id="divisa" style="width:90px" disabled="true" onkeypress="return validaNum(event)"/>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			
			<td>
			</td>
			
			<td>&nbsp;
			</td>
			
			<td>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
			<td align="right">
				<input type="submit" name="registrar" id="registrar" value="     Actualizar" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat"  onclick=Modifica_Parametro($cantidad, $id);/>
			</td>
			<td align="right">
				<input type="submit" id="cancel" name="cancel" style="display:block; background:url(../../images/action_cancel.gif); background-position:left; background-repeat:no-repeat" value="    Cancelar" onclick="clearGral();" />
			</td>
		</tr>
	</table>
	<input type="hidden" name="ir" id="ir" value="index" />
	<input type="hidden" name="idCat" id="idCat" value=""/>
	<input type="hidden" name="lTot" id="lTot" value=""/>
	<input type="hidden" name="lAc" id="lAc" value=""/>
</form>
</center>	
<br />
<br />
<?php
$paginacion="";

if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!=""){
	$paginacion="ltotal=".$_GET["ltotal"]."&lactual=".$_GET["lactual"]."&";
	?>
	<script language="javascript">
		$("#lTot").val("<?php  echo $_GET["ltotal"];?>");
		$("#lAc").val("<?php  echo $_GET["lactual"];?>");
	</script>
	<?php
}

if(isset($_GET["Eid"])){	
	//echo $Eid;
	$cat_id=$_GET["Eid"];
	
	$cat = new Parametro();	
	$cat -> Load($cat_id);
	
?>
<?php
//Query para pasar datos a cajas de texto del parámetro seleccionado en la tabla
$query="SELECT p.p_nombre, r.re_nombre, d.div_nombre  FROM parametro_regionbmw pr INNER JOIN parametrosbmw p ON pr.p_id = p.p_id LEFT JOIN cat_regionesbmw r ON pr.re_id= r.re_id LEFT JOIN divisa d ON pr.div_id= d.div_id where pr_id=". $cat_id;
$rst=mysql_query($query);
$arr=mysql_fetch_assoc($rst);
$nombrepw=$arr['p_nombre'];
$regionpw=$arr['re_nombre'];
$divisapw=$arr['div_nombre'];
?>

	<script language="javascript">	
		
		$("#name_parameter").val("<?php echo $nombrepw;?>");
		$("#valor_parametro").val("<?php  echo $cat->Get_dato("pr_cantidad");?>");
		$("#name_region").val("<?php  echo $regionpw;?>");
		$("#divisa").val("<?php  echo $divisapw;?>");
		$("#idCat").val("<?php  echo $cat_id;?>");	 


	</script>
<?php
}
			$L	= new Lista("catalogo=cg");
			$L->Cabeceras("Folio");
			$L->Cabeceras("Nombre");
			$L->Cabeceras("Regi&oacute;n");
			$L->Cabeceras("Valor par&aacute;metro");	
			$L->Cabeceras("Divisa");	
			$L->Herramientas("E","./index.php?".$paginacion."catalogo=cg&Eid=");			
			
			$query="SELECT pr.pr_id, p.p_nombre, r.re_nombre, FORMAT(round(pr.pr_cantidad),2), d.div_nombre
            FROM parametro_regionbmw pr INNER JOIN parametrosbmw p ON pr.p_id = p.p_id LEFT JOIN cat_regionesbmw r ON pr.re_id= r.re_id LEFT JOIN divisa d ON pr.div_id= d.div_id;";
			$L->muestra_lista($query,0);
			





