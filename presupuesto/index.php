<?php
session_start();
require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

$I	= new Interfaz("Administracion",true);

include("menu/menu.php");
	
$I->Footer();


?>