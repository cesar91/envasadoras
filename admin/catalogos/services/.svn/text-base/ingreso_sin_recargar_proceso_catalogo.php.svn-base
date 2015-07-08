<?php
//require_once "../../Connections/fwk_db.php";
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
header('Content-Type: text/html; charset=iso-8859-1');

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
	$query="select pro_id, pro_proveedor, pro_costo from proveedores where pro_tipo=1 and pro_ciudad='".$idCd."' and pro_activo=1";
	$L->muestra_lista($query,0);
}
?>
