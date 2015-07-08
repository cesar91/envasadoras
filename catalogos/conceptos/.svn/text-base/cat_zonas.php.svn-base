<?php
//require_once "$RUTA_A/Connections/fwk_db.php";

$cnn= new Conexion();
$aux= array();

$query ="select * from cat_regiones";
$rst=$cnn->consultar($query);

while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["re_id"],"nombre"=>$fila["re_nombre"]));
}

?>
<center>
	<h1>Cat&aacute;logo Zonas</h1>
	<br />
	<br />
<form name="addConcepto" id="addConcepto" action="index.php?catalogo=cz" method="post">
	<div align="left" style="width:70%" >Ver zonas de:
				<select id="view_zones" name="view_zones">
					<?php
						foreach($aux as $zonas){
					?>
					<option value="<?php echo $zonas["id"];?>"><?php echo $zonas["nombre"];?></option>
					<?php 
					}?>
				</select>
	</div>
<?php
$paginacion="";

if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!=""){
	$paginacion="ltotal=".$_GET["ltotal"]."&lactual=".$_GET["lactual"]."&";
	
}

if(isset($_GET["Eid"])){
	
	$cat_id=$_GET["Eid"];
	
	$cat = new CatalogsZone();	
	$cat -> Load_Catalogs($cat_id);
	
} ?>

	<div id="zones" style="width:70%;">
		<?php
			$idZone=1;
			if(isset($_GET['view_zones']) && $_GET['view_zones']!=""){
				$idZone=$_GET['view_zones'];
				$paginacion.="view_zones=".$idZone."&";
			}
			
			
			$L	= new Lista("catalogo=cz&view_zones=$idZone");
			$L->Cabeceras("Folio");
			$L->Cabeceras("Nombre");
			$L->Herramientas("E","./index.php?".$paginacion."catalogo=cz&Eid=");
			$query="select reco_id, reco_nombre from cat_regiones_conceptos where reco_activo=1 and reco_pertenece_region=".$idZone." order by reco_id desc";
			$L->muestra_lista($query,0);
		?>
	</div>
	<br />
	<br />
	<table style="background:#F8F8F8">
		<tr>
			<td>Nombre<span class="style1">*</span>:</td>
			<td><input type="text" name="name_zones" id="name_zones"/></td>
			<td align="right">Regi&oacute;n<span class="style1">*</span>:</td>
			<td>
				<select id="select_zones" name="select_zones">
				<?php foreach($aux as $zonas){ ?>
					<option value="<?php echo $zonas["id"];?>"><?php echo $zonas["nombre"];?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" align="center">
			<input type="reset" id="cancel" name="cancel" style="display:block; background:url(../../images/delete.gif); background-position:left; background-repeat:no-repeat" value="     Limpiar" />
		</td>
	</tr>
	</table>
	<br />
	<br />
	<br />
	<input type="hidden" name="ir" id="ir" value="cz" />
	<input type="hidden" name="idCat" id="idCat" value=""/>
	<input type="hidden" name="lTot" id="lTot" value=""/>
	<input type="hidden" name="lAc" id="lAc" value=""/>
</form>
</center>
<center>

<?php
if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!=""){	
	?>
	<script language="javascript">
		$("#lTot").val("<?php  echo $_GET["ltotal"];?>");
		$("#lAc").val("<?php  echo $_GET["lactual"];?>");
	</script>
	<?php
}

if(isset($_GET["Eid"])){		
?>
	<script language="javascript">		
		$("#name_zones").val("<?php  echo utf8_decode( $cat->Get_dato("reco_nombre"));?>");		
		$("#select_zones").val(<?php echo $cat->Get_dato("reco_pertenece_region");?>);
		$("#idCat").val("<?php  echo $cat_id;?>");					
		$("#registrar").val("    Actualizar");
	</script>
<?php } ?>

			<script language="javascript">
				$("#view_zones").val(<?php echo $idZone;?>);
			</script>				
	</div>
</center>