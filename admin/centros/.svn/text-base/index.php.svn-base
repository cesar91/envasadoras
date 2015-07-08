<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

/*
 *  Esta pagina implementa el catalogo de cecos de Expenses. Se
 * tienen los siguientes modos:
 * 
 *      INDEX  - Muestra la lista de cecos sin filtrar
 *      NUEVO  - Muestra la pantalla de alta de nuevo ceco
 *      EDITAR - Muestra la pantalla de edicion de ceco, espera el sig. parametro:
 *                  ceco_id: ID del ceco a editar
 *      ELIMINAR - Muestra la pantalla de eliminacion de ceco, espera el sig. parametro:
 *                  ceco_id: ID del ceco a editar
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
    
        $I  = new Interfaz("ceco",true);
        $L	= new Lista();
        $L->Cabeceras("ID");
        $L->Cabeceras("C&oacute;digo");
        $L->Cabeceras("Nombre");
        $L->Cabeceras("Responsable CC");        
        $L->Cabeceras("Empresa");
        $L->Cabeceras("Estatus");
        $L->Herramientas("E","./index.php?mode=EDITAR&ceco_id=");;
        //$L->Herramientas("D","./index.php?mode=ELIMINAR&ceco_id=");
        include("../../lib/php/mnu_toolbar.php");
        cecos_toolbar();
        
        $query="SELECT cc_id, cc_centrocostos, cc_nombre, (SELECT nombre FROM empleado WHERE idfwk_usuario=cc_responsable), e_codigo,if (cc_estatus=1,'Activo','Inactivo')cc_estatus FROM cat_cecos c 
INNER JOIN empresas e ON c.cc_empresa_id = e.e_codigo
WHERE cc_estatus >= 0 ORDER BY cc_estatus asc,cc_centrocostos asc"; 
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
    
    case "NUEVO": 
        require_once("ceco_new.php");
        break;
        
    case "EDITAR":  
        require_once("ceco_edit.php");
        break;
        
    case "BUSCAR": // Muestra el resultado de una busqueda 
		if(isset($_POST["criterio"])){
			$criterio = $_POST["criterio"];        			
		}else{
			$criterio = $_GET["criterio"];
		}
		$busqueda_value = "mode=BUSCAR&criterio=".$criterio;
		
        $I  = new Interfaz("ceco",true);
        $L	= new Lista($busqueda_value);
        $L->Cabeceras("ID");
        $L->Cabeceras("C&oacute;digo");
        $L->Cabeceras("Nombre");
        $L->Cabeceras("Responsable CC");                
        $L->Cabeceras("Empresa");
        $L->Cabeceras("Estatus");
        $L->Herramientas("E","./index.php?mode=EDITAR&ceco_id=");;
        //$L->Herramientas("D","./index.php?mode=ELIMINAR&ceco_id=");
        include("../../lib/php/mnu_toolbar.php");
        cecos_toolbar();
        
        $query="SELECT cc_id, cc_centrocostos, cc_nombre, (SELECT nombre FROM empleado WHERE idfwk_usuario=cc_responsable), e_codigo,if (cc_estatus=1,'Activo','Inactivo')as cc_estatus FROM cat_cecos c 
				INNER JOIN empresas e ON c.cc_empresa_id = e.e_codigo  
				WHERE cc_estatus >= 0 
				AND (cc_centrocostos LIKE '%".$criterio."%' OR cc_nombre LIKE '%".$criterio."%') 
				ORDER BY cc_estatus asc,cc_centrocostos asc"; 
        error_log($query);
        $L->muestra_lista($query,0);
        $I->Footer();   
        break;
               
        
    case "ELIMINAR": 
        $ceco_id = $_GET["ceco_id"]; 
        $ceco = new CentroCosto();
        $ceco->Delete_ceco($ceco_id);
        header("Location: index.php");
        break;    
}
?>
