<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

/*
 *  Esta pagina implementa el catalogo de empresas de Expenses. Se
 * tienen los siguientes modos:
 * 
 *      INDEX  - Muestra la lista de empresas sin filtrar
 *      NUEVO  - Muestra la pantalla de alta de nuevo empresa
 *      EDITAR - Muestra la pantalla de edicion de empresa, espera el sig. parametro:
 *                  empresa_id: ID del empresa a editar
 *      ELIMINAR - Muestra la pantalla de eliminacion de empresa, espera el sig. parametro:
 *                  empresa_id: ID del empresa a editar
 */
 //Elimina Concepto
if(isset($_GET['delete']) && $_GET['delete']==true && isset($_GET['id']) && $_GET['id']!="" && isset($_GET['catalogo']) && $_GET['catalogo']!=""){
	$idCat=$_GET['id'];
	
	$paginacion="";
	
	if(isset($_GET["ltotal"])&& isset($_GET["lactual"]) && $_GET["ltotal"]!="" && $_GET["lactual"]!="")		
			$paginacion="ltotal=".$_GET["ltotal"]."&lactual=".$_GET["lactual"]."&";
	
	if($_GET['catalogo']=="cg"){	
		$obCat=new CatalogsGral();
		$obCat -> Delete($idCat);
	}
	else if($_GET['catalogo']=="cz"){
		$obCat=new CatalogsZone();
		$obCat -> Delete($idCat);
		
		if(isset($_GET['view_zones']) && $_GET['view_zones'])
			$paginacion="view_zones=".$_GET['view_zones']."&";
	}
	else if($_GET['catalogo']=="hotel"){
		$obCat=new CatalogsHotel();
		$obCat -> Delete($idCat);
		
		if(isset($_GET['view_hoteles']) && $_GET['view_hoteles'])
			$paginacion="view_hoteles=".$_GET['view_hoteles']."&";
	}
		
	header('Location:./index.php?'.$paginacion.'catalogo='.$_GET['catalogo']);
}

//Inserta o actuliza en Catalogo Gral
else if(isset($_POST['name_concepto']) && isset($_POST['cuenta']) && isset($_POST['clasificacion']) && $_POST['name_concepto']!="" && $_POST['cuenta']!="" && $_POST['clasificacion']!="" && isset($_POST['idCat'])){

	$vb=trim($_POST["registrar"]);
	
	$concepto=$_POST['name_concepto'];
	$cuenta=$_POST['cuenta'];
	$clasificacion=$_POST['clasificacion'];
	$idCat=$_POST['idCat'];
	
	$paginacion="";		
	
	$obCat=new CatalogsGral();
	
	if($vb=="Agregar"){
		//file_put_contents("txt.txt",$concepto." ".$cuenta." ".$clasificacion." ".$idCat);
		$obCat->Add($concepto, $cuenta, $clasificacion);
	}
	else if($vb=="Actualizar" && $idCat!="")
		{
		
		if($_POST["lTot"]!=""&& $_POST["lAc"]!="")
			$paginacion="ltotal=".$_POST["lTot"]."&lactual=".$_POST["lAc"]."&";		
		//file_put_contents("pr.txt",$paginacion);
		//$paginacion="";
		$obCat->Modifica_Concepto($concepto,$cuenta, $clasificacion, $idCat);
	}
	header('Location:./index.php?'.$paginacion.'catalogo='.$_POST['ir']);
}

//Inserta o actuliza en Catalogo de Zonas
else if(isset($_POST['name_zones']) &&   $_POST['name_zones']!="" && isset($_POST['select_zones']) && $_POST['select_zones']!="" && isset($_POST['idCat'])){

	$idCat=$_POST['idCat'];
	$vb=trim($_POST["registrar"]);
	
	$nombre_zona=$_POST['name_zones'];
	$select_zona=$_POST['select_zones'];	

	$paginacion="";		
	
	$obCat=new CatalogsZone();

	if(isset($_POST['view_zones'])){
	
		$paginacion="view_zones=".$_POST['view_zones']."&";
		
	}
	
	
	if($vb=="Agregar"){
	
		$obCat->Add($nombre_zona, $select_zona);
	}
	
	else if($vb=="Actualizar" && $idCat!="")
		{
		
		if($_POST["lTot"]!=""&& $_POST["lAc"]!="")
			$paginacion.="ltotal=".$_POST["lTot"]."&lactual=".$_POST["lAc"]."&";		
		
		//$paginacion="";
		$obCat->Modifica_Concepto($nombre_zona,$select_zona, $idCat);
	}
	header('Location:./index.php?'.$paginacion.'catalogo='.$_POST['ir']);
}

//Inserta o actuliza en Catalogo de Hoteles
else if(isset($_POST['name_hotel']) &&   $_POST['name_hotel']!="" && isset($_POST['costo']) && $_POST['costo']!="" && ((isset($_POST['select_cd']) && $_POST['select_cd']!="") || (isset($_POST['name_cd']) && $_POST['name_cd']!=""))){

	
	
	$idCat=$_POST['idCat'];
	$vb=trim($_POST["registrar"]);
	
	$nombre_hotel=$_POST['name_hotel'];
	$select_cd=$_POST['select_cd'];
	$costo=$_POST['costo'];
	$cd=$_POST['name_cd'];
	
	$paginacion="";		
	
	$obCat=new CatalogsHotel();

	if(isset($_POST['view_hoteles'])){
	
		$paginacion="view_hoteles=".utf8_encode($_POST['view_hoteles'])."&";
		
	}
	
	if($vb=="Agregar"){
	
		if($select_cd!="")	
			$obCat->Add($nombre_hotel, $select_cd, $costo);
		
		else
			$obCat->Add($nombre_hotel, utf8_encode($cd), $costo);
	}
	
	else if($vb=="Actualizar" && $idCat!="")
		{
		
		if($_POST["lTot"]!=""&& $_POST["lAc"]!="")
			$paginacion.="ltotal=".$_POST["lTot"]."&lactual=".$_POST["lAc"]."&";		
		
		if($select_cd!="")	
			$obCat->Modifica_Hotel($nombre_hotel,strtolower(($select_cd)),$costo, $idCat);
		
		else
			$obCat->Modifica_Hotel($nombre_hotel,utf8_encode(($cd)), $costo, $idCat);
		
	}
	header('Location:./index.php?'.$paginacion.'catalogo='.$_POST['ir']);
}

//Acción del botón Cancelar
else if(isset($_POST['ir']) && $_POST["ir"]!=""){
	$paginacion="";
	
	if(isset($_POST['view_zones']) && $_POST['view_zones']!="")
		$paginacion="&view_zones=".$_POST['view_zones'];
	
	else if(isset($_POST['view_hoteles']) && $_POST['view_hoteles']!="")
		$paginacion="&view_hoteles=".utf8_encode(str_replace(" ","-",$_POST['view_hoteles']));
		
	header('Location:./index.php?catalogo='.$_POST['ir'].$paginacion);
}
?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
<style type="text/css">
	.style1 {color: #FF0000}
	.cuestionDel{
		vertical-align:middle;
		display:block;
		background-color:#CCCCCC;
		width:30%;
		top:35%;
		left:35%;	
		position:absolute;		
	}
	.im{	
		
		border:0;
		
	}
	.tds{
	text-align:center;
	color:#FF4646;
	font-weight:bold;
	}

</style>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="javascript">

	$(document).ready(function(){
		$("#view_zones").change(function(){
			//$("#zones").html($(this).val());
		//alert($(this).val());

			$.post("services/ingreso_sin_recargar_proceso_catalogo.php",{ id:$(this).val() },function(data){$("#zones").html(data);})
		});
		
		$("#view_hoteles").change(function(){			
			$.post("services/ingreso_sin_recargar_proceso_catalogo.php",{ idCd:$(this).val() },function(data){$("#cds").html(data);})
		});		
	})
	
	function validaNum(valor){
			cTecla=(document.all)?valor.keyCode:valor.which;
			if(cTecla==8) return true;
				patron=/^([0-9.]{1,2})?$/;
			cTecla= String.fromCharCode(cTecla);
			return patron.test(cTecla);
		}
	function clearGral(){
		$("#name_concepto").val("");
		$("#cuenta").val("");
		$("#registrar").val("     Agregar");	
	}
</script>
</head>
<body>
<?php
if (isset($_GET['catalogo']) && $_GET['catalogo']=="cz"){
	$I  = new Interfaz("Conceptos",true);
	require_once("menu.php");
	require_once("cat_zonas.php");
	$I->Footer();

}else if (isset($_GET['catalogo']) && $_GET['catalogo']=="hotel"){
	$I  = new Interfaz("Conceptos",true);
	require_once("menu.php");
	require_once("cat_hotel.php");
	$I->Footer();
	
}else{
	$I  = new Interfaz("Conceptos",true);
	require_once("menu.php");
	require_once("cat_zonas.php");
	$I->Footer();
}
?>
