<?php
?>
<center>
	<h1>Cat&aacute;logo General</h1>
<?php
$paginacion="";

if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!=""){
	$paginacion="ltotal=".$_GET["ltotal"]."&lactual=".$_GET["lactual"]."&";	
}

if(isset($_GET["Eid"])){
	
	$cat_id=$_GET["Eid"];
	
	$cat = new CatalogsGral();	
	$cat -> Load_Catalogs($cat_id);
?>
	
<?php
}
			$L	= new Lista("catalogo=cg");
			$L->Cabeceras("Folio");
			$L->Cabeceras("Nombre");
			$L->Cabeceras("Clasificaci&oacute;n");
			$L->Cabeceras("Cuenta");			
			$L->Herramientas("E","./index.php?".$paginacion."parametro&Eid=");
			//$L->Herramientas("D","./index.php?".$paginacion."catalogo=cg&delete=true&id=");
			
			$query="select dc_id, cp_concepto, cp_clasificacion, cp_cuenta from cat_conceptos where cp_activo=1  and dc_catalogo=1 order by dc_id desc";
			$L->muestra_lista($query,0);
?>
<br />
<br />
<form name="addConcepto" id="addConcepto" action="index.php?catalogo=cg" method="post">
	<!--<table style="background:#F8F8F8">
		<tr>
			<td>Nombre<span class="style1">*</span>:
			</td>
			
			<td> <input type="text" name="name_concepto" id="name_concepto" />
			</td>
			
			<td>Clasificaci&oacute;n<span class="style1">*</span>:
			</td>
			
			<td>
				<select name="clasificacion" id="clasificacion">
					<option value="Deducible">Deducible</option>
					<option value="No deducible">No deducible</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Cuenta<span class="style1">*</span>:
			</td>
			
			<td><input type="text" name="cuenta" id="cuenta"  />
			</td>
			
			<td>&nbsp;
			</td>
			
			<td>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;
			</td>
		</tr>
	
					
		<tr>
			<td colspan="2">
			</td>
			<td align="right">
				<input type="submit" name="registrar" id="registrar" value="     Agregar" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat"/>
			</td>
			<td align="right">
				<input type="submit" id="cancel" name="cancel" style="display:block; background:url(../../images/action_cancel.gif); background-position:left; background-repeat:no-repeat" value="    Cancelar" onclick="clearGral();" />
			</td>
		</tr>
	</table>-->
	<input type="hidden" name="ir" id="ir" value="cg" />
	<input type="hidden" name="idCat" id="idCat" value=""/>
	<input type="hidden" name="lTot" id="lTot" value=""/>
	<input type="hidden" name="lAc" id="lAc" value=""/>
</form>
</center>	
<br />
<br />

<?php
if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!=""){
	
	?>
	<script language="javascript">
		$("#lTot").val("<?php  echo $_GET["ltotal"];?>");
		$("#lAc").val("<?php  echo $_GET["lactual"];?>");
	</script>
	<?php
}
?>
<script language="javascript">		
		$("#name_concepto").val("<?php  echo $cat->Get_dato("cp_concepto");?>");
		$("#cuenta").val("<?php  echo $cat->Get_dato("cp_cuenta");?>");
		$("#idCat").val("<?php  echo $cat_id;?>");

		<?php 
			if($cat->Get_dato("cp_clasificacion")=='NO DEDUCIBLES'){
		?>
				$('#clasificacion').find('option:second').attr('selected', 'selected').parent('select');	
		<?php }	
		?>
		
		$("#registrar").val("    Actualizar");
</script>