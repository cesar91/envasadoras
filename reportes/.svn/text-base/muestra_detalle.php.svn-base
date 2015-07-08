<?php
session_start();
require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
$cadena="";
switch($_GET["flujo"]){
	
	case 1:
		$cadena="../flujos/solicitud_gastos/index.php?edit=edit&id=";
	break;
	case 2:
		$cadena="../flujos/solicitud_viaje/sv_captura.php?view=view&id=";
	break;
	case 3:
	case 4:
		$cadena="../flujos/comprobaciones/index.php?view=view&id=";
	break;
}
$cadena.=$_GET["id"];
	
	header("Location: {$cadena}");
?>