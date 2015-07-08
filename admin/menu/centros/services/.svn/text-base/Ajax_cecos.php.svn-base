<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
//require_once "$RUTA_A/functions/utils.php";
	$cnn = new conexion();

	if(isset($_POST['codigo'])){
		$sql = "SELECT cc_centrocostos FROM cat_cecos  WHERE cc_centrocostos = '".$_POST['codigo']."'";
		if(isset($_POST['id'])){
			$sql.= " AND cc_id != ".$_POST['id'];
		}				
		$res = $cnn->consultar($sql);
		$row = mysql_fetch_assoc($res);
		echo $row['cc_centrocostos'];	
	}
?>
