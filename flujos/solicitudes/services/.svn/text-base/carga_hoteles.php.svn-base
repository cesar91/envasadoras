<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
// no term passed - just exit early with no response
if (!empty($_GET['term'])){
$q = strtolower($_GET["term"]);
// remove slashes if they were magically added
if (get_magic_quotes_gpc()) $q = stripslashes($q);
	$cnn = new conexion();

	// Obtener tasa USD
	$query = "SELECT * FROM cat_hoteles WHERE hotel_ciudad LIKE \"%".$q."%\" GROUP BY hotel_ciudad";
	$rst = $cnn->consultar($query);
	$item = array();
	while ($fila = mysql_fetch_assoc($rst)) {
		$item[] = $fila['hotel_ciudad'];
	}
	echo json_encode($item);
}else{
	$cnn = new conexion();
	$query = "select hotel_id, hotel_nombre, hotel_costo from cat_hoteles where hotel_ciudad='".str_replace("-"," ",$_GET["label"])."' and hotel_activo=1";
	$rst = $cnn->consultar($query);
	//$hotel = array();
	//$costo = array();
	$hoteles = array();
	while ($fila = mysql_fetch_assoc($rst)) {
		//$hotel[] = $fila['pro_proveedor'];
		//$costo[] = $fila['pro_costo'];
		array_push($hoteles, array("nombre" => $fila['hotel_nombre'], "costo" => $fila['hotel_costo']));
	}
	echo json_encode($hoteles);
}
?>