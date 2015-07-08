<?php 
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
		//require_once("menu.php");
        $L	= new Lista($busqueda_value);
        $L->Cabeceras("ID");
        //$L->Cabeceras("Catálogo");
        $L->Cabeceras("Concepto");
        //$L->Cabeceras("Clasificaci&oacute;n");
        $L->Cabeceras("Cuenta");        
        //$L->Cabeceras("Empresa");
        $L->Cabeceras("Estatus"); 
        $L->Herramientas("E","./index.php?mode=EDITAR&concepto_id=");;
        //$L->Herramientas("D","./index.php?mode=ELIMINAR&concepto_id=");
        include("../../lib/php/mnu_toolbar.php");
        conceptos_toolbar();

        
        //$query = "SELECT cp_id, cp_concepto, cp_cuenta, replace (cp_activo,'0','INACTIVO') as cp_activo from (SELECT cp_id, cp_concepto, cp_cuenta, replace (cp_activo,'1','ACTIVO') as cp_activo  FROM cat_conceptos c WHERE cp_concepto like '%".$criterio."%' OR cp_cuenta LIKE '%".$criterio."%' ORDER BY cp_activo desc, cp_id) as T1";
        $query = "SELECT cp_id, cp_concepto, cp_cuenta, IF(cp_activo = 0, 'INACTIVO', 'ACTIVO') AS cp_activo
				FROM cat_conceptos c 
				WHERE cp_concepto LIKE '%". $criterio ."%' 
				OR cp_cuenta LIKE '%". $criterio ."%' 
				ORDER BY cp_concepto";
        //error_log("--->>Conceptos: ".$query);
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