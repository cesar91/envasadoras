<?php 
require_once "$RUTA_A/catalogos/mnu_toolbar.php";
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
    	$criterio = "";
		if(isset($_POST["name_concepto"])){
			$criterio = $_POST["name_concepto"];        			
		}else{
			$criterio = $_GET["name_concepto"];
		}
		$busqueda_value = "name_concepto=".$criterio;
		       
        $I  = new Interfaz("Conceptos",true);
        $L	= new Lista($busqueda_value);
        $L->Cabeceras("ID");
        $L->Cabeceras("Concepto");
        $L->Cabeceras("Cuenta");
        $L->Cabeceras("Estatus"); 
        $L->Herramientas("E","./index.php?mode=EDITAR&concepto_id=");
		conceptos_toolbar();
		
        $query = "SELECT cp_id, cp_concepto, cp_cuenta, IF(cp_activo = 0, 'INACTIVO', 'ACTIVO') AS cp_activo
				FROM cat_conceptos c 
				WHERE cp_concepto LIKE '%". $criterio ."%' 
				OR cp_cuenta LIKE '%". $criterio ."%' 
				ORDER BY cp_concepto";
        $L->muestra_lista($query,0);
        $I->Footer();
        break;
    
    case "EDITAR":  
        require_once("concepto_edit.php");
        break;
    
}
?>