<?php

require_once("../lib/php/constantes.php");
require_once("$RUTA_A/Connections/fwk_db.php");
require_once("$RUTA_A/functions/Divisa.php");
require_once("$RUTA_A/functions/ConexionERP.php");

/*
 *  Este script actualiza el catalogo de monedas de Expenses a partir
 * del catalogo de PeopleSoft
 */

$conn = new ConexionERP();
$conn->Conectar();
$conn->actualizaTasaCambio();
$conn->Desconectar();

?>
