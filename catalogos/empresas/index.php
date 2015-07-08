<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/catalogos/mnu_toolbar.php";

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
    case "INDEX": // Lista de usuarios
        $empresa_name = "";
		if(isset($_POST["name_empresa"])){
			$criterio = $_POST["name_empresa"];        			
		}else{
			$criterio = $_GET["name_empresa"];
		}
		$busqueda_value = "name_empresa=".$criterio;
		
        $I = new Interfaz("Empresa",true);
        $L	= new Lista($busqueda_value);
        $L->Cabeceras("ID");
        $L->Cabeceras("Nombre");
        $L->Cabeceras("C&oacute;digo");
        $L->Cabeceras("Estatus");
        $L->Herramientas("E","./index.php?mode=EDITAR&empresa_id=");
		empresas_toolbar();
		
        $query="SELECT e_id, e_nombre, e_codigo, IF (e_estatus=1,'ACTIVO','INACTIVO') as status_activo FROM empresas e WHERE (e_codigo LIKE '%".$criterio."%' or e_nombre LIKE '%".$criterio."%') ORDER BY e_estatus, e_id";
        $L->muestra_lista($query,0);
        $I->Footer();
        break;
    
	case "EDITAR":  
        require_once("empresa_edit.php");
        break;
}
?>
