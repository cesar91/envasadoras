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
		$L->Cabeceras("FInicial");
		$L->Cabeceras("FFinal");
		$L->Cabeceras("Inicial");
		$L->Cabeceras("Disponible");
        $query=	"SELECT cc.cc_empresa_id, cc.cc_id, cc.cc_centrocostos, pp.pp_periodo_inicial, pp.pp_periodo_final, FORMAT(pp.pp_presupuesto_inicial,2), FORMAT(pp.pp_presupuesto_disponible,2) FROM periodo_presupuestal pp 
									INNER JOIN cat_cecos cc ON pp.cc_id = cc.cc_id";
		$L->muestra_lista($query,0);
		$I->Footer();
		break;
			
	case "BUSCAR": 	
		if($_SESSION['perfil'] == 10){
			echo "<h1>Carga Presupuestal</h1>";
		}else{
			cargaPresupuesto_toolbar(null);     
		}
		presupuesto_toolbar();   
		if(isset($_POST["criterio"])){
			$criterio = $_POST["criterio"];        			
		}else{
			$criterio = $_GET["criterio"];
		}
		if(isset($_POST["finicial"])){
			$finicial = $_POST["finicial"];        			
		}else{
			$finicial = $_GET["finicial"];
		}
		if(isset($_POST["ffinal"])){
			$ffinal = $_POST["ffinal"];        			
		}else{
			$ffinal = $_GET["ffinal"];
		}		
		$busqueda_value = "mode=BUSCAR&criterio=".$criterio."&finicial=".$finicial."&ffinal=".$ffinal."";
		
        $L	= new Lista($busqueda_value);
		$L->Cabeceras("Empresa");
		$L->Cabeceras("# Ctro Costo");
		$L->Cabeceras("Ctro Costo");
		$L->Cabeceras("FInicial");
		$L->Cabeceras("FFinal");
		$L->Cabeceras("Inicial");
		$L->Cabeceras("Disponible");
		$FI = explode("/",$finicial);    
		$FF = explode("/",$ffinal); 
        $query="SELECT cc.cc_empresa_id, cc.cc_id, cc.cc_centrocostos, pp.pp_periodo_inicial, pp.pp_periodo_final, FORMAT(pp.pp_presupuesto_inicial,2), FORMAT(pp.pp_presupuesto_disponible,2) FROM periodo_presupuestal pp 
			INNER JOIN cat_cecos cc ON pp.cc_id = cc.cc_id
			WHERE 1=1";
		if($criterio != ""){
			$query.="  AND cc.cc_centrocostos LIKE '%$criterio%'";
		}	
		if($finicial > 0){
			$query.=" AND(pp.pp_periodo_inicial BETWEEN '".$FI[2]."-".$FI[1]."-".$FI[0]."' AND '".$FF[2]."-".$FF[1]."-".$FF[0]."')";
		}
		$query.= " ORDER BY pp.pp_periodo_inicial ASC";
        $L->muestra_lista($query,0);
        $I->Footer();    
        break;
        
     case "CARGAR":		     	
		$result=cargarPresupuesto($RUTA_A);
		if($_SESSION['perfil'] == 10){
			echo "<h1>Carga Presupuestal</h1>";
		}else{
			cargaPresupuesto_toolbar($result);     
		}
		presupuesto_toolbar();   
        $L	= new Lista();
		$L->Cabeceras("Empresa");
		$L->Cabeceras("# Ctro Costo");
		$L->Cabeceras("Ctro Costo");
		$L->Cabeceras("FInicial");
		$L->Cabeceras("FFinal");
		$L->Cabeceras("Inicial");
		$L->Cabeceras("Disponible");
        $query=	"SELECT cc.cc_empresa_id, cc.cc_id, cc.cc_centrocostos, pp.pp_periodo_inicial, pp.pp_periodo_final, FORMAT(pp.pp_presupuesto_inicial,2), FORMAT(pp.pp_presupuesto_disponible,2) FROM periodo_presupuestal pp 
									INNER JOIN cat_cecos cc ON pp.cc_id = cc.cc_id";
		$L->muestra_lista($query,0);
		$I->Footer();
		break;
}
	
?>


