<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

if(isset($_POST['Aceptar'])){

	$aceptar=$_POST['Aceptar'];
	$pesos=$_POST['pesos'];
	$dolares=$_POST['dolares'];
	$euros=$_POST['euros'];

	$obDiv=new Divisa();
	$result="";	
	if($aceptar==1)
	{			
        if(isset($pesos) && $pesos!=""){
            $obDiv->Update_Divisa("MXN", $pesos);
        }
        if(isset($dolares) && $dolares!=""){
            $obDiv->Update_Divisa("USD", $dolares);
        }
        if(isset($euros) && $euros!=""){
            $obDiv->Update_Divisa("EUR", $euros);
        }
	}
}
?>
