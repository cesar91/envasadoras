<?php
/*
 * Este archivo implementa las functiones Ajax usadas
 * para rellenar el catalogo de proveedores.
 * 
 */

require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";


if (isset($_GET["q"]) && isset($_GET["tip"])) {

    // Obtiene lista de proveedores
    function Get_list_proveedores($dato) {
        $cnn = new conexion();
        $query = "SELECT pro_proveedor FROM proveedores where pro_proveedor like '%$dato%'";
        $rst = $cnn->consultar($query);
        while ($fila = mysql_fetch_assoc($rst)) {
            echo utf8_encode($fila["pro_proveedor"] . "\n");
        }
    }

//obtiene lista de rfc de proveedores
    function Get_list_rfc_proveedores($dato) {
        $cnn = new conexion();
        $query = "select pro_rfc from proveedores where pro_rfc like '%$dato%'";
        $rst = $cnn->consultar($query);
        while ($fila = mysql_fetch_assoc($rst)) {
            echo $fila["pro_rfc"] . "\n";
        }
    }

    if ($_GET["tip"] == 1) {
        $q = $_GET["q"];
        Get_list_proveedores($q);
    } elseif ($_GET["tip"] == 2) {
        $q = $_GET["q"];
        Get_list_rfc_proveedores($q);
    }
} elseif (isset($_POST["nombre"]) && isset($_POST["tip"])) {

//obtiene el rfc del proveedor seleccionado
    function Get_rfc_proveedores($dato) {
        $cnn = new conexion();
        $query = "select pro_rfc from proveedores where pro_proveedor like '%$dato%'";
        $rst = $cnn->consultar($query);
        $fila = mysql_fetch_assoc($rst);
        echo $fila["pro_rfc"];
    }

//obtiene el nombre del proveedor seleccionado
    function Get_nombre_proveedores($dato) {
        $cnn = new conexion();
        $query = "select pro_proveedor from proveedores where pro_rfc like '%$dato%'";
        $rst = $cnn->consultar($query);
        $fila = mysql_fetch_assoc($rst);
        echo $fila["pro_proveedor"];
    }

    if ($_POST["tip"] == 1) {
        $q = $_POST["nombre"];
        Get_nombre_proveedores($q);
    } elseif ($_POST["tip"] == 2) {
        $q = $_POST["nombre"];
        Get_rfc_proveedores($q);
    }
} elseif (isset($_POST['submit'])) {
    $nameProv = $_POST["nameprov"];
    $rfcProv = $_POST["rfcprov"];

//Agrega proveedor a la base
    function Set_proveedores($dato1, $dato2) {
        $cnn = new conexion();
        $query = "insert into proveedores (pro_proveedor,pro_rfc) values ('$dato1','$dato2')";
        $rst = $cnn->insertar($query);
    }

//Busca si el proveedor ya esta en la base y si no, lo agregamos	
    $cnn = new conexion();
    $query = sprintf("SELECT pro_proveedor,pro_rfc FROM proveedores WHERE (pro_proveedor='%s' and pro_rfc='%s') or (pro_proveedor='%s' or pro_rfc='%s')", $nameProv, $rfcProv, $nameProv, $rfcProv);
    $rst = $cnn->consultar($query);
    $fila = mysql_num_rows($rst);
    if ($fila > 0) {
        echo "El proveedor ya se encuentra registrado";
    } else {
        Set_proveedores($nameProv, $rfcProv);
    }
} elseif (isset($_POST['busca'])) {
    $nameProv = $_POST["nameprov"];
    $rfcProv = $_POST["rfcprov"];
    $cnn = new conexion();
    $query = sprintf("SELECT pro_proveedor,pro_rfc FROM proveedores WHERE (pro_proveedor='%s' and pro_rfc='%s') or (pro_proveedor='%s' or pro_rfc='%s')", $nameProv, $rfcProv, $nameProv, $rfcProv);
    $rst = $cnn->consultar($query);
    $fila = mysql_num_rows($rst);
    if ($fila > 0) {
        echo "El proveedor ya se encuentra registrado";
    }
} elseif (isset($_POST['folios'])) {
    $folioFP = $_POST["folios"];
    $rfcProv = $_POST["rfcprov"];
    $cnn = new conexion();
    $query = sprintf("SELECT dc_id, dc_rfc, dc_total, dc_factura_impresa, dc_folio_factura FROM detalle_comprobacion where dc_folio_factura='%s' and dc_rfc='%s'", $folioFP, $rfcProv);
    $rst = $cnn->consultar($query);
    $fila = mysql_num_rows($rst);
    if ($fila > 0) {
        echo "El folio ya se encuentra ocupado";
    }
} else {
    echo "No se han encontrado el registros.";
}
?>