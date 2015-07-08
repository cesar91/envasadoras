<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
//require_once "$RUTA_A/functions/utils.php";
	$cnn = new conexion();

	if(isset($_POST['codigo'])){
		$sql = "SELECT e_codigo FROM empresas WHERE e_codigo = '".$_POST['codigo']."'";
		if(isset($_POST['id'])){
			$sql.= " AND e_id != ".$_POST['id'];
		}		
		$res = $cnn->consultar($sql);
		$row = mysql_fetch_assoc($res);
		echo $row['e_codigo'];	
	}
?>
