<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("../../lib/php/upload.php");
require_once("../../lib/php/mnu_toolbar.php");  

/*
 *  Esta pagina implementa la búsqueda de presupuestos x centros de costos. Se
 * tienen los siguientes modos:
 * 
 *      INDEX  - Muestra la lista de cecos sin filtrar y el botón de carga del presupuesto
 *      BUSCAR - Muestra el resultado de la consulta realizada      
 *      CARGAR - Realiza la carga del presupuesto
 *      RESULTADO_CARGA - Muestra el resultado de la carga
 */
	
	$I  = new Interfaz("Presupuesto",true);

//        
// Determina el modo de operacion
//
$mode = "INDEX"; // modo por defecto
if(isset($_GET["mode"])){
    $mode = $_GET["mode"];
}        

// esta sección es común para cualquier "mode" 
				        
switch($mode){	
		
	case "INDEX": 	         
		cargaPresupuesto_toolbar(null);     
		presupuesto_toolbar();   
        $L	= new Lista();
		$L->Cabeceras("Empresa");
		$L->Cabeceras("# Ctro Costo");
		$L->Cabeceras("Ctro Costo");
		$L->Cabeceras("# Cuenta");
		$L->Cabeceras("Concepto");
		$L->Cabeceras("Inicial");
		$L->Cabeceras("Disponible");
        $query=	"SELECT e.e_nombre, cc.cc_centrocostos, cc.cc_nombre, c.cp_cuenta, c.cp_concepto, FORMAT(pp.pp_presupuesto_inicial,2), FORMAT(pp.pp_presupuesto_disponible,2)
		FROM periodo_presupuestal pp, cat_cecos cc, cat_conceptosbmw c, empresas e
		WHERE pp.pp_id_concepto = c.dc_id and
		pp.pp_ceco = cc.cc_id and
		e.e_id = cc.cc_empresa_id order by cc.cc_centrocostos, c.dc_id";   
        error_log($query);    
		$L->muestra_lista($query,0);
		$I->Footer();
		break;
			
	case "BUSCAR": 	
		cargaPresupuesto_toolbar(null);     
		presupuesto_toolbar();   
		
		if(isset($_POST["criterio"])){
			$criterio = $_POST["criterio"];        			
		}else{
			$criterio = $_GET["criterio"];
		}
		$busqueda_value = "mode=BUSCAR&criterio=".$criterio;
		
        $L	= new Lista($busqueda_value);
		$L->Cabeceras("Empresa");
		$L->Cabeceras("# Ctro Costo");
		$L->Cabeceras("Ctro Costo");
		$L->Cabeceras("# Cuenta");
		$L->Cabeceras("Concepto");
		$L->Cabeceras("Inicial");
		$L->Cabeceras("Disponible");
		            
        $query=
        "SELECT e.e_nombre, cc.cc_centrocostos, cc.cc_nombre, c.cp_cuenta, c.cp_concepto, pp.pp_presupuesto_inicial, pp.pp_presupuesto_disponible
		FROM periodo_presupuestal pp, cat_cecos cc, cat_conceptosbmw c, empresas e
		WHERE (cc.cc_centrocostos LIKE '%".$criterio."%' OR cc.cc_nombre LIKE '%".$criterio."%')  and 
		pp.pp_id_concepto = c.dc_id and
		pp.pp_ceco = cc.cc_id and
		e.e_id = cc.cc_empresa_id 
		ORDER BY cc.cc_centrocostos, c.dc_id";                
        error_log($query);
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
        
     case "CARGAR":		     	
		$result=cargarPresupuesto($RUTA_A);
		cargaPresupuesto_toolbar($result);     
		presupuesto_toolbar();   
        $L	= new Lista();
		$L->Cabeceras("Empresa");
		$L->Cabeceras("# Ctro Costo");
		$L->Cabeceras("Ctro Costo");
		$L->Cabeceras("# Cuenta");
		$L->Cabeceras("Concepto");
		$L->Cabeceras("Inicial");
		$L->Cabeceras("Disponible");
        $query=	"SELECT e.e_nombre, cc.cc_centrocostos, cc.cc_nombre, c.cp_cuenta, c.cp_concepto, pp.pp_presupuesto_inicial, pp.pp_presupuesto_disponible
		FROM periodo_presupuestal pp, cat_cecos cc, cat_conceptosbmw c, empresas e
		WHERE pp.pp_id_concepto = c.dc_id and
		pp.pp_ceco = cc.cc_id and
		e.e_id = cc.cc_empresa_id order by cc.cc_centrocostos, c.dc_id";   
        error_log($query);    
		$L->muestra_lista($query,0);
		$I->Footer();
		break;
}
	
?>


