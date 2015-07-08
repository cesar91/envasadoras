<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

/*
 *  Esta pagina implementa el catalogo de modelos de autros de BMW. Se
 * tienen los siguientes modos:
 * 
 *      INDEX  - Muestra la lista de modelos sin filtrar
 *      NUEVO  - Muestra la pantalla de alta de nuevo modelo de auto
 *      EDITAR - Muestra la pantalla de edicion de modelo de auto, espera el sig. parametro:
 *                  ma_id: ID del modelo de auto a editar 
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
    
    case "INDEX": // Lista de modelos
    
        $I  = new Interfaz("modeloAuto",true);
        $L	= new Lista();
        $L->Cabeceras("Nombre");
        $L->Cabeceras("Factor");        
        $L->Cabeceras("Estatus");
        $L->Herramientas("E","./index.php?mode=EDITAR&ma_id=");        
        include("../../lib/php/mnu_toolbar.php");
        modelos_autos_toolbar();
        
        $query="SELECT ma_nombre,CONCAT(CONVERT(ma_factor,CHAR(5)),' MXN'),IF(ma_estatus=1,'Activo','Inactivo'),ma_id FROM modelo_auto ORDER BY ma_estatus DESC"; 
        $L->muestra_lista($query,3,false,3);
        $I->Footer();    
        break;
    
    case "NUEVO": 
        require_once("modelo_auto_new.php");
        break;
        
    case "EDITAR":  
        require_once("modelo_auto_edit.php");
        break;
        
    case "BUSCAR": // Muestra el resultado de una busqueda 
    
        if(isset($_POST["criterio"])){
			$criterio = $_POST["criterio"];        			
		}else{
			$criterio = $_GET["criterio"];
		}
		$busqueda_value = "mode=BUSCAR&criterio=".$criterio;
		
        $I  = new Interfaz("modeloAuto",true);
        $L	= new Lista($busqueda_value);
        $L->Cabeceras("Nombre");
        $L->Cabeceras("Factor");        
        $L->Cabeceras("Estatus");
        $L->Herramientas("E","./index.php?mode=EDITAR&ma_id=");        
        include("../../lib/php/mnu_toolbar.php");
        modelos_autos_toolbar();
        
        $query="SELECT ma_nombre, CONCAT(CONVERT(ma_factor,CHAR(5)), ' MXN'), IF(ma_estatus=1,'Activo','Inactivo'),ma_id FROM modelo_auto 
				WHERE ma_nombre LIKE '%".$criterio."%' 
				ORDER BY ma_estatus DESC"; 
        error_log($query);
        $L->muestra_lista($query,3,false,3);
        $I->Footer();   
        break;              

}
?>
