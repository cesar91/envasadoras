<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
//require_once "$RUTA_A/functions/utils.php";
	$cnn = new conexion();

	if(isset($_POST['codigo'])){
		$sql = "SELECT cp_cuenta FROM cat_conceptosbmw  WHERE cp_cuenta = '".$_POST['codigo']."' AND cp_cuenta != 54233";
		if(isset($_POST['id'])){
			$sql.= " AND dc_id != ".$_POST['id'];
		}				
		$res = $cnn->consultar($sql);
		$row = mysql_fetch_assoc($res);
		echo $row['cp_cuenta'];	
	}
?>
