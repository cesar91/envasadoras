<?php
//require_once "../../Connections/fwk_db.php";
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
header('Content-Type: text/html; charset=iso-8859-1');

//Imprime tabla de catalgos de zonas
if(isset($_POST['id']) and $_POST['id']!=""){

	$id=$_POST['id'];
	
	$paginacion="";
	$paginacion.="view_zones=".$id."&";
	$L	= new Lista("catalogo=cz&view_zones=".$id);
	$L->Cabeceras("Folio");
	$L->Cabeceras("Nombre");
	$L->Herramientas("E","./index.php?".$paginacion."catalogo=cz&Eid=");
	$L->Herramientas("D","./index.php?".$paginacion."catalogo=cz&delete=true&id=");
	$query="select reco_id, reco_nombre from cat_regiones_conceptos where reco_activo=1 and reco_pertenece_region=".$id." order by reco_id desc";
	$L->muestra_lista($query,0);
}

//imprine tabla de catalogo de hoteles
else if(isset($_POST['idCd']) && $_POST['idCd']!=""){	
	$idCd=$_POST['idCd'];	
	$paginacion="";
		
	$idCd=str_replace("-"," ",$idCd);
	$idCdAux=str_replace(" ","-",$idCd);
	$paginacion.="view_hoteles=".$idCdAux."&";

	$L	= new Lista("catalogo=hotel&view_hoteles=$idCd");
	$L->Cabeceras("Folio");
	$L->Cabeceras("Hotel");
	$L->Cabeceras("Precio","","number","R");
	$L->Herramientas("E","./index.php?".$paginacion."catalogo=hotel&Eid=");
	$L->Herramientas("D","./index.php?".$paginacion."catalogo=hotel&delete=true&id=");
	$query="select hotel_id, hotel_nombre, hotel_costo from cat_hoteles where hotel_ciudad='".$idCd."' and hotel_activo=1";
	$L->muestra_lista($query,0);
}
?>