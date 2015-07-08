<?php
	session_start();
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
		
	//Inserta o actualiza en Catalogo Gral
	if(isset($_POST['valor_parametro']) && isset($_POST['idCat']) && $_POST['idCat']!=""){
		$vb=trim($_POST["registrar"]);
			
		$valorparametro=$_POST['valor_parametro'];	
		$idCat=$_POST['idCat'];
		
		$paginacion="";		
		
		$obCat=new Parametro();
		
		if($vb=="Actualizar" && $idCat!="")
			{
			
			if($_POST["lTot"]!=""&& $_POST["lAc"]!="")
				$paginacion="ltotal=".$_POST["lTot"]."&lactual=".$_POST["lAc"]."&";		
			//file_put_contents("pr.txt",$paginacion);
			//$paginacion="";
			$obCat->Modifica_Parametro($valorparametro, $idCat);
		}
		header('Location:./index.php?'.$paginacion);
	}

	else if(isset($_POST['ir']) && $_POST["ir"]!=""){
		$paginacion="";
		
		if(isset($_POST['view_zones']) && $_POST['view_zones']!="")
			$paginacion="&view_zones=".$_POST['view_zones'];
			
		header('Location:./index.php?'.$paginacion);
	}
	?>
	<html>
	<head>

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
				$.post("services/ingreso_sin_recargar_proceso_catalogo.php",{ id:$(this).val() },function(data){$("#zones").html(data);})
			});
		})

		function clearGral(){
			$("#name_concepto").val("");
			$("#cuenta").val("");
			$("#registrar").val("     Agregar");
		
		}
		
		function validaNum(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		if(cTecla==8) return true;
		patron=/^([0-9.]{1,2})?$/;
		cTecla= String.fromCharCode(cTecla);
		return patron.test(cTecla);
		}
		
	</script>
	</head>
	<body>
	<?php
	$I  = new Interfaz("Parámetros",true);

		require_once("parametrosbmw.php");

	$I->Footer();
?>
