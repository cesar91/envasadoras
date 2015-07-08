<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

/*
 *  Esta pagina implementa el catalogo de empresas de Expenses. Se
 * tienen los siguientes modos:
 * 
 *      INDEX  - Muestra la lista de empresas sin filtrar
 *      NUEVO  - Muestra la pantalla de alta de nuevo empresa
 *      EDITAR - Muestra la pantalla de edicion de empresa, espera el sig. parametro:
 *                  empresa_id: ID del empresa a editar
 *      ELIMINAR - Muestra la pantalla de eliminacion de empresa, espera el sig. parametro:
 *                  empresa_id: ID del empresa a editar
 */

//
// Determina el modo de operacion
//
$mode = "INDEX"; // modo por defecto
if(isset($_GET["mode"])){
    $mode = $_GET["mode"];
}

//
//  Muestra la pantalla en base al modo de operacion
//
switch($mode){
    
    case "INDEX": 
    	$concepto_name = "";
        if ($_GET["name_concepto"])
        	$concepto_name= $_GET["name_concepto"];
        $I  = new Interfaz("Conceptos",true);
        $L	= new Lista();
        $L->Cabeceras("ID");
        //$L->Cabeceras("Catálogo");
        $L->Cabeceras("Concepto");
        $L->Cabeceras("Clasificaci&oacute;n");
        $L->Cabeceras("Cuenta");        
        $L->Cabeceras("Empresa");
        $L->Cabeceras("Estatus"); 
        $L->Herramientas("E","./index.php?mode=EDITAR&concepto_id=");;
        //$L->Herramientas("D","./index.php?mode=ELIMINAR&concepto_id=");
        include("../../lib/php/mnu_toolbar.php");
        conceptos_toolbar();

        
        $query="SELECT dc_id, cp_concepto, cp_clasificacion, cp_cuenta, e_codigo, replace (cp_activo,'0','INACTIVO') as cp_activo from (SELECT dc_id, cp_concepto, cp_clasificacion, cp_cuenta, e_codigo, replace (cp_activo,'1','ACTIVO')as cp_activo  FROM cat_conceptosbmw c INNER JOIN empresas e ON c.cp_empresa_id = e.e_id WHERE cp_concepto like '%".$concepto_name."%' OR cp_cuenta LIKE '%".$concepto_name."%' ORDER BY cp_activo desc,dc_id) as T1";
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
    
    case "NUEVO": 
        require_once("concepto_new.php");
        break;
        
    case "EDITAR":  
        require_once("concepto_edit.php");
        break;
        
    case "ELIMINAR": 
        $concepto_id = $_GET["concepto_id"]; 
        $concepto = new Concepto();
        $concepto->Delete_Concepto($concepto_id);
        header("Location: index.php");
        break;    
}
?>
