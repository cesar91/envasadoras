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
	$query = "SELECT * FROM empleado WHERE nombre LIKE \"%".$q."%\" GROUP BY nombre";
	$rst = $cnn->consultar($query);
	$item = array();
	while ($fila = mysql_fetch_assoc($rst)) {
		$item[] = $fila['idempleado'].'|'.utf8_encode($fila['nombre']);
	}
	echo json_encode($item);
}
?>