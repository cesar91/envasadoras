<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
$cnn= new Conexion();
$aux= array();

$query ="select * from cat_hoteles group by hotel_ciudad";
$rst=$cnn->consultar($query);

while($fila=mysql_fetch_assoc($rst)){
			array_push($aux,array("id"=>$fila["hotel_ciudad"]));
}

?>

<script language="javascript">
	function activeCd(){
		$("#name_cd").attr("disabled", "disable"); 
		$("#select_cd").removeAttr("disabled"); 
		$("#name_cd").val("");
	}
	
	function activeCd2(){
		$("#select_cd").attr("disabled", "disable"); 
		$("#name_cd").removeAttr("disabled");		
	}
</script>

<center>
	<h1>Cat&aacute;logo Hoteles</h1>
<br />
<br />
<form name="addHotel" id="addHotel" action="index.php?catalogo=hotel" method="post">
	<div align="left" style="width:70%" >Ver Hoteles de:
				<select id="view_hoteles" name="view_hoteles">
					<?php
						$i=0;
						$defaultCd="";
						foreach($aux as $cd){
							if($i==0)
								$defaultCd=$cd["id"];
					?>
					<option value="<?php echo utf8_decode($cd["id"]);?>"><?php echo strtr(utf8_decode(strtoupper($cd["id"])),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");?></option>
					<?php
					$i++;
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
	$cat = new CatalogsHotel();	
	$cat -> Load_Catalogs($cat_id);
} ?>

	<div id="cds" style="width:70%;">
		<?php
			$i=0;
			$defaultCd="";
			foreach($aux as $cd)
				if($i==0)
					$defaultCd=$cd["id"];
					
			$idCd=$defaultCd;
			
			if(isset($_GET['view_hoteles']) && $_GET['view_hoteles']!=""){
				$idCd=$_GET['view_hoteles'];
				$paginacion.="view_hoteles=".$idCd."&";
			}
			
				$L	= new Lista("catalogo=hotel&view_hoteles=$idCd");
				$L->Cabeceras("Folio");
				$L->Cabeceras("Hotel");
				$L->Cabeceras("Precio","","number","R");
				$L->Herramientas("E","./index.php?".$paginacion."catalogo=hotel&Eid=");
				$L->Herramientas("D","./index.php?".$paginacion."catalogo=hotel&delete=true&id=");
				$query="select hotel_id, hotel_nombre, hotel_costo from cat_hoteles where hotel_ciudad='".str_replace("-"," ",$idCd)."' and hotel_activo=1";
				$L->muestra_lista($query,0);
		?>
	</div>
	<br />
	<br />
	<br />
	<table style="background:#F8F8F8">
		<tr>
			<td>Hotel<span class="style1">*</span>:
			</td>
			
			<td> 
				<input type="text" name="name_hotel" id="name_hotel"/>
			</td>
			<td align="right">
				Costo<span class="style1">*</span>:
			</td>
			<td>
				<input type="text" name="costo" id="costo" onkeypress="return validaNum(event)" />
			</td>
		</tr>
		<tr>
			<td colspan="4">Ciudades:</td>
		</tr>
		<tr>
			<td align="right">
				<input type="radio" name="radioCd" id="radioCd" checked="checked" onclick="activeCd();"/>
				Existentes
				<span class="style1">*</span>:
			</td>					
			<td>
				<select id="select_cd" name="select_cd">
					<?php
						foreach($aux as $cd){
					?>
					<option value="<?php echo $cd["id"];?>"><?php echo strtr(utf8_decode(strtoupper($cd["id"])),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");?></option>
					<?php 
					}?>
				</select>
			</td>
			<td>
				<input type="radio" name="radioCd" id="radioCd" onclick="activeCd2();"/>
				Nueva:<span class="style1">*</span>:
			</td>			
			<td> 
				<input type="text" name="name_cd" id="name_cd" disabled="disabled" onkeyup="this.value = this.value.toLowerCase();"/>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
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
	</table>
	<br />
	<br />
	<br />
				
	<input type="hidden" name="ir" id="ir" value="hotel" />
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
		$("#name_hotel").val("<?php echo utf8_decode( $cat->Get_dato("hotel_nombre"));?>");		
		$("#select_cd").val('<?php echo $_GET['view_hoteles'];?>');
		$("#costo").val(<?php echo $cat->Get_dato("hotel_costo");?>);
		$("#idCat").val("<?php  echo $cat_id;?>");
		$("#registrar").val("    Actualizar");
	</script>
<?php } ?>

	<div id="cds" style="width:70%;">
			<script language="javascript">
				$("#view_hoteles").val('<?php echo utf8_decode(str_replace("-"," ",$idCd));?>');
			</script>					
	</div>
</center>