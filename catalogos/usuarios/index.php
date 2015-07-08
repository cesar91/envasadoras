<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/catalogos/mnu_toolbar.php";
/*
 *  Esta pagina implementa el catalogo de usuarios de Expenses. Se
 * tienen los siguientes modos:
 * 
 *      INDEX  - Muestra la lista de usuarios sin filtrar
 *      BUSCAR - Muestra el resultado de una busqueda, espera el sig. parametro:
 *                  criterio: Criterio de busqueda
 *      NUEVO  - Muestra la pantalla de alta de nuevo usuario
 *      EDITAR - Muestra la pantalla de edicion de usuario, espera el sig. parametro:
 *                  usuario_id: ID del usuario a editar
 *      ELIMINAR - Muestra la pantalla de eliminacion de usuario, espera el sig. parametro:
 *                  usuario_id: ID del usuario a editar
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
    	$Usu= new Usuario();
    	$usuario_id=$_SESSION['idusuario'];
    	$Usu->Load_Usuario_By_ID($usuario_id);
    	
        $I  = new Interfaz("Usuarios",true);
        $L	= new Lista();
        $L->Cabeceras("ID");          
        $L->Cabeceras("Empleado");
        $L->Cabeceras("Nombre");
        $L->Cabeceras("Apellido Paterno");
        $L->Cabeceras("Apellido Materno");
        $L->Cabeceras("Centro de Costos");        
        $L->Cabeceras("Estatus");
        if($_SESSION['perfil']=="2")
			$L->Herramientas("I","./index.php?mode=ASIGNAR&usuario_id=");
        $L->Herramientas("E","./index.php?mode=EDITAR&usuario_id=");
        
		usuarios_toolbar();
        $query="SELECT u_id, u_usuario,u_nombre, u_paterno, u_materno, cc_nombre, (CASE u_activo WHEN 1 THEN 'Activo' WHEN 0 THEN 'Inactivo' END) as estatus FROM usuario u
				INNER JOIN empleado e ON (u.u_id = e.idempleado) INNER JOIN cat_cecos c ON (cc_id = idcentrocosto)
				ORDER BY u_activo desc, u_nombre;";
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
    
    case "BUSCAR": // Muestra el resultado de una busqueda 
    	$Usu= new Usuario();
    	$usuario_id=$_SESSION['idusuario'];
    	$Usu->Load_Usuario_By_ID($usuario_id);
        $criterio = $_POST["criterio"]; 
		
		if(isset($_POST["criterio"])){
			$criterio = $_POST["criterio"];        			
		}else{
			$criterio = $_GET["criterio"];
		}
		$busqueda_value = "mode=BUSCAR&criterio=".$criterio;
		
        $I  = new Interfaz("Usuarios",true);
        $L	= new Lista($busqueda_value);
        $L->Cabeceras("ID");        
        $L->Cabeceras("Empleado");
        $L->Cabeceras("Nombre");
        $L->Cabeceras("Apellido Paterno");
        $L->Cabeceras("Apellido Materno");
        $L->Cabeceras("Centro de Costos");                
        $L->Cabeceras("Estatus");
        if($_SESSION['perfil']=="2")
			$L->Herramientas("I","./index.php?mode=ASIGNAR&usuario_id=");
        $L->Herramientas("E","./index.php?mode=EDITAR&usuario_id=");
        usuarios_toolbar();
        
        // Se filtra por nombre de usuario o no. de usuario.
        $query="SELECT u_id, u_usuario,u_nombre AS nombre, u_paterno, u_materno, CONCAT(cc_centrocostos, ' - ', cc_nombre),          
			(CASE u_activo WHEN 1 THEN 'Activo' WHEN 0 THEN 'Inactivo' END) as estatus FROM usuario u
			INNER JOIN empleado e ON (u.u_id = e.idempleado) INNER JOIN cat_cecos c ON (cc_id = idcentrocosto) 
			WHERE u_nombre LIKE '%".$criterio."%' OR u_usuario LIKE '%".$criterio."%' ORDER BY u_nombre;";     
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
    case "EDITAR":
        require_once("u_edit.php");
        break;
	}
?>
