<?php

require_once("../lib/php/constantes.php");
require_once("$RUTA_A/Connections/fwk_db.php");
require_once("$RUTA_A/functions/Divisa.php");
require_once("$RUTA_A/functions/ConexionERP.php");
require_once("$RUTA_A/functions/AddERP.php");

/*
 *  Este script ejecuta la interfaz de Expenses -> PeopleSoft,
 * debe de correr 2 veces al dia: 1.30 PM y 6.30 PM
 */

ExecutaInterfazPeopleSoft();
?>
