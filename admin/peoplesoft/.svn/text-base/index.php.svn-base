<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/functions/AddERP.php";

/*
 *  Esta pagina implementa la consola de administracion de la
 * interfaz de Peoplesoft
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
    
    case "EJECUTAR": // Ejecuta la interface
        
        //$DISABLE_ORACLE=TRUE;
        $ORACLE_QUERIES="";
        ExecutaInterfazPeopleSoft();
        $I  = new Interfaz("Solicitudes",true);       
        echo "<textarea name='observ' cols='200' rows='25' id='observ'>$ORACLE_QUERIES</textarea>";
        $I->Footer();    
        
        break;
        
    case "VALIDAR": // Valida la interface
        
        //$DISABLE_ORACLE=TRUE;
        $VALIDACIONES="";
        ValidarInterfazPeopleSoft();
        $I  = new Interfaz("Solicitudes",true);       
        echo "<textarea name='observ' cols='200' rows='25' id='observ'>$VALIDACIONES</textarea>";
        $I->Footer();    
        
        break;        
    
    case "INDEX": // Lista Solicitudes Pendientes de Envio
            
        $I  = new Interfaz("Solicitudes",true);
        $L	= new Lista();
        $L->Cabeceras("Folio");          
        $L->Cabeceras("Referencia");
        $L->Cabeceras("Fecha Registro");
        $L->Cabeceras("Usuario");        
        $L->Cabeceras("Monto");
        $L->Cabeceras("Divisa");        
        $L->Cabeceras("Estatus PeopleSoft");                
        $L->Herramientas("E","./index.php?mode=EDITAR&usuario_id=");
        $L->Herramientas("D","./index.php?mode=ELIMINAR&usuario_id=");        

        echo "<tr><td><h1><a href='index.php?mode=EJECUTAR'>Ejecutar Interface</a></h1></td></tr>";
        echo "<tr><td><h1></h1></td></tr>";
        echo "<tr><td><h1><a href='index.php?mode=VALIDAR'>Validar Interface</a></h1></td></tr>";        
        echo "<tr><td><h1></h1></td></tr>";
        
        echo "<tr><td><h1>Solicitudes</h1></td></tr>";
        $query="SELECT t_id,t_etiqueta,date(t_fecha_registro), (select concat(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador) AS atiende, sv_anticipo, sv_divisa, sv_status_erp FROM tramites INNER JOIN solicitud_viaje ON (solicitud_viaje.sv_tramite= tramites.t_id) WHERE t_flujo = 1 AND sv_status_erp > 0 ORDER BY  t_id DESC";
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
      
}
?>
